<?php 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap">
   <h1><?php esc_html_e('Menu Locations','mega-navify') ?></h1>
   <p><?php esc_html_e('This is an overview of the menu locations supported by your theme.','mega-navify') ?></p>
   <p><?php esc_html_e('Use these options to enable Max Mega Menu and define the behaviour of each menu location.','mega-navify') ?></p>

   <form method="post">      
      <table class="form-table" role="presentation">
         <tbody>
            <tr>
               <th><label for="blogname"><?php esc_html_e('Menu Locations','mega-navify') ?></label></th>
               <td>
                <?php 
                    $theme_locations  = get_registered_nav_menus();
                    $tagged_menu_locations = meganavify_get_tagged_theme_locations_for_menu_id($menu_id);
                    if( empty($theme_locations)){
                        echo '<p>' . esc_html__( 'There are currently no menu locations registered by your theme.', 'mega-navify') . '</p>';
                        echo '<p>' . esc_html__( 'Go to MegaNavify menu to create a new menu location.', 'mega-navify') . '</p>';
                        echo '<p>' . esc_html__( 'Then use the Meganavify block or shortcode to output the menu location on your site.', 'mega-navify') . '</p>';
                    }
                    else {
                        $i  = 1;

                        echo ' <div class="accordion">';                        
                       
                        foreach ( $theme_locations as $location => $name ) {
                            $i++;
                            $is_enabled_class = '';
                            ?>
                            <div class="accordion-item" id="<?php echo esc_html($location) ?>">
                                <div class="accordion-header">
                                    <h4><span class='dashicons dashicons-location'></span><?php echo esc_html( $name ); ?></h4>
                                </div>
                                <div class="accordion-content">
                                    <?php
                                        $location_from_get = isset($_GET['location']) ? sanitize_text_field(wp_unslash($_GET['location'])) : '';
                                                                                
                                        // Sanitize the '_wpnonce' and 'location' parameters from the GET request
                                        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
                                        $location_from_get = isset($_GET['location']) ? sanitize_text_field(wp_unslash($_GET['location'])) : '';
                                        
                                        // Check nonce and sanitized location
                                        if ( $nonce && wp_verify_nonce($nonce, 'navify_location_updated') && $location_from_get === $location ) {
                                            ?>
                                            <div class="notice notice-success is-dismissible"> 
                                                <p><?php echo esc_html__('Location Updated', 'mega-navify'); ?></p>				                                
                                            </div>
                                            <?php
                                        }
                                        

                                        if(has_nav_menu( $location )){
                                            $this->meganavify_show_menu_locations_options($location);
                                        }
                                        else{
                                            echo "<p class='mega-warning'><span class='dashicons dashicons-warning'></span>";
                                            echo " <a href='" . esc_url( admin_url( 'nav-menus.php?action=locations' ) ) . "'>" . esc_html__( 'Assign a menu', 'mega-navify' ) . '</a> ';
                                            echo esc_html__( 'to this location to enable these options.', 'mega-navify' );
                                            echo '</p>';
                                        }
                                    ?>
                                </div>
                            </div>
                            <?php
                            echo ' </div>';
                        }
                    }
                ?>
               </td>
            </tr>
            <tr>
               <th></th>
               <td>
                  <button type="button" class="navify-add-location button button-secondary "><?php esc_html_e('Add New Location','mega-navify') ?></button>
               </td>
            </tr>     
            
            
                     
         </tbody>
      </table>      
   </form>
</div>