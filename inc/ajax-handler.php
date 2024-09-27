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

function nierto_cube_get_config_data() {
    $cube_faces = [];
    for ($i = 1; $i <= 6; $i++) {
        $cube_faces[] = [
            'buttonText' => get_theme_mod("cube_face_{$i}_text", "Face {$i}"),
            'urlSlug' => get_theme_mod("cube_face_{$i}_slug", "face-{$i}"),
            'facePosition' => get_theme_mod("cube_face_{$i}_position", "face" . ($i - 1)),
            'contentType' => get_theme_mod("cube_face_{$i}_type", "page"),
        ];
    }
    return $cube_faces;
}

function nierto_cube_ajax_handler() {
    nierto_cube_verify_ajax_nonce();

    $action = isset($_POST['cube_action']) ? sanitize_text_field($_POST['cube_action']) : '';

    switch ($action) {
        case 'get_config':
            $cube_faces = nierto_cube_get_config_data();
            $js_content = 'const variables = ' . json_encode(['cubeFaces' => $cube_faces]) . ';';
            $js_content .= '
            function setupCubeButtons() {
                const navButtons = document.querySelectorAll(".navButton");
                variables.cubeFaces.forEach((face, index) => {
                    const navName = navButtons[index]?.querySelector(".navName");
                    if (navName) {
                        navName.textContent = face.buttonText;
                        navName.setAttribute("data-face", face.facePosition);
                        navName.setAttribute("data-slug", face.urlSlug);
                        navButtons[index].setAttribute("aria-label", `Navigate to ${face.buttonText}`);
                    }
                });
            }
            ';
            wp_send_json_success(['data' => $js_content]);
            break;

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

function ajax_test_valkey_connection() {
    check_ajax_referer('test_valkey_connection', 'nonce');
    
    try {
        $result = test_valkey_connection();
        
        if ($result) {
            wp_send_json_success(__('Connection successful!', 'nierto_cube'));
        } else {
            throw new Exception(__('Connection failed. Please check your settings.', 'nierto_cube'));
        }
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
}

function nierto_cube_get_face_content_ajax() {
    nierto_cube_verify_ajax_nonce();
    $slug = sanitize_text_field($_POST['slug']);
    $content = get_face_content(['slug' => $slug]);
    wp_send_json_success($content);
}

function nierto_cube_get_theme_url() {
    wp_send_json(array(
        'theme_url' => get_template_directory_uri() . '/',
        'nonce' => wp_create_nonce('nierto_cube_sw_cache')
    ));
}

add_action('wp_ajax_nierto_cube_ajax', 'nierto_cube_ajax_handler');
add_action('wp_ajax_nopriv_nierto_cube_ajax', 'nierto_cube_ajax_handler');
add_action('wp_ajax_test_valkey_connection', 'ajax_test_valkey_connection');
add_action('wp_ajax_nierto_cube_get_face_content', nierto_cube_ajax_wrapper('nierto_cube_get_face_content_callback'));
add_action('wp_ajax_nopriv_nierto_cube_get_face_content', nierto_cube_ajax_wrapper('nierto_cube_get_face_content_callback'));
add_action('wp_ajax_get_theme_url', 'nierto_cube_get_theme_url');
add_action('wp_ajax_nopriv_get_theme_url', 'nierto_cube_get_theme_url');
