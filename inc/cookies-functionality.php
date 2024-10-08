<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_cookie_notice() {
    if (!isset($_COOKIE['nierto_cube_cookie_notice_accepted'])) {
        echo '<div id="cookie-notice-overlay">
                <div id="cookie-notice">
                    <p>This site uses cookies and local storage for better user experience.</p>
                    <button onclick="acceptCookieNotice()">Accept</button>
                    ' . nierto_cube_privacy_policy_link() . '
                </div>
              </div>';
    }
}
add_action('wp_footer', 'nierto_cube_cookie_notice');

function nierto_cube_privacy_policy_link() {
    $privacy_policy_id = get_option('wp_page_for_privacy_policy');
    if ($privacy_policy_id) {
        return '<a href="' . get_permalink($privacy_policy_id) . '">Privacy Policy</a>';
    }
    return '';
}

function nierto_cube_clear_data_button() {
    echo '<button onclick="clearLocalData()">Clear Local Data</button>';
}
add_action('wp_footer', 'nierto_cube_clear_data_button');