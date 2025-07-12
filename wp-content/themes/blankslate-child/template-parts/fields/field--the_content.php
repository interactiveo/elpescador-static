<?php

if ( get_the_content() ) :

  // grab the fully‑filtered HTML (shortcodes + autop + embeds)
  $text_area = apply_filters( 'the_content', get_the_content() );

  // strip any empty <p> tags, if you still need that…
  $text_area = preg_replace( '/<p[^>]*>(?:\s|&nbsp;)*<\/p>/', '', $text_area );

  echo '
  <article class="body-area">
    ' . $text_area . '
  </article>
  ';

endif;
