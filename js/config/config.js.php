<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php'); // Ensure the path to wp-load.php is correct
header("Content-type: application/javascript");

$nav_texts = [
    'face0' => get_theme_mod("nav_text_face0", "WHAT?"),
    'face1' => get_theme_mod("nav_text_face1", "WHY?"),
    'face2' => get_theme_mod("nav_text_face2", "HOW?"),
    'face3' => get_theme_mod("nav_text_face3", "WHO?"),
    'face4' => get_theme_mod("nav_text_face4", "TERMS"),
    'face5' => get_theme_mod("nav_text_face5", "STORIES"),
    'face6' => get_theme_mod("nav_text_face6", "CONTACT"),
    'face7' => get_theme_mod("nav_text_face7", "RATES")
];

?>
const variables = {
    face0: '<?php echo get_option("face0_page_id"); ?>',
    face1: '<?php echo get_option("face1_page_id"); ?>',
    face2: '<?php echo get_option("face2_page_id"); ?>',
    face3: '<?php echo get_option("face3_page_id"); ?>',
    face4: '<?php echo get_option("face4_page_id"); ?>',
    face5: '<?php echo get_option("face5_page_id"); ?>',
    navTexts: <?php echo json_encode($nav_texts); ?>
};