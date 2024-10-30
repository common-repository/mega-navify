<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Widgets Class
 *
 * Handles Widgets functionalities
 *
 * @package Mega Navify
 * @since 1.0
 */

class Meganavify_Widgets {    

	//class constructor
	function __construct(){
	}

    /**
     * Get Available Widgets
     *
     * Handles get available widgets
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_get_available_widgets(){
        
        global $wp_widget_factory;

        $widgets = array();

        foreach ( $wp_widget_factory->widgets as $widget ) {

            $disabled_widgets = array();
            if ( ! in_array( $widget->id_base, $disabled_widgets ) ) {

                $widgets[] = array(
                    'text'  => $widget->name,
                    'value' => $widget->id_base,
                );
            }
        }

        return $widgets;
    }


    /**
     * Get Available Widgets
     *
     * Add widget t column
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_add_column_widget(){

        // Check if our nonce is set.
        if (!isset($_POST['nonce'])) {
            wp_send_json_error('Nonce is missing');
            return;
        }
        
        $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
        
        if (!wp_verify_nonce($nonce, 'meganavify-grid-ajax-nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }       
        
        $widget_id = isset( $_POST['widget'] ) ? sanitize_text_field( wp_unslash($_POST['widget'])) : '';
        $title = isset( $_POST['title'] ) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
        $menu_item_id = isset( $_POST['menu_item_id'] ) ? absint($_POST['menu_item_id']) : '';      

        $added = $this->meganavify_add_widget( $widget_id, $menu_item_id, $title );
      
        if ( $added ) {
            wp_send_json( $added );
        } else {
            /* translators: %1$s: widget ID, %2$s: menu item ID */
            wp_send_json( sprintf( esc_html__( 'Failed to add %1$s to %2$s', 'mega-navify' ), $widget_id, $menu_item_id ) );
            
        }
        wp_die();
    }

    /**
     * Adds a widget to WordPress. First creates a new widget instance, then
     * adds the widget instance to the mega menu widget sidebar area.
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_add_widget( $widget_id, $menu_item_id, $title ) {

        require_once( ABSPATH . 'wp-admin/includes/widgets.php' );        
        $next_id = next_widget_id_number( $widget_id );         

        $this->meganavify_add_widget_instance( $widget_id, $next_id, $menu_item_id );       

        $widget_id = $this->meganavify_add_widget_to_sidebar( $widget_id, $next_id );        

        $navify_settings = meganavify_get_menu_setting_by_menu_id($menu_item_id);

        $next_item_index = 0;

        if( isset($navify_settings['rows']['0']['columns'][0]['column_items'])){
            $next_item_index = count($navify_settings['rows']['0']['columns'][0]['column_items']);
        }

        $return  = '<div class="widget" data-item-index="'.$next_item_index.'" title="' . esc_attr( $title ) . '" data-columns="2" id="' . $widget_id . '" data-type="widget" data-id="' . $widget_id . '">';
        $return .= '    <div class="widget-top">';
        $return .= '        <div class="widget-title">';
        $return .= '            <h4>' . esc_html( $title ) . '</h4>';
        $return .= '        </div>';
        $return .= '        <div class="widget-title-action">';
        $return .= '            <a class="widget-option widget-action" title="' . esc_attr( __( 'Edit', 'mega-navify' ) ) . '"><span class="dashicons dashicons-admin-tools"></span></a>';
        $return .= '        </div>';
        $return .= '    </div>';
        $return .= '    <div class="widget-inner widget-inside"></div>';
        $return .= '</div>';

        return $return;
    }

   

    /*
     * Adds a widget to the Mega Menu widget sidebar
     *
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_add_widget_to_sidebar( $id_base, $next_id ) {

       $widget_id = $id_base . '-' . $next_id;

       $sidebar_widgets = $this->meganavify_get_mega_menu_sidebar_widgets();
       $sidebar_widgets[] = $widget_id;
      
       $this->set_mega_menu_sidebar_widgets( $sidebar_widgets );

       return $widget_id;

   }

    /**
     * Adds a new widget instance of the specified base ID to the database.
     *
     *
     * @package Mega Navify
     * @since 1.0
    */    
    public function meganavify_add_widget_instance( $widget_id, $next_id, $menu_item_id ) {

        $current_widgets = get_option( 'widget_' . $widget_id );

        $current_widgets[ $next_id ] = array(
            'meganavify_columns'        => 2,
            'meganavify_parent_menu_id' => $menu_item_id,
        );      

        update_option( 'widget_' . $widget_id, $current_widgets );

    }

    /**
     * Sets the sidebar widgets
     *
     *
     * @package Mega Navify
     * @since 1.0
    */  
    private function set_mega_menu_sidebar_widgets( $widgets ) {

        $sidebar_widgets = wp_get_sidebars_widgets();
        $sidebar_widgets[MEGANAVIFY_SIDEBAR_ID] = $widgets;   
      
        wp_set_sidebars_widgets( $sidebar_widgets );      
    }

    /**
     * Returns an unfiltered array of all widgets in our sidebar
     *
     *
     * @package Mega Navify
     * @since 1.0
    */  
    public function meganavify_get_mega_menu_sidebar_widgets() {

        $sidebar_widgets = wp_get_sidebars_widgets();       

        if ( ! isset( $sidebar_widgets[MEGANAVIFY_SIDEBAR_ID] ) ) {
            return false;
        }

        return $sidebar_widgets[MEGANAVIFY_SIDEBAR_ID];

    }


    /**
     * Edit column widget
     *     
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_edit_column_widget() {       
        // Check if our nonce is set.
        if (!isset($_POST['nonce'])) {
            wp_send_json_error('Nonce is missing');
            return;
        }
        
        $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
        
        if (!wp_verify_nonce($nonce, 'meganavify-grid-ajax-nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }   

        // Ensure user has appropriate permissions
        if (!current_user_can('edit_theme_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        $widget_id = isset($_POST['widget_id']) ? sanitize_text_field(wp_unslash($_POST['widget_id'])) : '';
        
        if (empty($widget_id)) {
            wp_send_json_error('Widget ID is required');
            return;
        }

        if ( ob_get_contents() ) {
            ob_clean(); // remove any warnings or output from other plugins which may corrupt the response
        }
            
        // Directly output the widget form
        $this->meganavify_show_widget_form($widget_id);

        wp_die();        
    }


    /**
     * Returns the widget ID (number)
     *
     * @since 1.0
     * @param string $widget_id - id_base-ID (eg meta-3)
     * @return int
     */
    public function meganavify_get_widget_number_for_widget_id( $widget_id ) {
        $parts = explode( '-', $widget_id );
        return absint( end( $parts ) );
    }

    
   /**
     * Shows the widget edit form for the specified widget.
     *     
     * @package Mega Navify
     * @since 1.0
     * @param string $widget_id The ID of the widget to show the form for.
     */ 
    public function meganavify_show_widget_form($widget_id) {
        global $wp_registered_widget_controls;

        if (!isset($wp_registered_widget_controls[$widget_id])) {
            return;
        }

        $control = $wp_registered_widget_controls[$widget_id];

        $id_base = $this->meganavify_get_id_base_for_widget_id($widget_id);
        $nonce = wp_create_nonce('meganavify_save_widget_' . $widget_id);

        ob_start();
        ?>
        <form method='post' class="mega-navify-widget-form">
            <input type="hidden" name="widget-id" class="widget-id" value="<?php echo esc_attr($widget_id); ?>" />
            <input type='hidden' name='action' value='meganavify_save_column_widget' />
            <input type='hidden' name='id_base' class="id_base" value='<?php echo esc_attr($id_base); ?>' />
            <input type='hidden' name='widget_id' value='<?php echo esc_attr($widget_id); ?>' />
            <input type='hidden' name='nonce' value='<?php echo esc_attr($nonce); ?>' />
            <div class='widget-content'>
                <?php
                if (is_callable($control['callback'])) {
                    call_user_func_array($control['callback'], $control['params']);
                }
                ?>
                <div class='widget-controls'>
                    <a class='delete' href="#"><?php echo esc_html__('Delete', 'mega-navify'); ?></a> |
                    <a class='close' href="#"><?php echo esc_html__('Close', 'mega-navify'); ?></a>
                </div>  
                <p class='widget-save-wrap'>
                    <button type="submit" class="button-primary save-column-widget"><?php echo esc_html__('Save', 'mega-navify'); ?></button>
                </p>
            </div>
        </form>
        <?php
        echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Save widget data
     *     
     * @package Mega Navify
     * @since 1.0
    */ 
    public function meganavify_save_column_widget(){
        
        $result = array();        
        $widget_id =  isset($_POST['widget_id']) ? sanitize_text_field(wp_unslash($_POST['widget_id'])) : '';
        
        // Check if our nonce is set.
        if (!isset($_POST['nonce'])) {
            wp_send_json_error('Nonce is missing');
            return;
        }
        
        $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
        
        if (!wp_verify_nonce($nonce, 'meganavify_save_widget_'.$widget_id)) {
            wp_send_json_error('Invalid nonce');
            return;
        }
                
        $base_id =  $this->meganavify_get_id_base_for_widget_id($widget_id);
        $updated = $this->meganavify_save_widget( $widget_id,$base_id );

        if ( $updated ) {
            $result['status'] = 'success';
            /* translators: %s: widget base ID */
            $result['message'] = sprintf( esc_html__( 'Saved %s', 'mega-navify' ), $base_id );
            
        } else {
            $result['status'] = 'error';
            /* translators: %s: widget base ID */
            $result['message'] = sprintf( esc_html__( 'Failed to save %s', 'mega-navify' ), $base_id );
        }
        wp_send_json($result);
        wp_die();
    }

   
    /**
     * Saves a widget. Calls the update callback on the widget.
     * The callback inspects the post values and updates all widget instances which match the base ID.
     *     
     * @package Mega Navify
     * @since 1.0
    */ 
    public function meganavify_save_widget( $widget_id,$base_id ) {

        global $wp_registered_widget_updates;
           
        $control = $wp_registered_widget_updates[ $base_id ];

        if ( is_callable( $control['callback'] ) ) {
            call_user_func_array( $control['callback'], $control['params'] );
            return true;
        }
        return false;
    }

    /**
     * Returns the id_base value for a Widget ID
     *
     * @package Mega Navify
     * @since 1.0
    */ 
    public function meganavify_get_id_base_for_widget_id( $widget_id ) {
        global $wp_registered_widget_controls;

        if ( ! isset( $wp_registered_widget_controls[ $widget_id ] ) ) {
            return false;
        }

        $control = $wp_registered_widget_controls[ $widget_id ];

        $id_base = isset( $control['id_base'] ) ? $control['id_base'] : $control['id'];

        return $id_base;

    }
    
    /**
	 * Adding Hooks
	 *
	 * Adding hooks for the widgets class
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
	function meganavify_add_hooks(){
        add_action('wp_ajax_meganavify_add_column_widget', array($this,'meganavify_add_column_widget'));
        add_action('wp_ajax_nopriv_meganavify_add_column_widget', array($this,'meganavify_add_column_widget'));
        
        add_action('wp_ajax_meganavify_edit_column_widget', array($this,'meganavify_edit_column_widget'));
        add_action('wp_ajax_nopriv_meganavify_edit_column_widget', array($this,'meganavify_edit_column_widget'));        
        
        add_action('wp_ajax_meganavify_save_column_widget', array($this,'meganavify_save_column_widget'));
        add_action('wp_ajax_nopriv_meganavify_save_column_widget', array($this,'meganavify_save_column_widget'));
	}
}