<?php
    $title = get_the_title();
    $url = get_the_permalink();
    $excerpt = get_the_excerpt();
?>

<div class="col col-xs-12 body-area small-gap blog--entry">
    <h3><a href="<?php echo $url; ?>"><?php echo $title; ?></a></h3>
    <?php if(!empty($excerpt)): $excerpt = preg_replace('#<a[^>]*>(.*?)</a>#is', '$1', $excerpt); echo '<p class="excerpt">'.$excerpt.'</p>'; endif; ?>
    <p class="pt2"><a href="<?php echo $url; ?>" class="btn" role="button">Report Details</a></p>
</div>
<div class="col col-xs-12">
    <hr class="mt0 mb0" />
</div>