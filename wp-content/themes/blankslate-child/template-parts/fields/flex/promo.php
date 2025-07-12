<?php $promo_text_area = get_field('promo_text_area', 'options'); ?>
<?php $promo_side_image = get_field('promo_side_image', 'options'); ?>

<div class="wrap-x">
    <div class="inside pl pr">
        <div class="bg-white-shade1 relative overflow-hidden border-radius box-shadow">
            <div class="row flush">

                <?php if(!empty($promo_text_area)): ?>
                <div class="col col-xs-12 col-md-6 col-left">
                    <article class="body-area pt2x pb2x pl2x pr2x padding">
                        <?php echo $promo_text_area; ?>
                    </article>
                </div>
                <?php endif; ?>

                <?php if(!empty($promo_side_image)): ?>
                <div class="col col-xs-12 col-md-6">
                    <div class="featured--image wide">
                        <div class="object-cover-wrap">
                            <img src="<?php echo $promo_side_image['sizes']['medium']; ?>" alt="<?php echo $promo_side_image['alt']; ?>" />
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>