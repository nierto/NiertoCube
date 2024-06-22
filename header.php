<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div id="page">
<header>
<?php
$logo_url = get_theme_mod('logo_source', '');
$logo_width = get_theme_mod('logo_width', '10vmin');
if ($logo_url):
?>
    <div id="logoWrapper" class="leftcorner">
        <img src="<?php echo esc_url($logo_url); ?>" alt="Logo that moves the page back to the home screen" id="logo_goHome" onclick="handleLogoClick()" style="max-width: <?php echo esc_attr($logo_width); ?>;">
    </div>
<?php
endif;
?>
</header>