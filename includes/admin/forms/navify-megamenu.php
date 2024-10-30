<?php 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $meganavify_menu_widget;
$event = '';
$widgets = $meganavify_menu_widget->meganavify_get_available_widgets();
?>
<div id="navify-menu" class="tabcontent">
    <div class="meganavify-model-header-container">
        <label for="navify_menu_display_mode"><?php esc_html_e('Sub menu display mode', 'mega-navify'); ?></label>
        <select name="display_mode" id="navify_menu_display_mode">    
            <option value="flyout" <?php selected( $event, 'flyout'); ?>><?php esc_html_e( 'Flyout Menu', 'mega-navify' ) ?></option>
            <option value="grid" <?php selected( $event, 'grid'); ?>><?php esc_html_e( 'Mega Menu - Grid Layout', 'mega-navify' ) ?></option>
        </select>

        <select id="panel_widgets" style="display: none;">
            <option value=""><?php esc_html_e( 'Select a Widget to add to the panel', 'mega-navify' ) ?></option>
            <?php 
                foreach ($widgets as $widget) {
                    echo '<option value="' . esc_attr($widget['value']) . '">' . esc_html($widget['text']) . '</option>';
                }
            ?>
        </select>    
    </div>

    <div class="navify-model-errors"></div>   

    <div class="navfify-grid-wrapper">
        <div id="navify_grid_result"></div>
        <div class="row-button-wrapper" style="display: none;">
            <button type="button" class="button-primary navify-add-row"><?php esc_html_e( 'Add Row', 'mega-navify' ) ?></button>
        </div>
    </div>
</div>