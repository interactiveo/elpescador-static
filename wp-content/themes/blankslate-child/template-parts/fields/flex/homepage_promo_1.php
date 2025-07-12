<?php $credability_text = get_sub_field('credability_text'); ?>

<div class="wrap-x">
    <div class="inside">
        <div class="row row--main">

            <!-- CREDABILITY -->
            <div class="col col-xs-12">
                <div class="row nested">

                    <?php if(!empty($credability_text)): ?>
                    <div class="col col-xs-12">
                        <article class="body-area center-xs">
                            <?php echo $credability_text; ?>
                        </article>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <!-- CREDABILITY -->

            <div class="col col-xs-12">
                <article class="body-area bg-white-base pt2x pb2x pl2x pr2x border-radius white-content-box">
                    <?php $content_col = null; ?>
                    <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>
                </article>
            </div>

        </div>
    </div>
</div>