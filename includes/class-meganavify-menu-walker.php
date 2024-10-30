<?php
// Exit if accessed directly

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\EchoedStringsSniff;

if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Megamenu Walker Class
 *
 * Handles Megamenu Walker functionalities
 *
 * @package Mega Navify
 * @since 1.0
 */

class Meganavify_MegaMenu_Walker extends Walker_Nav_Menu{    

	//class constructor
	function __construct(){
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @package Mega Navify
	 * @since 1.0
	*/
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class=\"meganavify-sub-menu\">\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @package Mega Navify
	 * @since 1.0
	*/
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}
	
	/**
	 * Custom walker. Add the widgets into the menu.
	 *
	 * @package Mega Navify
	 * @since 1.0
	*/
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {	
	
		if( empty($item)){
			return $output;
		}
		
		$item_settings = meganavify_get_item_settings($item->ID);
		$display_mode = get_post_meta($item->ID,MEGANAVIFY_PREFIX.'submenu_display_mode',true);	

		$hide_text 		= isset($item_settings['hide_text']) ? $item_settings['hide_text'] : '';
		$icon_position 	= isset($item_settings['icon_position']) ? $item_settings['icon_position'] : '';	
		$disable_link 	= isset($item_settings['disable_link']) ? $item_settings['disable_link'] : '';			
		$hide_on_mobile = isset($item_settings['hide_on_mobile']) ? $item_settings['hide_on_mobile'] : '';	
		$hide_on_desktop = isset($item_settings['hide_on_desktop']) ? $item_settings['hide_on_desktop'] : '';
		$item_icon = isset($item_settings['item_icon']) ? $item_settings['item_icon'] : '';
		$icon_type = isset($item_settings['icon_type']) ? $item_settings['icon_type'] : '';
		
		$attachment = '';
		if( $icon_type == 'custom-icon'){
			$attachment = get_post($item_icon);
		}

		if( $hide_on_mobile == 'true'){
			$item->classes[] = 'navify-hide-on-mobile';
		}
		if( $hide_on_desktop == 'true'){
			$item->classes[] = 'navify-hide-on-desktop';
		}
		$item->classes[] = $display_mode;		

		$item_html = $args->before;
		$item_html .= "<li class='" .  implode(" ", $item->classes) . "'>";
		
        if ($item->url ) {
			if( $disable_link == 'true'){
				$item_html .= '<a>';
			} else {
				$item_html .= '<a href="' . $item->url . '">';
			}

			if( !empty($item_icon) && $icon_type != 'custom-icon'){
				$item_html .= '<span class="'.$item_icon. ' ' . $icon_position .'"></span>';
			}
			elseif(!empty($item_icon) && $icon_type == 'custom-icon') {
				$item_html .= '<span><img src="'.$attachment->guid.'" alt="'.$attachment->post_title.'"> </span>';
			}

        } else {
            $item_html .= '<span>';
        }
		
		if( $hide_text != 'true' ){
        	$item_html .= $item->title;
		}
 
        if ($item->url ) {
            $item_html .= '</a>';
        } else {
            $item_html .= '</span>';
        }


		$item_id = $item->ID;

		$menu_items_rows = array();
		$menu_settings = meganavify_get_menu_setting_by_menu_id($item_id);
		if( !empty($menu_settings)){
			$menu_items_rows = meganavify_get_menu_item_rows($menu_settings);
		}
		
		if( !empty($menu_items_rows) && $display_mode == 'grid'){
			
			$item_html .= '<ul class="meganavify-submenu-wrap">';

			foreach($menu_items_rows as $item_row){
				$menu_items_cols = meganavify_get_columns_by_item_row($item_row);
				$css_class = array();
				$row_css_class = isset($item_row['css_class']) ? $item_row['css_class'] : '';
				$css_class[] = 'meganavify-row-container';
				$css_class[] = $row_css_class;
				$css_class = apply_filters('meganavify_row_css_class', $css_class, $item_row);
				$row_class = implode(' ', $css_class);	
				$row_class = trim($row_class);

				$item_html .= '<div class="'.$row_class.'">';

				if( !empty($menu_items_cols)){
	
						foreach($menu_items_cols as $column){
							$col_css_class = array();
							$no_of_columns = isset($column['no_of_columns']) ? $column['no_of_columns'] : 1;

							$colcss_class = isset($column['css_class']) ? $column['css_class'] : '';
							$col_css_class[] = 'meganavify-col-container';
							$col_css_class[] = 'col-'.$no_of_columns.'-12';
							$col_css_class[] = $colcss_class;
							$col_css_class = apply_filters('meganavify_column_css_class', $col_css_class, $item_row);
							$col_class = implode(' ', $col_css_class);
							
							$item_html .= '<div class="'.$col_class.'">';
							$menu_items_cols = meganavify_get_columns_items($column);

							foreach($menu_items_cols as $item){
								$widget_id = isset($item['widget_id']) ? $item['widget_id'] : ''; 
								if( !empty($widget_id)){
									$item_html .= '<div class="meganavify-item-container">';
										$item_html .= $this->meganavify_show_widget($widget_id);
									$item_html .= '</div>';
								}
							}

							$item_html .= '</div>';
						}
				}

				$item_html .= '</div>';
			}
			$item_html .= '</ul>';
		}

		$item_html .= $args->after;

		$output .= apply_filters( 'meganeviry_walker_nav_menu_start_el', $item_html, $item, $depth, $args );       
	}

	/**
	 * Get wigdets of column
	 *
	 * @package Mega Navify
	 * @since 1.0
	*/
	public function meganevify_get_column_items($item_data ){

		if( empty($item_data)  ){
			return ;
		}

		$item_settings  = get_post_meta($item_data->ID, MEGANAVIFY_PREFIX.'grid_system', true);
		$item_settings = json_decode($item_settings, true);

		if( !empty($item_settings)){
			foreach($item_settings as $key => $rows){
				
				if( !empty($rows)){
					foreach($rows as $row_key => $row){

						$columns = isset($row['columns']) ? $row['columns'] : array();
						foreach( $columns as $col_key => $column){

							$no_of_columns = isset($column['no_of_columns']) ? $column['no_of_columns'] : 1;
							$column_items = isset($column['column_items']) ? $column['column_items'] : array();
							
							foreach($column_items as $item){
								$widget_id = $item['widget_id'];
								
								echo esc_html($this->meganavify_show_widget($widget_id));
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Handle to render wigdets of column
	 *
	 * @package Mega Navify
	 * @since 1.0
	*/
	public function meganavify_show_widget( $id){

		global $wp_registered_widgets;

		$params = array_merge(
			array(
				array_merge(
					array(
						'widget_id'   => $id,
						'widget_name' => $wp_registered_widgets[ $id ]['name'],
					)
				),
			),
			(array) $wp_registered_widgets[ $id ]['params']
		);

		$params[0]['id']            = 'meganavify';
		$params[0]['before_title']  = apply_filters( 'meganavify_before_widget_title', '<h4 class="mega-block-title">', $wp_registered_widgets[ $id ] );
		$params[0]['after_title']   = apply_filters( 'meganavify_after_widget_title', '</h4>', $wp_registered_widgets[ $id ] );
		$params[0]['before_widget'] = apply_filters( 'meganavify_before_widget', '', $wp_registered_widgets[ $id ] );
		$params[0]['after_widget']  = apply_filters( 'meganavify_after_widget', '', $wp_registered_widgets[ $id ] );

		if ( defined( 'MEGANAVIFY_DYNAMIC_SIDEBAR_PARAMS' ) && MEGANAVIFY_DYNAMIC_SIDEBAR_PARAMS ) {
			$params[0]['before_widget'] = apply_filters( 'meganavify_before_widget', '<div id="" class="">', $wp_registered_widgets[ $id ] );
			$params[0]['after_widget']  = apply_filters( 'meganavify_after_widget', '</div>', $wp_registered_widgets[ $id ] );

			$params = apply_filters( 'meganavify_dynamic_sidebar_params', $params );
		}

		$callback = $wp_registered_widgets[ $id ]['callback'];

		if ( is_callable( $callback ) ) {
			ob_start();
			call_user_func_array( $callback, $params );
			return ob_get_clean();
		}
	}

	
	/**
	 * Ends the element output, if needed.
	 *
	 * @package Mega Navify
	 * @since 1.0
	*/
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= '</li>'; // remove new line to remove the 4px gap between menu items
	}   
}