<?php

get_header();

if(is_404()):
  get_template_part('template-parts/error/error-404');
endif;

if ( have_posts() ):
while ( have_posts() ): the_post();

if ( post_password_required() ):
  get_template_part('template-parts/fields/field--password_field');
else:
  include('template-parts/hero/hero.php');
  include('template-parts/fields/field--flexible_fields.php');
endif;

endwhile;
endif;

get_footer();

?>
