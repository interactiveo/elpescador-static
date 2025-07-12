<?php

// VARIABLES
$enable_adventures = '1';
$enable_adventures_tax = '1';
$enable_adventures_widget = '0';
function global_var_adventures() {
	global $enable_adventures;
	global $enable_adventures_tax;
	global $enable_adventures_widget;
}
add_action( 'after_setup_theme', 'global_var_adventures' );

// POST TYPE
function sn_register_my_cpts_adventures() {
	$labels = [
    "name" => __( "Adventure", "blankslate-child" ),
    "singular_name" => __( "Adventure", "blankslate-child" ),
    "menu_name" => __( "Adventure", "blankslate-child" ),
    "all_items" => __( "All Adventures", "blankslate-child" ),
    "add_new" => __( "Add New", "blankslate-child" ),
    "add_new_item" => __( "Add New Adventure", "blankslate-child" ),
    "edit_item" => __( "Edit Adventure", "blankslate-child" ),
    "new_item" => __( "New Adventure", "blankslate-child" ),
    "view_item" => __( "View Adventure", "blankslate-child" ),
    "view_items" => __( "View Adventure", "blankslate-child" ),
    "search_items" => __( "Search Adventure", "blankslate-child" ),
    "not_found" => __( "No Adventure found", "blankslate-child" ),
    "not_found_in_trash" => __( "No Adventure found in trash", "blankslate-child" ),
    "parent" => __( "Parent Adventure:", "blankslate-child" ),
    "featured_image" => __( "Featured image for this Adventure", "blankslate-child" ),
    "set_featured_image" => __( "Set featured image for this Adventure", "blankslate-child" ),
    "remove_featured_image" => __( "Remove featured image for this Adventure", "blankslate-child" ),
    "use_featured_image" => __( "Use as featured image for this Adventure", "blankslate-child" ),
    "archives" => __( "Adventure archives", "blankslate-child" ),
    "insert_into_item" => __( "Insert into Adventure", "blankslate-child" ),
    "uploaded_to_this_item" => __( "Upload to this Adventure", "blankslate-child" ),
    "filter_items_list" => __( "Filter Adventure list", "blankslate-child" ),
    "items_list_navigation" => __( "Adventure list navigation", "blankslate-child" ),
    "items_list" => __( "Adventure list", "blankslate-child" ),
    "attributes" => __( "Adventure attributes", "blankslate-child" ),
    "name_admin_bar" => __( "Adventure", "blankslate-child" ),
    "item_published" => __( "Adventure published", "blankslate-child" ),
    "item_published_privately" => __( "Adventure published privately.", "blankslate-child" ),
    "item_reverted_to_draft" => __( "Adventure reverted to draft.", "blankslate-child" ),
    "item_scheduled" => __( "Adventure scheduled", "blankslate-child" ),
    "item_updated" => __( "Adventure updated.", "blankslate-child" ),
    "parent_item_colon" => __( "Parent Adventure:", "blankslate-child" ),
  ];

	$args = [
		"label" => __( "Adventure", "blankslate-child" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
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
		"rewrite" => [ "slug" => "adventures", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "thumbnail", "excerpt", "editor", "revisions" ],
		"show_in_graphql" => false,
	];
	register_post_type( "adventures", $args );
}
if($enable_adventures == '1'):
add_action( 'init', 'sn_register_my_cpts_adventures' );
endif;


// TAXONOMY for Adventure Categories
function sn_register_my_tax_adventure_categories() {
	$labels = [
		"name" => __( "Adventure Categories", "blankslate-child" ),
		"singular_name" => __( "Adventure Category", "blankslate-child" ),
	];
	$args = [
		"label" => __( "Adventure Categories", "blankslate-child" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => false,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "adventures_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "adventures_category", [ "adventures" ], $args );
}

// TAXONOMY for Adventure Subcategories
function sn_register_my_tax_adventure_subcategories() {
	$labels = [
		"name" => __( "Adventure Subcategories", "blankslate-child" ),
		"singular_name" => __( "Adventure Subcategory", "blankslate-child" ),
	];
	$args = [
		"label" => __( "Adventure Subcategories", "blankslate-child" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => false,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "adventures_subcategory",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "adventures_subcategory", [ "adventures" ], $args );
}

if ( $enable_adventures_tax == '1' ):
	add_action( 'init', 'sn_register_my_tax_adventure_categories' );
	add_action( 'init', 'sn_register_my_tax_adventure_subcategories' );
endif;


// SIDE BAR
function sn_register_widget_adventures() {
    register_sidebar( array(
        'name'          => __( 'Adventure', 'textdomain' ),
        'id'            => 'adventures-widget',
        'description'   => __( '' ),
        'before_widget' => '<div id="%1$s" class="widget adventures-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6 class="widget-title">',
        'after_title'   => '</h6>',
    ) );
}
if($enable_adventures_widget == '1'):
add_action( 'widgets_init', 'sn_register_widget_adventures' );
endif;


// ACF 





?>
