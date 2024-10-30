<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Block Class
 *
 * Handles create custom gutenberg block for menu 
 *
 * @package Mega Navify
 * @since 1.0
 */
class Meganavify_Block {

	/**
	 * Block Class
	 *
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @package Mega Navify
	 * @since 1.0
	*/	
	function meganavify_block_init()
	{
		register_block_type(MEGANAVIFY_COMPATIBILITY_DIR.'/blocks//build', array(
			'render_callback' => array($this,'meganavify_block_render_callback')
		));
	}	

	/**
	 * Block Class
	 *
	 * Render callback function
	 *
	 * @package Mega Navify
	 * @since 1.0
	*/	
	public function meganavify_block_render_callback( $attributes, $content, $block )  {
	
		if ( isset( $attributes['locations'] ) && strlen( $attributes['locations'] ) ) {
			if ( has_nav_menu( $attributes['locations'] ) ) {
				$menu = wp_nav_menu( array( 'theme_location' => $attributes['locations'], 'echo' => false ) );
			} else {
				$menu = "<p>" . esc_html__("No menu assigned to this location.", "mega-navify") . "</p>";
			}
		} else {
			if ( $this->meganavify_is_editing_block_on_backend() ) {
				$menu = "<p>" . esc_html__("Enable Meganavify Menu for this location.", "mega-navify") . "</p>";
			} else {
				$menu = "<!--" . esc_html__("Enable Meganavify Menu for this location.", "mega-navify") . "-->";
			}
		}
	
		return $menu;
	}

	/**
	 * Block Class
	 *
	 * Editing block on backend
	 *
	 * @package Mega Navify
	 * @since 1.0
	*/	
	function meganavify_is_editing_block_on_backend() {
		return defined('REST_REQUEST') && true === REST_REQUEST && 'edit' === filter_input( INPUT_GET, 'context', FILTER_SANITIZE_STRING );
	}

	/**	 
	 *
	 * Adding hooks for the gutenberg block
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
	function meganavify_add_hooks(){
		add_action('init', array($this,'meganavify_block_init'));
	}
}