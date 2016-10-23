<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur 
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'pabwp');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'pab');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'pab');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données. 
  * N'y touchez que si vous savez ce que vous faites. 
  */
define('DB_COLLATE', '');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant 
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '8a)I(t_[QU=) -w.w~e8%G@F ]u1U12W.|gA.6czd(_y5&y3)*>qJ&R(-xeW]7gi');
define('SECURE_AUTH_KEY',  'mTO-#nH[M(E74;DW&|FKqmmM/KYI-~1|Y3$X4AGBL4}y26-h#,ohyf}wkwP<O;1>');
define('LOGGED_IN_KEY',    'B^%l2]>UG|{?DufEhKic$.6st&y9?4Sc!uQdv+>6tG<=@O :xPya-qDk6T!0*f{{');
define('NONCE_KEY',        '3SrCsl-#U~>+,J.u{~c6$,dU)h5:F>?4<r-7A$wSwp~a6=YR+AdVN@/Q+}*x(jO.');
define('AUTH_SALT',        'ss NUue:hkr}w@z,^6|B#O^,0Wy9fDSlV]fJ)C0!+m2N;xc<e&MAuH,1@=f6{A,`');
define('SECURE_AUTH_SALT', '^jZSsYp1+*N@8S^a2^W%1WOJ!uw0Y0?~1i!IH^J`&~es95B:1YiQ%e{X-sK7C%|`');
define('LOGGED_IN_SALT',   '+ac-?>I?vu+id_79Y>`oB6k,q|SP,: 8I7D|(I5bBH}nbzE)~/DqMCtA%7HbZJ/e');
define('NONCE_SALT',       'oTsBvW^J`Zz9LbOCfiiWMr?=OENugG|+0_ITnpl/R;eZ<X,K&<@Xv]eby^EW*+D3');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique. 
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'wp_';

/** 
 * Pour les développeurs : le mode deboguage de WordPress.
 * 
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de 
 * développement.
 */ 
define('WP_DEBUG', false); 

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');

define('FS_METHOD', 'direct');
