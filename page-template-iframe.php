<?php
/*
Template Name: Iframe-Optimized Page
*/
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <?php 
    $google_font_url = get_google_font_url();
    if ($google_font_url) : 
    ?>
    <link href="<?php echo esc_url($google_font_url); ?>" rel="stylesheet">
    <?php endif; ?>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: <?php echo get_font_family('body_font'); ?>;
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
        h1, h2, h3, h4, h5, h6 {
            font-family: <?php echo get_font_family('heading_font'); ?>;
            color: var(--color-header);
        }
        .custom-font {
            font-family: <?php echo get_font_family('extra_font'); ?>;
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

<script>
(function() {
    var container = document.querySelector('.scroll-container');
    var content = document.querySelector('.content');
    var scrollPosition = 0;
    var maxScroll = content.offsetHeight - container.offsetHeight;

    function updateScroll(deltaY) {
        scrollPosition = Math.max(0, Math.min(scrollPosition + deltaY, maxScroll));
        requestAnimationFrame(() => {
            const roundedPosition = Math.round(scrollPosition);
            content.style.transform = `translateY(-${roundedPosition}px)`;
        });
    }

    window.addEventListener('message', function(event) {
        switch(event.data.type) {
            case 'wheel':
            case 'touchmove':
            case 'scroll':
                updateScroll(event.data.deltaY);
                break;
            case 'finalScroll':
                scrollPosition = event.data.position;
                requestAnimationFrame(() => {
                    content.style.transform = `translateY(-${scrollPosition}px)`;
                });
                break;
            case 'touchstart':
                // Handle touch start if needed
                break;
        }
    });

    if (window.parent && window.parent.iframeReady) {
        window.parent.iframeReady();
    }
})();
</script>
</body>
</html>