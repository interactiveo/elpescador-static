<div class="wrap-x">
    <div class="inside pl pr">

        <!-- VIDEO GALLERY -->
        <div class="swiper-margins">
            <div class="swiper" id="slidingGallery">
                <div class="swiper-wrapper video-slide">

                    <?php if( have_rows('videos') ): ?>
                    <?php while( have_rows('videos') ) : the_row(); ?>
                    <?php 
                    // LABEL
                    $video_label = get_sub_field('video_label');

                    // VIDEO MANIPULATION
                    $video_url = get_sub_field('video_url');
                    preg_match('/src="(.+?)"/', $video_url, $matches);
                    $src = $matches[1];
                    $params = array(
                    'hd' => 1,
                    'rel' => 0,
                    'autoplay' => 1
                    );
                    $new_src = add_query_arg($params, $src);

                    // THUMBNAIL
                    $custom_video_thumbnail = get_sub_field('custom_video_thumbnail');
                    if(!empty($custom_video_thumbnail)):
                        $thumbnail = $custom_video_thumbnail['sizes']['medium'];
                    endif;
                    ?>
                    <div class="swiper-slide">
                        <a href="<?php echo $new_src; ?>" class="featured--image tall border-radius overflow-hidden bg-green-dark video video-gallery--entry video-still"
                            title="Open Video" data-featherlight="iframe" data-featherlight-iframe-frameborder="0"
                            data-featherlight-iframe-allow="autoplay; encrypted-media"
                            data-featherlight-iframe-style="width:100%; height:100%;"
                            data-featherlight-iframe-allowfullscreen="true">
                            <?php if(!empty($video_label)): echo '<h3 class="video-label">'.$video_label.'</h3>'; endif; ?>
                            <div class="object-cover-wrap top-center"><img src="<?php echo $thumbnail; ?>" alt="<?php echo $video_label; ?>" /></div>
                        </a>
                    </div>
                    <?php endwhile; ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <!-- VIDEO GALLERY -->

        <div class="swiper-pagination"></div>

    </div>
</div>