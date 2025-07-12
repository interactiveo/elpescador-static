<?php $feed_option = get_sub_field('feed_option'); ?>
<?php $post_type_selection = get_sub_field('post_type_selection'); ?>
<?php $manual_post_selection = get_sub_field('manual_post_selection'); ?>
<?php $posts_per_page = get_sub_field('posts_per_page'); ?>
<?php $optional_search_filter_id = get_sub_field('optional_search_filter_id'); ?>
<?php $disable_pager = get_sub_field('disable_pager'); ?>

<?php if(empty($optional_search_filter_id)): $optional_search_filter_id = null; endif; ?>
<?php if(empty($posts_per_page)): $posts_per_page = -1; endif; ?>

<?php 
// Automated Feed Arrays
if($feed_option == 'automated'):

$paged = max(1, get_query_var('paged'), get_query_var('page'));

// Standard Variables
$args = array(
    'post_type'      => $post_type_selection, // Your custom post type
    'posts_per_page' => $posts_per_page,          // Number of posts to display
    'paged'          => $paged, // Current page
    'search_filter_id' => $optional_search_filter_id, // Search and Filter Pro ID (OPTIONAL)
);

$query = new WP_Query($args);

$pager_args = array(
    'base'      => get_pagenum_link(1) . '%_%',
    'current'   => $paged,
    'total'     => $query->max_num_pages,
    'prev_text' => 'Previous',
    'next_text' => 'Next',
);
endif;
// Automated Feed Arrays
?>

<div class="post-type-selection--<?php echo $post_type_selection; ?>">
    <div class="wrap-x">
        <div class="inside">
            <div class="row row--query">

                <!-- CONTENT -->
                <?php $content_col = 'col-xs-12'; ?>
                <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>
                <!-- CONTENT -->


                <?php 
                // EVENT ONLY!
                // CLOSE FLEXBOX-GRID AND OPEN SWIPER
                if($post_type_selection == 'event') {
                echo '<div class="col col-xs-12">';
                echo '<div class="swiper-margins">';
                echo '<div class="swiper" id="dragScroll">';
                echo '<div class="swiper-wrapper">';
                };
                ?>

                <?php if(!empty($feed_option)) { ?>

                <?php if($feed_option == 'automated') { ?>
                <!-- AUTOMATED START -->

                <!-- SEARCH AND FILTER -->
                <?php if(!empty($optional_search_filter_id)) { echo '<div class="col col-xs-12">'.do_shortcode('[searchandfilter id="'.$optional_search_filter_id.'"]').'</div>'; }; ?>
                <!-- SEARCH AND FILTER -->

                <!-- FAQ START -->
                <?php if($post_type_selection == 'faq') { ?>
                <?php include get_stylesheet_directory() . '/template-parts/includes/post_type_query/post_type_query-faq.php'; ?>
                <!-- FAQ END -->

                <!-- REPORT START -->

                <?php } elseif($post_type_selection == 'report') { ?>
                <?php include get_stylesheet_directory() . '/template-parts/includes/post_type_query/post_type_query-report.php'; ?>
                <!-- REPORT END -->

                <!-- ADVENTURES START -->
                <?php } elseif($post_type_selection == 'adventures') { ?>
                <?php include get_stylesheet_directory() . '/template-parts/includes/post_type_query/post_type_query-adventures.php'; ?>
                <!-- ADVENTURES END -->

                <!-- BLOG START -->
                <?php } elseif($post_type_selection == 'blog') { ?>
                <?php include get_stylesheet_directory() . '/template-parts/includes/post_type_query/post_type_query-blog.php'; ?>
                <!-- BLOG END -->


                <!-- STANDARD QUERY START -->
                <?php } else { ?>
                <?php if ($query->have_posts()) { ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                <?php get_template_part('template-parts/includes/post_type_entry/post_type_entry', $post_type_selection); ?>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
                <?php }; ?>
                <!-- STANDARD QUERY START -->

                <!-- PAGER -->
                <?php if($disable_pager != 1){ ?>
                <?php if ($query->max_num_pages > 1){ ?>
                <div class="col col-xs-12">
                    <div class="pagination">
                    <div class="nav-links">
                        <?php echo paginate_links($pager_args); ?>
                    </div>
                    </div>
                </div>
                <?php }; ?>
                <?php }; ?>
                <!-- PAGER -->

                <!-- QUERY END -->
                <?php }; ?>

                <?php } elseif($feed_option == 'manual'){ ?>

                <!-- MANUAL -->
                <?php foreach( $manual_post_selection as $post ): setup_postdata($post); ?>
                <?php get_template_part('template-parts/includes/post_type_entry/post_type_entry', $post_type_selection); ?>
                <?php endforeach; ?>
                <?php wp_reset_postdata(); ?>
                <!-- MANUAL -->


                <?php }; ?>
                <?php }; ?>

                <?php 
                // EVENT ONLY!
                if($post_type_selection == 'event') {
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                };
                ?>

            </div>
        </div>
    </div>
</div>