<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


function enforce_single_settings_template($post_id) {
    if (get_post_type($post_id) === 'cube_face' && get_post_meta($post_id, '_cube_face_template', true) === 'settings') {
        $existing_settings = get_posts([
            'post_type' => 'cube_face',
            'meta_key' => '_cube_face_template',
            'meta_value' => 'settings',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'exclude' => [$post_id],
        ]);

        if (!empty($existing_settings)) {
            remove_action('save_post_cube_face', 'enforce_single_settings_template');
            wp_update_post([
                'ID' => $post_id,
                'post_status' => 'draft',
            ]);
            add_action('save_post_cube_face', 'enforce_single_settings_template');
            add_filter('redirect_post_location', function ($location) {
                return add_query_arg('settings_template_error', 1, $location);
            });
        }
    }
}
add_action('save_post_cube_face', 'enforce_single_settings_template');

function settings_template_error_notice() {
    if (isset($_GET['settings_template_error'])) {
        echo '<div class="error"><p>Only one Settings template can be published at a time. This post has been saved as a draft.</p></div>';
    }
}
add_action('admin_notices', 'settings_template_error_notice');