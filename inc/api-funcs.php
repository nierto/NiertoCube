<?php
// Add this to a new file, e.g., nierto-cube-cache-api.php, and include it in your functions.php

if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_get_cache_prefix() {
    $prefix = get_theme_mod('nierto_cube_cache_prefix', 'nierto_cube_');
    if (is_multisite()) {
        $prefix .= get_current_blog_id() . '_';
    } else {
        // For single-site installations, we'll use the domain to ensure uniqueness
        $domain = str_replace(['http://', 'https://', 'www.'], '', get_site_url());
        $prefix .= md5($domain) . '_'; // Using md5 to keep the prefix length manageable
    }
    return $prefix;
}
/**
 * Set a value in the cache
 *
 * @param string $key The cache key
 * @param mixed $value The value to cache
 * @param int $expiration Expiration time in seconds
 * @return bool Whether the value was successfully stored
 */
function nierto_cube_cache_set($key, $value, $expiration = 3600) {
    $prefix = nierto_cube_get_cache_prefix();
    $prefixed_key = $prefix . $key;
    
    if (function_exists('is_valkey_enabled') && is_valkey_enabled()) {
        return valkey_set($prefixed_key, json_encode($value), $expiration);
    } else {
        return set_transient($prefixed_key, $value, $expiration);
    }
}
/**
 * Get a value from the cache
 *
 * @param string $key The cache key
 * @return mixed The cached value, or false if not found
 */
function nierto_cube_cache_get($key) {
    $prefix = nierto_cube_get_cache_prefix();
    $prefixed_key = $prefix . $key;
    
    if (function_exists('is_valkey_enabled') && is_valkey_enabled()) {
        $value = valkey_get($prefixed_key);
        return $value !== false ? json_decode($value, true) : false;
    } else {
        return get_transient($prefixed_key);
    }
}

/**
 * Delete a value from the cache
 *
 * @param string $key The cache key
 * @return bool Whether the value was successfully deleted
 */
function nierto_cube_cache_delete($key) {
    $prefix = nierto_cube_get_cache_prefix();
    $prefixed_key = $prefix . $key;
    
    if (function_exists('is_valkey_enabled') && is_valkey_enabled()) {
        return valkey_delete($prefixed_key);
    } else {
        return delete_transient($prefixed_key);
    }
}

// Add a hook for cache operations
function nierto_cube_cache_operation($operation, $key, $value = null, $expiration = 3600) {
    return apply_filters('nierto_cube_cache_operation', null, $operation, $key, $value, $expiration);
}

/**
 * Clear all cache
 *
 * @return boolean true if operation was successful, false otherwise    
 */
function nierto_cube_cache_clear_all() {
    $prefix = nierto_cube_get_cache_prefix();
    
    if (function_exists('is_valkey_enabled') && is_valkey_enabled()) {
        if (function_exists('valkey_delete_by_prefix')) {
            try {
                valkey_delete_by_prefix($prefix);
            } catch (Exception $e) {
                error_log('ValKey cache clearing failed: ' . $e->getMessage());
            }
        }
    } else {
        global $wpdb;
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            $wpdb->esc_like('_transient_' . $prefix) . '%',
            $wpdb->esc_like('_transient_timeout_' . $prefix) . '%'
        ));
    }

    // Increment the cache version to invalidate all existing cache entries
    nierto_cube_increment_cache_version();

    // Clear config JS cache
    clear_config_js_cache();

    // Set a flag to clear browser caches
    update_option('nierto_cube_clear_browser_cache', true);

    // Allow other plugins to perform additional cache clearing actions
    do_action('nierto_cube_cache_clear_all');

    return true;
}

// Add this function to enqueue the clear-cache script when needed
function nierto_cube_maybe_enqueue_clear_cache_script() {
    if (get_option('nierto_cube_clear_browser_cache', false)) {
        wp_enqueue_script('clear-cache', get_template_directory_uri() . '/js/clear-cache.js', array(), '1.0', true);
        delete_option('nierto_cube_clear_browser_cache');
    }
}
add_action('wp_enqueue_scripts', 'nierto_cube_maybe_enqueue_clear_cache_script');

// Function to check if the current user can clear cache
function nierto_cube_can_clear_cache() {
    if (!current_user_can('manage_options')) {
        return false;
    }

    $last_clear_time = get_option('nierto_cube_last_cache_clear', 0);
    $current_time = time();
    $throttle_period = 300; // 5 minutes

    if ($current_time - $last_clear_time < $throttle_period) {
        return false;
    }

    return true;
}
// Add clear cache button to admin bar
function nierto_cube_add_clear_cache_button($wp_admin_bar) {
    if (nierto_cube_can_clear_cache()) {
        $wp_admin_bar->add_node(array(
            'id'    => 'nierto_cube_clear_cache',
            'title' => 'Clear Nierto Cube Cache',
            'href'  => wp_nonce_url(admin_url('admin-post.php?action=nierto_cube_clear_cache'), 'nierto_cube_clear_cache'),
        ));
    }
}
add_action('admin_bar_menu', 'nierto_cube_add_clear_cache_button', 100);

// Handle clear cache action
function nierto_cube_handle_clear_cache() {
    if (!nierto_cube_can_clear_cache() || !wp_verify_nonce($_GET['_wpnonce'], 'nierto_cube_clear_cache')) {
        wp_die('You do not have permission to clear the cache or it has been cleared too recently.', 'Error', array('response' => 403));
    }

    nierto_cube_cache_clear_all();
    update_option('nierto_cube_last_cache_clear', time());

    wp_safe_redirect(wp_get_referer());
    exit;
}
add_action('admin_post_nierto_cube_clear_cache', 'nierto_cube_handle_clear_cache');