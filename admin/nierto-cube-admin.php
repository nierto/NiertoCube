<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add NiertoCube admin page
function nierto_cube_add_admin_page() {
    add_menu_page(
        'NiertoCube Settings',
        'NiertoCube',
        'manage_options',
        'nierto-cube-settings',
        'nierto_cube_admin_page',
        'dashicons-cube',
        100
    );
}
add_action('admin_menu', 'nierto_cube_add_admin_page');

// Admin page content
function nierto_cube_admin_page() {
    ?>
    <div class="wrap">
        <h1>NiertoCube Settings</h1>
        <div class="nierto-cube-admin-container">
            <div class="nierto-cube-admin-section">
                <h2>Premium Plugins</h2>
                <?php nierto_cube_premium_plugins_section(); ?>
            </div>
            <div class="nierto-cube-admin-section">
                <h2>Cache Management</h2>
                <?php nierto_cube_cache_management_section(); ?>
            </div>
            <div class="nierto-cube-admin-section">
                <h2>About NiertoCube</h2>
                <?php nierto_cube_about_section(); ?>
            </div>
            <div class="nierto-cube-admin-section">
                <h2>SEO Dashboard</h2>
                <?php nierto_cube_seo_dashboard_section(); ?>
            </div>
        </div>
    </div>
    <?php
}

// Enqueue admin styles
function nierto_cube_admin_styles() {
    wp_enqueue_style('nierto-cube-admin-style', get_template_directory_uri() . '/css/admin-style.css');
}
add_action('admin_enqueue_scripts', 'nierto_cube_admin_styles');

// Premium Plugins Section
function nierto_cube_premium_plugins_section() {
    $premium_plugins = [
        'Woocommerce Plugin' => 'Integrate with WooCommerce and display product images inside the cube.',
        'LLM Plugin' => 'Connect to your own local LLM or an LLM hosted by Agentique.ai.',
        'SEO Plugin' => 'Enhance SEO scores specifically for NiertoCube.',
        'Image Optimization Plugin' => 'Render images on cube sides and optimize uploaded images.',
        'Contact Plugin' => 'Specialized contact form with call-to-action buttons for messaging apps.'
    ];

    foreach ($premium_plugins as $plugin_name => $description) {
        ?>
        <div class="premium-plugin-item">
            <h3><?php echo esc_html($plugin_name); ?></h3>
            <p><?php echo esc_html($description); ?></p>
            <input type="text" name="<?php echo esc_attr(sanitize_title($plugin_name)); ?>_key" placeholder="Enter serial key">
            <button class="button activate-plugin">Activate</button>
            <button class="button more-info" data-plugin="<?php echo esc_attr(sanitize_title($plugin_name)); ?>">More Info</button>
        </div>
        <?php
    }
}

// Cache Management Section
function nierto_cube_cache_management_section() {
    ?>
    <p>Manage server-side caches for optimal performance.</p>
    <button class="button clear-cache">Clear All Caches</button>
    <?php
}

// About Section
function nierto_cube_about_section() {
    ?>
    <p>NiertoCube is developed by Niels Erik Toren, a one-man army dedicated to creating innovative Solutions for Man and Machine.</p>
    <p>Follow NiertoCube:</p>
    <ul>
        <li><a href="https://nierto.com" target="_blank">Official Website</a></li>
        <li><a href="https://twitter.com/niertocube" target="_blank">Twitter</a></li>
        <li><a href="https://github.com/niertocube" target="_blank">GitHub</a></li>
    </ul>
    <?php
}

// SEO Dashboard Section (Work in Progress)
function nierto_cube_seo_dashboard_section() {
    ?>
    <div class="seo-dashboard-wip">
        <h3>SEO Dashboard - Coming Soon</h3>
        <p>We're working on a comprehensive SEO dashboard to help you track:</p>
        <ul>
            <li>Site visitors and their sources (search engines, referrers, etc.)</li>
            <li>Post and navigation button click analytics</li>
            <li>SEO performance metrics</li>
        </ul>
    </div>
    <?php
}
