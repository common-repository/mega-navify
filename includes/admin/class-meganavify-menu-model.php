<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * Admin Class
 *
 * Handles popup functionalities of the plugin 
 *
 * @package Mega Navify
 * @since 1.0
 */

class Meganavify_MenuModel {

    public $model;    

	//class constructor
	function __construct(){
        global $meganavify_model;
        $this->model = $meganavify_model;        
	}


    /**         
     * Handles render popup model
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_admin_navifymenu_popop(){
        ?>
        <div id="navify-menu-modal" class="navify-menu-modal" style="display: none;">
            <div class="navify-menu-modal-content">
                <div class="navify-menu-modal-header">
                    <h2><?php esc_html_e('Menu Title','mega-navify') ?></h2>
                    <div class="icons-loader">
                        
                        <div class="saving-loader" style="display: none;"><?php esc_html_e('Saving','mega-navify') ?></div>
                    </div>
                    <span class="navify-menu-close">&times;</span>
                </div>
                <div class="navify-menu-modal-body">
                    <div class="navify-model-tab-container">
                        <div class="tab-container">
                            <div class="tab">
                                <button class="tablinks active" data-tab-target="#navify-menu"><?php esc_html_e('Navify Menu','mega-navify') ?></button>
                                <button class="tablinks" data-tab-target="#navify-menu-settings"><?php esc_html_e('Settings','mega-navify') ?></button>
                                <button class="tablinks" data-tab-target="#navify-menu-icons"><?php esc_html_e('Icons','mega-navify') ?></button>
                            </div>
                        </div>
                        <div class="tab-content-container">
                            <input type="hidden" name="menu_item_id" id="menu_item_id" value="">
                            <div class="tab_content-result"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
    }
    

    /**         
     * Render model grid
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_get_grid_system(){     

        global $meganavify_menu_widget,$wp_registered_widgets;       

       // First, check if the nonce is set
        if (!isset($_POST['nonce'])) {
            wp_send_json_error('Nonce is missing');
            return;
        }

        // If we've reached this point, we know $_POST['nonce'] is set
        $clean_nonce = sanitize_text_field(wp_unslash($_POST['nonce']));      

        // Finally, verify the nonce
        if (!wp_verify_nonce($clean_nonce, 'meganavify-grid-ajax-nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }
       
        $display_mode = isset($_POST['display_mode']) ? sanitize_text_field(wp_unslash($_POST['display_mode'])) : 'flyout';
        
        require_once( ABSPATH . 'wp-admin/includes/widgets.php' );        
        
        $menu_item_id = isset($_POST['menu_item_id']) ? intval($_POST['menu_item_id']): 0;

        $navifyGrids = $post_navifyGrids = array();

        $navifyGrids = isset($_POST['navifyGrids']['rows']) ? meganavify_sanitize_mixed_array(wp_unslash($_POST['navifyGrids']['rows'])) : array();
        $post_navifyGrids = isset($_POST['navifyGrids']) ? meganavify_sanitize_mixed_array(wp_unslash($_POST['navifyGrids'])) : array();

        update_post_meta($menu_item_id, MEGANAVIFY_PREFIX.'submenu_display_mode',$display_mode);
        update_post_meta( $menu_item_id, MEGANAVIFY_PREFIX.'grid_system', wp_json_encode($post_navifyGrids) );

        $rowCount = count($navifyGrids);

        if( !empty( $navifyGrids ) ){

            foreach( $navifyGrids  as $row_key => $columns ){
                
                $total_columns = meganavify_get_row_column_counts($columns);
                $row_css_class = isset($columns['css_class']) ? $columns['css_class'] : '';
                $row_hide_on_desktop = isset($columns['hide_on_desktop']) ? $columns['hide_on_desktop'] : '';
                $row_hide_on_mobile = isset($columns['hide_on_mobile']) ? $columns['hide_on_mobile'] : '';
                ?>
                <div class="navify-grid-container" data-available-cols="12"  data-column-count="<?php echo esc_html($total_columns) ?>" data-row="<?php echo esc_html($row_key) ?>">
                    <div class="row-header">
                        <div class="row-settings">
                            <?php if( $rowCount > 1){ ?>
                                <span class="dashicons dashicons-move drag-drop-row"></span>
                            <?php } ?>
                            <span class="dashicons dashicons-admin-generic add-row-classs-toggle"></span>
                            <span class="dashicons dashicons-admin-page duplicate-row">
                            <span class="tooltiptext"><?php esc_html_e( 'Duplicate Row', 'mega-navify' ) ?></span>
                            </span>
                            <span class="dashicons dashicons-desktop row-show-desktop <?php echo ($row_hide_on_desktop == '1' ) ? 'active': ''  ?>">
                                <?php $row_tooltip_text_desktop  = ($row_hide_on_desktop == '1' ) ? esc_html__( 'Show on Desktop', 'mega-navify' ) : esc_html__( 'Hide on Desktop', 'mega-navify' ); ?>
                                <span class="tooltiptext"><?php echo esc_html($row_tooltip_text_desktop) ?></span>
                            </span>
                            <span class="dashicons dashicons-smartphone row-show-smartphone <?php echo ($row_hide_on_mobile == '1' ) ? 'active': ''  ?>">
                                <?php $row_tooltip_text_mobile  = ($row_hide_on_mobile == '1' ) ?  esc_html__( 'Show on Mobile', 'mega-navify' ) :  esc_html__( 'Hide on Mobile', 'mega-navify' ); ?>
                                <span class="tooltiptext"><?php echo esc_html($row_tooltip_text_mobile) ?></span>
                            </span>
                            <?php if( $rowCount > 1){ ?>
                                <span class="dashicons dashicons-trash remove-row">
                                    <span class="tooltiptext"><?php esc_html_e( 'Remove Row', 'mega-navify' ) ?></span>
                                </span>
                            <?php } ?>
                        </div>
                        <div class="row-add-col">
                            <select id="navify-add-colum" class="navify-add-colum">
                                <option value=""><?php esc_html_e( 'Select colum count', 'mega-navify' ) ?></option>
                                <?php 

                                    $remainig_cols = 12 - $total_columns;
                                    for ($i = 1; $i <=12; $i++) { 

                                        $disabled = '';
                                        if( $i > $remainig_cols ){
                                            $disabled = 'disabled';
                                        }
                                        echo '<option value="'.esc_html($i).'" '.esc_html($disabled).'>';
                                        /* translators: %s: The number of columns */
                                        echo sprintf(esc_html__('%s Column', 'mega-navify'), esc_html( $i ) );
                                        echo '</option>';
                                        }
                                ?>
                            </select>                            
                        </div>
                    </div>
                    <div class="setting-wrap row-settings" style="display: none;">
                        <input type="text" class="row-class" value="<?php echo esc_html($row_css_class)?>" placeholder="Row Class" />
                        <button class="button-primary add-class-btn row"> <?php esc_html_e( 'Save', 'mega-navify' ) ?></button>
                    </div>

                    <div class="row-error-message error" style="display: none;"></div>
                    <div class="grid-wrap">
                        <?php
                        $colCount = count($columns['columns']);

                        foreach($columns['columns'] as $column_index => $column){ 

                            $no_of_columns = $column['no_of_columns'];
                            $column_width = $column['column_width'];
                            $column_items = isset($column['column_items']) ? $column['column_items'] : array();
                            $css_class = isset($column['css_class']) ? $column['css_class'] : '';

                            $hide_on_mobile = isset($column['hide_on_mobile']) ? $column['hide_on_mobile'] : '';
                            $hide_on_desktop = isset($column['hide_on_desktop']) ? $column['hide_on_desktop'] : '';

                            if( $row_hide_on_mobile == '1') {
                                
                                $hide_on_mobile = '1';
                            }
                            if( $row_hide_on_desktop == '1') {
                                $hide_on_desktop = '1';
                            }
                         
                            ?>
                            <div class="navify-grid" data-span="<?php echo esc_html($no_of_columns) ?>" data-column-index="<?php echo esc_html($column_index) ?>">
                                <div class="column-header">
                                    <div class="column-settings">
                                        <?php if( $colCount > 1){ ?>
                                            <span class="dashicons dashicons-move drag-drop-column"></span>
                                        <?php } ?>
                                        <span class="dashicons dashicons-admin-generic add-row-classs-toggle"></span>
                                        <span class="dashicons dashicons-admin-page duplicate-column">                                            
                                            <span class="tooltiptext"><?php esc_html_e( 'Duplicate Column', 'mega-navify' ) ?></span>
                                        </span>
                                        <span class="dashicons dashicons-desktop column-show-desktop <?php echo ($hide_on_desktop == '1' ) ? 'active': ''  ?>">
                                        <?php $col_tooltip_text_desktop  = ($hide_on_desktop == '1' ) ?  esc_html__( 'Show on Desktop', 'mega-navify' )  : esc_html__( 'Hide on Desktop', 'mega-navify' ); ?>
                                            <span class="tooltiptext"><?php echo esc_html( $col_tooltip_text_desktop) ?></span>
                                        </span>
                                        <span class="dashicons dashicons-smartphone column-show-smartphone <?php echo ($hide_on_mobile == '1' ) ? 'active': ''  ?>">
                                            <?php $col_tooltip_text_mobile  = ($hide_on_mobile == '1' ) ?  esc_html__( 'Show on Mobile', 'mega-navify' )  : esc_html__( 'Hide on Mobile', 'mega-navify' ); ?>
                                            <span class="tooltiptext"><?php echo esc_html($col_tooltip_text_mobile) ?></span>
                                        </span>
                                        <?php if( $colCount > 1){ ?>
                                            <span class="dashicons dashicons-trash remove-column">
                                                <span class="tooltiptext"><?php esc_html_e( 'Remove Column', 'mega-navify' ) ?></span>
                                            </span>
                                        <?php } ?>
                                    </div>
                                    <div class="column-counter"> 
                                        <span class="dashicons decrease manage-col-width dashicons-arrow-left-alt2"></span> 
                                        <span class="no-of-col"><?php echo esc_html($no_of_columns) ?></span>
                                        <span class="saparator">/</span>
                                        <span>12</span>
                                        <span class="dashicons increase manage-col-width dashicons-arrow-right-alt2"></span>
                                    </div>
                                </div>
                                <div class="setting-wrap" style="display: none;">
                                    <input type="text" class="column-class" value="<?php echo esc_html($css_class) ?>"  placeholder="Column Class" />
                                    <button class="button-primary add-class-btn column">Save</button>
                                </div>

                                <div class="column-contant">
                                <?php
                                    $column_items = array_filter($column_items);
                                  
                                    foreach($column_items as $item_key => $item){

                                        $widget_id = $item['widget_id'];
                                        // Split the widget ID into its type and instance ID
                                        list($widget_type, $widget_instance_id) = explode('-', $widget_id, 2);

                                        // Get the widget's saved values
                                        $widget_options = get_option('widget_' . $widget_type);
                                        $widget_instance = isset($widget_options[$widget_instance_id]) ? $widget_options[$widget_instance_id] : array();
                                        $html  = '';
                                        $title = '';

                                        $title = !empty($widget_instance['title']) ? $widget_instance['title'] : '';

                                        if (isset($wp_registered_widgets[$widget_id]) && empty($title)) {
                                            $widget = $wp_registered_widgets[$widget_id];
                                            $title = !empty($widget['name']) ? $widget['name'] : '';
                                        }

                                        ?>
                                        <div class="widget" data-item-index="<?php echo esc_attr( $item_key ); ?>" title="<?php echo esc_attr( $title ); ?>" 
                                            data-columns="2" id="<?php echo esc_attr( $widget_id ); ?>" data-type="widget" data-id="<?php echo esc_attr( $widget_id ); ?>">
                                            <div class="widget-top">
                                                <div class="widget-title">
                                                    <h4><?php echo esc_html( $title ); ?></h4>
                                                </div>
                                                <div class="widget-title-action">
                                                    <a class="widget-option widget-action" title="<?php echo esc_attr( __( 'Edit', 'mega-navify' ) ); ?>">
                                                        <span class="dashicons dashicons-admin-tools"></span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="widget-inner widget-inside" data-item-key="<?php echo esc_attr( $item_key ); ?>"></div>
                                        </div>
                                        <?php
                                    }
                                ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php
            } 
        }
        
        wp_die();
    }

    /**
     * Get menu item settings
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_get_menu_item_settings(){      
       

        // Verify that the nonce is valid.
        if (!isset($_POST['nonce'])) {
            wp_send_json_error('Nonce is missing');
            return;
        }
        
        $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));        
        
        if (!wp_verify_nonce($nonce, 'meganavify-grid-ajax-nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }      

        $menu_item_id = isset($_POST['menu_item_id']) ? intval( wp_unslash($_POST['menu_item_id'])) : 0;       
        $result = array();

        if( !empty($menu_item_id)){      
            $display_mode = !empty(get_post_meta($menu_item_id, MEGANAVIFY_PREFIX.'submenu_display_mode', true)) ? get_post_meta($menu_item_id, MEGANAVIFY_PREFIX.'submenu_display_mode', true) : 'flyout';      
            $result['display_mode'] = $display_mode;
            $result['data'] = get_post_meta($menu_item_id, MEGANAVIFY_PREFIX.'grid_system', true);
            
        }
        wp_send_json($result);
        wp_die();        
    }
    


     /**
     * Save menu item settings
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_save_menu_item_settings() {             

        if (!isset($_POST['nounce'])) {
            wp_send_json_error('Nonce is missing');
            return;
        }
        
        $nonce = sanitize_text_field(wp_unslash($_POST['nounce']));       
        
        if (!wp_verify_nonce($nonce, 'navify_menu_item_settings_nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        } 
      
        $form_data = isset($_POST['form_data']) ? $_POST['form_data'] : '';            
        parse_str($form_data, $settings);     
        
        $settings = meganavify_sanitize_mixed_array(wp_unslash($settings));
        
        $result = array();
        if( isset($settings['menu_item_id']) && !empty($settings['menu_item_id']) ){
            $post_id = intval($settings['menu_item_id']);
            $save_settings = get_post_meta($post_id,MEGANAVIFY_PREFIX.'item_settings',true);
            $save_settings = unserialize($save_settings);
            

            unset($settings['_wpnonce'], $settings['_wp_http_referer'], $settings['post_id']); // We don't need to store these values
            $settings = isset($settings['navify_item_settings']) ? $settings['navify_item_settings'] : array();

           
            $hide_text          = isset($settings['hide_text']) ? $settings['hide_text'] : '';
            $disable_link       = isset($settings['disable_link']) ? $settings['disable_link'] : '';
            $hide_on_mobile     = isset($settings['hide_on_mobile']) ? $settings['hide_on_mobile'] : '';
            $hide_on_desktop    = isset($settings['hide_on_desktop']) ? $settings['hide_on_desktop'] : '';
            $icon_position      = isset($settings['icon_position']) ? $settings['icon_position'] : '';

            if( !empty($hide_text)){
                $settings['hide_text'] = $hide_text;
            }
            if( !empty($disable_link)){
                $settings['disable_link'] = $disable_link;
            }
            if( !empty($hide_on_mobile)){
                $settings['hide_on_mobile'] = $hide_on_mobile;
            }
            if( !empty($hide_on_desktop)){
                $settings['hide_on_desktop'] = $hide_on_desktop;
            }
            if( !empty($icon_position)){
                $settings['icon_position'] = $icon_position;
            }           

            if(isset($save_settings['item_icon']) ){
                $settings['item_icon'] =  $save_settings['item_icon'];
            }
            if(isset($save_settings['item_icon']) ){
                $settings['icon_type'] =  $save_settings['icon_type'];
            }           
            
           
            update_post_meta( $post_id, MEGANAVIFY_PREFIX.'item_settings', serialize($settings) );
            $result['status'] = 'updated';
            $result['message'] = esc_html__('Settings saved successfully','mega-navify');
        }
        else{
            $result['status'] = 'error';
            $result['message'] = esc_html__('Error while saving settings','mega-navify');
        }
        wp_send_json($result);
    }

     /**
     * Get menu item active tab
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_get_active_tab(){ 

        if (!isset($_POST['nonce'])) {
            wp_send_json_error('Nonce is missing');
            return;
        }
        
        $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));        
        
        if (!wp_verify_nonce($nonce, 'meganavify-grid-ajax-nonce')) {
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $menu_item_id = isset($_POST['menu_item_id']) ? intval($_POST['menu_item_id']) : 0;        
        
        if( !empty($menu_item_id)){           
            $navify_active_tab = get_post_meta( $menu_item_id, MEGANAVIFY_PREFIX.'active_tab',true );
            if( empty($navify_active_tab)){
                $navify_active_tab = '#navify-menu';
            }
        }

        $result['tab'] = $navify_active_tab;
        $result['item_grid'] = get_post_meta($menu_item_id,MEGANAVIFY_PREFIX.'grid_system',true);
        wp_send_json($result);      
    }
   
    /**
     * Render active tab data 
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_save_get_tab_content(){
       
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
        
        $menu_item_id = isset($_POST['menu_item_id']) ? intval($_POST['menu_item_id']) : 0;        
        $current_tab = isset($_POST['current_tab']) ? sanitize_text_field( wp_unslash($_POST['current_tab'])): '';
        $isSubmenu = isset($_POST['isSubmenu']) ? sanitize_text_field( wp_unslash($_POST['isSubmenu'])) : '';

        $result = array();
        if( !empty($menu_item_id)){
            $navify_active_tab = update_post_meta( $menu_item_id, MEGANAVIFY_PREFIX.'active_tab',$current_tab );
            if( empty($navify_active_tab)){
                $navify_active_tab = '#navify-menu';
            }
            
            ob_start();
            if( $current_tab == '#navify-menu'){                
               
                if($isSubmenu == 'true'){
                    echo '<div class="navify-menu-submenu-notice"><div class="tabcontent">'.esc_html__('Mega Menus can only be created on top level menu items.','mega-navify').'</div></div>';
                }
                else{
                    require_once MEGANAVIFY_ADMIN_DIR.'/forms/navify-megamenu.php';
                }
            }
            else if( $current_tab == '#navify-menu-settings'){
                require_once MEGANAVIFY_ADMIN_DIR.'/forms/navify-megamenu-settings.php';
            }
            else if( $current_tab == '#navify-menu-icons'){
                require_once MEGANAVIFY_ADMIN_DIR.'/forms/navify-megamenu-icons.php';
            }
            $display_mode = get_post_meta($menu_item_id,MEGANAVIFY_PREFIX.'submenu_display_mode',true);
            if( empty($display_mode)){
                $display_mode = 'flyout';
            }

            $tab_content = ob_get_clean();
        }

        $result['html'] = $tab_content;
        $result['active_tab'] = $current_tab;
        $result['display_mode'] = $display_mode;
        wp_send_json($result);        
        die;
    }
   

     /**
     * Handel to update menu item icon
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_update_menu_icon(){

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
    
        $item_icon = isset($_POST['item_icon']) ? sanitize_text_field(wp_unslash($_POST['item_icon'])) : '';
        $icon_type = isset($_POST['icon_type']) ? sanitize_text_field(wp_unslash($_POST['icon_type'])) : '';
        $menu_item_id = isset($_POST['menu_item_id']) ? intval($_POST['menu_item_id']) : 0;
        
        $item_settings = get_post_meta($menu_item_id,MEGANAVIFY_PREFIX.'item_settings',true);
        if( !empty($item_settings)){
            $item_settings = unserialize($item_settings);
        }

        if( empty( $item_settings ) && !is_array($item_settings) ){
            $item_settings = array();
        }     
      
        $item_settings['item_icon'] = $item_icon;
        $item_settings['icon_type'] = $icon_type;  
        
        
        update_post_meta($menu_item_id,MEGANAVIFY_PREFIX.'item_settings',serialize($item_settings));

        if( $icon_type == 'custom-icon'){
            $meganavify_icons = get_option(MEGANAVIFY_PREFIX.'_icons');            
            $meganavify_icons = json_decode($meganavify_icons,true);
            $meganavify_icons[$icon_type][] = $item_icon;
            $meganavify_icons[$icon_type] = array_unique($meganavify_icons[$icon_type]); 
            $meganavify_icons[$icon_type] = array_filter( $meganavify_icons[$icon_type]);

            update_option(MEGANAVIFY_PREFIX.'_icons',wp_json_encode($meganavify_icons));
        }

        wp_send_json_success();
    }

     /**
     * Handel to get list of icons
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_get_lib_icons(){       

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

        $icon_type = isset($_POST['icon_type']) ? sanitize_text_field( wp_unslash($_POST['icon_type'])) : 'dashicons';
        $menu_item_id = isset($_POST['menu_item_id']) ? intval($_POST['menu_item_id']) : 0;

        $meganavify_icons = get_option(MEGANAVIFY_PREFIX.'_icons');
        $meganavify_icons = json_decode($meganavify_icons,true);          
        
        $icons = isset($meganavify_icons[$icon_type]) ? $meganavify_icons[$icon_type] : array();       
        $item_settings = meganavify_get_item_settings($menu_item_id);       
     
        $item_icon = isset($item_settings['item_icon']) ? $item_settings['item_icon'] :'';
        $selected_icon_type = isset($item_settings['icon_type']) ? $item_settings['icon_type'] :'';
      
        ?>   
        <div class="navify-icon-list <?php echo esc_html( $icon_type ) ?>">          
            <div class="naviify-icon-action">
                <?php if($icon_type == 'custom-icon'){ ?>
                    <div class="navify-custom-icon-wrapper">                   
                        <button id="custom-icon-upload-button" class="button media-button"><?php esc_html_e('Upload Icon','mega-navify') ?></button>
                    </div>
                <?php } else{ ?>
                    <input type="text" id="icon-search" placeholder="<?php esc_html_e('Search for an icon...','mega-navify') ?>">
                <?php } ?>
            </div>


            <div class="navify-icon-wrapper">
                <div class="navify-icon-item">
                    <input type="radio" name="navify_item_settings[icon]" value="" class="navify-menu-icon" />
                </div>

                <?php foreach($icons as $key => $icon){ ?>
                    <div class="navify-icon-item">
                        
                        <input type="radio" <?php checked($item_icon,$icon) ?> name="navify_item_settings[icon]" value="<?php echo esc_html($icon) ?>" class="navify-menu-icon" />
                        <?php 
                        
                        if($icon_type == 'custom-icon'){                             
                            $attachment = get_post($icon);
                            ?>                            
                            <span class="dashicons dashicons-trash remove-custom-icon" data-custom-icon-index="<?php echo esc_html($key) ?>"></span> 
                            <img src="<?php echo esc_html($attachment->guid) ?>" alt="<?php echo esc_html($attachment->post_title) ?>" />
                        <?php } else { ?>
                            <span class="<?php echo esc_html($icon) ?>"></span>
                        <?php } ?>
                        
                    </div>
                <?php } ?>
            </div>
          
        </div>
        <?php       
        die();
    }

     /**
     * Handel to delete custom icon form the list
     *
     * @package Mega Navify
     * @since 1.0
     */
    public function meganavify_remove_custom_icon(){

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

       $iconIndex = isset($_POST['iconIndex']) ? sanitize_text_field(wp_unslash($_POST['iconIndex'])) : '';    
        
        $meganavify_icons = get_option(MEGANAVIFY_PREFIX.'_icons');
        $meganavify_icons = json_decode($meganavify_icons,true);        
        
        if( !empty($meganavify_icons['custom-icon']) && isset($meganavify_icons['custom-icon'][$iconIndex])){
            unset($meganavify_icons['custom-icon'][$iconIndex]);
        }        

        $meganavify_icons['custom-icon'] = array_unique($meganavify_icons['custom-icon']);       
       
        update_option(MEGANAVIFY_PREFIX.'_icons',wp_json_encode($meganavify_icons));
    
        wp_send_json_success();
    }

    /**
	 * Adding Hooks
	 *
	 * Adding hooks for the popup functionalities.
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
	function meganavify_add_hooks(){		
		add_action('admin_footer',array($this,'meganavify_admin_navifymenu_popop'));

        add_action('wp_ajax_meganavify_get_grid_system', array($this,'meganavify_get_grid_system'));
        add_action('wp_ajax_nopriv_meganavify_get_grid_system', array($this,'meganavify_get_grid_system'));

        add_action('wp_ajax_meganavify_get_menu_item_settings', array($this,'meganavify_get_menu_item_settings'));
        add_action('wp_ajax_nopriv_meganavify_get_menu_item_settings', array($this,'meganavify_get_menu_item_settings'));

        add_action( 'wp_ajax_meganavify_save_menu_item_settings', array($this,'meganavify_save_menu_item_settings'));
        add_action( 'wp_ajax_nopriv_meganavify_save_menu_item_settings', array($this,'meganavify_save_menu_item_settings'));
        
        add_action( 'wp_ajax_meganavify_get_active_tab', array($this,'meganavify_get_active_tab'));
        add_action( 'wp_ajax_nopriv_meganavify_get_active_tab', array($this,'meganavify_get_active_tab'));
        
        add_action( 'wp_ajax_meganavify_save_get_tab_content', array($this,'meganavify_save_get_tab_content'));
        add_action( 'wp_ajax_nopriv_meganavify_save_get_tab_content', array($this,'meganavify_save_get_tab_content'));
       
        add_action( 'wp_ajax_meganavify_update_menu_icon', array($this,'meganavify_update_menu_icon'));
        add_action( 'wp_ajax_nopriv_meganavify_update_menu_icon', array($this,'meganavify_update_menu_icon'));
        
        add_action( 'wp_ajax_meganavify_get_lib_icons', array($this,'meganavify_get_lib_icons'));
        add_action( 'wp_ajax_nopriv_meganavify_get_lib_icons', array($this,'meganavify_get_lib_icons'));
        
        add_action( 'wp_ajax_meganavify_remove_custom_icon', array($this,'meganavify_remove_custom_icon'));
        add_action( 'wp_ajax_nopriv_meganavify_remove_custom_icon', array($this,'meganavify_remove_custom_icon'));
	}
}