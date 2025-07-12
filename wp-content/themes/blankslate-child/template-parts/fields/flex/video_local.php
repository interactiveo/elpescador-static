<?php $video_file = get_sub_field('video_file'); ?>
<?php $video_settings = get_sub_field('video_settings'); ?>
<?php $custom_video_thumbnail = get_sub_field('custom_video_thumbnail'); ?>

<div class="wrap-x">
    <div class="inside">
        <div class="row middle-md">

            <!-- COL VIDEO -->
            <div class="col col-xs-12 col-md-7 col-md-grow">
                <button class="block no-style relative h-100" id="open-local-video" title="Enlarge Video">
                    <div class="featured--image wide video">
                        <div class="object-cover-wrap">
                            <img src="<?php echo $custom_video_thumbnail['sizes']['medium']; ?>"
                                alt="<?php echo $custom_video_thumbnail['alt']; ?>" class="border-radius" />
                        </div>
                    </div>
                </button>
            </div>
            <!-- COL VIDEO -->

            <?php $content_col = 'col col-xs-12 col-md-5 col-content'; ?>
            <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>


        </div>
    </div>
</div>

<?php if(!empty($video_file)): ?>
<dialog openmodal id="local-video">
    <button id="close-local-video" class="no-style" title="Close Video"><i>Close</i><span
            class="icon-close"></span></button>
    <video autoplay controls muted disablePictureInPicture preload="none" id="local-video-source"
        poster="<?php if(!empty($custom_video_thumbnail)): echo $custom_video_thumbnail['sizes']['medium']; endif; ?>"
        width="1024" height="576" title="">
        <source src="<?php echo $video_file['url']; ?>" type="video/mp4">
        <p>Your browser does not support HTML5 video.</p>
    </video>
</dialog>
<?php endif; ?>

<script>
const modal = document.querySelector("#local-video");
const openModal = document.querySelector("#open-local-video");
const closeModal = document.querySelector("#close-local-video");

openModal.addEventListener("click", () => {
    modal.showModal();
});

closeModal.addEventListener("click", () => {
    modal.close();
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const dialog = document.querySelector("#local-video");
    const video = dialog.querySelector("#local-video-source");

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
    observer.observe(dialog, {
        attributes: true,
        attributeFilter: ["open"]
    });

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
    const dialog = document.getElementById("local-video");
    const video = document.getElementById("local-video-source");

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
document.getElementById("local-video").addEventListener("open", makeVideoFullScreen);
document.getElementById("local-video").addEventListener("close", makeVideoFullScreen);

// Event listener for orientation change (when the device rotates)
window.addEventListener("orientationchange", makeVideoFullScreen);

// Initial check in case the page loads with the dialog already open and device in landscape
makeVideoFullScreen();
</script>