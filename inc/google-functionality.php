<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function get_google_font_url() {
    $google_fonts = [];
    $font_settings = ['body_font', 'heading_font', 'button_font', 'extra_font'];
    
    foreach ($font_settings as $setting) {
        if (get_theme_mod($setting . '_source', 'google') === 'google') {
            $font = get_theme_mod($setting . '_google', '');
            if (!empty($font)) {
                $google_fonts[] = $font;
            }
        }
    }
    
    if (!empty($google_fonts)) {
        return "https://fonts.googleapis.com/css2?family=" . implode('&family=', array_unique($google_fonts));
    }
    
    return '';
}

function get_font_family($setting) {
    $source = get_theme_mod($setting . '_source', 'google');
    if ($source === 'google') {
        $font_url = get_theme_mod($setting . '_google', 'Ubuntu:wght@300;400;700&display=swap');
        $font_family = explode(':', $font_url)[0];
        return "'" . str_replace('+', ' ', $font_family) . "', sans-serif";
    } else {
        return get_theme_mod($setting . '_local', 'Arial, sans-serif');
    }
}
// Output font CSS variables for use in other stylesheets
function nierto_cube_output_font_css_variables() {
    ?>
    <style>
        :root {
            --button-font: <?php echo get_font_family('button_font'); ?>;
        }
    </style>
    <?php
}
add_action('wp_head', 'nierto_cube_output_font_css_variables', 5);