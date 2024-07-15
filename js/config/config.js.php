<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
header("Content-type: application/javascript");

// Check if a cached version exists and is recent
$cache_file = get_template_directory() . '/js/config/cached_config.js';
$cache_time = 604800; // Cache 1 week

if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
    // Serve the cached file
    readfile($cache_file);
    exit;
}

// If no cache exists or it's expired, generate the content
ob_start(); // Start output buffering

$cube_faces = [];
for ($i = 1; $i <= 6; $i++) {
    $cube_faces[] = [
        'buttonText' => get_theme_mod("cube_face_{$i}_text", "Face {$i}"),
        'urlSlug' => get_theme_mod("cube_face_{$i}_slug", "face-{$i}"),
        'facePosition' => get_theme_mod("cube_face_{$i}_position", "face" . ($i - 1)),
    ];
}
?>

const variables = {
    cubeFaces: <?php echo json_encode($cube_faces); ?>
};

function setupCubeButtons() {
    const navButtons = document.querySelectorAll('.navButton');
    variables.cubeFaces.forEach((face, index) => {
        const navName = navButtons[index]?.querySelector('.navName');
        if (navName) {
            navName.textContent = face.buttonText;
            navName.setAttribute('data-face', face.facePosition);
            navName.setAttribute('data-slug', face.urlSlug);
        }
    });
}

document.addEventListener('DOMContentLoaded', setupCubeButtons);

<?php
// Get the generated content
$content = ob_get_clean();

// Save the content to the cache file
file_put_contents($cache_file, $content);

// Output the content
echo $content;