<?php

/**
 * The class which adds JavaScript for extended views.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Facebook
 */
class Ai1ec_Javascript_Extended_Views extends Ai1ec_Base {

	/**
	 * Adds the JavaScript for extended views if on the calendar page.
	 *
	 * @param array  $files
	 * @param string $page_to_load
	 *
	 * @return array
	 */
	public function add_js( array $files, $page_to_load ) {
		switch ( $page_to_load ) {
			case Ai1ec_Javascript_Controller::CALENDAR_PAGE_JS:
			case Ai1ec_Javascript_Controller::LOAD_ONLY_FRONTEND_SCRIPTS:
				$files[] = AI1ECEV_PATH . '/public/js/pages/extended_views.js';
				break;

			case 'ai1ec_widget.js':
			case 'main_widget.js':
				$files[] = array(
					'url' => AI1ECEV_URL . '/public/js/pages/extended_views.js',
					'id'  => 'ai1ec_extended_views_js'
				);
				break;
		}
		return $files;
	}

	/**
	 * Adds inline JavaScript to calendar template.
	 *
	 * @param string $code
	 *
	 * @return string
	 */
	 public function add_inline_js_calendar( $code ) {
		$parser = $this->_registry->get( 'http.request.parser' );
		$parser->parse();
		$action = $parser->get( 'action' );
		if (
			! $this->_registry->get( 'http.request' )->is_ajax() &&
			'posterboard' == $action
		) {
			$js_path = AI1ECEV_PATH . DIRECTORY_SEPARATOR;
			$code .= file_get_contents(
				$js_path . 'public' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR .
				'scripts' . DIRECTORY_SEPARATOR . 'calendar'. DIRECTORY_SEPARATOR .
				'fix_posterboard_columns.js'
			);
		}
		return $code;
	}
}
