</main>

<footer id="footer">
    <div class="wrap-x">
        <div class="inside">
            <div class="row gap-half footer-main-row center-xs start-sm">

                <div class="col col-xs-12 col-sm-6 col-md col-footer-1">
                    <div class="row flush">
                        <?php get_template_part('template-parts/fields/field--footer_logo'); ?>
                        <?php if($ffooter_show_address = get_field('footer_show_address','options') == '1'): echo '<div class="col col-xs-12">'; get_template_part('template-parts/fields/field--theme_address'); echo '</div>'; endif; ?>
                        <?php if($footer_show_phone = get_field('footer_show_phone','options') == '1'): echo '<div class="col col-xs-12">'; get_template_part('template-parts/fields/field--theme_phone'); echo '</div>'; endif; ?>
                        <?php if($footer_show_fax = get_field('footer_show_fax','options') == '1'): echo '<div class="col col-xs-12">'; get_template_part('template-parts/fields/field--theme_fax'); echo '</div>'; endif; ?>
                        <div class="col col-xs-12"><?php echo do_shortcode('[badge-images]'); ?></div>
                    </div>
                </div>

                <?php if($footer_show_main_menu = get_field('footer_show_main_menu','options') == '1'): ?>
                <div class="col col-xs-12 col-sm-6 col-md col-footer-2">
                    <?php echo wp_nav_menu(array('theme_location' => 'main-menu','container_class' => 'main-menu-in-footer-wrap' )); ?>
                </div>
                <?php endif; ?>

                <div class="col col-xs-12 col-md col-footer-3">
                    <div class="custom-cc-form">
                        <p class="intro">Sign up for our newsletter to find out about the latest updates and offers at El Pescador</p>
                        <!-- Begin Constant Contact Inline Form Code -->
                        <div class="ctct-inline-form" data-form-id="72cad349-6b62-4ace-b850-012043533c35"></div>
                        <!-- End Constant Contact Inline Form Code -->
                    </div>


                </div>

            </div>

            <div class="row mt gap-half compact middle-xs footer-bottom-row">
                <div class="col col-xs-12">
                    <hr class="gap">
                </div>
                <div class="col col-xs">
                    <?php get_template_part('template-parts/fields/field--footer_copyright'); ?>
                </div>
                <?php echo wp_nav_menu( array('theme_location' => 'footer-menu','container_class' => 'footer-menu-wrap' )); ?>
                <div class="col col-xs col-social">
                    <?php get_template_part('template-parts/fields/field--theme_social_media'); ?></div>
            </div>

        </div>
    </div>
</footer>

<?php wp_footer(); ?>

<!-- Begin Constant Contact Active Forms -->
<script>
var _ctct_m = "6d6184cea0ae2ebc4e1e68a953a03e92";
</script>
<script id="signupScript" src="//static.ctctcdn.com/js/signup-form-widget/current/signup-form-widget.min.js" async
    defer></script>
<!-- End Constant Contact Active Forms -->

</body>

</html>