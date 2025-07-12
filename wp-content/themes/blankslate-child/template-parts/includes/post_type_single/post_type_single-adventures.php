<?php $content = get_the_content(); ?>
<?php $adventure_external_url = get_field('adventure_external_url'); ?>
<?php $selling_points = get_field('selling_points'); ?>
<?php $adventure_category = get_field('adventure_category'); ?>
<?php $link_directly_to_external_link = get_field('link_directly_to_external_link'); ?>

<?php
if (
    !empty($link_directly_to_external_link) &&
    $link_directly_to_external_link == '1' &&
    !empty($adventure_external_url)
) {
    wp_redirect($adventure_external_url);
    exit;
}
?>

<?php include get_stylesheet_directory() . '/template-parts/hero/hero.php'; ?>

<div class="flex-margin">
    <div class="wrap-x">
        <div class="inside">
            <div class="row between-md">

                <?php if(!empty($content || $adventure_external_url)): ?>
                <div class="col col-xs-12 col-md-6 body-area">

                    <?php if(!empty($content)): ?>
                    <?php get_template_part('template-parts/fields/field--the_content'); ?>
                    <?php endif; ?>

                    <?php if(!empty($adventure_external_url)): ?>
                    <p>
                        <a href="<?php echo $adventure_external_url; ?>" class="btn" target="_blank" role="button">View
                            All Belize Adventures</a>
                    </p>
                    <?php endif; ?>


                </div>
                <?php endif; ?>

                <div class="col col-xs-12 col-md-6 col-lg-5">
                    <dl class="selling-points">

                        <?php if(!empty($selling_points)): ?>
                        <?php if( have_rows('selling_points') ): ?>
                        <?php while( have_rows('selling_points') ) : the_row(); ?>
                        <?php $label = get_sub_field('label'); ?>
                        <?php $description = get_sub_field('description'); ?>
                        <dt><?php echo $label; ?></dt>
                        <dd><?php echo $description; ?></dd>
                        <hr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                        <?php endif; ?>

                        <dt>Category</dt>
                        <dd>
                            <?php
                            if (!empty($adventure_category)) {
                                $term = get_term($adventure_category, 'adventures_category'); // Replace with your taxonomy slug
                                if (!is_wp_error($term)) {
                                    echo $term->name;
                                }
                            }
                            ?>
                        </dd>



                    </dl>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="flex-margin">
    <?php get_template_part('template-parts/fields/flex/promo'); ?>
</div>