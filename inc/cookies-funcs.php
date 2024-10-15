<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_cookie_notice() {
    if (!isset($_COOKIE['nierto_cube_cookie_notice_accepted'])) {
        echo '<div id="cookie-notice-overlay">
                <div id="cookie-notice">
                    <p>This site uses cookies and local storage for better user experience. We also collect IP addresses and user agents for analytics purposes.</p>
                    <button onclick="acceptCookieNotice()">Accept</button>
                    <button onclick="rejectCookieNotice()">Reject</button>
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

function nierto_cube_enqueue_cookie_script() {
    wp_enqueue_script('nierto-cube-cookie', get_template_directory_uri() . '/js/cookies.js', array(), '1.0', true);
    wp_localize_script('nierto-cube-cookie', 'niertoCubeData', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('nierto_cube_cookie_action')
    ));
}
add_action('wp_enqueue_scripts', 'nierto_cube_enqueue_cookie_script');

function nierto_cube_log_cookie_preference() {
    check_ajax_referer('nierto_cube_cookie_action', 'nonce');

    $preference = sanitize_text_field($_POST['preference']);
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $log_entry = date('Y-m-d H:i:s') . " | IP: $ip | User Agent: $user_agent | Preference: $preference\n";
    $log_file = get_template_directory() . '/logs/cookie_preferences.log';

    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }

    file_put_contents($log_file, $log_entry, FILE_APPEND);

    wp_send_json_success();
}
add_action('wp_ajax_nierto_cube_log_cookie_preference', 'nierto_cube_log_cookie_preference');
add_action('wp_ajax_nopriv_nierto_cube_log_cookie_preference', 'nierto_cube_log_cookie_preference');
