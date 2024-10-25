<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// functionality const files
const FUNCTIONALITY_FILES = [
    'register-options-wp.php',
    'ajax-handler.php',
    'api-funcs.php',
    'cache-version.php',
    'caching-funcs.php',
    'cookies-funcs.php',
    'customizer-css.php',
    'errors-funcs.php',
    'google-funcs.php',
    'hooks-funcs.php',
    'install-funcs.php',
    'manifest-settings.php',
    'metatags-funcs.php',
    'multipost-funcs.php',
    'performance-optimization.php',
    'sanitation-funcs.php',
    'structureddate-funcs.php',
    'valkey-funcs.php',
    'widgets-funcs.php',
    'admin/class-nierto-cube-premium.php',
];
// Include once func for use with the file consts 
function nierto_cube_include_once($array, $path) {
    foreach ($array as $file) {
        $file_path = get_template_directory() . $path . $file;
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
}
nierto_cube_include_once(FUNCTIONALITY_FILES, '/inc/');

add_action('wp_head', function() {
    if (get_theme_mod('enable_pwa', 0)) {
        echo '<link rel="manifest" href="' . home_url('/manifest.json') . '">';
    }
});

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
    wp_enqueue_script('nierto-cube-admin-script', get_template_directory_uri() . '/js/admin-scripts.js', array(), '1.0', true);
    wp_enqueue_script(
    'nierto-cube-performance',
    get_template_directory_uri() . '/js/performance-optimizer.js',
    array(),
    '1.0',
    true
    );
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
    wp_localize_script('nierto-cube-performance', 'niertoCubeData', array(
        'resourceVersion' => get_option('nierto_cube_resource_version', '1.0'),
        'enablePWA' => get_theme_mod('enable_pwa', 0),
        'debugMode' => WP_DEBUG,
        'logEndpoint' => admin_url('admin-ajax.php?action=log_performance'),
        'themeUrl' => get_template_directory_uri()
    ));
    wp_localize_script('cube-script', 'niertoCubeSettings', array(
        'maxZoom' => get_theme_mod('nierto_cube_max_zoom', 90),
        'longPressDuration' => get_theme_mod('nierto_cube_long_press_duration', 1300),
    ));
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

function register_cube_face_post_type() {
    register_post_type('cube_face', [
        'labels' => [
            'name' => 'Cube Faces',
            'singular_name' => 'Cube Face',
        ],
        'public' => true,
        'has_archive' => false,
        'supports' => ['title', 'editor', 'custom-fields', 'thumbnail', 'widgets'],
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'face'],
    ]);
}
add_action('init', 'register_cube_face_post_type');

function add_cube_face_metaboxes() {
    add_meta_box(
        'cube_face_template',
        'Cube Face Template',
        'cube_face_template_callback',
        'cube_face',
        'side',
        'default'
    );
    
    add_meta_box(
        'cube_face_sidebar',
        __('Cube Face Sidebar', 'nierto_cube'),
        'cube_face_sidebar_callback',
        'cube_face',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_cube_face_metaboxes');
function cube_face_position_callback($post) {
    wp_nonce_field('cube_face_position_nonce', 'cube_face_position_nonce');
    $value = get_post_meta($post->ID, '_cube_face_position', true);
    ?>
    <select name="cube_face_position" id="cube_face_position">
        <option value="face0" <?php selected($value, 'face0'); ?>>Face 0 (Top)</option>
        <option value="face1" <?php selected($value, 'face1'); ?>>Face 1 (Front)</option>
        <option value="face2" <?php selected($value, 'face2'); ?>>Face 2 (Right)</option>
        <option value="face3" <?php selected($value, 'face3'); ?>>Face 3 (Back)</option>
        <option value="face4" <?php selected($value, 'face4'); ?>>Face 4 (Left)</option>
        <option value="face5" <?php selected($value, 'face5'); ?>>Face 5 (Bottom)</option>
    </select>
    <?php
}

function save_cube_face_position($post_id) {
    if (!isset($_POST['cube_face_position_nonce']) || !wp_verify_nonce($_POST['cube_face_position_nonce'], 'cube_face_position_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['cube_face_position'])) {
        update_post_meta($post_id, '_cube_face_position', sanitize_text_field($_POST['cube_face_position']));
    }
}
add_action('save_post_cube_face', 'save_cube_face_position');

function cube_face_template_callback($post) {
    wp_nonce_field('cube_face_template_nonce', 'cube_face_template_nonce');
    $template = get_post_meta($post->ID, '_cube_face_template', true);
    ?>
    <select name="cube_face_template" id="cube_face_template">
        <option value="standard" <?php selected($template, 'standard'); ?>>Standard Template</option>
        <option value="multi_post" <?php selected($template, 'multi_post'); ?>>Multi-Post Template</option>
        <option value="settings" <?php selected($template, 'settings'); ?>>Settings Template</option>
    </select>
    <?php
}

function cube_face_sidebar_callback($post) {
    // Output the sidebar
    dynamic_sidebar('cube-face-sidebar');
}

function save_cube_face_template($post_id) {
    if (!isset($_POST['cube_face_template_nonce']) || !wp_verify_nonce($_POST['cube_face_template_nonce'], 'cube_face_template_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['cube_face_template'])) {
        update_post_meta($post_id, '_cube_face_template', sanitize_text_field($_POST['cube_face_template']));
    }
}
add_action('save_post_cube_face', 'save_cube_face_template');

function nierto_cube_register_settings() {
    register_setting('nierto_cube_options', 'nierto_cube_settings');
}
add_action('admin_init', 'nierto_cube_register_settings');

function nierto_cube_settings_page() {
    require_once get_template_directory() . '/admin-settings.php';
}


function register_face_content_endpoint() {
    register_rest_route('nierto-cube/v1', '/face-content/(?P<post_type>[\w-]+)/(?P<slug>[\w-]+)', [
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
    $post_type = $request['post_type'];
    $slug = $request['slug'];
    $cache_key = "face_content_{$post_type}_{$slug}";

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
                'post_type' => $post_type,
                'post_status' => 'publish',
                'numberposts' => 1
            ];
            $posts = get_posts($args);

            if ($posts) {
                $post = $posts[0];
                $content = [
                    'type' => 'post',
                    'content' => apply_filters('the_content', $post->post_content),
                    'title' => $post->post_title
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