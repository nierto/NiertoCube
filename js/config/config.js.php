<?php
// config.js.php

// Remove this line as we're no longer directly including WordPress
// require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

// This file will now be included by WordPress, so we can use WordPress functions directly

// Check if this is an AJAX request
if (defined('DOING_AJAX') && DOING_AJAX) {
    // This is an AJAX request, so we'll return JSON
    header('Content-Type: application/json');

    // Check nonce for security
    check_ajax_referer('nierto_cube_config', 'nonce');

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

    $cube_faces = [];
    for ($i = 1; $i <= 6; $i++) {
        $cube_faces[] = [
            'buttonText' => get_theme_mod("cube_face_{$i}_text", "Face {$i}"),
            'urlSlug' => get_theme_mod("cube_face_{$i}_slug", "face-{$i}"),
            'facePosition' => get_theme_mod("cube_face_{$i}_position", "face" . ($i - 1)),
            'contentType' => get_theme_mod("cube_face_{$i}_type", "page"),
        ];
    }

    $jsContent = 'const variables = ' . json_encode(['cubeFaces' => $cube_faces]) . ';';

    $jsContent .= '
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
    }';

    // Return the JavaScript content as JSON
    echo json_encode(['success' => true, 'data' => $jsContent]);
    exit;
} else {
    // This is a normal request, so we'll output JavaScript
    header("Content-type: application/javascript");

    // Check if a cached version exists
    $cache_file = get_template_directory() . '/js/config/cached_config.js';
    $cache_time = 86400; // Cache for 1 day

    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
        // Serve the cached file
        readfile($cache_file);
        exit;
    }

    // If we're here, we need to generate new content
    $cube_faces = [];
    for ($i = 1; $i <= 6; $i++) {
        $cube_faces[] = [
            'buttonText' => get_theme_mod("cube_face_{$i}_text", "Face {$i}"),
            'urlSlug' => get_theme_mod("cube_face_{$i}_slug", "face-{$i}"),
            'facePosition' => get_theme_mod("cube_face_{$i}_position", "face" . ($i - 1)),
            'contentType' => get_theme_mod("cube_face_{$i}_type", "page"),
        ];
    }

    $jsContent = 'const variables = ' . json_encode(['cubeFaces' => $cube_faces]) . ';';

    $jsContent .= '
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

    document.addEventListener(\'DOMContentLoaded\', setupCubeButtons);';

    // Ensure cache directory exists
    $cache_dir = dirname($cache_file);
    if (!is_dir($cache_dir)) {
        mkdir($cache_dir, 0755, true);
    }

    // Save the content to the cache file
    if (file_put_contents($cache_file, $jsContent) === false) {
        error_log('Failed to write to cache file: ' . $cache_file);
    }

    // Output the content
    echo $jsContent;

    // Log any potential errors
    $error = error_get_last();
    if ($error !== null) {
        $log_file = get_template_directory() . '/logs/NiertoCube.log';
        error_log($error['message'], 3, $log_file);
    }
}