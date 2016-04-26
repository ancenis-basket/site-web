<?php

/**
 * Extended Views settings.
 *
 * @author     Time.ly Network Inc.
 * @since      1.0
 *
 * @package    AI1ECEV
 * @subpackage AI1ECEV.Model
 */
class Ai1ec_Model_Settingsev extends Ai1ec_Base {

	/**
	 * Add views to the Super Widget dropdown
	 *
	 * @param array $options
	 * @param string $id
	 *
	 * @return array
	 */
	public function add_views_to_dropdown( $options, $id ) {
		if(
			'superwidget_display_type' === $id ||
			(
				'action' === $id &&
				isset( $_GET['page'] ) &&
				'all-in-one-event-calendar-widget-creator' === $_GET['page']
			)
		) {
			$options[] = array(
				'text' => __( 'Posterboard', AI1ECEV_PLUGIN_NAME ),
				'value' => 'posterboard'
			);
			$options[] = array(
				'text' => __( 'Stream', AI1ECEV_PLUGIN_NAME ),
				'value' => 'stream'
			);
		}
		return $options;
	}
}