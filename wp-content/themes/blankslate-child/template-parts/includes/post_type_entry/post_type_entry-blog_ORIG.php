<?php
    // Fallback post title
    $title = get_the_title();

    // Pull the ACF date fields
    $fromdate = get_field('fromdate');
    $todate   = get_field('todate');

    // Build a formatted date or date range
    $formatted_date = '';
    if ( ! empty( $fromdate ) ) {
        $from_obj = DateTime::createFromFormat( 'Ymd', $fromdate );
        if ( $from_obj ) {
            $formatted_date = $from_obj->format( 'F j, Y' );
        }
        if ( ! empty( $todate ) ) {
            $to_obj = DateTime::createFromFormat( 'Ymd', $todate );
            if ( $to_obj ) {
                $formatted_date .= ' through ' . $to_obj->format( 'F j, Y' );
            }
        }
    }

    // Permalink and excerpt
    $url     = get_the_permalink();
    $excerpt = get_the_excerpt();
?>

<div class="col col-xs-12 body-area small-gap blog--entry">
    <h3>
        <a href="<?php echo esc_url( $url ); ?>">
            <?php
                // If formatted_date is set, show it; otherwise show the post title
                echo esc_html( $formatted_date ? $formatted_date : $title );
            ?>
        </a>
    </h3>

    <?php if ( $excerpt ) :
        // Remove any read-more links from the excerpt
        $clean_excerpt = preg_replace( '#<a[^>]+>.*?</a>#i', '', $excerpt );
    ?>
        <p class="excerpt"><?php echo esc_html( $clean_excerpt ); ?></p>
    <?php endif; ?>

    <p class="pt2">
        <a href="<?php echo esc_url( $url ); ?>" class="btn" role="button">
            Report Details
        </a>
    </p>
</div>
<div class="col col-xs-12">
    <hr class="mt0 mb0" />
</div>
