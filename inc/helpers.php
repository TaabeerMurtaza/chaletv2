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



// /**
//  * Cancel a user's active subscription.
//  */
// function chalet_cancel_user_subscription($user_id, $product_id = null) {
//     $subscriptions = wcs_get_users_subscriptions($user_id);

//     foreach ($subscriptions as $subscription) {
//         foreach ($subscription->get_items() as $item) {
//             if (!$product_id || $item->get_product_id() == $product_id) {
//                 $subscription->update_status('cancelled');
//             }
//         }
//     }
// }

// /**
//  * Pause a user's subscription (set to 'on-hold')
//  */
// function chalet_pause_user_subscription($user_id, $product_id = null) {
//     $subscriptions = wcs_get_users_subscriptions($user_id);

//     foreach ($subscriptions as $subscription) {
//         foreach ($subscription->get_items() as $item) {
//             if (!$product_id || $item->get_product_id() == $product_id) {
//                 $subscription->update_status('on-hold');
//             }
//         }
//     }
// }

// /**
//  * Reactivate paused subscription
//  */
// function chalet_resume_user_subscription($user_id, $product_id = null) {
//     $subscriptions = wcs_get_users_subscriptions($user_id);

//     foreach ($subscriptions as $subscription) {
//         foreach ($subscription->get_items() as $item) {
//             if (!$product_id || $item->get_product_id() == $product_id) {
//                 if ($subscription->get_status() === 'on-hold') {
//                     $subscription->update_status('active');
//                 }
//             }
//         }
//     }
// }

// /**
//  * Get all active subscriptions for a user
//  */
// function chalet_get_active_subscriptions($user_id) {
//     $active_subs = [];
//     $subscriptions = wcs_get_users_subscriptions($user_id);

//     foreach ($subscriptions as $subscription) {
//         if ($subscription->has_status('active')) {
//             $active_subs[] = $subscription;
//         }
//     }

//     return $active_subs;
// }