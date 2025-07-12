<?php $content_width = get_sub_field('content_width'); ?>

<div class="wrap-x">
    <div class="inside pl pr">
        <div class="<?php if(!empty($content_width)): echo $content_width; endif; ?>">
            <?php include get_stylesheet_directory() . '/template-parts/includes/featured_image.php'; ?>
        </div>
    </div>
</div>