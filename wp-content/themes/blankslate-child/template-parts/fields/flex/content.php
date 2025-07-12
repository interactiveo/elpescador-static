<?php $content_width = get_sub_field('content_width'); ?>

<div class="wrap-x">
    <div class="inside pl pr">
        <div class="<?php if(!empty($content_width)): echo $content_width; endif; ?>">
            <article class="body-area">
                <?php $content_col = null; ?>
                <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>
            </article>
        </div>
    </div>
</div>