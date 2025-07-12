<?php

// VARIABLES
$enable_faq = '1';
$enable_faq_tax = '1';
$enable_faq_widget = '0';
function global_var_faq() {
	global $enable_faq;
	global $enable_faq_tax;
	global $enable_faq_widget;
}
add_action( 'after_setup_theme', 'global_var_faq' );

// POST TYPE
function sn_register_my_cpts_faq() {
  $labels = [
    "name" => __( "FAQ", "blankslate-child" ),
    "singular_name" => __( "FAQ", "blankslate-child" ),
    "menu_name" => __( "FAQ", "blankslate-child" ),
    "all_items" => __( "All FAQ", "blankslate-child" ),
    "add_new" => __( "Add New", "blankslate-child" ),
    "add_new_item" => __( "Add New FAQ", "blankslate-child" ),
    "edit_item" => __( "Edit FAQ", "blankslate-child" ),
    "new_item" => __( "New FAQ", "blankslate-child" ),
    "view_item" => __( "View FAQ", "blankslate-child" ),
    "view_items" => __( "View FAQ", "blankslate-child" ),
    "search_items" => __( "Search FAQ", "blankslate-child" ),
    "not_found" => __( "No FAQ found", "blankslate-child" ),
    "not_found_in_trash" => __( "No FAQ found in trash", "blankslate-child" ),
    "parent" => __( "Parent FAQ:", "blankslate-child" ),
    "featured_image" => __( "Featured image for this FAQ", "blankslate-child" ),
    "set_featured_image" => __( "Set featured image for this FAQ", "blankslate-child" ),
    "remove_featured_image" => __( "Remove featured image for this FAQ", "blankslate-child" ),
    "use_featured_image" => __( "Use as featured image for this FAQ", "blankslate-child" ),
    "archives" => __( "FAQ archives", "blankslate-child" ),
    "insert_into_item" => __( "Insert into FAQ", "blankslate-child" ),
    "uploaded_to_this_item" => __( "Upload to this FAQ", "blankslate-child" ),
    "filter_items_list" => __( "Filter FAQ list", "blankslate-child" ),
    "items_list_navigation" => __( "FAQ list navigation", "blankslate-child" ),
    "items_list" => __( "FAQ list", "blankslate-child" ),
    "attributes" => __( "FAQ attributes", "blankslate-child" ),
    "name_admin_bar" => __( "FAQ", "blankslate-child" ),
    "item_published" => __( "FAQ published", "blankslate-child" ),
    "item_published_privately" => __( "FAQ published privately.", "blankslate-child" ),
    "item_reverted_to_draft" => __( "FAQ reverted to draft.", "blankslate-child" ),
    "item_scheduled" => __( "FAQ scheduled", "blankslate-child" ),
    "item_updated" => __( "FAQ updated.", "blankslate-child" ),
    "parent_item_colon" => __( "Parent FAQ:", "blankslate-child" ),
  ];
	$args = [
		"label" => __( "FAQ", "blankslate-child" ),
    "labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => false,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "faq", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "revisions" ],
		"show_in_graphql" => false,
	];
	register_post_type( "faq", $args );
}
if($enable_faq == '1'):
add_action( 'init', 'sn_register_my_cpts_faq' );
endif;


// TAXONOMY
function sn_register_my_tax_faq() {
	$labels = [
		"name" => __( "FAQ Category", "blankslate-child" ),
		"singular_name" => __( "FAQ Categories", "blankslate-child" ),
	];
	$args = [
		"label" => __( "FAQ Category", "blankslate-child" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => false,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		//"rewrite" => [ 'slug' => 'faq-category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "faq_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "faq_category", [ "faq" ], $args );
}
if($enable_faq_tax == '1'):
add_action( 'init', 'sn_register_my_tax_faq' );
endif;


// SIDE BAR
function sn_register_widget_faq() {
    register_sidebar( array(
        'name'          => __( 'FAQ', 'textdomain' ),
        'id'            => 'faq-widget',
        'description'   => __( '' ),
        'before_widget' => '<div id="%1$s" class="widget faq-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6 class="widget-title">',
        'after_title'   => '</h6>',
    ) );
}
if($enable_faq_widget == '1'):
add_action( 'widgets_init', 'sn_register_widget_faq' );
endif;


?>
