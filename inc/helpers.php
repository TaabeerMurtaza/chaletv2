<?php
// Get linked WooCommerce Product ID from Chalet ID
function get_chalet_linked_product_id($chalet_id) {
    return get_post_meta($chalet_id, '_linked_product_id', true);
}

// Get WooCommerce Product object from Chalet ID
function get_chalet_product($chalet_id) {
    $product_id = get_chalet_linked_product_id($chalet_id);
    if ($product_id && get_post_status($product_id)) {
        return wc_get_product($product_id);
    }
    return false;
}

// Get Chalet ID from WooCommerce Product ID
function get_chalet_by_product_id($product_id) {
    $args = [
        'post_type'  => 'chalet',
        'meta_key'   => '_linked_product_id',
        'meta_value' => $product_id,
        'posts_per_page' => 1,
        'fields' => 'ids',
    ];
    $query = new WP_Query($args);
    return !empty($query->posts) ? $query->posts[0] : false;
}

// Get Chalet Pricing Details
function get_chalet_price_data($chalet_id) {
    return [
        'price'         => carbon_get_post_meta($chalet_id, 'chalet_price'),
        'duration'      => carbon_get_post_meta($chalet_id, 'duration'),
        'duration_type' => carbon_get_post_meta($chalet_id, 'duration_type'),
    ];
}

// Calculate Total Days from Chalet Fields
function get_chalet_total_days($chalet_id) {
    $data = get_chalet_price_data($chalet_id);
    $duration = $data['duration'] ?: 1;
    $type = $data['duration_type'] ?: 'day';
    return ($type === 'month') ? ($duration * 30) : $duration;
}


