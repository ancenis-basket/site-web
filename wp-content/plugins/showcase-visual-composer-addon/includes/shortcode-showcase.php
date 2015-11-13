<?php
function showcase_box($attributes, $content){
	
extract(shortcode_atts(	array(
	'title'=> __('Showcases', 'sc-carousel-vc-addon'),
	'show_content' => 'vc_below',
	'sc_order' => 'DESC',
	'post_count' => '4',
	'auto_play' => '3000',
	'pagination_speed' => '800',
	'stop_hover' => 'false',
	'navigation' => 'false',
	'pagination' => 'true',
	'pagination_numbers' => 'false',
	'image_thumbnail' => 'showcase-circle',
	'showcase_thumb' => '',
	'base_class' => '',
	'showcase_color_title' => '#333333',
	'showcase_size_title' => '30',
	'showcase_size_align_title' => 'left',
	'showcase_border_color' => '#F4F4F4',
	'showcase_border_width' => '6',
	'showcase_border_radius' => '0',
	'showcase_border_style' => 'solid',
	'showcase_bg_color' => '#FCFCFC'
), $attributes));

ob_start();	

if( empty($showcase_thumb) ) : $showcase_thumb = 'thumb-brand-testimonial'; else : $showcase_thumb; endif;
if ($sc_order == 'rand'){ $value_order = 'orderby'; }else{ $value_order = 'order'; };

;?>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		$("#showcase-carousel").owlCarousel({
	        autoPlay: <?php echo $auto_play;?>,
	        stopOnHover : <?php echo $stop_hover;?>,
	        paginationSpeed : <?php echo $pagination_speed;?>,
	        itemsDesktop : [1199,4],
			itemsDesktopSmall : [980,3],
			itemsTablet: [768,2],
			itemsTabletSmall: false,
			itemsMobile : [479,1],
	        items : <?php echo $post_count;?>,
	        navigation : <?php echo $navigation;?>,
	        navigationText : ["<?php _e( 'Previous', 'showcase-visual-composer-addon' );?>", "<?php _e( 'Next', 'showcase-visual-composer-addon' );?>"],
	        pagination : <?php echo $pagination;?>,
	        paginationNumbers : <?php echo $pagination_numbers;?><?php if( !empty($base_class) ) : ?>,
	        baseClass : "<?php echo $base_class;?>"<?php endif;?>
	    });

		$.ajaxSetup({cache:false});

	 	$(".showcase-post-link").click(function(){
 		<?php if ($show_content == 'vc_floats' ){ ?>
 			$("body").prepend('<div id="single-showcase" class="row vc_floats"></div>');
 			$("body").addClass('vc-floats-fixed');
 			$( "#single-showcase.vc_floats" ).wrap( "<div id='box_vc_floats'></div>" );
		<?php } ?>

			$( "#single-showcase" ).show();
	 		$('#showcase-carousel a.current').removeClass('current');
    		$(this).addClass('current');

    		$( "#showcase-carousel a" ).fadeTo( "fast", 0.33 );
			$( "#showcase-carousel a.showcase-post-link.current" ).fadeTo( "fast", 1 );

	 		var post_link = $(this).attr("href");	
			    $("#single-showcase").html('<div class="vc_col-sm-12 vc_span12 row vc-loading">Loading...</div>');
			    $("#single-showcase").load(post_link);
			return false;

		});

	});
</script>
<style type="text/css">
	.showcase-post-title{
		color: <?php echo $showcase_color_title;?>;
		font-size: <?php echo $showcase_size_title;?>px;
		text-align: <?php echo $showcase_size_align_title;?>;
	}
	
	#post-showcase{
		background-color: <?php echo $showcase_bg_color;?>;
		border: <?php echo $showcase_border_width . 'px ' . $showcase_border_style . ' ' . $showcase_border_color;?> ;
		border-radius: <?php echo $showcase_border_radius;?>px;
		-moz-border-radius: <?php echo $showcase_border_radius;?>px;
		-webkit-border-radius: <?php echo $showcase_border_radius;?>px;
	}
</style>

<div id="showcases" class="vc_col-sm-12 vc_span12 row " role="main">

	<?php if($show_content == 'vc_above') { ?>
	<div id="single-showcase" class="vc_col-sm-12 vc_span12 row"></div>
	<?php };?>
	
	<div id="showcase-carousel" class="vc_col-sm-12 vc_span12 row owl-carousel">
<?php
	$qParams = array( 'post_type' => 'showcases', $value_order => $sc_order, 'posts_per_page' => -1, 'caller_get_posts'=> 1 );	$wpbp = new WP_Query( $qParams );
	if ($wpbp->have_posts()) : 
		while ($wpbp->have_posts()) : $wpbp->the_post();
		if ( has_post_thumbnail() ) {
			echo '
			<figure class="'.$image_thumbnail.' showcase-border">
				<a class="showcase-post-link" rel="' . get_the_ID() . '" href="'. get_permalink() .'" title="'.get_the_title().'">
				' . get_the_post_thumbnail( get_the_ID(), $showcase_thumb , array('class' => 'showcase-thumb')) . '
				</a>
			</figure>';
		}
		endwhile;
	wp_reset_query();
	endif;
?>
	</div>

	<?php if($show_content == 'vc_below') { ?>
	<div id="single-showcase" class="vc_col-sm-12 vc_span12 row"></div>
	<?php };?>
	
</div>

<?php 
$output = ob_get_contents();
ob_end_clean();	
return $output;
}

add_shortcode('sc_showcase_box', 'showcase_box');