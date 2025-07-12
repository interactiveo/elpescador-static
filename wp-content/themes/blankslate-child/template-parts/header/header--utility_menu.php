<?php
if($utility_menu = get_field('utility_menu', 'options')):
endif;

$phone_number = null;
$phone_number_strippe = null;
$show_phone_out = null;
if($show_phone = get_field('show_phone', 'options') == '1'):
  if($phone_number = get_field('phone_number','options')):
  $phone_number_stripped = preg_replace('~\D~', '', $phone_number);
  $show_phone_out = '<li class="icon-phone2"><a href="tel:'.$phone_number_stripped.'">'.$phone_number.'</a></li>';
  endif;
endif;

$show_search_out = null;
if($show_search = get_field('show_search', 'options') == '1'):
  $show_search_out = '<li class="hide-xs show-sm icon-search"><span id="header-search">Search</span></li>';
endif;
 ?>

<div class="header-utility-menu-wrap">
  <ul class="menu">
    <?php if($show_search_out): echo $show_search_out; endif; ?>
    <?php if($utility_menu):
      if ( have_rows( 'utility_menu', 'options' ) ) :
      while ( have_rows( 'utility_menu', 'options' ) ) : the_row();
        if($link = get_sub_field('link')):
          $icon_class = get_sub_field('icon_class');
          $show_on = get_sub_field('show_on');
          echo '
          <li class="hide-xs '.$icon_class.'
          ';
          foreach($show_on as $show_on_out):
            echo ' '.$show_on_out.' ';
          endforeach;
          echo '
          ">
          <a href="'.$link['url'].'" target="'.$link['target'].'">'.$link['title'].'</a>
          </li>
          ';
        endif;
      endwhile;
      endif;
    endif; ?>
    <?php if($show_phone_out): echo $show_phone_out; endif; ?>
    <li class="hide-lg">
      <button class="relative overflow-hidden" id="header-burger" aria-label="Menu" title="Open Main Menu">
        <div class="line line1"></div>
        <div class="line line2"></div>
        <div class="line line3"></div>
        <div class="cross-wrap">
          <div class="cross cross1"></div>
          <div class="cross cross2"></div>
        </div>
      </button>
    </li>
  </ul>
</div>
