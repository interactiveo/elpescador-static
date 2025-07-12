<?php $title = get_the_title(); ?>
<?php $content = get_the_content(); ?>
<?php include get_stylesheet_directory() . '/template-parts/hero/hero.php'; ?>

<?php get_template_part('template-parts/fields/field--flexible_fields'); ?>

<?php if(!empty($content)): ?>
<div class="flex-margin">
    <div class="wrap-x">
        <div class="inside pl pr">
            <article class="body-area">
                <?php get_template_part('template-parts/fields/field--the_content'); ?>
            </article>
        </div>
    </div>
</div>
<?php endif; ?>