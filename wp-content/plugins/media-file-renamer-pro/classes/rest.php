<?php

class Meow_MFRH_Rest
{
	private $core = null;
	private $admin = null;
	private $namespace = 'media-file-renamer/v1';
	private $allow_usage = false;
	private $allow_setup = false;

	public function __construct( $core ) {
		$this->core = $core;
		$this->admin = $core->admin;

		// FOR DEBUG
		// For experiencing the UI behavior on a slower install.
		// sleep(1);
		// For experiencing the UI behavior on a buggy install.
		// trigger_error( "Error", E_USER_ERROR);
		// trigger_error( "Warning", E_USER_WARNING);
		// trigger_error( "Notice", E_USER_NOTICE);
		// trigger_error( "Deprecated", E_USER_DEPRECATED);

		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	function rest_api_init() {
		$this->allow_usage = apply_filters( 'mfrh_allow_usage', current_user_can( 'administrator' ) );
		$this->allow_setup = apply_filters( 'mfrh_allow_setup', current_user_can( 'manage_options' ) );

		// SETTINGS
		if ( $this->allow_setup ) {
			register_rest_route( $this->namespace, '/update_option', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_settings' ),
				'callback' => array( $this, 'rest_update_option' )
			) );
			register_rest_route( $this->namespace, '/all_settings', array(
				'methods' => 'GET',
				'permission_callback' => array( $this->core, 'can_access_settings' ),
				'callback' => array( $this, 'rest_all_settings' )
			) );
			register_rest_route( $this->namespace, '/reset_options', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_reset_options' )
			) );
			register_rest_route( $this->namespace, '/reset_metadata', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_reset_metadata' )
			) );
			register_rest_route( $this->namespace, '/toggle_parser', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_toggle_parser' )
			) );
			register_rest_route( $this->namespace, '/test_rules', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_test_rules' )
			) );


		}

		// STATS & LISTING
		if ( $this->allow_usage ) {
			register_rest_route( $this->namespace, '/stats', array(
				'methods' => 'GET',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_get_stats' ),
				'args' => array(
					'search' => array( 'required' => false ),
				)
			) );
			register_rest_route( $this->namespace, '/media', array(
				'methods' => 'GET',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_media' ),
				'args' => array(
					'limit' => array( 'required' => false, 'default' => 10 ),
					'skip' => array( 'required' => false, 'default' => 20 ),
					'filterBy' => array( 'required' => false, 'default' => 'all' ),
					'orderBy' => array( 'required' => false, 'default' => 'id' ),
					'order' => array( 'required' => false, 'default' => 'desc' ),
					'search' => array( 'required' => false ),
					'offset' => array( 'required' => false ),
					'order' => array( 'required' => false ),
				)
			) );

			register_rest_route( $this->namespace, '/media/id', array(
				'methods' => 'GET',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_media_id' ),
				'args' => array(
					'filterBy' => array( 'required' => false, 'default' => 'all' ),
				)
			) );

			register_rest_route( $this->namespace, '/uploads_directory_hierarchy', array(
				'methods' => 'GET',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_uploads_directory_hierarchy' ),
				'args' => array(
					'force' => array( 'required' => false, 'default' => false ),
				)
			) );
			register_rest_route( $this->namespace, '/analyze', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_analyze' )
			) );
			register_rest_route( $this->namespace, '/auto_attach', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_auto_attach' )
			) );
			register_rest_route( $this->namespace, '/get_all_ids', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_get_all_ids' )
			) );
			register_rest_route( $this->namespace, '/get_all_post_ids', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_get_all_post_ids' )
			) );

			// ACTIONS
			register_rest_route( $this->namespace, '/set_lock', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_set_lock' )
			) );
			register_rest_route( $this->namespace, '/rename', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_rename' )
			) );
			register_rest_route( $this->namespace, '/move', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_move' )
			) );
			register_rest_route( $this->namespace, '/undo', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_undo' )
			) );
			register_rest_route( $this->namespace, '/status', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_status' )
			) );
			register_rest_route( $this->namespace, '/update_media', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_update_media' )
			) );
			register_rest_route( $this->namespace, '/sync_fields', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_sync_fields' )
			) );
			register_rest_route( $this->namespace, '/ai_suggest', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_ai_suggest' )
			) );
			register_rest_route( $this->namespace, '/create_folder', array(
				'methods' => 'POST',
				'permission_callback' => array( $this->core, 'can_access_features' ),
				'callback' => array( $this, 'rest_create_folder' )
			) );
		}

		// LOGS
		register_rest_route( $this->namespace, '/refresh_logs', array(
			'methods' => 'POST',
			'permission_callback' => array( $this->core, 'can_access_features' ),
			'callback' => array( $this, 'rest_refresh_logs' )
		) );
		register_rest_route( $this->namespace, '/clear_logs', array(
			'methods' => 'POST',
			'permission_callback' => array( $this->core, 'can_access_features' ),
			'callback' => array( $this, 'rest_clear_logs' )
		) );
	}

	function rest_refresh_logs() {
		return new WP_REST_Response( [ 'success' => true, 'data' => $this->core->get_logs() ], 200 );
	}

	function rest_clear_logs() {
		$this->core->clear_logs();
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	function rest_analyze( $request ) {
		$params = $request->get_json_params();
		$mediaIds = isset( $params['mediaIds'] ) ? (array)$params['mediaIds'] : null;
		$mediaId = isset( $params['mediaId'] ) ? (int)$params['mediaId'] : null;
		$data = array();
		if ( !empty( $mediaIds ) ) {
			foreach ( $mediaIds as $mediaId ) {
				$entry = $this->core->get_media_status_one( $mediaId );
				array_push( $data, $entry );
			}
		}
		else if ( !empty( $mediaId ) ) {
			$data = $this->core->get_media_status_one( $mediaId );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ], 200 );
	}

	function rest_auto_attach( $request ) {
		$params = $request->get_json_params();
		$post_ids = isset( $params['postIds'] ) ? (array)$params['postIds'] : null;
		$post_id = isset( $params['postId'] ) ? (int)$params['postId'] : null;
		$media_ids = isset( $params['mediaIds'] ) ? (array)$params['mediaIds'] : [];
		if ( !empty( $post_ids ) ) {
			foreach ( $post_ids as $post_id ) {
				$this->do_auto_attach( $post_id, $media_ids );
			}
		}
		else if ( !empty( $post_id ) ) {
			$this->do_auto_attach( $post_id, $media_ids );
		}
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	/**
	 * TODO: delete if not used anywhere
	 */
	function rest_get_all_ids( $request ) {
		global $wpdb;
		$params = $request->get_json_params();
		$unlockedOnly = isset( $params['unlockedOnly'] ) ? (bool)$params['unlockedOnly'] : false;

		$innerJoinCondition = '';
		if ( $this->core->featured_only ) {
			$innerJoinCondition = "INNER JOIN $wpdb->postmeta pmm ON pmm.meta_value = p.ID AND pmm.meta_key = '_thumbnail_id'";
		}

		if ( $unlockedOnly ) {
			$ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts p 
				$innerJoinCondition
				LEFT JOIN $wpdb->postmeta pm ON p.ID = pm.post_id 
				AND pm.meta_key='_manual_file_renaming'
				WHERE post_type='attachment'
				AND post_status='inherit'
				AND pm.meta_value IS NULL"
			);
		}
		else {
			$ids = $wpdb->get_col( "SELECT ID FROM $wpdb->posts p 
				WHERE post_type='attachment'
				AND post_status='inherit'"
			);
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $ids ], 200 );
	}

	function rest_get_all_post_ids(  ) {
		global $wpdb;
		$ids = $wpdb->get_col( "SELECT p.ID FROM $wpdb->posts p
			WHERE p.post_status NOT IN ('inherit', 'trash', 'auto-draft')
			AND p.post_type NOT IN ('attachment', 'shop_order', 'shop_order_refund', 'nav_menu_item', 'revision', 'auto-draft', 'wphb_minify_group', 'customize_changeset', 'oembed_cache', 'nf_sub')
			AND p.post_type NOT LIKE 'dlssus%'
			AND p.post_type NOT LIKE 'ml-slide%'
			AND p.post_type NOT LIKE '%acf-%'
			AND p.post_type NOT LIKE '%edd%'"
		);
		return new WP_REST_Response( [ 'success' => true, 'data' => $ids ], 200 );
	}

	function rest_uploads_directory_hierarchy( $request ) {
		if ( !$this->admin->is_pro_user() ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => __( 'This feature for Pro users.', 'media-file-renamer' ) ], 200 );
		}

		$force = trim( $request->get_param('force') ) === 'true';
		$transientKey = 'uploads_directory_hierarchy';
		if ( $force ) {
			delete_transient( $transientKey );
		}

		$data = get_transient( $transientKey );
		if ( !$data ) {
			$data = $this->core->get_uploads_directory_hierarchy();
			set_transient( $transientKey, $data );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ], 200 );
	}

	function rest_create_folder( $request ) {
		if ( !$this->admin->is_pro_user() ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => __( 'This feature for Pro users.', 'media-file-renamer' ) ], 200 );
		}

		try {
			$params = $request->get_json_params();
			$folder = isset( $params['folder'] ) ? $params['folder'] : null;
			if ( !$folder ) {
				throw new Exception( __( 'The folder name is missing.', 'media-file-renamer' ) );
			}
			$this->core->create_folder( $folder );
			return new WP_REST_Response( [ 'success' => true ], 200 );
		} catch ( Exception $e ) {
			return new WP_REST_Response([
				'success' => false,
				'message' => $e->getMessage(),
			], 200 );
		}
	}

	function rest_status( $request ) {
		$params = $request->get_json_params();
		$mediaId = (int)$params['mediaId'];
		$entry = $this->core->get_media_status_one( $mediaId );
		return new WP_REST_Response( [ 'success' => true, 'data' => $entry ], 200 );
	}

	function rest_update_media( $request ) {
		$params = is_array( $request ) ? $request : $request->get_json_params();
		$id = isset( $params['id'] ) ? $params['id'] : '';
		$postTitle = isset( $params['post_title'] ) ? $params['post_title'] : '';
		$imageAlt = isset( $params['image_alt'] ) ? $params['image_alt'] : '';
		$imageDescription = isset( $params['image_description'] ) ? $params['image_description'] : '';
		$imageCaption = isset( $params['image_caption'] ) ? $params['image_caption'] : '';
		$method = isset( $params['method'] ) ? $params['method'] : 'manual';
		$sync = isset( $params['sync'] ) ? $params['sync'] : false;

		$result = $this->core->update_media($id, $postTitle, $imageAlt, $imageDescription, $imageCaption, $method, $sync);

		if (!empty($result['errors'])) {
			return new WP_REST_Response([
				'success' => false,
				'message' => implode(',', $result['errors']),
			], 200 );
		}

		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	function rest_sync_fields( $request ) {
		$params = $request->get_json_params();
		$mediaId = (int)$params['mediaId'];
		$useVision = isset( $params['ai'] ) ? (bool)$params['ai'] : false;
		$timeFilter = isset( $params['syncFieldsTimeFilter'] ) ? (int)$params['syncFieldsTimeFilter'] : 0;

		$post = get_post( $mediaId, ARRAY_A );
		if ( !$post ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'The media ID is invalid.' ], 200 );
		}

		do_action( 'mfrh_media_resync', $post, $useVision, $timeFilter );
		return new WP_REST_Response( [ 'success' => true, 'data' => [] ], 200 );
	}

	function rest_ai_suggest ( $request ) {
		$params = is_array( $request ) ? $request : $request->get_json_params();
		$mediaId = (int)$params['mediaId'];
		$metadataType = isset( $params['type'] ) ? (string)$params['type'] : null;

		if ( !$mediaId || !$metadataType ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'The media ID or the metadata type is missing.' ], 200 );
		}

		try {
			$newMetadata = $this->core->ai_suggestion( $mediaId, $metadataType );

			if ( empty( $newMetadata ) ) {
				return new WP_REST_Response( [ 'success' => false, 'message' => 'No suggestion.' ], 200 );
			}

			return new WP_REST_Response( [ 'success' => true, 'data' => $newMetadata ], 200 );
		}
		catch ( Exception $e ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => $e->getMessage() ], 200 );
		}
	}

	function rest_rename( $request ) {
		$params = $request->get_json_params();
		$mediaId = (int)$params['mediaId'];
		$filename = isset( $params['filename'] ) ? (string)$params['filename'] : null;
		$renameMethod = isset( $params['renameMethod'] ) ? (string)$params['renameMethod'] : null;
		if ( $filename && $renameMethod === 'auto' ) {
			$this->core->log( 'The rename method is set to Auto but a filename is provided. The filename will be ignored.' );
			$filename = null;
		}

		try {
			$res = $this->core->engine->rename( $mediaId, $filename, false, $renameMethod ?? 'auto' );
			$entry = $this->core->get_media_status_one( $mediaId );
			$response = [ 'success' => !!$res, 'data' => $entry ];

			if ( array_key_exists( 'warning', $res ) ) {
				$response['warning'] = $res['warning'];
			}

			return new WP_REST_Response( $response, 200 );
		}
		catch ( Exception $e ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => $e->getMessage() ], 200 );
		}
	}

	function rest_move( $request ) {
		$params = $request->get_json_params();
		$mediaId = (int)$params['mediaId'];
		$newPath = isset( $params['newPath'] ) ? (string)$params['newPath'] : null;
		$res = $this->core->move( $mediaId, $newPath );
		$entry = $this->core->get_media_status_one( $mediaId );
		return new WP_REST_Response( [ 'success' => !!$res, 'data' => $entry ], 200 );
	}

	function rest_undo( $request ) {
		$params = $request->get_json_params();
		$mediaId = (int)$params['mediaId'];
		$res = $this->core->undo( $mediaId );
		$entry = $this->core->get_media_status_one( $mediaId );
		return new WP_REST_Response( [ 'success' => !!$res, 'data' => $entry ], 200 );
	}

	function rest_set_lock( $request ) {
		$params = $request->get_json_params();
		$lock = (boolean)$params['lock'];
		$mediaIds = isset( $params['mediaIds'] ) ? (array)$params['mediaIds'] : null;
		$mediaId = isset( $params['mediaId'] ) ? (int)$params['mediaId'] : null;
		$data = null;
		if ( !empty( $mediaIds ) ) {
			foreach ( $mediaIds as $mediaId ) {
				$lock ? $this->core->lock( $mediaId ) : $this->core->unlock( $mediaId );
			}
			$data = 'N/A';
		}
		else if ( !empty( $mediaId ) ) {
			$lock ? $this->core->lock( $mediaId ) : $this->core->unlock( $mediaId );
			$data = $this->core->get_media_status_one( $mediaId );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ], 200 );
	}

	

	function count_locked( $search ) {
		global $wpdb;
		$innerJoinSql = '';
		$whereSql = '';
		if ( $search ) {
			$innerJoinSql = "INNER JOIN $wpdb->postmeta pm2 ON pm2.post_id = p.ID AND pm2.meta_key = '_wp_attached_file'";
			$searchValue = '%' . $wpdb->esc_like( $search ) . '%';
			$whereSql = $wpdb->prepare( "AND (p.post_title LIKE %s OR pm2.meta_value LIKE %s)", $searchValue, $searchValue );
		}
		if ( $this->core->featured_only ) {
			$innerJoinSql .= " INNER JOIN $wpdb->postmeta pmm ON pmm.meta_value = p.ID AND pmm.meta_key = '_thumbnail_id'";
		}
		return (int)$wpdb->get_var( "SELECT COUNT( DISTINCT p.ID) FROM $wpdb->posts p 
			INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.ID AND pm.meta_key = '_manual_file_renaming'
			$innerJoinSql 
			WHERE p.post_type = 'attachment' AND p.post_status = 'inherit' $whereSql"
		);
	}

	function count_pending( $search ) {
		global $wpdb;
		$whereCaluses = [];
		if ( $this->core->images_only ) {
			$images_mime_types = implode( "','", $this->core->images_mime_types );
			$whereCaluses[] = "p.post_mime_type IN ( '$images_mime_types' )";
		}
		$innerJoinSql = '';
		if ( $search ) {
			$innerJoinSql = "INNER JOIN $wpdb->postmeta pm2 ON pm2.post_id = p.ID AND pm2.meta_key = '_wp_attached_file'";
			$searchValue = '%' . $wpdb->esc_like($search) . '%';
			$whereCaluses[] = $wpdb->prepare("(p.post_title LIKE %s OR pm2.meta_value LIKE %s)", $searchValue, $searchValue);
		}
		if ( $this->core->featured_only ) {
			$innerJoinSql .= " INNER JOIN $wpdb->postmeta pmm ON pmm.meta_value = p.ID AND pmm.meta_key = '_thumbnail_id'";
		}
		$whereSql = count( $whereCaluses ) > 0 ? "AND " . implode( "AND ", $whereCaluses ) : "";
		return (int)$wpdb->get_var( "SELECT COUNT(DISTINCT p.ID) FROM $wpdb->posts p 
			INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.ID AND pm.meta_key = '_require_file_renaming'
			$innerJoinSql 
			WHERE p.post_type = 'attachment' AND p.post_status = 'inherit' $whereSql"
		);
	}

	function count_renamed($search) {
		global $wpdb;
		$whereCaluses = [];
		if ( $this->core->images_only ) {
			$images_mime_types = implode( "','", $this->core->images_mime_types );
			$whereCaluses[] = "p.post_mime_type IN ( '$images_mime_types' )";
		}
		$innerJoinSql = '';
		if ($search) {
			$innerJoinSql = "INNER JOIN $wpdb->postmeta pm2 ON pm2.post_id = p.ID AND pm2.meta_key = '_wp_attached_file'";
			$searchValue = '%' . $wpdb->esc_like($search) . '%';
			$whereCaluses[] = $wpdb->prepare("(p.post_title LIKE %s OR pm2.meta_value LIKE %s)", $searchValue, $searchValue);
		}
		if ( $this->core->featured_only ) {
			$innerJoinSql .= " INNER JOIN $wpdb->postmeta pmm ON pmm.meta_value = p.ID AND pmm.meta_key = '_thumbnail_id'";
		}
		$whereSql = count($whereCaluses) > 0 ? "AND " . implode("AND ", $whereCaluses) : "";
		return (int)$wpdb->get_var( "SELECT COUNT(DISTINCT p.ID) FROM $wpdb->posts p 
			INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.ID AND pm.meta_key = '_original_filename'
			$innerJoinSql 
			WHERE p.post_type = 'attachment' AND p.post_status = 'inherit' $whereSql"
		);
	}

	function count_all( $search ) {
		global $wpdb;
		$whereCaluses = [];
		if ( $this->core->images_only ) {
			$images_mime_types = implode( "','", $this->core->images_mime_types );
			$whereCaluses[] = "p.post_mime_type IN ( '$images_mime_types' )";
		}
		$innerJoinSql = '';
		if ( $search ) {
			$innerJoinSql = "INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.ID";
			$searchValue = '%' . $wpdb->esc_like($search) . '%';
			$whereCaluses[] = $wpdb->prepare("( p.post_title LIKE %s OR pm.meta_value LIKE %s )", $searchValue, $searchValue);
		}
		if ( $this->core->featured_only ) {
			$innerJoinSql .= " INNER JOIN $wpdb->postmeta pmm ON pmm.meta_value = p.ID AND pmm.meta_key = '_thumbnail_id'";
		}
		$whereSql = count($whereCaluses) > 0 ? "AND " . implode("AND ", $whereCaluses) : "";
		return (int)$wpdb->get_var( "SELECT COUNT(DISTINCT p.ID) FROM $wpdb->posts p 
			$innerJoinSql 
			WHERE post_type='attachment' AND post_status='inherit' $whereSql"
		);
	}

	function rest_get_stats($request) {
		$search = trim( $request->get_param( 'search' ) );
		$pending = $this->count_pending( $search );
		$all = $this->count_all( $search );
		$locked = $this->count_locked( $search );
		$renamed = $this->count_renamed( $search );
		$unlocked = $all - $locked;
		return new WP_REST_Response( [ 'success' => true, 'data' => array(
			'all' => $all,
			'locked' => $locked,
			'unlocked' => $unlocked,
			'renamed' => $renamed,
			'pending' => $pending,
			'unrenamed' => $all - $renamed,
		) ], 200 );
	}

	/**
	 * Simply get only the media IDs with a simple and fast query.
	 *
	 * @param integer      $skip
	 * @param integer      $limit
	 * @param string       $filterBy
	 * @param string|null  $search
	 * @param boolean      $hide_locked
	 * @return array
	 */
	function get_media_ids( $skip, $limit, $filterBy, $search = null, $hide_locked = true )
	{
		global $wpdb;

		// Initialize query parts
		$joins   = array();
		$wheres  = array();

		// Base WHERE conditions
		$wheres[] = "p.post_type = 'attachment'";
		$wheres[] = "p.post_status = 'inherit'";

		// Handle featured_only and images_only options
		if ( $this->core->featured_only ) {
			$joins[] = "INNER JOIN {$wpdb->postmeta} pmm ON pmm.meta_value = p.ID AND pmm.meta_key = '_thumbnail_id'";
		} elseif ( $this->core->images_only ) {
			$images_mime_types = implode( "','", $this->core->images_mime_types );
			$wheres[] = "p.post_mime_type IN ('$images_mime_types')";
		}

		// Depending on filterBy, add necessary joins and conditions
		if ( $filterBy === 'pending' ) {
			$joins[] = "INNER JOIN {$wpdb->postmeta} pm1 ON pm1.post_id = p.ID AND pm1.meta_key = '_require_file_renaming' AND pm1.meta_value IS NOT NULL";
		} elseif ( $filterBy === 'renamed' ) {
			$joins[] = "INNER JOIN {$wpdb->postmeta} pm1 ON pm1.post_id = p.ID AND pm1.meta_key = '_original_filename' AND pm1.meta_value IS NOT NULL";
		} elseif ( $filterBy === 'unrenamed' ) {
			$joins[]  = "LEFT JOIN {$wpdb->postmeta} pm1 ON pm1.post_id = p.ID AND pm1.meta_key = '_original_filename'";
			$wheres[] = "pm1.meta_value IS NULL";
		} elseif ( $filterBy === 'locked' ) {
			$joins[] = "INNER JOIN {$wpdb->postmeta} pm1 ON pm1.post_id = p.ID AND pm1.meta_key = '_manual_file_renaming' AND pm1.meta_value IS NOT NULL";
		} elseif ( $filterBy === 'unlocked' ) {
			$joins[]  = "LEFT JOIN {$wpdb->postmeta} pm1 ON pm1.post_id = p.ID AND pm1.meta_key = '_manual_file_renaming'";
			$wheres[] = "pm1.meta_value IS NULL";
		}

		// Hide locked files if hide_locked is true
		if ( $hide_locked && $filterBy !== 'locked' && $filterBy !== 'unlocked' ) {
			$joins[]  = "LEFT JOIN {$wpdb->postmeta} pm2 ON pm2.post_id = p.ID AND pm2.meta_key = '_manual_file_renaming'";
			$wheres[] = "pm2.meta_value IS NULL";
		}

		// Handle search functionality
		if ( $search ) {
			$search_value = '%' . $wpdb->esc_like( $search ) . '%';
			$joins[]      = "LEFT JOIN {$wpdb->postmeta} pm_search ON pm_search.post_id = p.ID";
			$wheres[]     = $wpdb->prepare( "(p.post_title LIKE %s OR pm_search.meta_value LIKE %s)", $search_value, $search_value );
		}

		// Build the query
		$query = "SELECT DISTINCT p.ID FROM {$wpdb->posts} p";
		if ( ! empty( $joins ) ) {
			$query .= ' ' . implode( ' ', $joins );
		}
		if ( ! empty( $wheres ) ) {
			$query .= ' WHERE ' . implode( ' AND ', $wheres );
		}
		$query .= $wpdb->prepare( " LIMIT %d, %d", $skip, $limit );

		// Retrieve and return the IDs
		$ids = $wpdb->get_col( $query );

		return $ids;
	}

	/**
	 * Get the status for many Media IDs.
	 *
	 * @param integer $skip
	 * @param integer $limit
	 * @param string $filterBy
	 * @param string $orderBy
	 * @param string $order
	 * @param string|null $search
	 * @return array
	 */
	function get_media_status(
        $skip = 0,
        $limit = 10,
        $filterBy = 'pending',
        $orderBy = 'post_title',
        $order = 'asc',
        $search = null,
        $hide_locked = true
    ) {
        global $wpdb;

        // Initialize an array to hold HAVING conditions
        $havingConditions = array();

        // Add conditions based on the filter
        if ( $filterBy === 'pending' ) {
            $havingConditions[] = 'pending IS NOT NULL';
        } else if ( $filterBy === 'renamed' ) {
            $havingConditions[] = 'original_filename IS NOT NULL';
        } else if ( $filterBy === 'unrenamed' ) {
            $havingConditions[] = 'original_filename IS NULL';
        } else if ( $filterBy === 'locked' ) {
            $havingConditions[] = 'locked IS NOT NULL';
        } else if ( $filterBy === 'unlocked' ) {
            $havingConditions[] = 'locked IS NULL';
        }

        // Hide locked files in other filters if hide_locked is true
        if ( $hide_locked && $filterBy !== 'locked' && $filterBy !== 'unlocked' ) {
            $havingConditions[] = 'locked IS NULL';
        }

        if ( count( $havingConditions ) > 0 ) {
            $havingSql = 'HAVING ' . implode( ' AND ', $havingConditions );
        } else {
            $havingSql = '';
        }

        $orderSql = 'ORDER BY p.ID DESC';
        if ( $orderBy === 'post_title' ) {
            $orderSql = 'ORDER BY post_title ' . ( $order === 'asc' ? 'ASC' : 'DESC' );
        } else if ( $orderBy === 'post_parent' ) {
            $orderSql = 'ORDER BY post_parent ' . ( $order === 'asc' ? 'ASC' : 'DESC' );
        } else if ( $orderBy === 'current_filename' ) {
            $orderSql = 'ORDER BY current_filename ' . ( $order === 'asc' ? 'ASC' : 'DESC' );
        }

        $whereSql = '';
        if ( $search ) {
            $searchValue = '%' . $wpdb->esc_like( $search ) . '%';
            $whereSql = $wpdb->prepare( "AND (p.post_title LIKE %s OR pm.meta_value LIKE %s)", $searchValue, $searchValue );
        }

        $innerJoinCondition = '';
        if ( $this->core->featured_only ) {
            $innerJoinCondition = "INNER JOIN $wpdb->postmeta pmm ON pmm.meta_value = p.ID AND pmm.meta_key = '_thumbnail_id'";
        } else {
            if ( $this->core->images_only ) {
                $images_mime_types = implode( "','", $this->core->images_mime_types );
                $whereSql .= "$whereSql AND p.post_mime_type IN ('$images_mime_types')";
            }
        }

        $request = $wpdb->prepare( "
            SELECT p.ID, p.post_title, p.post_parent, p.post_content AS image_description, p.post_excerpt AS image_caption,
                MAX(CASE WHEN pm.meta_key = '_wp_attached_file' THEN pm.meta_value END) AS current_filename,
                MAX(CASE WHEN pm.meta_key = '_original_filename' THEN pm.meta_value END) AS original_filename,
                MAX(CASE WHEN pm.meta_key = '_wp_attachment_metadata' THEN pm.meta_value END) AS metadata,
                MAX(CASE WHEN pm.meta_key = '_wp_attachment_image_alt' THEN pm.meta_value END) AS image_alt,
                MAX(CASE WHEN pm.meta_key = '_require_file_renaming' THEN pm.meta_value END) AS pending,
                MAX(CASE WHEN pm.meta_key = '_manual_file_renaming' THEN pm.meta_value END) AS locked,
                MAX(CASE WHEN pm.meta_key = '_mfrh_history' THEN pm.meta_value END) AS history
            FROM (
                SELECT p.ID,
                    MAX(CASE WHEN pm.meta_key = '_original_filename' THEN pm.meta_value END) AS original_filename,
                    MAX(CASE WHEN pm.meta_key = '_require_file_renaming' THEN pm.meta_value END) AS pending,
                    MAX(CASE WHEN pm.meta_key = '_manual_file_renaming' THEN pm.meta_value END) AS locked
                FROM $wpdb->posts p
                $innerJoinCondition
                JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
                WHERE p.post_type = 'attachment'
                    AND p.post_status = 'inherit'
                    $whereSql
                GROUP BY p.ID
                $havingSql
            ) AS filtered_posts
            JOIN $wpdb->posts p ON p.ID = filtered_posts.ID
            JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
            WHERE (pm.meta_key = '_wp_attached_file'
                    OR pm.meta_key = '_original_filename'
                    OR pm.meta_key = '_wp_attachment_metadata'
                    OR pm.meta_key = '_wp_attachment_image_alt'
                    OR pm.meta_key = '_require_file_renaming'
                    OR pm.meta_key = '_manual_file_renaming'
                    OR pm.meta_key = '_mfrh_history')
            GROUP BY p.ID
            $orderSql
            LIMIT %d, %d
        ", $skip, $limit );

        $entries = $wpdb->get_results( $request );

        foreach ( $entries as $entry ) {
            $this->core->consolidate_media_status( $entry );
            if ( !is_null( $entry->history ) ) {
                $entry->history = unserialize( $entry->history );
            }
        }

        return $entries;
    }

	function rest_media_id( $request ) {
		$filterBy = trim( $request->get_param( 'filterBy' ) );
		$skip     = trim( $request->get_param( 'skip' ) );
		$limit    = trim( $request->get_param( 'limit' ) );
		$search   = trim( $request->get_param( 'search' ) );
	
		$hide_locked = $this->core->get_option( 'hide_locked', true );
	
		$entries = $this->get_media_ids( $skip, $limit, $filterBy, $search, $hide_locked );
		return new WP_REST_Response( [ 'success' => true, 'data' => $entries ], 200 );
	}

	function rest_media( $request ) {
		$limit = trim( $request->get_param('limit') );
		$skip = trim( $request->get_param('skip') );
		$filterBy = trim( $request->get_param('filterBy') );
		$orderBy = trim( $request->get_param('orderBy') );
		$order = trim( $request->get_param('order') );
		$search = trim( $request->get_param('search') );
		
		$hide_locked = $this->core->get_option( 'hide_locked', true );

		$entries = $this->get_media_status( $skip, $limit, $filterBy, $orderBy, $order, $search, $hide_locked );
		
		return new WP_REST_Response( [ 'success' => true, 'data' => $entries ], 200 );
	}

	function rest_all_settings() {
		return new WP_REST_Response( [ 'success' => true, 'data' => $this->core->get_all_options() ], 200 );
	}

	function rest_reset_options() {
		$this->core->reset_options();
		return new WP_REST_Response( [ 'success' => true, 'options' => $this->core->get_all_options() ], 200 );
	}

	function rest_reset_metadata() {
		$this->core->reset_metadata();
		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	function rest_update_option( $request ) {
		try {
			$params = $request->get_json_params();
			$value = $params['options'];
			list( $options, $success, $message ) = $this->core->update_options( $value );
			return new WP_REST_Response([ 'success' => $success, 'message' => $message, 'options' => $options ], 200 );
		}
		catch ( Exception $e ) {
			return new WP_REST_Response([ 'success' => false, 'message' => $e->getMessage() ], 500 );
		}
	}

	function rest_toggle_parser( $request ) {
		try {
			$params = $request->get_json_params();
			$parser = $params['parser'];

			$res = $this->core->toggle_parser( $parser );
			if ( !$res ) {
				throw new Exception( __( 'The parser could not be toggled.', 'media-file-renamer' ) );
			}

			return new WP_REST_Response([ 'success' => true, 'options' => $res[0] ], 200 );
		}
		catch ( Exception $e ) {
			return new WP_REST_Response([ 'success' => false, 'message' => $e->getMessage() ], 500 );
		}
	}

	function rest_test_rules( $request ) {
		try {
			$params = $request->get_json_params();
			$filename = $params['filename'];

			$new_filename = $this->core->engine->new_filename( $filename , $filename );

			if ( !$new_filename ) {
				throw new Exception( __( 'The test could not be performed.', 'media-file-renamer' ) );
			}

			return new WP_REST_Response([ 'success' => true, 'filename' => $new_filename ], 200 );
		}
		catch ( Exception $e ) {
			return new WP_REST_Response([ 'success' => false, 'message' => $e->getMessage() ], 500 );
		}
	}

	function validate_updated_option( $option_name ) {
		$needsCheckingOptions = [
			'mfrh_auto_rename',
			'mfrh_sync_alt',
			'mfrh_sync_media_title',
			'mfrh_force_rename',
			'mfrh_numbered_files'
		];
		if ( !in_array( $option_name, $needsCheckingOptions ) ) {
			return $this->createValidationResult();
		}

		if ( $option_name === 'mfrh_force_rename' || $option_name === 'mfrh_numbered_files' ) {
			$force_rename = $this->core->get_option( 'force_rename', false );
			$numbered_files = $this->core->get_option( 'numbered_files', false );

			if ( !$force_rename || !$numbered_files ) {
				return $this->createValidationResult();
			}

			update_option( 'mfrh_force_rename', false, false );
			return $this->createValidationResult( false, __( 'Force Rename and Numbered Files cannot be used at the same time. Please use Force Rename only when you are trying to repair a broken install. For now, Force Rename has been disabled.', 'media-file-renamer' ));

		} 
		else if ( $option_name === 'mfrh_auto_rename' || $option_name === 'mfrh_sync_alt' || 
			$option_name ==='mfrh_sync_media_title' ) {
			if ( $this->core->method !== 'alt_text' && $this->core->method !== 'media_title' ) {
				return $this->createValidationResult();
			}

			$sync_alt = $this->core->get_option( 'sync_alt' );
			if ( $sync_alt && $this->core->method === 'alt_text' ) {
				update_option( 'mfrh_sync_alt', false, false );
				return $this->createValidationResult( false, __( 'The option Sync ALT was turned off since it does not make sense to have it with this Auto-Rename mode.', 'media-file-renamer' ));
			}

			$sync_meta_title = $this->core->get_option( 'sync_media_title' );
			if ( $sync_meta_title && $this->core->method === 'media_title' ) {
				update_option( 'mfrh_sync_media_title', false, false );
				return $this->createValidationResult( false, __( 'The option Sync Media Title was turned off since it does not make sense to have it with this Auto-Rename mode.', 'media-file-renamer' ));
			}
		}
		return $this->createValidationResult();
	}

	function createValidationResult( $result = true, $message = null) {
		$message = $message ? $message : __( 'Option updated.', 'media-file-renamer' );
		return ['result' => $result, 'message' => $message];
	}

	function do_auto_attach( $post_id, $selected_media_ids ) {
		$media_ids = [];

		// If the global variable $wpmc exists, it means Media Cleaner functions are accessible
		// and we can use them to attach the media to its post.
		global $wpmc;
		if ( !empty( $wpmc ) ) {
			$references = $wpmc->get_references_for_post_id( $post_id );
			if ( !empty( $references ) ) {
				foreach ( $references as $reference ) {
					$media_ids[] = $reference['mediaId'];
				}
			}
		}

		// Otherwise, let's go through simple WordPress functions.
		if ( empty( $media_ids ) ) {
			// Featured Image
			$mediaId = get_post_thumbnail_id( $post_id );
			if ( $mediaId !== false ) {
				$media_ids[] = $mediaId;
			}
			// WooCommerce Product Gallery
			if ( get_post_type( $post_id ) === 'product' && class_exists( 'WC_product' ) ) {
				$product = new WC_product( $post_id );
				$mediaIds = $product->get_gallery_image_ids();
				if ( !empty( $mediaIds ) ) {
					foreach ( $mediaIds as $mediaId ) {
						$media_ids[] = $mediaId;
					}
				}
			}
		}

		if ( count( $selected_media_ids ) > 0 ) {
			$media_ids = array_unique( array_intersect( $media_ids, $selected_media_ids ) );
		}

		if ( empty( $media_ids ) ) {
			return;
		}

		// Attach all the Media IDs to this post.
		foreach ( $media_ids as $mediaId ) {
			$attachment = array( 'ID' => (int)$mediaId, 'post_parent' => (int)$post_id );
			wp_update_post( $attachment );
		}
	}
}

?>