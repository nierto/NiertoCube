<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_premium_plugins_section() {
    $premium_plugins = [
        'Woocommerce Plugin' => 'Integrate with WooCommerce and display product images inside the cube.',
        'LLM Plugin' => 'Connect to your own local LLM or an LLM hosted by Agentique.ai.',
        'SEO Plugin' => 'Enhance SEO scores specifically for NiertoCube.',
        'Image Optimization Plugin' => 'Render images on cube sides and optimize uploaded images.',
        'Contact Plugin' => 'Specialized contact form with call-to-action buttons for messaging apps.'
    ];

    echo '<div class="nierto-cube-premium-plugins">';
    foreach ($premium_plugins as $plugin_name => $description) {
        $slug = sanitize_title($plugin_name);
        ?>
        <div class="premium-plugin-item">
            <h3><?php echo esc_html($plugin_name); ?></h3>
            <p><?php echo esc_html($description); ?></p>
            <input type="text" id="<?php echo esc_attr($slug); ?>_key" name="<?php echo esc_attr($slug); ?>_key" placeholder="COMING SOON!" disabled>
            <button class="button activate-plugin" data-plugin="<?php echo esc_attr($slug); ?>" disabled>Activate</button>
            <button class="button more-info" data-plugin="<?php echo esc_attr($slug); ?>">More Info</button>
        </div>
        <?php
    }
    echo '</div>';

    // Add JavaScript to handle "More Info" button clicks
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const moreInfoButtons = document.querySelectorAll('.more-info');
        moreInfoButtons.forEach(button => {
            button.addEventListener('click', function() {
                const plugin = this.getAttribute('data-plugin');
                const infoUrl = `https://nierto.com/plugins/${plugin}-info`;
                window.open(infoUrl, '_blank');
            });
        });
    });
    </script>
    <?php
}

// Hook to add the premium plugins section to the admin page
add_action('nierto_cube_admin_page', 'nierto_cube_premium_plugins_section');

// AJAX handler for future activation functionality
function nierto_cube_activate_premium_plugin() {
    // Check nonce for security
    check_ajax_referer('nierto_cube_admin', 'nonce');

    // Ensure user has permission
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }

    $plugin = sanitize_text_field($_POST['plugin']);
    // Future: Add activation logic here

    wp_send_json_success('Plugin activation placeholder for: ' . $plugin);
}
add_action('wp_ajax_nierto_cube_activate_premium_plugin', 'nierto_cube_activate_premium_plugin');