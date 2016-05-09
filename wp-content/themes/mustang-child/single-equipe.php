<?php get_header(); ?>
<?php if (have_posts()) : ?>
  <?php while (have_posts()) : the_post(); ?>
    <article class="projet">
      <?php the_post_thumbnail( 'large' ); ?>
      <h1 class="title">
        <?php the_title(); ?>
      </h1>
      <div class="content">
        <?php the_content(); ?>
      <p>ehoooo</p>
<?php
$post_objects = get_field('joueurs');
if( $post_objects ): ?>
    <ul>
    <?php foreach( $post_objects as $post_object): ?>
        <li>
            <a href="<?php echo get_permalink($post_object->ID); ?>"><?php echo get_the_title($post_object->ID); ?></a>
            <span>Post Object Custom Field: <?php the_field('field_name', $post_object->ID); ?></span>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif;


tablepress_print_table( array( 'id' => '2', 'use_datatables' => true, 'print_name' => true ) );
?>


</div>

    </article>
  <?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>