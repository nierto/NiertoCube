<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


function nierto_cube_get_face_data() {
    $face_data = array();
    for ($i = 1; $i <= 6; $i++) {
        $face_data[] = array(
            'text' => get_theme_mod("cube_face_{$i}_text", "Face {$i}"),
            'slug' => get_theme_mod("cube_face_{$i}_slug", "face-{$i}"),
            'position' => get_theme_mod("cube_face_{$i}_position", "face" . ($i - 1)),
        );
    }
    return $face_data;
}