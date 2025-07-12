<?php $vertical_alignment = get_sub_field('vertical_alignment'); ?>

<div class="wrap-x">
    <div class="inside">
        <div class="row 
        <?php if(!empty($vertical_alignment)): echo $vertical_alignment; endif; ?>
        ">

            <!-- COL TEXT -->
            <?php if( have_rows('content_group') ): ?>
                    <?php while( have_rows('content_group') ): the_row(); ?>
                    <?php $content_col = 'col col-xs-12 col-md-6 col-md-grow start-xs'; ?>
                    <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>
                    <?php endwhile; ?>
                    <?php endif; ?>
            <!-- COL TEXT -->

            <!-- COL IMG -->
            <?php if( have_rows('image_group') ): ?>
            <?php while( have_rows('image_group') ): the_row(); ?>
            <?php $image_position = get_sub_field('image_position'); ?>
            <?php $image_width = get_sub_field('image_width'); ?>
            <div class="col col-xs-12 start-xs <?php if(!empty($image_width)): echo $image_width; endif; ?> <?php if(!empty($image_position)): echo $image_position; endif; ?>">
                <?php include get_stylesheet_directory() . '/template-parts/includes/featured_image.php'; ?>
            </div>
            <?php endwhile; ?>
            <?php endif; ?>
            <!-- COL IMG -->

        </div>
    </div>
</div>