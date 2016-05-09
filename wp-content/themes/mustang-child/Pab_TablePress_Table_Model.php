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
// Define certain plugin variables as constants.
//define( 'TABLEPRESS_ABSPATH', plugin_dir_path( __FILE__ ) );
//define( 'TABLEPRESS__FILE__', __FILE__ );
//define( 'TABLEPRESS_BASENAME', plugin_basename( TABLEPRESS__FILE__ ) );

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

		$data = $this->_getDatas();
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

	
	protected function _getDatas(){
		$post_objects = get_field('joueurs');
		$data .= "[";
		$data .= "[\"Nom Prenom\", \"Numero de licence\", \"Taille\", \"Anniversaire\"]";

		if( $post_objects ){
   			foreach( $post_objects as $post_object){
				
				$data .= "[\"";
				$data.=get_the_title($post_object->ID);
				$data.="\",\"".the_field('numerolicence', $post_object->ID);
				$data.="\",\"".the_field("taille", $post_object->ID);
				$data.="\",\"".the_field('anniversaire', $post_object->ID);
				$data .= "\"]";      
    			}
		}
		$data = str_replace("][","],[",$data)."]";
		return $data;
	}
}