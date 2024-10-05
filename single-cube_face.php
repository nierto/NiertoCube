<?php
/*
Template Name: Single Cube Face
*/
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <?php wp_head(); ?>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: <?php echo get_font_family('body_font'); ?>;
            background-color: var(--color-bg);
            color: var(--color-txt);
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: <?php echo get_font_family('heading_font'); ?>;
            color: var(--color-header);
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
    <div class="cube-face-content">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <h1><?php the_title(); ?></h1>
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        <?php
        endwhile;
        ?>
    </div>
    <?php wp_footer(); ?>
</body>
</html>