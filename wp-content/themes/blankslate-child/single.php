<?php

$post_type = get_post_type();

get_header();


if ( have_posts() ):
while ( have_posts() ): the_post();

if ( post_password_required() ):
  get_template_part('template-parts/fields/field--password_field');
else:

  // LOAD POST TYPE TEMPLATE FILE
  get_template_part('template-parts/includes/post_type_single/post_type_single', $post_type); 

endif;

endwhile;
endif;

get_footer();

?>