<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
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
    if (!check_ajax_referer('nierto_cube_ajax', 'nonce', false)) {
        wp_send_json_error(['message' => 'Nonce verification failed']);
        return;
    }

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

add_action('wp_ajax_nierto_cube_ajax', 'nierto_cube_ajax_handler');
add_action('wp_ajax_nopriv_nierto_cube_ajax', 'nierto_cube_ajax_handler');