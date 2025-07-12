<?php
// Standard Variables
$args = array(
    'post_type'      => $post_type_selection,
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'meta_key'       => 'fromdate',
    'orderby'        => 'meta_value', // Use 'meta_value_num' if it's numeric
    'order'          => 'DESC', // or 'DESC' depending on your desired sort direction
);

$query = new WP_Query($args);

$pager_args = array(
    'base'      => get_pagenum_link(1) . '%_%',
    'current'   => $paged,
    'total'     => $query->max_num_pages,
    'prev_text' => 'Previous',
    'next_text' => 'Next',
);
?>

<?php if ($query->have_posts()) { ?>
<?php while ($query->have_posts()) : $query->the_post(); ?>
<?php get_template_part('template-parts/includes/post_type_entry/post_type_entry', $post_type_selection); ?>
<?php endwhile; ?>
<?php wp_reset_postdata(); ?>
<?php }; ?>

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