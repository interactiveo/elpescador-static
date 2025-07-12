<?php $title = get_the_title(); ?>
<?php $content = get_the_content(); ?>
<?php $date = get_the_date(); ?>
<?php $date2 = get_the_date('m-d-Y'); ?>

<?php include get_stylesheet_directory() . '/template-parts/hero/hero.php'; ?>

<div class="flex-margin">
    <div class="wrap-x">
        <div class="inside pl pr">
            <article class="body-area">

                <h1><?php echo $title; ?></h1>

                <time class="date" datetime="<?php echo $date2; ?>"><?php echo $date; ?></time>

                <?php if(!empty($content)): ?>
                    <?php get_template_part('template-parts/fields/field--the_content'); ?>
                <?php endif; ?>

            </article>
        </div>
    </div>
</div>