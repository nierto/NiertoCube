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
if ($logo_url):
?>
    <div id="logo_top">
        <img src="<?php echo esc_url($logo_url); ?>" alt="Logo" id="logo_goHome" onclick='goHome()'>
    </div>
<?php
endif;
?>
</header>
