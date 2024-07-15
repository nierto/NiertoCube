<?php
/*
Template Name: Default Page Template
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
                the_content();
            endwhile;
            ?>
        </div>
    </div>
    <?php wp_footer(); ?>
</body>
</html>