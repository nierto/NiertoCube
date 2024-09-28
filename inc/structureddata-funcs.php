<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_generate_structured_data() {
    global $post;

    if (is_singular()) {
        $schema = array(
            "@context" => "https://schema.org",
            "@type" => "WebPage",
            "name" => get_the_title(),
            "description" => get_the_excerpt(),
            "url" => get_permalink(),
            "datePublished" => get_the_date('c'),
            "dateModified" => get_the_modified_date('c'),
            "author" => array(
                "@type" => "Person",
                "name" => get_the_author()
            ),
            "publisher" => array(
                "@type" => "Organization",
                "name" => get_bloginfo('name'),
                "logo" => array(
                    "@type" => "ImageObject",
                    "url" => get_theme_mod('logo_source', '')
                )
            )
        );

        if (get_post_type() === 'cube_face') {
            $schema['@type'] = 'Article';
            $schema['articleSection'] = 'Cube Face';
            $schema['position'] = get_post_meta($post->ID, 'cube_face_position', true);
            $schema['associatedMedia'] = array(
                "@type" => "WebPage",
                "url" => get_permalink()
        );
        
        // Add meta tags as properties
        $meta_tags = get_post_meta($post->ID, 'cube_face_meta_tags', true);
        if (!empty($meta_tags)) {
            $schema['keywords'] = $meta_tags;
        }
        
        // Add any custom fields you've defined for cube faces
        $custom_content = get_post_meta($post->ID, 'cube_face_custom_content', true);
        if (!empty($custom_content)) {
            $schema['description'] = wp_strip_all_tags($custom_content);
        }

        if (has_post_thumbnail()) {
            $schema['image'] = array(
                "@type" => "ImageObject",
                "url" => get_the_post_thumbnail_url($post->ID, 'full'),
                "width" => 1200,
                "height" => 630
            );
        }

        return json_encode($schema);
    }

    return '';
}