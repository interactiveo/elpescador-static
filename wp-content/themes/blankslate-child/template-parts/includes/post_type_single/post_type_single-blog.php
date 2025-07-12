<?php global $enable_blog_widget; ?>
<?php $title = get_the_title(); ?>
<?php $content = get_the_content(); ?>
<?php $date = get_the_date(); ?>
<?php $date2 = get_the_date('m-d-Y'); ?>
<?php $show_blog_sidebar = get_field('show_blog_sidebar', 'options'); ?>
<?php $show_blog_featured_image = get_field('show_blog_featured_image', 'options'); ?>

<?php $fields = ['weather', 'wind', 'airtemp', 'watertemp', 'moonphase', 'sunrisesunset']; ?>

<?php
$fields2 = [
    'grandslam',
    'bonefish',
    'permit',
    'tarpon',
    'otherspecies',
    'bonefishflies',
    'permitflies',
    'tarponflies',
    'snookflies'
];
?>

<?php include get_stylesheet_directory() . '/template-parts/hero/hero.php'; ?>

<div class="flex-margin">
    <div class="wrap-x">
        <div class="inside">
            <div class="row">

                <!-- MAIN COL -->
                <div class="col col-xs-12 col-md-8" temprop="mainEntityOfPage">
                    <article class="body-area">

                        <h2 class="h4 caps fw-600 summary-title">Report Details</h2>

                        <dl class="selling-points">
                        <?php
                        foreach ( $fields as $field_name ) {
                            $value = get_field( $field_name );
                            if ( $value ) {
                                $field_object = get_field_object( $field_name );
                                $label = $field_object['label'];
                                echo '<dt>' . esc_html( $label ) . ':</dt>';
                                echo '<dd>' . $value . '</dd>';
                                echo '<hr class="hide-last">';
                            }
                        }
                        ?>
                        </dl>

                        <div class="pt2"><!-- spacer --></div>

                        <?php if(!empty($content)): ?>
                        <h2 class="h4 caps fw-600 summary-title">Week In Review</h2>
                        <?php get_template_part('template-parts/fields/field--the_content'); ?>
                        <div class="pt2"><!-- spacer --></div>
                        <?php endif; ?>

                        <h2 class="h4 caps fw-600 summary-title">What Guests were Catching</h2>
                        <dl class="selling-points ">
                            <?php 
                            foreach ( $fields2 as $field_name ) {
                                $value = get_field( $field_name );
                                if ( $value ) {
                                    $field_object = get_field_object( $field_name );
                                    $label = $field_object['label'];
                                    echo '<dt>' . esc_html( $label ) . '</dt>';
                                    echo '<dd>' . $value . '</dd>';
                                    echo '<hr class="hide-last">';
                                }
                            }
                            ?>
                        </dl>

                    </article>
                </div>
                <!-- MAIN COL -->

                <!-- SIDEBAR -->
                <?php if($show_blog_sidebar == '1'): ?>
                <?php if($enable_blog_widget == '1'): ?>
                <aside class="col col-xs-12 col-md-4" role="complementary" id="sidebar">
                    <?php get_template_part('sidebar'); ?>
                </aside>
                <?php endif; ?>
                <?php endif; ?>
                <!-- SIDEBAR -->

            </div>
        </div>
    </div>
</div>