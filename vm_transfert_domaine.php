<?
//-------------------------------------------------------------------------
// vm_transfert_domaine.php
//-------------------------------------------------------------------------
// Date: 2013-09-10
// Version: 0.1
// Auteur: Francis Parent-Valiquette - info@francisparent.com
// Site web: http://www.francisparent.com
//-------------------------------------------------------------------------
// Script permettant de remplacer un nom de domaine (ou toutes autres 
// chaîne de charater) "Search and replace" dans tous les colonnes de la 
// base de donnée choisie.
//
// Peux être utilisé en autre pour mettre à jour la base de donnée 
// wordpress après un transfert ou un déménagement de domaine
//-------------------------------------------------------------------------

//-------------------------------------------------------------------------
// Entrez vos informations de site ici
//-------------------------------------------------------------------------

//Information de chaîne à remplacer
$RECHERCHE = 'localhost/wordpress'; //Le nom de domaine ou chaîne recherché
$REMPLACE = '33ruedesoies.fr/pab44'; //Le nom de domaine ou chaîne qui va remplacer celui recherché

//Ne devrait pas être modifiéer
$NOM_SCHEMA_BD = 'pabwp'; //Nom de la BD information_schema

//Inclure le fichier wp-config 
require_once('wp-config.php');

//OU entrer les informations de db manuelement
/*define('DB_NAME', ''); //Nom de la BD
define('DB_USER', ''); //nom utilisateur BD
define('DB_PASSWORD', ''); //Mot de passe BD
define('DB_HOST', 'localhost'); //Le host name BD*/

//-------------------------------------------------------------------------
// Ne pas modifier en bas de cette ligne (sauf si nécessaire)
//-------------------------------------------------------------------------
$tables = array();

//Connexion simple à la BD schema
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, $NOM_SCHEMA_BD);

if($db->connect_errno > 0)
    die('Impossible de se connecter à la base de donn&eacute;e ['. $db->connect_error .']');
	
//Étape 1 - Faire la liste des tables dans la DB WP
$sql = "SELECT `TABLE_NAME` FROM `TABLES` WHERE `TABLE_TYPE`='BASE TABLE';";
if(!$result = $db->query($sql))
    die('Une erreur est survenu avec la requête [' . $db->error . ']');

while($row = $result->fetch_assoc())
    $tables[$row['TABLE_NAME']] = array();

$result->free();

//Étape 2 - Faire la liste des colonnes pour chacune des tables dans la DB WP
foreach($tables as $k=>$v){
	$colonnes = array();
	
	$sql = "SELECT `COLUMN_NAME` FROM `COLUMNS` WHERE `DATA_TYPE`!='int' AND `DATA_TYPE`!='bigint' AND `DATA_TYPE`!='datetime' AND `DATA_TYPE`!='decimal' AND `TABLE_NAME`='". $k ."';";
	if(!$result = $db->query($sql))
		die('Une erreur est survenu avec la requête [' . $db->error . ']');
	
	while($row = $result->fetch_assoc())
		$colonnes[] = $row['COLUMN_NAME'];
	
	$tables[$k] = $colonnes;
	
	$result->free();
}

$db->close();

//Étape 3 - Faire le search and replace dans la BD WP

//Connexion simple à la BD WP
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0)
    die('Impossible de se connecter à la base de donn&eacute;e ['. $db->connect_error .']');

foreach($tables as $k=>$v){
	
	foreach($v as $key => $value){
		$sql = "UPDATE `". $k ."` SET `". $value ."` = replace(`".$value."`, '". $RECHERCHE ."', '". $REMPLACE ."');";
		
		if(!$result = $db->query($sql))
			die('Une erreur est survenu avec la requête [' . $db->error . ']');
	}

}
$db->close();

echo '<h3>Op&eacute;ration r&eacute;ussie!</h3><br>Pour des question de s&eacute;curit&eacute; veuillez supprimer ce fichier de votre serveur web!';

?>