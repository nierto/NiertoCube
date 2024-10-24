<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/cache-version.php';

function get_valkey_settings() {
    $settings = get_option('nierto_cube_settings');
    if (!$settings || !isset($settings['valkey_ip']) || !isset($settings['valkey_port']) || !isset($settings['valkey_auth'])) {
        $settings = array(
            'use_valkey' => false,
            'valkey_ip' => '',
            'valkey_port' => '6379',
            'valkey_auth' => ''  // Authentication key only
        );
        update_option('nierto_cube_settings', $settings);
    }
    return $settings;
}

function is_valkey_enabled() {
    $settings = get_valkey_settings();
    return $settings['use_valkey'] && !empty($settings['valkey_ip']) && !empty($settings['valkey_auth']);
}

function valkey_get($key) {
    if (!is_valkey_enabled()) {
        return false;
    }
    
    $settings = get_valkey_settings();
    $redis = new Redis();
    try {
        $redis->connect($settings['valkey_ip'], $settings['valkey_port']);
        if (!empty($settings['valkey_auth'])) {
            $redis->auth($settings['valkey_auth']);
        }
        $version = nierto_cube_get_cache_version();
        $versioned_key = "v{$version}_{$key}";
        return $redis->get($versioned_key);
    } catch (Exception $e) {
        error_log('ValKey connection failed: ' . $e->getMessage());
        return false;
    }
}

function valkey_set($key, $value, $ttl = 3600) {
    if (!is_valkey_enabled()) {
        return false;
    }
    
    $settings = get_valkey_settings();
    $redis = new Redis();
    try {
        $redis->connect($settings['valkey_ip'], $settings['valkey_port']);
        if (!empty($settings['valkey_auth'])) {
            $redis->auth($settings['valkey_auth']);
        }
        $version = nierto_cube_get_cache_version();
        $versioned_key = "v{$version}_{$key}";
        return $redis->setex($versioned_key, $ttl, $value);
    } catch (Exception $e) {
        error_log('ValKey connection failed: ' . $e->getMessage());
        return false;
    }
}

function valkey_delete($key) {
    if (!is_valkey_enabled()) {
        return false;
    }
    
    $settings = get_valkey_settings();
    $redis = new Redis();
    try {
        $redis->connect($settings['valkey_ip'], $settings['valkey_port']);
        if (!empty($settings['valkey_auth'])) {
            $redis->auth($settings['valkey_auth']);
        }
        $version = nierto_cube_get_cache_version();
        $versioned_key = "v{$version}_{$key}";
        return $redis->del($versioned_key);
    } catch (Exception $e) {
        error_log('ValKey connection failed: ' . $e->getMessage());
        return false;
    }
}

// Enqueue script for AJAX call
function enqueue_valkey_test_script() {
    wp_enqueue_script('valkey-test', get_template_directory_uri() . '/js/valkey-test.js', array('jquery'), '1.0', true);
    wp_localize_script('valkey-test', 'valkeyTest', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('test_valkey_connection')
    ));
}
add_action('customize_controls_enqueue_scripts', 'enqueue_valkey_test_script');