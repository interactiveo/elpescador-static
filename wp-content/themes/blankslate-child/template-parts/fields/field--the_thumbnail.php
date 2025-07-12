<?php

if($thumbnail = get_post_thumbnail_id( $post->ID )):

  $image_alt = get_post_meta($thumbnail, '_wp_attachment_image_alt', true);
  $image_url = get_the_post_thumbnail_url(null,'large');

  $image_crop = get_field('image_crop');

  if($custom_crop_height = get_field('custom_crop_height')):
  else:
  $custom_crop_height = '100px';
  endif;

  if($image_crop == 'auto' || $image_crop == null):
    $image_out = '<div class="field--the_thumbnail featured--image auto"><img src="'.$image_url.'" alt="'.$image_alt.'" class="m-auto no-touch select-none" /></div>';
  elseif($image_crop == 'wide'):
    $image_out = '<div class="field--the_thumbnail featured--image wide border-radius"><div class="object-cover-wrap"><img src="'.$image_url.'" alt="'.$image_alt.'" class="m-auto no-touch select-none" /></div></div>';
  elseif($image_crop == 'square'):
    $image_out = '<div class="field--the_thumbnail featured--image square border-radius"><div class="object-cover-wrap"><img src="'.$image_url.'" alt="'.$image_alt.'" class="m-auto no-touch select-none" /></div></div>';
  elseif($image_crop == 'rectangle'):
    $image_out = '<div class="field--the_thumbnail featured--image rectangle border-radius"><div class="object-cover-wrap"><img src="'.$image_url.'" alt="'.$image_alt.'" class="m-auto no-touch select-none" /></div></div>';
  elseif($image_crop == 'custom'):
    $image_out = '<div class="field--the_thumbnail featured--image border-radius" style="height:'.$custom_crop_height.'px"><div class="object-cover-wrap"><img src="'.$image_url.'" alt="'.$image_alt.'" class="m-auto no-touch select-none" /></div></div>';
  elseif($image_crop == 'hide'):
    $image_out = null;
  endif;

  echo $image_out;

endif;

 ?>
