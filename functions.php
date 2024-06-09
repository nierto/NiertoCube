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
    // PAGE NAMES FOR SIDES
    $wp_customize->add_section('cube_page_names', array(
        'title' => __('Cube Page Names', 'nierto_cube'),
        'priority' => 161,
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
    ));
     // NAV TEXT SETTINGS
    $wp_customize->add_section('nav_texts', array(
        'title' => __('Navigation Texts', 'nierto_cube'),
        'priority' => 198,
    ));
    // NAV BUTTON STYLING
    $wp_customize->add_section('nav_button_styling', array(
        'title' => __('Navigation Button Styling', 'nierto_cube'),
        'priority' => 200,
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
        'color_background' => '#F97162',
        'color_text' => '#F97162',
        'color_header' => '#FEFEF9',
        'color_border' => '#F5F9E9',
        'color_highlight' => '#F5F9E9',
        'color_background_button' => '#F5F9E9',
        'color_text_button' => '#F5F9E9',
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
        'translate_x_scene' => [
            'default' => '-3vmin',
            'label' => 'Translate X for Scene'
        ],
        'translate_y_scene' => [
            'default' => '6vmin',
            'label' => 'Translate Y for Scene'
        ],
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
        ],
        'default_margin' => [
            'default' => '0.618033vmin auto',
            'label' => 'The default Margin (for the cube and other items!)'
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
    for ($i = 1; $i <= 3; $i++) {
        $wp_customize->add_setting("cube_face_page_name_{$i}", array(
            'default' => "Page Name {$i}",
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));
        $wp_customize->add_control("cube_face_page_name_{$i}", array(
            'label' => __("Face {$i} Page Name", 'nierto_cube'),
            'section' => 'cube_page_names',
            'type' => 'text',
            'settings' => "cube_face_page_name_{$i}",
        ));
    }
    //SECTION: LOGO
    // Logo settings
    for ($i = 1; $i <= 2; $i++) {
        $wp_customize->add_setting("logo_{$i}", array(
            'default' => "124px",
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));
        $wp_customize->add_control("logo_{$i}", array(
            'label' => __("Logo Setting {$i}", 'nierto_cube'),
            'section' => 'logo',
            'type' => 'text',
            'settings' => "logo_{$i}",
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
    //SECTION: FONT
    $font_settings = [
        'font_family' => [
            'default' => "'Ubuntu', sans-serif",
            'label' => 'the default font-family for the body.'
        ],
        'font_family_button' => [
            'default' => "'Rubik', sans-serif",
            'label' => 'the default font family for the buttons'
        ],
     ];
    foreach ($font_settings as $id => $values) {
        $wp_customize->add_setting($id, [
            'default' => $values['default'],
            'transport' => 'refresh',
            'sanitize_callback' => 'sanitize_text_field'
        ]);
        $wp_customize->add_control($id, [
            'label' => __($values['label'], 'nierto_cube'),
            'section' => 'font',
            'type' => 'text'
        ]);
    }
    // SECTION: NAV TEXTS
    $nav_texts = [
        'nav_text_face0' => 'WHAT?',
        'nav_text_face1' => 'WHY?',
        'nav_text_face2' => 'HOW?',
        'nav_text_face3' => 'WHO?',
        'nav_text_face4' => 'TERMS',
        'nav_text_face5' => 'STORIES',
        'nav_text_face6' => 'CONTACT',
        'nav_text_face7' => 'RATES',
    ];
    foreach ($nav_texts as $setting_id => $default_text) {
        $wp_customize->add_setting($setting_id, array(
            'default' => $default_text,
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'refresh',
        ));
        $wp_customize->add_control($setting_id, array(
            'label' => __(ucfirst(str_replace('_', ' ', $setting_id)), 'nierto_cube'),
            'section' => 'nav_texts',
            'type' => 'text',
            'settings' => $setting_id,
        ));
    }
    // Nav button background color
    $wp_customize->add_setting('nav_button_bg_color', array(
        'default' => '#ffffff',
        'transport' => 'refresh',
        'sanitize_callback' => 'nierto_cube_sanitize_css'
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_button_bg_color', array(
        'label' => __('Nav Button Background Color', 'nierto_cube'),
        'section' => 'nav_button_styling',
        'settings' => 'nav_button_bg_color',
    )));

    // Nav button text color
    $wp_customize->add_setting('nav_button_text_color', array(
        'default' => '#000000',
        'transport' => 'refresh',
        'sanitize_callback' => 'nierto_cube_sanitize_css'
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_button_text_color', array(
        'label' => __('Nav Button Text Color', 'nierto_cube'),
        'section' => 'nav_button_styling',
        'settings' => 'nav_button_text_color',
    )));

    // Nav button padding
    $wp_customize->add_setting('nav_button_padding', array(
        'default' => '10px 20px',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('nav_button_padding', array(
        'label' => __('Nav Button Padding', 'nierto_cube'),
        'section' => 'nav_button_styling',
        'type' => 'text',
    ));

    // Nav button margin
    $wp_customize->add_setting('nav_button_margin', array(
        'default' => '10px',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('nav_button_margin', array(
        'label' => __('Nav Button Margin', 'nierto_cube'),
        'section' => 'nav_button_styling',
        'type' => 'text',
    ));

    // Nav button font size
    $wp_customize->add_setting('nav_button_font_size', array(
        'default' => '16px',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('nav_button_font_size', array(
        'label' => __('Nav Button Font Size', 'nierto_cube'),
        'section' => 'nav_button_styling',
        'type' => 'text',
    ));

     // Nav button border style
    $wp_customize->add_setting('nav_button_border_style', array(
        'default' => 'solid',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('nav_button_border_style', array(
    'label' => __('Nav Button Border Style', 'nierto_cube'),
    'section' => 'nav_button_styling',
    'type' => 'text',
    ));

    // Nav button border color
    $wp_customize->add_setting('nav_button_border_color', array(
        'default' => '#000000',
        'transport' => 'refresh',
        'sanitize_callback' => 'nierto_cube_sanitize_css'
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_button_border_color', array(
        'label' => __('Nav Button Border Color', 'nierto_cube'),
        'section' => 'nav_button_styling',
        'settings' => 'nav_button_border_color',
    )));

    // Nav button border width
    $wp_customize->add_setting('nav_button_border_width', array(
        'default' => '1px',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('nav_button_border_width', array(
        'label' => __('Nav Button Border Width', 'nierto_cube'),
        'section' => 'nav_button_styling',
        'type' => 'text',
    ));

        // Nav button border radius
    $wp_customize->add_setting('nav_button_border_radius', array(
        'default' => '20%',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    $wp_customize->add_control('nav_button_border_radius', array(
        'label' => __('Nav Button Border Radius', 'nierto_cube'),
        'section' => 'nav_button_styling',
        'type' => 'text',
    ));

    // Nav button hover background color
    $wp_customize->add_setting('nav_button_hover_bg_color', array(
        'default' => '#dddddd',
        'transport' => 'refresh',
        'sanitize_callback' => 'nierto_cube_sanitize_css'
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_button_hover_bg_color', array(
        'label' => __('Nav Button Hover Background Color', 'nierto_cube'),
        'section' => 'nav_button_styling',
        'settings' => 'nav_button_hover_bg_color',
    )));

    // Nav button hover text color
    $wp_customize->add_setting('nav_button_hover_text_color', array(
        'default' => '#000000',
        'transport' => 'refresh',
        'sanitize_callback' => 'nierto_cube_sanitize_css'
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_button_hover_text_color', array(
        'label' => __('Nav Button Hover Text Color', 'nierto_cube'),
        'section' => 'nav_button_styling',
        'settings' => 'nav_button_hover_text_color',
    )));
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
            --gradcolor1: <?php echo prepend_hash(get_theme_mod('grad_color1', '#ee7752')); ?>;
            --gradcolor2: <?php echo prepend_hash(get_theme_mod('grad_color2', '#e73c7e')); ?>;
            --gradcolor3: <?php echo prepend_hash(get_theme_mod('grad_color3', '#23a6d5')); ?>;
            --gradcolor4: <?php echo prepend_hash(get_theme_mod('grad_color4', '#23d5ab')); ?>;
            --color-bg: <?php echo prepend_hash(get_theme_mod('color_background', '#F97162')); ?>;
            --color-txt: <?php echo prepend_hash(get_theme_mod('color_text', '#F97162')); ?>;
            --color-header: <?php echo prepend_hash(get_theme_mod('color_header', '#FEFEF9')); ?>;
            --color-border: <?php echo prepend_hash(get_theme_mod('color_border', '#F5F9E9')); ?>;
            --color-highlight: <?php echo prepend_hash(get_theme_mod('color_highlight', '#F5F9E9')); ?>;
            --color-bg-button: <?php echo prepend_hash(get_theme_mod('color_background_button', '#F5F9E9')); ?>;
            --color-txt-button: <?php echo prepend_hash(get_theme_mod('color_text_button', '#F5F9E9')); ?>;
            --default-cubeheight: <?php echo get_theme_mod('default_cubeheight', '80vmin'); ?>;
            --default-cubewidth: <?php echo get_theme_mod('default_cubewidth', '80vmin'); ?>;
            --default-margin: <?php echo get_theme_mod('default_margin', '0.618033vmin auto'); ?>; 
            --default-padding: <?php echo get_theme_mod('default_padding', '1.618033vmin'); ?>; 
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
        }
        #scene {
            transform: translate(<?php echo get_theme_mod('translate_x_scene', '-3vmin'); ?>, <?php echo get_theme_mod('translate_y_scene', '6vmin'); ?>);
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
        .body {
            font-family: <?php echo get_theme_mod('font_family', "'Rubik', sans-serif"); ?>;
            font-optical-sizing: auto;
            font-style: normal;
            background-color: var(--color-bg);
            color: var(--color-txt);
        }
        .navButton {
            background-color: var(--nav-button-bg-color);
            color: var(--nav-button-text-color);
            padding: var(--nav-button-padding);
            margin: var(--nav-button-margin);
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
    wp_enqueue_style('cube-style', get_template_directory_uri() . '/css/cube.css', array(), '1.0.0');
    wp_enqueue_style('root-style', get_template_directory_uri() . '/css/rootstyle.css', array(), '1.0.0');
    wp_enqueue_style('keyframes-style', get_template_directory_uri() . '/css/keyframes.css', array(), '1.0.0');
    wp_enqueue_style('screensizes-style', get_template_directory_uri() . '/css/screensizes.css', array(), '1.0.0');
    wp_enqueue_style('nav-style', get_template_directory_uri() . '/css/navigation.css', array(), '1.0.0');
    wp_enqueue_style('nierto-cube-style', get_stylesheet_uri());
    wp_enqueue_script('config-script', get_template_directory_uri() . '/js/config/config.js.php', array(), null, true);
    wp_enqueue_script('cube-script', get_template_directory_uri() . '/js/cube.js', array('jquery'), '1.0.0', true);
}

add_action('wp_enqueue_scripts', 'nierto_cube_scripts');


function nierto_cube_sanitize_css($input) {
    // Sanitize the input to ensure proper CSS formatting
    return preg_replace('/[^0-9a-zA-Z\.\-\% \,\(\)\:]/', '', $input);
}

remove_filter('the_content', 'wpautop');

