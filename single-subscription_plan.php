<?php

/**
 * Redirect template for single subscription plans.
 */

// Load WordPress environment if needed
if ( ! defined( 'ABSPATH' ) ) {
    require_once( dirname( __FILE__, 4 ) . '/wp-load.php' );
}

// Check if WooCommerce and WP Swings Subscriptions are active
if ( ! class_exists( 'WooCommerce' ) || !is_plugin_active('subscriptions-for-woocommerce/subscriptions-for-woocommerce.php')) {
    wp_die( 'WooCommerce or WP Swings Subscriptions plugin is not active.' );
}


// Create a new product
// Get custom field values
$description = carbon_get_post_meta( get_the_ID(), 'subscription_description');
$price = carbon_get_post_meta( get_the_ID(), 'subscription_price');
$interval = carbon_get_post_meta( get_the_ID(), 'subscription_interval');
$interval_duration = carbon_get_post_meta( get_the_ID(), 'subscription_interval_duration');
$chalets_allowed = carbon_get_post_meta( get_the_ID(), 'chalets_allowed');
$featured_allowed = carbon_get_post_meta( get_the_ID(), 'featured_allowed');

// Set defaults if fields are empty
$price = $price !== '' ? $price : 0;
$interval = $interval !== '' ? $interval : 'month';
$interval_duration = $interval_duration !== '' ? $interval_duration : 1;

// Create new product
$product = new WC_Product_Simple();
$product->set_name( get_the_title() );
$product->set_status( 'publish' );
$product->set_catalog_visibility( 'visible' );
$product->set_price( $price );
$product->set_regular_price( $price );
$product->set_description( $description );
$product->save();

// Set subscription options (WP Swings Subscriptions for WooCommerce)
update_post_meta( $product->get_id(), 'subscription_id', get_the_ID() );
update_post_meta( $product->get_id(), '_is_wps_subscriptions', 'yes' );
update_post_meta( $product->get_id(), '_wps_sfw_product', 'yes' );
update_post_meta( $product->get_id(), '_wps_subs_price', $price );
update_post_meta( $product->get_id(), 'wps_sfw_subscription_number', $interval_duration );
update_post_meta( $product->get_id(), 'wps_sfw_subscription_interval', $interval );
update_post_meta( $product->get_id(), '_wps_subs_length', '0' ); // 0 for infinite
update_post_meta( $product->get_id(), '_wps_subs_trial_length', '0' );
update_post_meta( $product->get_id(), '_wps_subs_trial_period', 'day' );

// Save custom fields for reference
update_post_meta( $product->get_id(), 'chalets_allowed', $chalets_allowed );
update_post_meta( $product->get_id(), 'featured_allowed', $featured_allowed );

// Add product to cart and redirect to cart page
WC()->cart->empty_cart(); // Optional: empty cart before adding
WC()->cart->add_to_cart( $product->get_id() );
wp_safe_redirect( wc_get_cart_url() );
exit;