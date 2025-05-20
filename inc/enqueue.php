<?php

function theme_enqueue_styles() {
    $theme_uri = get_template_directory_uri();

    wp_enqueue_style('font-awesome', $theme_uri . '/assets/css/font-awesome.css');
    wp_enqueue_style('slick', $theme_uri . '/assets/css/slick.css');
    wp_enqueue_style('font', $theme_uri . '/assets/css/font.css');
    wp_enqueue_style('styles', $theme_uri . '/assets/css/styles.css');
    wp_enqueue_style('responsive', $theme_uri . '/assets/css/responsive.css');
}
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');
