<?php
    // FALLBACK ENTRY TEMPLATE
    $title = get_the_title();
    $url = get_the_permalink();
?>

<div class="col col-xs-12">
    <a href="<?php echo $url; ?>">
        <?php echo $title; ?>
    </a>
</div>