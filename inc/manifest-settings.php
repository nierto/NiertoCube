<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_get_manifest_settings() {
    // Try to get from cache first
    $cache_key = 'nierto_cube_manifest_settings';
    
    if (is_valkey_enabled()) {
        $manifest = valkey_get($cache_key);
        if ($manifest) {
            return json_decode($manifest, true);
        }
    } else {
        $manifest = get_transient($cache_key);
        if ($manifest !== false) {
            return $manifest;
        }
    }

    // Generate new manifest settings
    $manifest = array(
        'enabled' => get_theme_mod('enable_pwa', 0),
        'name' => get_theme_mod('pwa_name', get_bloginfo('name')),
        'short_name' => get_theme_mod('pwa_short_name', substr(get_bloginfo('name'), 0, 12)),
        'start_url' => home_url('/'),
        'display' => 'standalone',
        'background_color' => get_theme_mod('pwa_background_color', '#ffffff'),
        'theme_color' => get_theme_mod('pwa_theme_color', '#000000'),
        'description' => get_theme_mod('pwa_description', get_bloginfo('description')),
        'icons' => array(
            array(
                'src' => get_theme_mod('pwa_icon_192', get_template_directory_uri() . '/assets/default-icon-192x192.png'),
                'sizes' => '192x192',
                'type' => 'image/png'
            ),
            array(
                'src' => get_theme_mod('pwa_icon_512', get_template_directory_uri() . '/assets/default-icon-512x512.png'),
                'sizes' => '512x512',
                'type' => 'image/png'
            )
        )
    );

    // Cache the settings
    if (is_valkey_enabled()) {
        valkey_set($cache_key, json_encode($manifest), 3600);
    } else {
        set_transient($cache_key, $manifest, 3600);
    }

    return $manifest;
}

function nierto_cube_update_manifest_settings() {
    $cache_key = 'nierto_cube_manifest_settings';
    if (is_valkey_enabled()) {
        valkey_delete($cache_key);
    } else {
        delete_transient($cache_key);
    }
}

add_action('customize_save_after', 'nierto_cube_update_manifest_settings');