<?php
/**
 * AJAX for EventON DV
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	dailyview/Functions/AJAX
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVODV_ajax{
	public function __construct(){
		$ajax_events = array(
			'the_ajax_daily_view'=>'evoDV_ajax_days_list',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
		add_filter('eventon_ajax_arguments',array($this,'evoDV_ajax_filter'), 10, 2);
	}
	// days list
		function evoDV_ajax_days_list(){
			global $eventon_dv;
			
			$filters = ((isset($_POST['filters']))? $_POST['filters']:null);
				
			$days_content = $eventon_dv->frontend->get_daily_view_list(
				$_POST['next_d'],
				$_POST['next_m'], 
				$_POST['next_y'], 
				$filters,
				$_POST['shortcode']
			);
					
			$return_content = array(
				'days_list'=> $days_content,
				'last_date_of_month'=>$eventon_dv->frontend->days_in_month($_POST['next_m'], $_POST['next_y']),
				'status'=>'ok'
			);
			
			echo json_encode($return_content);		
			exit;
		}

	// filter events
		function evoDV_ajax_filter($eve_args){
			global $eventon_dv;
			
			if(isset($_POST['dv_focus_day'])){
				$focused_month_num = date('n', $eve_args['focus_start_date_range']);
				$focused_year = date('Y', $eve_args['focus_start_date_range'] );
				$new_day = $_POST['dv_focus_day'];

				$number_days_in_month = $eventon_dv->frontend->days_in_month( $focused_month_num, $focused_year);
				
				$new_day = ($new_day<$number_days_in_month)? $new_day: $number_days_in_month;

				$focus_start_date_range = mktime( 0,0,0,$focused_month_num,$new_day,$focused_year );
				$focus_end_date_range = mktime(23,59,59,($focused_month_num),$new_day, ($focused_year));
				
				
				
				$eve_args['focus_start_date_range']=$focus_start_date_range;
				$eve_args['focus_end_date_range']=$focus_end_date_range;
				
			}
			return $eve_args;
		}
}
new EVODV_ajax();
?>