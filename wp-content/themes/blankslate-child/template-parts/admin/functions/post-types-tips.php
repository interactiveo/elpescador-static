<?php

// VARIABLES
$enable_tips = '1';
$enable_tips_tax = '0';
$enable_tips_widget = '0';
function global_var_tips() {
	global $enable_tips;
	global $enable_tips_tax;
	global $enable_tips_widget;
}
add_action( 'after_setup_theme', 'global_var_tips' );

// POST TYPE
function sn_register_my_cpts_tips() {
	$labels = [
    "name" => __( "Tips", "blankslate-child" ),
    "singular_name" => __( "Tips", "blankslate-child" ),
    "menu_name" => __( "Tips", "blankslate-child" ),
    "all_items" => __( "All Tips", "blankslate-child" ),
    "add_new" => __( "Add New", "blankslate-child" ),
    "add_new_item" => __( "Add New Tips", "blankslate-child" ),
    "edit_item" => __( "Edit Tips", "blankslate-child" ),
    "new_item" => __( "New Tips", "blankslate-child" ),
    "view_item" => __( "View Tips", "blankslate-child" ),
    "view_items" => __( "View Tips", "blankslate-child" ),
    "search_items" => __( "Search Tips", "blankslate-child" ),
    "not_found" => __( "No Tips found", "blankslate-child" ),
    "not_found_in_trash" => __( "No Tips found in trash", "blankslate-child" ),
    "parent" => __( "Parent Tips:", "blankslate-child" ),
    "featured_image" => __( "Featured image for this Tips", "blankslate-child" ),
    "set_featured_image" => __( "Set featured image for this Tips", "blankslate-child" ),
    "remove_featured_image" => __( "Remove featured image for this Tips", "blankslate-child" ),
    "use_featured_image" => __( "Use as featured image for this Tips", "blankslate-child" ),
    "archives" => __( "Tips archives", "blankslate-child" ),
    "insert_into_item" => __( "Insert into Tips", "blankslate-child" ),
    "uploaded_to_this_item" => __( "Upload to this Tips", "blankslate-child" ),
    "filter_items_list" => __( "Filter Tips list", "blankslate-child" ),
    "items_list_navigation" => __( "Tips list navigation", "blankslate-child" ),
    "items_list" => __( "Tips list", "blankslate-child" ),
    "attributes" => __( "Tips attributes", "blankslate-child" ),
    "name_admin_bar" => __( "Tips", "blankslate-child" ),
    "item_published" => __( "Tips published", "blankslate-child" ),
    "item_published_privately" => __( "Tips published privately.", "blankslate-child" ),
    "item_reverted_to_draft" => __( "Tips reverted to draft.", "blankslate-child" ),
    "item_scheduled" => __( "Tips scheduled", "blankslate-child" ),
    "item_updated" => __( "Tips updated.", "blankslate-child" ),
    "parent_item_colon" => __( "Parent Tips:", "blankslate-child" ),
  ];
	$args = [
		"label" => __( "Tips", "blankslate-child" ),
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
		"rewrite" => [ "slug" => "tips", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "excerpt", "revisions" ],
		"show_in_graphql" => false,
	];
	register_post_type( "tips", $args );
}
if($enable_tips == '1'):
add_action( 'init', 'sn_register_my_cpts_tips' );
endif;


// TAXONOMY
function sn_register_my_tax_tips() {
	$labels = [
		"name" => __( "Tips Category", "blankslate-child" ),
		"singular_name" => __( "Tips Categories", "blankslate-child" ),
	];
	$args = [
		"label" => __( "Tips Category", "blankslate-child" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		//"rewrite" => [ 'slug' => 'tips-category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "tips_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "tips_category", [ "tips" ], $args );
}
if($enable_tips_tax == '1'):
add_action( 'init', 'sn_register_my_tax_tips' );
endif;


// SIDE BAR
function sn_register_widget_tips() {
    register_sidebar( array(
        'name'          => __( 'Tips', 'textdomain' ),
        'id'            => 'tips-widget',
        'description'   => __( '' ),
        'before_widget' => '<div id="%1$s" class="widget tips-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6 class="widget-title">',
        'after_title'   => '</h6>',
    ) );
}
if($enable_tips_widget == '1'):
add_action( 'widgets_init', 'sn_register_widget_tips' );
endif;


// ACF
if($enable_tips == '1'):
	if( function_exists('acf_add_local_field_group') ):
	acf_add_local_field_group(array(
		'key' => 'group_63a32267a4deb',
		'title' => 'Tips Fields',
		'fields' => array(
			array(
				'key' => 'field_63a3226763293',
				'label' => 'Tips Category',
				'name' => 'tips_category',
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
				'taxonomy' => 'tips_category',
				'add_term' => 1,
				'save_terms' => 1,
				'load_terms' => 1,
				'return_format' => 'id',
				'field_type' => 'checkbox',
				'multiple' => 0,
				'allow_null' => 0,
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'tips',
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
		'key' => 'group_63a1ec1fc1e4b',
		'title' => 'Tips Settings',
		'fields' => array(
			array(
				'key' => 'field_63a1ec1fc42b8',
				'label' => 'Tips Post Sidebar Settings',
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
				'key' => 'field_63a1ec1fc42ff',
				'label' => 'Show Sidebar',
				'name' => 'show_tips_sidebar',
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
				'ui_on_text' => '',
				'ui_off_text' => '',
				'ui' => 1,
			),
			array(
				'key' => 'field_63a1ec1fc4352',
				'label' => '',
				'name' => '',
				'aria-label' => '',
				'type' => 'message',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_63a1ec1fc42ff',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => 'Tips sidebar widgets can be <a href="/wp-admin/widgets.php#tips-widget" target="_blank">adjusted here</a>.',
				'new_lines' => 'wpautop',
				'esc_html' => 0,
			),
			array(
				'key' => 'field_63a1ec1fc43a9',
				'label' => 'Tips Post Settings',
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
				'key' => 'field_63a1ec1fc43f0',
				'label' => 'Show Date',
				'name' => 'show_tips_date',
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
				'ui_on_text' => '',
				'ui_off_text' => '',
				'ui' => 1,
			),
			array(
				'key' => 'field_63a1ec1fc4434',
				'label' => 'Show Featured Image',
				'name' => 'show_tips_featured_image',
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
				'ui_on_text' => '',
				'ui_off_text' => '',
				'ui' => 1,
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'acf-options-tips',
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
