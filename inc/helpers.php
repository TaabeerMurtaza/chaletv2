<?php
// Get linked WooCommerce Product ID from Chalet ID
function get_chalet_linked_product_id($chalet_id)
{
    return get_post_meta($chalet_id, '_linked_product_id', true);
}

// Get WooCommerce Product object from Chalet ID
function get_chalet_product($chalet_id)
{
    $product_id = get_chalet_linked_product_id($chalet_id);
    if ($product_id && get_post_status($product_id)) {
        return wc_get_product($product_id);
    }
    return false;
}

// Get Chalet ID from WooCommerce Product ID
function get_chalet_by_product_id($product_id)
{
    $args = [
        'post_type' => 'chalet',
        'meta_key' => '_linked_product_id',
        'meta_value' => $product_id,
        'posts_per_page' => 1,
        'fields' => 'ids',
    ];
    $query = new WP_Query($args);
    return !empty($query->posts) ? $query->posts[0] : false;
}

// Get Chalet Pricing Details
function get_chalet_price_data($chalet_id)
{
    return [
        'price' => carbon_get_post_meta($chalet_id, 'chalet_price'),
        'duration' => carbon_get_post_meta($chalet_id, 'duration'),
        'duration_type' => carbon_get_post_meta($chalet_id, 'duration_type'),
    ];
}

// Calculate Total Days from Chalet Fields
function get_chalet_total_days($chalet_id)
{
    $data = get_chalet_price_data($chalet_id);
    $duration = $data['duration'] ?: 1;
    $type = $data['duration_type'] ?: 'day';
    return ($type === 'month') ? ($duration * 30) : $duration;
}
function get_my_bookings()
{
    $query = new WP_Query([
        'post_type' => 'booking',
        'posts_per_page' => -1,
    ]);
    return $query->posts;
}
function get_random_color()
{
    $colors = [
        '#65E80A',
        '#65047F',
        '#539308',
        '#AFB22C',
        '#AA825E',
        '#D4726D',
        '#39F126',
        '#2BCE85',
        '#D32ABA',
        '#4FE7EC',
        '#0C2E86',
        '#08D8C1',
        '#30035A',
        '#58CE29',
        '#49D4A6',
        '#E7871F',
        '#90896E',
        '#12AF9E',
        '#1FA730',
        '#0913C9',
        '#C28EE0',
        '#1543DA',
        '#6F4518',
        '#ED4DE5',
        '#1FE5E2',
    ];

    return $colors[array_rand($colors)];
}

function get_chalet_data($chalet_id = false)
{
    if (!$chalet_id) {
        $chalet_id = get_the_ID();
    }
    $chalet = get_post($chalet_id);
    if (!$chalet || $chalet->post_type !== 'chalet') {
        return false;
    }
    $data = [
        'id' => $chalet->ID,
        'title' => $chalet->post_title,
        'status' => $chalet->post_status,
    ];
    $basic_fields = [
        'description',
        'monthly_rate',
        'cleaning_fee',
        'guest_count',
        'baths',
        'default_rate_weekend',
        'default_rate_weekday',
        'default_rate_week',
        'guests_included',
        'extra_price_adult',
        'extra_price_child',
        'min_nights',
        'tax_gst',
        'tax_thq',
        'tax_qst',
        'security_deposit',
        'preparation_time',
        'reservation_window',
        'reservation_notice',
        'checkin_instructions_days',
        'checkout_instructions_days',
        'itinerary_instructions_days',
        'rules_reminder_days',
        'local_guide_days',
        'emergency_contact',
        'video_link',
        'country',
        'province',
        'full_address'
    ];
    foreach( $basic_fields as $field ) {
        $data[$field] = carbon_get_post_meta($chalet_id, $field);
    }

    return $data;
}
function get_my_chalets()
{
    if (current_user_can('manage_options')) {
        // Admin: return all chalets
        $args = [
            'post_type' => 'chalet',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ];
    } else {
        // Non-admin: return only chalets authored by current user
        $args = [
            'post_type' => 'chalet',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'author' => get_current_user_id(),
        ];
    }
    $query = new WP_Query($args);
    return $query->posts;
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