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

function nierto_cube_ajax_get_config() {
    check_ajax_referer('nierto_cube_config', 'nonce');
    
    $cube_faces = nierto_cube_get_config_data();
    
    // Prepare the JavaScript content
    $js_content = 'const variables = ' . json_encode(['cubeFaces' => $cube_faces]) . ';';
    
    // Add the setupCubeButtons function
    $js_content .= '
    function setupCubeButtons() {
        const navButtons = document.querySelectorAll(".navButton");
        variables.cubeFaces.forEach((face, index) => {
            const navName = navButtons[index]?.querySelector(".navName");
            if (navName) {
                navName.textContent = face.buttonText;
                navName.setAttribute("data-face", face.facePosition);
                navName.setAttribute("data-slug", face.urlSlug);
            }
        });
    }
    ';
    
    // Send the response
    wp_send_json_success(['data' => $js_content]);
}

add_action('wp_ajax_nierto_cube_get_config', 'nierto_cube_ajax_get_config');
add_action('wp_ajax_nopriv_nierto_cube_get_config', 'nierto_cube_ajax_get_config');

function nierto_cube_enqueue_config_script() {
    wp_enqueue_script('nierto-cube-config', get_template_directory_uri() . '/js/config.js', array(), null, true);
    wp_localize_script('nierto-cube-config', 'niertoCubeData', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('nierto_cube_config')
    ));
}
add_action('wp_enqueue_scripts', 'nierto_cube_enqueue_config_script');