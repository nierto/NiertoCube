<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Action hooks for cube face content
function nierto_cube_face_content($content, $face_id) {
    return apply_filters('nierto_cube_face_content', $content, $face_id);
}

// Action before rendering cube
function nierto_cube_before_cube() {
    do_action('nierto_cube_before_cube');
}

// Action after rendering cube
function nierto_cube_after_cube() {
    do_action('nierto_cube_after_cube');
}

// Filter for cube rotation speed
function nierto_cube_rotation_speed($speed) {
    return apply_filters('nierto_cube_rotation_speed', $speed);
}

// Action for adding custom scripts
function nierto_cube_enqueue_scripts() {
    do_action('nierto_cube_enqueue_scripts');
}

// Filter for modifying cube face settings
function nierto_cube_face_settings($settings, $face_id) {
    return apply_filters('nierto_cube_face_settings', $settings, $face_id);
}

// Hook for modifying cache behavior
function nierto_cube_cache_behavior($behavior) {
    return apply_filters('nierto_cube_cache_behavior', $behavior);
}

// Action before cache operations
function nierto_cube_before_cache_operation($operation, $key) {
    do_action('nierto_cube_before_cache_operation', $operation, $key);
}

// Action after cache operations
function nierto_cube_after_cache_operation($operation, $key, $result) {
    do_action('nierto_cube_after_cache_operation', $operation, $key, $result);
}

// Filter for modifying AJAX response
function nierto_cube_ajax_response($response, $action) {
    return apply_filters('nierto_cube_ajax_response', $response, $action);
}

// Action for custom AJAX handlers
function nierto_cube_custom_ajax_handler($action) {
    do_action('nierto_cube_custom_ajax_handler', $action);
}
