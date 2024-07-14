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
    $wp_customize->add_section('cube_page_names', array(
        'title' => __('Cube Page Names', 'nierto_cube'),
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
        'font_family_menus' => [
            'default' => "'Rubik', sans-serif",
            'label' => 'the default font family for Menu Buttons'
        ],
        'font_family_highlights' => [
            'default' => "'Rubik', sans-serif",
            'label' => 'the default font family for the Highlight'
        ]
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
    // CUBE PAGE NAMES
    for ($i = 0; $i <= 5; $i++) {
    $wp_customize->add_setting("cube_face_page_name_{$i}", array(
        'default' => "Face {$i}",
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
        .body {
            font-family: <?php echo get_theme_mod('font_family', "'Ubuntu', sans-serif"); ?>;
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
            font-family: <?php echo get_theme_mod('font_family_menus', "'Rubik', sans-serif"); ?>;
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
    
    wp_enqueue_style('cube-style', get_template_directory_uri() . '/css/cube.css', array(), '1.0.0');
    wp_enqueue_style('root-style', get_template_directory_uri() . '/css/rootstyle.css', array(), '1.0.0');
    wp_enqueue_style('keyframes-style', get_template_directory_uri() . '/css/keyframes.css', array(), '1.0.0');
    wp_enqueue_style('logo-style', get_template_directory_uri() . '/css/logo.css', array(), '1.0.0');
    wp_enqueue_style('nav-style', get_template_directory_uri() . '/css/navigation.css', array(), '1.0.0');
    wp_enqueue_style('media-style', get_template_directory_uri() . '/css/screensizes.css', array(), '1.0.0');
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

remove_filter('the_content', 'wpautop');