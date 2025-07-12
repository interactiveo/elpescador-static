<?php
// Adventures Variables
$adventure_category = get_sub_field('adventure_category');

$adventures_subcategory = get_terms([
    'taxonomy' => 'adventures_subcategory',
    'hide_empty' => true,
        'orderby'    => 'term_order',
        'order'      => 'ASC',
]);
$uncategorized_query = new WP_Query([
    'post_type' => 'adventures',
    'tax_query' => [
        'relation' => 'AND',
        [
            'taxonomy' => 'adventures_subcategory',
            'operator' => 'NOT EXISTS',
        ],
        [
            'taxonomy' => 'adventures_category',
            'field'    => 'term_id',
            'terms'    => $adventure_category,
        ],
    ],
]);
?>

<?php if (!empty($adventures_subcategory) && !is_wp_error($adventures_subcategory)) { ?>
<?php foreach ($adventures_subcategory as $term) { ?>

<?php 
        $adventures_query = new WP_Query([
            'post_type'      => 'adventures',
            'posts_per_page' => $posts_per_page,
            'tax_query'      => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'adventures_subcategory',
                    'field'    => 'slug',
                    'terms'    => $term->slug,
                ],
                [
                    'taxonomy' => 'adventures_category',
                    'field'    => 'term_id',
                    'terms'    => $adventure_category,
                ],
            ],
        ]); 
        ?>
<?php if ($adventures_query->have_posts()) { ?>
<div class="col col-xs-12">
    <div class="row nested">
        <div class="col col-xs-12">
            <h2 id="adventures-<?php echo $term->slug; ?>" class=""><?php echo $term->name; ?></h2>

        </div>
        <?php while ($adventures_query->have_posts()) : $adventures_query->the_post(); ?>
        <?php get_template_part('template-parts/includes/post_type_entry/post_type_entry', $post_type_selection); ?>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    </div>
</div>
<?php }; ?>

<?php }; ?>
<?php }; ?>

<?php if ($uncategorized_query->have_posts()) { ?>
<div class="col col-xs-12">
    <div class="row nested row--uncategorized_query">
        <div class="col col-xs-12">
            <h2 id="adventures-uncategorized" class="">Uncategorized</h2>
        </div>
        <?php while ($uncategorized_query->have_posts()): $uncategorized_query->the_post(); ?>
        <?php get_template_part('template-parts/includes/post_type_entry/post_type_entry', $post_type_selection); ?>
        <?php endwhile; ?>
    </div>
</div>
<?php wp_reset_postdata(); ?>
<?php }; ?>