<?php get_header(); ?>
<?php if (have_posts()) : ?>
  <?php while (have_posts()) : the_post(); ?>
    <article class="projet">
      <div class="content">
        <?php the_content(); ?>
      	<!-- 
		<?php
			tablepress_print_table( array( 'id' => '2', 'use_datatables' => true, 'print_name' => true, 'cache_table_output=false' ) );
		?>
		 -->
		</div>
    </article>
  <?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>