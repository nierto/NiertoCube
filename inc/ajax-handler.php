<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include the updated config.js.php
require_once get_template_directory() . '/js/config/config.js.php';

function nierto_cube_ajax_get_config() {
    check_ajax_referer('nierto_cube_config', 'nonce');
    
    // This function should be defined in config.js.php
    $cube_faces = nierto_cube_get_config_data();
    
    // Prepare the JavaScript content
    $js_content = 'const variables = ' . json_encode(['cubeFaces' => $cube_faces]) . ';';
    
    // Add the setupCubeButtons function
    $js_content .= '
    function setupCubeButtons() {
        const navButtons = document.querySelectorAll(\'.navButton\');
        variables.cubeFaces.forEach((face, index) => {
            const navName = navButtons[index]?.querySelector(\'.navName\');
            if (navName) {
                navName.textContent = face.buttonText;
                navName.setAttribute(\'data-face\', face.facePosition);
                navName.setAttribute(\'data-slug\', face.urlSlug);
            }
        });
    }
    ';
    
    // Send the response
    wp_send_json_success(['js_content' => $js_content]);
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