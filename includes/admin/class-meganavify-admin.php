<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Class
 *
 * Handles adding backend to the admin pages
 * as well as the front pages.
 *
 * @package Mega Navify
 * @since 1.0
 */
class Meganavify_Admin {

    public $model;

	//class constructor
	function __construct(){
        global $meganavify_model;
        $this->model = $meganavify_model;
	}

    /**
	 * Settings metaobox
	 *
	 * Handles to add metabo for the menu settings
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
    public function meganavify_register_menu_settings_meta_boxes(){
        add_meta_box('meganavify-menu-settings', esc_html__('MegaNavify Settings', 'mega-navify'), array($this,'meganavify_menu_settings_form'), 'nav-menus', 'side', 'high');      
    }
	
    /**
	 * Settings fields
	 *
	 * Handles to render the fields for the menu settings
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
    public function meganavify_menu_settings_form(){

        $menu_id = $this->meganavify_get_selected_menu_id();

        $tagged_menu_locations = $this->meganavify_get_tagged_theme_locations_for_menu_id($menu_id);               
        
        $theme_locations  = get_registered_nav_menus();

        if ( ! count( $theme_locations ) ) {

            $location_page_url =  add_query_arg( 'page', 'meganavify-settings',admin_url() );
            echo "<div>";
            echo '<p>' . esc_html__( 'There are currently no menu locations registered by your theme.', 'mega-navify' ) . '</p>';
            echo '<p>' . esc_html__( 'Go to MegaNavify menu to create a new menu location.', 'mega-navify' ) . '</p>';            
            echo '<p>' . esc_html__( 'Then use the Meganavify block,  shortcode to output the menu location on your site.', 'mega-navify' ) . '</p>';
            echo "</div>";
        } elseif ( ! count( $tagged_menu_locations ) ) {
            echo "<div>";
            echo '<p>' . esc_html__( 'Please assign this menu to a theme location to enable the Mega Menu settings.', 'mega-navify' ) . '</p>';
            echo '<p>' . esc_html__( "To assign this menu to a theme location, scroll to the bottom of this page and tag the menu to a 'Display location'.", 'mega-navify' ) . '</p>';
            echo "</div>";
        }
        else{ ?>        
            <div class="meganavify-accordion">
                <?php
                foreach ( $theme_locations as $location => $name ) { 

                    if( !in_array($location, array_keys($tagged_menu_locations)) ){
                        continue;
                    } 
                    ?>
                    <div class="meganavify-accordion-item">
                        <div class="meganavify-accordion-item-header"><?php echo esc_html($name) ?></div>
                        <div class="meganavify-accordion-item-body meganavify-collapsed">
                            <?php  $this->meganavify_get_menu_settings($location); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php
        }
    }    
    

     /**
	 * Settings fields
	 *
	 * Handles to get menu settings
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
    public function meganavify_get_menu_settings($theme_location = ''){
        if( empty($theme_location) ){
            return;
        }

        $options = get_option(MEGANAVIFY_PREFIX.'_options');

        //$event  = isset($options[$theme_location]['event']) ? $options[$theme_location]['event'] : '';
        $enable = isset($options[$theme_location]['enable']) ? $options[$theme_location]['enable'] : '';
        $desk_effect = isset($options[$theme_location]['desk_effect']) ? $options[$theme_location]['desk_effect'] : '';
        $desk_speed = isset($options[$theme_location]['desk_speed']) ? $options[$theme_location]['desk_speed'] : '';
        $mobile_effect = isset($options[$theme_location]['mobile_effect']) ? $options[$theme_location]['mobile_effect'] : '';
        $mobile_speed = isset($options[$theme_location]['mobile_speed']) ? $options[$theme_location]['mobile_speed'] : '';       
        ?>
            <div id="custom-menu-dropdown" class="posttypediv">
                <ul id="custom-dropdown-checklist" class="categorychecklist form-no-clear">
                    <li>
                        <label class="menu-item-title">
                            <?php esc_html_e('Enable MegaNavify', 'mega-navify') ?>
                        </label>
                        <div class="form-group">
                            <input type="checkbox" value="1" class="menu-item-checkbox" name="meganavify_options[<?php echo esc_html($theme_location) ?>][enable]" <?php checked($enable,1); ?>> 
                        </div>
                    </li>
                    <li>
                    <label class="menu-item-title">
                            <?php esc_html_e('Effect', 'mega-navify') ?>
                        </label>
                        <div class="form-group">
                            <select name="meganavify_options[<?php echo esc_html($theme_location) ?>][desk_effect]">
                                <option  <?php selected( $desk_effect, 'fade', true ) ?> value="fade"><?php esc_attr_e('Fade', 'mega-navify'); ?> </option>
                                <option  <?php selected( $desk_effect, 'fade_up', true ) ?> value="fade_up"><?php esc_attr_e('Fade Up', 'mega-navify'); ?> </option>
                                <option  <?php selected( $desk_effect, 'slide', true ) ?> value="slide"><?php esc_attr_e('Slide', 'mega-navify'); ?> </option>
                                <option  <?php selected( $desk_effect, 'slide_up', true ) ?> value="slide_up"><?php esc_attr_e('Slide Up', 'mega-navify'); ?> </option>
                            </select>   
                            <select name="meganavify_options[<?php echo esc_html($theme_location) ?>][desk_speed]">
                                <option  <?php selected( $desk_speed, 'fast', true ) ?> value="fast"><?php esc_attr_e('Fast', 'mega-navify'); ?> </option>
                                <option  <?php selected( $desk_speed, 'medium', true ) ?> value="medium"><?php esc_attr_e('Medium', 'mega-navify'); ?> </option>
                                <option  <?php selected( $desk_speed, 'slow', true ) ?> value="slow"><?php esc_attr_e('Slow', 'mega-navify'); ?> </option>
                            </select>
                        </div>
                    </li> 
                    
                    <li>
                    <label class="menu-item-title">
                            <?php esc_html_e('Mobile Effect', 'mega-navify') ?>
                        </label>
                        <div class="form-group">
                            <select name="meganavify_options[<?php echo esc_html($theme_location) ?>][mobile_effect]">
                                <option  <?php selected( $mobile_effect, 'slide_down', true ) ?> value="slide_down"><?php esc_attr_e('Slide Down', 'mega-navify'); ?> </option>
                                <option  <?php selected( $mobile_effect, 'slide_left', true ) ?> value="slide_left"><?php esc_attr_e('Slide Left', 'mega-navify'); ?> </option>
                                <option  <?php selected( $mobile_effect, 'slide_right', true ) ?> value="slide_right"><?php esc_attr_e('Slide Right', 'mega-navify'); ?> </option>
                            </select>   
                            <select name="meganavify_options[<?php echo esc_html($theme_location) ?>][mobile_speed]">
                                <option  <?php selected( $mobile_speed, 'fast', true ) ?> value="fast"><?php esc_attr_e('Fast', 'mega-navify'); ?> </option>
                                <option  <?php selected( $mobile_speed, 'medium', true ) ?> value="medium"><?php esc_attr_e('Medium', 'mega-navify'); ?> </option>
                                <option  <?php selected( $mobile_speed, 'slow', true ) ?> value="slow"><?php esc_attr_e('Slow', 'mega-navify'); ?> </option>
                            </select>
                        </div>
                    </li> 
                    <li>
                    <div class="meganavify-loader-wraper">
                        <input type="submit" class="button-primary meganavify-menu-save" name="submit" value="<?php esc_attr_e('Save', 'mega-navify'); ?>">
                        <div class="meganavify-loader" style="display: none;"></div>
                    </div>
                    </li>
                </ul>
            </div> 
        <?php
    }

    /**
	 * Save Settings fields
	 *
	 * Handles to save the fields for the menu settings in the options table
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
    public function meganavify_save_settings(){

        $response = array();

        if (!isset($_POST['nonce'])) {
            $response['status'] = 'error';
            $response['message'] = esc_html__('Nonce is nonce', 'mega-navify');
            wp_send_json($response);     
            wp_die();          
        }
        
        $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
        
        if (!wp_verify_nonce($nonce, 'meganavify-ajax-nonce')) {
            $response['status'] = 'error';
            $response['message'] = esc_html__('Invalid nonce', 'mega-navify');
            wp_send_json($response);     
            wp_die();
        }
       

        if ( ! isset( $_POST['meganavify_options'] ) ) {
            $response['status'] = 'error';
            $response['message'] = esc_html__('Something went wrong', 'mega-navify');
            wp_send_json($response);
            wp_die();
        }     
        
        $options = isset($_POST['meganavify_options']) ? meganavify_sanitize_mixed_array(wp_unslash($_POST['meganavify_options'])) : array();

        update_option(MEGANAVIFY_PREFIX.'options', $options);
        $response['status'] = 'success';
        $response['message'] = esc_html__('Settings saved successfully.', 'mega-navify');
        wp_send_json($response);
        wp_die();
    }

    /**
	 * Save Settings fields
	 *
	 * Handles to show location settings
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
    public function meganavify_show_menu_locations_options( $location ){

        $meganavify_options = get_option(MEGANAVIFY_PREFIX.'_options');

        $location_settings = isset($meganavify_options[$location]) ? $meganavify_options[$location] : array(); 
        $enable = isset($location_settings['enable']) ? $location_settings['enable'] :'';        
       
        $desk_effect    = isset($location_settings['desk_effect']) ? $location_settings['desk_effect'] : '';
        $desk_speed     = isset($location_settings['desk_speed']) ? $location_settings['desk_speed'] : '';
        $mobile_effect  = isset($location_settings['mobile_effect']) ? $location_settings['mobile_effect'] : '';
        $mobile_speed   = isset($location_settings['mobile_speed']) ? $location_settings['mobile_speed'] : '';        
       
        $delete_location_url = esc_url(
            add_query_arg(
                array(
                    'action'   => 'meganavify_delete_menu_location',
                    'location' => $location,
                ),
                wp_nonce_url( admin_url( 'admin-post.php' ), 'meganavify_delete_menu_location' )
            )
        );

     

        ?>
        <div id="custom-dropdown-checklist">
            <form method="post" class="location-setting-form">
                <input type="hidden" value="meganavify_save_location_settings" name="action" />
                <input type="hidden" value="<?php echo esc_html($location) ?>" name="locaton" />

                <?php wp_nonce_field( 'navify_save_location_setting_nouce', 'navify_save_location' ); ?>

               <div class="settings-wrapper-accordian">
               <li>
                    <label class="menu-item-title"><?php esc_html_e('Enable MegaNavify','mega-navify') ?></label>
                    <input type="checkbox" value="1" <?php checked('1',$enable) ?>class="menu-item-checkbox" name="meganavify_options[<?php echo esc_html($location) ?>][enable]"> 
                </li>
                <li>
                    <label class="menu-item-title"><?php esc_html_e('Effect','mega-navify') ?></label>
                    <div class="dropdown-wrap">
                        <select name="meganavify_options[<?php echo esc_html($location) ?>][desk_effect]">
                            <option  <?php selected( $desk_effect, 'fade', true ) ?> value="fade"><?php esc_attr_e('Fade', 'mega-navify'); ?> </option>
                            <option  <?php selected( $desk_effect, 'fade_up', true ) ?> value="fade_up"><?php esc_attr_e('Fade Up', 'mega-navify'); ?> </option>
                            <option  <?php selected( $desk_effect, 'slide', true ) ?> value="slide"><?php esc_attr_e('Slide', 'mega-navify'); ?> </option>
                            <option  <?php selected( $desk_effect, 'slide_up', true ) ?> value="slide_up"><?php esc_attr_e('Slide Up', 'mega-navify'); ?> </option>
                        </select>   
                        <select name="meganavify_options[<?php echo esc_html($location) ?>][desk_speed]">
                            <option  <?php selected( $desk_speed, 'fast', true ) ?> value="fast"><?php esc_attr_e('Fast', 'mega-navify'); ?> </option>
                            <option  <?php selected( $desk_speed, 'medium', true ) ?> value="medium"><?php esc_attr_e('Medium', 'mega-navify'); ?> </option>
                            <option  <?php selected( $desk_speed, 'slow', true ) ?> value="slow"><?php esc_attr_e('Slow', 'mega-navify'); ?> </option>
                        </select>
                    </div>
                </li>

                <li>
                    <label class="menu-item-title"><?php esc_html_e('Mobile Effect','mega-navify') ?></label>
                    <div class="dropdown-wrap">
                        <select name="meganavify_options[<?php echo esc_html($location) ?>][mobile_effect]">
                            <option  <?php selected( $mobile_effect, 'slide_down', true ) ?> value="slide_down"><?php esc_attr_e('Slide Down', 'mega-navify'); ?> </option>
                            <option  <?php selected( $mobile_effect, 'slide_left', true ) ?> value="slide_left"><?php esc_attr_e('Slide Left', 'mega-navify'); ?> </option>
                            <option  <?php selected( $mobile_effect, 'slide_right', true ) ?> value="slide_right"><?php esc_attr_e('Slide Right', 'mega-navify'); ?> </option>
                        </select>   
                        <select name="meganavify_options[<?php echo esc_html($location) ?>][mobile_speed]">
                            <option  <?php selected( $mobile_speed, 'fast', true ) ?> value="fast"><?php esc_attr_e('Fast', 'mega-navify'); ?> </option>
                            <option  <?php selected( $mobile_speed, 'medium', true ) ?> value="medium"><?php esc_attr_e('Medium', 'mega-navify'); ?> </option>
                            <option  <?php selected( $mobile_speed, 'slow', true ) ?> value="slow"><?php esc_attr_e('Slow', 'mega-navify'); ?> </option>
                        </select>
                    </div>
                </li>
                <li>
                    <div><input type="submit" value="<?php esc_html_e('Save','mega-navify') ?>" class="button button-primary"></div>
                    <div class="loation-btn-group">
                        <a class="button delete-location-btn" href='<?php echo esc_url($delete_location_url) ?>'><span class='dashicons dashicons-trash'></span><?php  esc_html_e( 'Delete location', 'mega-navify' ) ?></a>
                        <a class="button view-location-btn" href='<?php echo esc_url(admin_url('nav-menus.php')) ?>'><span class='dashicons dashicons-list-view'></span><?php  esc_html_e( 'View location', 'mega-navify' ) ?></a>
                    </div>
                </li>
               </div>
            </form>
        </div>
        <?php      
    }    

    /**
     * Get the current menu ID.
     *
     * Most of this taken from wp-admin/nav-menus.php (no built in functions to do this)
     *
     * @package Mega Navify
	 * @since 1.0
     */
    public function meganavify_get_selected_menu_id() {

        $nav_menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );
        $menu_count = count( $nav_menus );      

        $nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? absint( $_REQUEST['menu'] ) : 0;
        $add_new_screen = ( isset( $_GET['menu'] ) && 0 == absint( $_GET['menu'] ) ) ? true : false;
       
        // If we have one theme location, and zero menus, we take them right into editing their first menu.
        $page_count                  = wp_count_posts( 'page' );
        $one_theme_location_no_menus = ( 1 == count( get_registered_nav_menus() ) && ! $add_new_screen && empty( $nav_menus ) && ! empty( $page_count->publish ) ) ? true : false;

        // Get recently edited nav menu.
        $recently_edited = absint( get_user_option( 'nav_menu_recently_edited' ) );
        if ( empty( $recently_edited ) && is_nav_menu( $nav_menu_selected_id ) ) {
            $recently_edited = $nav_menu_selected_id;
        }

        // Use $recently_edited if none are selected.
        if ( empty( $nav_menu_selected_id ) && ! isset( $_GET['menu'] ) && is_nav_menu( $recently_edited ) ) {
            $nav_menu_selected_id = $recently_edited;
        }

        $action = isset($_GET['action']) ? sanitize_text_field( wp_unslash($_GET['action'])) : '';

        // On deletion of menu, if another menu exists, show it.
        if ( ! $add_new_screen && 0 < $menu_count && 'delete' === $action ) {
            $nav_menu_selected_id = $nav_menus[0]->term_id;
        }

        // Set $nav_menu_selected_id to 0 if no menus.
        if ( $one_theme_location_no_menus ) {
            $nav_menu_selected_id = 0;
        } elseif ( empty( $nav_menu_selected_id ) && ! empty( $nav_menus ) && ! $add_new_screen ) {
            // if we have no selection yet, and we have menus, set to the first one in the list.
            $nav_menu_selected_id = $nav_menus[0]->term_id;
        }

        return $nav_menu_selected_id;
    }

    /**
     * Return the locations that a specific menu ID has been tagged to.
     *
     * @package Mega Navify
	 * @since 1.0
     */
    public function meganavify_get_tagged_theme_locations_for_menu_id( $menu_id ) {

        $locations = array();
        $nav_menu_locations = get_nav_menu_locations();        
        
        foreach ( get_registered_nav_menus() as $id => $name ) {           
            if ( isset( $nav_menu_locations[ $id ] ) && $nav_menu_locations[ $id ] == $menu_id ) {
                $locations[ $id ] = $name;
            }
        }
        return $locations;
    }   

   
    /**
     *  Create our own widget area to store all mega menu widgets.
     *
     * @package Mega Navify
	 * @since 1.0
    */
    public function meganavify_register_sidebar() {        
        register_sidebar(
            array(
                'id'          => MEGANAVIFY_SIDEBAR_ID,
                'name'        => __( 'Mega Navify test Widgets', 'mega-navify' ),
                'description' => __( 'This is where Mega Navify Menu stores widgets that you have added to sub menus using the mega menu builder. You can edit existing widgets here, but new widgets must be added through the Mega Menu interface (under Appearance > Menus).', 'mega-navify' ),
            )
        );
    }
   
    /**
	 * Register a settings
	 *
	 * Register a  meganavify settings page
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
    public function meganavify_register_menu_setting_page(){
        add_menu_page(
            esc_html__( 'MegaNavify', 'mega-navify' ),
            'MegaNavify',
            'manage_options',
            'meganavify-settings',
            array($this,'meganavify_render_menu_setting_page'),
            MEGANAVIFY_URL .'includes/images/logo.png'
        );

        add_submenu_page( 
            'meganavify-settings',
            'General Settings',
            'General Settings',
            'manage_options',
            'navify-general-settings',
            array($this,'meganavify_render_general_setting_page')            
        );       
    }    

    /**
     * Funtion to render location setting page     
     *
	 * @package Mega Navify
	 * @since 1.0
     */
    public function meganavify_render_menu_setting_page(){
        $menu_id = $this->meganavify_get_selected_menu_id();  
        require MEGANAVIFY_ADMIN_DIR.'/forms/navify-megamenu-location-settings.php';
    }

    /**
     * Funtion to render general setting page     
     *
	 * @package Mega Navify
	 * @since 1.0
     */
    public function meganavify_render_general_setting_page(){

        $meganavify_responsive_breakpoint = get_option(MEGANAVIFY_PREFIX.'responsive_breakpoint');
        ?>
        <form method="post">      
            <input type="hidden" name="_wpnonce" value="<?php echo esc_attr(wp_create_nonce('navify_general_settings_nonce')); ?>" />
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <td>
                            <h2><?php esc_html_e('General Settings','mega-navify') ?></h2>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Responsive Breakpoint','mega-navify') ?></th>
                        <td>
                            <input type="number" name="meganavify_responsive_breakpoint" value="<?php echo esc_html($meganavify_responsive_breakpoint) ?>">
                           <p><?php esc_html_e('The menu will be converted to a mobile menu when the browser width is below this value.','mega-navify') ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="button button-primary" value="<?php esc_html_e('Save Changes','mega-navify') ?>" name="save_navify_general_settings">
                        </td>
                    </tr>  
                </tbody>
            </table>
        </form>
        <?php
    }

    /**
     * Handle to save general settings
     *
     * @since 2.8
     */
    public function meganavify_save_navify_general_settings(){

        if( isset($_POST['save_navify_general_settings'])){
           
            if (!isset($_POST['_wpnonce'])) {
                wp_send_json_error('Nonce is missing');
                return;  
            }
             
            $nonce = sanitize_text_field(wp_unslash($_POST['_wpnonce']));
             
            if (!wp_verify_nonce($nonce, 'navify_general_settings_nonce')) {
                wp_send_json_error('Invalid nonce');
                return;
            }           
           
            $meganavify_responsive_breakpoint = isset($_POST['meganavify_responsive_breakpoint']) ? sanitize_text_field(wp_unslash($_POST['meganavify_responsive_breakpoint'])) : '';
            update_option(MEGANAVIFY_PREFIX.'responsive_breakpoint',$meganavify_responsive_breakpoint);            
        }
    }


    /**
     * Delete a menu location.
     *
     * @since 2.8
     */
    public function meganavify_delete_menu_location() {
        check_admin_referer( 'meganavify_delete_menu_location' );
        
        $locations          = get_option(MEGANAVIFY_PREFIX.'_locations' );        
        $location_to_delete = isset($_GET['location']) ? sanitize_text_field( wp_unslash($_GET['location'])) : '';
        
        if ( isset( $locations[ $location_to_delete ] ) ) {
            unset( $locations[ $location_to_delete ] );
            update_option(MEGANAVIFY_PREFIX.'_locations', $locations );
        }

        $redirect_url = add_query_arg(
            array(
                'page'            => 'meganavify-settings',
                'delete_location' => 'true',
                
            ),
            admin_url( 'admin.php' )
        );

        wp_redirect($redirect_url);
        
    }

    /**
     * Returns the next available menu location ID     
     *
	 * @package Mega Navify
	 * @since 1.0
     */
    public function meganavify_get_next_menu_location_id(){
        $last_id = 0;

        if ( $locations = get_option(MEGANAVIFY_PREFIX.'_locations' ) ) {

            foreach ( $locations as $key => $value ) {
                if ( strpos( $key, 'mega_navify_menu_' ) !== false ) {
                    $parts   = explode( '_', $key );
                    $menu_id = end( $parts );

                    if ( $menu_id > $last_id ) {
                        $last_id = $menu_id;
                    }
                }
            }
        }

        $next_id = $last_id + 1;
        return $next_id;
    }

    /**
     * Register menu locations created within Max Mega Menu.
     *
     *
	 * @package Mega Navify
	 * @since 1.0
     */    
    public function meganavify_register_nav_menus(){
        $locations = get_option(MEGANAVIFY_PREFIX.'_locations' );      
        
        if ( is_array( $locations ) && count( $locations ) ) {
            foreach ( $locations as $key => $val ) {
                register_nav_menu( $key, $val );
            }
        }
    }

    public function meganavify_register_nav_popup(){
        $screen = get_current_screen();

        if( 'toplevel_page_meganavify-settings' === $screen->id ){
            
            $menus = wp_get_nav_menus();

            ?>
            <div id="new-menu-location-popup" class="new-menu-location-popup">
                <div class="new-menu-location-popup-content">
                    <h2><?php esc_html_e('Add Menu Location','mega-navify') ?> </h2>
                   
                    <form method="post" id="add_new_menu_location">
                        <input type="hidden" name="action" value="meganavify_register_menu_location">
                        <?php wp_nonce_field( 'meganavify_register_menu_location_nonce', 'meganavify_register_menu_location' ); ?>

                        <div class="input-group">
                            <label> <?php esc_html_e('Location Name','mega-navify') ?> </label>
                            <input type="text" class="regular-text" name="locaton_name" id="locaton_name">
                            <p> <?php esc_html_e('Give the location a name that describes where the menu will be displayed on your site.','mega-navify') ?> </p>
                        </div>
                       
                        <div class="input-group">
                            <label><?php esc_html_e('Assign a menu','mega-navify') ?> </label>
                            <?php 
                            $menus = wp_get_nav_menus();

                            if ( count( $menus ) ) {
                               foreach ( $menus as $menu ) {
                                   echo '<div><input type="radio" id="' . esc_attr( $menu->slug ) . '" name="assigned_locatrion" value="' . esc_attr( $menu->term_id ) . '" /><label for="' . esc_attr( $menu->slug ) . '">' . esc_attr( $menu->name ) . '</label></div>';
                               }
                            }

                           echo '<div><input checked="checked" type="radio" id="0" name="assigned_locatrion" value="0" /><label for="0">' . esc_html__( "Skip - I'll assign a menu later", 'mega-navify' ) . '</label></div>';
                        ?>
                            
                            <p><?php esc_html_e('Select a menu to be assigned to this location. This can be changed later using the Appearance > Menus > Manage Location page.','mega-navify') ?> </p> 
                        </div>
                        
                        <button type="submit" name="add_new_menu_location" class="button button-primary"><?php esc_html_e('Add Menu Location','mega-navify') ?></button>
                    </form>
                </div>
            </div>
            <?php
        }
    }

     /**
	 * Save Settings fields
	 *
	 * Handles to register menu location
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
    public function meganavify_register_menu_location(){

        if (isset($_POST['meganavify_register_menu_location'])) {
            
            $nonce = sanitize_text_field(wp_unslash($_POST['meganavify_register_menu_location']));
            
            if (wp_verify_nonce($nonce, 'meganavify_register_menu_location_nonce')) {            

                $location_name = isset($_POST['locaton_name']) ? sanitize_text_field(wp_unslash($_POST['locaton_name'])) : "";
                $locations = get_option(MEGANAVIFY_PREFIX . '_locations');
                $next_id = $this->meganavify_get_next_menu_location_id();
        
                $new_menu_location_id = 'mega_navify_menu_' . $next_id;

                $title = 'MegaNavity Location ' . $next_id;

                if ( !empty($location_name) ) {
                    $title = esc_attr( wp_unslash( $location_name ) );
                }

                $locations[ $new_menu_location_id ] = esc_attr( $title );
                update_option(MEGANAVIFY_PREFIX.'_locations', $locations );

                // Set the location to menu
                $menu_id = 0;
                if ( isset( $_POST['assigned_locatrion'] ) ) {
                    $menu_id = absint( $_POST['assigned_locatrion'] );
                }
                if ( $menu_id > 0 ) {
                    $menu_locations = get_theme_mod( 'nav_menu_locations' );
                    $menu_locations[ $new_menu_location_id ] = $menu_id;           
                    set_theme_mod( 'nav_menu_locations', $menu_locations );
                }        

            } else {
                // Handle invalid nonce
                wp_die('Security check failed');
            }
        } else {
            // Handle case where nonce is not set
            wp_die('Nonce is missing');
        }
    }

    /**
	 * Save Settings fields
	 *
	 * Handles to save menu location options
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
    public function meganavify_save_location_settings(){       
        
        if (  isset( $_POST['navify_save_location'] ) &&
              wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['navify_save_location'])), 'navify_save_location_setting_nouce' ) 
        ) {
           
            if( isset($_POST['action']) && $_POST['action'] == 'meganavify_save_location_settings'){
                $meganavify_settings = get_option(MEGANAVIFY_PREFIX.'_options', array());
                $locaton = isset($_POST['locaton']) ? sanitize_text_field( wp_unslash($_POST['locaton'])) : '';
                
                $meganavify_options = isset($_POST['meganavify_options']) ?  meganavify_sanitize_mixed_array(wp_unslash($_POST['meganavify_options'])) : array();
    
                if( !empty( $locaton ) && !empty( $meganavify_options ) ){
                    $meganavify_settings[$locaton] = $meganavify_options[$locaton];
                    update_option(MEGANAVIFY_PREFIX.'_options', $meganavify_settings);
                }

                $nonce = wp_create_nonce('navify_location_updated');

                $url = add_query_arg(
                    array(
                        'page' => 'meganavify-settings',
                        'location' => $locaton,
                        '_wpnonce' => $nonce
                    ),
        
                    admin_url('admin.php')
                );
                wp_redirect($url);
               die();
            }     
        }
    }

    /**
	 * Adding Hooks
	 *
	 * Adding hooks for the backend functionalities.
	 *
	 * @package Mega Navify
	 * @since 1.0
	 */
	function meganavify_add_hooks(){
		add_action('admin_init', array($this,'meganavify_register_menu_settings_meta_boxes'));

        add_action('wp_ajax_meganavify_save_settings', array($this,'meganavify_save_settings'));
        add_action('wp_ajax_nopriv_meganavify_save_settings', array($this,'meganavify_save_settings'));        
        add_action('init', array($this,'meganavify_register_sidebar'));
        
        add_action( 'admin_menu', array($this,'meganavify_register_menu_setting_page') );

        add_action( 'after_setup_theme', array( $this, 'meganavify_register_nav_menus' ) );
        add_action( 'admin_footer', array( $this, 'meganavify_register_nav_popup' ) );

        add_action('wp_ajax_meganavify_register_menu_location', array($this,'meganavify_register_menu_location'));
        add_action('wp_ajax_nopriv_meganavify_register_menu_location', array($this,'meganavify_register_menu_location'));        
       
        add_action('wp_ajax_meganavify_save_location_settings', array($this,'meganavify_save_location_settings'));
        add_action('wp_ajax_nopriv_meganavify_save_location_settings', array($this,'meganavify_save_location_settings'));        
        add_action('admin_init', array($this,'meganavify_save_location_settings'));        
        add_action('admin_init', array($this,'meganavify_save_navify_general_settings'));       
        
        
        add_action( 'admin_post_meganavify_delete_menu_location', array( $this, 'meganavify_delete_menu_location' ) );
	}
}