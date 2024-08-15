<?php
function nierto_cube_customize_register($wp_customize) {
    // Registering sections
    // COLORS
    $wp_customize->add_section('colors', array(
        'title' => __('Colors', 'nierto_cube'),
        'description' => __('Customize the colors of the theme.', 'nierto_cube'),
        'priority' => 30,
    ));
    // CUBE SETTINGS
    $wp_customize->add_section('cube_settings', array(
        'title' => __('Cube Settings', 'nierto_cube'),
        'priority' => 160,
    ));
    $wp_customize->add_section('cube_face_settings', array(
        'title' => __('Cube Face Settings', 'nierto_cube'),
        'priority' => 163,
    ));
    // LOGO
    $wp_customize->add_section('logo', array(
        'title' => __('Logo Settings', 'nierto_cube'),
        'priority' => 173,
    ));
    // FONT
    $wp_customize->add_section('font', array(
        'title' => __('Font Settings', 'nierto_cube'),
        'priority' => 183,
    ));// Define Customizer settings for page names

    // NAV BUTTON STYLING
    $wp_customize->add_section('nav_button_styling', array(
        'title' => __('Navigation Button Styling', 'nierto_cube'),
        'priority' => 203,
    ));


    //SECTION: COLORS
    // Gradient colors settings
    $gradient_colors = ['1', '2', '3', '4'];
    foreach ($gradient_colors as $num) {
        $wp_customize->add_setting("grad_color{$num}", array(
            'default' => '#ee7752',
            'transport' => 'refresh',
            'sanitize_callback' => 'nierto_cube_sanitize_css'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, "grad_color{$num}", array(
            'label' => __("Gradient Color {$num}", 'nierto_cube'),
            'section' => 'colors',
            'settings' => "grad_color{$num}",
        )));
    }
     // Other color settings
    $color_settings = [
        'scrollbar_color1' => '#F97162',
        'scrollbar_color2' => '#FEFEF9',
        'color_background' => '#F97162',
        'color_text' => '#F97162',
        'color_header' => '#FEFEF9',
        'color_border' => '#F5F9E9',
        'color_highlight' => '#F5F9E9',
        'color_hover' => '#F5F9E9',
        'color_background_button' => '#F5F9E9',
        'color_text_button' => '#F5F9E9',
        'nav_button_bg_color' => '#ffffff',
        'nav_button_text_color ' => '#000000',
        'nav_button_hover_bg_color' => '#dddddd',
        'nav_button_hover_text_color' => '#000000',
        'nav_button_border_color' => '#000000'
    ];
    foreach ($color_settings as $setting_id => $default) {
        $wp_customize->add_setting($setting_id, [
            'default' => $default,
            'transport' => 'refresh',
            'sanitize_callback' => 'nierto_cube_sanitize_css'
        ]);
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $setting_id, array(
            'label' => __(ucfirst(str_replace('_', ' ', $setting_id)), 'nierto_cube'),
            'section' => 'colors',
            'settings' => $setting_id,
        )));
    }
    // SECTION: CUBE SETTINGS
    $cube_settings = [
        'perspective_scene' => [
            'default' => '200vmin',
            'label' => 'Perspective for Scene'
        ],
        'perspective_origin_scene' => [
            'default' => '50% 50%',
            'label' => 'Perspective Origin for Scene'
        ],
        'default_cubeheight' => [
            'default' => '80vmin',
            'label' => 'The Height of the Cube'
        ],
        'default_cubewidth' => [
            'default' => '80vmin',
            'label' => 'The Width of the Cube'
        ]
    ];
    foreach ($cube_settings as $id => $values) {
        $wp_customize->add_setting($id, [
            'default' => $values['default'],
            'transport' => 'refresh',
            'sanitize_callback' => 'nierto_cube_sanitize_css'
        ]);
        $wp_customize->add_control($id, [
            'label' => __($values['label'], 'nierto_cube'),
            'section' => 'cube_settings',
            'type' => 'text'
        ]);
    }
    //background IMG for cube backside
    $wp_customize->add_setting('cube_four_bg_image', [
        'default' => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'esc_url_raw'
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'cube_four_bg_image', [
        'label' => __('Background Image for Cube Backside', 'nierto_cube'),
        'section' => 'cube_settings',
        'settings' => 'cube_four_bg_image',
    ]));
    //background style for cube backside
    $wp_customize->add_setting('cube_four_bg_size', [
        'default' => 'cover',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    $wp_customize->add_control('cube_four_bg_size', [
        'label' => __('Background Size for Cube Backside', 'nierto_cube'),
        'section' => 'cube_settings',
        'type' => 'text',
    ]);
    //SECTION: PAGE NAMES FOR SIDES
    // PAGE NAMES FOR FUNCTION CALLINGZ
        // CUBE PAGE NAMES
    for ($i = 1; $i <= 6; $i++) {
        $wp_customize->add_setting("cube_face_{$i}_text", array(
            'default' => "Face {$i}",
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control("cube_face_{$i}_text", array(
            'label' => "Face {$i} Text",
            'section' => 'cube_face_settings',
            'type' => 'text',
        ));

        $wp_customize->add_setting("cube_face_{$i}_type", array(
            'default' => 'page',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control("cube_face_{$i}_type", array(
            'label' => "Face {$i} Content Type",
            'section' => 'cube_face_settings',
            'type' => 'select',
            'choices' => array(
                'page' => 'Page (iframe)',
                'post' => 'Custom Post'
            ),
        ));

        $wp_customize->add_setting("cube_face_{$i}_slug", array(
            'default' => "face-{$i}",
            'sanitize_callback' => 'sanitize_title',
        ));

        $wp_customize->add_control("cube_face_{$i}_slug", array(
            'label' => "Face {$i} Slug/Title",
            'section' => 'cube_face_settings',
            'type' => 'text',
            'description' => 'Enter URL slug for Page or post title for Custom Post',
        ));

        // Keep your existing position setting
        $wp_customize->add_setting("cube_face_{$i}_position", array(
            'default' => "face" . ($i - 1),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control("cube_face_{$i}_position", array(
            'label' => "Face {$i} Position",
            'section' => 'cube_face_settings',
            'type' => 'select',
            'choices' => array(
                'face0' => 'Face 0',
                'face1' => 'Face 1 = Front',
                'face2' => 'Face 2',
                'face3' => 'Face 3 = Back',
                'face4' => 'Face 4',
                'face5' => 'Face 5 Reversed',
            ),
        ));
    }
    //SECTION: LOGO
    // Logo settings
    for ($i = 1; $i <= 2; $i++) {
        if ($i == 1){
            $name = "width";
        } else {
            $name = "height";
        }
        $wp_customize->add_setting("logo_{$name}", array(
            'default' => "124px",
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));
        $wp_customize->add_control("logo_{$name}", array(
            'label' => __("Logo Setting {$name}", 'nierto_cube'),
            'section' => 'logo',
            'type' => 'text',
            'settings' => "logo_{$name}",
        ));
    }
    $wp_customize->add_setting('logo_source', array(
        'default' => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'esc_url_raw'
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'source_logo', [
        'label' => __('Your logo', 'nierto_cube'),
        'section' => 'logo',
        'settings' => 'logo_source',
    ]));
// SECTION: FONT
$font_settings = [
    'body_font' => [
        'label' => 'Body Font',
        'default_google' => 'Ubuntu:wght@300;400;700&display=swap',
        'default_local' => 'Ubuntu, sans-serif',
        'description' => 'The default font-family for the body text.'
    ],
    'heading_font' => [
        'label' => 'Heading Font',
        'default_google' => 'Ubuntu:wght@300;400;700&display=swap',
        'default_local' => 'Ubuntu, sans-serif',
        'description' => 'The default font family for headings.'
    ],
    'button_font' => [
        'label' => 'Button Font',
        'default_google' => 'Rubik:wght@400;500&display=swap',
        'default_local' => 'Rubik, sans-serif',
        'description' => 'The default font family for buttons, including navigation buttons.'
    ],
    'extra_font' => [
        'label' => 'Extra Font',
        'default_google' => 'Rubik:wght@300;400;700&display=swap',
        'default_local' => 'Rubik, sans-serif',
        'description' => 'An additional font for use with custom classes.'
    ]
];

foreach ($font_settings as $setting_id => $values) {
    $wp_customize->add_setting($setting_id . '_source', [
        'default' => 'google',
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    $wp_customize->add_control($setting_id . '_source', [
        'label' => __($values['label'] . ' Source', 'nierto_cube'),
        'section' => 'font',
        'type' => 'radio',
        'choices' => [
            'google' => 'Google Font',
            'local' => 'Local Font'
        ]
    ]);

    $wp_customize->add_setting($setting_id . '_google', [
        'default' => $values['default_google'],
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    $wp_customize->add_control($setting_id . '_google', [
        'label' => __($values['label'] . ' (Google)', 'nierto_cube'),
        'description' => __($values['description'] . ' Enter the part of the Google Font URL after "https://fonts.googleapis.com/css2?family=". For example: Ubuntu:wght@300;400;700&display=swap', 'nierto_cube'),
        'section' => 'font',
        'type' => 'text'
    ]);

    $wp_customize->add_setting($setting_id . '_local', [
        'default' => $values['default_local'],
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    $wp_customize->add_control($setting_id . '_local', [
        'label' => __($values['label'] . ' (Local)', 'nierto_cube'),
        'description' => __($values['description'] . ' Enter the font-family value for a locally available font. For example: Ubuntu, sans-serif', 'nierto_cube'),
        'section' => 'font',
        'type' => 'text'
    ]);
}
    // SECTION: NAV STYLING
    // sizes and dimensions
        $nav_texts = [
        'nav_button_padding' => '10px 20px',
        'nav_button_font_size' => '16px',
        'nav_button_border_style' => 'solid',
        'nav_button_border_width' => '1px',
        'nav_button_border_radius' => '20%',
        'nav_wrapper_width' => '15%',
        'nav_button_min_width' => '18vmin',
        'nav_button_max_height' => '5vmin'
    ];
    foreach ($nav_texts as $setting_id => $default_text) {
        $wp_customize->add_setting($setting_id, array(
            'default' => $default_text,
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));
        $wp_customize->add_control($setting_id, array(
            'label' => __(ucfirst(str_replace('_', ' ', $setting_id)), 'nierto_cube'),
            'section' => 'nav_button_styling',
            'type' => 'text',
            'settings' => $setting_id,
        ));
    }
}

add_action('customize_register', 'nierto_cube_customize_register');

function nierto_cube_customizer_css() {
    $cube_four_bg_image = get_theme_mod('cube_four_bg_image', '');
    function prepend_hash($color) {
        return strpos($color, '#') === 0 ? $color : '#' . $color;
    }
    ?>
    <style type="text/css">
        :root { 
            scrollbar-color: <?php echo prepend_hash(get_theme_mod('scrollbar_color1', '#F97162')); ?> <?php echo prepend_hash(get_theme_mod('scrollbar_color2', '#FEFEF9')); ?>;
            --gradcolor1: <?php echo prepend_hash(get_theme_mod('grad_color1', '#ee7752')); ?>;
            --gradcolor2: <?php echo prepend_hash(get_theme_mod('grad_color2', '#e73c7e')); ?>;
            --gradcolor3: <?php echo prepend_hash(get_theme_mod('grad_color3', '#23a6d5')); ?>;
            --gradcolor4: <?php echo prepend_hash(get_theme_mod('grad_color4', '#23d5ab')); ?>;
            --color-bg: <?php echo prepend_hash(get_theme_mod('color_background', '#F97162')); ?>;
            --color-txt: <?php echo prepend_hash(get_theme_mod('color_text', '#F97162')); ?>;
            --color-header: <?php echo prepend_hash(get_theme_mod('color_header', '#FEFEF9')); ?>;
            --color-border: <?php echo prepend_hash(get_theme_mod('color_border', '#F5F9E9')); ?>;
            --color-highlight: <?php echo prepend_hash(get_theme_mod('color_highlight', '#F5F9E9')); ?>;
            --color-hover: <?php echo prepend_hash(get_theme_mod('color_hover', '#F5F9E9')); ?>;
            --color-bg-button: <?php echo prepend_hash(get_theme_mod('color_background_button', '#F5F9E9')); ?>;
            --color-txt-button: <?php echo prepend_hash(get_theme_mod('color_text_button', '#F5F9E9')); ?>;
            --default-cubeheight: <?php echo get_theme_mod('default_cubeheight', '80vmin'); ?>;
            --default-cubewidth: <?php echo get_theme_mod('default_cubewidth', '80vmin'); ?>;
            --semi-transparant: <?php echo get_theme_mod('semi_transparant', 'rgba(255, 255, 255, 0.28)'); ?>;
            --nav-button-bg-color: <?php echo prepend_hash(get_theme_mod('nav_button_bg_color', '#ffffff')); ?>;
            --nav-button-text-color: <?php echo prepend_hash(get_theme_mod('nav_button_text_color', '#000000')); ?>;
            --nav-button-padding: <?php echo get_theme_mod('nav_button_padding', '10px 20px'); ?>;
            --nav-button-margin: <?php echo get_theme_mod('nav_button_margin', '10px'); ?>;
            --nav-button-font-size: <?php echo get_theme_mod('nav_button_font_size', '16px'); ?>;
            --nav-button-border-style: <?php echo get_theme_mod('nav_button_border_style', 'solid'); ?>;
            --nav-button-border-color: <?php echo prepend_hash(get_theme_mod('nav_button_border_color', '#000000')); ?>;
            --nav-button-border-width: <?php echo get_theme_mod('nav_button_border_width', '1px'); ?>;
            --nav-button-border-radius: <?php echo get_theme_mod('nav_button_border_radius', '20%'); ?>;
            --nav-button-hover-bg-color: <?php echo prepend_hash(get_theme_mod('nav_button_hover_bg_color', '#dddddd')); ?>;
            --nav-button-hover-text-color: <?php echo prepend_hash(get_theme_mod('nav_button_hover_text_color', '#000000')); ?>;
            --nav-button-min-width: <?php echo get_theme_mod('nav_button-min-width', '17%'); ?>;
            --nav-button-max-height: <?php echo get_theme_mod('nav_button_max_height', '17%'); ?>;
            --nav-wrapper-default-width:  <?php echo get_theme_mod('nav_wrapper_width', '17%'); ?>;
        }
        body {
            font-family: <?php echo get_font_family('body_font'); ?>;
            font-optical-sizing: auto;
            font-style: normal;
            background-color: var(--color-bg);
            color: var(--color-txt);
        }
        #scene {
            perspective: <?php echo get_theme_mod('perspective_scene', '200vmin'); ?>;
            -webkit-perspective: <?php echo get_theme_mod('perspective_scene', '200vmin'); ?>;
            perspective-origin: <?php echo get_theme_mod('perspective_origin_scene', '50% 50%'); ?>;
            -webkit-perspective-origin: <?php echo get_theme_mod('perspective_origin_scene', '50% 50%'); ?>;
            z-index: 1;
        }
        #cube .four {
            background-image: url('<?php echo get_theme_mod('cube_four_bg_image', ''); ?>');
            background-size: <?php echo get_theme_mod('cube_four_bg_size', 'cover'); ?>;
            background-position: top center;
            background-attachment: fixed;  
        }
        .navName {
            color:var(--nav-button-text-color);
        }
        .navButton {
            background-color: var(--nav-button-bg-color);
            font-family: <?php echo get_font_family('button_font'); ?>;
            color: var(--nav-button-text-color);
            padding: var(--nav-button-padding);
            font-size: var(--nav-button-font-size);
            border-style: var(--nav-button-border-style);
            border-color: var(--nav-button-border-color);
            border-width: var(--nav-button-border-width);
            border-radius: var(--nav-button-border-radius);
        }
        .navButton:hover {
        background-color: var(--nav-button-hover-bg-color);
        color: var(--nav-button-hover-text-color);
        }
    </style>
    <?php
}

add_action('wp_head', 'nierto_cube_customizer_css');

function nierto_cube_scripts() {
    wp_deregister_script('jquery');
    wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', false, null, true);
    wp_enqueue_script('jquery');
    wp_enqueue_style('nierto-cube-all-styles', get_template_directory_uri() . '/css/all-styles.css', array(), '1.0.0');
    wp_enqueue_style('nierto-cube-style', get_stylesheet_uri());
    wp_enqueue_script('cube-script', get_template_directory_uri() . '/js/cube.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('config-script', get_template_directory_uri() . '/js/config/config.js.php', array(), null, true);
  
}

add_action('wp_enqueue_scripts', 'nierto_cube_scripts');

function get_theme_logo_details() {
    return array(
        'url' => get_theme_mod('logo_source', ''),
        'width' => get_theme_mod('logo_width', '10vmin')
    );
}

function nierto_cube_sanitize_css($input) {
    // Sanitize the input to ensure proper CSS formatting
    return preg_replace('/[^0-9a-zA-Z\.\-\% \,\(\)\:]/', '', $input);
}
function clear_config_js_cache() {
    $cache_file = get_template_directory() . '/js/config/cached_config.js';
    if (file_exists($cache_file)) {
        unlink($cache_file);
    }
}
add_action('customize_save_after', 'clear_config_js_cache');

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

// new iframe-less way to render content unto one of the cube sides.
function register_cube_face_post_type() {
    register_post_type('cube_face', [
        'labels' => [
            'name' => 'Cube Faces',
            'singular_name' => 'Cube Face',
        ],
        'public' => true,
        'has_archive' => false,
        'supports' => ['title', 'editor', 'custom-fields'],
    ]);
}
add_action('init', 'register_cube_face_post_type');

// adding the admin panel (new august 2024):
function nierto_cube_add_admin_menu() {
    add_menu_page('Nierto Cube Settings', 'Nierto Cube', 'manage_options', 'nierto_cube', 'nierto_cube_settings_page', 'dashicons-admin-generic', 99);
}
add_action('admin_menu', 'nierto_cube_add_admin_menu');

function nierto_cube_register_settings() {
    register_setting('nierto_cube_options', 'nierto_cube_settings');
}
add_action('admin_init', 'nierto_cube_register_settings');

function nierto_cube_settings_page() {
    // Implementation in admin-settings.php
}

// ValKey integration (new August 2024):
function is_valkey_enabled() {
    $settings = get_option('nierto_cube_settings');
    return isset($settings['use_valkey']) && $settings['use_valkey'] && !empty($settings['valkey_ip']);
}

function valkey_get($key) {
    if (!is_valkey_enabled()) {
        return false;
    }
    
    $settings = get_option('nierto_cube_settings');
    $valkey_ip = $settings['valkey_ip'];
    $valkey_port = isset($settings['valkey_port']) ? $settings['valkey_port'] : '6379';
    
    $redis = new Redis();
    try {
        $redis->connect($valkey_ip, $valkey_port);
        return $redis->get($key);
    } catch (Exception $e) {
        error_log('ValKey connection failed: ' . $e->getMessage());
        return false;
    }
}

function valkey_set($key, $value, $ttl = 3600) {
    if (!is_valkey_enabled()) {
        return false;
    }
    
    $settings = get_option('nierto_cube_settings');
    $valkey_ip = $settings['valkey_ip'];
    $valkey_port = isset($settings['valkey_port']) ? $settings['valkey_port'] : '6379';
    
    $redis = new Redis();
    try {
        $redis->connect($valkey_ip, $valkey_port);
        return $redis->setex($key, $ttl, $value);
    } catch (Exception $e) {
        error_log('ValKey connection failed: ' . $e->getMessage());
        return false;
    }
}

function register_face_content_endpoint() {
    register_rest_route('nierto-cube/v1', '/face-content/(?P<slug>[\w-]+)', [
        'methods' => 'GET',
        'callback' => 'get_face_content',
    ]);
}
add_action('rest_api_init', 'register_face_content_endpoint');

function get_face_content($request) {
    $slug = $request['slug'];
    
    // Find the face settings based on the slug
    $settings = get_option('nierto_cube_settings');
    $face_id = null;
    $face_type = null;
    $face_source = null;

    for ($i = 1; $i <= 6; $i++) {
        if (isset($settings['face'.$i.'_slug']) && $settings['face'.$i.'_slug'] === $slug) {
            $face_id = $i;
            $face_type = isset($settings['face'.$i.'_type']) ? $settings['face'.$i.'_type'] : 'post';
            $face_source = isset($settings['face'.$i.'_source']) ? $settings['face'.$i.'_source'] : '';
            break;
        }
    }

    if (!$face_id) {
        return new WP_Error('not_found', 'Face content not found', ['status' => 404]);
    }

    $cache_key = "face_content_{$slug}";
    $cache_time = 604800; // Cache for 1 week

    // Try ValKey first
    if (function_exists('is_valkey_enabled') && is_valkey_enabled()) {
        $cached_content = valkey_get($cache_key);
        if ($cached_content !== false) {
            return json_decode($cached_content, true);
        }
    }

    // If not in ValKey, check WordPress transients
    $cached_content = get_transient($cache_key);
    if ($cached_content !== false) {
        return $cached_content;
    }

    // If not cached, generate content
    if ($face_type === 'page') {
        $content = [
            'type' => 'page',
            'content' => home_url($slug)
        ];
    } else {
        $args = array(
            'name' => $slug,
            'post_type' => 'cube_face',
            'post_status' => 'publish',
            'numberposts' => 1
        );
        $posts = get_posts($args);
        
        if ($posts) {
            $post = $posts[0];
            $content = [
                'type' => 'post',
                'content' => apply_filters('the_content', $post->post_content)
            ];
        } else {
            return new WP_Error('not_found', 'Custom post not found', ['status' => 404]);
        }
    }

    // Cache the content
    if (function_exists('is_valkey_enabled') && is_valkey_enabled()) {
        valkey_set($cache_key, json_encode($content), $cache_time);
    }
    set_transient($cache_key, $content, $cache_time);

    return $content;
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

function add_clear_cache_button() {
    if (isset($_GET['clear_config_cache']) && current_user_can('manage_options')) {
        clear_config_js_cache();
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success"><p>Config cache cleared successfully!</p></div>';
        });
    }
}
add_action('admin_init', 'add_clear_cache_button');

function add_clear_cache_link($wp_admin_bar) {
    if (current_user_can('manage_options')) {
        $wp_admin_bar->add_menu(array(
            'id'    => 'clear_config_cache',
            'title' => 'Clear Config Cache',
            'href'  => add_query_arg('clear_config_cache', '1'),
        ));
    }
}
add_action('admin_bar_menu', 'add_clear_cache_link', 100);

remove_filter('the_content', 'wpautop');