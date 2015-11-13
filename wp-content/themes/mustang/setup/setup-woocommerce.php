<?php
/**
 * WooCommerce plugin integration
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Theme Setup
 * @copyright   2014 WebMan - Oliver Juhas
 *
 * @since    1.0
 * @version  1.4.7
 *
 * CONTENT:
 * - 1) Declare support
 * - 10) Actions and filters
 * - 20) Functions
 * - 30) Redefined functions
 */





/**
 * 1) Declare support
 */

	//Declaring WooCommerce 2.0+ support
		add_theme_support( 'woocommerce' );





/**
 * 10) Actions and filters
 */

	/**
	 * Remove actions
	 */

		//Remove default WC content wrappers
			remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper',     10 );
			remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );
		//Remove WC breadcrumbs
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		//Remove taxonomy description
			remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
			remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description',  10 );
		//Reposition products list product info
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash',    10 );
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
			remove_action( 'woocommerce_after_shop_loop_item_title',  'woocommerce_template_loop_rating',            5  );
			remove_action( 'woocommerce_after_shop_loop_item',        'woocommerce_template_loop_add_to_cart',       10 );
		//Reposition single product elements
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
		//Cart elements reposition
			remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

	/**
	 * Actions
	 */

		//Custom WC content wrappers
			add_action( 'woocommerce_before_main_content', 'wm_wc_wrapper_top',    10 );
			add_action( 'woocommerce_after_main_content',  'wm_wc_wrapper_bottom', 10 );
		//Before shop loop item
			add_action( 'woocommerce_before_shop_loop_item', 'wm_wc_thumbnail_wrapper_open',                10 );
			add_action( 'woocommerce_before_shop_loop_item', 'woocommerce_show_product_loop_sale_flash',    20 );
			add_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_thumbnail', 30 );
			// add_action( 'woocommerce_before_shop_loop_item', 'wm_wc_additional_thumbnail',                  40 );
			add_action( 'woocommerce_before_shop_loop_item', 'wm_wc_thumbnail_wrapper_close',               60 );
		//Shop loop product image container
			add_action( 'woocommerce_loop_add_to_cart_link', 'wm_wc_buy_button_class', 10 );
			add_action( 'wmhook_wm_wc_thumbnail_wrapper_close', 'wm_wc_buy_wrapper_open',                10 );
			add_action( 'wmhook_wm_wc_thumbnail_wrapper_close', 'woocommerce_template_loop_add_to_cart', 20 );
			add_action( 'wmhook_wm_wc_thumbnail_wrapper_close', 'woocommerce_template_loop_price',       30 );
			add_action( 'wmhook_wm_wc_thumbnail_wrapper_close', 'wm_wc_buy_wrapper_close',               40 );
		//After shop loop item title
			add_action( 'woocommerce_after_shop_loop_item_title', 'wm_wc_price_wrapper_open',         5  );
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 20 );
			add_action( 'woocommerce_after_shop_loop_item_title', 'wm_wc_price_wrapper_close',        30 );
		//Reposition single product elements
			add_action( 'woocommerce_after_single_product_summary', 'woocommerce_template_single_sharing', 5 );
		//Cart
			add_action( 'woocommerce_before_cart', 'wm_wc_cart_subtitle',            10 );
			add_action( 'woocommerce_after_cart',  'woocommerce_cross_sell_display', 10 );
		//Sharing
			add_action( 'woocommerce_before_single_product',        'wm_wc_remove_jetpack_sharing', 10 );
			add_action( 'woocommerce_after_single_product_summary', 'wm_wc_jetpack_sharing',        12 );
		//Floating cart
			add_action( 'wmhook_footer_before', 'wm_wc_floating_cart', 90 );



	/**
	 * Filters
	 */

		//Remove WC H1 headings
			add_filter( 'woocommerce_show_page_title', '__return_false' );
		//Set products list columns
			add_filter( 'loop_shop_columns', 'wm_wc_products_columns' );
		//Pagination setup
			add_filter( 'woocommerce_pagination_args',         'wm_wc_pagination_args' );
			add_filter( 'woocommerce_comment_pagination_args', 'wm_wc_pagination_args' );
		//Set number of products to display
			add_filter( 'loop_shop_per_page',                       'wm_wc_products_count' );
			add_filter( 'woocommerce_output_related_products_args', 'wm_wc_related_count'  );
		//Cart
			add_filter( 'woocommerce_cross_sells_total',   'wm_wc_products_columns' );
			add_filter( 'woocommerce_cross_sells_columns', 'wm_wc_products_columns' );
		//Checkout
			add_filter( 'woocommerce_cart_item_name', 'wm_wc_checkout_thumbnail', 10, 3 );
		//Thank you page
			add_filter( 'woocommerce_order_item_name', 'wm_wc_checkout_thumbnail', 10, 3 );
		//Posts shortcode implementation
			add_filter( 'wmhook_shortcode_post_types', 'wm_wc_shortcode_posts_post_types', 10 );





/**
 * 20) Functions
 */

	/**
	 * WooCommerce content wrappers
	 */

		/**
		 * Wrapper open
		 */
		if ( ! function_exists( 'wm_wc_wrapper_top' ) ) {
			function wm_wc_wrapper_top() {
				//Helper variables
					global $wc_page_id;

					$wc_page_id = ( is_shop() ) ? ( wc_get_page_id( 'shop' ) ) : ( null );
					$atts       = apply_filters( 'wmhook_wm_wc_wrapper_sidebar_atts', array(
							'page_id' => $wc_page_id,
						), $wc_page_id );
					$sidebar    = wm_sidebar_setup( false, $atts );

					$class = 'woocommerce-content';

					if ( $sidebar['output'] ) {
						$class .= ' shop-columns-3';
					} else {
						$class .= ' shop-columns-4';
					}

					$class = apply_filters( 'wmhook_wm_wc_wrapper_class', $class );

				//Output
					echo "\r\n\r\n" . '<div class="wrap-inner">' . "\r\n\t" . '<div class="content-area site-content' . $sidebar['class_main'] . '">' . "\r\n\r\n";

					wmhook_entry_before();

					echo '<section class="' . $class . '">';
			}
		} // /wm_wc_wrapper_top



		/**
		 * Wrapper close
		 */
		if ( ! function_exists( 'wm_wc_wrapper_bottom' ) ) {
			function wm_wc_wrapper_bottom() {
				//Helper variables
					global $wc_page_id;

					$atts    = apply_filters( 'wmhook_wm_wc_wrapper_sidebar_atts', array(
							'page_id' => $wc_page_id,
						), $wc_page_id );
					$sidebar = wm_sidebar_setup( false, $atts );

				//Output
					echo '</section>';

					wmhook_entry_after();

					echo "\r\n\r\n\t" . '</div> <!-- /content-area -->';

					echo $sidebar['output'];

					echo "\r\n" . '</div> <!-- /wrap-inner -->' . "\r\n\r\n";
			}
		} // /wm_wc_wrapper_bottom



	/**
	 * Products columns
	 *
	 * @param  absint $columns
	 */
	if ( ! function_exists( 'wm_wc_products_columns' ) ) {
		function wm_wc_products_columns( $columns = 4 ) {
			//Helper variables
				$columns = 4;

				$wc_page_id = ( is_shop() ) ? ( wc_get_page_id( 'shop' ) ) : ( null );
				$atts       = apply_filters( 'wmhook_wm_wc_wrapper_sidebar_atts', array(
						'page_id' => $wc_page_id,
					), $wc_page_id );
				$sidebar    = wm_sidebar_setup( false, $atts );

			//Preparing output
				if ( $sidebar['output'] ) {
					$columns = 3;
				}

			//Output
				return apply_filters( 'wmhook_wm_wc_products_columns_output', $columns, $sidebar );
		}
	} // /wm_wc_products_columns



	/**
	 * Number of products to display
	 */
	if ( ! function_exists( 'wm_wc_products_count' ) ) {
		function wm_wc_products_count() {
			return 12;
		}
	} // /wm_wc_products_count



		/**
		 * Number of related products to display
		 *
		 * @param  array $args
		 */
		if ( ! function_exists( 'wm_wc_related_count' ) ) {
			function wm_wc_related_count( $args ) {
				//Preparing output
					$args['posts_per_page'] = $args['columns'] = wm_wc_products_columns();

				//Output
					return $args;
			}
		} // /wm_wc_related_count



	/**
	 * Pagination args
	 *
	 * @param  array $args
	 */
	if ( ! function_exists( 'wm_wc_pagination_args' ) ) {
		function wm_wc_pagination_args( $args ) {
			//Preparing output
				$args['next_text'] = '&raquo;';
				$args['prev_text'] = '&laquo;';
				$args['type']      = 'plain';

			//Output
				return apply_filters( 'wmhook_wm_wc_pagination_args_output', $args );
		}
	} // /wm_wc_pagination_args



	/**
	 * Product thumbnail wrapper open
	 */
	if ( ! function_exists( 'wm_wc_thumbnail_wrapper_open' ) ) {
		function wm_wc_thumbnail_wrapper_open() {
			echo '<div class="image-container"><a href="' . get_permalink() . '">';
		}
	} // /wm_wc_thumbnail_wrapper_open



		/**
		 * Product thumbnail wrapper close
		 */
		if ( ! function_exists( 'wm_wc_thumbnail_wrapper_close' ) ) {
			function wm_wc_thumbnail_wrapper_close() {
				echo '</a>';

				do_action( 'wmhook_wm_wc_thumbnail_wrapper_close' );

				echo '</div>';
			}
		} // /wm_wc_thumbnail_wrapper_close



	/**
	 * Product price wrapper open
	 */
	if ( ! function_exists( 'wm_wc_price_wrapper_open' ) ) {
		function wm_wc_price_wrapper_open() {
			echo '<div class="price-container">';
		}
	} // /wm_wc_price_wrapper_open



		/**
		 * Product price wrapper close
		 */
		if ( ! function_exists( 'wm_wc_price_wrapper_close' ) ) {
			function wm_wc_price_wrapper_close() {
				echo '</div>';
			}
		} // /wm_wc_price_wrapper_close



	/**
	 * Product buy button class
	 *
	 * @since    1.0
	 * @version  1.4.7
	 *
	 * @param  string $html
	 */
	if ( ! function_exists( 'wm_wc_buy_button_class' ) ) {
		function wm_wc_buy_button_class( $html ) {
			//Helper variables
				$class = '';

			//Requirements check
				if ( is_admin() ) {
					return;
				}

			//Preparing output
				$cart = WC()->cart->get_cart_item_quantities();
				if (
						is_array( $cart )
						&& ! empty( $cart )
						&& in_array( get_the_id(), array_keys( $cart ) )
					) {
					$class = 'added ';
				}

				$replacements = array(
						'class="button ' => 'class="iconwm-basket-1 ' . esc_attr( $class ),
						'">'             => '"><span class="screen-reader-text">',
						'</a>'           => '</span></a>',
					);
				$html = strtr( $html, $replacements );

			//Output
				return $html;
		}
	} // /wm_wc_buy_button_class



	/**
	 * Product buy wrapper open
	 */
	if ( ! function_exists( 'wm_wc_buy_wrapper_open' ) ) {
		function wm_wc_buy_wrapper_open() {
			echo apply_filters( 'wmhook_wm_wc_buy_wrapper_open_output', '<div class="buy-container"><div class="buy-table"><div class="buy-cell">' );
		}
	} // /wm_wc_buy_wrapper_open



		/**
		 * Product buy wrapper close
		 */
		if ( ! function_exists( 'wm_wc_buy_wrapper_close' ) ) {
			function wm_wc_buy_wrapper_close() {
				echo apply_filters( 'wmhook_wm_wc_buy_wrapper_close_open_output', '<a href="' . get_permalink() . '" class="details-button">' . __( 'Product details &raquo;', 'wm_domain' ) . '</a></div></div></div>' );
			}
		} // /wm_wc_buy_wrapper_close



	/**
	 * Additional product thumbnail image
	 */
	if ( ! function_exists( 'wm_wc_additional_thumbnail' ) ) {
		function wm_wc_additional_thumbnail() {
			//Helper variables
				$output = '';

				$image_size    = apply_filters( 'wmhook_wm_wc_additional_thumbnail_image_size', 'shop_catalog' );
				$product_image = get_post_meta( get_the_id(), '_product_image_gallery', true );

			//Preparing output
				if ( $product_image ) {
					$product_image = explode( ',', $product_image );
					$product_image = $product_image[0];

					$output = wp_get_attachment_image( $product_image, $image_size );
				}

			//Return
				echo apply_filters( 'wmhook_wm_wc_additional_thumbnail_output', $output );
		}
	} // /wm_wc_additional_thumbnail



		/**
		 * Checkout summary product thumbnails
		 *
		 * @param  string $title
		 * @param  $cart_item
		 * @param  $cart_item_key
		 */
		if ( ! function_exists( 'wm_wc_checkout_thumbnail' ) ) {
			function wm_wc_checkout_thumbnail( $title, $cart_item, $cart_item_key = '' ) {
				//Requirements check
					if ( is_cart() ) {
						return $title;
					}

				//Helper variables
					$output = '';

					$image_size = apply_filters( 'wmhook_wm_wc_checkout_thumbnail_image_size', 'shop_thumbnail' );

				//Preparing output
					$output .= '<a href="' . get_permalink( $cart_item['product_id'] ) . '" class="wm-product-thumbnail">';
					$output .= get_the_post_thumbnail( $cart_item['product_id'], $image_size );
					$output .= '</a>';

				//Return
					return apply_filters( 'wmhook_wm_wc_checkout_thumbnail_output', $output . $title, $title, $cart_item, $cart_item_key );
			}
		} // /wm_wc_checkout_thumbnail



	/**
	 * Cart subtitle
	 *
	 * @since    1.0
	 * @version  1.4.7
	 */
	if ( ! function_exists( 'wm_wc_cart_subtitle' ) ) {
		function wm_wc_cart_subtitle() {
			//Requirements check
				if ( is_admin() ) {
					return;
				}

			//Output
				echo apply_filters( 'wmhook_wm_wc_cart_subtitle_output', '<h2>' . sprintf( __( 'Number of items in cart: %s', 'wm_domain' ), WC()->cart->get_cart_contents_count() ) . '</h2>' );
		}
	} // /wm_wc_cart_subtitle



	/**
	 * Posts shortcode support
	 *
	 * @param  array $post_types
	 */
	if ( ! function_exists( 'wm_wc_shortcode_posts_post_types' ) ) {
		function wm_wc_shortcode_posts_post_types( $post_types ) {
			//Preparing output
				$post_types['product'] = __( 'Products', 'wm_domain' );

			//Output
				return $post_types;
		}
	} // /wm_wc_shortcode_posts_post_types



	/**
	 * Remove JetPack sharing
	 */
	if ( ! function_exists( 'wm_wc_remove_jetpack_sharing' ) ) {
		function wm_wc_remove_jetpack_sharing() {
			if ( is_singular( 'product' ) && function_exists( 'sharing_display' ) ) {
				remove_filter( 'the_content', 'sharing_display', 19 );
			}
		}
	} // /wm_wc_remove_jetpack_sharing



		/**
		 * Display JetPack sharing
		 */
		if ( ! function_exists( 'wm_wc_jetpack_sharing' ) ) {
			function wm_wc_jetpack_sharing() {
				if ( is_singular( 'product' ) && function_exists( 'sharing_display' ) ) {
					sharing_display( '', true );
				}
			}
		} // /wm_wc_jetpack_sharing



	/**
	 * Floating cart
	 */
	if ( ! function_exists( 'wm_wc_floating_cart' ) ) {
		function wm_wc_floating_cart( $return = false ) {
			//Requirements check
				if ( apply_filters( 'wmhook_wm_wc_floating_cart_disable', false ) ) {
					return;
				}

			//Helper variables
				$output = $widget_area = array();

				$widget_area_atts = apply_filters( 'wmhook_wm_wc_floating_cart_widget_area_atts', array(
						'max_widgets_count' => 2,
						'sidebar'           => 'floating-cart',
					) );
				if ( function_exists( 'wma_sidebar' ) ) {
					$widget_area = wma_sidebar( $widget_area_atts );
				}

				if ( empty( $widget_area ) ) {
					return;
				}

			//Preparing output
				$output[10] = "\r\n\r\n" . '<div id="floating-cart" class="floating-cart">';
				$output[20] = '<a href="' . get_permalink( wc_get_page_id( 'cart' ) ) . '" id="floating-cart-switch" class="floating-cart-switch"><span class="screen-reader-text">' . __( 'Show cart', 'wm_domain' ) . '</span></a>';
				$output[30] = '<div id="floating-cart-content" class="floating-cart-content woocommerce-page">'; //.woocommerce-page is here to inherit WC styles when not on WC page
				$output[40] = $widget_area;
				$output[50] = '</div>';
				$output[60] = "\r\n" . '</div>' . "\r\n";

			//Output
				$output = apply_filters( 'wmhook_wm_wc_floating_cart_output', $output );
				if ( ! $return ) {
					echo implode( '', $output );
				} else {
					return implode( '', $output );
				}
		}
	} // /wm_wc_floating_cart





/**
 * 30) Redefined functions
 */

	if ( ! function_exists( 'woocommerce_upsell_display' ) ) {

		/**
		 * Output product up sells.
		 *
		 * @access public
		 * @param int $posts_per_page (default: -1)
		 * @param int $columns (default: 2)
		 * @param string $orderby (default: 'rand')
		 * @return void
		 *
		 * @wmedit  Default $post_per_page, $columns
		 */
		function woocommerce_upsell_display( $posts_per_page = '-1', $columns = 2, $orderby = 'rand' ) {
			$posts_per_page = $columns = wm_wc_products_columns();

			wc_get_template( 'single-product/up-sells.php', array(
					'posts_per_page' => $posts_per_page,
					'orderby'        => apply_filters( 'woocommerce_upsells_orderby', $orderby ),
					'columns'        => $columns
				) );
		}

	} // /woocommerce_upsell_display



	/**
	 * WC 2.3+ support
	 *
	 * @since    1.3
	 * @version  1.3
	 */
	if ( ! function_exists( 'woocommerce_shipping_calculator' ) ) {
		/**
		 * Output the cart shipping calculator.
		 *
		 * @subpackage	Cart
		 */
		function woocommerce_shipping_calculator() {
			if ( ! is_cart() ) { //WebMan: for compatibility if used on other places
				wc_get_template( 'cart/shipping-calculator.php' );
			}
		}


		/**
		 * Output the cart shipping calculator.
		 *
		 * WebMan version to be hooked anywhere.
		 *
		 * @subpackage	Cart
		 */
		function wm_woocommerce_shipping_calculator() {
			wc_get_template( 'cart/shipping-calculator.php' );
		}
		add_action( 'woocommerce_cart_collaterals', 'wm_woocommerce_shipping_calculator', 98 );
	}

?>