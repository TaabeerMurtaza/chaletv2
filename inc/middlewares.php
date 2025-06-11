<?php

/**
 * Middleware for handling custom redirects after WooCommerce purchases.
 *
 * This function checks if the user has just completed a purchase and redirects them
 * to a specific page in the dashboard.
 *
 * @return void
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
/**
 * Middleware for handling custom redirects after WooCommerce purchases.
 */
add_action( 'template_redirect', 'custom_redirect_after_purchase' );

function custom_redirect_after_purchase() {
    if ( is_wc_endpoint_url( 'order-received' ) && isset( $_GET['key'] ) ) {
        wp_redirect( get_home_url() . '/dashboard-edit-chalet' );
        exit;
    }
}
