<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_log_error($message, $error_data = null) {
    $log_entry = date('[Y-m-d H:i:s]') . " $message";
    if ($error_data) {
        $log_entry .= " Error data: " . print_r($error_data, true);
    }
    error_log($log_entry . PHP_EOL, 3, get_template_directory() . '/logs/nierto-cube-errors.log');
}

function nierto_cube_ajax_error_handler($errno, $errstr, $errfile, $errline) {
    nierto_cube_log_error("AJAX Error: $errstr in $errfile on line $errline");
    return true; // Don't execute the PHP internal error handler
}

// Use this function to wrap your AJAX callbacks
function nierto_cube_ajax_wrapper($callback) {
    return function() use ($callback) {
        try {
            set_error_handler('nierto_cube_ajax_error_handler');
            $result = call_user_func($callback);
            restore_error_handler();
            wp_send_json_success($result);
        } catch (Exception $e) {
            nierto_cube_log_error('AJAX Exception: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            wp_send_json_error(['message' => 'An error occurred. Please try again later.']);
        }
    };
}

// Example usage in your AJAX handlers
add_action('wp_ajax_nierto_cube_get_face_content', nierto_cube_ajax_wrapper('nierto_cube_get_face_content_callback'));
add_action('wp_ajax_nopriv_nierto_cube_get_face_content', nierto_cube_ajax_wrapper('nierto_cube_get_face_content_callback'));

function nierto_cube_get_face_content_callback() {
    // Your existing code here, but now with better error handling
    $slug = sanitize_text_field($_POST['slug']);
    $content = get_face_content(['slug' => $slug]);
    
    if (is_wp_error($content)) {
        throw new Exception($content->get_error_message());
    }
    
    return $content;
}

// Add this to your theme's init function or directly in functions.php
function nierto_cube_create_log_directory() {
    $log_dir = get_template_directory() . '/logs';
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
}
add_action('after_setup_theme', 'nierto_cube_create_log_directory');