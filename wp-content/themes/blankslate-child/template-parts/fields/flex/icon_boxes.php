<?php $icon_boxes = get_sub_field('icon_boxes'); ?>

<div class="wrap-x">
    <div class="inside">
        <div class="row gap-half compact">

            <!-- CONTENT -->
            <?php $content_col = 'col-xs-12 pb'; ?>
            <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>
            <!-- CONTENT -->

            <!-- ICON BOXES -->
            <?php if(!empty($icon_boxes)): ?>
            <?php if( have_rows('icon_boxes') ): ?>
            <?php while( have_rows('icon_boxes') ) : the_row(); ?>
            <?php $icon = get_sub_field('icon'); ?>
            <?php $label = get_sub_field('label'); ?>
            <?php $text_area = get_sub_field('text_area'); ?>
            <?php $cta = get_sub_field('cta'); ?>

            <!-- COL -->
            <div class="col col-xs-12 col-md-6">
                <div class="icon-box-wrapper bg-white-shade1 border-radius h-100">
                    <article class="body-area">

                        <!-- HEADER -->
                        <header class="icon-box-header">
                            <?php if(!empty($icon)): ?>
                            <!-- ICON -->
                            <img src="<?php echo $icon['sizes']['xsmall']; ?>" alt="<?php echo $icon['alt']; ?>" class="icon-box-img no-touch select-none" />
                            <?php endif; ?>

                            <?php if(!empty($label)): ?>
                            <h3 class="label">
                                <?php echo $label; ?>
                            </h3>
                            <?php endif; ?>
                        </header>
                        <!-- HEADER -->

                        <?php if(!empty($text_area)): ?>
                        <?php $text_area = preg_replace('/<p[^>]*>(?:\s|&nbsp;)*<\/p>/', '', $text_area); ?>
                        <?php echo $text_area; ?>
                        <?php endif; ?>

                        <?php if(!empty($cta)): ?>
                        <p class="cta">
                            <a href="<?php echo $cta['url']; ?>" class="btn" target="<?php echo $cta['target']; ?>"
                                role="button" title="<?php echo $cta['title']; ?>">
                                <?php echo $cta['title']; ?>
                            </a>
                        </p>
                        <?php endif; ?>

                    </article>
                </div>
            </div>
            <!-- COL -->

            <?php endwhile; ?>
            <?php endif; ?>
            <?php endif; ?>
            <!-- ICON BOXES -->

        </div>
    </div>
</div>