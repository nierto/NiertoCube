<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php'); // Ensure the path to wp-load.php is correct
header("Content-type: application/javascript");

$nav_texts = [];
for ($i = 0; $i <= 5; $i++) {
    $nav_texts["face{$i}"] = get_theme_mod("cube_face_page_name_{$i}", "Face {$i}");
}
?>

const variables = {
    navTexts: <?php echo json_encode($nav_texts); ?>
};

function addOnClickAttributes() {
    document.querySelectorAll('.navButton .navName').forEach((element, index) => {
        element.setAttribute('onclick', `cubeMoveButton('face${index}', '${variables.navTexts[`face${index}`]}')`);
    });
}

document.addEventListener('DOMContentLoaded', addOnClickAttributes);