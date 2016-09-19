<?php

/**
 * Single Forum Content Part
 *
 * @package bbPress
 * @subpackage Theme
 *
 * @edit  Removed bbp_get_template_part( 'form', 'topic' ) from bottom of the page and pushed to the top.
 */

?>

<div id="bbpress-forums">

	<?php bbp_breadcrumb(); ?>

	<?php bbp_forum_subscription_link(); ?>

	<?php do_action( 'bbp_template_before_single_forum' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php bbp_get_template_part( 'form', 'protected' ); ?>

	<?php else : ?>

		<?php bbp_single_forum_description(); ?>

		<?php if ( bbp_has_forums() ) : ?>

			<?php bbp_get_template_part( 'loop', 'forums' ); ?>

		<?php endif; ?>

		<?php
		//New post form
		if ( ! bbp_is_forum_category() ) {

			do_action( 'wmhook_bbp_before_topic_form_toggle' );

			if ( function_exists( 'wma_minify_html' ) ) {
				ob_start();
			}

				bbp_get_template_part( 'form', 'topic' );

			if ( function_exists( 'wma_minify_html' ) ) {
				$new_post_form = wma_minify_html( ob_get_clean() );

				if ( bbp_current_user_can_access_create_topic_form() ) {
					$new_post_form = do_shortcode( '[wm_accordion behaviour="toggle" class="bbp-new-post-toggle"][wm_item title="' . __( 'Create a new topic', 'mustang' ) . '"]' . $new_post_form . '[/wm_item][/wm_accordion]' );
				}

				echo apply_filters( 'wmhook_bbpress_new_topic_form', $new_post_form );
			}

			do_action( 'wmhook_bbp_after_topic_form_toggle' );

		}
		?>

		<?php if ( !bbp_is_forum_category() && bbp_has_topics() ) : ?>

			<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

			<?php bbp_get_template_part( 'loop',       'topics'    ); ?>

			<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

		<?php elseif ( !bbp_is_forum_category() ) : ?>

			<?php bbp_get_template_part( 'feedback',   'no-topics' ); ?>

		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_single_forum' ); ?>

</div>
