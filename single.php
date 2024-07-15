<?php
/*
Template Name: Default Single Post Template
*/

remove_action('wp_head', 'wp_enqueue_scripts', 1);
remove_action('wp_head', 'wp_print_styles', 8);
remove_action('wp_head', 'wp_print_head_scripts', 9);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: var(--font-family, <?php echo get_theme_mod('font_family', "'Ubuntu', sans-serif"); ?>);
            background-color: var(--color-bg);
            color: var(--color-txt);
        }
        .scroll-container {
            width: 100%;
            height: 100%;
            overflow-y: scroll;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .scroll-container::-webkit-scrollbar {
            display: none;
        }
        .content {
            padding: 20px;
        }
        .post-title {
            margin-top: 0;
            color: var(--color-header);
            font-family: var(--font-family-highlights, <?php echo get_theme_mod('font_family_highlights', "'Rubik', sans-serif"); ?>);
        }
        .post-meta {
            font-size: 0.9em;
            color: var(--color-highlight);
            margin-bottom: 20px;
        }
        a {
            color: var(--color-highlight);
        }
        a:hover {
            color: var(--color-hover);
        }
    </style>
</head>
<body <?php body_class(); ?>>
    <div class="scroll-container">
        <div class="content">
            <?php
            while (have_posts()) :
                the_post();
                ?>
                <h1 class="post-title"><?php the_title(); ?></h1>
                <div class="post-meta">
                    <?php echo get_the_date(); ?> by <?php the_author(); ?>
                </div>
                <?php the_content(); ?>
            <?php
            endwhile;
            ?>
        </div>
    </div>
</body>
</html>