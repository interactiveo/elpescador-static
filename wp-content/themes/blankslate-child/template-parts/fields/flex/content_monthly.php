<div class="wrap-x">
    <div class="inside">
        <article class="row main--row between-xs">

            <?php if( have_rows('content_monthly') ): ?>
            <?php while( have_rows('content_monthly') ) : the_row(); ?>
            <?php $label = get_sub_field('label'); ?>
            <?php $description = get_sub_field('description'); ?>

            <section class="col col-xs-12 col-md-6 col-lg-3 monthly-col">
                <?php if(!empty($label)): echo '<h3 class="label">'.$label.'</h3>'; endif; ?>
                <?php if(!empty($description)): echo $description; endif; ?>
            </section>

            <?php endwhile; ?>
            <?php endif; ?>

        </article>
    </div>
</div>