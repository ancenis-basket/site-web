<?php
/**
 * For PAB !
 */
add_action ( 'init', 'my_custom_init' );
function my_custom_init() {
	register_post_type ( 'equipe', array (
			'label' => 'Equipes',
			'labels' => array (
				'name' => 'Equipes',
				'singular_name' => 'Equipe',
				'all_items' => 'Toutes les équipes',
				'add_new_item' => 'Ajouter une équipe',
				'edit_item' => 'Éditer l\' équipe',
				'new_item' => 'Nouvelle équipe',
				'view_item' => 'Voir l équipe',
				'search_items' => 'Rechercher parmi les équipes',
				'not_found' => 'Pas d équipe trouvée',
				'not_found_in_trash' => 'Pas d équipe dans la corbeille'
				),
			'public' => true,
			'capability_type' => 'post',
			'rewrite' => array( 'slug' => 'equipe','with_front' => FALSE),
			'menu_position' => '10',
			'supports' => array (
					'title',
					'editor',
					'thumbnail'
					),
			'has_archive' => true
	) );
}

register_taxonomy(
  'catégorie',
  'equipe',
  array(
    'label' => 'Catégories',
    'labels' => array(
    'name' => 'Catégories',
    'singular_name' => 'Catégorie',
    'all_items' => 'Toutes les catégories',
    'edit_item' => 'Éditer la catégorie',
    'view_item' => 'Voir la catégorie',
    'update_item' => 'Mettre à jour la catégorie',
    'add_new_item' => 'Ajouter une catégorie',
    'new_item_name' => 'Nouvelle catégorie',
    'search_items' => 'Rechercher parmi les catégories',
    'popular_items' => 'Catégories les plus utilisées'
  ),
  'hierarchical' => true
  )
);

register_taxonomy(
  'type de catégorie',
  'equipe',
  array(
    'label' => 'Types de catégories',
    'labels' => array(
    'name' => 'Types de catégories',
    'singular_name' => 'type de catégorie',
    'all_items' => 'Toutes les types de catégories',
    'edit_item' => 'Éditer le type de catégorie',
    'view_item' => 'Voir le type de catégorie',
    'update_item' => 'Mettre à jour le type de catégorie',
    'add_new_item' => 'Ajouter un type de catégorie',
    'new_item_name' => 'Nouveau type de catégorie',
    'search_items' => 'Rechercher parmi les type de catégories',
    'popular_items' => 'Types de catégories les plus utilisées'
  ),
  'hierarchical' => true
  )
);
register_taxonomy(
  'championnat',
  'equipe',
  array(
    'label' => 'Championnats',
    'labels' => array(
    'name' => 'Championnats',
    'singular_name' => 'Championnat',
    'all_items' => 'Toutes les championnats',
    'edit_item' => 'Éditer le championnat',
    'view_item' => 'Voir le championnat',
    'update_item' => 'Mettre à jour le championnat',
    'add_new_item' => 'Ajouter un championnat',
    'new_item_name' => 'Nouveau championnat',
    'search_items' => 'Rechercher parmi les championnat',
    'popular_items' => 'Championnat les plus utilisés'
  ),
  'hierarchical' => true
  )
);
register_taxonomy ( 'poules', 'equipe', array (
		'label' => 'Poules',
		'labels' => array (
				'name' => 'Poules',
				'singular_name' => 'poule',
				'all_items' => 'Toutes les poules',
				'edit_item' => 'Éditer la poule',
				'view_item' => 'Voir la poule',
				'update_item' => 'Mettre à jour la poule',
				'add_new_item' => 'Ajouter une poule',
				'new_item_name' => 'Nouvelle poule',
				'search_items' => 'Rechercher parmi les poules',
				'popular_items' => 'poules les plus utilisées' 
		),
		'hierarchical' => true 
) );
register_taxonomy ( 'saisons', 'equipe', array (
		'label' => 'Saisons',
		'labels' => array (
				'name' => 'Saisons',
				'singular_name' => 'saison',
				'all_items' => 'Toutes les saisons',
				'edit_item' => 'Éditer la saison',
				'view_item' => 'Voir la saison',
				'update_item' => 'Mettre à jour la saison',
				'add_new_item' => 'Ajouter une saison',
				'new_item_name' => 'Nouvelle saison',
				'search_items' => 'Rechercher parmi les saisons',
				'popular_items' => 'saisons les plus utilisées'
		),
		'hierarchical' => true
) );
register_taxonomy_for_object_type( 'catégorie', 'equipe' );
register_taxonomy_for_object_type( 'type de catégorie', 'equipe' );
register_taxonomy_for_object_type( 'championnat', 'equipe' );
register_taxonomy_for_object_type( 'poule', 'equipe' );
register_taxonomy_for_object_type( 'saison', 'equipe' );

define( 'PAB_ABSPATH', plugin_dir_path( __FILE__ ) );
add_filter( 'tablepress_load_class_name', 'pab_load_class_name');
function pab_load_class_name( $classname ) {
	if (strcmp($classname, "TablePress_Table_Model")==0){
		require_once PAB_ABSPATH . '/' . "Pab_TablePress_Table_Model.php";
		return "Pab_TablePress_Table_Model";
	} else {
		return $classname ;
	}
}
//==========================================
//Pour ajouter des infos sur les événements du calendrier
// hook into event top correct place
add_filter('eventon_eventtop_one', 'eventon_insert', 10, 3);
function eventon_insert($array, $evvals, $passval){
	$array['custom'] = array('eventid'=>$passval['eventid'], 'evvals'=>$evvals);
	return $array;
}

// include in index as part of event top array
add_filter('evo_eventtop_adds', 'eventon_top_adds', 10, 1);
function eventon_top_adds($array){
	$array[] = 'custom';
	return $array;
}

// throw html content for the switch statement for this index
add_filter('eventon_eventtop_custom', 'eventon_top_content', 10, 2);
function eventon_top_content($object, $helpers){
        
        $OT.="<br/>";
	$event_id = $object->eventid;

	// $event_pmv = $object->evvals; // event post meta values
	//======pab addition for arbitres table et bar=======
	//arbitres
	$org = (!empty($object->evvals['arbitres']))? $object->evvals['arbitres'][0]:'';
	if( !empty($org)){
		$OT.='<span style="margin-top:1px"><span data-eventid="'.$event_id.'" class="custom_code">';
		$OT.="<i>"."Arbitres : </i> ";
		$datas = unserialize($org);
		foreach($datas as $arb){
			$OT.=get_the_title($arb).", ";
		}
		$OT.="</span></span>";
	}
	
	//table
	$org = (!empty($object->evvals['table']))? $object->evvals['table'][0]:'';
	if(!empty($org)){
		$OT.='<span style="margin-top:1px"><span data-eventid="'.$event_id.'" class="custom_code">';
		$OT.="<i>"."Table de marque : </i> ";
		$datas = unserialize($org);
		foreach($datas as $otm){
			$OT.=get_the_title($otm).", ";
		}
		$OT.="</span></span>";
	}
	
	//bar
	$org = (!empty($object->evvals['bar']))? $object->evvals['bar'][0]:'';
	if(!empty($org)){
		$OT.='<span style="margin-top:1px"><span data-eventid="'.$event_id.'" class="custom_code">';
		$OT.="<i>"."Bar : </i> ";
		$datas = unserialize($org);
		foreach($datas as $bar){
			$OT.=get_the_title($bar).", ";
		}
		$OT.="</span></span>";
	}
	
	
	//responsable
	$org = (!empty($object->evvals['responsable']))? $object->evvals['responsable'][0]:'';
	if(!empty($org)){
		$OT.='<span style="margin-top:1px"><span data-eventid="'.$event_id.'" class="custom_code">';
		$OT.="<i>"."Responsable : </i> ";
		$datas = unserialize($org);
		foreach($datas as $bar){
			$OT.=get_the_title($bar).", ";
		}
		$OT.="</span></span>";
	}
	$output = $OT;
	return $output;
}
	
	// styles for the new addition
add_action ( 'wp_head', 'eventon_additional_styles' );
function eventon_additional_styles() {
	echo "<style type='text/css'>
				body .eventon_list_event .evcal_desc .custom_code{
					background-color:rgba(186, 191, 201, 0.52);
					padding:1px 5px;
					border-radius:5px;
					display:inline-block;
					font-size:12px;
					text-transform:lowercase;
					color:#F00011;
				}
		</style>";
}

//===========================================================
//designation ajax !
//add_filter('eventon_wp_query_args', 'eventon_designation_discriminer', 10, 3);
function eventon_designation_discriminer($wp_arguments, $filters, $ecv){
	$wp_arguments['meta_query'] = array(
			array(
					'key' => 'responsable',
					'value' => '941',
					'compare' => 'LIKE'
			));
	return $wp_arguments;
}
function add_js_scripts() {
	wp_enqueue_script( 'script', get_template_directory_uri().'/js/script.js', array('jquery'), '1.0', true );

	// pass Ajax Url to script.js
	wp_localize_script('script', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
}
add_action('wp_enqueue_scripts', 'add_js_scripts');



add_action( 'wp_ajax_mon_action', 'mon_action' );
add_action( 'wp_ajax_nopriv_mon_action', 'mon_action' );

function mon_action() {

$args = array(
	    'post_type' => 'post',
	    'posts_per_page' => 10
	);

	$ajax_query = new WP_Query($args);

	var_dump($ajax_query);

	die();//fonction de sécurité très importante!
}

?>
