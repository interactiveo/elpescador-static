<?php $custom_html = get_sub_field('custom_html'); ?>

<div class="wrap-x">
    <div class="inside pl pr">
        <article class="body-area">
            <?php if(!empty($custom_html)): echo $custom_html; endif; ?>
        </article>
    </div>
</div>