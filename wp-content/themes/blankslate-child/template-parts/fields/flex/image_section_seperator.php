<?php $image = get_sub_field('image'); ?>


<?php if(!empty($image)): ?>
<div class="bkg-magic manual">
    <div class="object-cover-wrap top-center">
    <?php echo '<img rel="preload" as="image" src="'.$image['sizes']['xlarge'].'" srcset="'.$image['sizes']['small'].' 640w, '.$image['sizes']['medium'].' 768w, '.$image['sizes']['large'].' 1024w, '.$image['sizes']['xlarge'].' 1440w" alt="'.$image['alt'].'">'; ?>
    </div>
</div>
<?php endif; ?>

<div class="image-spacer"></div>