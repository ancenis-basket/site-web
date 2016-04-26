<?php
/**
 * Posts shortcode item template
 *
 * Default product item template
 * Consist of:
 * 		image,
 * 		title,
 * 		price,
 * 		taxonomy:product_cat
 *
 * @package     WebMan Amplifier
 * @subpackage  Shortcodes
 *
 * @uses        array $helper  Contains shortcode $atts array plus additional helper variables.
 */



$link_output = array( '', '' );

if ( $helper['link'] ) {
	$link_output = array( '<a' . $helper['link'] . wm_schema_org( 'bookmark' ) . '>', '</a>' );
}
?>

<article class="woocommerce <?php echo $helper['item_class']; ?>"<?php echo wm_schema_org( 'creative_work' ); ?>>

	<?php
	if ( has_post_thumbnail( $helper['post_id'] ) ) {
		echo '<div class="wm-posts-element wm-html-element image image-container scale-rotate"' . wm_schema_org( 'image' ) . '>';

			echo $link_output[0];

			the_post_thumbnail( $helper['image_size'], array( 'title' => esc_attr( get_the_title( get_post_thumbnail_id( $helper['post_id'] ) ) ) ) );

			echo $link_output[1];

			wm_wc_buy_wrapper_open();
			woocommerce_template_loop_add_to_cart();
			woocommerce_template_loop_price();
			wm_wc_buy_wrapper_close();

		echo '</div>';
	}
	?>

	<div class="wm-posts-element wm-html-element title"><?php
		echo '<' . $helper['atts']['heading_tag'] . wm_schema_org( 'name' ) . '>';

			echo $link_output[0];

			the_title();

			echo $link_output[1];

		echo '</' . $helper['atts']['heading_tag'] . '>';
	?></div>

	<?php
		global $product;

		if ( $price_html = $product->get_price_html() ) {
			echo '<div class="wm-posts-element wm-html-element price">' . $price_html . '</div>' ;
		}
	?>

	<?php
	/*
		$terms       = get_the_terms( $helper['post_id'], 'product_cat' );
		$terms_array = array();
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach( $terms as $term ) {
				$terms_array[] = '<span class="term term-' . sanitize_html_class( $term->slug ) . '"' . wm_schema_org( 'itemprop="keywords"' ) . '>' . $term->name . '</span>';
			}
			echo '<div class="wm-posts-element wm-html-element taxonomy">' . implode( ', ', $terms_array ) . '</div>' ;
		}
	*/
	?>

</article>