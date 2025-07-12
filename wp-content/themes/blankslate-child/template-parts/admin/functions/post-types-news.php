<?php

// VARIABLES
$enable_news = '1';
$enable_news_tax = '0';
$enable_news_widget = '0';
function global_var_news() {
	global $enable_news;
	global $enable_news_tax;
	global $enable_news_widget;
}
add_action( 'after_setup_theme', 'global_var_news' );

// POST TYPE
function sn_register_my_cpts_news() {
	$labels = [
    "name" => __( "Articles", "blankslate-child" ),
    "singular_name" => __( "Articles", "blankslate-child" ),
    "menu_name" => __( "Articles", "blankslate-child" ),
    "all_items" => __( "All Articles", "blankslate-child" ),
    "add_new" => __( "Add New", "blankslate-child" ),
    "add_new_item" => __( "Add New Articles", "blankslate-child" ),
    "edit_item" => __( "Edit Articles", "blankslate-child" ),
    "new_item" => __( "New Articles", "blankslate-child" ),
    "view_item" => __( "View Articles", "blankslate-child" ),
    "view_items" => __( "View Articles", "blankslate-child" ),
    "search_items" => __( "Search Articles", "blankslate-child" ),
    "not_found" => __( "No Articles found", "blankslate-child" ),
    "not_found_in_trash" => __( "No Articles found in trash", "blankslate-child" ),
    "parent" => __( "Parent Articles:", "blankslate-child" ),
    "featured_image" => __( "Featured image for this Articles", "blankslate-child" ),
    "set_featured_image" => __( "Set featured image for this Articles", "blankslate-child" ),
    "remove_featured_image" => __( "Remove featured image for this Articles", "blankslate-child" ),
    "use_featured_image" => __( "Use as featured image for this Articles", "blankslate-child" ),
    "archives" => __( "Articles archives", "blankslate-child" ),
    "insert_into_item" => __( "Insert into Articles", "blankslate-child" ),
    "uploaded_to_this_item" => __( "Upload to this Articles", "blankslate-child" ),
    "filter_items_list" => __( "Filter Articles list", "blankslate-child" ),
    "items_list_navigation" => __( "Articles list navigation", "blankslate-child" ),
    "items_list" => __( "Articles list", "blankslate-child" ),
    "attributes" => __( "Articles attributes", "blankslate-child" ),
    "name_admin_bar" => __( "Articles", "blankslate-child" ),
    "item_published" => __( "Articles published", "blankslate-child" ),
    "item_published_privately" => __( "Articles published privately.", "blankslate-child" ),
    "item_reverted_to_draft" => __( "Articles reverted to draft.", "blankslate-child" ),
    "item_scheduled" => __( "Articles scheduled", "blankslate-child" ),
    "item_updated" => __( "Articles updated.", "blankslate-child" ),
    "parent_item_colon" => __( "Parent Articles:", "blankslate-child" ),
  ];
	$args = [
		"label" => __( "Articles", "blankslate-child" ),
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
		"rewrite" => [ "slug" => "news", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "excerpt", "revisions" ],
		"show_in_graphql" => false,
	];
	register_post_type( "news", $args );
}
if($enable_news == '1'):
add_action( 'init', 'sn_register_my_cpts_news' );
endif;


// TAXONOMY
function sn_register_my_tax_news() {
	$labels = [
		"name" => __( "Articles Category", "blankslate-child" ),
		"singular_name" => __( "Articles Categories", "blankslate-child" ),
	];
	$args = [
		"label" => __( "Articles Category", "blankslate-child" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		//"rewrite" => [ 'slug' => 'news-category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "news_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "news_category", [ "news" ], $args );
}
if($enable_news_tax == '1'):
add_action( 'init', 'sn_register_my_tax_news' );
endif;


// SIDE BAR
function sn_register_widget_news() {
    register_sidebar( array(
        'name'          => __( 'Articles', 'textdomain' ),
        'id'            => 'news-widget',
        'description'   => __( '' ),
        'before_widget' => '<div id="%1$s" class="widget news-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6 class="widget-title">',
        'after_title'   => '</h6>',
    ) );
}
if($enable_news_widget == '1'):
add_action( 'widgets_init', 'sn_register_widget_news' );
endif;


// ACF
if($enable_news == '1'):
	if( function_exists('acf_add_local_field_group') ):
	acf_add_local_field_group(array(
		'key' => 'group_63a32267a4deb',
		'title' => 'Articles Fields',
		'fields' => array(
			array(
				'key' => 'field_63a3226763293',
				'label' => 'Articles Category',
				'name' => 'news_category',
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
				'taxonomy' => 'news_category',
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
					'value' => 'news',
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
		'title' => 'Articles Settings',
		'fields' => array(
			array(
				'key' => 'field_63a1ec1fc42b8',
				'label' => 'Articles Post Sidebar Settings',
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
				'name' => 'show_news_sidebar',
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
				'message' => 'Articles sidebar widgets can be <a href="/wp-admin/widgets.php#news-widget" target="_blank">adjusted here</a>.',
				'new_lines' => 'wpautop',
				'esc_html' => 0,
			),
			array(
				'key' => 'field_63a1ec1fc43a9',
				'label' => 'Articles Post Settings',
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
				'name' => 'show_news_date',
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
				'name' => 'show_news_featured_image',
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
					'value' => 'acf-options-news',
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
