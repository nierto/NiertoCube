<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_theme_activation() {
    // Get path to .htaccess file
    $htaccess_file = get_home_path() . '.htaccess';
    
    // Check if file exists and is writable
    if (!file_exists($htaccess_file)) {
        @touch($htaccess_file);
    }
    
    if (is_writable($htaccess_file)) {
        // Get existing content
        $htaccess_content = file_get_contents($htaccess_file);
        
        // Check if NiertoCube rules already exist
        if (strpos($htaccess_content, '# BEGIN NiertoCube') === false) {
            // Prepare NiertoCube rules
            $nierto_rules = "
# BEGIN NiertoCube
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase " . parse_url(get_option('siteurl'), PHP_URL_PATH) . "
RewriteRule ^manifest\\.json$ /index.php?manifest=1 [L,QSA]
</IfModule>
# END NiertoCube\n\n";
            
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
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Add flag to indicate theme was activated
    update_option('nierto_cube_activated', true);
}

// Function to remove rules on theme deactivation
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
    
    // Remove activation flag
    delete_option('nierto_cube_activated');
}

// Add notice if .htaccess is not writable
function nierto_cube_admin_notices() {
    if (get_option('nierto_cube_activated') && !is_writable(get_home_path() . '.htaccess')) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('NiertoCube Theme: Your .htaccess file is not writable. Please add the following rules manually:', 'nierto_cube'); ?></p>
            <pre style="background: #f0f0f0; padding: 10px;">
# BEGIN NiertoCube
&lt;IfModule mod_rewrite.c&gt;
RewriteEngine On
RewriteBase <?php echo parse_url(get_option('siteurl'), PHP_URL_PATH); ?>

RewriteRule ^manifest\.json$ /index.php?manifest=1 [L,QSA]
&lt;/IfModule&gt;
# END NiertoCube</pre>
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
    if (!get_option('nierto_cube_activated')) {
        nierto_cube_theme_activation();
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