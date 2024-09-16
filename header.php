<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="<?php echo get_bloginfo('name'); ?>">
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/path-to-your-icon.png">
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/manifest.php">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <a href="#main-content" class="skip-link screen-reader-text"><?php esc_html_e( 'Skip to content', 'nierto_cube' ); ?></a>
    <div id="page">
<header>
<?php
$logo_details = get_theme_logo_details();
$logo_url = $logo_details['url'];
$logo_width = $logo_details['width'];
if ($logo_url):
?>
    <div id="logoWrapper" class="leftcorner">
        <?php echo nierto_cube_get_image_with_alt(attachment_url_to_postid($logo_url), 'full', false, 'id="logo_goHome" onclick="handleLogoClick()" style="max-width: ' . esc_attr($logo_width) . ';"'); ?>
    </div>
<?php
endif;
?>
</header>