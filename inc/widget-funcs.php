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