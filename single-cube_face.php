<?php
$template = get_post_meta(get_the_ID(), '_cube_face_template', true);
$template = $template ? $template : 'standard';

if ($template === 'settings' && !current_user_can('manage_options')) {
    $template = 'standard';
}

get_template_part('single-cube_face', $template);