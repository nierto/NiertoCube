<?php
if (!get_theme_mod('enable_pwa', 0)) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

header('Content-Type: application/json');

$manifest = array(
    'name' => get_theme_mod('pwa_name', get_bloginfo('name')),
    'short_name' => get_theme_mod('pwa_short_name', substr(get_bloginfo('name'), 0, 12)),
    'start_url' => home_url('/'),
    'display' => 'standalone',
    'background_color' => get_theme_mod('pwa_background_color', '#ffffff'),
    'theme_color' => get_theme_mod('pwa_theme_color', '#000000'),
    'description' => get_theme_mod('pwa_description', get_bloginfo('description')),
    'icons' => array()
);

$icon_192 = get_theme_mod('pwa_icon_192', get_template_directory_uri() . '/assets/default-icon-192x192.png');
$icon_512 = get_theme_mod('pwa_icon_512', get_template_directory_uri() . '/assets/default-icon-512x512.png');

$manifest['icons'][] = array(
    'src' => $icon_192,
    'sizes' => '192x192',
    'type' => 'image/png'
);

$manifest['icons'][] = array(
    'src' => $icon_512,
    'sizes' => '512x512',
    'type' => 'image/png'
);

echo json_encode($manifest);