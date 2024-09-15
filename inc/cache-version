<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('nierto_cube_get_cache_version')) {
    function nierto_cube_get_cache_version() {
        return get_option('nierto_cube_cache_version', 1);
    }
}

if (!function_exists('nierto_cube_increment_cache_version')) {
    function nierto_cube_increment_cache_version() {
        $version = nierto_cube_get_cache_version();
        update_option('nierto_cube_cache_version', $version + 1);
    }
}