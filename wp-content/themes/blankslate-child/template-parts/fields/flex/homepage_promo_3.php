<?php $images = get_sub_field('images'); ?>

<?php 
// CONTENT
$fields = [
    'headline',
    'subheadline',
    'text_area',
    'button'
];
foreach ($fields as $field) {
    ${$field} = get_sub_field($field);
}
?>

<!-- TOP CONTENT -->
<?php // ENSURE THERE IS CONTENT TO SHOW ?>
<?php if($headline || $subheadline || $text_area || $button): ?>
<div class="mb2x">
    <div class="wrap-x">
        <div class="inside pl pr">
            <div class="inside mid">
                <article class="body-area section-content">
                    <?php $content_col = null; ?>
                    <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>
                </article>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- IMAGES -->
<?php if(!empty($images)): ?>
<div class="relative overflow-hidden">
    <div class="off-screen-adjust">
        <div class="swiper swiper-container-free-mode" id="slidingImages">
            <div class="swiper-wrapper">

                <!-- SLIDE -->
                <?php $counter = 0; ?>
                <div class="swiper-slide">
                    <div class="image-container row nested compact middle-xs gap-quarterrow-count-1">
                        <?php foreach( $images as $image ): ?>
                        <?php $counter++; ?>
                        <?php $image_url = $image['sizes']['medium']; ?>
                        <?php $image_url2 = $image['sizes']['xlarge']; ?>
                        <?php $image_alt = $image['alt']; ?>
                        <?php if($counter == 3): echo '<div class="col col-xs row-count-2"><div class="row flush inner-row">'; endif; ?>
                        <?php if($counter == 5): echo '<div class="col col-xs row-count-3"><div class="row flush inner-row">'; endif; ?>
                        <div class="col col-xs col-count-<?php echo $counter; ?>">
                            <?php echo '<img class="img-take-over border-radius select-none transition img-count-'.$counter.'" as="image" src="'.$image['sizes']['xlarge'].'" srcset="'.$image['sizes']['xsmall'].' 480w,'.$image['sizes']['small'].' 640w, '.$image['sizes']['medium'].' 768w, '.$image['sizes']['large'].' 1200w, '.$image['sizes']['xlarge'].' 1440w" alt="'.$image['alt'].'">'; ?>
                        </div>
                        <?php if (in_array($counter, array(4, 6))): echo '</div></div>'; endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- SLIDE -->

                <!-- SLIDE -->
                <?php $counter = 0; ?>
                <div class="swiper-slide">
                    <div class="image-container row nested compact middle-xs gap-quarterrow-count-1">
                        <?php foreach( $images as $image ): ?>
                        <?php $counter++; ?>
                        <?php $image_url = $image['sizes']['medium']; ?>
                        <?php $image_url2 = $image['sizes']['xlarge']; ?>
                        <?php $image_alt = $image['alt']; ?>
                        <?php if($counter == 3): echo '<div class="col col-xs row-count-2"><div class="row flush inner-row">'; endif; ?>
                        <?php if($counter == 5): echo '<div class="col col-xs row-count-3"><div class="row flush inner-row">'; endif; ?>
                        <div class="col col-xs col-count-<?php echo $counter; ?>">
                        <?php echo '<img class="img-take-over border-radius select-none transition img-count-'.$counter.'" as="image" src="'.$image['sizes']['xlarge'].'" srcset="'.$image['sizes']['xsmall'].' 480w,'.$image['sizes']['small'].' 640w, '.$image['sizes']['medium'].' 768w, '.$image['sizes']['large'].' 1200w, '.$image['sizes']['xlarge'].' 1440w" alt="'.$image['alt'].'">'; ?>
                        </div>
                        <?php if (in_array($counter, array(4, 6))): echo '</div></div>'; endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- SLIDE -->


            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- BOTTOM CONTENT -->
<?php if( have_rows('bottom_content') ): ?>
<?php while( have_rows('bottom_content') ): the_row(); ?>

<?php 
$fields = [
    'headline',
    'subheadline',
    'text_area',
    'button'
];
foreach ($fields as $field) {
    ${$field} = get_sub_field($field);
}
?>

<?php // ENSURE THERE IS CONTENT TO SHOW ?>
<?php if($headline || $subheadline || $text_area || $button): ?>
<div class="mt2x bottom-content-wrap">
    <div class="wrap-x">
        <div class="inside pl pr">
            <div class="inside mini">
                <article class="body-area section-content">
                    <?php $content_col = null; ?>
                    <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>
                </article>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php endwhile; ?>
<?php endif; ?>