<?php
/**
 * bbPress Archives and Search Results content
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Post Formats
 * @copyright   2014 WebMan - Oliver Juhas
 */
?>

<section <?php post_class(); echo wm_schema_org( 'item_list' ); ?>>

	<?php
	wmhook_entry_top();

	the_content();

	wmhook_entry_bottom();
	?>

</section>