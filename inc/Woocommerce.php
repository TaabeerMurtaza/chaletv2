<?php

add_action('save_post_chalet', 'sync_chalet_product_details', 10, 3);

function sync_chalet_product_details($post_ID, $post, $update) {
    // if ($post->post_status !== 'publish') return;

    $linked_product_id = get_post_meta($post_ID, '_linked_product_id', true);
    $price = carbon_get_post_meta($post_ID, 'chalet_price');
    $duration = carbon_get_post_meta($post_ID, 'duration');
    $duration_type = carbon_get_post_meta($post_ID, 'duration_type');

    if (!$price) $price = 0;
    if (!$duration) $duration = 1;
    if (!$duration_type) $duration_type = 'day';

    $unit_duration_days = ($duration_type === 'month') ? 30 : 1;
    $total_days = $unit_duration_days * $duration;
    $price_per_day = $price / $total_days;

    if (!$linked_product_id || !get_post_status($linked_product_id)) {
        $product = new WC_Product_Simple();
        $product->set_name($post->post_title);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_regular_price($price);
        $product->set_sold_individually(false); // Allow multiple quantity purchase
        $product->save();

        update_post_meta($post_ID, '_linked_product_id', $product->get_id());
    } else {
        $product = wc_get_product($linked_product_id);
        if ($product) {
            $product->set_name($post->post_title);
            $product->set_regular_price($price);
            $product->set_sold_individually(false);
            $product->save();
        }
    }
}

// Show linked products in admin panel
add_action('add_meta_boxes', function() {
    add_meta_box('linked_product_box', 'Linked WooCommerce Product', 'display_linked_product_meta', 'chalet', 'side');
});

function display_linked_product_meta($post) {
    $product_id = get_post_meta($post->ID, '_linked_product_id', true);
    if ($product_id && get_post_status($product_id)) {
        $url = get_edit_post_link($product_id);
        echo '<a href="' . esc_url($url) . '" target="_blank">Edit Linked Product</a>';
    } else {
        echo 'No product linked.';
    }
}

