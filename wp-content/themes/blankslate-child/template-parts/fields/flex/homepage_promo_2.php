<?php $side_image = get_sub_field('side_image'); ?>

<div class="wrap-x">
    <div class="inside">
        <div class="row middle-md between-md">


            <?php $content_col = 'col-xs-12 col-md-7 col-lg-6'; ?>
            <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>

            <?php if(!empty($side_image)): ?>
            <div class="col col-xs-12 col-md-5">
                <div class="img-wrap m-auto">
                    <img src="<?php echo $side_image['sizes']['medium']; ?>" alt="<?php echo $side_image['alt']; ?>"
                        class="no-touch select-none" />
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>