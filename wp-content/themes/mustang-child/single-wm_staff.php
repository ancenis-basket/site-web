<?php get_header(); ?>
<?php get_template_part('content-shortcode-posts', 'wm_staff'); ?>
<?php if (have_posts()) : ?>
  <?php while (have_posts()) : the_post(); ?>
	
	<article class="projet">
		<div class="content">
    	    <?php the_content(); ?>
		</div>
	</article>
	<?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>
