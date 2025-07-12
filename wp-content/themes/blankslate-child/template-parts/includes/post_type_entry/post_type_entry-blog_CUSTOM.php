<?php
/**
 * Entry template for blog posts (Fishing Reports) and other blog entries.
 */

// Basic values
$title   = get_the_title();
$url     = get_permalink();
$excerpt = get_the_excerpt();

// ACF date fields (should return 'YYYY-MM-DD')
$fromdate = get_field('start_date');
$enddate  = get_field('end_date');

// Default link text is the post title
$link_text = $title;

// On the Fishing Reports term archive, override with "Start Date through End Date"
if ( is_tax( 'blog_category', 'fishing-reports' ) && $fromdate ) {
    // Parse and format dates
    $start = date_i18n( 'F j, Y', strtotime( $fromdate ) );
    if ( $enddate ) {
        $end       = date_i18n( 'F j, Y', strtotime( $enddate ) );
        $link_text = "$start through $end";
    } else {
        $link_text = $start;
    }
}
?>

<div class="col col-xs-12 body-area small-gap blog--entry">
    <h3>
        <a href="<?php echo esc_url( $url ); ?>">
            <?php echo esc_html( $link_text ); ?>
        </a>
    </h3>

    <?php if ( ! empty( $excerpt ) ) :
        // Strip anchor tags from excerpt
        $clean_excerpt = preg_replace( '#<a[^>]*>(.*?)</a>#i', '$1', $excerpt );
        echo '<p class="excerpt">' . esc_html( $clean_excerpt ) . '</p>';
    endif; ?>

    <p class="pt2">
        <a href="<?php echo esc_url( $url ); ?>" class="btn" role="button">
            Report Details
        </a>
    </p>
</div>

<div class="col col-xs-12">
    <hr class="mt0 mb0" />
</div>
