<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Prevent duplicate class definition
if (!class_exists('NiertoCube_Performance')) {

class NiertoCube_Performance {
    private static $instance = null;
    private $cache_ttl = 3600;
    private $static_extensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'ico', 'svg', 'woff', 'woff2'];
    private $removed_filters = [];
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Early optimizations (priority 1)
        add_action('init', [$this, 'early_optimizations'], 1);
        
        // Standard priority optimizations
        if (!has_action('init', [$this, 'cleanup_headers'])) {
            add_action('init', [$this, 'cleanup_headers'], 10);
        }
        
        // Late optimizations (priority 999)
        add_action('init', [$this, 'late_optimizations'], 999);
        
        // REST API optimizations
        add_action('rest_api_init', [$this, 'register_cache_endpoints']);
        
        // Header optimizations
        if (!has_filter('wp_headers', [$this, 'optimize_asset_headers'])) {
            add_filter('wp_headers', [$this, 'optimize_asset_headers'], 10, 2);
        }
        
        // Cache headers
        if (!has_action('send_headers', [$this, 'add_cache_headers'])) {
            add_action('send_headers', [$this, 'add_cache_headers']);
        }

        // HTML optimization
        add_filter('template_include', [$this, 'buffer_start']);
        add_action('shutdown', [$this, 'buffer_end']);
        
        // Asset optimization
        add_action('wp_enqueue_scripts', [$this, 'optimize_assets'], 999);
        
        // Save optimized content to ValKey/transients
        add_action('save_post', [$this, 'cache_post_content'], 10, 3);
        
        // Initialize memory tracking
        $this->init_memory_tracking();
    }

    public function early_optimizations() {
        // Disable emoji support
        $this->disable_emojis();
        
        // Remove query strings from static resources
        add_filter('script_loader_src', [$this, 'remove_query_strings'], 15);
        add_filter('style_loader_src', [$this, 'remove_query_strings'], 15);
        
        // Disable XML-RPC
        add_filter('xmlrpc_enabled', '__return_false');
        
        // Remove shortlink
        remove_action('wp_head', 'wp_shortlink_wp_head');
        
        // Disable auto-updates notification
        remove_action('admin_init', '_maybe_update_core');
        remove_action('admin_init', '_maybe_update_plugins');
        remove_action('admin_init', '_maybe_update_themes');
    }

    public function late_optimizations() {
        // Remove unnecessary REST API endpoints
        $this->cleanup_rest_api();
        
        // Disable REST API if not needed
        if (!is_admin()) {
            $this->limit_rest_api_access();
        }
        
        // Optimize database queries
        $this->optimize_queries();
    }

    private function init_memory_tracking() {
        if (WP_DEBUG) {
            add_action('all', function() {
                $current_filter = current_filter();
                $memory = memory_get_usage();
                error_log("Memory usage at $current_filter: " . round($memory / 1024 / 1024, 2) . "MB");
            });
        }
    }

    public function cleanup_headers() {
        // Remove unnecessary headers
        header_remove('X-Powered-By');
        header_remove('X-Pingback');
        header_remove('Server');
        
        // Remove WP version
        remove_action('wp_head', 'wp_generator');
        
        // Remove wlwmanifest link
        remove_action('wp_head', 'wlwmanifest_link');
        
        // Remove RSD link
        remove_action('wp_head', 'rsd_link');
        
        // Remove REST API links if not needed
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('template_redirect', 'rest_output_link_header', 11);
    }

    public function register_cache_endpoints() {
        register_rest_route('niertocube/v1', '/content/(?P<slug>[a-zA-Z0-9-]+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_cached_content'],
            'permission_callback' => '__return_true',
            'args' => [
                'slug' => [
                    'required' => true,
                    'validate_callback' => function($param) {
                        return is_string($param) && preg_match('/^[a-zA-Z0-9-]+$/', $param);
                    }
                ]
            ]
        ]);
    }

    public function get_cached_content($request) {
        $slug = $request->get_param('slug');
        $cache_key = 'content_' . $slug;
        
        // Check browser cache first
        $etag = $request->get_header('If-None-Match');
        if ($etag) {
            $stored_etag = $this->get_content_etag($slug);
            if ($stored_etag && $stored_etag === $etag) {
                return new WP_REST_Response(null, 304);
            }
        }
        
        // Try ValKey first
        if (function_exists('is_valkey_enabled') && is_valkey_enabled()) {
            $content = valkey_get($cache_key);
            if ($content !== false) {
                return $this->prepare_cached_response($content);
            }
        }

        // Generate content if not cached
        $content = $this->generate_content($slug);
        if (is_wp_error($content)) {
            return $content;
        }

        // Cache the content
        $this->cache_content($cache_key, $content);

        return $this->prepare_cached_response($content);
    }

    private function prepare_cached_response($content) {
        $json = json_encode($content);
        $etag = md5($json);
        
        return new WP_REST_Response(json_decode($json), 200, [
            'Cache-Control' => 'public, max-age=' . $this->cache_ttl,
            'ETag' => '"' . $etag . '"',
            'X-Cache' => 'HIT'
        ]);
    }

    private function generate_content($slug) {
        $post = get_page_by_path($slug, OBJECT, ['post', 'page', 'cube_face']);
        if (!$post) {
            return new WP_Error('not_found', 'Content not found', ['status' => 404]);
        }

        return [
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $this->optimize_post_content($post->post_content),
            'type' => $post->post_type,
            'modified' => $post->post_modified_gmt
        ];
    }

    private function optimize_post_content($content) {
        // Remove empty paragraphs
        $content = preg_replace('/<p>\s*<\/p>/i', '', $content);
        
        // Optimize image tags
        $content = preg_replace_callback('/<img[^>]+>/i', [$this, 'optimize_image_tag'], $content);
        
        // Add loading="lazy" to iframes
        $content = preg_replace('/<iframe/i', '<iframe loading="lazy"', $content);
        
        return apply_filters('the_content', $content);
    }

    private function optimize_image_tag($matches) {
        $img = $matches[0];
        
        // Add loading="lazy" if not present
        if (strpos($img, 'loading=') === false) {
            $img = str_replace('<img', '<img loading="lazy"', $img);
        }
        
        // Add decoding="async" if not present
        if (strpos($img, 'decoding=') === false) {
            $img = str_replace('<img', '<img decoding="async"', $img);
        }
        
        return $img;
    }

    public function optimize_asset_headers($headers) {
        if (is_admin()) {
            return $headers;
        }

        $uri = $_SERVER['REQUEST_URI'];
        $extension = strtolower(pathinfo($uri, PATHINFO_EXTENSION));

        if (in_array($extension, $this->static_extensions)) {
            $headers['Cache-Control'] = 'public, max-age=31536000'; // 1 year
            $headers['Pragma'] = 'public';
            $headers['X-Content-Type-Options'] = 'nosniff';
        }

        return $headers;
    }

    public function add_cache_headers() {
        if (!is_admin() && !is_user_logged_in()) {
            header('Cache-Control: public, max-age=' . $this->cache_ttl);
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
        }
    }

    public function optimize_assets() {
        global $wp_scripts, $wp_styles;
        
        // Optimize script loading
        foreach ($wp_scripts->registered as $handle => $script) {
            // Add defer to non-critical scripts
            if (!in_array($handle, ['jquery'])) {
                $wp_scripts->registered[$handle]->extra['defer'] = true;
            }
        }
        
        // Optimize style loading
        foreach ($wp_styles->registered as $handle => $style) {
            if (!is_admin()) {
                // Add media="print" and onload for non-critical CSS
                if (!in_array($handle, ['nierto-cube-critical'])) {
                    $wp_styles->registered[$handle]->extra['media'] = 'print';
                    $wp_styles->registered[$handle]->extra['onload'] = "this.media='all'";
                }
            }
        }
    }

    public function buffer_start($template) {
        ob_start([$this, 'process_output']);
        return $template;
    }

    public function buffer_end() {
        if (ob_get_level() > 0) {
            ob_end_flush();
        }
    }

    public function process_output($buffer) {
        if (is_admin() || is_user_logged_in()) {
            return $buffer;
        }

        // Minify HTML
        $buffer = $this->minify_html($buffer);
        
        // Optimize resource hints
        $buffer = $this->optimize_resource_hints($buffer);
        
        return $buffer;
    }

    private function minify_html($html) {
        // Remove comments (except IE conditionals)
        $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);
        
        // Remove whitespace
        $html = preg_replace('/\s+/', ' ', $html);
        
        // Remove whitespace around HTML tags
        $html = preg_replace('/>\s+</', '><', $html);
        
        return trim($html);
    }

    private function optimize_resource_hints($html) {
        // Add preconnect for external resources
        $preconnect = '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
        $html = str_replace('</head>', $preconnect . '</head>', $html);
        
        return $html;
    }

    private function cleanup_rest_api() {
        // Remove unnecessary endpoints
        add_filter('rest_endpoints', function($endpoints) {
            if (isset($endpoints['/wp/v2/users'])) {
                unset($endpoints['/wp/v2/users']);
            }
            if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
                unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
            }
            return $endpoints;
        });
    }

    private function limit_rest_api_access() {
        add_filter('rest_authentication_errors', function($result) {
            if (!empty($result)) {
                return $result;
            }
            if (!is_user_logged_in()) {
                return new WP_Error('rest_not_logged_in', 'You are not currently logged in.', ['status' => 401]);
            }
            return $result;
        });
    }

    private function optimize_queries() {
        // Disable post revisions
        if (!defined('WP_POST_REVISIONS')) {
            define('WP_POST_REVISIONS', false);
        }
        
        // Disable auto drafts
        add_action('wp_insert_post_data', function($data) {
            if ($data['post_status'] == 'auto-draft') {
                $data['post_status'] = 'draft';
            }
            return $data;
        });
    }

    private function disable_emojis() {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }

    public function remove_query_strings($src) {
        if (strpos($src, '?ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }

    public function cache_post_content($post_id, $post, $update) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if ($post->post_status !== 'publish') {
            return;
        }
        
        $content = $this->generate_content($post->post_name);
        $cache_key = 'content_' . $post->post_name;
        
        $this->cache_content($cache_key, $content);
    }

    private function cache_content($key, $content) {
        if (function_exists('is_valkey_enabled') && is_valkey_enabled()) {
            valkey_set($key, json_encode($content), $this->cache_ttl);
        } else {
            set_transient($key, $content, $this->cache_ttl);
        }
    }

 private function get_content_etag($slug) {
        $cache_key = 'etag_' . $slug;
        if (function_exists('is_valkey_enabled') && is_valkey_enabled()) {
            return valkey_get($cache_key);
        }
        return get_transient($cache_key);
    }

    private function store_content_etag($slug, $etag) {
        $cache_key = 'etag_' . $slug;
        if (function_exists('is_valkey_enabled') && is_valkey_enabled()) {
            valkey_set($cache_key, $etag, $this->cache_ttl);
        } else {
            set_transient($cache_key, $etag, $this->cache_ttl);
        }
    }

    public function clear_cache() {
        if (function_exists('is_valkey_enabled') && is_valkey_enabled()) {
            $this->clear_valkey_cache();
        } else {
            $this->clear_transient_cache();
        }
        
        // Clear static file cache if exists
        $cache_dir = WP_CONTENT_DIR . '/cache/nierto-cube';
        if (is_dir($cache_dir)) {
            $this->recursive_remove_dir($cache_dir);
        }
        
        // Clear browser caches via version increment
        $this->increment_resource_version();
    }

    private function clear_valkey_cache() {
        global $wpdb;
        $prefix = 'content_';
        if (function_exists('valkey_delete_by_prefix')) {
            try {
                valkey_delete_by_prefix($prefix);
            } catch (Exception $e) {
                error_log('ValKey cache clearing failed: ' . $e->getMessage());
            }
        }
    }

    private function clear_transient_cache() {
        global $wpdb;
        $prefix = '_transient_content_';
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                $wpdb->esc_like($prefix) . '%',
                $wpdb->esc_like('_transient_timeout_content_') . '%'
            )
        );
    }

    private function recursive_remove_dir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->recursive_remove_dir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    private function increment_resource_version() {
        $version = get_option('nierto_cube_resource_version', 1);
        update_option('nierto_cube_resource_version', $version + 1);
    }

    public function get_performance_metrics() {
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }

        $metrics = [
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'cache_hits' => $this->get_cache_hit_rate(),
            'query_count' => $GLOBALS['wpdb']->num_queries,
            'load_time' => timer_stop(),
        ];

        return $metrics;
    }

    private function get_cache_hit_rate() {
        static $hits = 0;
        static $misses = 0;

        if (function_exists('is_valkey_enabled') && is_valkey_enabled()) {
            // Get ValKey stats if available
            try {
                $redis = new Redis();
                $settings = get_valkey_settings();
                $redis->connect($settings['valkey_ip'], $settings['valkey_port']);
                if (!empty($settings['valkey_auth'])) {
                    $redis->auth($settings['valkey_auth']);
                }
                $info = $redis->info();
                $hits = $info['keyspace_hits'];
                $misses = $info['keyspace_misses'];
            } catch (Exception $e) {
                error_log('Error getting ValKey stats: ' . $e->getMessage());
            }
        }

        $total = $hits + $misses;
        return $total > 0 ? ($hits / $total) * 100 : 0;
    }

    public function add_security_headers() {
        if (is_admin()) {
            return;
        }

        // Content Security Policy
        $csp = array(
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "frame-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "upgrade-insecure-requests"
        );
        
        header("Content-Security-Policy: " . implode('; ', $csp));
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: SAMEORIGIN");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
    }
}

// Initialize the performance optimization
add_action('after_setup_theme', function() {
    NiertoCube_Performance::get_instance();
});

} // end class_exists check

// Register hooks for cache clearing
add_action('save_post', function($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    NiertoCube_Performance::get_instance()->clear_cache();
});

add_action('switch_theme', function() {
    NiertoCube_Performance::get_instance()->clear_cache();
});

add_action('customize_save_after', function() {
    NiertoCube_Performance::get_instance()->clear_cache();
});