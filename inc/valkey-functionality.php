<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// ValKey integration (new August 2024):
function is_valkey_enabled() {
    $settings = get_option('nierto_cube_settings');
    return isset($settings['use_valkey']) && $settings['use_valkey'] && !empty($settings['valkey_ip']);
}

function valkey_get($key) {
    if (!is_valkey_enabled()) {
        return false;
    }
    
    $settings = get_option('nierto_cube_settings');
    $valkey_ip = $settings['valkey_ip'];
    $valkey_port = isset($settings['valkey_port']) ? $settings['valkey_port'] : '6379';
    
    $redis = new Redis();
    try {
        $redis->connect($valkey_ip, $valkey_port);
        return $redis->get($key);
    } catch (Exception $e) {
        error_log('ValKey connection failed: ' . $e->getMessage());
        return false;
    }
}

function valkey_set($key, $value, $ttl = 3600) {
    if (!is_valkey_enabled()) {
        return false;
    }
    
    $settings = get_option('nierto_cube_settings');
    $valkey_ip = $settings['valkey_ip'];
    $valkey_port = isset($settings['valkey_port']) ? $settings['valkey_port'] : '6379';
    
    $redis = new Redis();
    try {
        $redis->connect($valkey_ip, $valkey_port);
        return $redis->setex($key, $ttl, $value);
    } catch (Exception $e) {
        error_log('ValKey connection failed: ' . $e->getMessage());
        return false;
    }
}