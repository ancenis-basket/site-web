<?php
/**
 * Plugins Installation and Activation
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Plugins
 * @copyright   2014 WebMan - Oliver Juhas
 *
 * @since    1.0
 * @version  1.5.4
 *
 * CONTENT:
 * - 10) Actions and filters
 * - 20) Funcions
 * - 30) Admin notification
 */





/**
 * 10) Actions and filters
 */

	/**
	 * Actions
	 */

		//Include the TGM_Plugin_Activation class.
			add_action( 'tgmpa_register', 'wm_register_required_plugins' );





/**
 * 20) Funcions
 */

	/**
	 * Register the required plugins for the theme
	 *
	 * @link  https://github.com/thomasgriffin/TGM-Plugin-Activation/blob/develop/tgm-plugin-activation/example.php
	 */
	if ( ! function_exists( 'wm_register_required_plugins' ) ) {
		function wm_register_required_plugins() {

			/**
			 * Array of plugin arrays. Required keys are name and slug.
			 * If the source is NOT from the .org repo, then source is also required.
			 */
			$plugins = apply_filters( 'wmhook_wm_register_required_plugins', array(

					/**
					 * Packed with the theme
					 */

						//Recommended
							'ms' => array(
								'name'         => 'Master Slider',
								'slug'         => 'masterslider',
								'source'       => WM_SETUP . 'plugins/masterslider.zip',
								'required'     => false,
								'version'      => '2.20.4',
							),
							'ls' => array(
								'name'         => 'LayerSlider',
								'slug'         => 'LayerSlider',
								'source'       => WM_SETUP . 'plugins/layersliderwp.zip',
								'required'     => false,
								'version'      => '5.6.2',
							),
							'vc' => array(
								'name'         => 'Visual Composer',
								'slug'         => 'js_composer',
								'source'       => WM_SETUP . 'plugins/js_composer.zip',
								'required'     => false,
								'version'      => '4.7.4',
							),

					/**
					 * WordPress Repository plugins
					 */

						//Required

							'wma' => array(
								'name'             => 'WebMan Amplifier',
								'slug'             => 'webman-amplifier',
								'required'         => true,
								'version'          => '1.2.5',
								'force_activation' => true,
							),

						//Recommended

							'ws' => array(
								'name'     => 'WooSidebars',
								'slug'     => 'woosidebars',
								'required' => false,
							),
							'bnxt' => array(
								'name'     => 'Breadcrumb NavXT',
								'slug'     => 'breadcrumb-navxt',
								'required' => false,
							),
							'cei' => array(
								'name'     => 'Customizer Export/Import',
								'slug'     => 'customizer-export-import',
								'required' => false,
							),

				) );



			/**
			 * Array of configuration settings
			 */
			$config = apply_filters( 'wmhook_wm_register_required_plugins_config', array() );



			/**
			 * Actual action...
			 */
			tgmpa( $plugins, $config );

		}
	} // /wm_register_required_plugins





/**
 * 30) Admin notification
 */

	/**
	 * Admin notification about premium plugins
	 */
	if ( ! function_exists( 'wm_premium_plugins_admin_notification' ) ) {
		function wm_premium_plugins_admin_notification() {

			// Admin notice

				set_transient(
						'wm-admin-notice',
						array(
							'<big>
							<strong>This is an important security update! Please <a href="http://www.webmandesign.eu/manual/mustang/#plugins-license" target="_blank">update your premium plugins included with the theme manually via FTP</a>.</strong><br><br>
							<strong style="text-decoration: underline;">Also, please note that this is the last theme udpate containing the premium plugins for free!</strong>
							</big><br>
							Due to <strong>recent XSS security vulnerabilities in Visual Composer plugin</strong> I decided not to include the premium plugins with the theme any more, <strong>starting with the next theme version 1.5.5</strong>.<br>
							Instead, please <a href="http://www.webmandesign.eu/manual/mustang/#plugins-license" target="_blank">purchase the liceses for the plugins you use separatelly to keep them up to date</a> and your website secure for all the time.<br>
							I appologize if this has caused inconvenience to you, but website security is the most priority to me and should be for everyone.<br><br>
							Thank you for understanding!<br><br>
							Oliver from WebMan Design<br><br>
							<small><em>This message will disappear automatically after 3 displays.</em></small>', // text
							'error', // class
							'switch_themes', // capability
							3 // number of displays
						),
						60 * 60 * 24
					);

		}
	} // /wm_premium_plugins_admin_notification

	add_action( 'wmhook_theme_upgrade', 'wm_premium_plugins_admin_notification' );
