<?php
/**
 * Table Model
 *
 * @package TablePress
 * @subpackage Models
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Load TablePress class, which holds common functions and variables.
 */
require_once TABLEPRESS_ABSPATH . './models/model-table.php';

/**
 * Table Model class
 * @package TablePress
 * @subpackage Models
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class Pab_TablePress_Table_Model extends TablePress_Table_Model {
	/**
	 * Convert a post (from the database) to a table.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post      Post.
	 * @param string  $table_id  Table ID.
	 * @param bool    $with_data Whether the table data shall be loaded.
	 * @return array Table.
	 */
	protected function _post_to_table( $post, $table_id, $load_data ) {
		$table = array(
			'id' => $table_id,
			'name' => $post->post_title,
			'description' => $post->post_excerpt,
			'author' => $post->post_author,
			// 'created' => $post->post_date,
			'last_modified' => $post->post_modified,
		);

		if ( ! $load_data ) {
			return $table;
		}
		switch ($table_id) {
			case 2 :
				$data = $this->_getDatasForEquipe ();
				break;
			case 3 :
				$data = $this->_getDatasForDesignations ();
				break;
			case 4 :
				$data = $this->_getDatasForEntraineurs ();
				break;
		}
		
		$table['data'] = json_decode($data , true );

		// Check if JSON could be decoded.
		if ( is_null( $table['data'] ) ) {
			// Set a single cell as the default.
			$table['data'] = array( array( "The internal data of table {$table_id} is corrupted." ) );
			// Mark table as corrupted.
			$table['is_corrupted'] = true;

			// If possible, try to find out what error prevented the JSON from being decoded.
			$table['json_error'] = 'The error could not be determined.';
			// @TODO: The `function_exists` check can be removed once support for WP 4.3 is dropped, as a compat function was added in WP 4.4.
			if ( function_exists( 'json_last_error_msg' ) ) {
				$json_error_msg = json_last_error_msg();
				if ( false !== $json_error_msg ) {
					$table['json_error'] = $json_error_msg;
				}
			}

			$table['description'] = "[ERROR] TABLE IS CORRUPTED (JSON error: {$table['json_error']})!  DO NOT EDIT THIS TABLE NOW!\nInstead, please see https://tablepress.org/faq/corrupted-tables/ for instructions.\n-\n{$table['description']}";
		} else {
			// Specifically cast to an array again.
			$table['data'] = (array) $table['data'];
		}

		return $table;
	}

	
	/**
	 * 
	 */
	protected function _getDatasForEquipe(){
		$post_objects = get_field('joueurs');
		$data .= "[";
		$data .= "[\"\", \"Nom Prenom\", \"Numero de licence\", \"Taille\", \"Anniversaire\"]";

		if( $post_objects ){
   			foreach( $post_objects as $post_object){
				
   				$dateAnniv = new DateTime(get_field('date_anniversaire', $post_object->ID));
   				$data .= "[\"";
				$data .= "<img src='".get_the_post_thumbnail_url($post_object->ID, array(10,10))."' width='25' height='25'/>";			
   				$data.="\",\"".get_the_title($post_object->ID);
				$data.="\",\"".get_field('numerolicence', $post_object->ID);
				$data.="\",\"".get_field("taille", $post_object->ID);
				if (get_field('date_anniversaire', $post_object->ID)) {
					$data.="\",\"".date_i18n('d/m/Y', strtotime(get_field('date_anniversaire', $post_object->ID)));
				} else {
					$data.="\",\"".'';
				}
				$data .= "\"]";      
    		}
		}
		$data = str_replace("][","],[",$data)."]";
		return $data;
	}

	/**
	 * 
	 */
	protected function _getDatasForDesignations(){
		$args = array( 'numberposts' => -1, 'order'=> 'ASC', 'orderby' => 'title', 'post_type' => 'wm_staff');
		$postslist = get_posts( $args );
		
		
		$data .= "[";
		//$data .= "[\"Nom Prenom\", \"Numero de licence\", \"Taille\", \"Anniversaire\"]";
		$data .= "[\"Nom Prenom\", \"Departement\"]";
		
		if( $postslist ){
			foreach( $postslist as $post_object){
				setup_postdata( $post_object );
				$terms = get_the_terms($post_object->ID, "staff_department");
				$departments = "";
				if ($terms) {
					foreach ( $terms as $term) {
						$departments .= $term->name .", ";
					}
				} else if ($terms instanceof WP_Error){
					$terms = $terms->get_error_code();
				}
				$data .= "[\"";
				$data.="<a href='".$post_object->ID."'>".get_the_title($post_object->ID)."</a>";
				$data.="\",\"".$departments ;
				//$data.="\",\"".get_field("taille", $post_object->ID);
				//$data.="\",\"".get_field('anniversaire', $post_object->ID);
				$data .= "\"]";
			}
			wp_reset_postdata();
		}
		$data = str_replace("][","],[",$data)."]";
		return $data;
	}
	
	/**
	 *
	 */
	protected function _getDatasForEntraineurs(){
		$args = array( 'numberposts' => -1, 'order'=> 'ASC', 'orderby' => 'title', 'post_type' => 'wm_staff');
		$postslist = get_posts( $args );
		
		
		$data .= "[";
		//$data .= "[\"Nom Prenom\", \"Numero de licence\", \"Taille\", \"Anniversaire\"]";
		$data .= "[\"Nom Prenom\", \"Departement\"]";
		if( $postslist ){
			foreach( $postslist as $post_object){
				setup_postdata( $post_object );
				$terms = get_the_terms($post_object->ID, "staff_department");
				$departments = "";
				if ($terms) {
					foreach ( $terms as $term) {
						$departments .= $term->name .", ";
					}
				} else if ($terms instanceof WP_Error){
					$terms = $terms->get_error_code();
				}

				if (!strpos($departments, "sportive")===false) {
					$data .= "[\"";
					$data.="<a href='#'>".get_the_title($post_object->ID)."</a>";
					$data.="\",\"".$departments;
					
					//$data.="\",\"".get_field("taille", $post_object->ID);
					//$data.="\",\"".get_field('anniversaire', $post_object->ID);
					$data .= "\"]";
				}
			}
			wp_reset_postdata();
		}
		$data = str_replace("][","],[",$data)."]";
		return $data;
	}
	
}