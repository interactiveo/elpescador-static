<?php

class MeowPro_MFRH_CLI extends WP_CLI_Command {

	public function __construct() {
	}

  function rename_auto( $args ) {
    if ( empty( $args ) ) {
      WP_CLI::error( 'This command requires one or more Media IDs.' );
      return;
    }
    foreach ( $args as $mediaId ) {
      mfrh_rename( $mediaId );
      WP_CLI::line( "Renamed Media ID $mediaId automatically." );
    }
  }

  function rename_manual( $args ) {
    if ( empty( $args ) || count( $args ) !== 2 ) {
      WP_CLI::error( 'This command requires a Media ID and a filename.' );
      return;
    }
    $mediaId = $args[0];
    $filename = $args[1];
    mfrh_rename( $mediaId, $filename );
    WP_CLI::line( "Renamed Media ID $mediaId manually." );
  }

  function unlock_all() {
    global $wpdb;
    $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = '_manual_file_renaming'" );
  }

  function unlock( $args ) {
    if ( empty( $args ) ) {
      WP_CLI::error( 'This command requires one or more Media IDs.' );
      return;
    }
    global $wpdb;
    foreach ( $args as $mediaId ) {
      $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = '_manual_file_renaming'", $mediaId ) );
      WP_CLI::line( "Unlocked Media ID $mediaId." );
    }
  }

  function lock( $args ) {
    if ( empty( $args ) ) {
      WP_CLI::error( 'This command requires one or more Media IDs.' );
      return;
    }
    global $wpdb;
    foreach ( $args as $mediaId ) {
      update_post_meta( $mediaId, '_manual_file_renaming', 1 );
      WP_CLI::line( "Locked Media ID $mediaId." );
    }
  }

  function lock_all() {
    global $wpdb;
    $ids = $wpdb->get_col( "SELECT p.ID FROM $wpdb->posts p WHERE post_status = 'inherit' AND post_type = 'attachment'" );
    foreach ( $ids as $mediaId ) {
      update_post_meta( $mediaId, '_manual_file_renaming', 1 );
      WP_CLI::line( "Locked Media ID $mediaId." );
    }
  }

  function rename_all() {
    global $wpdb;
    $ids = $wpdb->get_col( "SELECT p.ID FROM $wpdb->posts p WHERE post_status = 'inherit' AND post_type = 'attachment'" );
    $idsToRemove = $wpdb->get_col( "SELECT m.post_id FROM $wpdb->postmeta m 
      WHERE m.meta_key = '_manual_file_renaming' and m.meta_value = 1" );
    $ids = array_values( array_diff( $ids, $idsToRemove ) );
    foreach ( $ids as $mediaId ) {
      mfrh_rename( $mediaId );
      WP_CLI::line( "Renamed Media ID $mediaId automatically." );
    }
  }

  function info( $args ) {

		$query_args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
		);


		if ( ! empty( $args ) ) {
			$query_args['post__in'] = $args;
		}

		$attachments = get_posts( $query_args );

		if ( empty( $attachments ) ) {
			WP_CLI::line( 'No media files found.' );
			return;
		}

		$rows    = array();
		$headers = array( 'Title', 'Filename', 'ID', 'Locked' );
		$rows[]  = $headers;

		foreach ( $attachments as $attachment ) {
			$file_path = get_post_meta( $attachment->ID, '_wp_attached_file', true );
			$filename  = basename( $file_path );
			$is_locked = get_post_meta( $attachment->ID, '_manual_file_renaming', true ) == 1 ? 'Yes' : 'No';
			$rows[]    = array( $attachment->post_title, $filename, $attachment->ID, $is_locked );
		}

		$numCols   = count( $headers );
		$colWidths = array();
		for ( $col = 0; $col < $numCols; $col++ ) {
			$maxLen = 0;
			foreach ( $rows as $row ) {
				$cellLen = strlen( (string) $row[ $col ] );
				if ( $cellLen > $maxLen ) {
					$maxLen = $cellLen;
				}
			}
			$colWidths[] = $maxLen;
		}

		// Helper to create a separator row
		$create_separator = function( $colWidths ) {
			$line = '+';
			foreach ( $colWidths as $width ) {
				$line .= str_repeat( '-', $width + 2 ) . '+';
			}
			return $line;
		};

		$separator = $create_separator( $colWidths );
		WP_CLI::line( $separator );

		$headerLine = '|';
		for ( $col = 0; $col < $numCols; $col++ ) {
			$headerLine .= ' ' . str_pad( $rows[0][ $col ], $colWidths[ $col ] ) . ' |';
		}
		WP_CLI::line( $headerLine );
		WP_CLI::line( $separator );

		for ( $i = 1, $total = count( $rows ); $i < $total; $i++ ) {
			$rowLine = '|';
			for ( $col = 0; $col < $numCols; $col++ ) {
				$rowLine .= ' ' . str_pad( (string) $rows[ $i ][ $col ], $colWidths[ $col ] ) . ' |';
			}
			WP_CLI::line( $rowLine );
		}
		WP_CLI::line( $separator );
	}

}

?>