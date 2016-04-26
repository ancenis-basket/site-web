<?php

/**
 * The concrete class for stream view.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View
 */
class Ai1ec_Calendar_View_Stream extends Ai1ec_Calendar_View_Extended_Views {

	/* (non-PHPdoc)
	 * @see Ai1ec_Calendar_View_Abstract::get_name()
	*/
	public function get_name() {
		return 'stream';
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Calendar_View_Agenda::get_content()
	*/
	public function get_content( array $view_args ) {
		$this->_registry->get( 'view.calendar.fallbacks' )->set(
			array(
				'post_thumbnail',
				'content_img',
				'category_avatar',
				'default_avatar'
			)
		);
		return parent::get_content( $view_args );
	}

	/**
	 * Add Stream-specific arguments to template.
	 *
	 * @param array $args Twig args.
	 *
	 * @return array Twig args.
	 */
	public function get_extra_template_arguments( array $args ) {
		$args['text'] = array(
			'no_results' => __(
				'There are no upcoming events to display at this time.',
				AI1ECEV_PLUGIN_NAME
			),
			'edit' => __(
				'Edit',
				AI1ECEV_PLUGIN_NAME
			),
		);
		$args['action_buttons'] = apply_filters(
			'ai1ec_action_buttons',
			''
		);
		$show_location_in_title = (bool)$args['show_location_in_title'];
		if (
			! apply_filters(
				'ai1ecev_show_location_in_title',
				$show_location_in_title
			)
		) {
			return $args;
		}
		do_action( 'ai1ecev_posterboard_before_event_location', $args );
		foreach ( $args['dates'] as $date => &$date_info ) {
			foreach ( $date_info['events'] as &$category ) {
				foreach ( $category as &$event ) {
					$venue = apply_filters(
						'ai1ecev_posteboard_event_venue',
						null,
						$event
					);
					if ( ! empty( $venue ) ) {
						$this->_set_event_value(
							$event,
							'event_location',
							$venue
						);
					}
				}
			}
		}
		return $args;
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Calendar_View_Abstract::_add_view_specific_runtime_properties()
	*/
	protected function _add_view_specific_runtime_properties(
		Ai1ec_Event $event
	) {
		parent::_add_view_specific_runtime_properties( $event );
		$taxonomy = $this->_registry->get( 'view.event.taxonomy' );
		$event->set_runtime(
			'categories_html',
			$taxonomy->get_categories_html( $event, 'inline' )
		);
		$event->set_runtime(
			'tags_html',
			$taxonomy->get_tags_html( $event, 'inline' )
		);
		$event->set_runtime(
			'category_text_color',
			$taxonomy->get_category_text_color( $event )
		);
	}
}
