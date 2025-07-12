<?php

  if($not_found_title = get_field('page_not_found_title', 'options')):
  else:
  $not_found_title = 'Page Not Found';
  endif;

  if($page_not_found_message = get_field('page_not_found_message', 'options')):
  else:
  $page_not_found_message = 'The link you followed may be broken, or the page may have been removed.';
  endif;

?>

<header class="page-hero bg-green-dark color-white-base relative hero-layout-default">
    <div class="wrap-x">
        <div class="inside pl pr center-xs">
            <hgroup class="inside mini block body-area flex-padding">
                <h1 class="color-white-base error-h1"><?php echo $not_found_title; ?></h1>
                <?php echo $page_not_found_message; ?>
            </hgroup>
        </div>
    </div>
    <div class="object-cover-wrap">
        <img src="/wp-content/themes/blankslate-child/img/webp/promo_bkg.webp" alt="Background Image" />
    </div>
</header>

<div class="flex-padding">
    <div class="wrap-x">
        <div class="inside pl pr">
            <div class="inside mini body-area small-gap">
                <h2 class="color-white-base h5">Search:</h2>
                <?php echo get_search_form(); ?>
                <ul class="three-col start-xs pt">
                    <li><a style="color:white;" href="/">Home</a></li>
                    <li><a style="color:white;" href="/sitemap/">Sitemap</a></li>
                    <li><a style="color:white;" href="/contact-us/">Contact</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>