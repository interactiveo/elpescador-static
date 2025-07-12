<?php $hero_layout = get_field('hero_layout'); ?>
<?php $hero_h1 = get_field('hero_h1'); ?>
<?php $title = get_the_title(); ?>
<?php $hero_h2 = get_field('hero_h2'); ?>
<?php $hero_cta = get_field('hero_cta'); ?>
<?php $hero_background_image = get_field('hero_background_image'); ?>
<?php $disable_wave_spacing = get_field('disable_wave_spacing'); ?>

<?php 
// Archive / Category H1
if(is_archive()):
    $hero_h1 = get_the_archive_title();
    $hero_layout = 'default';
endif;
?>

<?php if(is_singular('blog')):
    $fromdate = get_field('fromdate');
    $enddate = get_field('enddate');

    if(!empty($fromdate)):
        $date_object = DateTime::createFromFormat('Ymd', $fromdate);
        $formatted_date = $date_object->format('F j, Y');
        $hero_h1 = $formatted_date;
    endif;

    if(!empty($enddate)):
        $date_object2 = DateTime::createFromFormat('Ymd', $enddate);
        $formatted_date2 = $date_object2->format('F j, Y');
        $hero_h1 = '<span class="caps">'.$formatted_date.' through '.$formatted_date2.'</span>';
    endif;

    
    
endif; ?>

<?php if(is_singular(array('event', 'adventures', 'blog', 'news', 'tips'))): 
$hero_layout = 'default';
if($feat_thumbnail = get_post_thumbnail_id( $post->ID )):
    $feat_image_alt = get_post_meta($feat_thumbnail, '_wp_attachment_image_alt', true);
endif;
endif; ?>

<?php if(is_singular(array('event'))): 
    $event_location = get_field('event_location');
    $date_logic = get_field('date_logic');
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

endif; ?>

<header
    class="page-hero bg-green-dark color-white-base <?php if(!empty($disable_wave_spacing)): if($disable_wave_spacing == '1'): echo 'disable-wave-space'; endif; endif; ?> hero-layout-<?php echo $hero_layout; ?>">
    <div class="wrap-x">
        <div class="inside">
            <div class="row main--row middle-xs">
                <div class="col col-xs-12">
                    <hgroup class="block body-area center-xs flex-padding">


                        <!-- H1 -->
                        <h1 itemprop="name" class="hero-h1">
                            <?php if(!empty($hero_h1)): echo $hero_h1; else: echo $title; endif; ?>
                        </h1>
                        <!-- H1 -->


                        <!-- H2 -->
                        <?php if(!empty($hero_h2)): ?>
                        <h2 class="hero-h2 alt-heading h4">
                            <?php echo $hero_h2; ?>
                        </h2>
                        <?php endif; ?>
                        <!-- H2 -->

                        <?php if(is_singular('news')):  ?>
                        <?php $date = get_the_date(); ?>
                        <?php $date2 = get_the_date('m-d-Y'); ?>
                        <time class="date hero-h2 alt-heading h3" datetime="<?php echo $date2; ?>"><?php echo $date; ?></time>
                        <?php endif; ?>

                        <?php if(is_singular('event')):  ?>
                        <time class="date hero-h2 alt-heading h3"
                            datetime="<?php if(!empty($date_format)): echo $date_format; endif; if(!empty($event_time)): echo ' '.$event_time.''; endif; ?>">
                            <?php
                            if(!empty($date_dayname)): echo '<span class="day-name">'.$date_dayname.', </span>'; endif;
                            if(!empty($date_num)): echo '<span class="date-num">'.$date_num.'</span> '; endif;
                            if(!empty($date_month)): echo '<span class="month-name">'.$date_month_short.'</span> '; endif;
                            if(!empty($date_year)): echo '<span class="date-year">'.$date_year.'</span>'; endif;
                            if(!empty($event_time)): echo '<span class="time">, '.$event_time.'</span>'; endif;

                            if(!empty($date_string2)): 
                            echo '<span> / </span>';
                            if(!empty($date_dayname2)): echo '<span class="day-name">'.$date_dayname2.', </span>'; endif;
                            if(!empty($date_num2)): echo '<span class="date-num">'.$date_num2.'</span> '; endif;
                            if(!empty($date_month2)): echo '<span class="month-name">'.$date_month_short2.'</span> '; endif;
                            if(!empty($date_year2)): echo '<span class="date-year">'.$date_year2.'</span>'; endif;
                            if(!empty($event_time2)): echo '<span class="time">, '.$event_time2.'</span>'; endif;
                            endif;
                            ?>
                        </time>
                        <?php if(!empty($event_location)): ?>
                        <p class="location-hero">
                            <span>
                            <i class="icon-map"></i><?php echo $event_location; ?>
                        </span>
                        </p>
                        <?php endif; ?>
                        <?php endif; ?>

                        <!-- CTA -->
                        <?php if(!empty($hero_cta)): ?>
                        <p class="mt2x">
                            <a href="<?php echo $hero_cta['url']; ?>" target="<?php echo $hero_cta['target']; ?>"
                                title="<?php echo $hero_cta['title']; ?>" class="btn hero-btn">
                                <?php echo $hero_cta['title']; ?>
                            </a>
                        </p>
                        <?php endif; ?>
                        <!-- CTA -->


                    </hgroup>
                </div>
            </div>
        </div>
    </div>

    <!-- BKG -->
    <?php if(!empty($hero_background_image)): ?>
    <div class="object-cover-wrap hero-bkg top-center">
        <?php echo '<img rel="preload" fetchpriority="high" as="image" src="'.$hero_background_image['sizes']['xlarge'].'" srcset="'.$hero_background_image['sizes']['mobile3'].' 640w, '.$hero_background_image['sizes']['mobile2'].' 768w, '.$hero_background_image['sizes']['mobile1'].' 1024w, '.$hero_background_image['sizes']['large'].' 1200w, '.$hero_background_image['sizes']['xlarge'].' 1440w" alt="'.$hero_background_image['alt'].'">'; ?>
        <?php if($hero_layout == 'homepage'): ?>
        <img rel="preload" fetchpriority="high" as="image"
            src="/wp-content/themes/blankslate-child/img/home-hero-fish.png" aria-hidden="true"
            class="home-hero-fish" />
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <!-- BKG -->

    <!-- SINGLE BKG -->
    <?php if(!empty($feat_thumbnail)): ?>
    <div class="object-cover-wrap hero-bkg top-center">
        <?php echo '<img rel="preload" fetchpriority="high" as="image" src="'.get_the_post_thumbnail_url(null,'xlarge').'" srcset="'.get_the_post_thumbnail_url(null,'mobile3').' 640w, '.get_the_post_thumbnail_url(null,'mobile2').' 768w, '.get_the_post_thumbnail_url(null,'mobile1').' 1024w, '.get_the_post_thumbnail_url(null,'large').' 1200w, '.get_the_post_thumbnail_url(null,'xlarge').' 1440w" alt="'.$feat_image_alt.'">'; ?>
    </div>
    <?php endif; ?>
    <!-- SINGLE BKG -->

    <?php if($disable_wave_spacing != '1'): ?>
    <div class="wave-gap"></div>
    <?php endif; ?>

</header>