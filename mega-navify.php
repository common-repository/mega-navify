<?php
/*
  Plugin Name: Mega Navify - The Ultimate Mega Menu
  Plugin URI: https://www.yudiz.com/wordpress-plugin-support/
  Description: A user-friendly Mega Navify extension, built with WordPress core principles. An intuitive mega navigation solution crafted the WordPress way.
  Version: 1.0
  Requires at least: 6.0
  Tested up to: 6.6
  Author: Yudiz Solutions Limited
  Author URI: https://www.yudiz.com/
  Text Domain: mega-navify
  License: GPLv2 or later
  License URI: https://www.gnu.org/licenses/gpl-2.0.html  
*/

/**
 * Basic plugin definitions 
 * 
 * @package Mega Navify
 * @since 1.0
 */

if( !defined( 'MEGANAVIFY_DIR' ) ) {
  define( 'MEGANAVIFY_DIR', dirname( __FILE__ ) );      // Plugin dir
}
if( !defined( 'MEGANAVIFY_VERSION' ) ) {
  define( 'MEGANAVIFY_VERSION', '1.0.0' );      // Plugin Version
}
if( !defined( 'MEGANAVIFY_URL' ) ) {
  define( 'MEGANAVIFY_URL', plugin_dir_url( __FILE__ ) );   // Plugin url
}
if( !defined( 'MEGANAVIFY_INC_DIR' ) ) {
  define( 'MEGANAVIFY_INC_DIR', MEGANAVIFY_DIR.'/includes' );   // Plugin include dir
}
if( !defined( 'MEGANAVIFY_ADMIN_DIR' ) ) {
  define( 'MEGANAVIFY_ADMIN_DIR', MEGANAVIFY_INC_DIR.'/admin' );   // Plugin include dir
}
if( !defined( 'MEGANAVIFY_INC_URL' ) ) {
  define( 'MEGANAVIFY_INC_URL', MEGANAVIFY_URL.'includes' );    // Plugin include url
}
if( !defined( 'MEGANAVIFY_COMPATIBILITY_DIR' ) ) {
  define( 'MEGANAVIFY_COMPATIBILITY_DIR', MEGANAVIFY_INC_DIR.'/compatibility' );   // Plugin include dir
}
if(!defined('MEGANAVIFY_PREFIX')) {
  define('MEGANAVIFY_PREFIX', 'meganavify_'); // Plugin Prefix
}

if(!defined('MEGANAVIFY_SIDEBAR_ID')) {
  define('MEGANAVIFY_SIDEBAR_ID', 'meganavify'); // Variable Prefix
}

/**
 * Load Text Domain
 *
 * This gets the plugin ready for translation.
 *
 * @package Mega Navify
 * @since 1.0
 */
load_plugin_textdomain( 'meganavify', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/**
 * Activation Hook
 *
 * Register plugin activation hook.
 *
 * @package Mega Navify
 * @since 1.0
 */
register_activation_hook( __FILE__, 'meganavify_install' );

function meganavify_install(){
	
  ob_start();
    require MEGANAVIFY_DIR. '/meganavify-icons.json';
  $icon_json = ob_get_clean();

  update_option(MEGANAVIFY_PREFIX.'_icons',$icon_json);  
}

/**
 * Deactivation Hook
 *
 * Register plugin deactivation hook.
 *
 * @package Mega Navify
 * @since 1.0
 */
register_deactivation_hook( __FILE__, 'meganavify_uninstall');

function meganavify_uninstall(){  
}

// Global variables
global $meganavify_scripts, $meganavify_model,$meganavify_admin,$meganavify_menu_model,$meganavify_menu_widget;

//Declare mix system function
include_once( MEGANAVIFY_INC_DIR.'/meganavify-misc-functions.php' );

// Script class handles most of script functionalities of plugin
include_once( MEGANAVIFY_INC_DIR.'/class-meganavify-scripts.php' );
$meganavify_scripts = new Meganavify_Scripts();
$meganavify_scripts->meganavify_add_hooks();

// Model class handles most of model functionalities of plugin
include_once( MEGANAVIFY_INC_DIR.'/class-meganavify-model.php' );
$meganavify_model = new Meganavify_Model();

// Admin class handles most of backend functionalities of plugin
include_once( MEGANAVIFY_ADMIN_DIR.'/class-meganavify-admin.php' );
$meganavify_admin = new Meganavify_Admin();
$meganavify_admin->meganavify_add_hooks();

// Include the main megamenu customize class file.
include_once( MEGANAVIFY_INC_DIR.'/class-meganavify-megamenu.php' );
$meganavify_menumenus =  new Meganavify_MegaMenus();
$meganavify_menumenus->meganavify_add_hooks();

include_once( MEGANAVIFY_INC_DIR.'/class-meganavify-menu-walker.php' );
$meganavify_menumenus =  new Meganavify_MegaMenus();

include_once( MEGANAVIFY_ADMIN_DIR.'/class-meganavify-menu-model.php' );
$meganavify_menu_model = new Meganavify_MenuModel();
$meganavify_menu_model->meganavify_add_hooks();

include_once( MEGANAVIFY_ADMIN_DIR.'/class-meganavify-widgets.php' );
$meganavify_menu_widget = new Meganavify_Widgets();
$meganavify_menu_widget->meganavify_add_hooks();

include_once( MEGANAVIFY_COMPATIBILITY_DIR.'/blocks/class-meganavify-block.php' );
$Meganavify_block = new Meganavify_Block();
$Meganavify_block->meganavify_add_hooks();