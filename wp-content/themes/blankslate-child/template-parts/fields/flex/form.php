<?php $form_selection = get_sub_field('form_selection'); ?>
<?php $side_text_area = get_sub_field('side_text_area'); ?>
<?php $content_col = 'col-xs-12'; ?>

<div class="wrap-x">
    <div class="inside">
        <div class="row">

            <!-- CONTENT -->
            <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>
            <!-- CONTENT -->

            <!-- FORM -->
            <?php if(!empty($form_selection)): ?>
            <div class="col col-xs-12 col-md-7 col-md-grow">
                <?php if (function_exists('gravity_form')) { gravity_form($form_selection['id']); } ?>
            </div>
            <?php endif; ?>
            <!-- FORM -->

            <!-- SIDE CONTENT -->
            <?php if(!empty($side_text_area)): ?>
            <?php $side_text_area = preg_replace('/<p[^>]*>(?:\s|&nbsp;)*<\/p>/', '', $side_text_area); ?>
            <div class="col col-xs-12 col-md-5">
                <article class="body-area">
                    <?php echo $side_text_area; ?>
                </article>
            </div>
            <?php endif; ?>
            <!-- SIDE CONTENT -->

        </div>
    </div>
</div>