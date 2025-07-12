<?php

define( 'MFRH_OPTIONS', [
	'media_library_field' => 'none',

	'auto_rename' => 'none',
	'auto_rename_secondary' => 'none',
	'auto_rename_tertiary' => 'none',

	'on_upload_method' => 'none',
	'on_upload_method_secondary' => 'none',
	'on_upload_method_tertiary' => 'none',

	'hide_none_warning' => false,

	'rename_slug' => false,
	'convert_to_ascii' => false,

	'update_posts' => true,
	'update_excerpts' => false,
	'update_postmeta' => false,
	'trigger_on_attachment_updated' => false,

	'parsers' => [

	],

	'undo' => false,
	'move' => false,
	'manual_rename' => false,
	'manual_rename_ai' => false,
	'vision_rename_ai' => false,
	'vision_rename_ai_cache' => 60 * 5,
	'exif_context' => false,
	'manual_prompt' => false,

	'manual_sanitize' => true,
	'numbered_files' => false,
	'force_rename' => false,
	'log' => false,
	'logsql' => false,
	'rename_guid' => false,
	'case_insensitive_check' => false,
	'rename_on_save' => false,

	'filename_prefix' => '',
	'filename_suffix' => '',
	'filename_replace' => [],

	'acf_field_name' => false,
	'images_only' => false,
	'featured_only' => false,
	'posts_per_page' => 10,
	'lock' => false,
	'hide_locked' => true,
	'apply_or_cancel' => true,
	'autolock_auto' => false,
	'autolock_manual' => true,
	'delay' => 100,
	'clean_uninstall' => false,
	'mode' => 'rename', // rename or move
	'dashboard' => true,
	'alt_field' => false,
	'attached_to' => true,
	'logs_path' => null,
	'php_error_logs' => false,
	'history' => false,
	'history_limit' => 4,
	'metadata_title' => true,
	'metadata_alt' => true,
	'metadata_description' => false,
	'metadata_caption' => false,

	'manual_prompt_alt' => 'The ALT is used mainly for SEO. It should be stuffed with keywords, but also describe the image. It should be a short sentence, typically starting with a uppercase letter and ending with a period.',

	'manual_prompt_caption' => 'It should be a very short description of the image, what\'s happening on it, in one sentence which typically starts with a uppercase letter and ends with a period.',

	'manual_prompt_filename' => 'Based on the rules we set before, actually generate 5 different filenames. They should be all quite different, from the less creative to the most creative. Separate them by a comma (,) and do not end with a period. Do not forget the extension which is used in Current Filename.',

	'manual_prompt_title' => 'It should be a concise, catchy title for the image that summarizes its main subject or theme. The title should be capitalized like a headline, typically 5-10 words long, and not end with a period. It should be engaging and optimized for both users and SEO.',

	'manual_prompt_description' => 'It should be an actual description the image, what\'s happening on it, in one paragraph. It is not adressing the user, but describing the image.',
]);

class Meow_MFRH_Core {

	public $admin = null;
	public $engine = null;
	public $pro = false;
	public $is_rest = false;
	public $is_cli = false;

	public $method = 'media_title';
	public $method_secondary = 'none';
	public $method_tertiary = 'none';

	public $last_used_method = null;
	
	public $upload_folder = null;
	public $site_url = null;
	public $currently_uploading = false;
	public $contentDir = null; // becomes 'wp-content/uploads'
	public $allow_usage = null;
	public $allow_setup = null;
	public $images_only = false;
	public $featured_only = false;
	public $images_mime_types = array(
		'image/jpeg', 
		'image/gif', 
		'image/png', 
		'image/bmp',
		'image/tiff', 
		'image/x-icon', 
		'image/webp', 
		'image/svg+xml'
	);

	private $on_upload_fields = [
		'sync_alt' => false,
		'post_title' => false,
		'post_content' => false,
		'post_excerpt' => false,
	];

	private $option_name = 'mfrh_options';
	private $log_file = 'media-file-renamer.log';
	static private $plugin_option_name = 'mfrh_options';

	public function __construct() {
		$this->site_url = get_site_url();
		$this->upload_folder = wp_upload_dir();
		$this->contentDir = substr( $this->upload_folder['baseurl'], 1 + strlen( $this->site_url ) );

		$this->allow_usage = apply_filters( 'mfrh_allow_usage', false );
		$this->allow_setup = apply_filters( 'mfrh_allow_setup', false );

		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		

		// Before use get_option function, it has to set up Meow_MFRH_Admin.
		// Part of the core, settings and stuff
		$this->is_cli = defined( 'WP_CLI' ) && WP_CLI;
		$this->allow_setup = apply_filters( 'mfrh_allow_setup', current_user_can( 'manage_options' ) );
		$this->admin = new Meow_MFRH_Admin( $this->allow_setup, $this );
		$this->engine = new Meow_MFRH_Engine( $this );
		if ( class_exists( 'MeowPro_MFRH_Core' ) ) {
			$this->pro = new MeowPro_MFRH_Core( $this, $this->admin, $this->engine );
		}

		// This should be checked after the init (is_rest checks the capacities)
		$this->is_rest = MeowCommon_Helpers::is_rest();
		$this->images_only = $this->get_option( 'images_only', false ) == 1;
		$this->featured_only = $this->get_option( 'featured_only', false ) == 1;

		// Check the roles
		$this->allow_usage = apply_filters( 'mfrh_allow_usage', current_user_can( 'administrator' ) );
		if ( !$this->is_cli && !$this->allow_usage ) {
			return;
		}

		// Languages
		load_plugin_textdomain( MFRH_DOMAIN, false, basename( MFRH_PATH ) . '/languages' );

		// Initialize
		$this->method = apply_filters( 'mfrh_method', $this->get_option( 'auto_rename', 'media_title' ) );
		$this->method_secondary = apply_filters( 'mfrh_method', $this->get_option( 'auto_rename_secondary', 'none' ), '_secondary' );
		$this->method_tertiary = apply_filters( 'mfrh_method', $this->get_option( 'auto_rename_tertiary', 'none' ), '_tertiary' );
		

		add_action( 'add_attachment', [ $this, 'on_upload_hook' ] );
		add_filter( 'wp_handle_upload_prefilter', [ $this, 'on_upload_hook_prefilter' ] );

		add_action( 'attachment_updated', array( $this, 'attachment_fields_to_save' ), 10, 3 );

		if ( $this->get_option( 'filename_prefix', '' ) != '' || $this->get_option( 'filename_suffix', '' ) != '' ) {
			add_filter( 'mfrh_new_filename', [ $this, 'add_prefix_suffix' ], 10, 3 );
		}

		if ( !empty( $this->get_option( 'filename_replace', [] ) ) ) {
			add_filter( 'mfrh_new_filename', [ $this, 'replace_filename' ], 10, 3 );
		}
		

		// Only for REST
		if ( $this->is_rest ) {
			new Meow_MFRH_Rest( $this );
		}

		// Side-updates should be ran for CLI and REST
		if ( is_admin() || $this->is_rest || $this->is_cli ) {
			new Meow_MFRH_Updates( $this );
			do_action( 'mfrh_initialize_parsers' );
			if ( $this->get_option( 'rename_on_save', false ) ) {
				add_action( 'save_post', array( $this, 'save_post' ) );
			}
		}

		// Admin screens
		if ( is_admin() ) {
			new Meow_MFRH_UI( $this );
		}
	}

	

	/**
	 *
	 * TOOLS / HELPERS
	 *
	 */
	static function get_plugin_option( $option, $default ) {
		$options = get_option( Meow_MFRH_Core::$plugin_option_name, null );
		return $options[$option] ?? $default;
	}

	// Check if the file exists, if it is, return the real path for it
	// https://stackoverflow.com/questions/3964793/php-case-insensitive-version-of-file-exists

	static function sensitive_file_exists( $filename ) {
	
		// Normalize the incoming $filename
		if (class_exists('Normalizer')) {
			$filename = Normalizer::normalize($filename, Normalizer::FORM_C);
		}
	
		$original_filename = $filename;
		$caseInsensitive = Meow_MFRH_Core::get_plugin_option( 'case_insensitive_check', false );
	
		$output = false;
		$directoryName = mfrh_dirname( $filename );
		// Gather all files in the directory.
		$fileArray = glob( $directoryName . '/*', GLOB_NOSORT );
	
		$i = $caseInsensitive ? 'i' : '';
	
		// Extract just the basename from $filename if path separators exist
		if ( preg_match( "/\\\|\//", $filename ) ) {
			$array = preg_split("/\\\|\//", $filename);
			$filename = $array[ count($array) - 1 ];
			// Re-normalize after splitting, just to be safe
			if (class_exists('Normalizer')) {
				$filename = Normalizer::normalize($filename, Normalizer::FORM_C);
			}
		}
	
		// Now compare each file in the directory
		foreach ( $fileArray as $file ) {
			// Normalize the file path from the filesystem
			if (class_exists('Normalizer')) {
				$file = Normalizer::normalize($file, Normalizer::FORM_C);
			}

			if ( preg_match("/\/" . preg_quote($filename, '/') . "$/{$i}u", $file) ) {
				$output = $file;
				break;
			}
		}
	
		return $output;
	}

	static function rmdir_recursive( $directory ) {
		foreach ( glob( "{$directory}/*" ) as $file ) {
			if ( is_dir( $file ) )
				Meow_MFRH_Core::rmdir_recursive( $file );
			else
				unlink( $file );
		}
		rmdir( $directory );
	}

	function wpml_media_is_installed() {
		return defined( 'WPML_MEDIA_VERSION' );
	}

	// To avoid issue with WPML Media for instance
	function is_real_media( $id ) {
		if ( $this->wpml_media_is_installed() ) {
			global $sitepress;
			$language = $sitepress->get_default_language( $id );
			return icl_object_id( $id, 'attachment', true, $language ) == $id;
		}
		return true;
	}

	function is_header_image( $id ) {
		static $headers = false;
		if ( $headers == false ) {
			global $wpdb;
			$headers = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attachment_is_custom_header'" );
		}
		return in_array( $id, $headers );
	}

	function generate_unique_filename( $actual, $dirname, $filename, $counter = null ) {
		$new_filename = $filename;
		if ( !is_null( $counter ) ) {
			$whereisdot = strrpos( $new_filename, '.' );
			$new_filename = substr( $new_filename, 0, $whereisdot ) . '-' . $counter
				. '.' . substr( $new_filename, $whereisdot + 1 );
		}
		if ( $actual == $new_filename )
			return false;
		if ( file_exists( $dirname . "/" . $new_filename ) )
			return $this->generate_unique_filename( $actual, $dirname, $filename,
				is_null( $counter ) ? 2 : $counter + 1 );
		return $new_filename;
	}

	function get_uploads_directory_hierarchy() {
		$uploads_dir = wp_upload_dir();
		$base_dir = $uploads_dir['basedir'];
		$directories = array();

		// Get all subdirectories of the base directory
		$dir_iterator = new RecursiveDirectoryIterator( $base_dir, FilesystemIterator::KEY_AS_PATHNAME|FilesystemIterator::CURRENT_AS_FILEINFO|FilesystemIterator::SKIP_DOTS );
		$iterator = new RecursiveIteratorIterator( $dir_iterator, RecursiveIteratorIterator::SELF_FIRST );
		foreach ( $iterator as $file ) {
			if ($file->isDir()) {
				// Remove base_dir from path
				$directory = str_replace( $base_dir, '', $file->getPathname() );
				if ( $directory ) {
					$directories[] = $directory;
				}
			}
		}

		// Return the hierarchy as a JSON file
		return json_encode($directories);
	}

	/**
	 * Returns all the media sharing the same file
	 * @param string $file The attached file path
	 * @param int|array $excludes The post ID(s) to exclude from the results
	 * @return array An array of IDs
	 */
	function get_posts_by_attached_file( $file, $excludes = null ) {
		global $wpdb;
		$r = array ();
		$q = <<< SQL
SELECT post_id
FROM {$wpdb->postmeta}
WHERE meta_key = '%s'
AND meta_value = '%s'
SQL;
		$rows = $wpdb->get_results( $wpdb->prepare( $q, '_wp_attached_file', _wp_relative_upload_path( $file ) ), OBJECT );
		if ( $rows && is_array( $rows ) ) {
			if ( !is_array( $excludes ) )
				$excludes = $excludes ? array ( (int) $excludes ) : array ();

			foreach ( $rows as $item ) {
				$id = (int) $item->post_id;
				if ( in_array( $id, $excludes ) ) continue;
				$r[] = $id;
			}
			$r = array_unique( $r );
		}
		return $r;
	}

	/*****************************************************************************
		RENAME ON UPLOAD
	*****************************************************************************/

	function set_sync_on_upload( $option ) {
		$this->on_upload_fields = [
			'sync_alt' => $this->get_option( 'sync_on_' . $option . '_alt', false ),
			'post_title' => $this->get_option( 'sync_on_' . $option . '_title', false ),
			'post_content' => $this->get_option( 'sync_on_' . $option . '_description', false ),
			'post_excerpt' => $this->get_option( 'sync_on_' . $option . '_caption', false ),
		];
	}

	function get_prefilter_option( $option ) {
		return $this->get_option( 'sync_on_' . $option . '_filename', false );
	}

	function on_upload_hook_prefilter( $file ) {
		if ( empty( $file ) || empty( $file['tmp_name'] ) ) {
			$this->log( "⚠️ File is empty or no name. (Prefilter)" );
			return $file;
		}

		$this->log( "⏰ Event: New Upload on Prefilter (" . $file['name'] . ")" );

		// If it's not an image, we don't do anything
		$filetype = $this->get_mime_type( $file['tmp_name'] );
		if( strpos( $filetype, 'image' ) === false ){
			$this->log( "⚠️ Not an image." );
			return $file;
    	}

		$upload_methods = [
			$this->get_option( 'on_upload_method', 'none' ),
			$this->get_option( 'on_upload_method_secondary', 'none' ),
			$this->get_option( 'on_upload_method_tertiary', 'none' ),
		];

		$done = false;
		foreach ( $upload_methods as $method ) {
			if ( $done ) { break; }

			if ( !$this->get_prefilter_option( $method ) ) { continue; }
			switch ( $method ) {
				case 'upload_exif': // "auto" => EXIF Title
					list( $done, $file ) = $this->exif_data_upload_prefilter( $file );
					break;
				case 'upload_clean':
					list( $done, $file ) = $this->clean_upload_prefilter( $file );
					break;
				case 'upload_vision':
					list( $done, $file ) = $this->vision_rename_ai_on_upload_prefilter( $file );
					break;
				default:
					$done = false;
					break;
			}
		}


		return $file;
	}

	function on_upload_hook( $id ) {
		if ( !wp_attachment_is_image( $id ) ) {
			$this->log( "⚠️ Not an image." );
			return;
		}

		$post = get_post( $id );
		if(!$post) {
			$this->log( "⚠️ Post not found." );
			return;
		}

		$this->log( "⏰ New Upload (" . $post->post_title . ")" );
		$done = false;
		
		$upload_methods = [
			$this->get_option( 'on_upload_method', 'none' ),
			$this->get_option( 'on_upload_method_secondary', 'none' ),
			$this->get_option( 'on_upload_method_tertiary', 'none' ),
		];

		foreach ( $upload_methods as $method ) {
			if ( $done ) { break; }
			$this->set_sync_on_upload( $method );

			switch ( $method ) {
				case 'upload_exif': // "auto" => EXIF Title
					$this->log( "🗒️ Trying EXIF Title... " );
					$done = $this->exif_data_upload( $post );
					
					break;
				case 'upload_clean':
					$this->log( "🗒️ Trying Clean Upload... " );
					$done = $this->clean_upload( $post );
					
					break;
				case 'upload_vision':
					$this->log( "🗒️ Trying Vision Rename AI... " );
					$done = $this->vision_rename_ai_on_upload( $post );
					
					break;
				default:
					$done = false;
					break;
			}
		}

		$this->log( "👌 Done." );
	}

	function rename_media_on_post_upload( $post ) {
		try {
			$parent_post_id = $post->post_parent;
			
			if ( $parent_post_id ) {
				$parent_post = get_post( $parent_post_id );

				if ( is_null( $parent_post ) ) {
					$this->log( "⚠️ Parent post not found." );
					return false;
				}
				
				if ( $parent_post && $parent_post->post_type === 'post' ) {
					$new_title = $parent_post->post_title . ' - ' . $post->post_title;
					$new_title = wp_unique_filename( dirname( get_attached_file( $post->ID ) ), $new_title );
					
					$my_image_meta = [
						'ID' => $post->ID,
						'post_title' => $new_title,
					];
					
					$result = wp_update_post( $my_image_meta );
					return $result != 0;
				}
			}
			
			return false;
		} catch (Exception $e) {
			$this->log( '⚠️ Rename Media on Post Upload failed: ' . $e->getMessage() );
			return false;
		}
	}

	/**
	 * Get the EXIF data from an image. If a field is specified, only that field will be returned.
	 * Otherwise, an associative array containing all the EXIF data will be returned.
	 * @param string $path The path to the file
	 * @param string $field The field to return
	 * @return array An associative array containing the EXIF data, or a specific field if specified
	 */
	function get_exif_data( $path, $field = null ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$exif = wp_read_image_metadata( $path );
		if ( empty( $exif ) ) { return null; }
		if ( !empty( $field ) ) {
			return isset( $exif[ $field ] ) ? $exif[ $field ] : null;
		}
		$allData = [];
		if ( !empty( $exif['title'] ) ) {
			$allData['title'] = $exif['title'];
		}
		if ( !empty( $exif['caption'] ) ) {
			$allData['caption'] = $exif['caption'];
		}
		if ( !empty( $exif['keywords'] ) ) {
			$allData['keywords'] = implode( ', ', $exif['keywords'] );
		}
		return $allData;
	}

	function exif_data_upload_prefilter ( $file ) {
		try {
			$title = $this->get_exif_data( $file['tmp_name'], 'title' );
			if ( !empty( $title ) ) {
				$filename = $this->engine->new_filename( $title, $file['name'] );
				if ( !is_null( $filename ) ) {
					$file['name'] = $filename;
					$this->log( "👌 Title EXIF found." );
					$this->log( "New file should be: " . $file['name'] );
					return [ true, $file ];
				}
				return [ false, $file ];
			}
			else
			{
				$this->log( "😭 Title EXIF not found." );
				return [ false, $file ];
			}
		}
		catch ( Exception $e ) {
			$this->log( '⚠️ EXIF Data failed: ' . $e->getMessage() );
			return [ false, $file ];
		}
	}
				
	function exif_data_upload ( $post ) {
		try {
			$file = get_attached_file( $post->ID );
			$title = $this->get_exif_data( $file, 'title' );
			if ( !empty( $title ) ) {
				$my_image_title = $title;
				$my_image_title = preg_replace( '%\s*[-_\s]+\s*%', ' ', $my_image_title );
				$my_image_title = ucwords( strtolower( $my_image_title ) );
				$my_image_meta = array( 'ID' => $post->ID );
				foreach ( $this->on_upload_fields as $field => $sync ) {
					if ( $sync ){
						if ( $field == 'sync_alt' ) {
							$my_image_title = apply_filters( 'mfrh_exif_upload_alt', $my_image_title );
							update_post_meta( $post->ID, '_wp_attachment_image_alt', $my_image_title );
						}
						else {
							$value = apply_filters( 'mfrh_exif_upload_' . $field, $my_image_title );
							$my_image_meta[$field] = $value;
						}
					}
				}
				$result = wp_update_post( $my_image_meta );
				return $result != 0;
			}
			return false;
		}
		catch ( Exception $e ) {
			$this->log( '⚠️ EXIF Data failed: ' . $e->getMessage() );
			return false;
		}
	}

	function clean_upload_prefilter( $file ) {
		try {
			
			$image_title = preg_replace( '%\s*[-_\s]+\s*%', ' ', $file['name'] );
			$image_title = ucwords( strtolower( $image_title ) );

			$filename = $this->engine->new_filename( $image_title, $file['name'] );
			if ( !is_null( $filename ) ) {
				$file['name'] = $filename;
				$this->log( "👌 Clean Upload found." );
				$this->log( "New file should be: " . $file['name'] );
				return [ true, $file ];
			}

			return [ false, $file ];

		} catch (Exception $e) {
			$this->log( '⚠️ Clean Upload failed: ' . $e->getMessage() );
			return [ false, $file ];
		}
		
	}

	function clean_upload( $post ) {
		try {
			$my_image_title = preg_replace('%\s*[-_\s]+\s*%', ' ', $post->post_title);
			$my_image_title = ucwords(strtolower($my_image_title));
			
			$my_image_meta = [
				'ID' => $post->ID,
			];

			foreach ( $this->on_upload_fields as $field => $sync ) {
				if ( $sync ){
					if ( $field == 'sync_alt' ) {
						$my_image_title = apply_filters( 'mfrh_clean_upload_alt', $my_image_title );
						update_post_meta($post->ID, '_wp_attachment_image_alt', $my_image_title);
					}
					else {
						$value = apply_filters( 'mfrh_exif_upload_' . $field, $my_image_title );
						$my_image_meta[$field] = $value;
					}
				}
			}

			$result = wp_update_post($my_image_meta);
			return $result != 0;
			

			return true;
		} catch (Exception $e) {
			$this->log( '⚠️ Clean Upload failed: ' . $e->getMessage() );
			return false;
		}
	}

	function vision_rename_ai_on_upload_prefilter( $file ) {
		try {

			if ( !has_filter( 'mfrh_vision_suggestion' ) ) {
				if ( $this->pro ) {
					add_filter( 'mfrh_vision_suggestion', array( $this->pro, 'vision_suggestion' ), 10, 4 );
				} else {
					$this->log( '⚠️ Vision AI is enabled but no filter is set.' );
					return [ false, $file ];
				}
			}

			$binary = file_get_contents( $file['tmp_name'] );
			if ( !$binary ) {
				$this->log( '⚠️ Vision AI: Could not read the file.' );
				return [ false, $file ];
			}

			$filename = $this->ai_suggestion( null, 'filename', $file['tmp_name'] );
			$filename = $this->engine->new_filename( $filename, $file['name'] );

			if ( $filename ) {
				$file['name'] = $filename;
				$this->log( "👌 Vision AI found." );
				$this->log( "New file should be: " . $file['name'] );
				return [ true, $file ];
			}

			return [ false, $file ];

		}catch (Exception $e) {
			$this->log( '⚠️ Vision AI failed: ' . $e->getMessage() );
			return [ false, $file ];
		}
	}

	function vision_rename_ai_on_upload( $post ){
		try {

			if ( !has_filter( 'mfrh_vision_suggestion' ) ) {
				if ( $this->pro ) {
					add_filter( 'mfrh_vision_suggestion', array( $this->pro, 'vision_suggestion' ), 10, 4 );
				} else {
					$this->log( '⚠️ Vision AI is enabled but no filter is set.' );
					return false;
				}
			}

			$conversion = [
				'sync_alt' => 'alternative text',
				'post_title' => 'title',
				'post_content' => 'description',
				'post_excerpt' => 'caption',
			];

			$results = [];
			$my_image_meta = [
				'ID' => $post->ID,
			];

			foreach ( $this->on_upload_fields as $field => $sync ) {
				if ( $sync ){
					$metadataType = $conversion[$field];
					$newMetadata = $this->ai_suggestion( $post->ID, $metadataType );

					if ( $newMetadata ) {
						if ( $field == 'sync_alt' ) {
							$alt = apply_filters( 'mfrh_vision_upload_alt', $newMetadata );
							update_post_meta( $post->ID, '_wp_attachment_image_alt', $alt );
						}
						else {
							$value = apply_filters( 'mfrh_exif_upload_' . $field, $newMetadata );
							$my_image_meta [ $field ] = $value;
						}
					}
				}
			}

			$result = wp_update_post( $my_image_meta );
			return $result != 0;

		} catch (Exception $e) {
			$this->log( '⚠️ Vision AI failed: ' . $e->getMessage() );
			return false;
		}
	}

	function after_image_upload( $metadata, $attachment_id, $context ) {
		if ( $this->currently_uploading ) {
			$metadata = apply_filters( 'mfrh_after_upload', $metadata, $attachment_id );
		}
		return $metadata;
	}

	function wp_handle_upload_prefilter( $file ) {
		$this->log( "⏰ Event: New Upload (" . $file['name'] . ")" );
		$pp = mfrh_pathinfo( $file['name'] );
		$this->currently_uploading = true; 

		// If everything's fine, renames in based on the Title in the EXIF
		switch ( $this->method ) {
			case 'media_title':
				$this->log( "🗒️ Trying Media Title" );
				$exif = wp_read_image_metadata( $file['tmp_name'] );
				if ( !empty( $exif ) && isset( $exif[ 'title' ] ) && !empty( $exif[ 'title' ] ) ) {
					$new_filename = $this->engine->new_filename( $exif[ 'title' ], $file['name'] );
					if ( !is_null( $new_filename ) ) {
						$file['name'] = $new_filename;
						$this->log( "👌 Title EXIF found." );
						$this->log( "New file should be: " . $file['name'] );
					}					
					return $file;
				}else{
					$this->log( "😭 Title EXIF not found." );
				}
			break;
			case 'post_title':
				$this->log( "🗒️ Trying Post Title" );
				if ( !isset( $_POST['post_id'] ) || $_POST['post_id'] < 1 ) break;
				$post = get_post( $_POST['post_id'] );
				if ( !empty( $post ) && !empty( $post->post_title ) ) {
					$new_filename = $this->engine->new_filename( $post->post_title, $file['name'] );
					if ( !is_null( $new_filename ) ) {
						$file['name'] = $new_filename;
						$this->log( "👌 Post Title found." );
						$this->log( "New file should be: " . $file['name'] );
					}
					return $file;
				}else{
					$this->log( "😭 Post Title not found." );
				}
			case 'post_acf_field':
				if ( !isset( $_POST['post_id'] ) || $_POST['post_id'] < 1 ) break;
				$acf_field_name = $this->get_option('acf_field_name', false);
				if ($acf_field_name) {
					$new_filename = $this->engine->new_filename( get_field($acf_field_name, $_POST['post_id']), $file['name'] );
					if ( !is_null( $new_filename ) ) {
						$file['name'] = $new_filename;
						$this->log( "New file should be: " . $file['name'] );
					}
					return $file;
				}
			break;
		}
		// Otherwise, let's do the basics based on the filename

		// The name will be modified at this point so let's keep it in a global
		// and re-inject it later
		global $mfrh_title_override;
		$mfrh_title_override = $pp['filename'];
		add_filter( 'wp_read_image_metadata', array( $this, 'wp_read_image_metadata' ), 10, 2 );

		// Modify the filename
		$pp = mfrh_pathinfo( $file['name'] );
		$new_filename = $this->engine->new_filename( $pp['filename'], $file['name'] );
		if ( !is_null( $new_filename ) ) {
			$file['name'] = $new_filename;
		}
		return $file;
	}

	function wp_read_image_metadata( $meta, $file ) {
		// Override the title, without this it is using the new filename
		global $mfrh_title_override;
    $meta['title'] = $mfrh_title_override;
    return $meta;
	}

	/****************************************************************************/

	function add_prefix_suffix( $new, $old, $post ) {

		$prefix = $this->get_option( 'filename_prefix', '' );
		$suffix = $this->get_option( 'filename_suffix', '' );
		
		if ( $prefix != '' ) {
			$new = $prefix . $new;
		}

		if ( $suffix != '' ) {
			$new = $new . $suffix;
		}
		
		return $new;
	}

	function replace_filename( $new, $old, $post ) {
		$replacements = $this->get_option( 'filename_replace', [] );

		if ( !empty( $replacements ) ) {
			foreach ( $replacements as $replacement ) {
				$replacement = explode( '=>', $replacement );
				if ( count( $replacement ) == 2 ) {
					$new = str_replace( $replacement[0], $replacement[1], $new );
				}
			}
		}

		return $new;
	}

	// Return false if everything is fine, otherwise return true with an output
	// which details the conditions and results about the renaming.
	function check_attachment( $post, &$output = array(), $manual_filename = null, $force_rename = false, $preview = true, $skipped_methods = [] ) {
		$id = $post['ID'];
		$old_filepath = get_attached_file( $id );

		if( PHP_OS_FAMILY == 'Windows' ) {
			$old_filepath = str_replace( '\\', '/', $old_filepath );	
		}

		$old_filepath = !$force_rename ? Meow_MFRH_Core::sensitive_file_exists( $old_filepath ): $old_filepath;
		$path_parts = mfrh_pathinfo( $old_filepath );

		if ( $this->images_only ) {
			$is_image = in_array( $post['post_mime_type'], $this->images_mime_types );
			if ( !$is_image ) {
				$this->log( "😭 Not an image." );
				return false;
			}
		}

		// If the file doesn't exist, let's not go further.
		if ( !$force_rename && ( !isset( $path_parts['dirname'] ) || !isset( $path_parts['basename'] ) ) ) {
			if (!isset($path_parts['dirname'])) {
				$this->log("😕 Directory name is missing in path parts.");
			}
			if (!isset($path_parts['basename'])) {
				$this->log("😕 Base name is missing in path parts.");
			}
			$this->log("😭 File doesn't exist. Path parts: " . print_r($path_parts, true) . " | " . $old_filepath );
			return false;
		}

		//print_r( $path_parts );
		$directory = isset( $path_parts['dirname'] ) ? $path_parts['dirname'] : null;
		$old_filename = isset( $path_parts['basename'] ) ? $path_parts['basename'] : null;

		// Check if media/file is dead
		if ( !$force_rename && ( !$old_filepath || !file_exists( $old_filepath ) ) ) {
			if (!$old_filepath) {
				$this->log("😕 Old file path is missing.");
			} else if (!file_exists($old_filepath)) {
				$this->log("😕 File path provided but file does not exist: " . $old_filepath);
			}
			delete_post_meta( $id, '_require_file_renaming' );
			$this->log( "😭 File doesn't exist." );
			return false;
		}

		// Is it forced/manual
		// Check mfrh_new_filename (coming from manual input) if it is different than previous filename
		if ( empty( $manual_filename ) && isset( $post['mfrh_new_filename'] ) ) {
			if ( strtolower( $post['mfrh_new_filename'] ) != strtolower( $old_filename ) ){
				$manual_filename =  $post['mfrh_new_filename'];
			}
		}

		if ( $force_rename ) {
			$new_filename = $manual_filename;
			$output['manual'] = true;
		}
		else if ( !empty( $manual_filename ) ) {
			// Through the new_filename function to rename when the sanitize option is enabled.
			// To validate the filename (i.g. space will be “-“), use the $manual_filename as the first argument $text.
			$new_filename = $this->get_option( 'manual_sanitize', true )
				? $this->engine->new_filename( $manual_filename, $old_filename, null, $post )
				: $manual_filename;

			$this->last_used_method = 'manual';
			$output['manual'] = true;
		}
		else {
			if ( $this->method === 'none') {
				// If no methods are selected, let the user do an automatic rename to trigger filters
				$new_filename = $this->engine->new_filename( $old_filename, $old_filename, null, $post );
				if ( $new_filename !== $old_filename ) {
					$this->log( "⚡ Without method, but with filters." );
				} else {
					delete_post_meta( $id, '_require_file_renaming' );
					$this->log( "😭 No method." );
					return false;
				}	
			}
			$lock_enabled = $this->get_option( 'lock' );
			if ( $lock_enabled && get_post_meta( $id, '_manual_file_renaming', true ) ) {
				$this->log( "😭 Locked." );
				return false;
			}

			// Skip header images
			if ( $this->is_header_image( $id ) ) {
				delete_post_meta( $id, '_require_file_renaming' );
				$this->log( "😭 Header Image." );
				return false;
			}

			// check if the filter exists
			if ( !has_filter( 'mfrh_base_for_rename' ) && class_exists( 'MeowPro_MFRH_Core' ) ) {
				$this->log( " ⚠️ The filter was not found. Initializing Meow Pro." );
				$this->pro = new MeowPro_MFRH_Core( $this, $this->admin, $this->engine );
				add_filter( 'mfrh_base_for_rename', [ $this->pro, 'base_for_rename' ], 10, 4);
			}

			$base_for_rename = $this->pro ? apply_filters( 'mfrh_base_for_rename', $post['post_title'], $id, $preview, $skipped_methods ) : $this->core_base_for_rename( $id, $skipped_methods );

			if ( $base_for_rename !== '{VISION}') {
				$new_filename = $this->engine->new_filename( $base_for_rename, $old_filename, null, $post );
			} else {
				// TODO: This should be probably handled by the UI, not here.
				$new_filename = 'Auto with AI Vision...';
			}

			if ( is_null( $new_filename ) ) {
				$this->log( "😭 No new filename." );
				return false; // Leave it as it is
			}
		}

		// If a filename has a counter, and the ideal is without the counter, let's ignore it
		$ideal = preg_replace( '/-[1-9]{1,10}\./', '$1.', $old_filename );
		if ( !$manual_filename ) {
			if ( $ideal == $new_filename ) {
				delete_post_meta( $id, '_require_file_renaming' );
				$this->log( "😭 Ideal filename." );
				return false;
			}
		}

		// Filename is equal to sanitized title
		if ( $new_filename == $old_filename ) {
			delete_post_meta( $id, '_require_file_renaming' );
			$this->log( "😭 Same filename." );
			return false;
		}

		// Check for case issue, numbering
		//if ( !$force_rename ) {
		$ideal_filename = $new_filename;
		$new_filepath = trailingslashit( $directory ) . $new_filename;
		$existing_file = Meow_MFRH_Core::sensitive_file_exists( $new_filepath );
		$case_issue = strtolower( $old_filename ) == strtolower( $new_filename );
		if ( !$force_rename && $existing_file && !$case_issue ) {
			$is_numbered = apply_filters( 'mfrh_numbered', false );
			if ( $is_numbered ) {
				$new_filename = $this->generate_unique_filename( $ideal, $directory, $new_filename );
				if ( !$new_filename ) {
					$this->log( "😭 Numbered: No new filename." );
					delete_post_meta( $id, '_require_file_renaming' );
					return false;
				}
				$new_filepath = trailingslashit( $directory ) . $new_filename;
				$existing_file = Meow_MFRH_Core::sensitive_file_exists( $new_filepath );
			}
		}
		//}

		// Send info to the requester function
	
		$output['post_id'] = $id;
		$output['post_name'] = $post['post_name'];
		$output['post_title'] = $post['post_title'];
		$output['current_filename'] = $old_filename;
		$output['current_filepath'] = $old_filepath;
		$output['ideal_filename'] = $ideal_filename;
		$output['proposed_filename'] = $new_filename;
		$output['desired_filepath'] = $new_filepath;
		$output['case_issue'] = $case_issue;
		$output['manual'] = !empty( $manual_filename );
		$output['locked'] = get_post_meta( $id, '_manual_file_renaming', true );
		$output['proposed_filename_exists'] = !!$existing_file;
		$output['original_image'] = null;
		$output['used_method'] = $this->last_used_method;
		$output['skipped_methods'] = empty( $skipped_methods ) ? null : $skipped_methods;
		
		// If the ideal filename already exists
		// Maybe that's the original_image! If yes, we should let it go through
		// as the original_rename will be renamed into another filename anyway.
		if ( !!$existing_file ) {
			$meta = wp_get_attachment_metadata( $id );
			if ( isset( $meta['original_image'] ) && $new_filename === $meta['original_image'] ) {
				$output['original_image'] = $meta['original_image'];
				$output['proposed_filename_exists'] = false;
			}
		}

		// -- 
		if ( $output['proposed_filename_exists'] && empty( $manual_filename ) ) {
			$skipped_methods = array_merge( $skipped_methods, [ $this->last_used_method ] );

			if ( count( $skipped_methods ) < 3 ) {
				return $this->check_attachment( $post, $output, $manual_filename, $force_rename, $preview, $skipped_methods );
			} else {
				$this->log( "😭 More than 3 skipped methods, no new filename." );
			}

		}

		// Set the '_require_file_renaming', even though it's not really used at this point (but will be,
		// with the new UI).
		if ( !get_post_meta( $post['ID'], '_require_file_renaming', true ) && !$output['locked']) {
			add_post_meta( $post['ID'], '_require_file_renaming', true, true );
		}

		return true;
	}

	function core_base_for_rename( $id , $skipped_methods = [] )
	{
		$base_for_rename = null;
		$methods = [ $this->method, $this->method_secondary, $this->method_tertiary ];
		
		foreach ( $methods as $method ) {
			if ( in_array( $method, $skipped_methods ) ) {
				$this->log( "✒️ Method " . $method . " was skipped." );
				continue;
			}
			switch ( $method ) {
				case 'none':
					$base_for_rename = null;
					break;
				case 'media_title':
					$base_for_rename = get_the_title( $id );
					break;
				case 'alt_text':
					$image_alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
					$base_for_rename = $image_alt;
					break;
				default:
					$this->log( "⚠️ Method " . $method . " not found." );
					break;
			}
			if ( !is_null( $base_for_rename ) ) {
				$this->last_used_method = $method;
				break;
			}
			else {
				$this->log( "✒️ Method " . $method . " returned null. Trying next method." );
			}
		}
		return !is_null($base_for_rename) ? html_entity_decode($base_for_rename) : '';
	}

	function check_text() {
		$issues = array();
		global $wpdb;
		$ids = $wpdb->get_col( "
			SELECT p.ID
			FROM $wpdb->posts p
			WHERE post_status = 'inherit'
			AND post_type = 'attachment'
		" );
		foreach ( $ids as $id )
			if ( $this->check_attachment( get_post( $id, ARRAY_A ), $output ) )
				array_push( $issues, $output );
		return $issues;
	}

	/**
	 *
	 * RENAME ON SAVE / PUBLISH
	 * Originally proposed by Ben Heller
	 * Added and modified by Jordy Meow
	 */

	function save_post( $post_id ) {
		$this->log( '⏰ Event: Save Post' );
		$status = get_post_status( $post_id );
		if ( !in_array( $status, array( 'publish', 'draft', 'future', 'private' ) ) ) {
			$this->log( "😭 Not a valid status for renaming: " . $status );
			return;
		}

		$args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' =>'any', 'post_parent' => $post_id );
		$medias = get_posts( $args );

		if ( $medias ) {
			
			foreach ( $medias as $attach ) {
				$this->engine->rename( $attach->ID, null, false, 'updated' );
			}
		} else {
			$this->log( "😭 No media found on save. ( The medias might be attached to a prior post parent )." );
		}
	}




	/**
	 *
	 * EDITOR
	 *
	 */

	function attachment_fields_to_save( $post_id, $post_after, $post_before ) {

		$should_rename = $this->get_option( 'trigger_on_attachment_update', false );
		if ( !$should_rename ) { return; }	

		// Check that it's actually an attachment post type
		if ( get_post_type( $post_id ) !== 'attachment' ) {
			return;
		}

		$this->log( '⏰ Event: Attachment Fields updated.' );
		$this->engine->rename( $post_after, null, false, 'updated' );
	}

	function log_sql( $data, $antidata ) {
		if ( !$this->get_option( 'logsql' ) || !$this->admin->is_registered() )
			return;
		$dir = wp_upload_dir();
		$dir = $dir['basedir'];
		$fh = fopen( trailingslashit( $dir ) . 'mfrh_sql.log', 'a' );
		$fh_anti = fopen( trailingslashit( $dir ) . 'mfrh_sql_revert.log', 'a' );
		fwrite( $fh, "{$data}\n" );
		fwrite( $fh_anti, "{$antidata}\n" );
		fclose( $fh );
		fclose( $fh_anti );
	}

	// MFRH_PREFIX

	function get_logs_path() {
		$uploads_dir = wp_upload_dir();
		$uploads_dir_path = trailingslashit( $uploads_dir['basedir'] );

		$path = $this->get_option( 'logs_path' );

		if ( $path && file_exists( $path ) ) {
			// make sure the path is legal (within the uploads directory with the MFRH_PREFIX prefix and log extension)
			if ( strpos( $path, $uploads_dir_path ) !== 0 || strpos( $path, MFRH_PREFIX ) === false || substr( $path, -4 ) !== '.log' ) {
				$path = null;
			} else {
				return $path;
			}
		}

		if ( !$path ) {
			$path = $uploads_dir_path . MFRH_PREFIX . "_" . $this->random_ascii_chars() . ".log";
			if ( !file_exists( $path ) ) {
				touch( $path );
			}
			
			$options = $this->get_all_options();
			$options['logs_path'] = $path;
			$this->update_options( $options );
		}

		return $path;
	}

	function log( $data = null ) {
		$log = $this->get_option( 'log', false );
		if ( !$log ) { return false; }

		$php_logs = $this->get_option( 'php_error_logs', false );
		if ( $php_logs ) {
			error_log( $data );
		}
		
		$log_file_path = $this->get_logs_path();
		$fh = @fopen( $log_file_path, 'a' );
		if ( !$fh ) { return false; }
		$date = date( "Y-m-d H:i:s" );
		if ( is_null( $data ) ) {
			fwrite( $fh, "\n" );
		}
		else {
			fwrite( $fh, "$date: {$data}\n" );
		}
		fclose( $fh );
		return true;
	}

	function get_logs() {
		$log_file_path = $this->get_logs_path();

		if ( !file_exists( $log_file_path ) ) {
			return "No logs found.";
		}

		$content = file_get_contents( $log_file_path );
		$lines = explode( "\n", $content );
		$lines = array_filter( $lines );
		$lines = array_reverse( $lines );
		$content = implode( "\n", $lines );
		return $content;
	}

	function clear_logs() {
		$logPath = $this->get_logs_path();
		if ( file_exists( $logPath ) ) {
			unlink( $logPath );
		}

		$options = $this->get_all_options();
		$options['logs_path'] = null;
		$this->update_options( $options );
	}

	// Only replace the first occurence
	function str_replace( $needle, $replace, $haystack ) {
		if ( empty( $needle ) || empty( $haystack ) ) {
			return $haystack;
		}
		$pos = strpos( $haystack, $needle );
		if ( $pos !== false )
			$haystack = substr_replace( $haystack, $replace, $pos, strlen( $needle ) );
		return $haystack;
	}

	/**
	 *
	 * RENAME FILES + COFFEE TIME
	 */
	// From a url to the shortened and cleaned url (for example '2025/02/file.png')
	function clean_url( $url ) {
		$dirIndex = strpos( $url, $this->contentDir );
		if ( empty( $url ) || $dirIndex === false ) {
			$finalUrl =  null;
		}
		else {
			$finalUrl = urldecode( substr( $url, 1 + strlen( $this->contentDir ) + $dirIndex ) );
		}
		return $finalUrl;
	}

	function call_hooks_rename_url( $post, $orig_image_url, $new_image_url, $size = 'N/A'  ) {
		// With the full URLs
		// 2021/11/03: I am not sure we need this, since the clean URLs would also match
		// do_action( 'mfrh_url_renamed', $post, $orig_image_url, $new_image_url );

		// With clean URLs relative to /uploads
		$cleaned_orig_image_url = $this->clean_url( $orig_image_url );
		$cleaned_new_image_url = $this->clean_url( $new_image_url );
		if ( !empty( $cleaned_orig_image_url ) && !empty( $cleaned_new_image_url ) ) {
		do_action( 'mfrh_url_renamed', $post, $cleaned_orig_image_url, $cleaned_new_image_url, $size );
		}

		// With DB URLs (honestly, not sure about this...)
		//  $upload_dir = wp_upload_dir();
		//  do_action( 'mfrh_url_renamed', $post, str_replace( $upload_dir, "", $orig_image_url ),
		//  	str_replace( $upload_dir, "", $new_image_url ) );
	}

	function create_folder( $directory_path ) {
		$upload_dir = wp_upload_dir();
		$new_directory_path = trailingslashit( $upload_dir['basedir'] ) . trim( $directory_path, '/' );

		if ( file_exists( $new_directory_path ) ) {
			$this->log( "🚫 The directory already existed: $new_directory_path" );
			throw new Exception( __( 'The directory already existed.', 'media-file-renamer') );
		}

		if ( !mkdir( $new_directory_path, 0777, true ) ) {
			$this->log( "🚫 The directory couldn't be created: $new_directory_path" );
			throw new Exception( __( "The directory couldn't be created.", 'media-file-renamer') );
		}
		$this->log( "✅ The directory was created: $new_directory_path" );
	}

	function move( $media, $newPath ) {
		$id = null;
		$post = null;

		if ( PHP_OS_FAMILY == 'Windows' ) {
			$newPath = str_replace( '\\', '/', $newPath );
		}

		// Check the arguments
		if ( is_numeric( $media ) ) {
			$id = $media;
			$post = get_post( $media, ARRAY_A );
		}
		else if ( is_array( $media ) ) {
			$id = $media['ID'];
			$post = $media;
		}
		else {
			die( 'Media File Renamer: move() requires the ID or the array for the media.' );
		}

		// Prepare the variables
		$orig_attachment_url = null;

		$old_filepath = get_attached_file( $id );
		if ( PHP_OS_FAMILY == 'Windows' ) {
			$old_filepath = str_replace( '\\', '/', $old_filepath );
		}

		$path_parts = mfrh_pathinfo( $old_filepath );
		$old_ext = $path_parts['extension'];
		$upload_dir = wp_upload_dir();
		if ( PHP_OS_FAMILY == 'Windows' ) {
			$upload_dir['basedir'] = str_replace( '\\', '/', $upload_dir['basedir'] );
		}

		$old_directory = trim( str_replace( $upload_dir['basedir'], '', $path_parts['dirname'] ), '/' ); // '2011/01'
		$new_directory = trim( $newPath, '/' );
		$filename = $path_parts['basename']; // 'whatever.jpeg'
		$new_filepath = trailingslashit( trailingslashit( $upload_dir['basedir'] ) . $new_directory ) . $filename;

		$this->log( "🏁 Move Media: " . $filename );
		$this->log( "The new directory will be: " . mfrh_dirname( $new_filepath ) );

		// Create the directory if it does not exist
		if ( !file_exists( mfrh_dirname( $new_filepath ) ) ) {
			mkdir( mfrh_dirname( $new_filepath ), 0777, true );
		}

		// There is no support for UNDO (as the current process of Media File Renamer doesn't keep the path for the undo, only the filename... so the move breaks this - let's deal with this later).

		// Move the main media file
		if ( !$this->engine->rename_file( $old_filepath, $new_filepath ) ) {
			$this->log( "🚫 File $old_filepath ➡️ $new_filepath" );
			return false;
		}
		$this->log( "✅ File $old_filepath ➡️ $new_filepath" );
		do_action( 'mfrh_path_renamed', $post, $old_filepath, $new_filepath );

		// Handle the WebP if it exists
		$this->engine->rename_alternative_image_formats( $old_filepath, $old_ext, $new_filepath, $old_ext );

		// Update the attachment meta
		$meta = wp_get_attachment_metadata( $id );

		if ( $meta ) {
			if ( isset( $meta['file'] ) && !empty( $meta['file'] ) )
				$meta['file'] = $this->str_replace( $old_directory, $new_directory, $meta['file'] );
			if ( isset( $meta['url'] ) && !empty( $meta['url'] ) && strlen( $meta['url'] ) > 4 )
				$meta['url'] = $this->str_replace( $old_directory, $new_directory, $meta['url'] );
			//wp_update_attachment_metadata( $id, $meta );
		}

		// Better to check like this rather than with wp_attachment_is_image
		// PDFs also have thumbnails now, since WP 4.7
		$has_thumbnails = isset( $meta['sizes'] );

		if ( $has_thumbnails ) {

			// Support for the original image if it was "-rescaled".
			$is_scaled_image = isset( $meta['original_image'] ) && !empty( $meta['original_image'] );
			if ( $is_scaled_image ) {
				$meta_old_filename = $meta['original_image'];
				$meta_old_filepath = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $old_directory ) . $meta_old_filename;
				$meta_new_filepath = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $new_directory ) . $meta_old_filename;
				if ( !$this->engine->rename_file( $meta_old_filepath, $meta_new_filepath ) ) {
					$this->log( "🚫 File $meta_old_filepath ➡️ $meta_new_filepath" );
				}
				else {
					$this->log( "✅ File $meta_old_filepath ➡️ $meta_new_filepath" );
					do_action( 'mfrh_path_renamed', $post, $meta_old_filepath, $meta_new_filepath );
				}
			}

			// Image Sizes (Thumbnails)
			$orig_image_urls = array();
			$orig_image_data = wp_get_attachment_image_src( $id, 'full' );
			$orig_image_urls['full'] = $orig_image_data[0];
			foreach ( $meta['sizes'] as $size => $meta_size ) {
				if ( !isset($meta['sizes'][$size]['file'] ) )
					continue;
				$meta_old_filename = $meta['sizes'][$size]['file'];
				$meta_old_filepath = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $old_directory ) . $meta_old_filename;
				$meta_new_filepath = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $new_directory ) . $meta_old_filename;
				$orig_image_data = wp_get_attachment_image_src( $id, $size );
				$orig_image_urls[$size] = $orig_image_data[0];

				// Double check files exist before trying to rename.
				if ( file_exists( $meta_old_filepath )
						&& ( ( !file_exists( $meta_new_filepath ) ) || is_writable( $meta_new_filepath ) ) ) {
					// WP Retina 2x is detected, let's rename those files as well
					if ( function_exists( 'wr2x_get_retina' ) ) {
						$wr2x_old_filepath = $this->str_replace( '.' . $old_ext, '@2x.' . $old_ext, $meta_old_filepath );
						$wr2x_new_filepath = $this->str_replace( '.' . $old_ext, '@2x.' . $old_ext, $meta_new_filepath );
						if ( file_exists( $wr2x_old_filepath )
							&& ( ( !file_exists( $wr2x_new_filepath ) ) || is_writable( $wr2x_new_filepath ) ) ) {

							// Rename retina file
							if ( !$this->engine->rename_file( $wr2x_old_filepath, $wr2x_new_filepath ) ) {
								$this->log( "🚫 Retina $wr2x_old_filepath ➡️ $wr2x_new_filepath" );
								return $post;
							}
							$this->log( "✅ Retina $wr2x_old_filepath ➡️ $wr2x_new_filepath" );
							do_action( 'mfrh_path_renamed', $post, $wr2x_old_filepath, $wr2x_new_filepath );
						}
					}

					// Handle the WebP if it exists
					$this->engine->rename_alternative_image_formats( $meta_old_filepath, $old_ext, $meta_new_filepath, $old_ext );

					// Rename meta file
					if ( !$this->engine->rename_file( $meta_old_filepath, $meta_new_filepath ) ) {
						$this->log( "🚫 File $meta_old_filepath ➡️ $meta_new_filepath" );
						return false;
					}

					// Success, call other plugins
					$this->log( "✅ File $meta_old_filepath ➡️ $meta_new_filepath" );
					do_action( 'mfrh_path_renamed', $post, $meta_old_filepath, $meta_new_filepath );

				}
			}
		}
		else {
			$orig_attachment_url = wp_get_attachment_url( $id );
		}

		// Update DB: Media and Metadata
		$new_filepath = str_replace( $upload_dir['basedir'] . '/', '', $new_filepath );

		update_attached_file( $id, $new_filepath );
		if ( $meta ) {
			wp_update_attachment_metadata( $id, $meta );
		}
		clean_post_cache( $id ); // TODO: Would be good to know what this WP function actually does (might be useless)

		// Post actions
		$this->call_post_actions( $id, $post, $meta, $has_thumbnails, $orig_image_urls, $orig_attachment_url );
		do_action( 'mfrh_media_renamed', $post, $old_filepath, $new_filepath, false, 'move' );
		return true;
	}

	function get_human_readable_language() {
		$locale = get_locale();
		if ( class_exists( 'Locale' ) ) {
			return Locale::getDisplayName( $locale, $locale );
		}
		// Sorry if your language is not listed here. It's not because I do not love you!
		$languages = array(
			'en_US' => 'English (United States)',
			'fr_FR' => 'Français (France)',
			'de_DE' => 'Deutsch (Deutschland)',
			'es_ES' => 'Español (España)',
			'it_IT' => 'Italiano (Italia)',
			'pt_PT' => 'Português (Portugal)',
			'pt_BR' => 'Português (Brasil)',
			'ja_JP' => '日本語 (日本)',
			'zh_CN' => '中文 (中国)',
			'zh_TW' => '中文 (台灣)',
			'ko_KR' => '한국어 (대한민국)',
			'ru_RU' => 'Русский (Россия)',
			'vi_VN' => 'Tiếng Việt (Việt Nam)',
			'tr_TR' => 'Türkçe (Türkiye)'
		);
		return isset( $languages[$locale] ) ? $languages[$locale] : 'English (United States)';
	}

	/**
	 * Generates a new suggestion for media metadata based on type.
	 * 
	 * @param int    $mediaId      ID of the media.
	 * @param string $metadataType Type of metadata (title, description, alt text, caption, filename).
	 * @return string New metadata suggestion.
	 */
	function ai_suggestion( $mediaId, $metadataType, $binary_path = null ) {
		$is_binary = !is_null( $binary_path );

		// Prepare metadata from the entry.
		$metadata = [];
		$entry = null;

		if ( !$is_binary ) {
			$entry = $this->get_media_status_one( $mediaId );
			$metadata = [
				'title'       => $entry->post_title,
				'alt'         => $entry->image_alt,
				'description' => $entry->image_description,
				'caption'     => $entry->image_caption,
				'filename'    => $entry->current_filename,
			];
		}

		// Adjust metadata type for the prompt.
		$locale = get_locale();
		$readableType = $metadataType === 'alt' ? 'alternative text' : $metadataType;
		$promptType = $metadataType === 'filename' ? 
			"$readableType. Should be ASCII-friendly, lowercase, respecting filename standards, words separated by hyphens, but do not use the language (or locale) in the filename" :
			"$readableType. Should be human-readable with spaces and punctuation";

		// Start constructing the prompt.
		$prompt = "Suggest a new $promptType in " . $this->get_human_readable_language() . ".";

		if ( !$is_binary ) {
			$prompt .= "\n\nAdditional information to craft this $readableType:\n";
			foreach ( $metadata as $type => $value ) {
				if ( !empty ( $value ) ) {
					$prompt .= "* Current " . ucfirst ( $type ) . ": $value.\n";
				}
			}
			$prompt .= "The values above might also be modified soon, so it's only for reference.\n\n";
		}

		// Define max lengths for each metadata type.
		$lengths = [
			'alternative text' => '16 words',
			'title' => '64 characters',
			'description' => '256 characters',
			'filename' => '64 characters',
			'caption' => '155 characters',
		];

		// Append specifications for the new metadata.
		$length = $lengths[$readableType];
		$prompt .= "This new $readableType must be shorter than $length, SEO-optimized, humanly-readable. Only return the $readableType.";
		// If it's a description, mention that it should describe the image, of what's happening on it in one paragraph.
		if ( $metadataType === 'description' ) {
			$prompt .= " " . $this->get_option( 'manual_prompt_description', 'It should be an actual description the image, what\'s happening on it, in one paragraph. It is not adressing the user, but describing the image.' );
		}
		else if ( $metadataType === 'caption' ) {
			$prompt .= " " . $this->get_option( 'manual_prompt_caption', 'It should be a very short description of the image, what\'s happening on it, in one sentence which typically starts with a uppercase letter and ends with a period.' );
		}
		else if ( $metadataType === 'alternative text' ) {
			$prompt .= " " . $this->get_option( 'manual_prompt_alt', 'The ALT is used mainly for SEO. It should be stuffed with keywords, but also describe the image. It should be a short sentence, typically starting with a uppercase letter and ending with a period.' );
		}
		else if ( $metadataType === 'filename' ) {
			$prompt .= " " . $this->get_option( 'manual_prompt_filename', 'Based on the rules we set before, actually generate 5 different filenames. They should be all quite different, from the less creative to the most creative. Separate them by a comma (,) and do not end with a period. Do not forget the extension which is used in Current Filename.' );
		}
		else if ( $metadataType === 'title' ) {
			$prompt .= " " . $this->get_option( 'manual_prompt_title', 'It should be a concise, catchy title for the image that summarizes its main subject or theme. The title should be capitalized like a headline, typically 5-10 words long, and not end with a period. It should be engaging and optimized for both users and SEO.' );
		}

		// Give the user a chance to modify the prompt.
		$prompt = apply_filters( 'mfrh_ai_prompt', $prompt, $metadataType, $entry );

		// Get new metadata, first trying a filter, then defaulting to an AI query.
		$newMetadata = apply_filters( 'mfrh_vision_suggestion', null, $mediaId, $binary_path, $prompt );
		if ( empty ( $newMetadata ) ) {
			global $mwai;
			if ( is_null( $mwai ) ) {
				$this->log( '🚫 AI Engine is missing for a text query.' );
				return '';
			}

			$newMetadata = $mwai->simpleTextQuery( $prompt, [ 'max_tokens' => 512, 'scope' => 'renamer' ] );
		}

		// Clean up the new metadata.
		$newMetadata = trim( $newMetadata );
		$newMetadata = str_replace( ['"', "'"], '', $newMetadata );

		// If it's a filename, additional cleaning and checks are needed.
		if ( $metadataType === 'filename' ) {
			$newMetadata = strtolower( $newMetadata );
			if ( substr( $newMetadata, -1 ) === '.' ) {
				$newMetadata = substr( $newMetadata, 0, -1 );
			}
			if ( strpos( $newMetadata, ',' ) !== false ) {
				$newMetadata = explode( ',', $newMetadata );
				$newMetadata = array_map( 'trim', $newMetadata );
			}

			// Let's get the file which doesn't exist yet.
			$path = get_attached_file( $mediaId );
			$directory_path = dirname( $path );
			foreach ( $newMetadata as $filename ) {
				$newMetadata = $filename;
				$newPath = $directory_path . '/' . $filename;
				if ( !file_exists( $newPath ) ) {
					break;
				}
			}
		}

		return $newMetadata;
	}


	/**
	 * Get the status for many Media IDs.
	 *
	 * @param integer $mediaId
	 * @return object|null
	 */
	function get_media_status_one( $mediaId ) {
		global $wpdb;
		$entry = $wpdb->get_row( 
			$wpdb->prepare( "SELECT p.ID, p.post_title, p.post_parent, p.post_content AS image_description,
				p.post_excerpt AS image_caption,
				MAX(CASE WHEN pm.meta_key = '_wp_attached_file' THEN pm.meta_value END) AS current_filename,
				MAX(CASE WHEN pm.meta_key = '_original_filename' THEN pm.meta_value END) AS original_filename,
				MAX(CASE WHEN pm.meta_key = '_wp_attachment_metadata' THEN pm.meta_value END) AS metadata,
				MAX(CASE WHEN pm.meta_key = '_wp_attachment_image_alt' THEN pm.meta_value END) AS image_alt,
				MAX(CASE WHEN pm.meta_key = '_require_file_renaming' THEN pm.meta_value END) AS pending,
				MAX(CASE WHEN pm.meta_key = '_manual_file_renaming' THEN pm.meta_value END) AS locked
				FROM $wpdb->posts p
				INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
				WHERE p.ID = %d
					AND post_type='attachment'
					AND (pm.meta_key = '_wp_attached_file' 
						OR pm.meta_key = '_original_filename'
						OR pm.meta_key = '_wp_attachment_metadata'
						OR pm.meta_key = '_wp_attachment_image_alt'
						OR pm.meta_key = '_require_file_renaming'
						OR pm.meta_key = '_manual_file_renaming'
					)
				GROUP BY p.ID", $mediaId 
			)
		);
		if ( empty( $entry ) ) {
			error_log( "Media File Renamer: Could not find the status for the Media ID: $mediaId." );
			return null;
		}
		$this->consolidate_media_status( $entry );
		return $entry;
	}

	/**
	 * Organize the data of the entry.
	 * It is used by get_media_status and get_media_status_one.
	 *
	 * @param [type] $entry
	 * @return void
	 */
	function consolidate_media_status( &$entry ) {
		// $metadata = unserialize( $entry->metadata );
		$entry->issues = [];
		$entry->ID = (int)$entry->ID;
		$entry->post_parent = !empty( $entry->post_parent ) ? (int)$entry->post_parent : null;
		$entry->post_parent_title = !empty( $entry->post_parent ) ? get_the_title( $entry->post_parent ) : null;
		$entry->thumbnail_url = wp_get_attachment_thumb_url( $entry->ID );
		$entry->url = wp_get_attachment_url( $entry->ID );

		// After September 14th 2023:
		$path = get_post_meta( $entry->ID, '_wp_attached_file', true );
		if ( !empty( $path ) ) {
			if ( substr( $path, 0, 1 ) !== '/' ) {
				$path = '/' . $path;
			}
			$path = substr( $path, 0, strrpos( $path, '/' ) );
			$entry->path = empty( $path ) ? '/' : $path;
		}
		else {
			$entry->issues[] = 'missing_file';
		}

		// Before September 14th 2023:
		// if ( !empty( $metadata ) || !isset( $metadata['file'] ) ) {
		// 	$entry->metadata = $metadata;
		// 	$mediaFolder = get_attached_file( $entry->ID );
		// 	$entry->path = '/' . pathinfo( $metadata['file'], PATHINFO_DIRNAME );
		// }
		// else {
		// 	// For some reason, the metadata is sometimes missing from Media entries.
		// 	// This will try to recover the URL from the attachment URL.
		// 	$url = wp_get_attachment_url( $entry->ID );
		// 	$url = substr( $url, strpos( $url, '/uploads/' ) + 9 );
		// 	if ( !empty( $url ) ) {
		// 		$entry->path = '/' . pathinfo( $url, PATHINFO_DIRNAME );
		// 	}
		// 	else {
		// 		$entry->issues[] = 'missing_metadata';
		// 	}
		// }

		$entry->current_filename = pathinfo( $entry->current_filename, PATHINFO_BASENAME );
		$entry->locked = $entry->locked === '1';
		$entry->pending = $entry->pending === '1';
		$entry->proposed_filename = null;
		$lock_enabled = $this->get_option( 'lock' );
		$force_rename = $this->get_option( 'force_rename' );
		if ( !$lock_enabled || !$entry->locked ) {
			$output = [];
			// TODO: We should optimize this check_attachment function one day.
			$this->check_attachment( get_post( $entry->ID, ARRAY_A ), $output, null, $force_rename );
			if ( isset( $output['ideal_filename'] ) ) {
				$entry->ideal_filename = $output['ideal_filename'];
			}
			if ( isset( $output['proposed_filename'] ) ) {
				$entry->proposed_filename = $output['proposed_filename'];
				$entry->proposed_filename_exists = $output['proposed_filename_exists'];
			}

			if( isset( $output['used_method'] ) ) {
				$entry->used_method = $output['used_method'];
			}

			if( isset( $output['skipped_methods'] ) ) {
				$entry->skipped_methods = $output['skipped_methods'];
			}
			//error_log( print_r( $output, 1 ) );
		}
	}

	// Call the actions so that the plugin's plugins can update everything else (than the files)
	// Called by rename() and move()
	function call_post_actions( $id, $post, $meta, $has_thumbnails, $orig_image_urls, $orig_attachment_url ) {
		if ( $has_thumbnails ) {
			$orig_image_url = $orig_image_urls['full'];
			$new_image_data = wp_get_attachment_image_src( $id, 'full' );
			$new_image_url = $new_image_data[0];
			$this->call_hooks_rename_url( $post, $orig_image_url, $new_image_url, 'full' );
			if ( !empty( $meta['sizes'] ) ) {
				foreach ( $meta['sizes'] as $size => $meta_size ) {
					if ( isset( $orig_image_urls[$size] ) ) {
						$orig_image_url = $orig_image_urls[$size];
						$new_image_data = wp_get_attachment_image_src( $id, $size );
						$new_image_url = $new_image_data[0];
						$this->call_hooks_rename_url( $post, $orig_image_url, $new_image_url, $size );
					}
				}
			}
		}
		else {
			$new_attachment_url = wp_get_attachment_url( $id );
			$this->call_hooks_rename_url( $post, $orig_attachment_url, $new_attachment_url, 'full' );
		}
		// HTTP REFERER set to the new media link
		if ( isset( $_REQUEST['_wp_original_http_referer'] ) &&
			strpos( $_REQUEST['_wp_original_http_referer'], '/wp-admin/' ) === false ) {
			$_REQUEST['_wp_original_http_referer'] = get_permalink( $id );
		}
	}

	/**
	 * The params should be an array with the following keys:
	 * - Method (string): 'manual', 'vision', 'ai', 'auto', 'undo'
	 * - Metadata (string): 'title', 'alt', 'description', 'filename'
	 * - Original (string): The original value
	 * - New (string): The new value
	 * - Date (date): The date of the change
	 * 
	 * @param int $media_id
	 * @param array $params
	 * @return void
	 * 
	 */
	function add_to_media_history( $media_id, $params ) {
		$history = get_post_meta( $media_id, '_mfrh_history', true );
		if ( !is_array( $history ) ) {
			$history = array();
		}
	
		$metadata = $params['metadata']; 
		if (!isset($history[$metadata])) {
			$history[$metadata] = array();
		}
	
		$history[$metadata][] = $params;
	
		$history_limit = $this->get_option( 'history_limit', 4);
		if ( count( $history[$metadata] ) > $history_limit ) {
			array_shift( $history[$metadata] );
		}
		
		update_post_meta( $media_id, '_mfrh_history', $history );
	}

	function update_media( $id, $postTitle, $imageAlt, $imageDescription, $imageCaption, $method, $sync = false) {
		$errors = [];
	
		if ( !$id || ( !$postTitle && !$imageAlt && !$imageDescription && !$imageCaption ) ) {
			$errors[] = __( 'The update title or alt parameters are missing.', 'media-file-renamer' );
		}
	
		if ( $postTitle ) {
			$previousPostTitle = get_post_field( 'post_title', $id );
			if ( $previousPostTitle !== $postTitle ) {
				$result = wp_update_post( [ 'ID' => $id, 'post_title' => $postTitle ], true );
				if ( is_wp_error( $result ) ) {
					$errors = array_merge($errors, $result->get_error_messages());
				}
	
				$this->add_to_media_history( $id, [
					'method' => $method,
					'metadata' => 'title',
					'original' => $previousPostTitle,
					'new' => $postTitle,
					'sync' => $sync,
					'date' => date( 'Y-m-d H:i:s' ),
				] );
			}
		}
	
		if ( $imageAlt ) {
			$previousImageAlt = get_post_meta( $id, '_wp_attachment_image_alt', true );
			if ( $previousImageAlt !== $imageAlt ) {
				$result = update_post_meta( $id, '_wp_attachment_image_alt', $imageAlt );
				if ( !$result ) {
					$errors[] = __( 'The image alt could not be updated.', 'media-file-renamer' );
				}
	
				$this->add_to_media_history( $id, [
					'method' => $method,
					'metadata' => 'alt',
					'original' => $previousImageAlt,
					'new' => $imageAlt,
					'sync' => $sync,
					'date' => date( 'Y-m-d H:i:s' ),
				] );
			}
		}
	
		if ( $imageDescription ) {
			$previousImageDescription = get_post_field( 'post_content', $id );
			if ( $previousImageDescription !== $imageDescription ) {
				$result = wp_update_post( [ 'ID' => $id, 'post_content' => $imageDescription ], true );
				if ( is_wp_error( $result ) ) {
					$errors = array_merge($errors, $result->get_error_messages());
				}
	
				$this->add_to_media_history( $id, [
					'method' => $method,
					'metadata' => 'description',
					'original' => $previousImageDescription,
					'new' => $imageDescription,
					'sync' => $sync,
					'date' => date( 'Y-m-d H:i:s' ),
				] );
			}
		}

		if ( $imageCaption ) {
			$previousImageCaption = get_post_field( 'post_excerpt', $id );
			if ( $previousImageCaption !== $imageCaption ) {
				$result = wp_update_post( [ 'ID' => $id, 'post_excerpt' => $imageCaption ], true );
				if ( is_wp_error( $result ) ) {
					$errors = array_merge($errors, $result->get_error_messages());
				}
	
				$this->add_to_media_history( $id, [
					'method' => $method,
					'metadata' => 'caption',
					'original' => $previousImageCaption,
					'new' => $imageCaption,
					'sync' => $sync,
					'date' => date( 'Y-m-d H:i:s' ),
				] );
			}
		}
	
		return ['errors' => $errors];
	}

	function undo( $mediaId ) {
		$original_filename = get_post_meta( $mediaId, '_original_filename', true );
		if ( empty( $original_filename ) ) {
			return true;
		}

		

		$res = $this->engine->rename( $mediaId, $original_filename, true, 'undo' );
		if ( !!$res ) {
			delete_post_meta( $mediaId, '_original_filename' );
		}
		return $res;
	}

	/**
	 * Linking with api.php call (l.20)
	 */
	function rename( $mediaId, $manual ){
		$res = $this->engine->rename( $mediaId, $manual );
		return $res;
	}

	/**
	 * Locks a post to be manual-rename only
	 * @param int|WP_Post $post The post to lock
	 * @return True on success, false on failure
	 */
	function lock( $post ) {
		//TODO: We should probably only take an ID as the argument
		$id = $post instanceof WP_Post ? $post->ID : $post;
		delete_post_meta( $id, '_require_file_renaming' );
		update_post_meta( $id, '_manual_file_renaming', true, true );
		return true;
	}

	/**
	 * Unlocks a locked post
	 * @param int|WP_Post $post The post to unlock
	 * @return True on success, false on failure
	 */
	function unlock( $post ) {
		delete_post_meta( $post instanceof WP_Post ? $post->ID : $post, '_manual_file_renaming' );
		return true;
	}

	/**
	 * Determines whether a post is locked
	 * @param int|WP_Post $post The post to check
	 * @return Boolean
	 */
	function is_locked( $post ) {
		return get_post_meta( $post instanceof WP_Post ? $post->ID : $post, '_manual_file_renaming', true ) === true;
	}

	/**
	 *
	 * Roles & Access Rights
	 *
	 */

	public function can_access_settings() {
		return apply_filters( 'mfrh_allow_setup', current_user_can( 'manage_options' ) );
	}

	public function can_access_features() {
		return apply_filters( 'mfrh_allow_usage', current_user_can( 'administrator' ) );
	}

	#region Options
	function reset_options() {
		delete_option( $this->option_name );
	}

	function reset_metadata() {
		global $wpdb;
		// Delete the specific meta keys to reset the status
		$count = $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key IN ('_require_file_renaming', '_manual_file_renaming', '_original_filename')" );
		return $count;
	}

	function get_option( $option, $default = null ) {
		$options = $this->get_all_options();
		return $options[$option] ?? $default;
	}

	function list_options() {
		$options = get_option( $this->option_name, null );
		foreach ( MFRH_OPTIONS as $key => $value ) {
			if ( !isset( $options[$key] ) ) {
				$options[$key] = $value;
			}
		}

		$options['parsers'] = [
			'elementor' => [
				'display' => 'Elementor',
				'exists' => defined( 'ELEMENTOR_VERSION' ),
				'enabled' => true,
			],
			'oxygen' => [
				'display' => 'Oxygen Builder',
				'exists' => class_exists( 'CT_Component' ),
				'enabled' => true,
			],
			'beaver_builder' => [
				'display' => 'Beaver Builder',
				'exists' => class_exists( 'FLBuilderModel' ),
				'enabled' => true,
			],
			'wpml' => [
				'display' => 'WPML',
				'exists' => function_exists( 'icl_object_id' ),
				'enabled' => true,
			]
		];

		return $options;
	}

	function needs_registered_options() {
		return array(
			'convert_to_ascii',
			'numbered_files',
			'sync_alt',
			'sync_media_title',
			'force_rename',
			'logsql',
		);
	}

	function get_all_options() {
		$options = $this->list_options();
		$options = $this->check_options( $options );

		$needs_registered_options = $this->needs_registered_options();
		foreach ( $options as $key => $value ) {
			if ( in_array( $key, $needs_registered_options ) ) {
				//$options[ $key ] = $this->admin->is_registered() && $value;
				$options[$key] = isset( $this->admin ) && $this->admin->is_registered() && $value;
				continue;
			}
		}

		return $options;
	}

	function toggle_parser( $parser ) {
		$options = $this->get_all_options();

		if ( key_exists( $parser, $options['parsers'] ) ) {
			$options['parsers'][$parser]['enabled'] = !$options['parsers'][$parser]['enabled'];
		}
		
		
		return $this->update_options( $options );
	}


	function update_options( $options ) {
		if ( !update_option( $this->option_name, $options, false ) ) {
			return false;
		}
		list($options, $result, $message) = $this->sanitize_options();
		$validation_result = $this->createValidationResult( $result, $message );
		return [ $options, $validation_result['result'], $validation_result['message'] ];
	}

	// Upgrade from the old way of storing options to the new way.
	function check_options( $options = [] ) {
		$plugin_options = $this->list_options();
		$options = empty( $options ) ? [] : $options;
		$hasChanges = false;
		foreach ( $plugin_options as $option => $default ) {
			// The option already exists
			if ( isset( $options[$option] ) ) {
				continue;
			}
			// The option does not exist, so we need to add it.
			// Let's use the old value if any, or the default value.
			$options[$option] = get_option( 'mfrh_' . $option, $default );
			delete_option( 'mfrh_' . $option );
			$hasChanges = true;
		}
		if ( $hasChanges ) {
			update_option( $this->option_name , $options );
		}



		return $options;
	}

	function get_mime_type( $file ) {
        $mimeType = null;

        // Let's try to use mime_content_type if the function exists
        if ( function_exists( 'mime_content_type' ) ) {
            $mimeType = mime_content_type( $file );
        }

        // Otherwise, let's check the file extension (which can actually also be an URL)
        if ( !$mimeType ) {
            $extension = pathinfo( $file, PATHINFO_EXTENSION );
            $extension = strtolower( $extension );
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'bmp' => 'image/bmp',
                'tiff' => 'image/tiff',
                'tif' => 'image/tiff',
                'svg' => 'image/svg+xml',
                'ico' => 'image/x-icon',
                'pdf' => 'application/pdf',
            ];
            $mimeType = isset( $mimeTypes[$extension] ) ? $mimeTypes[$extension] : null;
        }

        return $mimeType;
    }

	// Validate and keep the options clean and logical.
	function sanitize_options() {
		$options = $this->get_all_options();
		$result = true;
		$message = null;

		$needs_update = false;

		$force_rename = $options['force_rename'];
		$numbered_files = $options['numbered_files'];

		if ( $force_rename && $numbered_files ) {
			$options['force_rename'] = false;
			$result = false;
			$message = __( 'Force Rename and Numbered Files cannot be used at the same time. Please use Force Rename only when you are trying to repair a broken install. For now, Force Rename has been disabled.', 'media-file-renamer' );
		}

		$lock = $options['lock'];
		$has_lock_options = $options['autolock_auto'] || $options['autolock_manual'];
		if ( !$lock && $has_lock_options ) {
			$options['autolock_auto'] = false;
			$options['autolock_manual'] = false;
			$needs_update = true;
		}
		
		if ( !$options['move'] && $options['mode'] === 'move' ) {
			$options['mode'] = 'rename';
			$needs_update = true;
		}

		$isVisionEnabled = $options['vision_rename_ai'] && $options['manual_rename_ai'];
		if ( !$isVisionEnabled ) {

			$visionRename = $options['auto_rename'] === 'vision' || $options['auto_rename_secondary'] === 'vision' || $options['auto_rename_tertiary'] === 'vision';

			if ( $visionRename ) {
				$options['auto_rename']           = $options['auto_rename']           === 'vision' ? 'none' : $options['auto_rename'];
				$options['auto_rename_secondary'] = $options['auto_rename_secondary'] === 'vision' ? 'none' : $options['auto_rename_secondary'];
				$options['auto_rename_tertiary']  = $options['auto_rename_tertiary']  === 'vision' ? 'none' : $options['auto_rename_tertiary'];
			
				$needs_update = true;
			}

			$visionUpload = $options['on_upload_method'] === 'upload_vision' || $options['on_upload_method_secondary'] === 'upload_vision' || $options['on_upload_method_tertiary'] === 'upload_vision';

			if ( $visionUpload ) {
				$options['on_upload_method']           = $options['on_upload_method']           === 'upload_vision' ? 'none' : $options['on_upload_method'];
				$options['on_upload_method_secondary'] = $options['on_upload_method_secondary'] === 'upload_vision' ? 'none' : $options['on_upload_method_secondary'];
				$options['on_upload_method_tertiary']  = $options['on_upload_method_tertiary']  === 'upload_vision' ? 'none' : $options['on_upload_method_tertiary'];
			
				$needs_update = true;
			}
			
		}

		
		if ( $options['manual_prompt'] ) {
			$prompt_types = ['title', 'alt', 'description', 'caption', 'filename'];
			
			foreach ( $prompt_types as $type ) {
				$option_key = 'manual_prompt_' . $type;
				if ( empty( $options[$option_key] ) ) {
					$options[$option_key] = MFRH_OPTIONS[$option_key];
					$needs_update = true;
				}
			}
		} else {
			
			foreach ( ['title', 'alt', 'description', 'caption', 'filename'] as $type ) {
				$option_key = 'manual_prompt_' . $type;
				if ( $options[$option_key] !== MFRH_OPTIONS[$option_key] ) {
					$options[$option_key] = MFRH_OPTIONS[$option_key];
					$needs_update = true;
				}
			}
		}


		if ( !$result || $needs_update ) {
			update_option( $this->option_name, $options, false );
		}

		return [ $options, $result, $message ];
	}

	function createValidationResult( $result = true, $message = null) {
		$message = $message ? $message : __( 'Option updated.', 'media-file-renamer' );
		return [ 'result' => $result, 'message' => $message ];
	}

	#endregion

	private function random_ascii_chars( $length = 8 ) {
		$characters = array_merge( range( 'A', 'Z' ), range( 'a', 'z' ), range( '0', '9' ) );
		$characters_length = count( $characters );
		$random_string = '';

		for ($i = 0; $i < $length; $i++) {
			$random_string .= $characters[rand(0, $characters_length - 1)];
		}

		return $random_string;
	}
}
