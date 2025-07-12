<?php if(have_rows('flexible_fields')) : ?>
<?php $flexibleContentPath = dirname(__FILE__) . '/flex/'; ?>
<?php $counter = 0; ?>
<div class="flexible-fields-wrap" itemprop="mainEntityOfPage">

    <!-- FLEX ROW -->
    <?php while (have_rows('flexible_fields') ) : the_row(); ?>
    <?php $layout = get_row_layout(); ?>
    <?php $file = ($flexibleContentPath . $layout . '.php'); ?>
    <?php //check ./template-files/fields/flex/ for all componets. ?>
    <?php if (file_exists($file)): ?>
    <?php $counter++; ?>

    <?php
    // ROW SETTING VARIABLES
    $fields = [
        'custom_css_classes',
        'background_image',
        'background_color',
        'text_color',
        'disable_top_spacing',
        'disable_bottom_spacing',
        'disable_top_wave',
        'keep_bottom_wave_space',
        'custom_css_id'
    ];
    foreach ($fields as $field) {
        ${$field} = get_sub_field($field);
    }
    ?>

    <?php 
    // RESET 
    $content_col = null;
    $content_col = 'bg-red-error'; //debugging remove later
    ?>

    <?php if($layout == 'homepage_promo_1'): ?>
    <?php $background_color = '#d0dadc'; ?>
    <?php endif; ?>

    <?php if($layout == 'promo'): ?>
    <?php $background_image = get_field('promo_background_image', 'options'); ?>
    <?php endif; ?>

    <section 
    class="<?php 
        $classes = [
            'flexible-fields-row',
            'flexible-fields-row-count-' . $counter,
            !empty($layout) ? 'flex-id-' . $layout : '',
            !empty($custom_css_classes) ? $custom_css_classes : '',
            !empty($background_image) ? 'bkg-img-true' : 'bkg-img-false',
            ($layout == 'image_section_seperator') ? 'bkg-color-true' : (!empty($background_color) ? 'bkg-color-true' : 'bkg-color-false'),
            !empty($text_color) ? 'txt-color-true' : 'txt-color-false',
            !empty($disable_top_spacing) ? 'mt0 pt0' : '',
            !empty($disable_bottom_spacing) ? 'mb0 pb0' : '',
            !empty($disable_top_wave) ? 'top-wave-false' : 'top-wave-true',
            !empty($keep_bottom_wave_space) ? 'bottom-wave-space-false' : 'bottom-wave-space-true',
        ];
        echo implode(' ', array_filter($classes));
    ?>"
    style="<?php 
        if (!empty($text_color)) {
            echo 'color:' . $text_color . ';';
        }
    ?>"
    <?php
    if (!empty($custom_css_id)) {
        // Sanitize the ID - lowercase, no spaces, only alphanumeric, hyphen, and underscore
        $sanitized_id = strtolower(preg_replace('/[^a-z0-9_-]/', '', str_replace(' ', '-', $custom_css_id)));
        echo 'id="' . esc_attr($sanitized_id) . '"';
    }
    ?>
>

        <?php // HOME ART ASSETS ?>
        <?php if(is_front_page()): ?>
            <?php if($layout == 'homepage_promo_2'): ?>
            <?php if($counter == '2'): ?>
            <div class="inside art-container" aria-hidden="true">
                <div class="homepage-art-1"><img
                        src="/wp-content/themes/blankslate-child/img/webp/homepage_promo_2_fish.webp" alt="Fish" /></div>
            </div>
            <?php endif; ?>

            <?php elseif($layout == 'menu'): ?>
            <?php if($custom_css_classes == 'fish-art-2'): ?>
            <div class="inside art-container" aria-hidden="true">
                <div class="homepage-art-2"><img src="/wp-content/themes/blankslate-child/img/homepage_art-2.png"
                        alt="Fisherman" /></div>
            </div>
            <?php elseif($custom_css_classes == 'fish-art-3'): ?>
            <div class="inside art-container" aria-hidden="true">
                <div class="homepage-art-3"><img src="/wp-content/themes/blankslate-child/img/homepage_art-3.png"
                        alt="Fish" /></div>
            </div>
            <?php endif; ?>


        <?php endif; //if layout ?>
        <?php endif; //is_front ?>
        <?php // HOME ART ASSETS ?>

        <?php if(!empty($file)): include($file); endif; ?>

        <?php // HOME ART ASSETS BOTTOM ?>
        <?php if(is_front_page()): ?>
        <?php if($layout == 'homepage_promo_3'): ?>
        <?php if($custom_css_classes == 'fish-art-4'): ?>
        <div class="inside art-container" aria-hidden="true">
            <div class="homepage-art-4"><img
                    src="/wp-content/themes/blankslate-child/img/homepage_art-4.png" alt="Hut" /></div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        <?php endif; ?>
        <?php // HOME ART ASSETS BOTTOM ?>

        <div class="bkg-magic" style="
        <?php if(!empty($background_color)): echo 'background-color:'.$background_color.';'; endif; ?>
        ">
        <?php if(!empty($background_image)): ?>
            <div class="object-cover-wrap bkg top-center">
                <?php echo '<img rel="preload" as="image" src="'.$background_image['sizes']['xlarge'].'" srcset="'.$background_image['sizes']['mobile3'].' 640w, '.$background_image['sizes']['mobile2'].' 768w, '.$background_image['sizes']['mobile1'].' 1024w, '.$background_image['sizes']['large'].' 1200w, '.$background_image['sizes']['xlarge'].' 1440w" alt="'.$background_image['alt'].'">'; ?>
            </div>
        <?php endif; ?>
        </div>

        <div class="bottom-wave-space"></div>

    </section>
    <?php endif; ?>
    <?php endwhile; ?>
    <!-- FLEX ROW -->

</div>
<?php endif; ?>