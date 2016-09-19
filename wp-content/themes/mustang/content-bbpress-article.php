<?php
/**
 * bbPress "Article" content
 *
 * User account
 * Topic tags edit
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Post Formats
 * @copyright   2014 WebMan - Oliver Juhas
 */



$schema_type = 'article';

if ( bbp_is_single_user() ) {
	$schema_type = 'person';
}
?>

<article <?php post_class(); echo wm_schema_org( $schema_type ); ?>>

	<?php
	wmhook_entry_top();

	the_content();

	wmhook_entry_bottom();
	?>

</article>