<?php

if ( is_singular('blog') ): dynamic_sidebar( 'blog-widget' );

elseif ( is_singular('news') ): dynamic_sidebar( 'news-widget' );

elseif ( is_singular('faq') ): dynamic_sidebar( 'faq-widget' );

elseif ( is_singular('team') ): dynamic_sidebar( 'team-widget' );

elseif ( is_singular('career') ): dynamic_sidebar( 'career-widget' );

else: dynamic_sidebar( 'primary-widget-area' );

endif;

?>
