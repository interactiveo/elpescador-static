<?php $menu_layout = get_sub_field('menu_layout'); ?>
<?php $menu_links = get_sub_field('menu_links'); ?>

<div class="wrap-x">
    <div class="inside">
        <div class="row menu-layout-<?php echo $menu_layout; ?>">

            <!-- CONTENT -->
            <?php $content_col = 'col-xs-12 col-top-content'; ?>
            <?php include get_stylesheet_directory() . '/template-parts/includes/content.php'; ?>
            <!-- CONTENT -->

            <?php if(!empty($menu_layout)): ?>
            <?php if(!empty($menu_links)): ?>

            <?php if($menu_layout == 'card2' || $menu_layout == 'card1'): ?>
            <?php if( have_rows('menu_links') ): ?>
            <div class="col col-xs-12">
                <div class="swiper-margins">
                    <div class="swiper" id="dragScroll">
                        <nav class="swiper-wrapper">
                            <?php while( have_rows('menu_links') ) : the_row(); ?>
                            <?php $menu_link = get_sub_field('menu_link'); ?>
                            <?php $title = $menu_link['title']; ?>
                            <?php $words = explode(" ", $title); ?>
                            <?php $firstWord = $words[0]; ?>
                            <?php $menu_image = get_sub_field('menu_image'); ?>


                            <?php if($menu_layout == 'card1'): ?>
                            <!-- CARD 1 -->
                            <div class="swiper-slide col-card-1">
                                <a href="<?php echo $menu_link['url']; ?>" class="menu-link-<?php echo $menu_layout; ?>"
                                    target="<?php echo $menu_link['target']; ?>" title="<?php echo $title; ?>">
                                    <h3 class="post-title"><?php echo $title; ?></h3>
                                    <p class="decorate-title" aria-hidden="true" lang="en"><?php echo $firstWord; ?></p>
                                    <?php if(!empty($menu_image)): ?>
                                    <div class="object-cover-wrap menu-bkg">
                                        <img src="<?php echo $menu_image['sizes']['card1']; ?>"
                                            alt="<?php echo $menu_image['alt']; ?>" loading="lazy" />
                                    </div>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <!-- CARD 1 -->

                            <?php elseif($menu_layout == 'card2'): ?>
                            <!-- CARD 2 -->
                            <div class="swiper-slide col-card-2">
                                <a href="<?php echo $menu_link['url']; ?>" class="menu-link-<?php echo $menu_layout; ?>"
                                    target="<?php echo $menu_link['target']; ?>" title="<?php echo $title; ?>">
                                    <h3 class="post-title"><?php echo $title; ?></h3>
                                    <p class="decorate-title" aria-hidden="true" lang="en"><?php echo $firstWord; ?></p>
                                    <?php if(!empty($menu_image)): ?>
                                    <div class="object-cover-wrap menu-bkg">
                                        <img src="<?php echo $menu_image['sizes']['medium']; ?>"
                                            alt="<?php echo $menu_image['alt']; ?>" loading="lazy" />
                                    </div>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <!-- CARD 2 -->

                            <?php endif; ?>


                            <?php endwhile; ?>
                        </nav>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php //menu-card 1,2 ?>
            <?php elseif($menu_layout == 'simple'): ?>
            <?php if( have_rows('menu_links') ): ?>
            <div class="col col-xs-12 col-simple-menu">
            <ul>
            <?php while( have_rows('menu_links') ) : the_row(); ?>
            <?php $menu_link = get_sub_field('menu_link'); ?>
            <?php $title = $menu_link['title']; ?>
            <li>
                <a href="<?php echo $menu_link['url']; ?>" class="simple-menu-link h5" target="<?php echo $menu_link['target']; ?>" title="<?php echo $menu_link['title']; ?>" role="button"><?php echo $menu_link['title']; ?></a>
            </li>
            <?php endwhile; ?>
            </ul>
            </div>
            <?php endif; ?>
            <?php endif; //menu-card 1,2 ?>

            <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</div>