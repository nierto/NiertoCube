<?php
/*
Template Name: Iframe Page for NiertoCube (Reversed Content)
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
            transform: rotate(180deg);
            transform-origin: center center;
        }
        .scroll-container::-webkit-scrollbar {
            display: none;
        }
        .content {
            padding: 20px;
            transform: rotate(180deg);
            transform-origin: center center;
        }
        a {
            color: var(--color-highlight);
        }
        a:hover {
            color: var(--color-hover);
        }
        h1, h2, h3, h4, h5, h6 {
            color: var(--color-header);
            font-family: var(--font-family-highlights, <?php echo get_theme_mod('font_family_highlights', "'Rubik', sans-serif"); ?>);
        }
    </style>
    <?php wp_head(); ?>
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