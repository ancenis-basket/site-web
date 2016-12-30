<?php
/**
 * Admin settings class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-slider/classes
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evosl_admin{
	
	public $optSL;
	function __construct(){
		add_action('admin_init', array($this, 'admin_init'));
		add_action( 'admin_menu', array( $this, 'menu' ),9);
	}

	// INITIATE
		function admin_init(){

			// eventCard inclusion
			//add_filter( 'eventon_eventcard_boxes',array($this,'evoSL_add_toeventcard_order') , 10, 1);

			// language
			//add_filter('eventon_settings_lang_tab_content', array($this, 'evoSL_language_additions'), 10, 1);

			global $pagenow, $typenow, $wpdb, $post;	
			
			if ( $typenow == 'post' && $post && ! empty( $_GET['post'] ) ) {
				$typenow = $post->post_type;
			} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
		        $typenow = get_post_type( $_GET['post'] );
		    }
			
			// settings
			add_filter('eventon_settings_tabs',array($this, 'evoSL_tab_array' ),10, 1);
			add_action('eventon_settings_tabs_evcal_sl',array($this, 'evoSL_tab_content' ));		
		}

	// other hooks
		
		function evoSL_add_toeventcard_order($array){
			$array['evoSL']= array('evoSL',__('Event Photos','eventon'));
			return $array;
		}
		
		// EventON settings menu inclusion
		function menu(){
			add_submenu_page( 'eventon', 'Slider', __('Slider','eventon'), 'manage_eventon', 'admin.php?page=eventon&tab=evcal_sl', '' );
		}
	
	// TABS SETTINGS
		function evoSL_tab_array($evcal_tabs){
			$evcal_tabs['evcal_sl']='Slider';		
			return $evcal_tabs;
		}
		function evoSL_tab_content(){
			global $eventon;
			$eventon->load_ajde_backender();			
		?>
			<form method="post" action=""><?php settings_fields('evoSL_field_group'); 
					wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );?>
			<div id="evcal_re" class="evcal_admin_meta">	
				<div class="evo_inside">
				<?php
					$cutomization_pg_array = array(
						array(
							'id'=>'evoSL1','display'=>'show',
							'name'=>'General Slider Settings',
							'tab_name'=>'General',
							'fields'=>array(
								array('id'=>'evoSL_skin','type'=>'dropdown','name'=>'Select lightbox theme','options'=>array('default'=>'Dark','light'=>'Light')),
								array('id'=>'evoSL_thumb','type'=>'dropdown','name'=>'EventCard thumbnail size (px)','options'=>array('def'=>'100x100','150'=>'150x150','75'=>'75x75','50'=>'50x50')),	

						)),array(
							'id'=>'evoSLt',
							'name'=>'Basic Troubleshooting',
							'tab_name'=>'Troubleshoot','icon'=>'anchor',
							'fields'=>array(
								array('id'=>'evoSL_troublshooter','type'=>'customcode','code'=>$this->troubleshooter_code()),	
						))
					);							
					$eventon->load_ajde_backender();	
					$evcal_opt = get_option('evcal_options_evcal_sl'); 
					print_ajde_customization_form($cutomization_pg_array, $evcal_opt);	
				?>
			</div>
			</div>
			<div class='evo_diag'>
				<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /><br/><br/>
				<a target='_blank' href='http://www.myeventon.com/support/'><img src='<?php echo AJDE_EVCAL_URL;?>/assets/images/myeventon_resources.png'/></a>
			</div>			
			</form>	
		<?php
		}

	function troubleshooter_code(){		
		$output = '<p><b><i>Photos does not show in eventcard?</i></b> <br/>Go to myEventON Settings > EventCard > Re-arrange the order of eventCard event data boxes -- make sure Event photos row is checked. And move this up or down and click "save changes"</p>';

		$output .= '<br/><p><b><i>How to add captions for images?</i></b> <br/>When you choose an image from Event Photos box in event edit page, in the light box for choose an image, make sure to fill in the <b>Caption</b> section for selected image. The caption for the image you enter is what is shown in frontend eventCard photos lightbox.</p>';

		return $output;
	}
}

new evoSL_admin();