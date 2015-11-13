<?php
/**
 * bbPress plugin integration
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Theme Setup
 * @copyright   2014 WebMan - Oliver Juhas
 *
 * CONTENT:
 * - 1) Declare support
 * - 10) Actions and filters
 * - 20) Functions
 */





/**
 * 1) Declare support
 */

	//Enable forum thumb
		add_post_type_support( bbp_get_forum_post_type(), array( 'thumbnail' ) );





/**
 * 10) Actions and filters
 */

	/**
	 * Actions
	 */

		//Topic title display
			if ( ! apply_filters( 'wmhook_enable_large_topic', true ) ) {
				add_action( 'bbp_theme_before_reply_content', 'wm_bbp_topic_title', 10 );
			}
		//Forum search
			add_action( 'bbp_template_before_single_forum', 'wm_bbp_search_form', 10 );
			add_action( 'bbp_template_before_single_forum', 'wm_bbp_search_form', 10 );
		//Large topic display
			if ( apply_filters( 'wmhook_enable_large_topic', true ) ) {
				add_action( 'wmhook_content_top', 'wm_bbp_large_topic', 100 );
			}
		//HTML alterations
			add_action( 'bbp_theme_before_topic_started_in', 'wm_bbp_topic_in', 10 );

			add_action( 'bbp_theme_before_topic_form_type',   'wm_bbp_column_half_open',      10 ); // .wm-row > .wm-column
			add_action( 'bbp_theme_after_topic_form_type',    'wm_bbp_div_close',             10 ); // /.wm-column
			add_action( 'bbp_theme_before_topic_form_status', 'wm_bbp_column_half_last_open', 10 ); // .wm-column.last
			add_action( 'bbp_theme_after_topic_form_status',  'wm_bbp_div_close',             10 ); // /.wm-column
			add_action( 'bbp_theme_after_topic_form_status',  'wm_bbp_div_close',             20 ); // /.wm-row

			add_action( 'bbp_theme_before_forum_form_type',         'wm_bbp_column_half_open',      10 ); // .wm-row > .wm-column
			add_action( 'bbp_theme_after_forum_form_status',        'wm_bbp_div_close',             10 ); // /.wm-column
			add_action( 'bbp_theme_before_forum_visibility_status', 'wm_bbp_column_half_last_open', 10 ); // .wm-column.last
			add_action( 'bbp_theme_after_forum_form_parent',        'wm_bbp_div_close',             10 ); // /.wm-column
			add_action( 'bbp_theme_after_forum_form_parent',        'wm_bbp_div_close',             20 ); // /.wm-row
		//Anonymous form wrapper
			add_action( 'bbp_theme_before_anonymous_form', 'wm_bbp_anonymous_form', 10 );
			add_action( 'bbp_theme_after_anonymous_form',  'wm_bbp_div_close',      10 );
		//"Topics" and "SubForums" headings
			add_action( 'bbp_template_before_forums_loop',    'wm_bbp_subforums_heading', 10 );
			add_action( 'wmhook_bbp_after_topic_form_toggle', 'wm_bbp_topics_heading',    10 );
		//Copy bbPress notices before accordion form on single forum
			add_action( 'bbp_template_before_single_forum', 'wm_bbp_accordion_forum_form_notices', 10 );



	/**
	 * Filters
	 */

		//Topic title display
			if ( apply_filters( 'wmhook_enable_large_topic', true ) ) {
				add_filter( 'wmhook_wm_section_heading_args', 'wm_bbp_topic_main_heading', 10 );
			}
		//Modify topic classes
			add_filter( 'bbp_get_forum_class', 'wm_bbp_additional_class', 98 );
			add_filter( 'bbp_get_topic_class', 'wm_bbp_additional_class', 98 );
		//Remove bbPress breadcrumbs
			add_filter( 'bbp_no_breadcrumb', '__return_true' );
		//Avatars
			add_filter( 'get_avatar',                                 'wm_bbp_avatar_wrapper',     10, 5 );
			add_filter( 'bbp_after_get_topic_author_link_parse_args', 'wm_bbp_avatar_size'               );
			add_filter( 'bbp_after_get_author_link_parse_args',       'wm_bbp_avatar_size'               );
			add_filter( 'bbp_single_user_details_avatar_size',        'wm_bbp_account_avatar_size'       );
		//Remove forum and single topic summaries at the top of the page
			add_filter( 'bbp_get_single_forum_description', '__return_false', 10 );
			add_filter( 'bbp_get_single_topic_description', '__return_false', 10 );
		//Apply content filters on Forum description
			add_filter( 'bbp_get_forum_content', 'wm_bbp_content_container' );
			add_filter( 'bbp_get_reply_content', 'wm_bbp_content_container' );
		//Theme single template content types
			add_filter( 'wmhook_loop_singular_content_type', 'wm_bbp_content_type' );
		//Posts shortcode implementation
			add_filter( 'wmhook_shortcode_post_types', 'wm_bbp_shortcode_posts_post_types', 20    );
			add_filter( 'wmhook_wm_post_meta',         'wm_bbp_post_custom_metas',          20, 3 );





/**
 * 20) Functions
 */

	/**
	 * Theme single template content types
	 *
	 * @param  array $content_type
	 */
	if ( ! function_exists( 'wm_bbp_content_type' ) ) {
		function wm_bbp_content_type( $content_type ) {
			//Preparing output
				if (
						bbp_is_search()
						|| bbp_is_topic_tag()
						|| bbp_is_single_view()
					) {
					$content_type = 'bbpress-archive';
				} elseif (
						bbp_is_single_reply()
						|| bbp_is_topic_edit()
						|| bbp_is_topic_merge()
						|| bbp_is_topic_split()
						|| bbp_is_reply_edit()
						|| bbp_is_reply_move()
						|| bbp_is_topic_tag_edit()
						|| bbp_is_single_user()
						|| bbp_is_single_user_edit()
					) {
					$content_type = 'bbpress-article';
				}

			//Output
				return apply_filters( 'wmhook_wm_bbp_content_type_output', $content_type );
		}
	} // /wm_bbp_content_type



	/**
	 * Topic classes
	 *
	 * @param  array $classes
	 */
	if ( ! function_exists( 'wm_bbp_additional_class' ) ) {
		function wm_bbp_additional_class( $classes ) {
			//Helper variables
				$voices_count  = bbp_get_topic_voice_count();
				$replies_count = ( bbp_show_lead_topic() ) ? ( bbp_get_topic_reply_count() ) : ( bbp_get_topic_post_count() );

				if ( bbp_get_forum_post_type() == get_post_type() ) {
					$voices_count  = bbp_get_forum_topic_count();
					$replies_count = ( bbp_show_lead_topic() ) ? ( bbp_get_forum_reply_count() ) : ( bbp_get_forum_post_count() );
				}

			//Preparing output
				$classes[] = ( 1 < $voices_count ) ? ( 'multi-voices' ) : ( 'single-voice' );
				$classes[] = ( 1 < $replies_count ) ? ( 'multi-replies' ) : ( 'single-reply' );

			//Output
				return apply_filters( 'wmhook_wm_bbp_additional_class_output', $classes );
		}
	} // /wm_bbp_additional_class



	/**
	 * Forum description
	 *
	 * @param  string $content
	 */
	if ( ! function_exists( 'wm_bbp_content_container' ) ) {
		function wm_bbp_content_container( $content ) {
			//Preparing output
				if ( $content ) {
					$content = '<div class="bbp-content-container">' . apply_filters( 'wmhook_content_filters', $content ) . '</div>';
				}

			//Output
				return apply_filters( 'wmhook_wm_bbp_content_container_output', $content );
		}
	} // /wm_bbp_content_container



	/**
	 * Large topic content output
	 */
	if ( ! function_exists( 'wm_bbp_large_topic' ) ) {
		function wm_bbp_large_topic() {
			global $paged;

			//Requirements check
				if ( ! ( bbp_is_single_topic() || bbp_is_single_reply() ) ) {
					return;
				}

			//Helper variables
				$output  = array();
				$post_id = ( bbp_is_single_reply() ) ? ( bbp_get_reply_topic_id() ) : ( get_the_id() );

			//Preparing output
				$output[10] = '<div class="bbp-large-topic">';
				$output[20] = '<div class="wrap-inner">';
				$output[30] = '<div class="content-area site-content pane twelve">';
					$output[100] = '<div ' . bbp_get_reply_class() . wm_schema_org( 'article' ) . '>';

					//Author
						$output[110] = '<div class="bbp-reply-author">';
							$output[120] = bbp_get_reply_author_link( array(
									'post_id'   => $post_id,
									'sep'       => '<br />',
									'show_role' => true
								) );
						$output[130] = '</div>'; // /.bbp-reply-author

					//Heading and content
						$output[200] = '<div class="bbp-reply-content">';
							$output[210] = '<h1 class="bbp-topic-title">';
							if ( 1 < $paged ) {
								$output[210] .= '<a href="' . get_permalink( $post_id ) . '">';
							}
							$output[210] .= bbp_get_topic_title( $post_id );
							if ( 1 < $paged ) {
								$output[210] .= '</a> ' . wm_paginated_suffix( 'small' );
							}
							$output[210] .= '</h1>';
							$output[220] = bbp_get_topic_tag_list( $post_id );
							if ( ! post_password_required( $post_id ) ) {
								$output[230] = '<div class="bbp-content-container">';
								setup_postdata( get_post( $post_id ) );
								$output[240] = apply_filters( 'wmhook_content_filters', bbp_get_topic_content( $post_id ), $post_id );
								wp_reset_postdata();
								$output[250] = '</div>'; // /.bbp-content-container
							}
						$output[260] = '</div>'; // /.bbp-reply-content

					//Meta
						$output[300] = '<div class="bbp-meta">';
							$output[310] = '<span class="bbp-reply-post-date">' . bbp_get_reply_post_date( $post_id ) . '</span>';
							if ( bbp_is_single_user_replies() ) {
								$output[320] = '<span class="bbp-header">';
									$output[330] = __( 'in reply to: ', 'wm_domain' );
									$output[340] = '<a class="bbp-topic-permalink" href="' . bbp_get_topic_permalink( bbp_get_reply_topic_id( $post_id ) ) . '">';
										$output[350] = bbp_get_topic_title( bbp_get_reply_topic_id( $post_id ) );
									$output[360] = '</a>'; // /.bbp-topic-permalink
								$output[370] = '</span>'; // /.bbp-header
							}
							$output[380] = bbp_get_reply_admin_links( array( 'id' => $post_id ) );
						$output[390] = '</div>'; // /.bbp-meta

					$output[500] = '</div>'; // /.bbp_get_reply_class()
				$output[600] = '</div>'; // /.content-area
				$output[610] = '</div>'; // /.wrap-inner
				$output[620] = '</div>'; // /.bbp-large-topic

			//Output
				$output = apply_filters( 'wmhook_wm_bbp_large_topic_output', $output, $post_id );
				echo implode( '', $output );
		}
	} // /wm_bbp_large_topic



	/**
	 * HTML wrappers
	 */

		/**
		 * "Topic in..." wrapper
		 */
		if ( ! function_exists( 'wm_bbp_topic_in' ) ) {
			function wm_bbp_topic_in() {
				echo '</p><p class="bbp-topic-meta topic-in">';
			}
		} // /wm_bbp_topic_in



		/**
		 * Column 1/2 open
		 */
		if ( ! function_exists( 'wm_bbp_column_half_open' ) ) {
			function wm_bbp_column_half_open() {
				echo '<div class="wm-row"><div class="wm-column width-1-2">';
			}
		} // /wm_bbp_column_half_open



		/**
		 * Column 1/2 last open
		 */
		if ( ! function_exists( 'wm_bbp_column_half_last_open' ) ) {
			function wm_bbp_column_half_last_open() {
				echo '<div class="wm-column width-1-2 last">';
			}
		} // /wm_bbp_column_half_last_open



		/**
		 * Column close
		 */
		if ( ! function_exists( 'wm_bbp_div_close' ) ) {
			function wm_bbp_div_close() {
				echo '</div>';
			}
		} // /wm_bbp_div_close



	/**
	 * Additional headings
	 */

		/**
		 * Topic title
		 */
		if ( ! function_exists( 'wm_bbp_topic_title' ) ) {
			function wm_bbp_topic_title() {
				//Helper variables
					$output = '';

				//Preparing output
					if ( bbp_get_topic_post_type() == get_post_type() ) {
						$output .= '<h1 class="bbp-topic-title">' . get_the_title() . '</h1>';
					}

				//Output
					echo apply_filters( 'wmhook_wm_bbp_topic_title_output', $output );
			}
		} // /wm_bbp_topic_title



			/**
			 * Topic main heading
			 *
			 * Removing the title from main heading
			 *
			 * @param  array $args
			 */
			if ( ! function_exists( 'wm_bbp_topic_main_heading' ) ) {
				function wm_bbp_topic_main_heading( $args ) {
					//Preparing output
						if ( bbp_get_topic_post_type() == get_post_type() ) {
							$args['output'] = "\r\n\r\n" . '<header id="main-heading" class="{class}">' . "\r\n" . apply_filters( 'wmhook_section_inner_wrappers', '' ) . '{addons}' . apply_filters( 'wmhook_section_inner_wrappers_close', '' ) . "\r\n" . '</header>' . "\r\n";
						}

					//Output
						return $args;
				}
			} // /wm_bbp_topic_main_heading



		/**
		 * "Topics" title
		 */
		if ( ! function_exists( 'wm_bbp_topics_heading' ) ) {
			function wm_bbp_topics_heading() {
				echo '<h2 class="bbp-topics-list-heading">' . sprintf( __( '<strong>Topics</strong> in "%s":', 'wm_domain' ), bbp_get_forum_title() ) . '</h2>';
			}
		} // /wm_bbp_topics_heading



		/**
		 * "SubForums" title
		 */
		if ( ! function_exists( 'wm_bbp_subforums_heading' ) ) {
			function wm_bbp_subforums_heading() {
				if ( ! bbp_is_single_forum() ) {
					return;
				}

				echo '<h2 class="bbp-forums-list-heading">' . sprintf( __( '<strong>Forums</strong> in "%s":', 'wm_domain' ), bbp_get_forum_title() ) . '</h2>';
			}
		} // /wm_bbp_subforums_heading



	/**
	 * Images
	 */

		/**
		 * Avatar size
		 *
		 * @param  array $atts
		 */
		if ( ! function_exists( 'wm_bbp_avatar_size' ) ) {
			function wm_bbp_avatar_size( $atts ) {
				//Preparing output
					if ( isset( $atts['size'] ) ) {
						$atts['size'] = 120;
					}

				//Output
					return apply_filters( 'wmhook_wm_bbp_avatar_size_output', $atts );
			}
		} // /wm_bbp_avatar_size



		/**
		 * User account page avatar size
		 *
		 * @param  absint $size
		 */
		if ( ! function_exists( 'wm_bbp_account_avatar_size' ) ) {
			function wm_bbp_account_avatar_size( $size ) {
				return absint( apply_filters( 'wmhook_wm_install_image_sizes_mobile_width_max', 520 ) );
			}
		} // /wm_bbp_account_avatar_size



		/**
		 * Avatar wrapper
		 *
		 * @param  string $avatar
		 * @param  int|string|object $id_or_email
		 * @param  absint $size
		 * @param  string $default
		 * @param  string $alt
		 */
		if ( ! function_exists( 'wm_bbp_avatar_wrapper' ) ) {
			function wm_bbp_avatar_wrapper( $avatar, $id_or_email, $size, $default, $alt ) {
				//Preparing output
					if ( is_bbpress() && $avatar ) {
						$avatar = '<span class="bbp-avatar-wrapper">' . $avatar . '</span>';
					}

				//Output
					return apply_filters( 'wmhook_wm_bbp_avatar_wrapper_output', $avatar );
			}
		} // /wm_bbp_avatar_wrapper



	/**
	 * Forms
	 */

		/**
		 * Anonymous form wrapper
		 */
		if ( ! function_exists( 'wm_bbp_anonymous_form' ) ) {
			function wm_bbp_anonymous_form() {
				echo '<div class="bbp-anonymous-form">';
			}
		} // /wm_bbp_anonymous_form



		/**
		 * Search form
		 */
		if ( ! function_exists( 'wm_bbp_search_form' ) ) {
			function wm_bbp_search_form() {
				//Helper variables
					$enable_button = apply_filters( 'wmhook_wm_bbp_search_form_enable_button', false );
					$single_forum  = ( $enable_button && bbp_is_single_forum() && bbp_current_user_can_access_create_topic_form() );

				//Output
					if ( bbp_allow_search() ) {
						echo '<div class="bbp-search-form">';

							if ( $single_forum ) {
								echo '<div class="wm-row"><div class="wm-column width-2-3">';
							}

							bbp_get_template_part( 'form', 'search' );

							if ( $single_forum ) {
								echo '</div><div class="wm-column width-1-3 last">';
								echo apply_filters( 'wmhook_wm_bbp_search_form_new_post_link', '<a href="#new-post" class="wm-button">' . __( 'Create a new topic', 'wm_domain' ) . '</a>' );
								echo '</div></div>';
							}

						echo '</div>';
					}
			}
		} // /wm_bbp_search_form



		/**
		 * Form notices
		 */
		if ( ! function_exists( 'wm_bbp_accordion_forum_form_notices' ) ) {
			function wm_bbp_accordion_forum_form_notices() {
				//Requirements check
					if ( ! bbp_is_single_forum() ) {
						return;
					}

				//Display notices before the accordion form
					do_action( 'bbp_template_notices' );
			}
		} // /wm_bbp_accordion_forum_form_notices



	/**
	 * Posts shortcode integration
	 */

		/**
		 * Adding new Posts shortcode supported post type
		 *
		 * @param  array $post_types
		 */
		if ( ! function_exists( 'wm_bbp_shortcode_posts_post_types' ) ) {
			function wm_bbp_shortcode_posts_post_types( $post_types ) {
				//Preparing output
					$forums_labels = bbp_get_forum_post_type_labels();
					$post_types[ bbp_get_forum_post_type() ] = $forums_labels['name'];

				//Output
					return $post_types;
			}
		} // /wm_bbp_shortcode_posts_post_types



		/**
		 * Forum custom meta (for Posts shortcode)
		 *
		 * @param  string $empty
		 * @param  string $meta
		 * @param  array  $args
		 */
		if ( ! function_exists( 'wm_bbp_post_custom_metas' ) ) {
			function wm_bbp_post_custom_metas( $empty, $meta, $args ) {
				//Requirements check
					if ( ! in_array( $meta, array( 'forum-update', 'forum-replies', 'forum-topics' ) ) ) {
						return $empty;
					}

				//Helper variables
					$meta_output = $output = $title = '';

					if ( 'forum-update' === $meta ) {
						$title       = __( 'Last update', 'wm_domain' );
						$meta_output = bbp_get_forum_freshness_link( $args['post_id'] );
					} elseif ( 'forum-topics' === $meta ) {
						$title       = __( 'Topics count', 'wm_domain' );
						$meta_output = bbp_get_forum_topic_count( $args['post_id'] );
					} elseif ( 'forum-replies' === $meta ) {
						$title       = __( 'Replies count', 'wm_domain' );
						$meta_output = bbp_get_forum_reply_count( $args['post_id'] );
					}

				//Add new meta
					$replacements = array(
							'{attributes}' => ' title="' . $title . '"',
							'{class}'      => 'entry-' . $meta . ' entry-meta-element',
							'{content}'    => $meta_output,
						);
					$replacements = apply_filters( 'wmhook_wm_bbp_post_custom_metas_replacements_' . $meta, $replacements );

					if ( isset( $args['html_custom'][$meta] ) ) {
						$output .= strtr( $args['html_custom'][$meta], $replacements );
					} else {
						$output .= strtr( $args['html'], $replacements );
					}

				//Output
					return apply_filters( 'wmhook_wm_bbp_post_custom_metas_output', $empty . $output, $meta );
			}
		} // /wm_bbp_post_custom_metas

?>