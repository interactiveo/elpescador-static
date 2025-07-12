<?php $title = get_the_title(); ?>
<?php $content = get_the_content(); ?>

<?php include get_stylesheet_directory() . '/template-parts/hero/hero.php'; ?>

<div class="flex-margin">
    <div class="wrap-x">
        <div class="inside pl pr">
            <article class="body-area">

                <?php if(!empty($content)): ?>
                    <?php get_template_part('template-parts/fields/field--the_content'); ?>
                <?php endif; ?>

            </article>
        </div>
    </div>
</div>