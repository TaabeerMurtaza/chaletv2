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
        'chalet_type',
        'description',
        'monthly_rate',
        'cleaning_fee',
        'guest_count',
        'baths',
        'bedrooms',
        'default_rate_weekend',
        'default_rate_weekday',
        'default_rate_week',
        'guests_included',
        'extra_price_adult',
        'extra_price_child',
        'free_for_babies',
        'checkin_unavailable_days',
        'checkout_unavailable_days',
        'early_checkin_unavailable_days',
        'late_checkout_unavailable_days',
        'min_nights',
        'tax_gst',
        'tax_thq',
        'tax_qst',

        'indoor_features',
        'outdoor_features',
        'kitchen_features',
        'family_features',
        'sports_features',
        'services_features',
        'accessibility_features',
        'events_features',

        'seasonal_rates',

        'security_deposit',
        'extra_options',
        'checkin_time',
        'checkout_time',
        'price_night_monday',
        'price_night_tuesday',
        'price_night_wednesday',
        'price_night_thursday',
        'price_night_friday',
        'price_night_saturday',
        'price_night_sunday',

        'citq_document',
        'chalet_images',

        'preparation_time',
        'reservation_policy',
        'cancellation_policy',
        'reservation_contract',
        'reservation_window',
        'reservation_notice',
        'checkin_instructions_days',
        'checkin_instructions',
        'checkout_instructions_days',
        'checkout_instructions',
        'itinerary_instructions_days',
        'itinerary_instructions',
        'rules_reminder_days',
        'rules_reminder',
        'local_guide',
        'local_guide_days',
        'emergency_contact',
        'video_link',
        'country',
        'city',
        'province',
        'full_address'
    ];
    foreach ($basic_fields as $field) {
        $data[$field] = carbon_get_post_meta($chalet_id, $field);
    }

    return $data;
}
function get_my_chalets($query_only = false)
{
    if (current_user_can('manage_options')) {
        // Admin: return all chalets
        $args = [
            'post_type' => 'chalet',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ];
    } else {
        // Non-admin: return only chalets authored by current user
        $args = [
            'post_type' => 'chalet',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'author' => get_current_user_id(),
        ];
    }
    $query = new WP_Query($args);
    return $query_only ? $query : $query->posts;
}
function has_subscription($user_id = false)
{
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    if (!$user_id) {
        return false;
    }
    return has_available_chalet_slot($user_id);
}
function get_subscription_name($sub_id)
{
    $order = wc_get_order(wps_sfw_get_meta_data($sub_id, 'wps_parent_order', true));
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $sub_id = get_post_meta($product_id, 'subscription_id', true);
        if (!$sub_id) {
            continue; // Skip if no subscription ID is found
        }
        $product = wc_get_product($product_id);
        if ($product) {
            return $product->get_name();
        }
    }
}
function get_subscription_slots($sub_id)
{
    $order = wc_get_order(wps_sfw_get_meta_data($sub_id, 'wps_parent_order', true));
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $sub_id = get_post_meta($product_id, 'subscription_id', true);
        if (!$sub_id) {
            continue; // Skip if no subscription ID is found
        }
        return get_post_meta($product_id, 'chalets_allowed', true) ?: 0;
    }
}
function get_featured_slots($sub_id)
{
    $order = wc_get_order(wps_sfw_get_meta_data($sub_id, 'wps_parent_order', true));
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $sub_id = get_post_meta($product_id, 'subscription_id', true);
        if (!$sub_id) {
            continue; // Skip if no subscription ID is found
        }
        return get_post_meta($product_id, 'featured_allowed', true) ?: 0;
    }
}

function get_user_subscriptions($user_id = false)
{
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    if (!$user_id) {
        return [];
    }
    $args = array(
        'number' => 10,
        'return' => 'ids',
        'type' => 'wps_subscriptions',
        'meta_query' => array(
            'key' => 'wps_customer_id',
            'compare' => 'EXISTS',
        ),
    );
    if (isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {
        // Logic to fetch subscription using subscription id or parent id.
        $maybe_subscription_or_parent_id = (int) sanitize_text_field(wp_unslash($_REQUEST['s']));

        $sub_id = wps_sfw_get_meta_data($maybe_subscription_or_parent_id, 'wps_parent_order', true);
        if ($sub_id) {
            $maybe_subscription_or_parent_id = $sub_id;
        }
        if ($maybe_subscription_or_parent_id) {
            $args['meta_query'] = array(
                array(
                    'key' => 'wps_parent_order',
                    'value' => $maybe_subscription_or_parent_id,
                    'compare' => 'LIKE',
                ),
            );
        } else {
            $username_or_email = sanitize_text_field(wp_unslash($_REQUEST['s']));
            // Logic to fetch subscription using username or email.

            $user = get_user_by('email', $username_or_email);

            // If no user is found by email, try to get by username.
            if (!$user) {
                $user = get_user_by('login', $username_or_email);
            }
            $customer_id = $user ? $user->ID : false;

            $args['meta_query'] = array(
                array(
                    'key' => 'wps_customer_id',
                    'value' => $customer_id,
                    'compare' => 'LIKE',
                ),
            );
        }
    }
    $subs = wc_get_orders($args) ?: [];
    $active = [];
    foreach ($subs as $order) {
        $wps_subscription_status = wps_sfw_get_meta_data($order, 'wps_subscription_status', true);
        if ($wps_subscription_status == 'active') {
            $active[] = $order;
        }
    }
    return $active;
}
function total_chalet_slots($user_id = false)
{
    $user_id = $user_id ?: get_current_user_id();
    if (!$user_id) {
        return 0;
    }
    $subscriptions = get_user_subscriptions($user_id);
    if (empty($subscriptions)) {
        return 0;
    }
    $slots = 0;
    foreach ($subscriptions as $id) {
        $order = wc_get_order(wps_sfw_get_meta_data($id, 'wps_parent_order', true));
        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            $sub_id = get_post_meta($product_id, 'subscription_id', true);
            if (!$sub_id) {
                continue; // Skip if no subscription ID is found
            }
            $allowed = get_post_meta($product_id, 'chalets_allowed', true);
            $slots += intval($allowed);
        }
    }
    return $slots;
}
function get_available_chalet_slots($user_id = false)
{
    $user_id = $user_id ?: get_current_user_id();
    if (!$user_id) {
        return 0;
    }
    $slots = total_chalet_slots($user_id);
    $chalets = count(get_my_chalets());
    if ($slots <= $chalets) {
        return 0; // No available slots
    } else {
        return $slots - $chalets; // Return available slots
    }

}
function has_available_chalet_slot($user_id = false)
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }
    $subscriptions = get_user_subscriptions($user_id);
    if (empty($subscriptions)) {
        return false;
    }
    // $slots = 0;
    $slots = total_chalet_slots($user_id);
    $chalets = count(get_my_chalets());
    // foreach ($subscriptions as $id) {
    //     $order = wc_get_order(wps_sfw_get_meta_data($id, 'wps_parent_order', true));
    //     foreach ($order->get_items() as $item) {
    //         $product_id = $item->get_product_id();
    //         $sub_id = get_post_meta($product_id, 'subscription_id', true);
    //         if(!$sub_id) {
    //             continue; // Skip if no subscription ID is found
    //         }
    //         $allowed = get_post_meta($product_id, 'chalets_allowed', true);
    //         $slots += intval($allowed);
    //     }
    // }
    return $slots > $chalets;
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