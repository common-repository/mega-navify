<?php 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

$item_settings = meganavify_get_item_settings($menu_item_id);
$item_icon = isset($item_settings['item_icon']) ? $item_settings['item_icon'] : '';
?>
<div id="navify-menu-icons" class="tabcontent">
    <div class="navify-icon-wrap">
        <div class="icon-container">
            <ul class="navify-icon-tab-wrap">
                <li class="navify-tab-item active" data-tab="dashicons"><?php esc_html_e('Dashicons','mega-navify') ?> </li>
                <li class="navify-tab-item" data-tab="font-awesome"><?php esc_html_e('Font Awesome','mega-navify') ?></li>
                <li class="navify-tab-item" data-tab="custom-icon"><?php esc_html_e('Custom Icon','mega-navify') ?></li>
            </ul>
        </div>
        <div class="navify-icon-tab-content"></div>
    </div>
</div>
