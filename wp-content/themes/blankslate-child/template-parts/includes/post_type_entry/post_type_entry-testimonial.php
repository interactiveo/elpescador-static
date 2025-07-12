<?php $text_area = get_the_content(); ?>
<?php $testimonial_author = get_field('testimonial_author'); ?>
<?php $testimonial_company = get_field('testimonial_company'); ?>


<div class="col col-xs-12">
    <blockquote class="testimonial">
        <?php if(!empty($text_area)): $text_area = preg_replace('/<p[^>]*>(?:\s|&nbsp;)*<\/p>/', '', $text_area); echo $text_area; endif; ?>
        <?php if(!empty($testimonial_author || $testimonial_company)): ?>
            <cite>
                <?php if(!empty($testimonial_author)): echo '<p class="testimonial_author">'.$testimonial_author.'</p>'; endif; ?>
                <?php if(!empty($testimonial_company)): echo '<p class="testimonial_company">'.$testimonial_company.'</p>'; endif; ?>
            </cite>
            <?php endif; ?>
    </blockquote>
</div>