<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
function nierto_cube_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Cube Face Sidebar', 'nierto_cube' ),
        'id'            => 'cube-face-sidebar',
        'description'   => __( 'Widgets in this area will be shown on all cube faces.', 'nierto_cube' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'nierto_cube_widgets_init' );

// Modify your existing register_cube_face_post_type function
function register_cube_face_post_type() {
    register_post_type('cube_face', [
        'labels' => [
            'name' => 'Cube Faces',
            'singular_name' => 'Cube Face',
        ],
        'public' => true,
        'has_archive' => false,
        'supports' => ['title', 'editor', 'custom-fields', 'thumbnail'],
        'register_meta_box_cb' => 'add_cube_face_metaboxes'
    ]);
}

function add_cube_face_metaboxes() {
    add_meta_box(
        'cube_face_sidebar',
        __('Cube Face Sidebar', 'nierto_cube'),
        'cube_face_sidebar_callback',
        'cube_face',
        'side',
        'default'
    );
}

function cube_face_sidebar_callback($post) {
    // Output the sidebar
    dynamic_sidebar('cube-face-sidebar');
}