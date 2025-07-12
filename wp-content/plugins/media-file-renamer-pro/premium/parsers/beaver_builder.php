<?php

  // Beaver Builder: Update the Metadata, clear the cache
  // https://www.wpbeaverbuilder.com/frequently-asked-questions/

  add_action( 'mfrh_url_renamed', 'mfrh_beaver_builder', 10, 3 );

  function mfrh_beaver_builder( $post, $orig_image_url, $new_image_url ) {
    global $wpdb, $mfrh_core;


    //Beaver Builder stores the image URL in the postmeta table with the following format:
    //s:10:"image.jpg";
    //s:60:"https://www.example.com/wp-content/uploads/2021/01/image.jpg";
    //We need to replace the old URL and Filename with the new ones and keep the same string length.

    // Build the full URLs with site URL and upload path
    $uploads         = wp_upload_dir();
    $uploads_baseurl = trailingslashit( $uploads['baseurl'] );
    
    $full_orig_url   = $uploads_baseurl . $orig_image_url;
    $full_new_url    = $uploads_baseurl . $new_image_url;
    
    // Extract just the filenames (without path)
    $orig_filename   = basename( $orig_image_url );
    $new_filename    = basename( $new_image_url );
    
    // Find serialized data patterns and replace with correct string length
    $query = $wpdb->prepare(
      "SELECT post_id, meta_id, meta_value FROM $wpdb->postmeta 
       WHERE (meta_key = '_fl_builder_data' OR meta_key = '_fl_builder_draft')
       AND (meta_value LIKE %s OR meta_value LIKE %s)",
      '%' . $wpdb->esc_like( $full_orig_url ) . '%',
      '%' . $wpdb->esc_like( $orig_filename ) . '%'
    );
    
    $results = $wpdb->get_results( $query );
    
    if ( $results ) {

      foreach ( $results as $row ) {
        $meta_value = $row->meta_value;
        
        // Create a backup for revert operations
        $orig_meta = $meta_value;
        
        // Handle serialized strings with s:XX:"URL" pattern for full URLs
        $pattern = '/s:(\d+):"' . preg_quote( $full_orig_url, '/' ) . '"/';
        $meta_value = preg_replace_callback( $pattern, function( $matches ) use ( $full_new_url ) {
          // Calculate new string length
          $new_length = strlen( $full_new_url );
          return 's:' . $new_length . ':"' . $full_new_url . '"';
        }, $meta_value );
        
        // Handle serialized strings with s:XX:"filename.ext" pattern for just filenames
        $filename_pattern = '/s:(\d+):"' . preg_quote( $orig_filename, '/' ) . '"/';
        $meta_value = preg_replace_callback( $filename_pattern, function( $matches ) use ( $new_filename ) {
          // Calculate new string length
          $new_length = strlen( $new_filename );
          return 's:' . $new_length . ':"' . $new_filename . '"';
        }, $meta_value );

        
        // Update the meta value if changed
        if ( $meta_value !== $orig_meta ) {
          $wpdb->update(
            $wpdb->postmeta,
            array( 'meta_value' => $meta_value   ),
            array( 'meta_id'    => $row->meta_id )
          );
          
          // Log the change for full URL
          $revert_pattern = '/s:(\d+):"' . preg_quote( $full_new_url, '/' ) . '"/';
          $revert_meta = preg_replace_callback( $revert_pattern, function( $matches ) use ( $full_orig_url ) {
            $orig_length = strlen( $full_orig_url );
            return 's:' . $orig_length . ':"' . $full_orig_url . '"';
          }, $meta_value );
          
          // Log the change for filename
          $revert_filename_pattern = '/s:(\d+):"' . preg_quote( $new_filename, '/' ) . '"/';
          $revert_meta = preg_replace_callback( $revert_filename_pattern, function( $matches ) use ( $orig_filename ) {
            $orig_length = strlen( $orig_filename );
            return 's:' . $orig_length . ':"' . $orig_filename . '"';
          }, $revert_meta );
          
          
          $query = $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_value = %s WHERE meta_id = %d", 
                    $meta_value, $row->meta_id );
          $query_revert = $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_value = %s WHERE meta_id = %d", 
                           $revert_meta, $row->meta_id );
          
          $mfrh_core->log_sql( $query, $query_revert );
          $mfrh_core->log("Beaver Builder serialized data updated: Full URLs and filenames were replaced with correct string lengths.");
        }
      }
    }

    // Clear cache
    $uploads = wp_upload_dir();
    $cache = trailingslashit( $uploads['basedir'] ) . 'bb-plugin';
    if ( file_exists( $cache ) )
      Meow_MFRH_Core::rmdir_recursive( $cache );
    else {
      $cache = trailingslashit( $uploads['basedir'] ) . 'fl-builder';
      if ( file_exists( $cache ) )
        Meow_MFRH_Core::rmdir_recursive( $cache );
    }
  }

?>