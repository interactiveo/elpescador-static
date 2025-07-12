<?php $title = get_the_title(); ?>
<?php $excerpt = get_the_excerpt(); ?>
<?php $permalink = get_the_permalink() ?>
<?php $link_directly_to_external_link = get_field('link_directly_to_external_link'); ?>
<?php $adventure_external_url = get_field('adventure_external_url'); ?>
<?php $target = '_self'; ?>

<?php
if (!empty($link_directly_to_external_link) && $link_directly_to_external_link == '1') {
    $permalink = $adventure_external_url;
    $target = '_blank';
}
?>



<?php 
if($thumbnail = get_post_thumbnail_id( $post->ID )) {
    $image_alt = get_post_meta($thumbnail, '_wp_attachment_image_alt', true);
    $image_url = get_the_post_thumbnail_url(null,'large');
} else {
    $image_alt = 'El Pescador Lodge and Villas';
    $image_url = '/wp-content/themes/blankslate-child/img/placeholder-logo-wide.png';
}
?>

<div class="col col-xs-12 col-md-6 col-lg-4 body-area small-gap">

    <?php if(!empty($permalink)): ?>
    <a href="<?php echo $permalink; ?>" class="block relative" target="<?php echo $target; ?>"
        title="<?php echo $title; ?>">
        <div class="featured--image wide border-radius bg-black-shade1">
            <div class="object-cover-wrap">
                <img src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>" class="border-radius" />
            </div>
        </div>
    </a>
    <?php endif; ?>


    <h3 class="post-title h4">
        <?php if(!empty($permalink)): ?>
        <a href="<?php echo $permalink; ?>" target="<?php echo $target; ?>"
            class="post-title-link inline-block"><?php echo $title; ?></a>
        <?php else: ?>
        <?php echo $title; ?>
        <?php endif; ?>
    </h3>
    <?php if(!empty($excerpt)): echo '<p class="excerpt">'.$excerpt.'</p>'; endif; ?>
</div>