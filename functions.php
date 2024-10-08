<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include custom functionality
$functionality_files = [
    'ajax-handler.php',
    'cache-version.php',
    'caching-functionality.php',
    'cookies-functionality.php',
    'errors-functionality.php',
    'google-functionality.php',
    'metatags-functionality.php',
    'sanitation-functionality.php',
    'structureddate-functionality.php',
    'valkey-functionality.php',
];

foreach ($functionality_files as $file) {
    $file_path = get_template_directory() . '/inc/' . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}

add_action('wp_head', function() {
    echo '<link rel="manifest" href="' . get_template_directory_uri() . '/manifest.php">';
});

function nierto_cube_customize_register($wp_customize) {
    // Registering sections
    // COLORS
    $wp_customize->add_section('colors', array(
        'title' => __('Colors', 'nierto_cube'),
        'description' => __('Customize the colors of the theme.', 'nierto_cube'),
        'priority' => 23,
    ));
    // VALKEY INTEGRATION - JWZ
    $wp_customize->add_section('nierto_cube_valkey', array(
    'title' => __('ValKey Settings', 'nierto_cube'),
    'priority' => 29,
    ));
    $wp_customize->add_section('nierto_cube_pwa', array(
    'title' => __('PWA Settings', 'nierto_cube'),
    'priority' => 35,
    ));
    // CUBE SETTINGS
    $wp_customize->add_section('cube_settings', array(
        'title' => __('Cube Settings', 'nierto_cube'),
        'priority' => 150,
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
            'sanitize_callback' => 'nierto_cube_sanitize_hex_color'
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
            'sanitize_callback' => 'nierto_cube_sanitize_hex_color'
        ]);
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $setting_id, array(
            'label' => __(ucfirst(str_replace('_', ' ', $setting_id)), 'nierto_cube'),
            'section' => 'colors',
            'settings' => $setting_id,
        )));
    }
    // SECTION: VALKEY INTEGRATION
    $wp_customize->add_setting('use_valkey', array(
    'default' => 0,
    'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('use_valkey', array(
        'label' => __('Use ValKey', 'nierto_cube'),
        'section' => 'nierto_cube_valkey',
        'type' => 'checkbox',
    ));
    $wp_customize->add_setting('valkey_ip', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('valkey_ip', array(
        'label' => __('ValKey IP Address', 'nierto_cube'),
        'section' => 'nierto_cube_valkey',
        'type' => 'text',
    ));
    $wp_customize->add_setting('valkey_port', array(
        'default' => '6379',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('valkey_port', array(
        'label' => __('ValKey Port', 'nierto_cube'),
        'section' => 'nierto_cube_valkey',
        'type' => 'number',
    ));

    // SECTION: PWA SETTINGS

    $wp_customize->add_setting('enable_pwa', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('enable_pwa', array(
        'label' => __('Enable PWA Functionality', 'nierto_cube'),
        'section' => 'nierto_cube_pwa',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('pwa_icon_192', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'pwa_icon_192', array(
        'label' => __('PWA Icon (192x192)', 'nierto_cube'),
        'section' => 'nierto_cube_pwa',
        'settings' => 'pwa_icon_192',
    )));

    $wp_customize->add_setting('pwa_icon_512', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'pwa_icon_512', array(
        'label' => __('PWA Icon (512x512)', 'nierto_cube'),
        'section' => 'nierto_cube_pwa',
        'settings' => 'pwa_icon_512',
    )));
    $wp_customize->add_setting('pwa_short_name', array(
        'default' => 'NCube',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('pwa_short_name', array(
        'label' => __('PWA Short Name', 'nierto_cube'),
        'section' => 'nierto_cube_pwa',
        'type' => 'text',
    ));

    $wp_customize->add_setting('pwa_background_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'pwa_background_color', array(
        'label' => __('PWA Background Color', 'nierto_cube'),
        'section' => 'nierto_cube_pwa',
    )));

    $wp_customize->add_setting('pwa_theme_color', array(
        'default' => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'pwa_theme_color', array(
        'label' => __('PWA Theme Color', 'nierto_cube'),
        'section' => 'nierto_cube_pwa',
    )));

    $wp_customize->add_setting('pwa_install_banner', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'pwa_install_banner', array(
        'label' => __('PWA Install Banner Image', 'nierto_cube'),
        'description' => __('Upload an image to use as the install banner. If not set, a default banner will be used.', 'nierto_cube'),
        'section' => 'nierto_cube_pwa',
        'settings' => 'pwa_install_banner',
    )));
    
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
            'sanitize_callback' => 'nierto_cube_sanitize_css_value'
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
            'default' => 'post',
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

function nierto_cube_enqueue_assets() {
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();

    // Enqueue styles
    wp_enqueue_style('nierto-cube-all-styles', $theme_uri . '/css/all-styles.css', array(), filemtime($theme_dir . '/css/all-styles.css'));
    wp_enqueue_style('nierto-cube-style', get_stylesheet_uri(), array(), filemtime(get_stylesheet_directory() . '/style.css'));

    // Enqueue scripts
    wp_enqueue_script('utils-script', $theme_uri . '/js/utils.js', array(), filemtime($theme_dir . '/js/utils.js'), true);
    wp_enqueue_script('config-script', $theme_uri . '/js/config.js', array('utils-script'), filemtime($theme_dir . '/js/config.js'), true);
    wp_enqueue_script('cookie-script', $theme_uri . '/js/cookies.js', array('utils-script'), filemtime($theme_dir . '/js/cookies.js'), true);
    wp_enqueue_script('cube-script', $theme_uri . '/js/cube.js', array('utils-script', 'config-script'), filemtime($theme_dir . '/js/cube.js'), true);
    
    // Conditional scripts
    if ((is_front_page()) && (get_theme_mod('enable_pwa', 1))) {
        wp_enqueue_script('pwa-script', $theme_uri . '/js/pwa.js', array('utils-script'), filemtime($theme_dir . '/js/pwa.js'), true);
        wp_script_add_data('pwa-script', 'async', true);
        wp_localize_script('pwa-script', 'swData', array(
            'themeUrl' => $theme_uri . '/'
        ));
        wp_localize_script('pwa-script', 'niertoCubePWA', array(
            'installBanner' => get_theme_mod('pwa_install_banner', ''),
        ));
    }

    $cube_faces = array();
    for ($i = 1; $i <= 6; $i++) {
        $cube_faces[] = array(
            'buttonText' => get_theme_mod("cube_face_{$i}_text", "Face {$i}"),
            'urlSlug' => get_theme_mod("cube_face_{$i}_slug", "face-{$i}"),
            'facePosition' => get_theme_mod("cube_face_{$i}_position", "face" . ($i - 1)),
            'contentType' => get_theme_mod("cube_face_{$i}_type", "page"),
        );
    }

    wp_localize_script('cube-script', 'niertoCubeCustomizer', array(
        'cubeFaces' => $cube_faces
    ));

    // Localize scripts
    wp_localize_script('config-script', 'niertoCubeData', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('nierto_cube_ajax')
    ));

    wp_localize_script('utils-script', 'themeData', array(
        'themeName' => wp_get_theme()->get_stylesheet()
    ));
}
add_action('wp_enqueue_scripts', 'nierto_cube_enqueue_assets');

function get_theme_logo_details() {
    return array(
        'url' => get_theme_mod('logo_source', ''),
        'width' => get_theme_mod('logo_width', '10vmin')
    );
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


function nierto_cube_register_settings() {
    register_setting('nierto_cube_options', 'nierto_cube_settings');
}
add_action('admin_init', 'nierto_cube_register_settings');

function nierto_cube_settings_page() {
    require_once get_template_directory() . '/admin-settings.php';
}


function register_face_content_endpoint() {
    register_rest_route('nierto-cube/v1', '/face-content/(?P<slug>[\w-]+)', [
        'methods' => 'GET',
        'callback' => 'get_face_content',
    ]);
}
add_action('rest_api_init', 'register_face_content_endpoint');


function nierto_cube_get_face_content() {
    $cache_key = 'nierto_cube_face_content';
    
    // Try to get the cached content
    $faces = get_cached_content($cache_key);
    
    if ($faces === false) {
        $faces = [];
        for ($i = 1; $i <= 6; $i++) {
            $faces[] = array(
                'buttonText' => get_theme_mod("cube_face_{$i}_text", "Face {$i}"),
                'urlSlug' => get_theme_mod("cube_face_{$i}_slug", "face-{$i}"),
                'facePosition' => get_theme_mod("cube_face_{$i}_position", "face" . ($i - 1)),
                'contentType' => get_theme_mod("cube_face_{$i}_type", "page"),
            );
        }
        
        // Cache the content for 1 hour (3600 seconds)
        set_cached_content($cache_key, $faces, 3600);
    }
    
    return $faces;
}

function get_face_content($request) {
    $slug = $request['slug'];
    $cache_key = "face_content_{$slug}";

    // Try to get the cached content
    $content = get_cached_content($cache_key);

    if ($content === false) {
        // Get the face settings directly from theme mods
        $face_settings = null;
        for ($i = 1; $i <= 6; $i++) {
            if (get_theme_mod("cube_face_{$i}_slug") === $slug) {
                $face_settings = [
                    'type' => get_theme_mod("cube_face_{$i}_type", "page"),
                    'slug' => $slug,
                    'position' => get_theme_mod("cube_face_{$i}_position", "face" . ($i - 1)),
                ];
                break;
            }
        }

        if (!$face_settings) {
            return new WP_Error('not_found', 'Face content not found', ['status' => 404]);
        }

        if ($face_settings['type'] === 'page') {
            $page = get_page_by_path($slug);
            if ($page) {
                $content = [
                    'type' => 'page',
                    'content' => get_permalink($page->ID)
                ];
            } else {
                return new WP_Error('not_found', 'Page not found', ['status' => 404]);
            }
        } else {
            $args = [
                'name' => $slug,
                'post_type' => 'cube_face',
                'post_status' => 'publish',
                'numberposts' => 1
            ];
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

        // Cache the content for 1 hour (3600 seconds)
        set_cached_content($cache_key, $content, 3600);
    }

    return $content;
}
// Function to clear the cache when a cube face is updated
function clear_face_content_cache($post_id) {
    if (get_post_type($post_id) === 'cube_face') {
        $slug = get_post_field('post_name', $post_id);
        clear_cached_content("face_content_{$slug}");
        clear_cached_content('nierto_cube_face_content');
    }
}
add_action('save_post', 'clear_face_content_cache');
add_action('delete_post', 'clear_face_content_cache');

// Clear cache when theme mods are updated
function clear_face_content_cache_on_customize_save() {
    clear_cached_content('nierto_cube_face_content');
}
add_action('customize_save_after', 'clear_face_content_cache_on_customize_save');

function nierto_cube_get_image_with_alt($attachment_id, $size = 'full', $icon = false, $attr = '') {
    // Get the image HTML
    $html = wp_get_attachment_image($attachment_id, $size, $icon, $attr);
    
    // If no image is found, return an empty string
    if (!$html) {
        return '';
    }
    
    // Get the alt text
    $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
    
    // If no alt text is set, use the site name as a fallback
    if (empty($alt)) {
        $alt = get_bloginfo('name') . ' logo';
    }
    
    // Add or update the alt attribute
    $html = preg_replace('/alt=(["\']).*?\1/', sprintf('alt=$1%s$1', esc_attr($alt)), $html);
    
    return $html;
}

function nierto_cube_remove_wpautop_for_cube_faces($content) {
    global $post;
    if ($post->post_type == 'cube_face') {
        remove_filter('the_content', 'wpautop');
    }
    return $content;
}
add_filter('the_content', 'nierto_cube_remove_wpautop_for_cube_faces', 0);