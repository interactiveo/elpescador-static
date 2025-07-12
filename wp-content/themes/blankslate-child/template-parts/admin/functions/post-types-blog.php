<?php

// VARIABLES
$enable_blog = '1';
$enable_blog_tax = '1';
$enable_blog_widget = '1';
function global_var_blog() {
	global $enable_blog;
	global $enable_blog_tax;
	global $enable_blog_widget;
}
add_action( 'after_setup_theme', 'global_var_blog' );

// POST TYPE
function sn_register_my_cpts_blog() {
	$labels = [
    "name" => __( "Fishing Reports", "blankslate-child" ),
    "singular_name" => __( "Fishing Report", "blankslate-child" ),
    "menu_name" => __( "Fishing Reports", "blankslate-child" ),
    "all_items" => __( "All Fishing Reports", "blankslate-child" ),
    "add_new" => __( "Add New", "blankslate-child" ),
    "add_new_item" => __( "Add New Fishing Report", "blankslate-child" ),
    "edit_item" => __( "Edit Fishing Reports", "blankslate-child" ),
    "new_item" => __( "New Fishing Reports", "blankslate-child" ),
    "view_item" => __( "View Fishing Reports", "blankslate-child" ),
    "view_items" => __( "View Fishing Reports", "blankslate-child" ),
    "search_items" => __( "Search Fishing Reports", "blankslate-child" ),
    "not_found" => __( "No Fishing Reports found", "blankslate-child" ),
    "not_found_in_trash" => __( "No Fishing Reports found in trash", "blankslate-child" ),
    "parent" => __( "Parent Fishing Reports:", "blankslate-child" ),
    "featured_image" => __( "Featured image for this Fishing Reports", "blankslate-child" ),
    "set_featured_image" => __( "Set featured image for this Fishing Reports", "blankslate-child" ),
    "remove_featured_image" => __( "Remove featured image for this Fishing Reports", "blankslate-child" ),
    "use_featured_image" => __( "Use as featured image for this Fishing Reports", "blankslate-child" ),
    "archives" => __( "Fishing Reports archives", "blankslate-child" ),
    "insert_into_item" => __( "Insert into Fishing Reports", "blankslate-child" ),
    "uploaded_to_this_item" => __( "Upload to this Fishing Reports", "blankslate-child" ),
    "filter_items_list" => __( "Filter Fishing Reports list", "blankslate-child" ),
    "items_list_navigation" => __( "Fishing Reports list navigation", "blankslate-child" ),
    "items_list" => __( "Fishing Reports list", "blankslate-child" ),
    "attributes" => __( "Fishing Reports attributes", "blankslate-child" ),
    "name_admin_bar" => __( "Fishing Reports", "blankslate-child" ),
    "item_published" => __( "Fishing Reports published", "blankslate-child" ),
    "item_published_privately" => __( "Fishing Reports published privately.", "blankslate-child" ),
    "item_reverted_to_draft" => __( "Fishing Reports reverted to draft.", "blankslate-child" ),
    "item_scheduled" => __( "Fishing Reports scheduled", "blankslate-child" ),
    "item_updated" => __( "Fishing Reports updated.", "blankslate-child" ),
    "parent_item_colon" => __( "Parent Fishing Reports:", "blankslate-child" ),
  ];

	$args = [
		"label" => __( "Fishing Reports", "blankslate-child" ),
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
		"rewrite" => [ "slug" => "blog", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "excerpt", "revisions" ],
		"show_in_graphql" => false,
	];
	register_post_type( "blog", $args );
}
if($enable_blog == '1'):
add_action( 'init', 'sn_register_my_cpts_blog' );
endif;


// TAXONOMY
function sn_register_my_tax_blog() {
	$labels = [
		"name" => __( "Fishing Reports Category", "blankslate-child" ),
		"singular_name" => __( "Fishing Reports Categories", "blankslate-child" ),
	];
	$args = [
		"label" => __( "Fishing Reports Category", "blankslate-child" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		//"rewrite" => [ 'slug' => 'blog-category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "blog_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "blog_category", [ "blog" ], $args );
}
if($enable_blog_tax == '1'):
add_action( 'init', 'sn_register_my_tax_blog' );
endif;


// SIDE BAR
function sn_register_widget_blog() {
    register_sidebar( array(
        'name'          => __( 'Fishing Reports', 'textdomain' ),
        'id'            => 'blog-widget',
        'description'   => __( '' ),
        'before_widget' => '<div id="%1$s" class="widget blog-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6 class="widget-title">',
        'after_title'   => '</h6>',
    ) );
}
if($enable_blog_widget == '1'):
add_action( 'widgets_init', 'sn_register_widget_blog' );
endif;


// ACF 
if($enable_blog == '1'):
	if( function_exists('acf_add_local_field_group') ):
	acf_add_local_field_group(array(
		'key' => 'group_62fbcad6182fb',
		'title' => 'Fishing Reports Settings',
		'fields' => array(
			array(
				'key' => 'field_62fbcb5547716',
				'label' => 'Fishing Reports Post Sidebar Settings',
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
				'key' => 'field_62fbcae7edc85',
				'label' => 'Show Sidebar',
				'name' => 'show_blog_sidebar',
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
				'key' => 'field_62fbce8c389dd',
				'label' => '',
				'name' => '',
				'aria-label' => '',
				'type' => 'message',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_62fbcae7edc85',
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
				'message' => 'Fishing Reports sidebar widgets can be <a href="/wp-admin/widgets.php#blog-widget" target="_blank">adjusted here</a>.',
				'new_lines' => 'wpautop',
				'esc_html' => 0,
			),
			array(
				'key' => 'field_62fbcbad23411',
				'label' => 'Fishing Reports Post Settings',
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
				'key' => 'field_62fbcbb623412',
				'label' => 'Show Date',
				'name' => 'show_blog_date',
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
				'key' => 'field_62fbcbc623413',
				'label' => 'Show Featured Image',
				'name' => 'show_blog_featured_image',
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
					'value' => 'acf-options-blog',
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
	
	if( function_exists('acf_add_local_field_group') ):
		acf_add_local_field_group(array(
			'key' => 'group_62e96993bc0f2',
			'title' => 'Fishing Reports Fields',
			'fields' => array(
				array(
					'key' => 'field_62e96997f9891',
					'label' => 'Fishing Reports Category',
					'name' => 'blog_category',
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
					'taxonomy' => 'blog_category',
					'field_type' => 'checkbox',
					'add_term' => 1,
					'save_terms' => 1,
					'load_terms' => 1,
					'return_format' => 'id',
					'multiple' => 0,
					'allow_null' => 0,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'blog',
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
