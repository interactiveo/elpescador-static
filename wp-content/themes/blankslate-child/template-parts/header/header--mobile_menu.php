<div id="header-overlay" class="modal-window">
  <div class="inner">
    <div class="m-auto">
    <div class="wrap-x">
    <div class="max-width">

    <?php
    if( function_exists('bellows')):
      bellows( 'main' , array( 'menu' => 5 ) );
    endif;
    ?>

<?php
if($utility_menu = get_field('utility_menu', 'options')):
endif;

if($show_phone = get_field('show_phone', 'options') == '1'):
  if($phone_number = get_field('phone_number','options')):
  $phone_number_stripped = preg_replace('~\D~', '', $phone_number);
  $show_phone_out = '<li class="icon-phone2"><a href="tel:'.$phone_number_stripped.'">'.$phone_number.'</a></li>';
  endif;
endif;


if ( have_rows( 'utility_menu', 'options' ) ) :
  echo '<div class="header-utility-menu">';
  echo '<ul class="menu">';
while ( have_rows( 'utility_menu', 'options' ) ) : the_row();
  if($link = get_sub_field('link')):
    $icon_class = get_sub_field('icon_class');
    $show_on = get_sub_field('show_on');
    echo '
    <li class=" '.$icon_class.' ">
    <a href="'.$link['url'].'" target="_self">'.$link['title'].'</a>
    </li>
    ';
  endif;
endwhile;
if($show_phone_out): echo $show_phone_out; endif;
echo '</ul>';
echo '</div>';
endif;
?>

    </div>
    </div>
    </div>
  </div>
</div>
