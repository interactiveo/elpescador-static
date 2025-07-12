<?php

// VARIABLES
$enable_event = '1';
$enable_event_tax = '0';
$enable_event_widget = '0';
function global_var_event() {
	global $enable_event;
	global $enable_event_tax;
	global $enable_event_widget;
}
add_action( 'after_setup_theme', 'global_var_event' );

// POST TYPE
function sn_register_my_cpts_event() {
  $labels = [
    "name" => __( "Special Offers", "blankslate-child" ),
    "singular_name" => __( "Special Offer", "blankslate-child" ),
    "menu_name" => __( "Special Offers", "blankslate-child" ),
    "all_items" => __( "All Special Offers", "blankslate-child" ),
    "add_new" => __( "Add New", "blankslate-child" ),
    "add_new_item" => __( "Add New Special Offer", "blankslate-child" ),
    "edit_item" => __( "Edit Special Offer", "blankslate-child" ),
    "new_item" => __( "New Special Offer", "blankslate-child" ),
    "view_item" => __( "View Special Offer", "blankslate-child" ),
    "view_items" => __( "View Special Offer", "blankslate-child" ),
    "search_items" => __( "Search Special Offer", "blankslate-child" ),
    "not_found" => __( "No Special Offer found", "blankslate-child" ),
    "not_found_in_trash" => __( "No Special Offer found in trash", "blankslate-child" ),
    "parent" => __( "Parent Special Offer:", "blankslate-child" ),
    "featured_image" => __( "Featured image for this Special Offer", "blankslate-child" ),
    "set_featured_image" => __( "Set featured image for this Special Offer", "blankslate-child" ),
    "remove_featured_image" => __( "Remove featured image for this Special Offer", "blankslate-child" ),
    "use_featured_image" => __( "Use as featured image for this Special Offer", "blankslate-child" ),
    "archives" => __( "Special Offer archives", "blankslate-child" ),
    "insert_into_item" => __( "Insert into Special Offer", "blankslate-child" ),
    "uploaded_to_this_item" => __( "Upload to this Special Offer", "blankslate-child" ),
    "filter_items_list" => __( "Filter Special Offer list", "blankslate-child" ),
    "items_list_navigation" => __( "Special Offer list navigation", "blankslate-child" ),
    "items_list" => __( "Special Offer list", "blankslate-child" ),
    "attributes" => __( "Special Offer attributes", "blankslate-child" ),
    "name_admin_bar" => __( "Special Offer", "blankslate-child" ),
    "item_published" => __( "Special Offer published", "blankslate-child" ),
    "item_published_privately" => __( "Special Offer published privately.", "blankslate-child" ),
    "item_reverted_to_draft" => __( "Special Offer reverted to draft.", "blankslate-child" ),
    "item_scheduled" => __( "Special Offer scheduled", "blankslate-child" ),
    "item_updated" => __( "Special Offer updated.", "blankslate-child" ),
    "parent_item_colon" => __( "Parent Special Offer:", "blankslate-child" ),
  ];
	$args = [
		"label" => __( "Special Offer", "blankslate-child" ),
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
		"rewrite" => [ "slug" => "events", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "excerpt", "thumbnail", "revisions" ],
		"show_in_graphql" => false,
	];
	register_post_type( "event", $args );
}
if($enable_event == '1'):
add_action( 'init', 'sn_register_my_cpts_event' );
endif;


// TAXONOMY
function sn_register_my_tax_event() {
	$labels = [
		"name" => __( "Special Offer Category", "blankslate-child" ),
		"singular_name" => __( "Special Offer Categories", "blankslate-child" ),
	];
	$args = [
		"label" => __( "Special Offer Category", "blankslate-child" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		//"rewrite" => [ 'slug' => 'event-category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "event_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "event_category", [ "event" ], $args );
}
if($enable_event_tax == '1'):
add_action( 'init', 'sn_register_my_tax_event' );
endif;


// SIDE BAR
function sn_register_widget_event() {
    register_sidebar( array(
        'name'          => __( 'Special Offer', 'textdomain' ),
        'id'            => 'event-widget',
        'description'   => __( '' ),
        'before_widget' => '<div id="%1$s" class="widget event-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6 class="widget-title">',
        'after_title'   => '</h6>',
    ) );
}
if($enable_event_widget == '1'):
add_action( 'widgets_init', 'sn_register_widget_event' );
endif;


// ACF 
if($enable_event == '1'):
	if( function_exists('acf_add_local_field_group') ):
	acf_add_local_field_group(array(
		'key' => 'group_634024c7a99e6',
		'title' => 'Special Offer Fields',
		'fields' => array(
			array(
				'key' => 'field_pvFwYutvNN3u',
				'label' => 'Special Offer Location',
				'name' => 'event_location',
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
			),
			array(
				'key' => 'field_63402585c5f87',
				'label' => 'Date Logic',
				'name' => 'date_logic',
				'aria-label' => '',
				'type' => 'button_group',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'Date Only' => 'Date Only',
					'Date & Time' => 'Date & Time',
				),
				'allow_null' => 0,
				'default_value' => '',
				'layout' => 'horizontal',
				'return_format' => 'value',
			),
			array(
				'key' => 'field_634024d383b55',
				'label' => 'Special Offer Date (Start)',
				'name' => 'event_date',
				'aria-label' => '',
				'type' => 'date_picker',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '50',
					'class' => '',
					'id' => '',
				),
				'display_format' => 'm/d/Y',
				'return_format' => 'm/d/Y',
				'first_day' => 1,
			),
			array(
				'key' => 'field_634024d383b54',
				'label' => 'Special Offer Date (End)',
				'name' => 'event_date2',
				'aria-label' => '',
				'type' => 'date_picker',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '50',
					'class' => '',
					'id' => '',
				),
				'display_format' => 'm/d/Y',
				'return_format' => 'm/d/Y',
				'first_day' => 1,
			),
			array(
				'key' => 'field_63402571c5f86',
				'label' => 'Special Offer Time (Start)',
				'name' => 'event_time',
				'aria-label' => '',
				'type' => 'time_picker',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_63402585c5f87',
							'operator' => '==',
							'value' => 'Date & Time',
						),
					),
				),
				'wrapper' => array(
					'width' => '50',
					'class' => '',
					'id' => '',
				),
				'display_format' => 'g:i A',
				'return_format' => 'g:i A',
			),
			array(
				'key' => 'field_63402571c5f89',
				'label' => 'Special Offer Time (End)',
				'name' => 'event_time2',
				'aria-label' => '',
				'type' => 'time_picker',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_63402585c5f87',
							'operator' => '==',
							'value' => 'Date & Time',
						),
					),
				),
				'wrapper' => array(
					'width' => '50',
					'class' => '',
					'id' => '',
				),
				'display_format' => 'g:i A',
				'return_format' => 'g:i A',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'event',
				),
			),
		),
		'menu_order' => -1,
		'position' => 'acf_after_title',
		//'style' => 'seamless',
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
			'key' => 'group_63a32499dd99c',
			'title' => 'Special Offer Settings',
			'fields' => array(
				array(
					'key' => 'field_63a3249a47773',
					'label' => 'Special Offers are Clickable',
					'name' => 'events_are_clickable',
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
					'default_value' => 1,
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
						'value' => 'acf-options-event',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
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
