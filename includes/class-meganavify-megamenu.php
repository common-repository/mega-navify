<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * MegaMenus Class
 *
 * Handles MegaMenu functionalities
 *
 * @package Mega Navify
 * @since 1.0
 */

class Meganavify_MegaMenus {
	
    public function meganavify_modify_nav_menu_args( $args ) {     
		
        if ( ! isset( $args['theme_location'] ) ) {
            return $args;
        }        

        $settings               = get_option( MEGANAVIFY_PREFIX.'options' ); 	  
        
        $current_theme_location = $args['theme_location'];   

        $locations = get_nav_menu_locations();       	 
	
        if ( isset( $settings[ $current_theme_location ]['enable'] ) && true === boolval( $settings[ $current_theme_location ]['enable'] ) ) {            
    
            if ( ! isset( $locations[ $current_theme_location ] ) ) {
                return $args;
            }

            $menu_id = $locations[ $current_theme_location ];            

            if ( ! $menu_id ) {
                return $args;
            }        
            
            $container = 'div';    
           
			$desk_effect = isset($settings[$current_theme_location]['desk_effect']) ? $settings[$current_theme_location]['desk_effect'] : '';
			$desk_speed = isset($settings[$current_theme_location]['desk_speed']) ? $settings[$current_theme_location]['desk_speed'] : '';
			$mobile_effect = isset($settings[$current_theme_location]['mobile_effect']) ? $settings[$current_theme_location]['mobile_effect'] : '';
			$mobile_speed = isset($settings[$current_theme_location]['mobile_speed']) ? $settings[$current_theme_location]['mobile_speed'] : '';

			$container_class[] = 'meganavify-menu-wrap meganavify-menu-container mobile-slide';
			if( !empty($desk_effect)){
				$container_class[] = 'desktop-effect-'.$desk_effect;
			}
			if( !empty($desk_speed)){
				$container_class[] = 'desktop-speed-'.$desk_speed;
			}
			if( !empty($mobile_effect)){
				$container_class[] = $mobile_effect;
			}
			if( !empty($mobile_effect)){
				$container_class[] = 'mobile-speed-'.$mobile_effect;
			}

			$container_class = implode(" " ,$container_class); 

            $sanitized_location = str_replace( apply_filters( 'meganavify_location_replacements', array( '-', ' ' ) ), '-', $current_theme_location );
			
            $defaults = array(
                'menu'            => wp_get_nav_menu_object( $menu_id ),
                'container'       => $container,
                'container_class' => $container_class,
                'container_id'    => 'meganavify-menu-wrap-' . $sanitized_location,
                'menu_class'      => 'meganavify-menu max-meganavify-menu meganavify-menu-horizontal',
                'menu_id'         => 'meganavify-menu-' . $sanitized_location,
                'fallback_cb'     => 'wp_page_menu',
                'before'          => '',
                'after'           => '',
                'link_before'     => '',
                'link_after'      => '',
                'items_wrap'      => '<ul>%3$s</ul>',
                'depth'           => 0,
                'walker'          => new Meganavify_MegaMenu_Walker(),
            );

			

            $args = array_merge( $args, apply_filters( 'meganavify_nav_menu_args', $defaults, $menu_id, $current_theme_location ) );
        }

        return $args;
    }

	/**
	 * Returns the HTML for a single widget instance.
	 *
	 * @package Mega Navify
	 * @since 1.0
	*/
	public function show_widget( $id ) {
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

		$params[0]['id']            = MEGANAVIFY_SIDEBAR_ID;
		$params[0]['before_title']  = apply_filters( 'meganavify_before_widget_title', '<h4 class="mega-block-title">', $wp_registered_widgets[ $id ] );
		$params[0]['after_title']   = apply_filters( 'meganavify_after_widget_title', '</h4>', $wp_registered_widgets[ $id ] );
		$params[0]['before_widget'] = apply_filters( 'meganavify_before_widget', '', $wp_registered_widgets[ $id ] );
		$params[0]['after_widget']  = apply_filters( 'meganavify_after_widget', '', $wp_registered_widgets[ $id ] );

		if ( defined( 'MEGANAVIFY_DYNAMIC_SIDEBAR_PARAMS' ) && MEGANAVIFY_DYNAMIC_SIDEBAR_PARAMS ) {
			$params[0]['before_widget'] = apply_filters( 'meganavify_before_widget', '<div id="" class="">', $wp_registered_widgets[ $id ] );
			$params[0]['after_widget']  = apply_filters( 'meganavify_after_widget', '</div>', $wp_registered_widgets[ $id ] );			
		}

		$callback = $wp_registered_widgets[ $id ]['callback'];

		if ( is_callable( $callback ) ) {
			ob_start();
			call_user_func_array( $callback, $params );
			echo wp_kses_post(ob_get_clean());
		}
	}    

    /**
	 * Adding Hooks
	 *
	 * Adding hooks for the megamenus class
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
	function meganavify_add_hooks(){
        add_filter( 'wp_nav_menu_args', array( $this, 'meganavify_modify_nav_menu_args' ), 99999 );        
	}
}