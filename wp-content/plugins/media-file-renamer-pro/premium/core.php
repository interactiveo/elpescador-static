<?php

class MeowPro_MFRH_Core {
	private $item = 'Media File Renamer Pro';
	private $admin = null;
	private $core = null;
	private $engine = null;
	private $onceCacheKey = null;
	private $parsers = null;

	public function __construct( $core, $admin, $engine ) {
		$this->core = $core;
		$this->admin = $admin;
		$this->engine = $engine;
		$this->onceCacheKey = md5( time() );

		// Common behaviors, license, update system, etc.
		new MeowCommonPro_Licenser( MFRH_PREFIX, MFRH_ENTRY, MFRH_DOMAIN, $this->item, MFRH_VERSION );

		// Support for WP CLI
		if ( $core->is_cli ) {
			$wpcli = new MeowPro_MFRH_CLI( $this, $admin, $this->core );
			WP_CLI::add_command( 'media-file-renamer', $wpcli );
			WP_CLI::add_command( 'mfrh', $wpcli );
		}

		// Filters and actions
		if ( $this->admin->is_registered() ) {

			add_filter( 'mfrh_numbered', array( $this, 'numbered' ), 10, 1 );
			add_filter( 'mfrh_method', array( $this, 'method' ), 10, 2 );
			add_filter( 'mfrh_new_filename', array( $this, 'transliterate' ), 10, 3 );
			add_filter( 'mfrh_new_filename', array( $this, 'additional_filter' ), 10, 3 );
			add_filter( 'mfrh_force_rename', array( $this, 'force_rename' ), 10, 1 );
			add_filter( 'mfrh_base_for_rename', array( $this, 'base_for_rename' ), 10, 4 );
			add_filter( 'mfrh_vision_suggestion', array( $this, 'vision_suggestion' ), 10, 4 );

			// Redirection support disabled for now (let's see how it can be used exactly)
			//add_filter( 'mfrh_path_renamed', array( $this, 'add_redirection'), 10, 3);

			// Sync the metadata (Media ALT and Meta Title).
			add_action( 'mfrh_media_renamed', array( $this, 'sync_media_meta' ), 10, 5 );
			add_action( 'mfrh_media_resync', array( $this, 're_sync_media_meta' ), 10, 3 );

			add_filter( 'mfrh_after_upload', array( $this, 'after_upload' ), 10, 2 );

			// Parsers for builders and other plugins
			add_action( 'mfrh_initialize_parsers', array( $this, 'initialize_parsers' ) );
		}
	}

	function initialize_parsers() {
		if ( $this->parsers ) { return; }
		$this->parsers = new MeowPro_MFRH_Parsers( $this->core );
	}

	function after_upload( $metadata, $mediaId ) {
		if ( $this->core->method === 'anonymize_md5' ) {
			$this->engine->rename( $mediaId ); 
			$meta = wp_get_attachment_metadata( $mediaId );
			return $meta;
		}
		return $metadata;
	}

	private $cached_posts = array();

	function get_post_from_media( $id ) {
		if ( isset( $this->cached_posts[$id] ) ) {
			return $this->cached_posts[$id];
		}
		global $wpdb;
		$postid = $wpdb->get_var( $wpdb->prepare( "
			SELECT post_parent p
			FROM $wpdb->posts p
			WHERE ID = %d", $id ),
			0, 0 );
		if ( empty( $postid ) ) {
			return null;
		}
		$post = get_post( $postid, ARRAY_A );
		if ( !isset( $this->cached_posts[$postid] ) ) {
			$this->cached_posts[$postid] = $post;
		}
		return $post;
	}

	function base_for_rename( $base_for_rename, $id, $preview = true, $skipped_methods = [] ) {
		$methods = [
			$this->core->method,
			$this->core->method_secondary,
			$this->core->method_tertiary,
		];
	
		foreach ( $methods as $method ) {
			if ( in_array( $method, $skipped_methods ) ) {
				$this->core->log( "✒️ Method " . $method . " was skipped." );
				continue;
			}
	
			switch ($method) {
				case 'none':
					$base_for_rename = null;
					break;
				case 'vision':
					$base_for_rename = $preview ? '{VISION}' : $this->core->ai_suggestion( $id, 'filename' );
					break;
				case 'post_title':
					$attachedpost = $this->get_post_from_media( $id );
					$base_for_rename =  !is_null( $attachedpost )? $attachedpost['post_title'] : null;
					break;
				case 'media_title':
					$base_for_rename = get_the_title( $id );
					break;
				case 'alt_text':
					$image_alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
					$base_for_rename = $image_alt;
					break;
				case 'anonymize_md5':
					$base_for_rename = md5( $id );
					break;
				case 'post_acf_field':
					if ( !function_exists( 'get_field' ) ) {
						break;
					}
					$attachedpost = $this->get_post_from_media( $id );
					if ( is_null( $attachedpost ) ) {
						break;
					}
					$post_id = $attachedpost['ID'];
					$acf_field_name = $this->core->get_option( 'acf_field_name', false );
					if ( $acf_field_name ) {
						$base_for_rename = get_field( $acf_field_name, $post_id );
					}
					break;
				default:
					$this->core->log( "⚠️ Method " . $method . " not found." );
					break;
			}
	
			if ( !is_null( $base_for_rename ) ) {
				$this->core->last_used_method = $method;
				break;
			} else if ( !$preview ) {
				$this->core->log( "✒️ Method " . $method . " returned null. Trying next method." );
			}
		}
	
		return !is_null($base_for_rename) ? html_entity_decode($base_for_rename) : '';
	}

	function method( $default, $suffix = '' ) {
		$value = $this->core->get_option( 'auto_rename' . $suffix, $default );
		if ( $this->admin->is_registered() ) {
			return $value;
		}
		return $value === 'none' ? 'none' : $default;
	}

	public function re_sync_media_meta( $post, $useVision = false, $timeFilter = 0 ) {
		$filename = get_post_meta( $post['ID'], '_wp_attached_file', true );
	
		// Get the post _mfrh_history meta
		$history = get_post_meta( $post['ID'], '_mfrh_history', true );
	
		// Map the "metadata_..." keys to the corresponding $history indices
		$metadataMap = [
			'metadata_title'       => 'title',
			'metadata_description' => 'description',
			'metadata_alt'         => 'alt',
			'metadata_caption'     => 'caption',
		];
	
		// Full set of sync options we might use
		$allSyncOptions = [
			'metadata_title'       => 'action_sync_media_title',
			'metadata_description' => 'action_sync_description',
			'metadata_alt'         => 'action_sync_alt',
			'metadata_caption'     => 'action_sync_caption',
		];
	
		// We will remove items from $allSyncOptions if they don't meet the criteria
		if ( ! empty( $history ) && is_array( $history ) && $timeFilter > 0 ) {
			foreach ( $metadataMap as $metaKey => $historyKey ) {
				// If there's no history for this metadata, or it's not an array, we should sync it
				if ( empty( $history[ $historyKey ] ) || ! is_array( $history[ $historyKey ] ) ) {
					$this->core->log( "🔃 $metaKey RESYNC | No history found. Syncing." );
					continue;
				}
	
				// Sort entries by date descending, so index 0 is the most recent
				usort( $history[ $historyKey ], function ( $a, $b ) {
					return strtotime( $b['date'] ) - strtotime( $a['date'] );
				});

				// Grab the most recent entry
				$latestEntry = $history[ $historyKey ][0];

				// If the latest was not a sync, we should sync it
				if ( empty( $latestEntry['sync'] ) || $latestEntry['sync'] !== true ) {
					$this->core->log( "🔃 $metaKey RESYNC | Latest entry was not a sync. Syncing." );
					continue;
				}

				// Check if the date is older than $timeFilter hours
				$entryTime = strtotime( $latestEntry['date'] );
				$now       = time();
				$diffHours = ( $now - $entryTime ) / 3600;
	
				// If the most recent entry is NOT older than $timeFilter hours, remove from sync
				if ( $timeFilter > $diffHours ) {
					$this->core->log( "🔃 $metaKey RESYNC | Latest entry is not older than $timeFilter hours. Skipping." );
					unset( $allSyncOptions[ $metaKey ] );
				} else {
					$this->core->log( "🔃 $metaKey RESYNC | Latest entry is older than $timeFilter hours. Syncing." );
				}
			}
		}
	
		$this->syncOptions(
			$post,
			'updated',
			$filename,
			$filename,
			$allSyncOptions,
			$useVision
		);
	}

	public function sync_media_meta( $post, $old_filename = null, $new_filename = null, $undo = false, $method = null )   {
		if  ( is_numeric ( $post ) ) {
			$post = get_post ( $post, ARRAY_A ) ;
		}
	
		$this->syncOptions ( $post, $method, $old_filename, $new_filename, [
			'sync_' . $this->core->last_used_method . '_title' => 'action_sync_media_title',
			'sync_' . $this->core->last_used_method . '_description' => 'action_sync_description',
			'sync_' . $this->core->last_used_method . '_alt' => 'action_sync_alt',
			'sync_' . $this->core->last_used_method . '_caption' => 'action_sync_caption',
		] ) ;
	}
	
	private function syncOptions ( $post, $method, $old_filename, $new_filename, $options, $force_vision = false )  {
		foreach  ( $options as $option => $action )  {
			if  ( $this->core->get_option ( $option ) ) {

				if( str_contains( $option, 'vision') ) { $method = 'vision'; }
				if( $force_vision ) { $method = 'vision'; }
				
				$this->core->log("🔃 $option SYNC | Method: $method");

				$this->$action ( $post, $method, $old_filename, $new_filename ) ;
			}
		}
	}
	
	private function action_sync_common ( $post, $method, $old_filename, $new_filename, $type ) {

		$new_value = '';
		$filter_suffix = $type;

		$new_filename = pathinfo ( $new_filename, PATHINFO_FILENAME ) ;

	
		if  ( $method == 'auto' )  {
			$current_method =  $this->core->last_used_method;
			if  ( $current_method == 'alt_text' )  {
				$new_value = get_post_meta ( $post['ID'], '_wp_attachment_image_alt', true ) ;
			} elseif  ( $current_method == 'post_title' )  {
				$attachedpost = $this->get_post_from_media ( $post['ID'] ) ;
				if  ( $attachedpost )  {
					$new_value = $attachedpost['post_title'];
				}
			} else {
				$attached_file = get_post_meta ( $post['ID'], '_wp_attached_file', true ) ;
				if  ( $attached_file )  {
					$filename = pathinfo ( $attached_file, PATHINFO_FILENAME ) ;
					$filename = preg_replace ( '%\s*[-_\s]+\s*%', ' ', $filename ) ;
					$filename = ucwords ( strtolower ( $filename ) );
					$new_value = $filename;
				}
			}
		} elseif  ( $method == 'manual' || $method == 'updated' )  {
			$new_value = preg_replace ( '%\s*[-_\s]+\s*%', ' ', $new_filename ) ;
			$new_value = ucwords ( strtolower ( $new_value ) );
		} elseif  ( $method == 'vision' )  {
			$new_value = $this->core->ai_suggestion ( $post['ID'], $type ) ;
		}
	
		if  ( $new_value )  {
			$new_value = apply_filters ( "mfrh_rewrite_$filter_suffix", $new_value, $post ) ;
			$postTitle = null; $imageAlt = null; $imageDescription = null; $imageCaption = null;
			
			switch ( $type ) {
				case 'title':
					$postTitle = $new_value;
					break;
				case 'alt':
					$imageAlt = $new_value;
					break;
				case 'description':
					$imageDescription = $new_value;
					break;
				case 'caption':
					$imageCaption = $new_value;
					break;
				default:
					break;
			}
			$this->core->update_media($post['ID'], $postTitle, $imageAlt, $imageDescription, $imageCaption, $method, true);
			$this->core->log ( "🔃 $type SYNC | New $type : $new_value" ) ;
		} elseif  ( $method == 'auto' && !$new_value )  {
			$this->core->log ( "🔃 $type SYNC | ⚠️ No value found. Could not sync $type." ) ;
		}
	}
	
	function action_sync_alt ( $post, $method, $old_filename, $new_filename )  {
		$this->action_sync_common ( $post, $method, $old_filename, $new_filename, 'alt' ) ;
	}
	
	function action_sync_media_title ( $post, $method, $old_filename, $new_filename )  {
		$this->action_sync_common ( $post, $method, $old_filename, $new_filename, 'title' ) ;
	}
	
	function action_sync_description ( $post, $method, $old_filename, $new_filename )  {
		$this->action_sync_common ( $post, $method, $old_filename, $new_filename, 'description' ) ;
	}

	function action_sync_caption ( $post, $method, $old_filename, $new_filename )  {
		$this->action_sync_common ( $post, $method, $old_filename, $new_filename, 'caption' ) ;
	}

	/**
	 * Returns a decoded unicode character from its code point
	 * @param int $code_point
	 * @return string
	 */
	function u( $code_point ) {
		$u = str_pad( dechex( $code_point ), 4, '0', STR_PAD_LEFT ); // 4 digits hexadecimal string
		return json_decode( '"\u' . $u . '"' );
	}

	/**
	 * Performs transliteration
	 * @param string $str The string to transliterate
	 * @return string
	 */
	function transliterate_to_ascii( $str ) {
		// Conversion table
		static $chars = null;

		if ( is_null($chars) ) {
			$chars = array (
				/** Cyrillics **/
				'А' => 'A',    'Б' => 'B',    'В' => 'V',    'Г' => 'G',
				'Д' => 'D',    'Е' => 'E',    'Ё' => 'E',    'Ж' => 'Zh',
				'З' => 'Z',    'И' => 'I',    'Й' => 'I',    'К' => 'K',
				'Л' => 'L',    'М' => 'M',    'Н' => 'N',    'О' => 'O',
				'П' => 'P',    'Р' => 'R',    'С' => 'S',    'Т' => 'T',
				'У' => 'U',    'Ф' => 'F',    'Х' => 'Kh',   'Ц' => 'Ts',
				'Ч' => 'Ch',   'Ш' => 'Sh',   'Щ' => 'Shch', 'Ъ' => 'Ie',
				'Ы' => 'Y',    'Ь' => '',     'Э' => 'E',    'Ю' => 'Iu',
				'Я' => 'Ia',
				'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',
				'д' => 'd',    'е' => 'e',    'ё' => 'e',    'ж' => 'zh',
				'з' => 'z',    'и' => 'i',    'й' => 'i',    'к' => 'k',
				'л' => 'l',    'м' => 'm',    'н' => 'n',    'о' => 'o',
				'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
				'у' => 'u',    'ф' => 'f',    'х' => 'kh',   'ц' => 'ts',
				'ч' => 'ch',   'ш' => 'sh',   'щ' => 'shch', 'ъ' => 'ie',
				'ы' => 'y',    'ь' => '',     'э' => 'e',    'ю' => 'iu',
				'я' => 'ia',

				/** Greek letters */
				'Α' => 'A',    'Β' => 'B',    'Γ' => 'G',    'Δ' => 'D',
				'Ε' => 'E',    'Ζ' => 'Z',    'Η' => 'H',    'Θ' => 'Th',
				'Ι' => 'I',    'Κ' => 'K',    'Λ' => 'L',    'Μ' => 'M',
				'Ν' => 'N',    'Ξ' => 'X',    'Ο' => 'O',    'Π' => 'P',
				'Ρ' => 'R',    'Σ' => 'S',    'Τ' => 'T',    'Υ' => 'U',
				'Φ' => 'Ph',   'Χ' => 'Ch',   'Ψ' => 'Ps',   'Ω' => 'O',
				'α' => 'a',    'β' => 'b',    'γ' => 'g',    'δ' => 'd',
				'ε' => 'e',    'ζ' => 'z',    'η' => 'i',    'θ' => 'th',
				'ι' => 'i',    'κ' => 'k',    'λ' => 'l',    'μ' => 'm',
				'ν' => 'n',    'ξ' => 'x',    'ο' => 'o',    'π' => 'p',
				'ρ' => 'r',    'σ' => 's',    'ς' => 's',    'τ' => 't',
				'υ' => 'u',    'φ' => 'f',   'χ' => 'ch',   'ψ' => 'ps',
				'ω' => 'o',    'ά' => 'a',    'έ' => 'e',    'ή' => 'i',
				'ί' => 'i',    'ό' => 'o',    'ύ' => 'y',    'ώ' => 'o',
			);
		}
		// Preform conversion
		foreach ( $chars as $from => $to )
			$str = str_replace( $from, $to, $str );

		return $str;
	}

	/**
	 * Removes some unicode puncuation characters from a string.
	 * The conversion table derived from `sanitize_title_with_dashes()`
	 * @param string $str The string to remove from
	 * @return string
	 * @see sanitize_title_with_dashes()
	 */
	function remove_special_chars( $str ) {
		// Conversion table
		static $chars = null;

		if ( is_null( $chars ) ) {
			$chars = array (
				// iexcl and iquest
				'%c2%a1', '%c2%bf',
				// angle quotes
				'%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
				// curly quotes
				'%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d',
				'%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
				// copy, reg, deg, hellip and trade
				'%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
				// acute accents
				'%c2%b4', '%cb%8a', '%cc%81', '%cd%81',
				// grave accent, macron, caron
				'%cc%80', '%cc%84', '%cc%8c',
				// quotation marks
				chr(194), chr(171)
			);

			// error_log( print_r( $new_filename, 1 ));
			// error_log( print_r( $new_filename[20], 1 ));
			// error_log( print_r( ord( $new_filename[20] ), 1 ));

			$chars = array_map( 'urldecode', $chars );

			// Combining Diacritical Marks (U+0300 - U+036F)
			// @see http://www.fileformat.info/info/unicode/block/combining_diacritical_marks/list.htm
			for ( $i = 0x0300; $i <= 0x036f; $i++ ) $chars[] = $this->u( $i );

			// Combining Diacritical Marks Extended (U+1AB0 - U+1ABE)
			// @see http://www.fileformat.info/info/unicode/block/combining_diacritical_marks_extended/list.htm
			for ( $i = 0x1ab0; $i <= 0x1abe; $i++ ) $chars[] = $this->u( $i );

			// Combining Diacritical Marks Supplement (U+1DC0 - U+1DEF)
			// @see http://www.fileformat.info/info/unicode/block/combining_diacritical_marks_supplement/list.htm
			for ( $i = 0x1dc0; $i <= 0x1def; $i++ ) $chars[] = $this->u( $i );

			// Common Diacrical Marks
			$x = array (
				// Circumflex
				0x005e, 0x02c6,
				// Low Circumflex
				0xa788,
				// Tilde
				0x007e, 0x02dc,
				// Low Tilde
				0x02f7,
			);
			$chars = array_merge( $chars, array_map( array ( $this, 'u' ), $x ) );
		}
		// Preform conversion
		foreach ( $chars as $char )
			$str = str_replace( $char, '', $str );

		return $str;
	}

	/// https://stackoverflow.com/questions/12807176/php-writing-a-simple-removeemoji-function
	public function remove_emoji( $text ) {
    $clean_text = "";

		// Modern emojis
		$clean_text = preg_replace('/[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0077}\x{E006C}\x{E0073}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0073}\x{E0063}\x{E0074}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0065}\x{E006E}\x{E0067}\x{E007F})|[\x{1F3F4}](?:\x{200D}\x{2620}\x{FE0F})|[\x{1F3F3}](?:\x{FE0F}\x{200D}\x{1F308})|[\x{0023}\x{002A}\x{0030}\x{0031}\x{0032}\x{0033}\x{0034}\x{0035}\x{0036}\x{0037}\x{0038}\x{0039}](?:\x{FE0F}\x{20E3})|[\x{1F441}](?:\x{FE0F}\x{200D}\x{1F5E8}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F468})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F468})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F9B0})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B0})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2640}\x{FE0F})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2640}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FF}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FE}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FD}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FC}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{1F3FB}\x{200D}\x{2642}\x{FE0F})|[\x{1F46E}\x{1F9B8}\x{1F9B9}\x{1F482}\x{1F477}\x{1F473}\x{1F471}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}](?:\x{200D}\x{2642}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{1F3FF}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FE}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FD}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FC}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{1F3FB}\x{200D}\x{2695}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2695}\x{FE0F})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FF})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FE})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FD})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FC})|[\x{1F476}\x{1F9D2}\x{1F466}\x{1F467}\x{1F9D1}\x{1F468}\x{1F469}\x{1F9D3}\x{1F474}\x{1F475}\x{1F46E}\x{1F575}\x{1F482}\x{1F477}\x{1F934}\x{1F478}\x{1F473}\x{1F472}\x{1F9D5}\x{1F9D4}\x{1F471}\x{1F935}\x{1F470}\x{1F930}\x{1F931}\x{1F47C}\x{1F385}\x{1F936}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F647}\x{1F926}\x{1F937}\x{1F486}\x{1F487}\x{1F6B6}\x{1F3C3}\x{1F483}\x{1F57A}\x{1F9D6}\x{1F9D7}\x{1F9D8}\x{1F6C0}\x{1F6CC}\x{1F574}\x{1F3C7}\x{1F3C2}\x{1F3CC}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{26F9}\x{1F3CB}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93D}\x{1F93E}\x{1F939}\x{1F933}\x{1F4AA}\x{1F9B5}\x{1F9B6}\x{1F448}\x{1F449}\x{261D}\x{1F446}\x{1F595}\x{1F447}\x{270C}\x{1F91E}\x{1F596}\x{1F918}\x{1F919}\x{1F590}\x{270B}\x{1F44C}\x{1F44D}\x{1F44E}\x{270A}\x{1F44A}\x{1F91B}\x{1F91C}\x{1F91A}\x{1F44B}\x{1F91F}\x{270D}\x{1F44F}\x{1F450}\x{1F64C}\x{1F932}\x{1F64F}\x{1F485}\x{1F442}\x{1F443}](?:\x{1F3FB})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FA}](?:\x{1F1FF})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1FA}](?:\x{1F1FE})|[\x{1F1E6}\x{1F1E8}\x{1F1F2}\x{1F1F8}](?:\x{1F1FD})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F9}\x{1F1FF}](?:\x{1F1FC})|[\x{1F1E7}\x{1F1E8}\x{1F1F1}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1FB})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1FB}](?:\x{1F1FA})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FE}](?:\x{1F1F9})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FA}\x{1F1FC}](?:\x{1F1F8})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F7})|[\x{1F1E6}\x{1F1E7}\x{1F1EC}\x{1F1EE}\x{1F1F2}](?:\x{1F1F6})|[\x{1F1E8}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}](?:\x{1F1F5})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EE}\x{1F1EF}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F8}\x{1F1F9}](?:\x{1F1F4})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1F3})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FF}](?:\x{1F1F2})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F1})|[\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FD}](?:\x{1F1F0})|[\x{1F1E7}\x{1F1E9}\x{1F1EB}\x{1F1F8}\x{1F1F9}](?:\x{1F1EF})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EB}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F3}\x{1F1F8}\x{1F1FB}](?:\x{1F1EE})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1ED})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1EC})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F9}\x{1F1FC}](?:\x{1F1EB})|[\x{1F1E6}\x{1F1E7}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FB}\x{1F1FE}](?:\x{1F1EA})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1E9})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FB}](?:\x{1F1E8})|[\x{1F1E7}\x{1F1EC}\x{1F1F1}\x{1F1F8}](?:\x{1F1E7})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F6}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}\x{1F1FF}](?:\x{1F1E6})|[\x{00A9}\x{00AE}\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}-\x{21AA}\x{231A}-\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}-\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}-\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}-\x{2623}\x{2626}\x{262A}\x{262E}-\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{2660}\x{2663}\x{2665}-\x{2666}\x{2668}\x{267B}\x{267E}-\x{267F}\x{2692}-\x{2697}\x{2699}\x{269B}-\x{269C}\x{26A0}-\x{26A1}\x{26AA}-\x{26AB}\x{26B0}-\x{26B1}\x{26BD}-\x{26BE}\x{26C4}-\x{26C5}\x{26C8}\x{26CE}-\x{26CF}\x{26D1}\x{26D3}-\x{26D4}\x{26E9}-\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}-\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}-\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}-\x{2935}\x{2B05}-\x{2B07}\x{2B1B}-\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F0CF}\x{1F170}-\x{1F171}\x{1F17E}-\x{1F17F}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}-\x{1F202}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F23A}\x{1F250}-\x{1F251}\x{1F300}-\x{1F321}\x{1F324}-\x{1F393}\x{1F396}-\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}-\x{1F3F0}\x{1F3F3}-\x{1F3F5}\x{1F3F7}-\x{1F3FA}\x{1F400}-\x{1F4FD}\x{1F4FF}-\x{1F53D}\x{1F549}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F56F}-\x{1F570}\x{1F573}-\x{1F57A}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F590}\x{1F595}-\x{1F596}\x{1F5A4}-\x{1F5A5}\x{1F5A8}\x{1F5B1}-\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}-\x{1F64F}\x{1F680}-\x{1F6C5}\x{1F6CB}-\x{1F6D2}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6EB}-\x{1F6EC}\x{1F6F0}\x{1F6F3}-\x{1F6F9}\x{1F910}-\x{1F93A}\x{1F93C}-\x{1F93E}\x{1F940}-\x{1F945}\x{1F947}-\x{1F970}\x{1F973}-\x{1F976}\x{1F97A}\x{1F97C}-\x{1F9A2}\x{1F9B0}-\x{1F9B9}\x{1F9C0}-\x{1F9C2}\x{1F9D0}-\x{1F9FF}]/u', '', $text);

    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $clean_text);

    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);

    // Match Miscellaneous Symbols
    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, '', $clean_text);

    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    $clean_text = preg_replace($regexDingbats, '', $clean_text);

    return $clean_text;
	}

	// Helper function to run the vision query
	private function runVisionQuery( $mediaId, $binary_path, $prompt ) {
		global $mwai;
		if ( is_null( $mwai ) ) {
			$this->core->log( '🚫 AI Engine is missing for a vision query.' );
			return false;
		}
	
		try {
			if ( is_null( $binary_path ) ) {
				$intermediate = image_get_intermediate_size( $mediaId, 'medium' );
				$path = $intermediate 
					? path_join( dirname( get_attached_file( $mediaId ) ), $intermediate['file'] ) 
					: get_attached_file( $mediaId );
				$url = $intermediate 
					? wp_get_attachment_image_url( $mediaId, 'medium' ) 
					: wp_get_attachment_url( $mediaId );
				
				// Log information about the media
				$this->core->log("🔍 Vision Query for Media ID: " . $mediaId);
				$this->core->log("🖼️ Intermediate: " . print_r($intermediate, true));
				$this->core->log("📂 Path: " . $path);
				$this->core->log("🌐 URL: " . $url);
				
				return $mwai->simpleVisionQuery( $prompt, $url, $path, ['scope' => 'renamer'] );
			} else {
				return $mwai->simpleVisionQuery( $prompt, null, $binary_path, ['scope' => 'renamer'] );
			}
		} catch (Exception $e) {
			// Log the exception message and the full backtrace for debugging.
			$this->core->log("🚫 Vision Query Error: " . $e->getMessage());
			return false;
		}
	}

	function vision_suggestion( $res, $mediaId, $binary_path, $prompt ) {
		$isVision = $this->core->get_option( 'vision_rename_ai', false );
		if ( !$isVision ) { return $res; }
	
		global $mwai;
		if ( is_null( $mwai ) ) {
			$this->core->log( '🚫 AI Engine is missing for a vision suggestion.' );
			return $res;
		}

		$inVisionCache = $this->core->get_option( 'vision_rename_ai_cache', false );
		$cacheKey = !empty( $mediaId ) ? ( 'mfrh_vision_' . $mediaId ) : $this->onceCacheKey;
		
		// If cache is disabled, directly run the vision query
		if ( empty( $inVisionCache ) ) {
			$vision = $this->runVisionQuery( $mediaId, $binary_path, $prompt );
			if ( $vision !== false ) {
				return $vision;
			} else {
				return $res;
			}
		}
	
		// If no cache, run the vision query and set the cache
		$imageDesc = get_transient( $cacheKey );
		if ( empty( $imageDesc ) ) {
			$promptDesc = "Describe this image in detail, in 4 paragraphs.";
			$exifContext = $this->core->get_option( 'exif_context', false );
			if ( $exifContext ) {
				$filePath = $binary_path ? $binary_path : get_attached_file( $mediaId );
				$data = $this->core->get_exif_data( $filePath );
				if ( !empty( $data ) ) {
					$promptDesc .= " There is some data available, which might help you describe it better. If available, the title should absolutely be used to craft the the description, caption might be useful too. The keywords will also be pointers to what the image is about. Here is the available data:\n";
					foreach ( $data as $key => $value ) {
						$promptDesc .= '- ' . ucfirst( $key ) . ": " . $value . "\n";
					}
				}
			}
			$imageDesc = $this->runVisionQuery( $mediaId, $binary_path, $promptDesc );
			if ( $imageDesc === false ) {
				return $res;
			}
			set_transient( $cacheKey, $imageDesc, (int)$inVisionCache );
		}
	
		$textPrompt = "Description of the image:\n\n===\n" . $imageDesc . "\n===\n\n" . $prompt;
		$res = $mwai->simpleTextQuery( $textPrompt, ['scope' => 'renamer'] );
		return $res;
	}

	function additional_filter( $new_filename, $old_filename_no_ext, $media ) {
		if ( !empty( $media ) && isset( $media['ID'] ) ) {
			$attachedPost = $this->get_post_from_media( $media['ID'] );
			if ( !empty( $attachedPost ) ) {
				return apply_filters( 'mfrh_new_filename_attached', $new_filename, $old_filename_no_ext, $media, $attachedPost );
			}
		}
		return $new_filename;
	}

	// mfrh_converts was a trick filter, maybe it's useless now
	function transliterate( $new_filename, $old_filename_no_ext, $media ) {
		if ( !( $this->core->get_option( 'convert_to_ascii', false ) && $this->admin->is_registered() ) ) {
			return $new_filename;
		}
		$new_filename = $this->remove_emoji( $new_filename );
		$new_filename = $this->remove_special_chars( $new_filename );
		$new_filename = remove_accents( $new_filename );
		$new_filename = $this->transliterate_to_ascii( $new_filename );
		$new_filename = strtolower( $new_filename );
		$new_filename = $this->engine->format_hyphens( $new_filename );
		return $new_filename;
	}

	function numbered() {
		return $this->core->get_option( 'numbered_files', false ) && $this->admin->is_registered();
	}

	function force_rename() {
		return $this->core->get_option( 'force_rename', false ) && $this->admin->is_registered();
	}

	function add_redirection( $post, $old_filepath, $new_filepath ) {
		if ( !class_exists( 'Red_Item' ) ) {
			return;
		}
		// old path
		$before = wp_parse_url( wp_get_attachment_url( $post['ID'] ), PHP_URL_PATH );

		// new path
		$old_path_parts = mfrh_pathinfo( $old_filepath );
		$new_path_parts = mfrh_pathinfo( $new_filepath );
		$old_file_name = $old_path_parts['basename'];
		$new_file_name = $new_path_parts['basename'];
		$after = str_replace($old_file_name, $new_file_name, $before);

		if ($before === $after) {
			return;
		}

		// Check whether the redirection already set or not to avoid to avoid duplication.
		$data = [
			'filterBy' => [
				'url'      => $before,
				'target'   => $after,
				'group_id' => 1,
				'status'   => 'enabled',
			],
		];
		$registered_info = Red_Item::get_filtered( $data );
		if ($registered_info['total'] > 0) {
			return;
		}

		// create redirection
		$data = [
			'url'         => $before,
			'action_data' => [ 'url' => $after ],
			'match_type'  => 'url',
			'action_type' => 'url',
			'action_code' => 301,
			'group_id'    => 1,
			'status'      => 'enabled',
		];
		Red_Item::create( $data );
	}
}

?>
