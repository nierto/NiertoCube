<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_get_htaccess_rules() {
    $site_path = parse_url(get_option('siteurl'), PHP_URL_PATH);
    $site_path = $site_path ? $site_path : '/';
    
    return "
# BEGIN NiertoCube
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase {$site_path}
    RewriteRule ^manifest\\.json$ /index.php?manifest=1 [L,QSA]
    
    # Prevent direct access to PHP files except index.php
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteCond %{REQUEST_FILENAME} \.php$
    RewriteCond %{REQUEST_FILENAME} !index\.php$
    RewriteRule ^ - [F,L]
</IfModule>

<IfModule mod_headers.c>
    # Remove unnecessary headers
    Header unset X-Powered-By
    Header unset Server
    Header unset X-Pingback
    
    # Security headers
    Header set X-Content-Type-Options \"nosniff\"
    Header set X-Frame-Options \"SAMEORIGIN\"
    Header set X-XSS-Protection \"1; mode=block\"
    Header set Referrer-Policy \"strict-origin-when-cross-origin\"
    Header set Permissions-Policy \"geolocation=(), microphone=(), camera=()\"
    
    # Cache Control for static assets
    <FilesMatch \"\.(ico|pdf|jpg|jpeg|png|gif|js|css|swf|svg|woff|woff2)$\">
        Header set Cache-Control \"max-age=31536000, public\"
        Header set Pragma \"public\"
        Header unset ETag
        FileETag None
    </FilesMatch>
    
    # CORS headers for font files
    <FilesMatch \"\.(ttf|ttc|otf|eot|woff|woff2|font.css|css)$\">
        Header set Access-Control-Allow-Origin \"*\"
    </FilesMatch>
</IfModule>

<IfModule mod_expires.c>
    ExpiresActive On
    
    # Default expiration
    ExpiresDefault \"access plus 1 month\"
    
    # Images
    ExpiresByType image/jpeg \"access plus 1 year\"
    ExpiresByType image/gif \"access plus 1 year\"
    ExpiresByType image/png \"access plus 1 year\"
    ExpiresByType image/webp \"access plus 1 year\"
    ExpiresByType image/svg+xml \"access plus 1 year\"
    ExpiresByType image/x-icon \"access plus 1 year\"
    
    # CSS, JavaScript
    ExpiresByType text/css \"access plus 1 year\"
    ExpiresByType text/javascript \"access plus 1 year\"
    ExpiresByType application/javascript \"access plus 1 year\"
    
    # Fonts
    ExpiresByType application/font-woff \"access plus 1 year\"
    ExpiresByType application/font-woff2 \"access plus 1 year\"
    ExpiresByType application/vnd.ms-fontobject \"access plus 1 year\"
    ExpiresByType application/x-font-ttf \"access plus 1 year\"
    ExpiresByType font/opentype \"access plus 1 year\"
</IfModule>

<IfModule mod_deflate.c>
    # Compress HTML, CSS, JavaScript, Text, XML and fonts
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/vnd.ms-fontobject
    AddOutputFilterByType DEFLATE application/x-font
    AddOutputFilterByType DEFLATE application/x-font-opentype
    AddOutputFilterByType DEFLATE application/x-font-otf
    AddOutputFilterByType DEFLATE application/x-font-truetype
    AddOutputFilterByType DEFLATE application/x-font-ttf
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE font/opentype
    AddOutputFilterByType DEFLATE font/otf
    AddOutputFilterByType DEFLATE font/ttf
    AddOutputFilterByType DEFLATE image/svg+xml
    AddOutputFilterByType DEFLATE image/x-icon
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/javascript
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/xml
</IfModule>
# END NiertoCube\n\n";
}

function nierto_cube_theme_activation() {
    // Get path to .htaccess file
    $htaccess_file = get_home_path() . '.htaccess';
    
    // Create .htaccess if it doesn't exist
    if (!file_exists($htaccess_file)) {
        @touch($htaccess_file);
    }
    
    // Check if file exists and is writable
    if (is_writable($htaccess_file)) {
        $htaccess_content = file_get_contents($htaccess_file);
        
        // Check if NiertoCube rules already exist
        if (strpos($htaccess_content, '# BEGIN NiertoCube') === false) {
            $nierto_rules = nierto_cube_get_htaccess_rules();
            
            // If WordPress rules exist, insert before them
            if (strpos($htaccess_content, '# BEGIN WordPress') !== false) {
                $htaccess_content = preg_replace(
                    '/# BEGIN WordPress/',
                    $nierto_rules . '# BEGIN WordPress',
                    $htaccess_content
                );
            } else {
                // If no WordPress rules, append to end
                $htaccess_content .= $nierto_rules;
            }
            
            // Write back to file
            file_put_contents($htaccess_file, $htaccess_content);
        }
    }
    
    // Create necessary directories
    nierto_cube_create_directories();
    
    // Initialize default options
    nierto_cube_initialize_options();
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Add flag to indicate theme was activated
    update_option('nierto_cube_activated', true);
    update_option('nierto_cube_version', '1.0.0');
}

function nierto_cube_create_directories() {
    $dirs = [
        '/logs',
        '/cache',
        '/temp'
    ];
    
    foreach ($dirs as $dir) {
        $path = get_template_directory() . $dir;
        if (!file_exists($path)) {
            wp_mkdir_p($path);
            // Create .htaccess to prevent direct access
            file_put_contents($path . '/.htaccess', "Deny from all\n");
        }
    }
}

function nierto_cube_initialize_options() {
    // Default ValKey settings
    $valkey_settings = get_option('nierto_cube_settings', []);
    $valkey_defaults = [
        'use_valkey' => false,
        'valkey_ip' => '',
        'valkey_port' => '6379',
        'valkey_auth' => ''
    ];
    update_option('nierto_cube_settings', array_merge($valkey_defaults, $valkey_settings));
    
    // Initialize cache version
    if (!get_option('nierto_cube_cache_version')) {
        update_option('nierto_cube_cache_version', 1);
    }
}

function nierto_cube_theme_deactivation() {
    $htaccess_file = get_home_path() . '.htaccess';
    
    if (is_writable($htaccess_file)) {
        $htaccess_content = file_get_contents($htaccess_file);
        
        // Remove NiertoCube rules
        $htaccess_content = preg_replace(
            '/\s*# BEGIN NiertoCube.*# END NiertoCube\s*/s',
            "\n",
            $htaccess_content
        );
        
        file_put_contents($htaccess_file, $htaccess_content);
    }
    
    // Clean up options
    delete_option('nierto_cube_activated');
    delete_option('nierto_cube_version');
    
    // Don't remove ValKey settings to preserve configuration
}

// Add notice if .htaccess is not writable
function nierto_cube_admin_notices() {
    if (get_option('nierto_cube_activated') && !is_writable(get_home_path() . '.htaccess')) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('NiertoCube Theme: Your .htaccess file is not writable. Please add the following rules manually:', 'nierto_cube'); ?></p>
            <pre style="background: #f0f0f0; padding: 10px; overflow: auto; max-height: 300px;">
            <?php echo htmlspecialchars(nierto_cube_get_htaccess_rules()); ?>
            </pre>
        </div>
        <?php
    }
}

// Register activation/deactivation hooks
register_activation_hook(__FILE__, 'nierto_cube_theme_activation');
register_deactivation_hook(__FILE__, 'nierto_cube_theme_deactivation');

// Add admin notices
add_action('admin_notices', 'nierto_cube_admin_notices');

// Add upgrade routine
function nierto_cube_after_switch_theme() {
    $installed_version = get_option('nierto_cube_version', '0');
    $current_version = '1.0.0'; // Update this when releasing new versions
    
    if (version_compare($installed_version, $current_version, '<')) {
        nierto_cube_theme_activation();
        update_option('nierto_cube_version', $current_version);
    }
}
add_action('after_switch_theme', 'nierto_cube_after_switch_theme');

// Add this to handle multisite installations
function nierto_cube_new_blog($blog_id) {
    if (is_plugin_active_for_network('nierto-cube/nierto-cube.php')) {
        switch_to_blog($blog_id);
        nierto_cube_theme_activation();
        restore_current_blog();
    }
}
add_action('wpmu_new_blog', 'nierto_cube_new_blog');

// Clean up on theme deletion
function nierto_cube_theme_deletion() {
    $upload_dir = wp_upload_dir();
    $theme_cache_dir = $upload_dir['basedir'] . '/nierto-cube-cache';
    
    if (file_exists($theme_cache_dir)) {
        nierto_cube_recursive_remove_dir($theme_cache_dir);
    }
}
add_action('switch_theme', 'nierto_cube_theme_deletion');

// Helper function for directory removal
function nierto_cube_recursive_remove_dir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object)) {
                    nierto_cube_recursive_remove_dir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        rmdir($dir);
    }
}