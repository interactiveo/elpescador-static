<?php 
// FAQ Variables
$faq_terms = get_terms([
    'taxonomy' => 'faq_category',
    'hide_empty' => true,
]);
$uncategorized_query = new WP_Query([
    'post_type' => 'faq',
    'tax_query' => [
        [
            'taxonomy' => 'faq_category',
            'operator' => 'NOT EXISTS',
        ],
    ],
]);
?>

<?php if (!empty($faq_terms) && !is_wp_error($faq_terms)) { ?>
<?php foreach ($faq_terms as $term) { ?>
<div class="col col-xs-12">
    <div class="row nested gap-quarter">
        <div class="col col-xs-12">
            <h2 id="faq-<?php echo $term->slug; ?>" class="h3 lh-compact"><?php echo $term->name; ?></h2>
        </div>

        <?php $faq_query = new WP_Query([
                    'post_type' => 'faq',
                    'tax_query' => [
                        [
                            'taxonomy' => 'faq_category',
                            'field'    => 'slug',
                            'terms'    => $term->slug,
                            'posts_per_page' => $posts_per_page,
                        ],
                    ],
                ]); ?>
        <?php if ($faq_query->have_posts()) { ?>
        <?php while ($faq_query->have_posts()) : $faq_query->the_post(); ?>
        <?php get_template_part('template-parts/includes/post_type_entry/post_type_entry', $post_type_selection); ?>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
        <?php }; ?>
    </div>
</div>
<?php }; ?>
<?php }; ?>

<?php if ($uncategorized_query->have_posts()) { ?>
<div class="col col-xs-12">
</div>
<div class="col col-xs-12">
    <div class="row nested row--uncategorized_query gap-quarter">
        <?php while ($uncategorized_query->have_posts()): $uncategorized_query->the_post(); ?>
        <?php get_template_part('template-parts/includes/post_type_entry/post_type_entry', $post_type_selection); ?>
        <?php endwhile; ?>
    </div>
</div>
<?php wp_reset_postdata(); ?>
<?php }; ?>