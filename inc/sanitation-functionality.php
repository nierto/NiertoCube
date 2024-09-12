<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_sanitize_hex_color($color) {
    if ('' === $color) {
        return '';
    }
    if (preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color)) {
        return $color;
    }
    return null;
}

function nierto_cube_sanitize_css_value($input) {
    $valid_units = ['px', 'em', 'rem', '%', 'vw', 'vh', 'vmin', 'vmax'];
    $pattern = '/^(\d*\.?\d+)(' . implode('|', $valid_units) . ')?$/';
    if (preg_match($pattern, $input)) {
        return $input;
    }
    return null;
}

function nierto_cube_sanitize_option($input, $setting) {
    $choices = $setting->manager->get_control($setting->id)->choices;
    return (array_key_exists($input, $choices) ? $input : $setting->default);
}
