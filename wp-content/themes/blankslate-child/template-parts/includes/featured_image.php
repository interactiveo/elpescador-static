<?php
$fields = [
    'image_file',
    'image_crop'
];
foreach ($fields as $field) {
    ${$field} = get_sub_field($field);
}
?>

<?php if(!empty($image_file)): ?>
<div class="featured--image <?php if(!empty($image_crop)): echo $image_crop; endif; ?>">
    <div class="object-cover-wrap">
        <img 
        src="<?php echo $image_file['sizes']['large']; ?>" 
        srcset="
        <?php echo $image_file['sizes']['xsmall']; ?> 360w,
        <?php echo $image_file['sizes']['small']; ?> 768w,
        <?php echo $image_file['sizes']['medium']; ?> 1024w
        " 
        sizes="(max-width: 360px) 360px, 
            (max-width: 768px) 768px, 
            (max-width: 1024px) 1024px, 
            100vw" 
        alt="<?php echo $image_file['alt']; ?>" 
        />
    </div>
</div>
<?php endif; ?>