<?php
get_header();

$posts = get_posts([
    'post_type' => 'post',
    'posts_per_page' => 6,
]);

foreach ($posts as $post) {
    setup_postdata($post);
    ?>
    <div class="multi-post-item">
        <h2><?php the_title(); ?></h2>
        <?php the_excerpt(); ?>
    </div>
    <?php
}
wp_reset_postdata();

get_footer();