<?php
/*
Template Name: Iframe Page for NiertoCube
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
            overflow: hidden;
            position: relative;
        }
        .content {
            padding: 20px;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            transition: transform 0.3s ease;
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
    <script>
    (function() {
        var container = document.querySelector('.scroll-container');
        var content = document.querySelector('.content');
        var scrollPosition = 0;
        var maxScroll = content.offsetHeight - container.offsetHeight;

        function updateScroll(delta) {
            scrollPosition = Math.max(0, Math.min(scrollPosition + delta, maxScroll));
            content.style.transform = `translateY(-${scrollPosition}px)`;
        }

        // Expose the handleScroll function to be called via the tunnel
        window.handleScroll = function(delta) {
            if (window.parent && window.parent.tunnel) {
                window.parent.tunnel(function() {
                    updateScroll(delta);
                });
            }
        };

        // Notify the parent that the iframe is ready
        if (window.parent && window.parent.iframeReady) {
            window.parent.iframeReady();
        }
    })();
    </script>
</body>
</html>