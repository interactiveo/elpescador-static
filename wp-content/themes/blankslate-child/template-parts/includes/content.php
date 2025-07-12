<?php 
$fields = [
    'headline',
    'subheadline',
    'text_area',
    'button',
    'text_alignment',
    'headline_size',
    'teal_box'
];
foreach ($fields as $field) {
    ${$field} = get_sub_field($field);
}
?>

<?php if(empty($teal_box)): $teal_box = null; endif; ?>
<?php if(empty($text_alignment)): $text_alignment = null; endif; ?>
<?php if(empty($headline_size)): $headline_size = null; endif; ?>

<?php // ENSURE THERE IS CONTENT TO SHOW ?>
<?php if($headline || $subheadline || $text_area || $button): ?>

<?php // LOAD COL FROM FLEX-FIELD IF NECESSARY ?>
<?php if(!empty($content_col)): echo '<div class="col '.$content_col.'">'; endif; ?>

<?php // BODY AREA ?>
<?php echo '<article class="body-area section-content '.$text_alignment.'">'; ?>

<?php if(!empty($headline)): echo '<h2 class="headline '.$headline_size.'">'.$headline.'</h2>'; endif; ?>
<?php if($teal_box == '1'): echo '<div class="info-box">'; endif; ?>
<?php if(!empty($subheadline)): echo '<h3 class="subheadline">'.$subheadline.'</h3>'; endif; ?>
<?php if(!empty($text_area)): $text_area = preg_replace('/<p[^>]*>(?:\s|&nbsp;)*<\/p>/', '', $text_area); echo $text_area; endif; ?>
<?php if(!empty($button)): echo '<p class="cta"><a href="'.$button['url'].'" class="btn" target="'.$button['target'].'" role="button" title="'.$button['title'].'">'.$button['title'].'</a></p>'; endif; ?>
<?php if($teal_box == '1'): echo '</div>'; endif; ?>

<?php // BODY AREA ?>
<?php echo '</article>'; ?>

<?php // CLOSE COL FROM FLEX-FIELD IF NECESSARY ?>
<?php if(!empty($content_col)): echo '</div>'; endif; ?>

<?php endif; ?>