<?php

// VARIABLES
$enable_testimonial = '1';
$enable_testimonial_tax = '0';
$enable_testimonial_widget = '0';
function global_var_testimonial() {
	global $enable_testimonial;
	global $enable_testimonial_tax;
	global $enable_testimonial_widget;
}
add_action( 'after_setup_theme', 'global_var_testimonial' );

// POST TYPE
function sn_register_my_cpts_testimonial() {
  $labels = [
    "name" => __( "Testimonial", "blankslate-child" ),
    "singular_name" => __( "Testimonial", "blankslate-child" ),
    "menu_name" => __( "Testimonial", "blankslate-child" ),
    "all_items" => __( "All Testimonial", "blankslate-child" ),
    "add_new" => __( "Add New", "blankslate-child" ),
    "add_new_item" => __( "Add New Testimonial", "blankslate-child" ),
    "edit_item" => __( "Edit Testimonial", "blankslate-child" ),
    "new_item" => __( "New Testimonial", "blankslate-child" ),
    "view_item" => __( "View Testimonial", "blankslate-child" ),
    "view_items" => __( "View Testimonial", "blankslate-child" ),
    "search_items" => __( "Search Testimonial", "blankslate-child" ),
    "not_found" => __( "No Testimonial found", "blankslate-child" ),
    "not_found_in_trash" => __( "No Testimonial found in trash", "blankslate-child" ),
    "parent" => __( "Parent Testimonial:", "blankslate-child" ),
    "featured_image" => __( "Featured image for this Testimonial", "blankslate-child" ),
    "set_featured_image" => __( "Set featured image for this Testimonial", "blankslate-child" ),
    "remove_featured_image" => __( "Remove featured image for this Testimonial", "blankslate-child" ),
    "use_featured_image" => __( "Use as featured image for this Testimonial", "blankslate-child" ),
    "archives" => __( "Testimonial archives", "blankslate-child" ),
    "insert_into_item" => __( "Insert into Testimonial", "blankslate-child" ),
    "uploaded_to_this_item" => __( "Upload to this Testimonial", "blankslate-child" ),
    "filter_items_list" => __( "Filter Testimonial list", "blankslate-child" ),
    "items_list_navigation" => __( "Testimonial list navigation", "blankslate-child" ),
    "items_list" => __( "Testimonial list", "blankslate-child" ),
    "attributes" => __( "Testimonial attributes", "blankslate-child" ),
    "name_admin_bar" => __( "Testimonial", "blankslate-child" ),
    "item_published" => __( "Testimonial published", "blankslate-child" ),
    "item_published_privately" => __( "Testimonial published privately.", "blankslate-child" ),
    "item_reverted_to_draft" => __( "Testimonial reverted to draft.", "blankslate-child" ),
    "item_scheduled" => __( "Testimonial scheduled", "blankslate-child" ),
    "item_updated" => __( "Testimonial updated.", "blankslate-child" ),
    "parent_item_colon" => __( "Parent Testimonial:", "blankslate-child" ),
  ];
	$args = [
		"label" => __( "Testimonial", "blankslate-child" ),
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
		"rewrite" => [ "slug" => "testimonials", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "excerpt", "revisions" ],
		"show_in_graphql" => false,
	];
	register_post_type( "testimonial", $args );
}
if($enable_testimonial == '1'):
add_action( 'init', 'sn_register_my_cpts_testimonial' );
endif;


// TAXONOMY
function sn_register_my_tax_testimonial() {
	$labels = [
		"name" => __( "Testimonial Category", "blankslate-child" ),
		"singular_name" => __( "Testimonial Categories", "blankslate-child" ),
	];
	$args = [
		"label" => __( "Testimonial Category", "blankslate-child" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => false,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		//"rewrite" => [ 'slug' => 'testimonial-category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "testimonial_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "testimonial_category", [ "testimonial" ], $args );
}
if($enable_testimonial_tax == '1'):
add_action( 'init', 'sn_register_my_tax_testimonial' );
endif;


// SIDE BAR
function sn_register_widget_testimonial() {
    register_sidebar( array(
        'name'          => __( 'Testimonial', 'textdomain' ),
        'id'            => 'testimonial-widget',
        'description'   => __( '' ),
        'before_widget' => '<div id="%1$s" class="widget testimonial-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6 class="widget-title">',
        'after_title'   => '</h6>',
    ) );
}
if($enable_testimonial_widget == '1'):
add_action( 'widgets_init', 'sn_register_widget_testimonial' );
endif;


// ACF
if($enable_testimonial == '1'):
	if( function_exists('acf_add_local_field_group') ):
	acf_add_local_field_group(array(
		'key' => 'group_62f1110b1f79a',
		'title' => 'Testimonial Fields',
		'fields' => array(
			array(
				'key' => 'field_62f1111c722f3',
				'label' => 'Testimonial Author',
				'name' => 'testimonial_author',
				'aria-label' => '',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_62f11123722f4',
				'label' => 'Testimonial Company',
				'name' => 'testimonial_company',
				'aria-label' => '',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'testimonial',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'acf_after_title',
		'style' => 'seamless',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'show_in_rest' => 0,
	));
	
	endif;		
endif;		


?>
