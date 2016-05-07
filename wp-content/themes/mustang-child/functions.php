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
register_taxonomy_for_object_type( 'catégorie', 'equipe' );
register_taxonomy_for_object_type( 'type de catégorie', 'equipe' );
register_taxonomy_for_object_type( 'championnat', 'equipe' );
?>
