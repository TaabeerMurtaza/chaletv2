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
        } else {
            // TODO
        }
    }
    return $active;
}
function cancel_subscription($sub_id)
{
    if (!$sub_id) {
        return false;
    }
    $order = wc_get_order(wps_sfw_get_meta_data($sub_id, 'wps_parent_order', true));
    $is_admin = current_user_can('manage_options');
    if (!$order) {
        return false;
    }
    if (!$is_admin && $order->get_user_id() !== get_current_user_id()) {
        return false; // User is not authorized to cancel this subscription
    }
    $order->update_status('cancelled', __('Subscription cancelled by user.', 'textdomain'));
    // Optionally, you can also update the subscription status
    wps_sfw_update_meta_data($sub_id, 'wps_subscription_status', 'cancelled');
    update_unpaid_chalet_slots(); // Reset unpaid chalet slots
    return true;
}
function update_unpaid_chalet_slots()
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        return false;
    }
    $subscriptions = get_user_subscriptions($user_id);
    if (empty($subscriptions)) {
        return false;
    }
    $slots = total_chalet_slots($user_id);
    $args = [
        'post_type' => 'chalet',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'author' => $user_id,
    ];
    $query = new WP_Query($args);
    if (!$query->have_posts()) {
        return false; // No chalets found
    }
    $chalets = $query->posts; // not using get_my_chalets() because it may be admin doing this action
    $chalets_count = count($chalets);
    // Unpublish the unpaid chalet slots
    if ($chalets_count != 0 && $chalets_count > $slots) {
        // Trim the chalets array to the number of unpaid slots
        $unpaid = array_slice($chalets, $slots);
        foreach ($unpaid as $chalet) {
            wp_update_post([
                'ID' => $chalet->ID,
                'post_status' => 'draft',
            ]);
            // Optionally, you can also delete the chalet's linked product
            $product_id = get_chalet_linked_product_id($chalet->ID);
            if ($product_id) {
                wp_delete_post($product_id, true); // Force delete the product
            }
        }
    }
    return true;
}
function get_subscription_details($sub_id)
{
    $subscription = [];
    $order = wc_get_order(wps_sfw_get_meta_data($sub_id, 'wps_parent_order', true));
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $sub_id = get_post_meta($product_id, 'subscription_id', true);
        if (!$sub_id) {
            continue; // Skip if no subscription ID is found
        }
        $product = wc_get_product($product_id);
        $subscription['name'] = $product ? $product->get_name() : '';
        $sub_cpt = get_post($sub_id);

        if ($sub_cpt) {
            $subscription['id'] = $sub_cpt->ID;
            $subscription['description'] = carbon_get_post_meta($sub_cpt->ID, 'subscription_description');
            $subscription['color'] = carbon_get_post_meta($sub_cpt->ID, 'subscription_color');
        } else {
            $subscription['id'] = 0;
            $subscription['description'] = 0;
            $subscription['color'] = get_random_color(); // Fallback color if no subscription CPT found
        }
    }
    return $subscription;
}
function get_subscription_cpt($sub_id)
{
    $subscription = [];
    $sub_cpt = false;
    $order = wc_get_order(wps_sfw_get_meta_data($sub_id, 'wps_parent_order', true));
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $sub_id = get_post_meta($product_id, 'subscription_id', true);
        if (!$sub_id) {
            continue; // Skip if no subscription ID is found
        }
        $product = wc_get_product($product_id);
        $subscription['name'] = $product ? $product->get_name() : '';
        $sub_cpt = get_post($sub_id);
    }
    return $sub_cpt;
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
function total_featured_slots($user_id = false)
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
            $allowed = get_post_meta($product_id, 'featured_allowed', true);
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
function get_available_featured_slots($user_id = false)
{
    $user_id = $user_id ?: get_current_user_id();
    if (!$user_id) {
        return 0;
    }
    $slots = total_featured_slots($user_id);
    $chalets = count(get_my_chalets());
    $featured_chalets = count(array_filter(get_my_chalets(), function ($chalet) {
        return carbon_get_post_meta($chalet->ID, 'featured');
    }));
    if ($slots <= $featured_chalets) {
        return 0; // No available slots
    } else {
        return $slots - $featured_chalets; // Return available slots
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
function check_pending_payments()
{
    // Check all bookings for pending payments based on new payment structure
    $query = new WP_Query([
        'post_type' => 'booking',
        'posts_per_page' => -1,
        'post_status' => 'any',
        'fields' => 'ids',
    ]);
    if (empty($query->posts)) {
        return;
    }

    foreach ($query->posts as $booking_id) {
        $payment_id = carbon_get_post_meta($booking_id, 'payment_id');
        $payment_id = intval($payment_id);
        if (!$payment_id) {
            continue;
        }

        // Loop through up to 5 scheduled payments (see CustomStructures.php Payment Schedule)
        for ($i = 1; $i <= 5; $i++) {
            $amount = carbon_get_post_meta($payment_id, "payment_{$i}_amount");
            $date = carbon_get_post_meta($payment_id, "payment_{$i}_date");
            $status = carbon_get_post_meta($payment_id, "payment_{$i}_status");

            if (!$amount || !$date || $status === 'completed') {
                continue;
            }

            $timestamp = strtotime($date);
            if ($timestamp && $timestamp <= time()) {
                // Attempt payment
                $result = excecute_payment($booking_id, floatval($amount));
                if (!empty($result['success']) && $result['success'] === 'completed') {
                    carbon_set_post_meta($payment_id, "payment_{$i}_status", 'completed');

                    // Create an invoice for this payment
                    $booking_post = get_post($booking_id);
                    if ($booking_post) {
                        $invoice_id = wp_insert_post([
                            'post_type' => 'invoice',
                            'post_status' => 'publish',
                            'post_title' => 'Invoice for Booking #' . $booking_id . " - Payment {$i}",
                            'post_author' => $booking_post->post_author,
                        ]);
                        if ($invoice_id && !is_wp_error($invoice_id)) {
                            carbon_set_post_meta($invoice_id, 'booking_id', $booking_id);
                            carbon_set_post_meta($invoice_id, 'payment_date', date('Y-m-d'));
                            carbon_set_post_meta($invoice_id, 'total_amount', $amount);
                            carbon_set_post_meta($invoice_id, 'status', 'paid');
                            carbon_set_post_meta($invoice_id, 'invoice_type', 'booking');
                            carbon_set_post_meta($invoice_id, 'name', get_post_meta($booking_id, 'guest_first_name', true) . ' ' . get_post_meta($booking_id, 'guest_last_name', true));
                            carbon_set_post_meta($invoice_id, 'email', get_post_meta($booking_id, 'guest_email', true));
                            carbon_set_post_meta($invoice_id, 'phone', get_post_meta($booking_id, 'guest_phone', true));
                            // Optionally link to chalet
                            $chalet_id = carbon_get_post_meta($booking_id, 'booking_chalet');
                            if ($chalet_id) {
                                carbon_set_post_meta($invoice_id, 'chalet', [$chalet_id]);
                            }
                        }
                    }
                } else {
                    carbon_set_post_meta($payment_id, "payment_{$i}_status", 'failed');
                    echo '<pre>';
                    print_r($result);
                    echo '</pre>';
                    exit;
                }
            }
        }
    }
}

function excecute_payment($booking_id, $amount)
{
    // // TODO: Implement the payment logic here
    \Stripe\Stripe::setApiKey('sk_test_51MgvO9JY9fEdgWqYkqZfPSaPeHwtDDtptbTLlrgXuXOEMrBR9fG11jQ39yLyrbFyo13bL4bTl7Tr0eExAA0aCJS900ynWnHJkX'); // use env var for prod
    try {
        // Get Stripe customer and payment method from booking meta
        $customer_id = carbon_get_post_meta($booking_id, 'stripe_customer_id');
        $payment_method = carbon_get_post_meta($booking_id, 'stripe_payment_method_id');

        // If missing, try to create/retrieve from Stripe and save to booking
        if (!$customer_id) {
            // Try to get guest email from booking
            $guest_email = carbon_get_post_meta($booking_id, 'guest_email');
            print_r($guest_email);
            
            if ($guest_email) {
                // Try to find existing Stripe customer by email
                $stripe_customers = \Stripe\Customer::all(['email' => $guest_email, 'limit' => 1]);
                if (!empty($stripe_customers->data)) {
                    $customer_id = $stripe_customers->data[0]->id;
                } else {
                    // Create new customer
                    $customer = \Stripe\Customer::create([
                        'email' => $guest_email,
                        'name' => get_post_meta($booking_id, 'guest_first_name', true) . ' ' . get_post_meta($booking_id, 'guest_last_name', true),
                        'phone' => get_post_meta($booking_id, 'guest_phone', true),
                    ]);
                    $customer_id = $customer->id;
                }
                if ($customer_id) {
                    carbon_set_post_meta($booking_id, 'stripe_customer_id', $customer_id);
                }
            }
        }

        if (!$payment_method && $customer_id) {
            // Try to get the customer's default payment method from Stripe
            $payment_methods = \Stripe\PaymentMethod::all([
                'customer' => $customer_id,
                'type' => 'card',
                'limit' => 1,
            ]);
            if (!empty($payment_methods->data)) {
                $payment_method = $payment_methods->data[0]->id;
                carbon_set_post_meta($booking_id, 'stripe_payment_method_id', $payment_method);
            }
        }
        if (!$customer_id || !$payment_method) {
            return [
                'success' => false,
                'message' => 'Missing Stripe customer or payment method for booking ID ' . $booking_id,
            ];
        }

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => intval($amount * 100), // Stripe expects amount in cents
            'currency' => 'cad',
            'customer' => $customer_id,
            'payment_method' => $payment_method,
            'off_session' => true,
            'confirm' => true,
        ]);

        if ($paymentIntent->status === 'succeeded') {
            return [
                'success' => 'completed',
                'message' => 'Payment of ' . wc_price($amount) . ' processed successfully for booking ID ' . $booking_id,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Payment failed with status: ' . $paymentIntent->status,
            ];
        }
    } catch (\Stripe\Exception\CardException $e) {
        return [
            'success' => false,
            'message' => 'Card declined: ' . $e->getError()->message,
        ];
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return [
            'success' => false,
            'message' => 'Stripe API error: ' . $e->getMessage(),
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Payment error: ' . $e->getMessage(),
        ];
    }

    // try {
    //     $paymentIntent = \Stripe\PaymentIntent::create([
    //         'amount' => 5000, // amount in cents
    //         'currency' => 'usd',
    //         'customer' => 'cus_xxx',
    //         'payment_method' => 'pm_xxx',
    //         'off_session' => true,
    //         'confirm' => true,
    //     ]);
    //     echo "Payment successful: " . $paymentIntent->id;
    // } catch (\Stripe\Exception\ApiErrorException $e) {
    //     echo "Payment failed: " . $e->getMessage();
    // }

    return [
        'success' => 'completed', // Set to false if payment fails
        'message' => 'Payment of ' . wc_price($amount) . ' processed failed for booking ID ' . $booking_id,
    ];
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