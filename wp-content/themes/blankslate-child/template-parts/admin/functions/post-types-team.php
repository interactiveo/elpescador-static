<?php

// VARIABLES
$enable_team = '0';
$enable_team_tax = '0';
$enable_team_widget = '0';
function global_var_team() {
	global $enable_team;
	global $enable_team_tax;
	global $enable_team_widget;
}
add_action( 'after_setup_theme', 'global_var_team' );

// POST TYPE
function sn_register_my_cpts_team() {
  $labels = [
    "name" => __( "Team", "blankslate-child" ),
    "singular_name" => __( "Team", "blankslate-child" ),
    "menu_name" => __( "Team", "blankslate-child" ),
    "all_items" => __( "All Team", "blankslate-child" ),
    "add_new" => __( "Add New", "blankslate-child" ),
    "add_new_item" => __( "Add New Team", "blankslate-child" ),
    "edit_item" => __( "Edit Team", "blankslate-child" ),
    "new_item" => __( "New Team", "blankslate-child" ),
    "view_item" => __( "View Team", "blankslate-child" ),
    "view_items" => __( "View Team", "blankslate-child" ),
    "search_items" => __( "Search Team", "blankslate-child" ),
    "not_found" => __( "No Team found", "blankslate-child" ),
    "not_found_in_trash" => __( "No Team found in trash", "blankslate-child" ),
    "parent" => __( "Parent Team:", "blankslate-child" ),
    "featured_image" => __( "Featured image for this Team", "blankslate-child" ),
    "set_featured_image" => __( "Set featured image for this Team", "blankslate-child" ),
    "remove_featured_image" => __( "Remove featured image for this Team", "blankslate-child" ),
    "use_featured_image" => __( "Use as featured image for this Team", "blankslate-child" ),
    "archives" => __( "Team archives", "blankslate-child" ),
    "insert_into_item" => __( "Insert into Team", "blankslate-child" ),
    "uploaded_to_this_item" => __( "Upload to this Team", "blankslate-child" ),
    "filter_items_list" => __( "Filter Team list", "blankslate-child" ),
    "items_list_navigation" => __( "Team list navigation", "blankslate-child" ),
    "items_list" => __( "Team list", "blankslate-child" ),
    "attributes" => __( "Team attributes", "blankslate-child" ),
    "name_admin_bar" => __( "Team", "blankslate-child" ),
    "item_published" => __( "Team published", "blankslate-child" ),
    "item_published_privately" => __( "Team published privately.", "blankslate-child" ),
    "item_reverted_to_draft" => __( "Team reverted to draft.", "blankslate-child" ),
    "item_scheduled" => __( "Team scheduled", "blankslate-child" ),
    "item_updated" => __( "Team updated.", "blankslate-child" ),
    "parent_item_colon" => __( "Parent Team:", "blankslate-child" ),
  ];
	$args = [
		"label" => __( "Team", "blankslate-child" ),
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
		"rewrite" => [ "slug" => "team", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "excerpt", "thumbnail", "revisions" ],
		"show_in_graphql" => false,
	];
	register_post_type( "team", $args );
}
if($enable_team == '1'):
add_action( 'init', 'sn_register_my_cpts_team' );
endif;


// TAXONOMY
function sn_register_my_tax_team() {
	$labels = [
		"name" => __( "Team Category", "blankslate-child" ),
		"singular_name" => __( "Team Categories", "blankslate-child" ),
	];
	$args = [
		"label" => __( "Team Category", "blankslate-child" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		//"rewrite" => [ 'slug' => 'team-category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "team_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "team_category", [ "team" ], $args );
}
if($enable_team_tax == '1'):
add_action( 'init', 'sn_register_my_tax_team' );
endif;


// SIDE BAR
function sn_register_widget_team() {
    register_sidebar( array(
        'name'          => __( 'Team', 'textdomain' ),
        'id'            => 'team-widget',
        'description'   => __( '' ),
        'before_widget' => '<div id="%1$s" class="widget team-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h6 class="widget-title">',
        'after_title'   => '</h6>',
    ) );
}
if($enable_team_widget == '1'):
add_action( 'widgets_init', 'sn_register_widget_team' );
endif;


// ACF
if($enable_team == '1'):
	if( function_exists('acf_add_local_field_group') ):
	acf_add_local_field_group(array(
		'key' => 'group_62ebfad30c4e1',
		'title' => 'Team Fields',
		'fields' => array(
			array(
				'key' => 'field_62ebfaddc1bbd',
				'label' => 'Job Title',
				'name' => 'job_title',
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
				'key' => 'field_62ebfae1c1bbe',
				'label' => 'Phone Number',
				'name' => 'phone_number',
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
				'key' => 'field_62ebfae8c1bbf',
				'label' => 'Email Address',
				'name' => 'email_address',
				'aria-label' => '',
				'type' => 'email',
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
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'team',
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
		'key' => 'group_62fbe0fe09b94',
		'title' => 'Team Settings',
		'fields' => array(
			array(
				'key' => 'field_62fbe10d22a64',
				'label' => 'Team Post Settings',
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
				'key' => 'field_62fbe11522a65',
				'label' => 'Show Featured Image',
				'name' => 'show_team_featured_image',
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
					'value' => 'acf-options-team',
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
