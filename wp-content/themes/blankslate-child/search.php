<?php get_header();

if($search_query = get_search_query()):
  $search_h1 = 'Search Results for: <em>"'.$search_query.'"</em>';
else:
  $search_h1 = 'Search';
endif;

if($no_results_error_message = get_field('no_results_error_message', 'options')):
else:
  $no_results_error_message = 'No Results Found.';
endif;

$show_search_side_filters = get_field('show_search_side_filters', 'options');

?>


<!-- HERO -->
<header class="page-hero bg-green-dark color-white-base">
    <div class="wrap-x">
        <div class="inside">
            <div class="row main--row middle-xs">
                <div class="col col-xs-12">
                    <hgroup class="block body-area center-xs flex-padding">
                        <h1 class="entry-title color-white-base" itemprop="name"><?php echo $search_h1; ?></h1>
                    </hgroup>
                </div>
            </div>
        </div>
    </div>

</header>
<!-- HERO -->



<div class="flex-margin">
    <div class="wrap-x">
        <div class="inside">
            <div class="row mb2x-row">

                <div class="col col-xs-12">
                    <div class="bg-white-base border-radius top-search-wrap">
                        <?php echo get_search_form(); ?>
                    </div>
                </div>

                <div class="col col-xs-12 col-md-8 col-md-grow" itemprop="mainEntityOfPage">
                    <div class="row nested" id="search-results">
                        <?php if ( have_posts() ) : ?>
                        <?php while ( have_posts() ) : the_post(); ?>
                        <div class="col col-xs-12 col-flex-1">
                            <?php get_template_part('template-parts/search/search--entry'); ?>
                        </div>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <div class="col col-xs-12">
                            <div class="inner-background pt pb pl pr border-radius overflow-hidden relative">
                                <div class="mb2">
                                    <strong><?php echo $no_results_error_message; ?></strong>
                                </div>
                                <?php get_search_form(); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col col-xs-12">
                            <?php
                the_posts_pagination( array(
                 'mid_size'  => 4,
                 'screen_reader_text' => ' ',
                 'prev_text' => __( 'Previous', 'textdomain' ),
                 'next_text' => __( 'Next', 'textdomain' ),
                ) );
                ?>
                        </div>
                    </div>
                </div>

                <?php if($show_search_side_filters == '1'): ?>
                <aside class="col col-xs-12 col-md-4" role="complementary" id="sidebar">
                    <?php echo do_shortcode('[searchandfilter id="819"]'); ?>
                </aside>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>