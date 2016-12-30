<?php
/**
 * Event Slider shortcode
 *
 * Handles all shortcode related functions
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON-SL/Functions/shortcode
 * @version     0.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evosl_shortcode{
	
	function __construct(){
		add_shortcode('add_eventon_slider', array($this,'slider_events'));
		add_filter('eventon_shortcode_popup',array($this,'add_shortcode_options'), 10, 1);
		add_filter('eventon_shortcode_defaults',array($this,  'add_shortcode_defaults'), 10, 1);		
	}


	/**	Shortcode processing */	
		function slider_events($atts){
			global $eventon_sl, $eventon;

			// add el scripts to footer
			//add_action('wp_footer', array($eventon_sl, 'print_scripts'));
						
			// /print_r($atts);
			// connect to support arguments
			$supported_defaults = $eventon->evo_generator->get_supported_shortcode_atts();
			//print_r($supported_defaults);
			
			$args = shortcode_atts( $supported_defaults, $atts ) ;			
			
			ob_start();				
				echo $eventon_sl->frontend->get_slider_content($args);			
			return ob_get_clean();
					
		}

	// add new default shortcode arguments
		function add_shortcode_defaults($arr){			
			return array_merge($arr, array(
				//'mobreaks'=>'no',
				'el_type'=>'ue',
				'slider_type'=>'def',
				'slide_auto'=>'no',
				'slide_pause_hover'=>'no',
				'slide_hide_control'=>'no',
				'slide_loop'=>'no',
				'slider_pause'=>'2000',
				'slider_speed'=>'400',
				'ux_val'=>3
			));			
		}

	/*	ADD shortcode buttons to eventON shortcode popup	*/
		function add_shortcode_options($shortcode_array){
			global $evo_shortcode_box;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_sl',
					'name'=>'Event Slider',
					'code'=>'add_eventon_slider',
					'variables'=>array(
						$evo_shortcode_box->shortcode_default_field('cal_id')
						,array(
							'name'=>'Select Slide Type',
							'type'=>'select',
							'var'=>'slider_type',
							'options'=>array(
								'def'=>'Default Slider',
								'multi'=>'Multiple Events Slider',
								'multimini'=>'Multiple Mini Events Slider',
								'vertical'=>'Vertical Slider',
								'minicar'=>'Mini Carousel',
							)
						),array(
							'name'=>'Auto Start and Slide',
							'type'=>'YN',
							'guide'=>'This will make slider run automatically on load',
							'var'=>'slide_auto',
							'default'=>'no',
							'afterstatement'=>'slider_pause'
						),array(
								'name'=>'The Time (in ms) Between each Auto Transition',
								'type'=>'select',
								'options'=>array(
									'2000'=>'2000',
									'4000'=>'4000',
									'6000'=>'6000',
									'8000'=>'8000',
									'10000'=>'10000',
									),
								'guide'=>'Miliseconds between each auto slide pause',
								'var'=>'slider_pause','default'=>'2000',
								
							),
							array(
								'name'=>'Pause Autoplay on Hover',
								'type'=>'YN',
								'guide'=>'This will pause the auto slider when hover',
								'var'=>'slide_pause_hover',
								'default'=>'no',
								'closestatement'=>'slider_pause'
							),
						array(
							'name'=>'Hide Event Slide Controls',
							'type'=>'YN',
							'guide'=>'This will hide prev/next buttons on the slider',
							'var'=>'slide_hide_control',
							'default'=>'no',
						),
						array(
							'name'=>'Loop Event Slides',
							'type'=>'YN',
							'guide'=>'This will loop slides back to beginning of slide when on the last event slide',
							'var'=>'slide_loop',
							'default'=>'no',
						),
						array(
							'name'=>'Transition Duration (in ms)',
							'type'=>'select',
							'options'=>array(
								'2000'=>'200',
								'4000'=>'400',
								'6000'=>'600',
								'8000'=>'800',
								'10000'=>'1000',
								),
							'guide'=>'How many miliseconds it will take for transition between each event slide',
							'var'=>'slider_speed','default'=>'400'
						)
						

						,array(
							'name'=>'Select Event List Type',
							'type'=>'select',
							'guide'=>'Type of event list you want to show.',
							'var'=>'el_type',
							'options'=>array(
								'ue'=>'Default (Upcoming Events)',
								'pe'=>'Past Events'
							)
						),array(
							'name'=>'Event Cut-off',
							'type'=>'select_step',
							'guide'=>'Past or upcoming events cut-off time. This will allow you to override past event cut-off settings for calendar events. Current date = today at 12:00am',
							'var'=>'pec',
							'default'=>'Current Time',
							'options'=>array( 
								'ct'=>'Current Time: '.date('m/j/Y g:i a', current_time('timestamp')),
								'cd'=>'Current Date: '.date('m/j/Y', current_time('timestamp')),
								'ft'=>'Fixed Time'
							)
						)						
							,array(
								'type'=>'open_select_steps','id'=>'ct'
							)
							,array(	'type'=>'close_select_step')
							,array(
								'type'=>'open_select_steps','id'=>'cd'
							)
							,array(	'type'=>'close_select_step')
							,array(
								'type'=>'open_select_steps','id'=>'ft'
							)
								,$evo_shortcode_box->shortcode_default_field('fixed_d_m_y')
								
							,array(	'type'=>'close_select_step')
						,$this->event_opening_array(),
						array(
							'name'=>'Number of Months',
							'type'=>'text',
							'var'=>'number_of_months',
							'default'=>'0',
							'guide'=>'If number of month is not provided, by default it will get events from one month either back or forward of current month',
							'placeholder'=>'eg. 5'
						),
						array(
							'name'=>'Event Count Limit',
							'placeholder'=>'eg. 3',
							'type'=>'text',
							'guide'=>'Limit number of events displayed in the list eg. 3',
							'var'=>'event_count',
							'default'=>'0'
						),						
						$evo_shortcode_box->shortcode_default_field('event_order'),
						$evo_shortcode_box->shortcode_default_field('hide_mult_occur'),
						array(
							'name'=>'Show All Repeating Events While HMO',
							'type'=>'YN',
							'guide'=>'If you are hiding multiple occurence of event but want to show all repeating events set this to yes',
							'var'=>'show_repeats',
							'default'=>'no',
						),
						$evo_shortcode_box->shortcode_default_field('event_type'),
						$evo_shortcode_box->shortcode_default_field('event_type_2'),
						$evo_shortcode_box->shortcode_default_field('etc_override'),
						$evo_shortcode_box->shortcode_default_field('only_ft'),
						
					)
				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
		}

		function event_opening_array(){

			//if( is_plugin_active('eventon-single-events/eventon-single-event.php')){
			return array(
				'name'=>'Open events as',
				'type'=>'select',
				'var'=>'ux_val',
				'options'=>apply_filters('eventon_uix_shortcode_opts',array(
					'3'=>__('Lightbox popup window','eventon'),
					'2'=>__('Single Events Page','eventon'),
					'X'=>__('Do not interact','eventon'),					
				))
			);			
		}
}
?>