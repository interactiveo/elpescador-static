<?php $vertical_alignment = get_sub_field('vertical_alignment'); ?>

<div class="wrap-x">
    <div class="inside">
        <div class="row main--row <?php if(!empty($vertical_alignment)): echo $vertical_alignment; endif; ?>">

            <?php if( have_rows('columns_content') ): ?>
            <?php while( have_rows('columns_content') ) : the_row(); ?>
            <?php $column_width = get_sub_field('column_width'); ?>
            <?php $teal_box = get_sub_field('teal_box'); ?>
            
            <?php if(!empty($column_width)): 
            $content_col = 'content-col col col-xs-12 '.$column_width.''; 
            endif; ?>
            
            <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>
            <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </div>
</div>