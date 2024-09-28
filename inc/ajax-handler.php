<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_verify_ajax_nonce() {
    if (!check_ajax_referer('nierto_cube_ajax', 'nonce', false)) {
        wp_send_json_error(['message' => 'Nonce verification failed']);
        exit;
    }
}

function nierto_cube_ajax_handler() {
    nierto_cube_verify_ajax_nonce();

    $action = isset($_POST['cube_action']) ? sanitize_text_field($_POST['cube_action']) : '';

    switch ($action) {
        case 'get_face_content':
            $slug = sanitize_text_field($_POST['slug']);
            $content = get_face_content(['slug' => $slug]);
            if (is_wp_error($content)) {
                wp_send_json_error(['message' => $content->get_error_message()]);
            } else {
                wp_send_json_success($content);
            }
            break;

        default:
            wp_send_json_error(['message' => 'Invalid action']);
            break;
    }
}

function nierto_cube_ajax_error_handler($errno, $errstr, $errfile, $errline) {
    nierto_cube_log_error("AJAX Error: $errstr in $errfile on line $errline");
    return true; // Don't execute the PHP internal error handler
}

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
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    };
}

function nierto_cube_get_face_content_ajax() {
    nierto_cube_verify_ajax_nonce();
    $slug = sanitize_text_field($_POST['slug']);
    $args = array(
        'name'        => $slug,
        'post_type'   => 'cube_face',
        'post_status' => 'publish',
        'numberposts' => 1
    );
    $posts = get_posts($args);
    if ($posts) {
        $post = $posts[0];
        $content = apply_filters('the_content', $post->post_content);
        $content = do_shortcode($content); // Process shortcodes
        
        // Get sidebar content
        ob_start();
        dynamic_sidebar('cube-face-sidebar');
        $sidebar_content = ob_get_clean();
        
        return array(
            'content' => $content,
            'sidebar' => $sidebar_content
        );
    } else {
        throw new Exception('Post not found');
    }
}

function nierto_cube_get_theme_url() {
    wp_send_json(array(
        'theme_url' => get_template_directory_uri() . '/',
        'nonce' => wp_create_nonce('nierto_cube_sw_cache')
    ));
}

add_action('wp_ajax_nierto_cube_ajax', nierto_cube_ajax_wrapper('nierto_cube_ajax_handler'));
add_action('wp_ajax_nopriv_nierto_cube_ajax', nierto_cube_ajax_wrapper('nierto_cube_ajax_handler'));
add_action('wp_ajax_nierto_cube_get_face_content', nierto_cube_ajax_wrapper('nierto_cube_get_face_content_ajax'));
add_action('wp_ajax_nopriv_nierto_cube_get_face_content', nierto_cube_ajax_wrapper('nierto_cube_get_face_content_ajax'));
add_action('wp_ajax_get_theme_url', nierto_cube_ajax_wrapper('nierto_cube_get_theme_url'));
add_action('wp_ajax_nopriv_get_theme_url', nierto_cube_ajax_wrapper('nierto_cube_get_theme_url'));