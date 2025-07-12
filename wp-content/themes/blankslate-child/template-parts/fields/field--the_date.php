<?php

if($date = get_the_date()):
  if($date2 = get_the_date('m-d-Y')):
    echo '<time class="post-date" datetime="'.$date2.'">'.$date.'</time>';
  endif;
endif;

?>
