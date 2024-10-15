<?php
get_header();

if (current_user_can('manage_options')) {
    ?>
    <h1>Settings</h1>
    <button onclick="clearLocalData()">Clear Local Data</button>
    <!-- Add more settings options here -->
    <?php
} else {
    echo '<p>You do not have permission to access this page.</p>';
}

get_footer();