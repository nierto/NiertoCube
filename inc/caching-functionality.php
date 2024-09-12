<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function get_cached_content($key) {
    if (is_valkey_enabled()) {
        $content = valkey_get($key);
        if ($content !== false) {
            return $content;
        }
    }
    return get_transient($key);
}

function set_cached_content($key, $value, $expiration) {
    if (is_valkey_enabled()) {
        valkey_set($key, $value, $expiration);
    }
    set_transient($key, $value, $expiration);
}

function clear_config_js_cache() {
    $cache_file = get_template_directory() . '/js/config/cached_config.js';
    if (file_exists($cache_file)) {
        unlink($cache_file);
    }
}
add_action('customize_save_after', 'clear_config_js_cache');


function add_clear_cache_button() {
    if (isset($_GET['clear_config_cache']) && current_user_can('manage_options')) {
        clear_config_js_cache();
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success"><p>Config cache cleared successfully!</p></div>';
        });
    }
}
add_action('admin_init', 'add_clear_cache_button');

function add_clear_cache_link($wp_admin_bar) {
    if (current_user_can('manage_options')) {
        $wp_admin_bar->add_menu(array(
            'id'    => 'clear_config_cache',
            'title' => 'Clear Config Cache',
            'href'  => add_query_arg('clear_config_cache', '1'),
        ));
    }
}
add_action('admin_bar_menu', 'add_clear_cache_link', 100);
