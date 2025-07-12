<?php

// ** ACF **
require_once(__DIR__.'/template-parts/admin/functions/post-types-page.php');
require_once(__DIR__.'/template-parts/admin/functions/post-types-adventures.php');
require_once(__DIR__.'/template-parts/admin/functions/post-types-news.php');
require_once(__DIR__.'/template-parts/admin/functions/post-types-career.php');
require_once(__DIR__.'/template-parts/admin/functions/post-types-faq.php');
require_once(__DIR__.'/template-parts/admin/functions/post-types-blog.php');
require_once(__DIR__.'/template-parts/admin/functions/post-types-event.php');
require_once(__DIR__.'/template-parts/admin/functions/post-types-team.php');
require_once(__DIR__.'/template-parts/admin/functions/post-types-tips.php');
require_once(__DIR__.'/template-parts/admin/functions/post-types-testimonial.php');
require_once(__DIR__.'/template-parts/admin/functions/acf.php');
require_once(__DIR__.'/template-parts/admin/functions/menu.php');


// ======================================
// LOGO LOGIN
// ======================================
function my_login_logo_one() {
  $logo = get_field('header_logo', 'options');
?>
<?php if($logo): ?>
<style type="text/css">
body.login div#login h1 a {
background-image: url(<?php echo $logo['url']; ?>);
width: auto;
border:15px solid #034145;
background-size:contain;
background-color:#034145;
background-position: 50% 50%;
box-shadow: 0 0 0 1px #ccd0d4, 0 2px 4px rgba(0,0,0,.04);
}
</style>
<?php endif; ?>
 <?php
} add_action( 'login_enqueue_scripts', 'my_login_logo_one' );


// ======================================
// MODIFY ADMIN TOOLBAR NEW DROP-DOWN
// ======================================
add_action( 'admin_bar_menu', 'customize_my_wp_admin_bar', 80 );
function customize_my_wp_admin_bar( $wp_admin_bar ) {
    $new_content_node = $wp_admin_bar->get_node('new-content');
    $new_content_node->href = '/wp-admin/post-new.php?post_type=page'; // new default url
    $wp_admin_bar->add_node($new_content_node);
    $wp_admin_bar->remove_menu('new-post'); //remove posts
    $wp_admin_bar->remove_menu('new-user'); //remove user
}

// =========================================
// REMOVE ITEMS FROM ADMIN TOOLBAR
// =========================================
function update_adminbar($wp_adminbar) {
  $wp_adminbar->remove_node('wp-logo');
  $wp_adminbar->remove_node('customize');
  $wp_adminbar->remove_node('comments');
  $wp_adminbar->add_node([
    'id' => 'straightnorth',
    'title' => 'Theme Settings',
    'href' => '/wp-admin/admin.php?page=theme-general-settings',
    'meta' => [
      'target' => '_self'
    ]
  ]);
}
add_action('admin_bar_menu', 'update_adminbar', 999);


// =========================================
// Hide Draft Pages from the menu
// =========================================
function filter_draft_pages_from_menu ($items, $args) {
  foreach ($items as $ix => $obj) {
   if (!is_user_logged_in () && 'draft' == get_post_status ($obj->object_id)) {
    unset ($items[$ix]);
   }
  }
  return $items;
 }
 add_filter ('wp_nav_menu_objects', 'filter_draft_pages_from_menu', 10, 2); 

// ======================================
// REMOVE ADMIN MENU ITEMS
// ======================================
function remove_menus(){
	remove_menu_page( 'edit.php' );                     //Posts
	remove_menu_page( 'edit-comments.php' );            //Comments
}
add_action( 'admin_menu', 'remove_menus' );

// =========================================
// prioritetize pagination over displaying custom post type content
// =========================================
add_action('init', function() {
  add_rewrite_rule('(.?.+?)/page/?([0-9]{1,})/?$', 'index.php?pagename=$matches[1]&paged=$matches[2]', 'top');
});

// =========================================
// CURRENT YEAR SHORTCODE [year]
// =========================================
function year_shortcode () {
$year = date_i18n ('Y');
return $year;
}
add_shortcode ('year', 'year_shortcode');

// =========================================
// SHORTCODE: [SITENAME]
// =========================================
function custom_sitename_shortcode() {
	return get_bloginfo();
}
add_shortcode( 'sitename', 'custom_sitename_shortcode' );

// =========================================
// SHORTCODE: [SITEURL]
// =========================================
function custom_site_url_shortcode() {
	return get_bloginfo('url');
}
add_shortcode('siteurl','custom_site_url_shortcode');


// ======================================
// DISABLE WP AUTO <P>
// ======================================
remove_filter('the_content', 'wpautop');

// =========================================
// DISABLE GUTENBURG BLOCK CSS
// =========================================
function wps_deregister_styles() {
    wp_dequeue_style( 'wp-block-library' );
}
add_action( 'wp_print_styles', 'wps_deregister_styles', 100 );


// =========================================
// VISUAL MODE DEFAULT
// =========================================
//add_filter( 'wp_default_editor', create_function('', 'return "tinymce";'));


// ======================================
// CUSTOM IMAGE SIZES
// ======================================
if ( function_exists( 'add_image_size' ) ) {
  add_image_size( 'xsmall', 360, 360, false );
  add_image_size( 'small', 640, 640, false );
  add_image_size( 'card1', 566, 850, ['center', 'top'] ); // hidden from editor
  add_image_size( 'mobile1', 1024, 1024, ['center', 'top'] ); // hidden from editor
  add_image_size( 'mobile2', 768, 768, ['center', 'top'] );  // hidden from editor
  add_image_size( 'mobile3', 640, 640, ['center', 'top'] );  // hidden from editor
  add_image_size( 'xlarge', 2500, 2500, false );
}
add_filter( 'image_size_names_choose', 'my_custom_sizes' );

function my_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'xsmall' => __('X-Small'),
        'small' => __('Small'),
        'xlarge' => __('X-Large'),
    ) );
}


// ======================================
// ADMIN .CSS
// ======================================
function admin_style() {
  wp_enqueue_style('admin-styles', get_stylesheet_directory_uri().'/admin.css');
}
add_action('admin_enqueue_scripts', 'admin_style');


// ======================================
// ACF CUSTOM COLOR PALLETE
// ======================================
function klf_acf_input_admin_footer() { ?>

	<script type="text/javascript">
	(function($) {
	
	acf.add_filter('color_picker_args', function( args, $field ){
	
	// add the hexadecimal codes here for the colors you want to appear as swatches
	args.palettes = ['#D6EBEC', '#9AC133', '#00A99D', '#034145', '#FFF', '#EEE', '#CCC', 'rgba(35, 31, 32, 0.60)', '#000']
	
	// return colors
	return args;
	
	});
	
	})(jQuery);
	</script>
	
	<?php }
	
	add_action('acf/input/admin_footer', 'klf_acf_input_admin_footer');

  // TINYCE COLORS
  function custom_tinymce_color_palette( $init ) {
    // Define your custom color palette
    $custom_colors = '
        "D6EBEC", "Light Aqua",
        "9AC133", "Green",
        "00A99D", "Teal",
        "034145", "Dark Teal",
        "FFFFFF", "White",
        "EEEEEE", "Light Gray",
        "CCCCCC", "Gray",
        "rgba(35, 31, 32, 0.60)", "Semi-Transparent Black",
        "000000", "Black"
    ';

    // Add the custom color palette to the TinyMCE settings
    $init['textcolor_map'] = '[' . $custom_colors . ']';

    // Enable the text color dropdown
    $init['textcolor_rows'] = 1; // Display the colors in one row
    return $init;
}
add_filter( 'tiny_mce_before_init', 'custom_tinymce_color_palette' );

// =========================================
// ARCHIVE TITLES HACK
// =========================================
add_filter('get_the_archive_title', function ($title) {
    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = '<span class="vcard">' . get_the_author() . '</span>';
    } elseif (is_tax()) { //for custom post types
        $title = sprintf(__('%1$s'), single_term_title('', false));
    } elseif (is_post_type_archive()) {
        $title = post_type_archive_title('', false);
    }
    return $title;
});


// =========================================
// Register the Constant Contact form shortcode
// =========================================
function newsletter_form_shortcode() {
  ob_start();
  ?>
  <!-- Begin Constant Contact Active Forms -->
  <script> var _ctct_m = "6d6184cea0ae2ebc4e1e68a953a03e92"; </script>
  <script id="signupScript" src="//static.ctctcdn.com/js/signup-form-widget/current/signup-form-widget.min.js" async defer></script>
  <!-- End Constant Contact Active Forms -->
  <?php
  return ob_get_clean();
}
add_shortcode('newsletter-form', 'newsletter_form_shortcode');


// =========================================
// SEARCH HIJAC
// =========================================
add_filter('sf_edit_query_args', 'custom_sf_priority_post_types', 10, 2);
function custom_sf_priority_post_types($args, $sf_form_id) {
    // Optional: limit to specific form ID
    // if ($sf_form_id != 123) return $args;

    $args['post_type'] = ['adventures', 'event', 'page', 'blog'];

    // We're going to override orderby manually anyway
    $args['orderby'] = 'custom_menu_relevance';

    // Add custom ORDER BY
    add_filter('posts_orderby', 'custom_sf_orderby_post_type_priority', 10, 2);

    return $args;
}

function custom_sf_orderby_post_type_priority($orderby, $query) {
    global $wpdb;

    if ($query->is_main_query()) {
        return "
            (CASE 
                WHEN {$wpdb->posts}.post_type = 'adventures' THEN 0
                WHEN {$wpdb->posts}.post_type = 'event' THEN 1
                WHEN {$wpdb->posts}.post_type = 'page' THEN 2
                WHEN {$wpdb->posts}.post_type = 'blog' THEN 3
                ELSE 4
            END),
            {$wpdb->posts}.menu_order ASC,
            {$wpdb->posts}.post_title LIKE '%" . esc_sql($query->get('s')) . "%' DESC,
            {$wpdb->posts}.post_date DESC
        ";
    }

    return $orderby;
}

// =========================================
// BADGES IMAGES SHORTCODE
// =========================================
function badge_images_shortcode() {
  $credibility_badges = get_field('credibility_badges', 'options');
  if (!empty($credibility_badges) && have_rows('credibility_badges', 'options')) {
      ob_start();
      ?>
          <ul class="row nested center-xs wrap_credibility_badges">
              <?php 
              // Loop through the credibility badges
              while (have_rows('credibility_badges', 'options')) : the_row(); 
                  $badge_image = get_sub_field('badge_image');
                  $badge_link = get_sub_field('badge_link');
                  if (!empty($badge_image)) : ?>
                      <li class="col col-xs">
                          <?php if (!empty($badge_link)) : ?><a href="<?php echo esc_url($badge_link); ?>" target="_blank" title="<?php echo esc_attr($badge_image['title']); ?>"><?php endif; ?>
                            <img src="<?php echo esc_url($badge_image['sizes']['xsmall']); ?>" alt="<?php echo esc_attr($badge_image['alt']); ?>" />
                          <?php if (!empty($badge_link)) : ?></a><?php endif; ?>
                      </li>
                  <?php endif; ?>
              <?php endwhile; ?>
          </ul>
      <?php
      return ob_get_clean(); // Return the buffered HTML
  }
  return ''; // Return empty string if no badges found
}
// Add the shortcode to WordPress
add_shortcode('badge-images', 'badge_images_shortcode');


// =========================================
//Snippet Name: Custom post type yearly monthly archive permalinks
// =========================================
 function wpc_blog_rewrite_rules(){

    add_rewrite_rule(
        'blog/([0-9]{4})/([0-9]{1,2})/?$',
        'index.php?post_type=blog&year=$matches[1]&monthnum=$matches[2]',
        'top'
    );

    add_rewrite_rule(
        'blog/([0-9]{4})/?$',
        'index.php?post_type=blog&year=$matches[1]',
        'top'
    );

}
add_action( 'init', 'wpc_blog_rewrite_rules' );


function wpc_news_rewrite_rules(){

  add_rewrite_rule(
      'news/([0-9]{4})/([0-9]{1,2})/?$',
      'index.php?post_type=news&year=$matches[1]&monthnum=$matches[2]',
      'top'
  );

  add_rewrite_rule(
      'news/([0-9]{4})/?$',
      'index.php?post_type=news&year=$matches[1]',
      'top'
  );

}
add_action( 'init', 'wpc_news_rewrite_rules' );




 ?>
