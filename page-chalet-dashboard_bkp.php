<?php
/* Tem Name: Chalet Dashboard */
get_header();

// if (!is_user_logged_in()) {
//     echo '<div class="container"><p>You must be logged in to view this page.</p></div>';
//     get_footer();
//     exit;
// }

$current_user = wp_get_current_user();
$is_admin = current_user_can('manage_options');
$author_query = $is_admin ? [] : ['author' => $current_user->ID];
$edit_mode = isset($_GET['edit']) ? intval($_GET['edit']) : false;
$chalet_data = []; // Initialize for form population

// --- Display Transient Messages --- //
$message_data = get_transient('chalet_dashboard_message');
if ($message_data) {
    delete_transient('chalet_dashboard_message'); // Clear the transient
    $message_type = esc_attr($message_data['type']); // 'success' or 'error'
    $message_text = esc_html($message_data['text']);
    echo "<div class='notice notice-{$message_type} is-dismissible'><p>{$message_text}</p></div>";
}

// --- Prepare Data for Edit Form --- //
if ($edit_mode) {
    $post_to_edit = get_post($edit_mode);
    // Permission check: must exist, be a chalet, and user must be admin or author
    if (!$post_to_edit || $post_to_edit->post_type !== 'chalet') {
        echo '<div class="notice notice-error"><p>Invalid Chalet ID or permission denied. Cannot edit.</p></div>';
        $edit_mode = false; // Don't show edit form
    } else {
        // Populate $chalet_data for the form
        // If in edit mode, fetch chalet data from post
        $edit_mode = intval($_GET['edit']);
        $post_to_edit = get_post($edit_mode);

        if ($post_to_edit && $post_to_edit->post_type === 'chalet') {
            $chalet_data['ID'] = $edit_mode;
            $chalet_data['title'] = $post_to_edit->post_title;
            // $chalet_data['description'] = $post_to_edit->post_content;

            // Get Carbon Fields meta data
            $field_keys = array(
                // Information Tab
                'title',
                'description',
                'featured',
                'monthly_rate',
                'cleaning_fee',
                'cleaning_fee_type',
                'guest_count',
                'baths',
                'bedrooms',

                // Tariffs Tab
                'default_rate_weekend',
                'default_rate_weekday',
                'default_rate_week',
                'guests_included',
                'extra_price_adult',
                'extra_price_child',
                'free_for_babies',
                'min_nights',
                'tax_gst',
                'tax_thq',
                'tax_qst',
                'security_deposit',
                'extra_options',
                'checkin_time',
                'checkout_time',
                'early_checkin_options',
                'late_checkout_options',
                'seasonal_rates',

                // Terms Tab
                'reservation_policy',
                'cancellation_policy',
                'preparation_time',
                'reservation_window',
                'reservation_notice',
                'reservation_contract',

                // Instructions Tab
                'checkin_instructions',
                'checkin_instructions_days',
                'checkout_instructions',
                'checkout_instructions_days',
                'itinerary_instructions',
                'itinerary_instructions_days',
                'rules_reminder',
                'rules_reminder_days',
                'local_guide',
                'local_guide_days',
                'emergency_contact',

                // Media Tab
                'chalet_images',
                'video_link',

                // Amenities Tab
                'indoor_features',
                'outdoor_features',
                'kitchen_features',
                'family_features',
                'sports_features',
                'services_features',
                'accessibility_features',
                'events_features',

                // Location
                'chalet_location',
                'country',
                'province',
                'region',
                'full_address',

                // Rules Tab
                'rules_text',
                'ical_feed_url',

                // Integration Tab
                'booking_form_shortcode',
                'availability_calendar_shortcode',

                // Instructions Tab
                'parking_info',
                'notes',
                'host_message',
                'reservation_confirmation_message',
                'checkin_instructions',
                'wifi_info',
                'house_manual',

                // Media Tab
                'chalet_images',
                'video_link',

                // Amenities Tab
                'indoor_features',
                'outdoor_features',
                'kitchen_features',
                'family_features',
                'sports_features',
                'services_features',
                'accessibility_features',
                'events_features',

                // Location
                'chalet_location',

                // Rules Tab
                'rules_text',
                'cancellation_policy',
                'ical_feed_url',

                // Integration Tab
                'booking_form_shortcode',
                'availability_calendar_shortcode'
            );

            foreach ($field_keys as $key) {
                $chalet_data[$key] = carbon_get_post_meta($edit_mode, $key);
            }
        }
    }
}

// --- Display Dashboard --- //
?>
<div class="container chalet-dashboard-container" style="margin-top: 20px; margin-bottom: 20px;">
    <h1>Chalet Dashboard</h1>

    <?php if ($edit_mode):
        ?>
        <h2>Edit Chalet: <?php echo esc_html($chalet_data['title'] ?? '...'); ?></h2>
        <p><a href="<?php echo get_page_link(); // Link back to main dashboard ?>" class="button">&larr; Back to
                Dashboard</a></p>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="chalet_dashboard_save">
            <input type="hidden" name="chalet_action" value="edit">
            <input type="hidden" name="chalet_id" value="<?php echo esc_attr($edit_mode); ?>">
            <?php wp_nonce_field('edit_chalet', 'chalet_nonce'); ?>

            <?php
            // Pass data to the form template part
            // Ensure $chalet_data and $edit_mode are available in the template part scope
            global $chalet_data, $edit_mode;
            get_template_part('template-parts/chalet-dashboard-form');
            ?>

            <hr style="margin-top: 20px; margin-bottom: 20px;">
            <!-- <p><button type="submit" class="button button-primary">Update Chalet</button></p> -->
        </form>

    <?php else: ?>
        <h2>Your Chalets</h2>
        <!-- <a href="#add-new-chalet-form" class="button button-primary" style="margin-bottom: 15px;">Add New Chalet</a> -->
        <?php
        $args = array_merge([
            'post_type' => 'chalet',
            'posts_per_page' => -1,
            'post_status' => 'publish' // Consider adding 'draft' if needed: ['publish', 'draft']
        ], $author_query);
        $chalets_query = new WP_Query($args);

        if ($chalets_query->have_posts()):
            ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <!-- <th>Guests</th> -->
                        <!-- <th>Weekend Price</th> -->
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($chalets_query->have_posts()):
                        $chalets_query->the_post(); ?>
                        <?php
                        $chalet_id = get_the_ID();
                        $guests = carbon_get_post_meta($chalet_id, 'guests');
                        $weekend_price = carbon_get_post_meta($chalet_id, 'weekend_price');
                        // $edit_url = add_query_arg('edit', $chalet_id, get_page_link()); // Add edit param to current page URL
                        $edit_url = get_home_url() . "/chalet-dashboard?edit=" . $chalet_id;
                        // Create nonce for deletion
                        $delete_nonce = wp_create_nonce('delete_chalet_' . $chalet_id);
                        // Build delete URL pointing to admin-post.php
                        $delete_url = add_query_arg([
                            'action' => 'chalet_dashboard_delete',
                            'chalet_id' => $chalet_id,
                            '_wpnonce' => $delete_nonce
                        ], admin_url('admin-post.php'));
                        ?>
                        <tr>
                            <td><a href="<?php echo esc_url($edit_url); ?>"><?php the_title(); ?></a></td>
                            <!-- <td><?php echo esc_html($guests ?: 'N/A'); ?></td> -->
                            <!-- <td><?php echo esc_html($weekend_price ? '$' . number_format_i18n((float) $weekend_price, 2) : 'N/A'); ?> -->
                            </td>
                            <td><?php echo esc_html(ucfirst(get_post_status($chalet_id))); ?></td>
                            <td>
                                <a href="<?php echo esc_url($edit_url); ?>">Edit</a> |
                                <!-- <a href="<?php echo esc_url(get_permalink($chalet_id)); ?>" target="_blank">View</a> | -->
                                <!-- <a href="<?php echo esc_url($delete_url); ?>"
                                    onclick="return confirm('Are you sure you want to permanently delete this chalet? This action cannot be undone.');"
                                    style="color: red;">Delete</a> -->
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php
            wp_reset_postdata();
        else:
            echo '<p>You have not created any chalets yet.</p>';
        endif;
        ?>

        <hr style="margin-top: 30px; margin-bottom: 30px;">
        <?php if(0): ?>
        <h2 id="add-new-chalet-form">Add New Chalet</h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="chalet_dashboard_save">
            <input type="hidden" name="chalet_action" value="create">
            <?php wp_nonce_field('create_chalet', 'chalet_nonce'); ?>

            <?php
            // Reset $chalet_data for the add form
            $chalet_data = [];
            $edit_mode = false; // Ensure edit mode is false for add form
            global $chalet_data, $edit_mode;
            get_template_part('template-parts/chalet-dashboard-form');
            ?>

            <!-- <hr style="margin-top: 20px; margin-bottom: 20px;"> -->
            <!-- <p><button type="submit" class="button button-primary">Add Chalet</button></p> -->
        </form>
        <?php endif; ?>
    <?php endif; // End if($edit_mode) ?>

</div> <?php // .container ?>

<?php
// Enqueue necessary scripts (if not already done globally)
// Ensure JS file exists and path is correct
$theme_version = wp_get_theme()->get('Version'); // Get theme version from style.css
wp_enqueue_script('chalet-dashboard-js', get_template_directory_uri() . '/assets/js/chalet-dashboard.js', ['jquery', 'wp-util'], $theme_version, true);
wp_localize_script('chalet-dashboard-js', 'chaletDashboard', [
    'ajax_url' => admin_url('admin-ajax.php'), // If using AJAX
    'nonce' => wp_create_nonce('chalet_dashboard_ajax_nonce') // Example nonce for AJAX
]);

// Needed for Media Library integration
wp_enqueue_media();

get_footer();
?>