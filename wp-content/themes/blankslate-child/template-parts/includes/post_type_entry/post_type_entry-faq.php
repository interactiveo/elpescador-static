<?php
    $title = get_the_title();
    $content = get_the_content();
    $id = get_the_id();
?>

<?php if ( !has_term( '', 'faq_category', $id ) ): $open_modal = 'open'; endif; ?>
<?php if(empty($open_modal)): $open_modal = null; endif; ?>

<div class="col col-xs-12">
    <details class="faq" id="id-<?php echo $id; ?>" <?php echo $open_modal; ?>>
        <summary><?php echo $title; ?></summary>
        <article class="expander">
            <div class="body-area">
                <?php if(!empty($content)): $content = preg_replace('/<p[^>]*>(?:\s|&nbsp;)*<\/p>/', '', $content); echo $content; endif; ?>
            </div>
        </article>
    </details>
</div>