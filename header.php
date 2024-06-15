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
<div id="wrapper_left">
    <div class="navButton accelerated">
        <div class="controlBar">
                <span class="navName";"></span>
        </div> 
    </div>
    <div class="navButton accelerated">
        <div class="controlBar">
                <span class="navName"></span>
        </div> 
    </div>
    <div class="navButton accelerated">
        <div class="controlBar">
            <span class="navName"></span>
        </div> 
    </div>
</div>
<?php
$logo_url = get_theme_mod('logo_source', '');
if ($logo_url):
?>
    <div id="logo_top">
        <img src="<?php echo esc_url($logo_url); ?>" alt="Logo that moves the page back to the home screen" id="logo_goHome" onclick="goHome()" style="width: <?php echo esc_attr($logo_width); ?>; height: <?php echo esc_attr($logo_height); ?>;">
    </div>
<?php
endif;
?>
</header>
