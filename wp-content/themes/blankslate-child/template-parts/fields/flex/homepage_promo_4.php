<div class="wrap-x">
    <div class="inside">
        <div class="row main--row">


            <!-- TOP CONTENT -->
            <?php if( have_rows('top_content') ): ?>
            <?php while( have_rows('top_content') ): the_row(); ?>
            <?php $content_col = 'col-xs-12 center-xs'; ?>
            <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>
            <?php endwhile; ?>
            <?php endif; ?>

            <div class="col col-xs-12">
                    <script src="https://static.elfsight.com/platform/platform.js" async></script>
                    <div class="elfsight-app-39893c2d-f033-41ea-8ebc-7f150eb574a7" data-elfsight-app-lazy></div>
            </div>

            <?php if( have_rows('promo_content') ): ?>
            <?php while( have_rows('promo_content') ): the_row(); ?>
            <div class="col col-xs-12 center-xs">
                <?php include get_stylesheet_directory() . '/template-parts/includes/promo.php'; ?>
            </div>
            <?php endwhile; ?>
            <?php endif; ?>


        </div>
    </div>
</div>