<?php
/*
Plugin Name: Showcase Social Media (icons)
License: GPLv2 https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Description: Show your website's social media icons via shortcode.
Version: 1.0.0
Author: KNEET
Author URI: https://kneet.be/
*/

if (!defined('ABSPATH')) {
    exit;
}

function ssmi_plugin_menu() {
    add_menu_page(
        'Social Media Icons Settings',
        'Social Icons',
        'manage_options',
        'ssmi-social-media-icons-plugin',
        'ssmi_plugin_settings_page',
        'dashicons-share'
    );
}
add_action('admin_menu', 'ssmi_plugin_menu');

function ssmi_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h2>Social Media Icons Settings</h2>
        <form action="options.php" method="post">
            <?php
            settings_fields('ssmi-social-media-icons-plugin');
            do_settings_sections('ssmi-social-media-icons-plugin');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function ssmi_plugin_settings_init() {
    register_setting('ssmi-social-media-icons-plugin', 'ssmi_plugin_settings');

    add_settings_section(
        'ssmi_plugin_icon_settings_section',
        'Icon Display Settings',
        'ssmi_plugin_icon_settings_section_cb',
        'ssmi-social-media-icons-plugin'
    );

  add_settings_field(
    'icon_order',
    'Icon Order',
    'ssmi_plugin_field_icon_order_cb',
    'ssmi-social-media-icons-plugin',
    'ssmi_plugin_icon_settings_section',
    [
        'label_for' => 'icon_order',
        'description' => 'Enter the comma-separated list of social media identifiers (these can be found in the list below) in the order you want them to appear. Example: facebook,twitter,instagram,google-drive',
    ]
);

    add_settings_field(
        'icon_size',
        'Icon Size',
        'ssmi_plugin_field_icon_size_cb',
        'ssmi-social-media-icons-plugin',
        'ssmi_plugin_icon_settings_section',
        [
            'label_for' => 'icon_size',
            'type' => 'number',
            'name' => 'icon_size',
            'value' => '55',
            'min' => '0',
            'max' => '512',
            'description' => 'Set the size of the social media icons in pixels.',
        ]
    );

    add_settings_field(
        'icon_spacing',
        'Icon Spacing',
        'ssmi_plugin_field_icon_spacing_cb',
        'ssmi-social-media-icons-plugin',
        'ssmi_plugin_icon_settings_section',
        [
            'label_for' => 'icon_spacing',
            'type' => 'number',
            'name' => 'icon_spacing',
            'value' => '1',
            'min' => '-15',
            'max' => '45',
            'description' => 'Set the horizontal space between the icons in pixels. You can use negative spacing too. This can be handy for option 1 or 2 of Icon Style.',
        ]
    );

    add_settings_field(
        'icon_style',
        'Icon Style',
        'ssmi_plugin_field_icon_style_cb',
        'ssmi-social-media-icons-plugin',
        'ssmi_plugin_icon_settings_section',
        [
            'label_for' => 'icon_style',
            'description' => 'Select your desired style for the icons.',
        ]
    );

    $social_media_options = [
    'behance' => 'Behance (behance)',
    'dribbble' => 'Dribbble (dribble)',
    'discord' => 'Discord (discord)',
    'dropbox' => 'Dropbox (dropbox)',
    'facebook' => 'Facebook (facebook)',
    'flickr' => 'Flickr (flickr)',
    'google-drive' => 'Google Drive (google-drive)',
    'instagram' => 'Instagram (instagram)',
    'kik' => 'Kik (kik)',
    'line' => 'Line (line)',
    'linkedin' => 'LinkedIn (linkedin)',
    'messenger' => 'Messenger (messenger)',
    'patreon' => 'Patreon (patreon)',
    'paypal' => 'PayPal (paypal)',
    'pinterest' => 'Pinterest (pinterest)',
    'reddit' => 'Reddit (reddit)',
    'skype' => 'Skype (skype)',
    'snapchat' => 'Snapchat (snapchat)',
    'spotify' => 'Spotify (spotify)',
    'threads' => 'Threads (threads)',
    'tiktok' => 'TikTok (tiktok)',
    'twitch' => 'Twitch (twitch)',
    'twitter' => 'Twitter (twitter)',
    'vimeo' => 'Vimeo (vimeo)',
    'whatsapp' => 'WhatsApp (whatsapp)',
    'wordpress' => 'WordPress (wordpress)',
    'youtube' => 'YouTube (youtube)'
];


    add_settings_section(
        'ssmi_plugin_social_media_section',
        'Social Media Platforms',
        'ssmi_plugin_social_media_section_cb',
        'ssmi-social-media-icons-plugin'
    );

    foreach ($social_media_options as $id => $label) {
        add_settings_field(
            "{$id}_settings",
            $label,
            'ssmi_plugin_field_social_media_cb',
            'ssmi-social-media-icons-plugin',
            'ssmi_plugin_social_media_section',
            [
                'id' => $id,
                'label' => $label
            ]
        );
    }
}
add_action('admin_init', 'ssmi_plugin_settings_init');

function ssmi_plugin_icon_settings_section_cb() {
    echo '<p>Adjust the display size of the social media icons and the space between them.</p>';
    echo '<div style="background-color: #f7f7f7; padding: 10px; border-left: 4px solid #0073aa; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
        <p><strong>Note:</strong> Please remember to clear your cache when making changes to ensure that the latest icons are displayed correctly.</p>
    </div>';
}

function ssmi_plugin_social_media_section_cb() {
    echo '<p>Select the icons you want to use by providing their URLs. Use the shortcode <code>[ssmi_icons]</code> to display them on your site.</p>';
	 echo '<div style="background-color: #f7f7f7; padding: 10px; border-left: 4px solid #0073aa; margin: 20px 0; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
        <p><strong>Note:</strong> Please remember to fill in the identifiers in the Icon Order, otherwise your icons will not be displayed.</p>
    </div>';
}

function ssmi_plugin_field_icon_order_cb($args) {
    // Retrieve the settings from the database.
    $options = get_option('ssmi_plugin_settings');
    $icon_order = isset($options['icon_order']) ? $options['icon_order'] : '';

    // Define the placeholder text.
    $placeholder = "e.g., facebook,twitter,instagram,google-drive";

    // Render the input field with a placeholder attribute.
    echo '<input type="text" id="' . esc_attr($args['label_for']) . '" name="ssmi_plugin_settings[' . esc_attr($args['label_for']) . ']" value="' . esc_attr($icon_order) . '" class="regular-text" placeholder="' . esc_attr($placeholder) . '">';

    // Display the description below the input field if provided.
    if (isset($args['description'])) {
        echo '<p class="description">' . esc_html($args['description']) . '</p>';
    }
}

function ssmi_plugin_field_icon_size_cb($args) {
    $options = get_option('ssmi_plugin_settings');
    $value = isset($options[$args['name']]) ? $options[$args['name']] : $args['value'];
    echo '<input type="number" id="' . esc_attr($args['label_for']) . '" name="ssmi_plugin_settings[' . esc_attr($args['name']) . ']" value="' . esc_attr($value) . '" min="' . esc_attr($args['min']) . '" max="' . esc_attr($args['max']) . '" class="small-text">';
    echo '<p class="description">' . esc_html($args['description']) . '</p>';
}

function ssmi_plugin_field_icon_spacing_cb($args) {
    $options = get_option('ssmi_plugin_settings');
    $value = isset($options[$args['name']]) ? $options[$args['name']] : $args['value'];
    echo '<input type="number" id="' . esc_attr($args['label_for']) . '" name="ssmi_plugin_settings[' . esc_attr($args['name']) . ']" value="' . esc_attr($value) . '" min="' . esc_attr($args['min']) . '" max="' . esc_attr($args['max']) . '" class="small-text">';
    echo '<p class="description">' . esc_html($args['description']) . '</p>';
}

function ssmi_plugin_field_icon_style_cb($args) {
    $options = get_option('ssmi_plugin_settings');
    $current_value = isset($options['icon_style']) ? $options['icon_style'] : 'Option 1';
    echo '<select id="' . esc_attr($args['label_for']) . '" name="ssmi_plugin_settings[' . esc_attr($args['label_for']) . ']">';
    echo '<option value="Option 1" ' . selected($current_value, 'Option 1', false) . '>Color logo (no shape)</option>';
    echo '<option value="Option 2" ' . selected($current_value, 'Option 2', false) . '>Black logo (no shape)</option>';
    echo '<option value="Option 3" ' . selected($current_value, 'Option 3', false) . '>White logo (circular, colored shape))</option>';
    // Add the new option here
    echo '<option value="Option 4" ' . selected($current_value, 'Option 4', false) . '>White logo (circular, black shape)</option>';
    echo '</select>';
    if (isset($args['description'])) {
        echo '<p class="description">' . esc_html($args['description']) . '</p>';
    }
}

function ssmi_plugin_field_social_media_cb($args) {
    $options = get_option('ssmi_plugin_settings');
    $link = isset($options[$args['id'] . '_link']) ? $options[$args['id'] . '_link'] : '';
    echo '<input type="text" id="' . esc_attr($args['id'] . '_link') . '" name="ssmi_plugin_settings[' . esc_attr($args['id'] . '_link') . ']" value="' . esc_url($link) . '" class="regular-text" placeholder="https://example.com" style="width: 400px;">';
}

function ssmi_plugin_enqueue_scripts() {
    wp_enqueue_script(
        'ssmi-social-media-icons-plugin-js',
        plugin_dir_url(__FILE__) . 'js/ssmi-social-media-icons-plugin.js',
        [],
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'ssmi_plugin_enqueue_scripts');

function ssmi_plugin_shortcode() {
    $options = get_option('ssmi_plugin_settings');
    $icon_order = isset($options['icon_order']) ? explode(',', $options['icon_order']) : [];
    $icon_size = isset($options['icon_size']) ? $options['icon_size'] : 65;
    $icon_spacing = isset($options['icon_spacing']) ? $options['icon_spacing'] : 5;
    $icon_style = isset($options['icon_style']) ? strtolower(str_replace(' ', '', $options['icon_style'])) : 'option1';  // Default to Option 1

    $output = '<div class="social-media-icons">';
    foreach ($icon_order as $icon) {
        $icon = trim($icon);
        $key = $icon . '_link';
        if (isset($options[$key]) && !empty($options[$key])) {
            $url = $options[$key];
            $output .= '<a href="' . esc_url($url) . '" target="_blank"><img src="' . plugin_dir_url(__FILE__) . 'icons/' . $icon . $icon_style . '.png" alt="' . ucfirst($icon) . '" style="width: ' . esc_attr($icon_size) . 'px; height: ' . esc_attr($icon_size) . 'px; margin-right: ' . esc_attr($icon_spacing) . 'px;"></a>';
        }
    }
    $output .= '</div>';
    return $output;
}

add_shortcode('ssmi_icons', 'ssmi_plugin_shortcode');

function ssmi_plugin_admin_styles($hook) {
    if ('toplevel_page_ssmi-social-media-icons-plugin' !== $hook) {
        return;
    }
    wp_enqueue_style(
        'ssmi-social-media-icons-plugin-admin',
        plugin_dir_url(__FILE__) . 'css/admin-style.css',
        [],
        '1.0.0'
    );
}
add_action('admin_enqueue_scripts', 'ssmi_plugin_admin_styles');
