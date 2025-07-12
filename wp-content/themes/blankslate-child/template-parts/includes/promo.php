<?php 
$fields = [
    'side_image',
    'side_video_preview',
    'side_video_full',
    'headline',
    'subheadline',
    'text_area',
    'button'
];
foreach ($fields as $field) {
    ${$field} = get_sub_field($field);
}
?>

<div class="promo-wrap relative overflow-hidden border-radius bg-white-shade1">
    <div class="row flush start-xs row--main">

        <?php // ENSURE THERE IS CONTENT TO SHOW ?>
        <?php if($headline || $subheadline || $text_area || $button): ?>
        <div class="col col-xs-12 col-md-6 col-left">
            <div class="pt2x pb2x pl2x pr2x m-auto">
                <article class="body-area">
                    <?php if(!empty($headline)): echo '<h2 class="headline">'.$headline.'</h2>'; endif; ?>
                    <?php if(!empty($subheadline)): echo '<h3 class="subheadline">'.$subheadline.'</h3>'; endif; ?>
                    <?php if(!empty($text_area)): $text_area = preg_replace('/<p[^>]*>(?:\s|&nbsp;)*<\/p>/', '', $text_area); echo $text_area; endif; ?>
                    <?php if(!empty($button)): echo '<p class="cta"><a href="'.$button['url'].'" class="btn" target="'.$button['target'].'" role="button" title="'.$button['title'].'">'.$button['title'].'</a></p>'; endif; ?>
                </article>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($side_image || $side_video_preview || $side_video_full)): ?>
        <div class="col col-xs-12 col-md-6">

            <?php if(!empty($side_video_full)): ?>
            <button class="block no-style relative h-100" id="open-promo-video" title="Enlarge Video">
                <?php endif; ?>

                <div class="featured--image wide video">
                    <div class="object-cover-wrap">

                        <?php if(!empty($side_video_preview)): ?>
                        <?php // VIDEO PREVIEW FIRST ?>
                        <video autoplay muted loop disablePictureInPicture preload="none"
                            poster="<?php if(!empty($side_image)): echo $side_image['sizes']['medium']; endif; ?>"
                            width="1024" height="576" title="<?php if(!empty($headline)): echo $headline; endif; ?>">
                            <source src="<?php echo $side_video_preview; ?>" type="video/mp4">
                            <p>Your browser does not support HTML5 video.</p>
                        </video>

                        <?php elseif(!empty($side_image)): ?>
                        <?php // IMAGE ONLY ?>
                        <img src="<?php echo $side_image['sizes']['medium']; ?>"
                            alt="<?php echo $side_image['alt']; ?>" />
                        <?php endif; ?>

                    </div>
                </div>

            <?php if(!empty($side_video_full)): ?>
            </button>
            <?php endif; ?>

        </div>
        <?php endif; ?>


    </div>
</div>

<?php if($side_video_full): ?>
    <dialog openmodal id="promo-video">
    <button id="close-promo-video" class="no-style" title="Close Video"><i>Close</i><span class="icon-close"></span></button>
    <video autoplay controls muted disablePictureInPicture preload="none" id="promo-video-source"
                            poster="<?php if(!empty($side_image)): echo $side_image['sizes']['medium']; endif; ?>"
                            width="1024" height="576" title="<?php if(!empty($headline)): echo $headline; endif; ?>">
                            <source src="<?php echo $side_video_full; ?>" type="video/mp4">
                            <p>Your browser does not support HTML5 video.</p>
                        </video>
            </dialog>

<script>
const modal = document.querySelector("#promo-video");
const openModal = document.querySelector("#open-promo-video");
const closeModal = document.querySelector("#close-promo-video");

openModal.addEventListener("click", () => {
  modal.showModal();
});

closeModal.addEventListener("click", () => {
  modal.close();
});
    </script>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const dialog = document.querySelector("#promo-video");
    const video = dialog.querySelector("#promo-video-source");

    // Check if the dialog is not open
    const stopVideoPlayback = () => {
      if (!dialog.hasAttribute("open")) {
        video.pause(); // Stop playback
        video.muted = true; // Mute the video
        document.body.classList.remove('modal-open');
      } else {
        video.play();
        video.muted = false;
        video.currentTime = 0;
        document.body.classList.add('modal-open');
      }
    };

    // Monitor dialog attribute changes
    const observer = new MutationObserver(stopVideoPlayback);
    observer.observe(dialog, { attributes: true, attributeFilter: ["open"] });

    // Initial check (in case the dialog starts without being open)
    stopVideoPlayback();
  });
</script>

<script>
// Function to check if the device is in landscape mode
function isLandscape() {
  return window.innerHeight < window.innerWidth;
}

// Function to make the video full screen
function makeVideoFullScreen() {
  const dialog = document.getElementById("promo-video");
  const video = document.getElementById("promo-video-source");

  if (dialog.open && isLandscape()) {
    // Apply styles for full-screen
    video.style.width = "100vw";
    video.style.height = "100vh";
    video.style.objectFit = "cover"; // Optional: ensure the video covers the screen
  } else {
    // Reset to default styling
    video.style.width = "";
    video.style.height = "";
    video.style.objectFit = "";
  }
}

// Event listener for when the dialog is opened or closed
document.getElementById("promo-video").addEventListener("open", makeVideoFullScreen);
document.getElementById("promo-video").addEventListener("close", makeVideoFullScreen);

// Event listener for orientation change (when the device rotates)
window.addEventListener("orientationchange", makeVideoFullScreen);

// Initial check in case the page loads with the dialog already open and device in landscape
makeVideoFullScreen();
    </script>

<?php endif; ?>