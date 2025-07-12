<?php
if($header_logo = get_field( 'header_logo', 'options' )):
  echo '
  <div class="field--header_logo">
  <a href="'.get_home_url().'" title="'.get_bloginfo( 'name' ).'">
    <img src="'.$header_logo['sizes']['small'].'" alt="'.$header_logo['alt'].'" class="no-touch select-none no-lazy" border="0" width="" height="" />
  </a>
  </div>
  ';
endif;
?>
