<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_cookie_notice() {
    if (!isset($_COOKIE['nierto_cube_cookie_notice_accepted'])) {
        echo '<div id="cookie-notice">This site uses cookies and local storage for better user experience. <button onclick="acceptCookieNotice()">Accept</button></div>';
    }
}
add_action('wp_footer', 'nierto_cube_cookie_notice');

function nierto_cube_privacy_policy_link() {
    $privacy_policy_id = get_option('wp_page_for_privacy_policy');
    if ($privacy_policy_id) {
        echo '<a href="' . get_permalink($privacy_policy_id) . '">Privacy Policy</a>';
    }
}
add_action('wp_footer', 'nierto_cube_privacy_policy_link');

function nierto_cube_clear_data_button() {
    echo '<button onclick="clearLocalData()">Clear Local Data</button>';
}
add_action('wp_footer', 'nierto_cube_clear_data_button');