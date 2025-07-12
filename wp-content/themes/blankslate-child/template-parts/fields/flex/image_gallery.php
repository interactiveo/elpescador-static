<?php $images = get_sub_field('images'); ?>
<?php $random_id = rand(1,5000); ?>

<div class="wrap-x">
    <div class="inside pl pr">
        <div class="swiper-margins">
            <div class="swiper" id="slidingGallery" data-featherlight-gallery
                data-featherlight-filter="a.gallery-<?php echo $random_id; ?>">
                <div class="swiper-wrapper">
                    <?php if(!empty($images)): ?>
                    <?php foreach( $images as $image ): ?>
                    <div class="swiper-slide">
                        <a href="<?php echo esc_url($image['sizes']['xlarge']); ?>"
                            class="featured--image square block border-radius overflow-hidden bg-green-dark gallery-<?php echo $random_id; ?>"
                            title="View Larger Image" data-featherlight-variant="fl-image-gallery-custom">
                            <div class="object-cover-wrap border-radius top-center">
                                <img src="<?php echo esc_url($image['sizes']['mobile3']); ?>"
                                    alt="<?php echo esc_url($image['alt']); ?>" class="border-radius" />
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>