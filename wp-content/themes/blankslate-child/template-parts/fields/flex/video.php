<?php $video_url = get_sub_field('video_url'); ?>
<?php $video_settings = get_sub_field('video_settings'); ?>
<?php $custom_video_thumbnail = get_sub_field('custom_video_thumbnail'); ?>

<?php 
// VIDEO MANIPULATION
$url = get_sub_field('video_url', false, false);
$oembed = _wp_oembed_get_object();
$provider = $oembed->get_provider($url);
$oembed_data = $oembed->fetch( $provider, $url );
$thumbnail = $oembed_data->thumbnail_url;

preg_match('/src="(.+?)"/', $video_url, $matches);
$src = $matches[1];
$params = array(
  'hd' => 1,
  'rel' => 0,
  'autoplay' => 1
);
$new_src = add_query_arg($params, $src);
?>

<?php 
// THUMBNAIL
if(!empty($custom_video_thumbnail)):
    $thumbnail = $custom_video_thumbnail['sizes']['medium'];
endif;
?>

<div class="wrap-x">
    <div class="inside">
        <div class="row middle-md">

            <!-- COL VIDEO -->
            <div class="col col-xs-12 col-md-7 col-md-grow">
                <?php if($video_settings == 'embed'): ?>
                <?php // EMBEDDED ?>
                <div class="embed-container border-radius">
                    <?php echo $video_url; ?>
                </div>
                <?php elseif($video_settings == 'lightbox'): ?>
                <?php // LIGHTBOX ?>
                <a href="<?php echo $new_src; ?>" class="featured--image wide video video-still block"
                    title="Open Video" data-featherlight="iframe" data-featherlight-iframe-frameborder="0"
                    data-featherlight-iframe-allow="autoplay; encrypted-media"
                    data-featherlight-iframe-style="width:100%; height:100%;"
                    data-featherlight-iframe-allowfullscreen="true">
                    <div class="object-cover-wrap border-radius">
                        <img src="<?php echo $thumbnail; ?>" alt="Video Thumbnail" class="border-radius" />
                    </div>
                </a>
                <?php endif; ?>
            </div>
            <!-- COL VIDEO -->
             
            <?php $content_col = 'col col-xs-12 col-md-5 col-content'; ?>
            <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>


        </div>
    </div>
</div>