<?php

if($text_area = get_the_content()):
  $text_area = preg_replace('/<p[^>]*>(?:\s|&nbsp;)*<\/p>/', '', $text_area);
  echo '
  <article class="body-area">
  '.$text_area.'
  </article>
  ';
endif;

?>
