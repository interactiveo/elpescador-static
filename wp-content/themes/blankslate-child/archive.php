<?php get_header(); ?>

<?php include('template-parts/hero/hero.php'); ?>

<div class="flex-margin">
    <div class="wrap-x">
        <div class="inside">
            <div class="row">

                <?php if ( have_posts() ): ?>
                <?php while ( have_posts() ): the_post(); ?>
                <?php $post_type = get_post_type(); ?>
                <?php get_template_part('template-parts/includes/post_type_entry/post_type_entry', $post_type); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
                <?php the_posts_pagination( array('mid_size'  => 4,'screen_reader_text' => ' ','prev_text' => __( 'Previous', 'textdomain' ),'next_text' => __( 'Next', 'textdomain' )));?>
				<?php endif; ?>

            </div>
        </div>
    </div>
</div> 

<?php get_footer(); ?>