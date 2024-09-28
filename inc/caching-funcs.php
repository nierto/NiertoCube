<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/cache-version.php';

function get_cached_content($key, $generate_callback = null) {
    $version = nierto_cube_get_cache_version();
    $versioned_key = "v{$version}_{$key}";

    if (is_valkey_enabled()) {
        $content = valkey_get($versioned_key);
        if ($content !== false) {
            return json_decode($content, true);
        }
    } else {
        $content = get_transient($versioned_key);
        if ($content !== false) {
            return $content;
        }
    }

    // Cache miss, generate content if callback provided
    if ($generate_callback && is_callable($generate_callback)) {
        $content = $generate_callback();
        set_cached_content($key, $content);
        return $content;
    }

    return false;
}

function set_cached_content($key, $value, $expiration = 86400) {
    $version = nierto_cube_get_cache_version();
    $versioned_key = "v{$version}_{$key}";
    $json_value = json_encode($value);

    if (is_valkey_enabled()) {
        valkey_set($versioned_key, $json_value, $expiration);
    } else {
        set_transient($versioned_key, $value, $expiration);
    }
}

function clear_cached_content($key) {
    $version = nierto_cube_get_cache_version();
    $versioned_key = "v{$version}_{$key}";

    if (is_valkey_enabled()) {
        valkey_delete($versioned_key);
    } else {
        delete_transient($versioned_key);
    }
}

function clear_all_cache() {
    nierto_cube_increment_cache_version();
    clear_config_js_cache();
    // Clear browser caches via service worker
    wp_enqueue_script('clear-cache', get_template_directory_uri() . '/js/clear-cache.js', array(), '1.0', true);
}

function clear_config_js_cache() {
    $cache_file = get_template_directory() . '/js/config/cached_config.js';
    if (file_exists($cache_file)) {
        unlink($cache_file);
    }
    clear_cached_content('config_js');
}
add_action('customize_save_after', 'clear_config_js_cache');

function add_clear_cache_button() {
    if (isset($_GET['clear_config_cache']) && current_user_can('manage_options')) {
        clear_all_cache();
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success"><p>All caches cleared successfully!</p></div>';
        });
    }
}
add_action('admin_init', 'add_clear_cache_button');

function add_clear_cache_link($wp_admin_bar) {
    if (current_user_can('manage_options')) {
        $wp_admin_bar->add_menu(array(
            'id'    => 'clear_config_cache',
            'title' => 'Clear All Caches',
            'href'  => add_query_arg('clear_config_cache', '1'),
        ));
    }
}
add_action('admin_bar_menu', 'add_clear_cache_link', 100);