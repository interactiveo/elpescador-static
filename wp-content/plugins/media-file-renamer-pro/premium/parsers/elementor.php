<?php

add_action( 'mfrh_media_renamed', 'mfrh_media_updates_elementor', 10, 3 );

function mfrh_media_updates_elementor( $post, $old_filepath, $new_filepath ) {
    // Extract the relative path from the old and new file paths
    $upload_dir = wp_upload_dir();
    $relative_old_filepath = str_replace( $upload_dir['basedir'], '', $old_filepath );
    $relative_new_filepath = str_replace( $upload_dir['basedir'], '', $new_filepath );

    // Ensure the relative paths start with a slash
    if ( substr( $relative_old_filepath, 0, 1 ) !== '/' ) {
        $relative_old_filepath = '/' . $relative_old_filepath;
    }
    if ( substr( $relative_new_filepath, 0, 1 ) !== '/' ) {
        $relative_new_filepath = '/' . $relative_new_filepath;
    }

    // Escape slashes in the relative file paths because Elementor data is serialized
    $escaped_relative_old_filepath = addcslashes( $relative_old_filepath, '/_' );
    $escaped_relative_new_filepath = addcslashes( $relative_new_filepath, '/_' );

    // Get all the post meta where meta value contains the old filepath
    // and meta key is in _elementor_data or _elementor_element_cache
    global $wpdb;

    $res = $wpdb->get_results( $wpdb->prepare(
        "SELECT meta_id, meta_value FROM $wpdb->postmeta 
         WHERE meta_value LIKE %s 
         AND meta_key IN ('_elementor_data', '_elementor_element_cache')",
        '%' . $wpdb->esc_like( $escaped_relative_old_filepath ) . '%'
    ));

    // Update each meta entry
    foreach ( $res as $row ) {
        $data = maybe_unserialize( $row->meta_value );
        $new_data = mfrh_update_elementor_data( $data, $escaped_relative_old_filepath, $escaped_relative_new_filepath );
        $wpdb->update( $wpdb->postmeta, array( 'meta_value' => maybe_serialize( $new_data ) ), array( 'meta_id' => $row->meta_id ) );
    }

    // After updating references, reset Elementor CSS cache
    mfrh_elementor_reset_css();
}

function mfrh_update_elementor_data( $data, $relative_old_filepath, $relative_new_filepath ) {
    // Recursively update serialized Elementor data
    if ( is_array( $data ) ) {
        foreach ( $data as $key => $value ) {
            if ( is_array( $value ) ) {
                $data[$key] = mfrh_update_elementor_data( $value, $relative_old_filepath, $relative_new_filepath );
            } elseif ( is_string( $value ) ) {
                $data[$key] = str_replace( $relative_old_filepath, $relative_new_filepath, $value );
            }
        }
    } elseif ( is_string( $data ) ) {
        $data = str_replace( $relative_old_filepath, $relative_new_filepath, $data );
    }

    return $data;
}

function mfrh_elementor_reset_css() {
    global $wpdb;
    // Delete all _elementor_css entries to force regeneration
    $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = '_elementor_css'" );
}

?>