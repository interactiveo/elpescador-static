<?php

// ACF FLEX FIELDS START
		
// ACF FLEX FIELDS END 


// HIDE FLEX FIELD BLOG
function my_flexible_content_blog($field){
  if(!isset($field['layouts']) || empty($field['layouts']))
    return $field;
    foreach($field['layouts'] as $layout_key => $layout){
      if($layout['name'] === 'blog'){
        unset($field['layouts'][$layout_key]);
      }        
    }
  return $field;    
}
if($enable_blog == '0') {
  add_filter('acf/prepare_field/type=flexible_content', 'my_flexible_content_blog');
}

/// HIDE FLEX FIELD CAREERS
function my_flexible_content_careeers($field){
  if(!isset($field['layouts']) || empty($field['layouts']))
    return $field;
    foreach($field['layouts'] as $layout_key => $layout){
      if($layout['name'] === 'careers'){
        unset($field['layouts'][$layout_key]);
      }        
    }
  return $field;    
}
if($enable_careers == '0') {
  add_filter('acf/prepare_field/type=flexible_content', 'my_flexible_content_careeers');
}

/// HIDE FLEX FIELD EVENTS
function my_flexible_content_events($field){
  if(!isset($field['layouts']) || empty($field['layouts']))
    return $field;
    foreach($field['layouts'] as $layout_key => $layout){
      if($layout['name'] === 'events'){
        unset($field['layouts'][$layout_key]);
      }        
    }
  return $field;    
}
if($enable_event == '0') {
  add_filter('acf/prepare_field/type=flexible_content', 'my_flexible_content_events');
}

/// HIDE FLEX FIELD FAQ
function my_flexible_content_faq($field){
  if(!isset($field['layouts']) || empty($field['layouts']))
    return $field;
    foreach($field['layouts'] as $layout_key => $layout){
      if($layout['name'] === 'faq'){
        unset($field['layouts'][$layout_key]);
      }        
    }
  return $field;    
}
if($enable_faq == '0') {
  add_filter('acf/prepare_field/type=flexible_content', 'my_flexible_content_faq');
}

/// HIDE FLEX FIELD NEWS
function my_flexible_content_news($field){
  if(!isset($field['layouts']) || empty($field['layouts']))
    return $field;
    foreach($field['layouts'] as $layout_key => $layout){
      if($layout['name'] === 'news'){
        unset($field['layouts'][$layout_key]);
      }        
    }
  return $field;    
}
if($enable_news == '0') {
  add_filter('acf/prepare_field/type=flexible_content', 'my_flexible_content_news');
}

/// HIDE FLEX FIELD TEAM
function my_flexible_content_team($field){
  if(!isset($field['layouts']) || empty($field['layouts']))
    return $field;
    foreach($field['layouts'] as $layout_key => $layout){
      if($layout['name'] === 'team'){
        unset($field['layouts'][$layout_key]);
      }        
    }
  return $field;    
}
if($enable_team == '0') {
  add_filter('acf/prepare_field/type=flexible_content', 'my_flexible_content_team');
}


/// HIDE FLEX FIELD TESTIMONIAL
function my_flexible_content_testimonial($field){
  if(!isset($field['layouts']) || empty($field['layouts']))
    return $field;
    foreach($field['layouts'] as $layout_key => $layout){
      if($layout['name'] === 'testimonial'){
        unset($field['layouts'][$layout_key]);
      }        
    }
  return $field;    
}
if($enable_testimonial == '0') {
  add_filter('acf/prepare_field/type=flexible_content', 'my_flexible_content_testimonial');
}







  // Define ACF Theme Settings
  if( function_exists('acf_add_options_page') ):
   acf_add_options_page(array(
    'page_title' 	=> 'Theme Settings',
    'menu_title'	=> 'Theme Settings',
    'menu_slug' 	=> 'theme-general-settings',
    'capability'	=> 'edit_posts',
    //'capability'	=> 'edit_themes',
    'redirect'		=> false
   ));
   acf_add_options_sub_page(array(
    'page_title' 	=> 'Header Settings',
    'menu_title'	=> 'Header',
    'parent_slug'	=> 'theme-general-settings',
    'capability'	=> 'edit_posts',
   ));
   acf_add_options_sub_page(array(
    'page_title' 	=> 'Footer Settings',
    'menu_title'	=> 'Footer',
    'parent_slug'	=> 'theme-general-settings',
    'capability'	=> 'edit_posts',
   ));
   acf_add_options_sub_page(array(
    'page_title' 	=> 'Contact Settings',
    'menu_title'	=> 'Contact',
    'parent_slug'	=> 'theme-general-settings',
    'capability'	=> 'edit_posts',
   ));

  if($enable_blog == '1'):
    acf_add_options_sub_page(array(
     'page_title' 	=> 'Blog Settings',
     'menu_title'	=> 'Blog',
     'parent_slug'	=> 'theme-general-settings',
     'capability'	=> 'edit_posts',
    ));
  endif;

  if($enable_careers == '1'):
  acf_add_options_sub_page(array(
    'page_title' 	=> 'Career Settings',
    'menu_title'	=> 'Careers',
    'parent_slug'	=> 'theme-general-settings',
    'capability'	=> 'edit_posts',
   ));
  endif;

  if($enable_news == '1'):
   acf_add_options_sub_page(array(
    'page_title' 	=> 'News Settings',
    'menu_title'	=> 'News',
    'parent_slug'	=> 'theme-general-settings',
    'capability'	=> 'edit_posts',
   ));
  endif;

  if($enable_team == '1'):
   acf_add_options_sub_page(array(
    'page_title' 	=> 'Team Settings',
    'menu_title'	=> 'Team',
    'parent_slug'	=> 'theme-general-settings',
    'capability'	=> 'edit_posts',
   ));
  endif;

   if($enable_event == '1'):
   acf_add_options_sub_page(array(
    'page_title' 	=> 'Event Settings',
    'menu_title'	=> 'Event',
    'parent_slug'	=> 'theme-general-settings',
    'capability'	=> 'edit_posts',
   ));
   endif;

   acf_add_options_sub_page(array(
    'page_title' 	=> 'Reading Settings',
    'menu_title'	=> 'Reading',
    'parent_slug'	=> 'theme-general-settings',
    'capability'	=> 'edit_posts',
   ));

   acf_add_options_sub_page(array(
    'page_title' 	=> 'Search Settings',
    'menu_title'	=> 'Search',
    'parent_slug'	=> 'theme-general-settings',
    'capability'	=> 'edit_posts',
   ));

   acf_add_options_sub_page(array(
    'page_title' 	=> 'Promo Settings',
    'menu_title'	=> 'Promo',
    'parent_slug'	=> 'theme-general-settings',
    'capability'	=> 'edit_posts',
   ));

   acf_add_options_sub_page(array(
    'page_title' 	=> 'Examples',
    'menu_title'	=> 'Examples',
    'parent_slug'	=> 'theme-general-settings',
    'capability'	=> 'edit_posts',
   ));

 endif;


 if( function_exists('acf_add_local_field_group') ):

  acf_add_local_field_group(array(
    'key' => 'group_62eaca90b514c',
    'title' => 'Hide Fields',
    'fields' => array(
    ),
    'location' => array(
      array(
        array(
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'page',
        ),
      ),
    ),
    'menu_order' => -999,
    'position' => 'acf_after_title',
    'style' => 'seamless',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => array(
      0 => 'the_content',
      1 => 'featured_image',
    ),
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
  ));
  
  acf_add_local_field_group(array(
    'key' => 'group_62d975f33a150',
    'title' => 'Contact Settings',
    'fields' => array(
      array(
        'key' => 'field_62d97602ed3b4',
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
        'key' => 'field_WqCnSL5pFq5jab',
        'label' => 'Reservations Number',
        'name' => 'phone_number2',
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
        'key' => 'field_62d9760ced3b5',
        'label' => 'Fax Number',
        'name' => 'fax_number',
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
        'key' => 'field_62d9761bed3b6',
        'label' => 'Street Address',
        'name' => 'street_address',
        'aria-label' => '',
        'type' => 'textarea',
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
        'maxlength' => '',
        'rows' => 3,
        'new_lines' => 'br',
      ),
      array(
        'key' => 'field_62dfeef027483',
        'label' => 'Social Media',
        'name' => 'social_media',
        'aria-label' => '',
        'type' => 'repeater',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'collapsed' => '',
        'min' => 0,
        'max' => 0,
        'layout' => 'table',
        'button_label' => 'Add Social Link',
        'sub_fields' => array(
          array(
            'key' => 'field_62dfef2e27485',
            'label' => 'URL',
            'name' => 'url',
            'aria-label' => '',
            'type' => 'url',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '75',
              'class' => '',
              'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'parent_repeater' => 'field_62dfeef027483',
          ),
          array(
            'key' => 'field_62dfef3727486',
            'label' => 'Social',
            'name' => 'social',
            'aria-label' => '',
            'type' => 'select',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '25',
              'class' => '',
              'id' => '',
            ),
            'choices' => array(
              'icon-facebook' => 'Facebook',
              'icon-google-plus' => 'Google',
              'icon-instagram' => 'Instagram',
              'icon-linkedin' => 'LinkedIn',
              'icon-pinterest' => 'Pinterest',
              'icon-snapchat' => 'Snapchat',
              'icon-tiktok' => 'TikTok',
              'icon-twitch' => 'Twitch',
              'icon-twitter' => 'Twitter',
              'icon-youtube' => 'YouTube',
              'icon-yelp' => 'Yelp',
              'icon-vimeo' => 'Vimeo',
              'icon-houzz' => 'Houzz',
              'icon-rss' => 'RSS',
              'icon-mail' => 'Email',
              'icon-map' => 'Map',
            ),
            'default_value' => false,
            'allow_null' => 0,
            'multiple' => 0,
            'ui' => 0,
            'return_format' => 'value',
            'ajax' => 0,
            'placeholder' => '',
            'parent_repeater' => 'field_62dfeef027483',
          ),
        ),
        'rows_per_page' => 20,
      ),
      array(
        'key' => 'field_6793abbe34a3a',
        'label' => 'Credibility Badges',
        'name' => 'credibility_badges',
        'aria-label' => '',
        'type' => 'repeater',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'layout' => 'table',
        'pagination' => 0,
        'min' => 0,
        'max' => 0,
        'collapsed' => '',
        'button_label' => 'Add Badge',
        'rows_per_page' => 20,
        'sub_fields' => array(
          array(
            'key' => 'field_6793abd434a3b',
            'label' => 'Badge Image',
            'name' => 'badge_image',
            'aria-label' => '',
            'type' => 'image',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '10',
              'class' => '',
              'id' => '',
            ),
            'return_format' => 'array',
            'library' => 'all',
            'min_width' => '',
            'min_height' => '',
            'min_size' => '',
            'max_width' => '',
            'max_height' => '',
            'max_size' => '',
            'mime_types' => 'png,webp,svg',
            'allow_in_bindings' => 0,
            'preview_size' => 'xsmall',
            'parent_repeater' => 'field_6793abbe34a3a',
          ),
          array(
            'key' => 'field_6793abf234a3c',
            'label' => 'Badge Link',
            'name' => 'badge_link',
            'aria-label' => '',
            'type' => 'url',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '90',
              'class' => '',
              'id' => '',
            ),
            'default_value' => '',
            'allow_in_bindings' => 0,
            'placeholder' => '',
            'parent_repeater' => 'field_6793abbe34a3a',
          ),
        ),
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'acf-options-contact',
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
    'key' => 'group_62f40a722d8cd',
    'title' => 'Example Settings',
    'fields' => array(
      array(
        'key' => 'field_62fbe508ad04a',
        'label' => 'Headlines',
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
        'key' => 'field_62fbe512ad04b',
        'label' => 'Headlines',
        'name' => '',
        'aria-label' => '',
        'type' => 'message',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'message' => '<div class="type-examples">
  <h1>H1 Font Size Example</h1>
  <h2>H2 Font Size Example</h2>
  <h3>H3 Font Size Example</h3>
  <h4>H4 Font Size Example</h4>
  <h5>H5 Font Size Example</h5>
  <h6>H6 Font Size Example</h6>
  </div>',
        'new_lines' => '',
        'esc_html' => 0,
      ),
      array(
        'key' => 'field_62fbe500ad049',
        'label' => 'Glyph Icons',
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
        'key' => 'field_62f40a8236df3',
        'label' => 'Glyph Icons',
        'name' => '',
        'aria-label' => '',
        'type' => 'message',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'message' => '
        <div class="admin-glyph-wrap">
        <p><span class="icon-left"></span></p>
        <p><span class="icon-right"></span></p>
        <p><span class="icon-up"></span></p>
        <p><span class="icon-down"></span></p>
        <p><span class="icon-search"></span></p>
        <p><span class="icon-phone"></span></p>
        <p><span class="icon-map"></span></p>
        <p><span class="icon-play"></span></p>
        <p><span class="icon-mail"></span></p>
        <p><span class="icon-close"></span></p>
        <p><span class="icon-checkmark"></span></p>
        <p><span class="icon-rss"></span></p>
        <p><span class="icon-facebook"></span></p>
        <p><span class="icon-twitch"></span></p>
        <p><span class="icon-twitter"></span></p>
        <p><span class="icon-linkedin"></span></p>
        <p><span class="icon-instagram"></span></p>
        <p><span class="icon-youtube"></span></p>
        <p><span class="icon-google-plus"></span></p>
        <p><span class="icon-pinterest"></span></p>
        <p><span class="icon-vimeo"></span></p>
        <p><span class="icon-yelp"></span></p>
        <p><span class="icon-snapchat"></span></p>
        <p><span class="icon-slack"></span></p>
        <p><span class="icon-tiktok"></span></p>
        <p><span class="icon-long-arrow-left"></span></p>
        <p><span class="icon-long-arrow-right"></span></p>
        <p><span class="icon-star"></span></p>
        <p><span class="icon-fax"></span></p>
        <p><span class="icon-phone2"></span></p>
        <p><span class="icon-mobile"></span></p>
        <p><span class="icon-caret-down"></span></p>
        <p><span class="icon-caret-left"></span></p>
        <p><span class="icon-caret-right"></span></p>
        <p><span class="icon-caret-up"></span></p>
        <p><span class="icon-exclamation"></span></p>
        <p><span class="icon-left-double"></span></p>
        <p><span class="icon-right-double"></span></p>
        <p><span class="icon-houzz"></span></p>
        <p><span class="icon-globe"></span></p>
        <p><span class="icon-long-arrow-alt-left"></span></p>
        <p><span class="icon-long-arrow-alt-right"></span></p>
        <p><span class="icon-triangle"></span></p>
        <p><span class="icon-download"></span></p>
        <p><span class="icon-external-link"></span></p>
  </div>',
        'new_lines' => '',
        'esc_html' => 0,
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'acf-options-examples',
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
    'key' => 'group_62e94c1398f97',
    'title' => 'Featured Image Settings',
    'fields' => array(
      array(
        'key' => 'field_62e94c83277d2',
        'label' => 'Featured Image Crop',
        'name' => 'image_crop',
        'aria-label' => '',
        'type' => 'radio',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'choices' => array(
          'auto' => 'Auto',
          'wide' => '16:9 Cropped',
          'rectangle' => '4:3 Cropped',
          'square' => '1:1 Cropped',
          'custom' => 'Custom',
          'hide' => 'Hide Image',
        ),
        'allow_null' => 0,
        'other_choice' => 0,
        'default_value' => '',
        'layout' => 'vertical',
        'return_format' => 'value',
        'save_other_choice' => 0,
      ),
      array(
        'key' => 'field_62e94d33637e4',
        'label' => 'Custom Crop Height',
        'name' => 'custom_crop_height',
        'aria-label' => '',
        'type' => 'range',
        'instructions' => '',
        'required' => 1,
        'conditional_logic' => array(
          array(
            array(
              'field' => 'field_62e94c83277d2',
              'operator' => '==',
              'value' => 'custom',
            ),
          ),
        ),
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'default_value' => 100,
        'min' => 10,
        'max' => 1000,
        'step' => 10,
        'prepend' => '',
        'append' => '',
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
      array(
        array(
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'news',
        ),
      ),
      array(
        array(
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'career',
        ),
      ),
    ),
    'menu_order' => 0,
    'position' => 'side',
    'style' => 'seamless',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
  ));
  
  acf_add_local_field_group(array(
    'key' => 'group_62dff3053a7da',
    'title' => 'Footer Settings',
    'fields' => array(
      array(
        'key' => 'field_62dff736d1c38',
        'label' => 'Footer Layout',
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
        'key' => 'field_62dff77cf9eee',
        'label' => 'Show Main Menu',
        'name' => 'footer_show_main_menu',
        'aria-label' => '',
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '25',
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
        'key' => 'field_62dff78df9eef',
        'label' => 'Show Social Media',
        'name' => 'footer_show_social_media',
        'aria-label' => '',
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '25',
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
        'key' => 'field_62dff7abf9ef1',
        'label' => 'Show Phone',
        'name' => 'footer_show_phone',
        'aria-label' => '',
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '25',
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
        'key' => 'field_62e02e3bc13c4',
        'label' => 'Show Fax',
        'name' => 'footer_show_fax',
        'aria-label' => '',
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '25',
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
        'key' => 'field_62dff7b2f9ef2',
        'label' => 'Show Address',
        'name' => 'footer_show_address',
        'aria-label' => '',
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '25',
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
        'key' => 'field_62dff73ed1c39',
        'label' => 'Footer Logo',
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
        'key' => 'field_62dff318b1528',
        'label' => 'Footer Logo',
        'name' => 'footer_logo',
        'aria-label' => '',
        'type' => 'image',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'return_format' => 'array',
        'preview_size' => 'xsmall',
        'library' => 'all',
        'min_width' => '',
        'min_height' => '',
        'min_size' => '',
        'max_width' => '',
        'max_height' => '',
        'max_size' => '',
        'mime_types' => '',
      ),
      array(
        'key' => 'field_62e0315354aa0',
        'label' => 'Footer Copyright',
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
        'key' => 'field_62e0316154aa1',
        'label' => 'Footer Copyright Text',
        'name' => 'footer_copyright_text',
        'aria-label' => '',
        'type' => 'wysiwyg',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'default_value' => '',
        'tabs' => 'all',
        'toolbar' => 'full',
        'media_upload' => 0,
        'delay' => 1,
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'acf-options-footer',
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
    'key' => 'group_62d96fbe13d42',
    'title' => 'Header Settings',
    'fields' => array(
      array(
        'key' => 'field_62d975291968d',
        'label' => 'Header Layout',
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
        'open' => 1,
        'multi_expand' => 0,
        'endpoint' => 0,
      ),
      array(
        'key' => 'field_62d9745c18a3b',
        'label' => 'Header Layout',
        'name' => 'header_layout',
        'aria-label' => '',
        'type' => 'button_group',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'choices' => array(
          'header-one' => '<i class="admin-icon icon-layout-header-1">One</i>',
          'header-two' => '<i class="admin-icon icon-layout-header-2">Two</i>',
        ),
        'allow_null' => 0,
        'default_value' => '',
        'layout' => 'horizontal',
        'return_format' => 'value',
      ),
      array(
        'key' => 'field_62d975361968e',
        'label' => 'Header Logo',
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
        'key' => 'field_62d9751813d61',
        'label' => 'Header Logo',
        'name' => 'header_logo',
        'aria-label' => '',
        'type' => 'image',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'return_format' => 'array',
        'preview_size' => 'xsmall',
        'library' => 'all',
        'min_width' => '',
        'min_height' => '',
        'min_size' => '',
        'max_width' => '',
        'max_height' => '',
        'max_size' => '',
        'mime_types' => '',
      ),
      array(
        'key' => 'field_62d976816f7eb',
        'label' => 'Utility Menu',
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
        'key' => 'field_62d9894688920',
        'label' => 'Show Phone',
        'name' => 'show_phone',
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
        'ui' => 1,
        'ui_on_text' => '',
        'ui_off_text' => '',
      ),
      array(
        'key' => 'field_62d99ee3d86b9',
        'label' => 'Show Search',
        'name' => 'show_search',
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
        'ui' => 1,
        'ui_on_text' => '',
        'ui_off_text' => '',
      ),
      array(
        'key' => 'field_62d976886f7ec',
        'label' => 'Utility Menu Links',
        'name' => 'utility_menu',
        'aria-label' => '',
        'type' => 'repeater',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'collapsed' => '',
        'min' => 0,
        'max' => 0,
        'layout' => 'table',
        'button_label' => 'Add Utility Menu Link',
        'sub_fields' => array(
          array(
            'key' => 'field_62d976906f7ed',
            'label' => 'Link',
            'name' => 'link',
            'aria-label' => '',
            'type' => 'link',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '50',
              'class' => '',
              'id' => '',
            ),
            'return_format' => 'array',
            'parent_repeater' => 'field_62d976886f7ec',
          ),
          array(
            'key' => 'field_62dac0cba6b8a',
            'label' => 'Show On',
            'name' => 'show_on',
            'aria-label' => '',
            'type' => 'checkbox',
            'instructions' => '',
            'required' => 1,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '',
              'class' => '',
              'id' => '',
            ),
            'choices' => array(
              'show-sm' => 'Phone',
              'show-md' => 'Tablet',
              'show-lg' => 'Desktop',
            ),
            'allow_custom' => 0,
            'default_value' => array(
            ),
            'layout' => 'vertical',
            'toggle' => 0,
            'return_format' => 'value',
            'save_custom' => 0,
            'parent_repeater' => 'field_62d976886f7ec',
          ),
          array(
            'key' => 'field_62d976c66f7ee',
            'label' => 'Icon Class',
            'name' => 'icon_class',
            'aria-label' => '',
            'type' => 'text',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
              'width' => '25',
              'class' => '',
              'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
            'parent_repeater' => 'field_62d976886f7ec',
          ),
        ),
        'rows_per_page' => 20,
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'acf-options-header',
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
    'key' => 'group_62e420c44b737',
    'title' => 'Reading Settings',
    'fields' => array(
      array(
        'key' => 'field_62f284d931e8c',
        'label' => 'Read More Button Label',
        'name' => 'read_more_label',
        'aria-label' => '',
        'type' => 'text',
        'instructions' => 'If left blank. Will revert to, "Read More".',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'default_value' => 'Read More',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
        'maxlength' => '',
      ),
      array(
        'key' => 'field_62f299c750240',
        'label' => 'No Results Error Message',
        'name' => 'no_results_error_message',
        'aria-label' => '',
        'type' => 'text',
        'instructions' => 'If left blank. Will revert to, "No Results Found".',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'default_value' => 'No results found.',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
        'maxlength' => '',
      ),
      array(
        'key' => 'field_62f3c5dc367ac',
        'label' => 'Page Not Found - Title',
        'name' => 'page_not_found_title',
        'aria-label' => '',
        'type' => 'text',
        'instructions' => 'If left blank. Will revert to, "Page Not Found".',
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
        'key' => 'field_62f3c5f3367ad',
        'label' => 'Page Not Found	- Message',
        'name' => 'page_not_found_message',
        'aria-label' => '',
        'type' => 'textarea',
        'instructions' => 'If left blank. Will revert to, "The link you followed may be broken, or the page may have been removed".',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'default_value' => '',
        'placeholder' => '',
        'maxlength' => '',
        'rows' => 2,
        'new_lines' => 'wpautop',
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'acf-options-reading',
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
    'key' => 'group_62fbe1da819cc',
    'title' => 'Search Settings',
    'fields' => array(
      array(
        'key' => 'field_62fbe1ee548de',
        'label' => 'Search Page Settings',
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
        'key' => 'field_62fbe1fa548df',
        'label' => 'Show Side Filters',
        'name' => 'show_search_side_filters',
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
          'value' => 'acf-options-search',
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
    'key' => 'group_62fbe35f38d2f',
    'title' => 'Theme Settings Welcome',
    'fields' => array(
      array(
        'key' => 'field_62fbe36df9bc9',
        'label' => '',
        'name' => '',
        'aria-label' => '',
        'type' => 'message',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'message' => '<div style="background:white; padding:25px; margin:0 auto; display:block; text-align:center; line-height:1;">
  <img src="/wp-content/themes/blankslate-child/screenshot.png" style="display:block; width:100%; height:auto; max-width:600px; margin:0 auto;">
  </div>',
        'new_lines' => '',
        'esc_html' => 0,
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'theme-general-settings',
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
  
  acf_add_local_field_group( array(
    'key' => 'group_64ef482c4e414',
    'title' => 'Promo Settings',
    'fields' => array(
      array(
        'key' => 'field_64ef482cb5aa0',
        'label' => 'Promo Text Area',
        'name' => 'promo_text_area',
        'aria-label' => '',
        'type' => 'wysiwyg',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '50',
          'class' => '',
          'id' => '',
        ),
        'default_value' => '',
        'tabs' => 'all',
        'toolbar' => 'full',
        'media_upload' => 0,
        'delay' => 1,
      ),
      array(
        'key' => 'field_64ef4862b5aa1',
        'label' => 'Promo Side Image',
        'name' => 'promo_side_image',
        'aria-label' => '',
        'type' => 'image',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '25',
          'class' => '',
          'id' => '',
        ),
        'return_format' => 'array',
        'library' => 'all',
        'min_width' => '',
        'min_height' => '',
        'min_size' => '',
        'max_width' => '',
        'max_height' => '',
        'max_size' => '',
        'mime_types' => '',
        'preview_size' => 'xsmall',
      ),
      array(
        'key' => 'field_rDmBGRszgMwU',
        'label' => 'Promo Background Image',
        'name' => 'promo_background_image',
        'aria-label' => '',
        'type' => 'image',
        'instructions' => '',
        'required' => 1,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '25',
          'class' => '',
          'id' => '',
        ),
        'return_format' => 'array',
        'library' => 'all',
        'min_width' => '',
        'min_height' => '',
        'min_size' => '',
        'max_width' => '',
        'max_height' => '',
        'max_size' => '',
        'mime_types' => '',
        'preview_size' => 'xsmall',
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'acf-options-promo',
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
  ) );

  endif;		

  add_action( 'acf/include_fields', function() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
      return;
    }
  
    acf_add_local_field_group( array(
    'key' => 'group_64ef482c4e414',
    'title' => 'Promo Settings',
    'fields' => array(
      array(
        'key' => 'field_64ef482cb5aa0',
        'label' => 'Promo Text Area',
        'name' => 'promo_text_area',
        'aria-label' => '',
        'type' => 'wysiwyg',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '50',
          'class' => '',
          'id' => '',
        ),
        'default_value' => '',
        'tabs' => 'all',
        'toolbar' => 'full',
        'media_upload' => 0,
        'delay' => 1,
      ),
      array(
        'key' => 'field_64ef4862b5aa1',
        'label' => 'Promo Background Image',
        'name' => 'promo_background_image',
        'aria-label' => '',
        'type' => 'image',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '50',
          'class' => '',
          'id' => '',
        ),
        'return_format' => 'array',
        'library' => 'all',
        'min_width' => '',
        'min_height' => '',
        'min_size' => '',
        'max_width' => '',
        'max_height' => '',
        'max_size' => '',
        'mime_types' => '',
        'preview_size' => 'xsmall',
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'acf-options-promo',
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
  ) );
  } );
  
  




?>
