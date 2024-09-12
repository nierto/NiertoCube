<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function nierto_cube_verify_nonce($nonce, $action) {
    if (!wp_verify_nonce($nonce, $action)) {
        wp_die('Security check failed');
    }
}