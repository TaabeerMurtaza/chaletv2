<?php

// add_action('save_post_chalet', 'sync_chalet_subscription_products', 10, 3);

function sync_chalet_subscription_products($post_ID, $post, $update) {
    if ($post->post_status !== 'publish') return;

    $price = carbon_get_post_meta($post_ID, 'chalet_price');
    $duration = carbon_get_post_meta($post_ID, 'duration');
    $duration_type = carbon_get_post_meta($post_ID, 'duration_type');

    if (!$price) $price = 0;
    if (!$duration) $duration = 1;
    if (!$duration_type) $duration_type = 'day';

    // Step 1: Validate linked product IDs
    $linked_product_ids = carbon_get_post_meta($post_ID, 'chalet_subscriptions') ?: [];
    $valid_product_ids = [];

    foreach ($linked_product_ids as $pid) {
        if (get_post_status($pid) === 'publish' && get_post_type($pid) === 'product') {
            $valid_product_ids[] = $pid;
        }
    }

    // Step 2: Create new product if needed
    if (empty($valid_product_ids)) {
        $product = new WC_Product_Simple();
        $product->set_name($post->post_title);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_sold_individually(false);
        $product->save();

        $new_id = $product->get_id();
        update_post_meta($new_id, '_created_by_chalet', 'yes');
        update_post_meta($new_id, '_chalet_owner_id', $post_ID);

        $valid_product_ids = [$new_id];
    }

    // Step 3: Save valid IDs back to chalet
    carbon_set_post_meta($post_ID, 'chalet_subscriptions', $valid_product_ids);

    // Step 4: Update products and YITH subscription fields
    foreach ($valid_product_ids as $pid) {
        $product = wc_get_product($pid);
        $_pid = @$pid['id'] ?? $pid;
        if ($product) {
            $product->set_name($post->post_title);
            $product->set_regular_price($price);
            $product->set_sold_individually(false);
            $product->save();

            // YITH subscription fields
            update_post_meta($_pid, '_ywsbs_subscription', 'yes');
            update_post_meta($_pid, '_ywsbs_subscription_price', $price);
            update_post_meta($_pid, '_ywsbs_subscription_period', $duration);
            update_post_meta($_pid, '_ywsbs_subscription_period_type', $duration_type);
            update_post_meta($_pid, '_ywsbs_renewal_order', 'yes');
            update_post_meta($_pid, '_ywsbs_subscription_trial', 'no');

            // Link back to chalet (new bit)
            update_post_meta($_pid, '_chalet_owner_id', $post_ID);
        }
    }

    // Step 5: Cleanup
    cleanup_orphan_products();
}



function cleanup_orphan_products() {
    // Only get products that were created by the chalet system
    $chalet_products = get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'numberposts' => -1,
        'fields' => 'ids',
        'meta_key' => '_created_by_chalet',
        'meta_value' => 'yes',
    ]);

    $linked_products = [];

    $chalets = get_posts([
        'post_type' => 'chalet',
        'post_status' => 'any',
        'numberposts' => -1,
    ]);

    foreach ($chalets as $chalet) {
        $subs = carbon_get_post_meta($chalet->ID, 'chalet_subscriptions') ?: [];
        foreach ($subs as $_sub_id) {
            $sub_id = $_sub_id['id'] ?? $_sub_id;
            if (get_post_status($sub_id) === 'publish' && get_post_type($sub_id) === 'product') {
                $linked_products[] = intval($sub_id);
            }
        }
    }

    foreach ($chalet_products as $pid) {
        if (!in_array($pid, $linked_products)) {
            wp_trash_post($pid); // Or wp_delete_post($pid, true) if you want it gone forever
        }
    }
}


add_action('add_meta_boxes', function () {
    add_meta_box('linked_product_box', 'Linked WooCommerce Products', 'display_linked_product_meta', 'chalet', 'side');
});

function display_linked_product_meta($post) {
    $product_ids = carbon_get_post_meta($post->ID, 'chalet_subscriptions') ?: [];

    if ($product_ids) {
        echo '<ul>';
        foreach ($product_ids as $pid) {
            if (get_post_status($pid['id'])) {
                $url = get_edit_post_link($pid);
                echo '<li><a href="' . esc_url($url) . '" target="_blank">Edit Product #' . $pid['id'] . '</a></li>';
            }
        }
        echo '</ul>';
    } else {
        echo 'No products linked.';
    }
}
