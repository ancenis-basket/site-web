<?php
/**
 * Plugin Name: All-in-One Event Calendar Extended Views by Time.ly
 * Plugin URI: http://time.ly/
 * Description: Posterboard and Stream views for Time.ly's All-in-One Event Calendar plugin
 * Author: Time.ly Network Inc.
 * Author URI: http://time.ly/
 * Version: 1.2.6
 * Text Domain: all-in-one-event-calendar-extended-views
 * Domain Path: /language
 */

// Define constants for extension.
define( 'AI1ECEV_VERSION', '1.2.6' );
define( 'AI1ECEV_PLUGIN_NAME', 'all-in-one-event-calendar-extended-views' );
define( 'AI1ECEV_PATH',         dirname( __FILE__ ) );
define( 'AI1ECEV_URL',          plugins_url( '', __FILE__ ) );
define( 'AI1ECEV_FILE',         __FILE__ );

function ai1ec_extended_views( Ai1ec_Registry_Object $registry ) {
	$registry->extension_acknowledge( AI1ECEV_PLUGIN_NAME, AI1ECEV_PATH );
	load_plugin_textdomain(
		AI1ECEV_PLUGIN_NAME,
		false,
		basename( AI1ECEV_PATH ) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR
	);
	$registry->get( 'controller.ai1ecev' )->init( $registry );
}

// on activation all plugins are loaded but plugins_loaded has not been triggered.
function ai1ec_extended_views_activation() {
	global $ai1ec_registry;
	// if no global registry is set, core is not active
	// i could have checked for existance of extension class but class_exist calls are not reliable
	if (
		null === $ai1ec_registry ||
		! ( $ai1ec_registry instanceof Ai1ec_Registry_Object )
	) {
		return trigger_error(
			__(
				'All In One Event Calendar Core must be installed to activate extensions',
				AI1ECEV_PLUGIN_NAME
			),
			E_USER_ERROR
		);
	}
	require_once AI1ECEV_PATH . DIRECTORY_SEPARATOR . 'app' .
		DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR .
		'ai1ecev.php';
	// no need to register this, we are redirected afterwards.
	$controller = new Ai1ec_Controller_Ai1ecev();
	if ( ! $controller->check_compatibility( AI1EC_VERSION ) ) {
		// This works here as referrer is not checked.
		$controller->on_deactivation();
		$message = __(
			'Could not activate the Extended Views add-on: All-in-One Event Calendar version %s or higher is required.',
			AI1ECEV_PLUGIN_NAME
		);
		$version = $controller->minimum_core_required();
		$message = sprintf( $message, $version );
		return trigger_error( $message, E_USER_ERROR );
	}
	$controller->show_settings( $ai1ec_registry );
}

register_activation_hook( __FILE__, 'ai1ec_extended_views_activation' );
add_action( 'ai1ec_loaded', 'ai1ec_extended_views', 9 );
