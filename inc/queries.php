<?php
/**
 * Handles custom form submissions and queries for ChaletV2 Theme.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Carbon_Fields\Carbon_Fields;

// --- Helper Functions --- //

/**
 * Safely retrieves a value from the $_POST superglobal.
 *
 * @param string $key     The key to look for in $_POST.
 * @param mixed  $default Optional. The default value to return if the key isn't found.
 * @return mixed The value from $_POST or the default value.
 */
function chaletv2_get_post_var($key, $default = null)
{
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

/**
 * Recursively sanitizes an array using specified callbacks.
 *
 * @param array $array           The array to sanitize.
 * @param array $sanitize_rules  An associative array where keys match array keys and values are sanitize functions (e.g., 'sanitize_text_field').
 * @return array The sanitized array.
 */
function chaletv2_sanitize_array(array $array, array $sanitize_rules): array
{
    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            // If the rule is also an array, recurse with nested rules
            $nested_rules = $sanitize_rules[$key] ?? [];
            if (is_array($nested_rules)) {
                $value = chaletv2_sanitize_array($value, $nested_rules);
            } else {
                // If no specific rule for sub-array, apply a default or skip
                // For now, let's apply sanitize_text_field recursively as a basic default
                $value = chaletv2_sanitize_array($value, array_fill_keys(array_keys($value), 'sanitize_text_field'));
            }
        } elseif (isset($sanitize_rules[$key]) && is_callable($sanitize_rules[$key])) {
            $value = call_user_func($sanitize_rules[$key], $value);
        } else {
            // Default sanitization if no specific rule is provided
            $value = sanitize_text_field($value);
        }
    }
    unset($value); // Unset reference
    return $array;
}


// --- Form Submission Handler --- //

/**
 * Handles the submission of the Chalet Dashboard form (Create/Edit).
 */
function chaletv2_handle_chalet_dashboard_submission()
{
    // Check if user is logged in (redundant check, but good practice)
    if (!is_user_logged_in()) {
        wp_die('You must be logged in to perform this action.', 'Permission Denied', ['response' => 403]);
    }

    $current_user = wp_get_current_user();
    $is_admin = current_user_can('manage_options');

    // Get Action and Nonce
    $form_action = chaletv2_get_post_var('chalet_action'); // 'create' or 'edit'
    $nonce = chaletv2_get_post_var('chalet_nonce');
    $nonce_action = $form_action === 'create' ? 'create_chalet' : 'edit_chalet';

    // Verify Nonce
    if (!$nonce || !wp_verify_nonce($nonce, $nonce_action)) {
        wp_die('Security check failed. Please try submitting the form again.', 'Nonce Verification Failed', ['response' => 403]);
    }

    // Get Chalet ID for editing
    $chalet_id = chaletv2_get_post_var('chalet_id') ? intval(chaletv2_get_post_var('chalet_id')) : null;

    // --- Basic Post Data --- //
    $title = sanitize_text_field(chaletv2_get_post_var('chalet_title', 'Untitled Chalet'));
    // Using 'description' field from form for post_content // REMOVED - Description is a Carbon Field
    // $content = wp_kses_post(chaletv2_get_post_var('description', ''));

    $post_data = [
        'post_title' => $title,
        // 'post_content' => $content, // REMOVED
        'post_content' => '', // Set post_content to empty
        'post_status' => 'publish', // Default to publish, adjust if draft status needed
        'post_type' => 'chalet',
    ];

    // --- Create or Update Post --- //
    $result_id = null;
    $message = '';
    $message_type = 'error'; // Default to error

    try {
        if ($form_action === 'create') {
            $post_data['post_author'] = $current_user->ID;
            $result_id = wp_insert_post($post_data, true); // Pass true to return WP_Error on failure
            if (is_wp_error($result_id)) {
                throw new Exception('Error creating chalet: ' . $result_id->get_error_message());
            }
            $chalet_id = $result_id; // Use the new ID
            $message = 'Chalet created successfully!';
            $message_type = 'success';

        } elseif ($form_action === 'edit' && $chalet_id) {
            $chalet = get_post($chalet_id);
            // Verify ownership or admin status
            if (!$chalet || $chalet->post_type !== 'chalet' || (!$is_admin && $chalet->post_author != $current_user->ID)) {
                throw new Exception('Permission denied or invalid Chalet ID.');
            }
            $post_data['ID'] = $chalet_id;
            $result_id = wp_update_post($post_data, true); // Pass true to return WP_Error on failure
            if (is_wp_error($result_id)) {
                throw new Exception('Error updating chalet: ' . $result_id->get_error_message());
            }
            $message = 'Chalet updated successfully!';
            $message_type = 'success';
            $result_id = $chalet_id; // Keep the existing ID for redirect

        } else {
            throw new Exception('Invalid action or missing Chalet ID for editing.');
        }

        // --- Save Custom Fields using Carbon Fields (Only if create/update was successful) --- //
        if ($chalet_id && ($form_action === 'create' || $form_action === 'edit')) {
            // Simple Text/Number Fields (Sanitize as text/numbers)
            $simple_fields = [
                'chalet_title' => 'sanitize_text_field', // Changed from title
                'guest_count' => 'intval',
                'baths' => 'intval',
                'description' => 'wp_kses_post',
                'chalet_code' => 'sanitize_text_field',
                'instant_booking' => 'sanitize_text_field', // Expect 'yes' or 'no'
                'base_price' => 'floatval',

                'default_rate_weekend' => 'floatval',
                'default_rate_weekday' => 'floatval',
                'default_rate_week' => 'floatval',

                'guests_included' => 'intval',
                'extra_price_adult' => 'floatval',
                'extra_price_child' => 'floatval',
                'free_for_babies' => 'sanitize_text_field',

                'checkin_time' => 'sanitize_text_field',
                'checkout_time' => 'sanitize_text_field',

                'checkin_instructions' => 'wp_kses_post',
                'checkin_instructions_days' => 'intval',
                'checkout_instructions' => 'wp_kses_post',
                'checkout_instructions_days' => 'intval',
                'itinerary_instructions' => 'wp_kses_post',
                'itinerary_instructions_days' => 'intval',
                'rules_reminder' => 'wp_kses_post',
                'rules_reminder_days' => 'intval',
                'local_guide' => 'wp_kses_post',
                'local_guide_days' => 'intval',
                'emergency_contact' => 'wp_kses_post',

                'preparation_time' => 'intval',
                'reservation_window' => 'intval',
                'reservation_notice' => 'intval',
                'reservation_contract' => 'wp_kses_post',

                'chalet_location' => 'sanitize_text_field',
                'country' => 'sanitize_text_field',
                'province' => 'sanitize_text_field',
                'region' => 'sanitize_text_field',
                'full_address' => 'sanitize_text_field',

                'weekly_discount' => 'intval',
                'monthly_rate' => 'intval',
                'monthly_discount' => 'intval',
                'security_deposit' => 'floatval',
                'additional_guest_fee' => 'floatval',
                'additional_guest_limit' => 'intval',
                'cleaning_fee_type' => 'wp_kses_post',
                'cleaning_fee' => 'floatval',
                'city_fee' => 'floatval',
                'min_nights' => 'intval',
                'max_nights' => 'intval',
                'tax_gst' => 'sanitize_text_field',
                'tax_thq' => 'sanitize_text_field',
                'tax_qst' => 'sanitize_text_field',
                'late_checkout_fee' => 'floatval',
                'reservation_policy' => 'wp_kses_post',
                'cancellation_policy' => 'wp_kses_post',
                'parking_info' => 'wp_kses_post',
                'notes' => 'wp_kses_post',
                'host_message' => 'wp_kses_post',
                'reservation_confirmation_message' => 'wp_kses_post',
                'wifi_info' => 'wp_kses_post',
                'house_manual' => 'wp_kses_post',
                'rules_text' => 'wp_kses_post',
                'contract' => 'wp_kses_post',
                'video_link' => 'esc_url_raw',
                'ical_feed_url' => 'esc_url_raw',
                'booking_form_shortcode' => 'sanitize_text_field',
                'availability_calendar_shortcode' => 'sanitize_text_field',
                'reservation_window_min' => 'intval',
                'reservation_window_max' => 'intval',
                'featured' => 'sanitize_text_field' // Added featured field
            ];
            // Get title from form field
            $title = sanitize_text_field(chaletv2_get_post_var('title', 'Untitled Chalet'));
            // Update post_data with the correct title field
            $post_data['post_title'] = $title;


            foreach ($simple_fields as $key => $sanitizer) {
                $value = chaletv2_get_post_var($key);
                if ($value !== null) {
                    if($sanitizer){
                        $sanitized_value = call_user_func($sanitizer, $value);
                    }else{
                        $sanitized_value = $value;
                    }
                    carbon_set_post_meta($chalet_id, $key, $sanitized_value);
                } else {
                    // Use set_post_meta with empty value to clear
                    carbon_set_post_meta($chalet_id, $key, '');
                }
            }

            // Gallery (Array of IDs)
            $gallery_ids = chaletv2_get_post_var('chalet_images', []);
            $sanitized_gallery_ids = [];
            if (is_array($gallery_ids)) {
                $sanitized_gallery_ids = array_map('intval', $gallery_ids);
                $sanitized_gallery_ids = array_filter($sanitized_gallery_ids); // Remove zeros/invalid IDs
            }
            carbon_set_post_meta($chalet_id, 'chalet_images', $sanitized_gallery_ids);

            // Bedrooms (Array of bedroom objects)
            $bedrooms = chaletv2_get_post_var('bedrooms', []);
            if (is_array($bedrooms)) {
                $sanitized_bedrooms = array_map(function($bedroom) {
                    return [
                        'bedroom_type' => sanitize_text_field($bedroom['bedroom_type'] ?? ''),
                        'num_beds' => intval($bedroom['num_beds'] ?? 1)
                    ];
                }, $bedrooms);
                carbon_set_post_meta($chalet_id, 'bedrooms', $sanitized_bedrooms);
            }

            // Amenities (Array of term IDs for each taxonomy)
            $amenity_keys = [
                'indoor_features',
                'outdoor_features',
                'kitchen_features',
                'family_features',
                'sports_features',
                'services_features',
                'accessibility_features',
                'events_features'
            ];
            foreach ($amenity_keys as $key) {
                $term_ids = chaletv2_get_post_var($key, []);
                $sanitized_term_ids = [];
                if (is_array($term_ids)) {
                    $sanitized_term_ids = array_map('intval', $term_ids);
                    $sanitized_term_ids = array_filter($sanitized_term_ids); // Remove zeros
                }
                // Carbon Fields handles association saving correctly with an array of IDs
                carbon_set_post_meta($chalet_id, $key, $sanitized_term_ids);
            }

            // Location (Map Field)
            $map_data_input = chaletv2_get_post_var('chalet_location', []);
            $map_data = [
                'address' => isset($map_data_input['address']) ? sanitize_text_field($map_data_input['address']) : '',
                'lat' => isset($map_data_input['lat']) ? floatval($map_data_input['lat']) : '',
                'lng' => isset($map_data_input['lng']) ? floatval($map_data_input['lng']) : '',
                'zoom' => isset($map_data_input['zoom']) ? intval($map_data_input['zoom']) : 10,
            ];
            // Save only if we have address or coords
            if (!empty($map_data['address']) || (!empty($map_data['lat']) && !empty($map_data['lng']))) {
                carbon_set_post_meta($chalet_id, 'chalet_location', $map_data);
            } else {
                // Delete meta if all parts are empty to clear the map
                // Use set_post_meta with empty value to clear
                carbon_set_post_meta($chalet_id, 'chalet_location', '');
            }

            // Chambers (Complex Field)
            $chambers_input = chaletv2_get_post_var('chalet_chambers', []);
            $sanitized_chambers = [];
            if (is_array($chambers_input)) {
                foreach ($chambers_input as $chamber) {
                    if (!is_array($chamber))
                        continue;
                    $sanitized_chamber = [
                        '_type' => 'chamber_group', // Assuming a fixed type
                        'chamber_name' => sanitize_text_field($chamber['chamber_name'] ?? ''),
                        'chamber_description' => wp_kses_post($chamber['chamber_description'] ?? ''),
                        'chamber_beds' => [], // Handle beds separately
                        'chamber_amenities' => [], // Handle amenities separately
                    ];

                    // Sanitize beds (assuming it's another complex field or similar structure)
                    if (isset($chamber['chamber_beds']) && is_array($chamber['chamber_beds'])) {
                        foreach ($chamber['chamber_beds'] as $bed) {
                            if (!is_array($bed))
                                continue;
                            $sanitized_chamber['chamber_beds'][] = [
                                '_type' => 'bed_group', // Assuming type
                                'bed_type' => sanitize_text_field($bed['bed_type'] ?? ''),
                                'bed_count' => intval($bed['bed_count'] ?? 0),
                            ];
                        }
                    }
                    // Sanitize amenities (assuming array of term IDs)
                    if (isset($chamber['chamber_amenities']) && is_array($chamber['chamber_amenities'])) {
                        $sanitized_chamber['chamber_amenities'] = array_map('intval', $chamber['chamber_amenities']);
                        $sanitized_chamber['chamber_amenities'] = array_filter($sanitized_chamber['chamber_amenities']);
                    }

                    // Only add if chamber has a name
                    if (!empty($sanitized_chamber['chamber_name'])) {
                        $sanitized_chambers[] = $sanitized_chamber;
                    }
                }
            }
            carbon_set_post_meta($chalet_id, 'chalet_chambers', $sanitized_chambers);


        } // End saving custom fields

    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'error';
        // We might not have a result_id if creation failed early
        $result_id = ($form_action === 'edit' && $chalet_id) ? $chalet_id : null;
    }

    // --- Set Transient Message --- //
    if ($message) {
        set_transient('chalet_dashboard_message', ['type' => $message_type, 'text' => $message], 30); // Store for 30 seconds
    }

    // --- Redirect --- //
    // Redirect back to the dashboard page. Try to get the dashboard page URL dynamically.
    // Assume the dashboard is the current page IF the submission came from it.
    $redirect_url = wp_get_referer(); // Get the referring URL

    // If referer is not available or doesn't seem right, fallback to a known dashboard slug or ID.
    // Replace 'chalet-dashboard' with your actual page slug if different.
    if (!$redirect_url || strpos($redirect_url, 'wp-admin/admin-post.php') !== false) {
        $dashboard_page = get_page_by_path('chalet-dashboard'); // Or use get_option('page_on_front') etc.
        if ($dashboard_page) {
            $redirect_url = get_permalink($dashboard_page->ID);
        } else {
            $redirect_url = home_url('/'); // Fallback to home URL
        }
    }

    // If created/updated successfully, redirect to the edit view of the chalet
    if ($message_type === 'success' && $result_id) {
        $redirect_url = add_query_arg('edit', $result_id, remove_query_arg(['edit', 'delete'], $redirect_url));
    } elseif ($form_action === 'edit' && $chalet_id) {
        // If update failed, redirect back to the edit screen of the same chalet
        $redirect_url = add_query_arg('edit', $chalet_id, remove_query_arg(['edit', 'delete'], $redirect_url));
    } else {
        // If create failed, redirect back to the main dashboard view (remove edit/delete args)
        $redirect_url = remove_query_arg(['edit', 'delete'], $redirect_url);
    }

    // Add a timestamp to prevent browser caching issues after redirect
    $redirect_url = add_query_arg('t', time(), $redirect_url);

    wp_safe_redirect($redirect_url);
    exit;
}
add_action('admin_post_chalet_dashboard_save', 'chaletv2_handle_chalet_dashboard_submission');


/**
 * Handles Chalet Deletion via admin-post
 */
function chaletv2_handle_chalet_delete()
{
    if (!is_user_logged_in()) {
        wp_die('You must be logged in.', 'Permission Denied', ['response' => 403]);
    }

    if (!isset($_GET['chalet_id']) || !isset($_GET['_wpnonce'])) {
        wp_die('Missing parameters.', 'Bad Request', ['response' => 400]);
    }

    $delete_id = intval($_GET['chalet_id']);
    $nonce = $_GET['_wpnonce'];

    if (!wp_verify_nonce($nonce, 'delete_chalet_' . $delete_id)) {
        wp_die('Security check failed.', 'Nonce Verification Failed', ['response' => 403]);
    }

    $chalet_to_delete = get_post($delete_id);
    $current_user = wp_get_current_user();
    $is_admin = current_user_can('manage_options');

    if (!$chalet_to_delete || $chalet_to_delete->post_type !== 'chalet' || (!$is_admin && $chalet_to_delete->post_author != $current_user->ID)) {
        set_transient('chalet_dashboard_message', ['type' => 'error', 'text' => 'Error deleting chalet or permission denied.'], 30);
    } else {
        $delete_result = wp_delete_post($delete_id, true); // true = force delete, bypass trash
        if ($delete_result) {
            set_transient('chalet_dashboard_message', ['type' => 'success', 'text' => 'Chalet deleted successfully.'], 30);
        } else {
            set_transient('chalet_dashboard_message', ['type' => 'error', 'text' => 'Failed to delete chalet.'], 30);
        }
    }

    // Redirect back to the dashboard (remove delete/edit params)
    $redirect_url = wp_get_referer();
    if (!$redirect_url || strpos($redirect_url, 'wp-admin/admin-post.php') !== false) {
        $dashboard_page = get_page_by_path('chalet-dashboard');
        if ($dashboard_page) {
            $redirect_url = get_permalink($dashboard_page->ID);
        } else {
            $redirect_url = home_url('/');
        }
    }
    $redirect_url = remove_query_arg(['action', 'chalet_id', '_wpnonce', 'edit', 'delete', 't']);
    $redirect_url = add_query_arg('t', time(), $redirect_url);

    wp_safe_redirect($redirect_url);
    exit;

}
add_action('admin_post_chalet_dashboard_delete', 'chaletv2_handle_chalet_delete');

?>