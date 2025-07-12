<?php
    $title = get_the_title();
    $url = get_the_permalink();
    $excerpt = get_the_excerpt();
    $event_location = get_field('event_location');
    $date_logic = get_field('date_logic');
?>

<?php
// IMAGE FIELDS
$image_alt = null;
$image_url = null;
$thumbnail = null;
if($thumbnail = get_post_thumbnail_id( $post->ID )):
    $image_alt = get_post_meta($thumbnail, '_wp_attachment_image_alt', true);
    $image_url = get_the_post_thumbnail_url(null,'medium');
endif;
?>

<?php 
    // DATE AND TIME ARRAY
    $date_string = get_field('event_date');
    $date_format = date("Y-m-d", strtotime($date_string));
    $date_month = date("F", strtotime($date_string));
    $date_month_short = date("M", strtotime($date_string));
    $date_num = date("j", strtotime($date_string));
    $date_dayname = date("D", strtotime($date_string));
    $date_year = date("Y", strtotime($date_string));

    if($date_logic == 'Date & Time'):
        $event_time = get_field('event_time');
        $event_time2 = get_field('event_time2');
    endif;

    $date_string2 = get_field('event_date2');
    if(!empty($date_string2)):
        $date_format2 = date("Y-m-d", strtotime($date_string2));
        $date_month2 = date("F", strtotime($date_string2));
        $date_month_short2 = date("M", strtotime($date_string2));
        $date_num2 = date("j", strtotime($date_string2));
        $date_dayname2 = date("D", strtotime($date_string2));
        $date_year2 = date("Y", strtotime($date_string2));
    endif;

?>

<div class="swiper-slide event-slide">
    <div class="event--entry--wrapper">


        <a href="<?php echo $url; ?>" role="button" target="_self" class="block featured--image rectangle border-radius"
            title="<?php echo $title; ?>">
            <div class="object-cover-wrap border-radius bg-black-shade1">
                <?php if(!empty($image_url)): ?>
                <img src="<?php echo $image_url; ?>" alt="<?php echo $image_alt; ?>" class="border-radius" />
                <?php endif; ?>
            </div>
        </a>


        <time class="date" datetime="<?php if(!empty($date_format)): echo $date_format; endif; if(!empty($event_time)): echo ' '.$event_time.''; endif; ?>">
        <?php
        if(!empty($date_dayname)): echo '<span class="day-name">'.$date_dayname.', </span>'; endif;
        if(!empty($date_num)): echo '<span class="date-num">'.$date_num.'</span> '; endif;
        if(!empty($date_month)): echo '<span class="month-name">'.$date_month_short.'</span> '; endif;
        if(!empty($date_year)): echo '<span class="date-year">'.$date_year.'</span>'; endif;
        if(!empty($event_time)): echo '<span class="time">, '.$event_time.'</span>'; endif;

        if(!empty($date_string2)): 
        echo '<span> / </span>';
        //echo '<span class="end-date"> / </span>';
        echo '<br>';
        if(!empty($date_dayname2)): echo '<span class="day-name">'.$date_dayname2.', </span>'; endif;
        if(!empty($date_num2)): echo '<span class="date-num">'.$date_num2.'</span> '; endif;
        if(!empty($date_month2)): echo '<span class="month-name">'.$date_month_short2.'</span> '; endif;
        if(!empty($date_year2)): echo '<span class="date-year">'.$date_year2.'</span>'; endif;
        if(!empty($event_time2)): echo '<span class="time">, '.$event_time2.'</span>'; endif;
        endif;

        ?>
        </time>

        <?php if(!empty($event_location)): ?>
        <p class="location"><?php echo $event_location; ?></p>
        <?php endif; ?>

        <h3 class="post-title">
            <a href="<?php echo $url; ?>" target="_self" class="post-title-link" title="<?php echo $title; ?>">
                <?php echo $title; ?>
            </a>
        </h3>

    </div>
</div>