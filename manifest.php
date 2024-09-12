<?php
header('Content-Type: application/json');

$manifest = array(
    'name' => get_bloginfo('name'),
    'short_name' => 'NCube',
    'start_url' => home_url('/'),
    'display' => 'standalone',
    'background_color' => '#ffffff',
    'theme_color' => '#000000',
    'icons' => array()
);

$icon_192 = get_theme_mod('pwa_icon_192');
if ($icon_192) {
    $manifest['icons'][] = array(
        'src' => $icon_192,
        'sizes' => '192x192',
        'type' => 'image/png'
    );
}

$icon_512 = get_theme_mod('pwa_icon_512');
if ($icon_512) {
    $manifest['icons'][] = array(
        'src' => $icon_512,
        'sizes' => '512x512',
        'type' => 'image/png'
    );
}

echo json_encode($manifest);