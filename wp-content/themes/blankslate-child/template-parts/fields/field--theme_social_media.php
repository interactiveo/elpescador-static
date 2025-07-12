<?php
if ( have_rows( 'social_media', 'options' ) ) :
echo '<div class="social_media_menu">';
echo '<ul class="menu">';
while ( have_rows( 'social_media', 'options' ) ) : the_row();
$social = get_sub_field('social');
$url = get_sub_field('url');
echo '
<li>
<a href="'.$url.'" target="_blank" class="social-media-link"><span class="'.$social.'"></span></a>
</li>
';
endwhile;
echo '</ul>';
echo '</div>';
endif;
?>
