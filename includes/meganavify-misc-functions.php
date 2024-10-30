<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Handle to get column count of the row
 * 
 * @package Mega Navify
 * @since 1.0
 */
if(!function_exists('meganavify_get_row_column_counts')){    
    function meganavify_get_row_column_counts($columns){
        $count = 0;        
       // Loop through the array to find elements with 'no_of_columns' equal to $desiredNoOfColumns
        foreach ($columns['columns'] as $item) {
            if (isset($item['no_of_columns'])) {
                $count += $item['no_of_columns'];
            }
        }
        return $count;
    }
}

/**
 * Handle to get menu grid data
 * 
 * @package Mega Navify
 * @since 1.0
 */
if(!function_exists('meganavify_get_menu_setting_by_menu_id')){
    function meganavify_get_menu_setting_by_menu_id($menu_id){
      
        if(!$menu_id){
            return false;
        }

        $item_settings = get_post_meta($menu_id, MEGANAVIFY_PREFIX.'grid_system', true);

        $item_settings = json_decode($item_settings, true);
        
        return $item_settings;
    }
}

/**
 * Return list of rows in menu item
 * 
 * @package Mega Navify
 * @since 1.0
 */
if(!function_exists('meganavify_get_menu_item_rows')){
    function meganavify_get_menu_item_rows($item_settings){
      
        if(!$item_settings){
            return false;
        }       

        $rows = isset($item_settings['rows']) ? $item_settings['rows'] : array();
        return $rows;     
    }
}

/**
 * Return list of column in menu item
 * 
 * @package Mega Navify
 * @since 1.0
 */
if(!function_exists('meganavify_get_columns_by_item_row')){
    function meganavify_get_columns_by_item_row($item_rows){
      
        if(!$item_rows){
            return false;
        }       

        $columns = isset($item_rows['columns']) ? $item_rows['columns'] : array();
        return $columns;     
    }
}

/**
 * Return list of column items like widgets
 * 
 * @package Mega Navify
 * @since 1.0
 */
if(!function_exists('meganavify_get_columns_items')){
    function meganavify_get_columns_items($column){
      
        if(!$column){
            return false;
        }       

        $columns = isset($column['column_items']) ? $column['column_items'] : array();
        return $columns;     
    }
}


/**
 * Return settings of the menu item
 * 
 * @package Mega Navify
 * @since 1.0
 */
if(!function_exists('meganavify_get_item_settings')){
    function meganavify_get_item_settings($item_id){
      
        if(!$item_id){
            return false;
        }       

        $item_settings = get_post_meta($item_id,MEGANAVIFY_PREFIX.'item_settings',true);
        return unserialize($item_settings);     
    }
}

/**
 * Return the locations that a specific menu ID has been tagged to.
 *
 * @package Mega Navify
 * @since 1.0
*/
if(!function_exists('meganavify_get_tagged_theme_locations_for_menu_id')){    
   
    function meganavify_get_tagged_theme_locations_for_menu_id( $menu_id ) {

        $locations = array();

        $nav_menu_locations = get_nav_menu_locations();

        foreach ( get_registered_nav_menus() as $id => $name ) {

            if ( isset( $nav_menu_locations[ $id ] ) && $nav_menu_locations[ $id ] == $menu_id ) {
                $locations[ $id ] = $name;
            }
        }

        return $locations;
    }

}
if(!function_exists('meganavify_sanitize_array_recursive')){    
    function meganavify_sanitize_array_recursive($array) {
        if (!is_array($array)) {
            return sanitize_text_field(wp_unslash($array));
        }

        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = meganavify_sanitize_array_recursive($value);
            } else {
                $value = sanitize_text_field(wp_unslash($value));
            }
        }

        return $array;
    }
}



/**
 * Recursively sanitizes an array containing mixed data types.
 *
 * This function iterates through each element of the given array,
 * applying appropriate sanitization based on the data type.
 * It handles nested arrays by recursively calling itself.
 *
 * @param array|mixed $array The array to be sanitized (or a single value)
 * @return array|mixed The sanitized array (or single sanitized value) 
 * @package Mega Navify
 * @since 1.0
 */
function meganavify_sanitize_mixed_array($array) {   
    // If the input is not an array, treat it as a single value
    if (!is_array($array)) {
        return meganavify_sanitize_mixed_value($array);
    }

    // Iterate through each element of the array
    foreach ($array as $key => $value) {
        // If the element is an array, recursively sanitize it
        // Otherwise, sanitize it as a single value
        $array[$key] = is_array($value) ? meganavify_sanitize_mixed_array($value) : meganavify_sanitize_mixed_value($value);
    }

    
    return $array;
}

/**
 * Sanitizes a single value based on its data type.
 *
 * This function determines the type of the input value and applies
 * the appropriate sanitization method. It currently handles numbers,
 * URLs, and text (as a fallback for other types).
 *
 * @param mixed $value The value to be sanitized
 * @return mixed The sanitized value 
 * @package Mega Navify
 * @since 1.0
 */
function meganavify_sanitize_mixed_value($value) {
    // Check if the value is numeric (integer or float)
    if (is_numeric($value)) {
        // Convert to float to handle both integers and floats
        return floatval($value);
    } 
    // Check if the value is a valid URL
    elseif (filter_var($value, FILTER_VALIDATE_URL)) {
        // Use WordPress's built-in function to sanitize URLs
        return esc_url_raw($value);
    } 
    // For all other types, treat as text
    else {
        // Use WordPress's built-in function to sanitize text fields
        return sanitize_text_field($value);
    }
}