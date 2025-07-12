<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta name="viewport" content="width=device-width" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, viewport-fit=cover">
  <?php wp_head(); ?>
  <script src="<?php bloginfo('stylesheet_directory'); ?>/js/plugins/featherlight.min.js"></script>
  <script src="<?php bloginfo('stylesheet_directory'); ?>/js/plugins/featherlight.gallery.min.js"></script>
  <script src="<?php bloginfo('stylesheet_directory'); ?>/js/plugins/swiper-bundle.min.js"></script>
  <script src="<?php bloginfo('stylesheet_directory'); ?>/js/plugins/select2.full.min.js"></script>
  <script src="<?php bloginfo('stylesheet_directory'); ?>/js/plugins/datatables.min.js"></script>
  <script src="<?php bloginfo('stylesheet_directory'); ?>/js/min/scripts.min.js"></script>
  <link href="<?php bloginfo('stylesheet_directory'); ?>/datatables.min.css" rel="stylesheet">
  <!-- FAV -->
  <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
  <link rel="shortcut icon" href="/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
  <meta name="apple-mobile-web-app-title" content="El Pescador Lodge and Villa" />
  <link rel="manifest" href="/site.webmanifest" />
  <!-- FAV -->
  <?php if ( is_user_logged_in() ): ?><link rel='stylesheet' href='<?php echo get_stylesheet_directory_uri(); ?>/admin.css' media='all' /><?php endif; ?>
</head>

<?php

if($border_design = get_field('border_design', 'options')):
else:
  $border_design = 'border_design_one';
endif;

if($header_layout = get_field('header_layout', 'options')):
  // HEADER ONE
  if($header_layout == 'header-one'):
    $header_alignment = 'bottom-lg';
  // HEADER TWO
  elseif($header_layout == 'header-two'):
    $header_alignment = null;
  endif;
endif;


?>

<body <?php body_class(''.$border_design.''); ?>>

  <?php get_template_part('template-parts/header/header--mobile_menu'); ?>

  <!-- HEADER START -->
  <header id="header" class="<?php if($header_layout): echo $header_layout; endif; ?>" role="banner">
    <div class="header-top">
      <div class="wrap-x">
        <div class="inside">
          <div class="row nowrap gap-half between-xs middle-xs row--main <?php if($header_alignment): echo $header_alignment; endif; ?> ">

            <!-- COL LOGO -->
            <div class="col col-xs col-logo">
              <?php get_template_part('template-parts/fields/field--header_logo'); ?>
            </div>
            <!-- COL LOGO -->

            <!-- COL NAV -->
            <div class="col col-xs col-nav">
              <div class="row gap-half nested end-xs">
                <?php get_template_part('template-parts/header/header--utility_menu'); ?>
                <?php if($header_layout == 'header-one'):
                  echo wp_nav_menu( array(
                        'theme_location' => 'main-menu',
                        'container_class' => 'header-main-menu-wrap hide-xs show-lg' ) );
                endif; ?>
              </div>
            </div>
            <!-- COL NAV -->

          </div>
        </div>
      </div>
    </div>

    <?php if($header_layout == 'header-two'):
      echo '<div class="header-bottom hide-xs show-lg">';
      echo '<div class="wrap-x">';
      echo '<div class="inside pl pr">';
      echo wp_nav_menu( array(
            'theme_location' => 'main-menu',
            'container_class' => 'header-main-menu-wrap' ) );
      echo '</div>';
      echo '</div>';
      echo '</div>';
    endif; ?>

    <?php get_template_part('template-parts/header/header--search_overlay'); ?>

    <div id="header-shadow"></div>

  </header>
  <!-- HEADER END -->

  <main id="main">
