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



// Example usage in your AJAX handlers


function nierto_cube_get_face_content_callback() {
    $slug = sanitize_text_field($_POST['slug']);
    $content = get_face_content(['slug' => $slug]);
    
    if (is_wp_error($content)) {
        throw new Exception($content->get_error_message());
    }
    
    // If it's a page, we'll return the URL for the iframe
    if ($content['type'] === 'page') {
        return $content;
    }
    
    // If it's a custom post, we'll return the content directly
    if ($content['type'] === 'post') {
        return $content['content'];
    }
    
    throw new Exception('Invalid content type');
}

// Add this to your theme's init function or directly in functions.php
function nierto_cube_create_log_directory() {
    $log_dir = get_template_directory() . '/logs';
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
}
add_action('after_setup_theme', 'nierto_cube_create_log_directory');