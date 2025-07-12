<?php

if($read_more_label = get_field('read_more_label', 'options')):
else:
  $read_more_label = 'Read More';
endif;

$postType = get_post_type_object(get_post_type());
$postTypeName = $postType->name;
$title = get_the_title();
$url = get_the_permalink();
$hero_h2 = get_field('hero_h2');
$excerpt = get_the_excerpt();

?>

<div class="search--card inner-background pt2x pb2x pl2x pr2x">
    <div class="body-area small-gap">
        <h3 class="search-title">
            <a href="<?php if(!empty($url)): echo $url; endif; ?>" target="_self">
                <?php if(!empty($title)): echo $title; endif; ?>
            </a>
        </h3>
        <?php if(!empty($hero_h2)): ?><p class="excerpt"><?php echo $hero_h2; ?></p>
        <?php elseif(!empty($excerpt)): ?><p class="excerpt"><?php echo $excerpt; ?></p>
        <?php endif; ?>
    </div>
</div>