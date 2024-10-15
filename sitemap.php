<?php
function nierto_cube_generate_sitemap() {
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    // Add homepage  CHECK IF STILL THIS IS THE BEST VERSION OF THIS IDEA!
    $sitemap .= nierto_cube_sitemap_entry(home_url('/'), '1.0');

    // Add cube faces
    $cube_faces = get_posts([
        'post_type' => 'cube_face',
        'posts_per_page' => -1,
    ]);

    foreach ($cube_faces as $face) {
        $sitemap .= nierto_cube_sitemap_entry(get_permalink($face->ID), '0.8');
    }

    $sitemap .= '</urlset>';

    file_put_contents(ABSPATH . 'sitemap.xml', $sitemap);
}

function nierto_cube_sitemap_entry($url, $priority) {
    return "
    <url>
        <loc>" . esc_url($url) . "</loc>
        <lastmod>" . date('c') . "</lastmod>
        <changefreq>weekly</changefreq>
        <priority>{$priority}</priority>
    </url>";
}

add_action('save_post_cube_face', 'nierto_cube_generate_sitemap');
add_action('trash_post', 'nierto_cube_generate_sitemap');
add_action('publish_post', 'nierto_cube_generate_sitemap');
add_action('publish_page', 'nierto_cube_generate_sitemap');