<?php

// Enqueue theme styles
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('chaletv2-style', get_stylesheet_uri(), [], filemtime(get_stylesheet_directory() . '/style.css'));
});

// Theme setup
require_once get_template_directory() . '/inc/ThemeSetup.php';

// Helpers
require_once get_template_directory() .'/inc/helpers.php';

// Enqueue scripts and styles
require_once get_template_directory() .'/inc/enqueue.php';

// Custom Structures (CPT, Meta, etc)
require_once get_template_directory() . '/inc/CustomStructures.php';
require_once get_template_directory() . '/inc/admin/chalet-generator.php';

// Handle Form Submissions & Queries
require_once get_template_directory() . '/inc/queries.php';

// Woocommerce
require_once get_template_directory() . '/inc/Woocommerce.php';

// Subscriptions
require_once get_template_directory() . '/inc/Subscriptions.php';

// Middlewares
require_once get_template_directory() . '/inc/middlewares.php';

// Chalet V1 helpers
// require_once get_template_directory() . '/inc/chalet_v1.php';