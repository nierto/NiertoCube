<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_add_seo_meta_box() {
    add_meta_box(
        'nierto_cube_seo_meta_box',
        'SEO Settings',
        'nierto_cube_seo_meta_box_callback',
        'cube_face',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'nierto_cube_add_seo_meta_box');

function nierto_cube_seo_meta_box_callback($post) {
    wp_nonce_field('nierto_cube_save_seo_meta_box_data', 'nierto_cube_seo_meta_box_nonce');

    $meta_title = get_post_meta($post->ID, '_nierto_cube_meta_title', true);
    $meta_description = get_post_meta($post->ID, '_nierto_cube_meta_description', true);

    echo '<p><label for="nierto_cube_meta_title">Meta Title</label><br>';
    echo '<input type="text" id="nierto_cube_meta_title" name="nierto_cube_meta_title" value="' . esc_attr($meta_title) . '" size="50"></p>';

    echo '<p><label for="nierto_cube_meta_description">Meta Description</label><br>';
    echo '<textarea id="nierto_cube_meta_description" name="nierto_cube_meta_description" rows="4" cols="50">' . esc_textarea($meta_description) . '</textarea></p>';
}

function nierto_cube_save_seo_meta_box_data($post_id) {
    if (!isset($_POST['nierto_cube_seo_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['nierto_cube_seo_meta_box_nonce'], 'nierto_cube_save_seo_meta_box_data')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['nierto_cube_meta_title'])) {
        update_post_meta($post_id, '_nierto_cube_meta_title', sanitize_text_field($_POST['nierto_cube_meta_title']));
    }
    if (isset($_POST['nierto_cube_meta_description'])) {
        update_post_meta($post_id, '_nierto_cube_meta_description', sanitize_textarea_field($_POST['nierto_cube_meta_description']));
    }
}
add_action('save_post', 'nierto_cube_save_seo_meta_box_data');

function nierto_cube_add_meta_tags() {
    if (is_singular('cube_face')) {
        $post_id = get_the_ID();
        $meta_title = get_post_meta($post_id, '_nierto_cube_meta_title', true);
        $meta_description = get_post_meta($post_id, '_nierto_cube_meta_description', true);

        if (!empty($meta_title)) {
            echo '<meta name="title" content="' . esc_attr($meta_title) . '">' . "\n";
        }
        if (!empty($meta_description)) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'nierto_cube_add_meta_tags');