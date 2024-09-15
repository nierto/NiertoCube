<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add this new function for secure settings retrieval
function get_valkey_settings() {
    $settings = get_option('nierto_cube_settings');
    if (!$settings || !isset($settings['valkey_ip']) || !isset($settings['valkey_port'])) {
        $settings = array(
            'use_valkey' => false,
            'valkey_ip' => '',
            'valkey_port' => '6379'
        );
        update_option('nierto_cube_settings', $settings);
    }
    return $settings;
}

// Update this function to use the new get_valkey_settings()
function is_valkey_enabled() {
    $settings = get_valkey_settings();
    return $settings['use_valkey'] && !empty($settings['valkey_ip']);
}

// Add this new function for cache versioning
function nierto_cube_get_cache_version() {
    $version = get_option('nierto_cube_cache_version', 1);
    return $version;
}

// Add this new function to increment cache version
function nierto_cube_increment_cache_version() {
    $version = nierto_cube_get_cache_version();
    update_option('nierto_cube_cache_version', $version + 1);
}

function valkey_get($key) {
    if (!is_valkey_enabled()) {
        return false;
    }
    
    $settings = get_valkey_settings();
    $valkey_ip = $settings['valkey_ip'];
    $valkey_port = $settings['valkey_port'];
    
    $version = nierto_cube_get_cache_version();
    $versioned_key = "v{$version}_{$key}";
    
    $redis = new Redis();
    try {
        $redis->connect($valkey_ip, $valkey_port);
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
    $valkey_ip = $settings['valkey_ip'];
    $valkey_port = $settings['valkey_port'];
    
    $version = nierto_cube_get_cache_version();
    $versioned_key = "v{$version}_{$key}";
    
    $redis = new Redis();
    try {
        $redis->connect($valkey_ip, $valkey_port);
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
    $valkey_ip = $settings['valkey_ip'];
    $valkey_port = $settings['valkey_port'];
    
    $version = nierto_cube_get_cache_version();
    $versioned_key = "v{$version}_{$key}";
    
    $redis = new Redis();
    try {
        $redis->connect($valkey_ip, $valkey_port);
        return $redis->del($versioned_key);
    } catch (Exception $e) {
        error_log('ValKey connection failed: ' . $e->getMessage());
        return false;
    }
}
