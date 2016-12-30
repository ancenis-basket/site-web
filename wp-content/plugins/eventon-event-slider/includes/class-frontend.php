<?php
/**
 * Event Slider front end class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-slider/classes
 * @version     0.1
 */
class evosl_front{
	
	function __construct(){
		global $eventon_sl;

		$this->evopt1 = get_option('evcal_options_evcal_1');

		include_once('class-functions.php');
		$this->functions = new evosl_functions();

		// scripts and styles 
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	

		$this->opt = $eventon_sl->opt;
		$this->opt2 = $eventon_sl->opt2;
	}

	// STYLES: for photos 
		public function register_styles_scripts(){
			global $eventon_sl;			
			wp_register_style( 'evosl_styles',$eventon_sl->assets_path.'css/evosl_styles.css');
			
			wp_register_script('mainscript',$eventon_sl->assets_path.'js/evoslider.js', array('jquery'), $eventon_sl->version, true );
			wp_register_script('sl_script',$eventon_sl->assets_path.'js/SL_script.js', array('jquery'), $eventon_sl->version, true );
			
			$this->print_scripts();
			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));				
		}
		public function print_scripts(){
			wp_enqueue_script('mainscript');		
			wp_enqueue_script('sl_script');		
		}
		function print_styles(){	wp_enqueue_style( 'evosl_styles');	}

	// Generate Slider HTML content
		function get_slider_content($args){
			global $eventon;

			$args['show_et_ft_img']='yes';
			//$args['ux_val']='3';
			
			$this->only__actions();
			$content = '';
				
			// CUT OFF time calculation
				//fixed time list
				if(!empty($args['pec']) && $args['pec']=='ft'){
					$__D = (!empty($args['fixed_date']))? $args['fixed_date']:date("j", current_time('timestamp'));
					$__M = (!empty($args['fixed_month']))? $args['fixed_month']:date("m", current_time('timestamp'));
					$__Y = (!empty($args['fixed_year']))? $args['fixed_year']:date("Y", current_time('timestamp'));

					$current_timestamp = mktime(0,0,0,$__M,$__D,$__Y);

				// current date cd
				}else if(!empty($args['pec']) && $args['pec']=='cd'){
					$current_timestamp = strtotime( date("m/j/Y", current_time('timestamp')) );
				}else{// current time ct
					$current_timestamp = current_time('timestamp');
				}
				// reset arguments
				$args['fixed_date']= $args['fixed_month']= $args['fixed_year']='';
			
			// restrained time unix
				$number_of_months = (!empty($args['number_of_months']))? (int)($args['number_of_months']):0;
				$month_dif = ($args['el_type']=='ue')? '+':'-';
				$unix_dif = strtotime($month_dif.($number_of_months-1).' months', $current_timestamp);

				$restrain_monthN = ($number_of_months>0)?				
					date('n',  $unix_dif):
					date('n',$current_timestamp);

				$restrain_year = ($number_of_months>0)?				
					date('Y', $unix_dif):
					date('Y',$current_timestamp);			

			// upcoming events list 
				if($args['el_type']=='ue'){
					$restrain_day = date('t', mktime(0, 0, 0, $restrain_monthN+1, 0, $restrain_year));
					$__focus_start_date_range = $current_timestamp;
					$__focus_end_date_range =  mktime(23,59,59,($restrain_monthN),$restrain_day, ($restrain_year));
								
				}else{// past events list

					if(!empty($args['event_order']))
						$args['event_order']='DESC';

					$args['hide_past']='no';
					
					$__focus_start_date_range =  mktime(0,0,0,($restrain_monthN),1, ($restrain_year));
					$__focus_end_date_range = $current_timestamp;
				}
			
			
			// Add extra arguments to shortcode arguments
			$new_arguments = array(
				'focus_start_date_range'=>$__focus_start_date_range,
				'focus_end_date_range'=>$__focus_end_date_range,
			);

			// Alter user interaction
				if($args['ux_val']== '1' || $args['ux_val'] == '2' || empty($args['ux_val'])) $args['ux_val'] = 3;		

			//print_r($args);
			$args = (!empty($args) && is_array($args))? 
				wp_parse_args($new_arguments, $args): $new_arguments;
			
			
			// PROCESS variables
			$args__ = $eventon->evo_generator->process_arguments($args, $__focus_start_date_range, $__focus_end_date_range);
			$this->shortcode_args=$args__;
			
			
			// Content for the slider
			$content .= $this->html_header($args);

			$content .=$eventon->evo_generator->eventon_generate_events($args__);

			$content .= $this->html_footer();

			$this->remove_only__actions();
			
			return  $content;	
		}

		// Header content for the slider
			function html_header($args){
				$dataString = '';

				// data attributes slide_auto
					foreach($args as $field=>$value){
						if(!in_array($field, array(
							'slider_speed','slide_auto', 'slide_pause_hover','slide_hide_control',
							'slide_loop','slider_pause','slider_type'
						)) ) continue;

						$dataString .= 'data-'. $field .'="'. $value .'" ';
					}

				// class names for slider container
				$classNames = '';
				if(!empty($this->evopt1['evo_rtl']) && $this->evopt1['evo_rtl']=='yes')	$classNames[] = 'rtlslider';
				if(!empty($args['slider_type']) ) $classNames[] = $args['slider_type'].'Slider';
				array_filter($classNames);
				if(is_array($classNames)) $class_names = implode(' ', $classNames);

				$out = '';
				$out .= '<div class="evosliderbox ajde_evcal_calendar '.$class_names.'">';
				$out .= '<div class="evo_slider_width clearfix" '.$dataString.'>
	                <ul class="gallery list-unstyled evo-hidden evosl_slider eventon_events_list ">';
	            return $out;
			}
			function html_footer(){
				$out = '';
				$out .= '</ul>';
				$out .= '<div class="evosl_footer_outter"><div class="evosl_footer">	</div></div>';
				$out .= '</div>';
				$out .= '</div>';
				return $out;
			}


	// SUPPORT functions
		// ONLY for el calendar actions 
		public function only__actions(){
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);	
		}
		public function remove_only__actions(){
			//add_filter('eventon_cal_class', array($this, 'remove_eventon_cal_class'), 10, 1);
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));				
		}
		// add class name to calendar header for DV
		function eventon_cal_class($name){
			$name[]='evoSL';
			return $name;
		}
		// add class name to calendar header for DV
		function remove_eventon_cal_class($name){
			if(($key = array_search('evoSL', $name)) !== false) {
			    unset($name[$key]);
			}
			return $name;
		}
		// RETURN: language
			function lang($variable, $default_text){
				global $eventon_sl;
				return $eventon_sl->lang($variable, $default_text);
			}
		// function replace event name from string
			function replace_en($string){
				return str_replace('[event-name]', "<span class='eventName'>Event Name</span>", $string);
			}		
		
	    
}
