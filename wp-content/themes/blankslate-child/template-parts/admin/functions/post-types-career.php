<?php

// VARIABLES
$enable_careers = '0';
$enable_careers_tax = '0';
$enable_careers_widget = '0';
function global_var_career() {
	global $enable_careers;
	global $enable_careers_tax;
	global $enable_careers_widget;
}
add_action( 'after_setup_theme', 'global_var_career' );

// POST TYPE
function sn_register_my_cpts_career() {
  $labels = [
    "name" => __( "Careers", "blankslate-child" ),
    "singular_name" => __( "Career", "blankslate-child" ),
    "menu_name" => __( "Careers", "blankslate-child" ),
    "all_items" => __( "All Careers", "blankslate-child" ),
    "add_new" => __( "Add New", "blankslate-child" ),
    "add_new_item" => __( "Add New Career", "blankslate-child" ),
    "edit_item" => __( "Edit Career", "blankslate-child" ),
    "new_item" => __( "New Career", "blankslate-child" ),
    "view_item" => __( "View Career", "blankslate-child" ),
    "view_items" => __( "View Careers", "blankslate-child" ),
    "search_items" => __( "Search Careers", "blankslate-child" ),
    "not_found" => __( "No Careers found", "blankslate-child" ),
    "not_found_in_trash" => __( "No Careers found in trash", "blankslate-child" ),
    "parent" => __( "Parent Career:", "blankslate-child" ),
    "featured_image" => __( "Featured image for this Career", "blankslate-child" ),
    "set_featured_image" => __( "Set featured image for this Career", "blankslate-child" ),
    "remove_featured_image" => __( "Remove featured image for this Career", "blankslate-child" ),
    "use_featured_image" => __( "Use as featured image for this Career", "blankslate-child" ),
    "archives" => __( "Career archives", "blankslate-child" ),
    "insert_into_item" => __( "Insert into Career", "blankslate-child" ),
    "uploaded_to_this_item" => __( "Upload to this Career", "blankslate-child" ),
    "filter_items_list" => __( "Filter Careers list", "blankslate-child" ),
    "items_list_navigation" => __( "Careers list navigation", "blankslate-child" ),
    "items_list" => __( "Careers list", "blankslate-child" ),
    "attributes" => __( "Careers attributes", "blankslate-child" ),
    "name_admin_bar" => __( "Career", "blankslate-child" ),
    "item_published" => __( "Career published", "blankslate-child" ),
    "item_published_privately" => __( "Career published privately.", "blankslate-child" ),
    "item_reverted_to_draft" => __( "Career reverted to draft.", "blankslate-child" ),
    "item_scheduled" => __( "Career scheduled", "blankslate-child" ),
    "item_updated" => __( "Career updated.", "blankslate-child" ),
    "parent_item_colon" => __( "Parent Career:", "blankslate-child" ),
  ];
	$args = [
		"label" => __( "Careers", "blankslate-child" ),
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
		"rewrite" => [ "slug" => "career", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "excerpt", "revisions" ],
		"show_in_graphql" => false,
	];
	register_post_type( "career", $args );
}
if($enable_careers == '1'):
add_action( 'init', 'sn_register_my_cpts_career' );
endif;


// TAXONOMY
function sn_register_my_tax_career() {
	$labels = [
		"name" => __( "Career Category", "blankslate-child" ),
		"singular_name" => __( "Career Categories", "blankslate-child" ),
	];
	$args = [
		"label" => __( "Career Category", "blankslate-child" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		//"rewrite" => [ 'slug' => 'career-category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "career_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "career_category", [ "career" ], $args );
}
if($enable_careers_tax == '1'):
add_action( 'init', 'sn_register_my_tax_career' );
endif;


// SIDE BAR
function sn_register_widget_career() {
    register_sidebar( array(
        'name'          => __( 'Careers', 'textdomain' ),
        'id'            => 'career-widget',
        'description'   => __( '' ),
        'before_widget' => '<div id="%1$s" class="widget career-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6 class="widget-title">',
        'after_title'   => '</h6>',
    ) );
}
if($enable_careers_widget == '1'):
add_action( 'widgets_init', 'sn_register_widget_career' );
endif;


// ACF
if($enable_careers == '1'):
	if( function_exists('acf_add_local_field_group') ):
	acf_add_local_field_group(array(
		'key' => 'group_62ea68c853141',
		'title' => 'Career Fields',
		'fields' => array(
			array(
				'key' => 'field_62ea68ff47b20',
				'label' => 'Career Category',
				'name' => 'career_category',
				'aria-label' => '',
				'type' => 'taxonomy',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'taxonomy' => 'career_category',
				'field_type' => 'checkbox',
				'add_term' => 1,
				'save_terms' => 1,
				'load_terms' => 1,
				'return_format' => 'id',
				'multiple' => 0,
				'allow_null' => 0,
			),
			array(
				'key' => 'field_62ea6b816743a',
				'label' => 'Custom Career Application URL',
				'name' => 'custom_career_application_url',
				'aria-label' => '',
				'type' => 'link',
				'instructions' => 'If left blank. Will link to <a href="/wp-admin/admin.php?page=acf-options-careers" target="_blank">default careers application</a> page.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'array',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'career',
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
	
	acf_add_local_field_group(array(
		'key' => 'group_62ea6c7f5b98f',
		'title' => 'Career Settings',
		'fields' => array(
			array(
				'key' => 'field_62ea6c8da569b',
				'label' => 'Default Career Application Page',
				'name' => 'default_career_application_page',
				'aria-label' => '',
				'type' => 'link',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'array',
			),
			array(
				'key' => 'field_62fbcf9fc35ea',
				'label' => 'Career Post Settings',
				'name' => '',
				'aria-label' => '',
				'type' => 'accordion',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'open' => 0,
				'multi_expand' => 0,
				'endpoint' => 0,
			),
			array(
				'key' => 'field_62fbcfbcc35eb',
				'label' => 'Show Featured Image',
				'name' => 'show_career_featured_image',
				'aria-label' => '',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_62fbcfe9c35ec',
				'label' => 'Show Apply Button',
				'name' => 'show_career_apply_button',
				'aria-label' => '',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'acf-options-careers',
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
