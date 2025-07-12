<?php
if($footer_logo = get_field( 'footer_logo', 'options' )):
  echo '
  <div class="col col-xs-12 mb">
    <div class="field--footer_logo_wrap">
      <div class="field--footer_logo">
      <a href="'.get_home_url().'" title="'.get_bloginfo( 'name' ).'">
        <img src="'.$footer_logo['sizes']['small'].'" alt="'.$footer_logo['alt'].'" class="no-touch select-none no-lazy" border="0" width="" height="" />
      </a>
      </div>
    </div>
  </div>
  ';
endif;
?>
