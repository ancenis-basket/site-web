<?php
/*
Plugin Name: Showcase - Visual Composer Addon
Version: 1.0.4
Description: Create an amazing carousel showcase with Ajax presentations with Showcase - Visual Composer Addon you have three different box presentations.
Author: CHR Designer
Author URI:  http://chrdesigner.com
Text Domain: showcase-visual-composer-addon
Domain Path: /languages/
*/

load_plugin_textdomain( 'showcase-visual-composer-addon', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );

/*
 * Function Verification version or VC actived
 */
add_action('admin_init','sc_init_addons');
function sc_init_addons()
{
    $required_vc = '4.3';
    if(defined('WPB_VC_VERSION')){
        if( version_compare( $required_vc, WPB_VC_VERSION, '>' )){
            add_action( 'admin_notices', 'sc_alert_version_vc');
        }
    } else {
        add_action( 'admin_notices', 'admin_notice_sc_activation');
    }
}// end init_addons
function sc_alert_version_vc()
{
    echo '<div class="error"><p>' . __('The <strong>Showcase Visual Composer</strong> plugin requires <strong>Visual Composer</strong> version 4.3 or greater.' , 'showcase-visual-composer-addon' ) . '</p></div>';   
}
function admin_notice_sc_activation()
{
    echo '<div class="error"><p>' . __('The <strong>Showcase Visual Composer</strong> plugin requires <strong>Visual Composer</strong> installed and activated.' , 'showcase-visual-composer-addon' ) . '</p></div>';
}

/*
 * Function Create Custom Post - showcase_carousel
 */
add_action( 'init', 'create_showcase' );
function create_showcase() {
    register_post_type( 'showcases',
        array(
            'labels' => array(
                'name'                => __( 'Showcases' , 'showcase-visual-composer-addon' ),
                'singular_name'       => __( 'Showcase', 'showcase-visual-composer-addon' ),
                'menu_name'           => __( 'Showcases', 'showcase-visual-composer-addon' ),
                'parent_item_colon'   => __( 'Parent Showcase:', 'showcase-visual-composer-addon' ),
                'all_items'           => __( 'All Showcases', 'showcase-visual-composer-addon' ),
                'view_item'           => __( 'View Showcase', 'showcase-visual-composer-addon' ),
                'add_new_item'        => __( 'Add New Showcase', 'showcase-visual-composer-addon' ),
                'add_new'             => __( 'Add New', 'showcase-visual-composer-addon' ),
                'edit_item'           => __( 'Edit Showcase', 'showcase-visual-composer-addon' ),
                'update_item'         => __( 'Update Showcase', 'showcase-visual-composer-addon' ),
                'search_items'        => __( 'Search Showcase', 'showcase-visual-composer-addon' ),
                'not_found'           => __( 'Not found', 'showcase-visual-composer-addon' ),
                'not_found_in_trash'  => __( 'Not found in Trash', 'showcase-visual-composer-addon' )
            ),
 
            'public'               => true,
            'menu_position'        => 15,
            'supports'             => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies'           => array( '' ),
            'menu_icon'            => '',
            'register_meta_box_cb' => 'showcase_meta_box',
            'has_archive'          => true
        )
    );
}

/*
 * Function for new images sizes - thumb-brand-testimonial | brand-testimonial
 */

add_image_size( 'thumb-brand-testimonial', 220, 220, true );
add_image_size( 'brand-testimonial', 450, 450, array( 'left', 'top' ) );

add_action( 'wp_enqueue_scripts', 'chr_script_showcase_vc_addon' );
function chr_script_showcase_vc_addon() {
    wp_register_script( 'owl.carousel.js', plugins_url('/assets/js/owl.carousel.min.js' , __FILE__ ), false, '1.3.3', true );
    wp_enqueue_script( 'owl.carousel.js' );
    wp_register_style( 'owl.carousel.css', plugins_url('/assets/css/owl.carousel.min.css' , __FILE__ ), false, '1.3.3', false );
    wp_enqueue_style( 'owl.carousel.css' );
    wp_register_style( 'owl.theme.css', plugins_url('/assets/css/owl.theme.min.css' , __FILE__ ), false, '1.3.3', false );
    wp_enqueue_style( 'owl.theme.css' );
    wp_register_style( 'sc.vc.addon.style', plugins_url('/assets/css/style.min.css' , __FILE__ ), false, '1.0.4', false );
    wp_enqueue_style( 'sc.vc.addon.style' );
}

function chr_style_showcase_vc_addon() {
    wp_register_style( 'style.sc.vc.addon.admin', plugins_url('/admin/css/style.sc.vc.addon.admin.css' , __FILE__ ), false, '1.0.4', false );
    wp_enqueue_style( 'style.sc.vc.addon.admin' );
}
add_action( 'admin_enqueue_scripts', 'chr_style_showcase_vc_addon' );

/*
 * Includes
 */
require_once('includes/add-colums-showcase.php');
require_once('includes/shortcode-showcase.php');
require_once('includes/showcase_meta_box.php');
require_once('includes/vc-showcase.php');

/*
 * Add Custom Css Field in Admin Page
 */
add_action('admin_head', 'showcase_admin_css');
function showcase_admin_css() {
    global $post_type;
    if (($_GET['post_type'] == 'showcases') || ($post_type == 'showcases')) :      
        echo "<link type='text/css' rel='stylesheet' href='" . plugins_url('/admin/css/style.min.css', __FILE__) . "' />";
    endif;
}

/*
 * Create and Include custom single page - single-showcases.php
 */

add_filter( 'template_include', 'include_template_showcase', 1 );
function include_template_showcase( $template_path ) {
    if ( get_post_type() == 'showcases' ) {
        if ( is_single() ) {
            if ( $theme_file = locate_template( array ( 'single-showcases.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-showcases.php';
            }
        }
    }
    return $template_path;
}