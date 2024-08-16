<?php
// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

// Save settings
if (isset($_POST['nierto_cube_settings'])) {
    update_option('nierto_cube_settings', $_POST['nierto_cube_settings']);
    echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
}

$settings = get_option('nierto_cube_settings');
?>

<div class="wrap nierto-cube-admin">
    <h1 class="nierto-cube-title"><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form action="" method="post" class="nierto-cube-form">
        <div class="nierto-cube-table valkey-settings">
            <h2 class="nierto-cube-subtitle">ValKey Settings</h2>
            <div class="nierto-cube-field">
                <label class="nierto-cube-label">Use ValKey</label>
                <label class="switch">
                    <input type="checkbox" name="nierto_cube_settings[use_valkey]" value="1" <?php checked(isset($settings['use_valkey']) ? $settings['use_valkey'] : ''); ?>>
                    <span class="slider"></span>
                </label>
            </div>
            <div class="nierto-cube-field">
                <label for="valkey_ip" class="nierto-cube-label">ValKey IP Address</label>
                <input type="text" id="valkey_ip" name="nierto_cube_settings[valkey_ip]" value="<?php echo esc_attr(isset($settings['valkey_ip']) ? $settings['valkey_ip'] : ''); ?>" class="nierto-cube-input">
            </div>
            <div class="nierto-cube-field">
                <label for="valkey_port" class="nierto-cube-label">ValKey Port</label>
                <input type="text" id="valkey_port" name="nierto_cube_settings[valkey_port]" value="<?php echo esc_attr(isset($settings['valkey_port']) ? $settings['valkey_port'] : '6379'); ?>" class="nierto-cube-input">
            </div>
        </div>
        
        <h2 class="nierto-cube-subtitle">Cube Face Settings</h2>
        <div class="face-settings-container">
            <?php for ($i = 1; $i <= 6; $i++) : ?>
                <div class="face-settings">
                    <h3 class="nierto-cube-face-title">Face <?php echo $i; ?></h3>
                    <div class="nierto-cube-field">
                        <label for="face<?php echo $i; ?>_type" class="nierto-cube-label">Content Type</label>
                        <select id="face<?php echo $i; ?>_type" name="nierto_cube_settings[face<?php echo $i; ?>_type]" class="nierto-cube-select">
                            <option value="post" <?php selected(isset($settings['face'.$i.'_type']) ? $settings['face'.$i.'_type'] : '', 'post'); ?>>Cube Face Post</option>
                            <option value="page" <?php selected(isset($settings['face'.$i.'_type']) ? $settings['face'.$i.'_type'] : '', 'page'); ?>>Page (iframe)</option>
                        </select>
                    </div>
                    <div class="nierto-cube-field">
                        <label for="face<?php echo $i; ?>_source" class="nierto-cube-label">Content Source</label>
                        <input type="text" id="face<?php echo $i; ?>_source" name="nierto_cube_settings[face<?php echo $i; ?>_source]" value="<?php echo esc_attr(isset($settings['face'.$i.'_source']) ? $settings['face'.$i.'_source'] : ''); ?>" class="nierto-cube-input">
                        <p class="nierto-cube-description">For Cube Face Post, enter the post ID. For Page, enter the URL slug.</p>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        
        <?php submit_button('Save Settings', 'primary', 'submit', true, array('class' => 'nierto-cube-submit')); ?>
    </form>
</div>