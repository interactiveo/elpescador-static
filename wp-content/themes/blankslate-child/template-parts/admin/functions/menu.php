<?php
function sn_custom_new_menu() {
  register_nav_menus(
    array(
      'footer-menu' => __( 'Footer Menu' )
    )
  );
}
add_action( 'init', 'sn_custom_new_menu' );
?>
