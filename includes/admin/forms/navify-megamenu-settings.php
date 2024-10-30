<?php 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

$item_settings = get_post_meta($menu_item_id, MEGANAVIFY_PREFIX.'item_settings', true);

if( !empty($item_settings )){
    $item_settings  = unserialize($item_settings );
}

$hide_text          = isset($item_settings['hide_text']) ? $item_settings['hide_text'] : '';
$hide_arrow         = isset($item_settings['hide_arrow']) ? $item_settings['hide_arrow'] : '';
$disable_link       = isset($item_settings['disable_link']) ? $item_settings['disable_link'] : '';
$hide_on_mobile     = isset($item_settings['hide_on_mobile']) ? $item_settings['hide_on_mobile'] : '';
$hide_on_desktop    = isset($item_settings['hide_on_desktop']) ? $item_settings['hide_on_desktop'] : '';
$icon_position      = isset($item_settings['icon_position']) ? $item_settings['icon_position'] : '';

?>
<div id="navify-menu-settings" class="tabcontent">
    <div class="navify_content general_settings">
        <form id="navify_menu_settings_form" method="post">        
            <div class="navify-menu-settings-form-message"></div>
            <input type="hidden" name="menu_item_id" class="menu_item_id" value="<?php echo esc_html( $menu_item_id ) ?>" />
            <input type="hidden" name="action" value="meganavify_save_menu_item_settings" />
            <input type="hidden" name="_wpnonce" value="<?php echo esc_attr(wp_create_nonce('navify_menu_item_settings_nonce')); ?>" />
            <h3><?php esc_html_e('Menu Item Settings', 'mega-navify'); ?></h3>
            <table>
                <tbody>
                    <tr>
                        <td class="navify-name"><?php esc_html_e('Hide text', 'mega-navify'); ?></td>
                        <td class="navify-value"><input type="checkbox" <?php echo checked($hide_text,'true') ?> name="navify_item_settings[hide_text]" value="true" /></td>
                    </tr>                    
                    <tr>
                        <td class="navify-name"><?php esc_html_e('Disable link', 'mega-navify'); ?></td>
                        <td class="navify-value"><input type="checkbox" <?php echo checked($disable_link,'true') ?> name="navify_item_settings[disable_link]" value="true" /></td>
                    </tr>
                    <tr>
                        <td class="navify-name"><?php esc_html_e('Hide item on mobile', 'mega-navify'); ?></td>
                        <td class="navify-value"><input type="checkbox" <?php echo checked($hide_on_mobile,'true') ?> name="navify_item_settings[hide_on_mobile]" value="true" /></td>
                    </tr>
                    <tr>
                        <td class="navify-name"><?php esc_html_e('Hide item on desktop', 'mega-navify'); ?></td>
                        <td class="navify-value"><input type="checkbox"  <?php echo checked($hide_on_desktop,'true') ?> name="navify_item_settings[hide_on_desktop]" value="true" /></td>
                    </tr>                    
                    <tr>
                        <td class="navify-name"><?php esc_html_e('Icon position', 'mega-navify'); ?></td>
                        <td class="navify-value">
                            <select name="navify_item_settings[icon_position]">
                                <option <?php echo selected($icon_position,'left') ?> value="left" selected="selected"><?php esc_html_e('Left', 'mega-navify'); ?></option>
                                <option <?php echo selected($icon_position,'top') ?>  value="top"><?php esc_html_e('Top', 'mega-navify'); ?></option>
                                <option <?php echo selected($icon_position,'right') ?>  value="right"><?php esc_html_e('Right', 'mega-navify'); ?></option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit"><input type="submit" name="submit" class="button button-primary button-large" value="<?php esc_attr_e('Save Changes', 'mega-navify'); ?>" /></p>
        </form>
    </div>
</div>