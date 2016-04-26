<?php
	$post = get_post($_POST['id']);
	
	global $post;
	
	$meta_element_align = get_post_meta(get_the_ID(), 'custom_element_grid_align_meta_box', true); 
	if( empty($meta_element_align) ) { $meta_element_align = 'left'; };
	$vc_showcase_facebook = get_post_meta(get_the_ID(), 'vc_showcase_facebook', true); 
	$vc_showcase_google_plus = get_post_meta(get_the_ID(), 'vc_showcase_google_plus', true); 
	$vc_showcase_linkedin = get_post_meta(get_the_ID(), 'vc_showcase_linkedin', true); 
	$vc_showcase_custom_link = get_post_meta(get_the_ID(), 'vc_showcase_custom_link', true); 
?>
<div id="post-showcase" class="row">
<?php while (have_posts()) : the_post();?>
	<section id="showcase-post-<?php the_ID(); ?>">
		<a id="showcase-closebtn">X</a>
		<article class="vc_col-xs-12 vc_col-sm-12 vc_col-md-12 vc_col-lg-12">

		<?php
		if ( has_post_thumbnail() ) { 	
			if($meta_element_align == 'center'){
				$class_sc_figure = 'vc_col-xs-12 vc_col-sm-12 vc_col-md-12 vc_col-lg-12 vc_gitem-float-none';
				$class_sc_blockquote = 'vc_col-xs-12 vc_col-sm-12 vc_col-md-12 vc_col-lg-12';
				$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), large );
			}elseif($meta_element_align == 'right'){
				$class_sc_figure = 'vc_col-xs-4 vc_col-sm-4 vc_col-md-4 vc_col-lg-4 vc_gitem-float-right ';
				$class_sc_blockquote = 'vc_col-xs-7 vc_col-sm-7 vc_col-md-7 vc_col-lg-7';
				$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), thumbnail );
			}else{
				$class_sc_figure = 'vc_col-xs-4 vc_col-sm-4 vc_col-md-4 vc_col-lg-4 vc_gitem-float-left';
				$class_sc_blockquote = 'vc_col-xs-7 vc_col-sm-7 vc_col-md-7 vc_col-lg-7';
				$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), thumbnail );
			};?>
			<figure class="<?php echo $class_sc_figure;?>">
				<?php echo '<img src="' . $src[0] . '" alt="'. get_the_title() .'" title="'. get_the_title() .'"  class="showcase-brand" />'; ?>
			</figure>
		<?php }; ?>
			
			<blockquote class="<?php echo $class_sc_blockquote;?>">
		 		<h4 class="showcase-post-title"><?php the_title(); ?></h4>
		    	<div class="showcase-blog-detail">
		    	<?php

		    		the_content();
					
					if( !empty($vc_showcase_facebook) || !empty($vc_showcase_google_plus) || !empty($vc_showcase_linkedin) || !empty($vc_showcase_custom_link) ) : ?>
					<dl id="showcase-icons">
						<dt><?php _e('Links:', 'showcase-visual-composer-addon');?></dt>
						<?php if( !empty($vc_showcase_facebook) ) : ?><dd class="sc-icon-link sc-icon-facebook"><?php echo '<a href="'.$vc_showcase_facebook.'" title="Facebook - '.$vc_showcase_facebook.'" target="_blank">' . $vc_showcase_facebook . '</a>';?></dd><?php endif; ?>
						<?php if( !empty($vc_showcase_google_plus) ) : ?><dd class="sc-icon-link sc-icon-google-plus"><?php echo '<a href="'.$vc_showcase_google_plus.'" title="Google Plus - '.$vc_showcase_google_plus.'" target="_blank">' . $vc_showcase_google_plus . '</a>';?></dd><?php endif; ?>
						<?php if( !empty($vc_showcase_linkedin) ) : ?><dd class="sc-icon-link sc-icon-linkedin"><?php echo '<a href="'.$vc_showcase_linkedin.'" title="LinkedIn - '.$vc_showcase_linkedin.'" target="_blank">' . $vc_showcase_linkedin . '</a>';?></dd><?php endif; ?>
						<?php if( !empty($vc_showcase_custom_link) ) : ?><dd class="sc-icon-link sc-icon-custom-link"><?php echo '<a href="'.$vc_showcase_custom_link.'" title="'.$vc_showcase_custom_link.'" target="_blank">' . $vc_showcase_custom_link . '</a>';?></dd><?php endif; ?>
					</dl><?php
					endif;
	    		?>
		    	</div>
	    	</blockquote>
		</article> 
	</section><?php

	echo sprintf( '<script type="text/javascript" src="%1$s"></script>', plugins_url('/assets/js/single.showcase.min.js' , __FILE__ ) );
	
endwhile; ?>

</div>