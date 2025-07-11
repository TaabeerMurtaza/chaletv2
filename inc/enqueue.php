<?php

function theme_enqueue_styles() {
    $theme_uri = get_template_directory_uri();

    $styles = [
        'font-awesome' => '/assets/css/font-awesome.css',
        'slick' => '/assets/css/slick.css',
        'font' => '/assets/css/font.css',
        'styles' => '/assets/css/styles.css',
        'responsive' => '/assets/css/responsive.css',
    ];

    foreach ($styles as $handle => $relative_path) {
        $file = get_template_directory() . $relative_path;
        $ver = file_exists($file) ? filemtime($file) : false;
        wp_enqueue_style($handle, $theme_uri . $relative_path, [], $ver);
    }

    // FontAwesome
    wp_enqueue_style(
        'font-awesome-672',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',
        [],
        '6.7.2'
    );

    wp_enqueue_script(
        'font-awesome-672-js',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js',
        [],
        '6.7.2',
        true
    );
    
    wp_enqueue_script(
        'fullcalendar-6117-js',
        // 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js',
        'https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.17/index.global.min.js',
        [],
        '6.1.17',
        true
    );
    // Dequeue default jQuery
    wp_dequeue_script('jquery');
    wp_deregister_script('jquery');

    // Enqueue jQuery from CDN
    wp_enqueue_script(
        'jquery',
        'https://code.jquery.com/jquery-3.7.1.min.js',
        [],
        '3.7.1',
        true
    );
    // Select2
    wp_enqueue_script(
        'select2',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        ['jquery'],
        '4.1.0',
        true
    );
    wp_enqueue_style(
        'select2',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
        [],
        '4.1.0'
    );

}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');
