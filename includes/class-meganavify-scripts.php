<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Scripts Class
 *
 * Handles adding scripts functionality of the plugin
 * as well as the front pages.
 *
 * @package Mega Navify
 * @since 1.0
 */

class Meganavify_Scripts {

	//class constructor
	function __construct()
	{
		
	}
	
	/**
	 * Enqueue Scripts on Admin Side
	 * 
	 * @package Mega Navify
	 * @since 1.0
	*/
	public function meganavify_admin_scripts($hook_suffix){		

		$allowed_pages = array('nav-menus.php','toplevel_page_meganavify-settings');

		if( in_array($hook_suffix, $allowed_pages)){

			wp_enqueue_script('jquery');

			wp_register_style( 'meganavify-admin-css', MEGANAVIFY_INC_URL.'/css/meganavify-admin.css', false, '1.0.0' );
			wp_enqueue_style('meganavify-admin-css');

			wp_register_script( 'meganavify-admin',MEGANAVIFY_INC_URL.'/js/meganavify-admin.js', array('jquery'), '1.0.0', true );
			wp_enqueue_script('meganavify-admin');

			wp_localize_script( 'meganavify-admin', 'meganavify_object',
				array( 
					'ajaxurl' => admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')),
					'nonce' => wp_create_nonce('meganavify-ajax-nonce'),
					'menu_label' => __('Navify Megamenu', 'mega-navify'),
					'save_menu_error' => __('An error occurred. Please save the menu before proceeding.', 'mega-navify'),
					'meganavify_disabled' => __('Please activate  Meganavify Menu using the settings available on the left side of this page.', 'mega-navify')
				)
			);
			wp_register_script( 'meganavify-grid-system',MEGANAVIFY_INC_URL.'/js/meganavify-grid-system.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), '1.0.0', false );
			wp_enqueue_script('meganavify-grid-system');

			wp_localize_script( 'meganavify-grid-system', 'megaNavifyGridObject',
				array( 
					'ajaxurl' => admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')),
					'nonce' => wp_create_nonce('meganavify-grid-ajax-nonce'),
					'confirm_delete_row' => __('Are you sure you want to delete this row?', 'mega-navify'),	
					'confirm_delete_column' => __('Are you sure you want to delete this column?', 'mega-navify'),
					'row_not_exits' => __('Row does not exist', 'mega-navify'),		
					'column_limit_reached' => __('You can not add more than 12 columns in a row.', 'mega-navify'),
					'min_column_reached' => __('You can not decrease the column width less than 1.', 'mega-navify'),	
					'top_level_menu_items' => __('Mega Menus can only be created on top level menu items.', 'mega-navify'),	
					'select_upload_icon' => __('Select or Upload Icon', 'mega-navify'),	
					'use_this_icon' => __('Use this icon', 'mega-navify')
				)
			);

			wp_register_style('meganavify-font-awesome-all-admin', MEGANAVIFY_INC_URL.'/css/meganavify-font-awesome-all.min.css', false, '1.0.0' );
			wp_enqueue_style('meganavify-font-awesome-all-admin');	

		}
	}

	/**
	 * Enqueue Scripts on Public Side
	 * 
	 * @package Mega Navify
	 * @since 1.0
	 */
	public function meganavify_public_scripts(){
				
		$enabled_menus = array();
		$meganavify_options = get_option(MEGANAVIFY_PREFIX.'_options');
		if( !empty($meganavify_options)){
			$enabled_menus =  array_keys($meganavify_options);	
		}
		foreach ($enabled_menus as $menu) {
			if (has_nav_menu($menu)) {
				wp_register_style('meganavify-public-css', MEGANAVIFY_INC_URL . '/css/meganavify-public.css', false, '1.0.0');
				wp_enqueue_style('meganavify-public-css');

				wp_register_style('meganavify-font-awesome-all-admin', MEGANAVIFY_INC_URL.'/css/meganavify-font-awesome-all.min.css', false, '1.0.0' );
				wp_enqueue_style('meganavify-font-awesome-all-admin');


				wp_register_script('meganavify-public-js', MEGANAVIFY_INC_URL . '/js/meganavify-public.js', array('jquery'), '1.0.0', false);
				wp_enqueue_script('meganavify-public-js');

				wp_localize_script( 'meganavify-public-js', 'megaNavifyPublicObject',
				array( 
					'ajaxurl' => admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')),
					'mobile_resolution' =>  !empty(get_option(MEGANAVIFY_PREFIX.'responsive_breakpoint')) ? get_option(MEGANAVIFY_PREFIX.'responsive_breakpoint') : '767'
				)
			);

				break; // Exit the loop once a menu is found and the style is enqueued
			}
		}
	}

	/**
	 * Print the widgets.php scripts on the nav-menus.php page
	 * 
	 * @package Mega Navify
	 * @since 1.0
	 */	
	public function meganavify_load_widget_scripts( $hook ) {
		do_action( 'admin_print_scripts-widgets.php' );
	}

	/**
	 * Print the widgets.php scripts on the nav-menus.php page
	 * 
	 * @package Mega Navify
	 * @since 1.0
	 */	
	public function meganavify_load_widget_styles( $hook ) {
		do_action( 'admin_print_styles-widgets.php' );
	}
		
	/**
	 * Print the widgets.php scripts on the nav-menus.php page
	 * 
	 * @package Mega Navify
	 * @since 1.0
	 */	
	public function meganavify_load_widget_footer_scripts( $hook ) {
		do_action( 'admin_footer-widgets.php' );
	}
	
	/**
	 * Adding Hooks
	 *
	 * Adding hooks for the styles and scripts.
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
	function meganavify_add_hooks(){
		add_action('admin_enqueue_scripts', array($this, 'meganavify_admin_scripts'),11);
		add_action('wp_enqueue_scripts', array($this, 'meganavify_public_scripts'));
		add_action( 'enqueue_block_editor_assets', array($this,'meganavify_public_scripts') );
		add_action( 'admin_print_scripts-nav-menus.php', array( $this, 'meganavify_load_widget_scripts' ) );
		add_action( 'admin_print_styles-nav-menus.php', array( $this, 'meganavify_load_widget_styles' ) );
        add_action( 'admin_print_footer_scripts-nav-menus.php', array( $this, 'meganavify_load_widget_footer_scripts' ) );
	}
}