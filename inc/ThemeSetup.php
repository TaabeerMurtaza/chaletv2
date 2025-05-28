<?php
function chaletv2_setup() {

// Add theme supports
add_theme_support('title-tag');
add_theme_support('post-thumbnails');
add_theme_support('automatic-feed-links');
add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
add_theme_support('custom-logo');
add_theme_support('customize-selective-refresh-widgets');
add_theme_support('woocommerce');

// Register nav menus
register_nav_menus([
    'primary' => __('Primary Menu', 'chaletv2'),
    'footer'  => __('Footer Menu', 'chaletv2')
]);
}
add_action('after_setup_theme', 'chaletv2_setup');

// Enqueue scripts and styles
function chaletv2_scripts() {
wp_enqueue_style('chaletv2-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'chaletv2_scripts');

// Widget Area
function chaletv2_widgets_init() {
register_sidebar([
    'name'          => __('Sidebar', 'chaletv2'),
    'id'            => 'sidebar-1',
    'description'   => __('Add widgets here.', 'chaletv2'),
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
]);
}
add_action('widgets_init', 'chaletv2_widgets_init');


add_action('after_setup_theme', function() {
    register_nav_menus([
        'main_nav' => 'Main Navigation',
        'extra_nav' => 'Extra Navigation',
    ]);
});

add_action('after_setup_theme', function () {
    \Carbon_Fields\Carbon_Fields::boot();
});



add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('dashboard-ajax', get_template_directory_uri() . '/dashboard/js/ajax-filter.js', ['jquery'], null, true);
    wp_localize_script('dashboard-ajax', 'ajaxurl', admin_url('admin-ajax.php'));
});
