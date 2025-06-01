<?php
/**
 * Template part for displaying the chalet dashboard form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ChaletV2
 */

// Ensure $chalet_data and $edit_mode are available
// These variables should be passed from page-chalet-dashboard.php
global $chalet_data, $edit_mode;

$chalet_id = $edit_mode;
// If not set globally, try to get them from request parameters
if (!isset($chalet_data) || empty($chalet_data)) {
    $chalet_data = [];
}

// Helper function to safely get data from $chalet_data (assuming it holds Carbon Fields data or is processed)
function get_chalet_field_value($field_name, $default = '')
{
    global $chalet_data;
    // Adjust this logic based on how $chalet_data is structured (direct meta vs. Carbon Fields get_post_meta)
    return isset($chalet_data[$field_name]) ? esc_attr($chalet_data[$field_name]) : $default;
}

// Helper function to check if a checkbox/association should be checked
function is_chalet_field_checked($field_name, $value_to_check, $is_association = false)
{
    global $chalet_data;
    if (!isset($chalet_data[$field_name])) {
        return false;
    }

    $field_value = $chalet_data[$field_name];

    if ($is_association) {
        // Assuming association field returns an array of post IDs or similar
        if (!is_array($field_value))
            return false;
        // Check if the value_to_check (e.g., post ID) exists in the array
        return in_array($value_to_check, $field_value);
        // If association stores more complex data (like arrays of ['id' => X]), adjust accordingly:
        /*
        foreach ($field_value as $item) {
            if (isset($item['id']) && $item['id'] == $value_to_check) {
                return true;
            }
        }
        return false;
        */
    } else {
        // For simple checkboxes or multi-selects stored as arrays
        if (is_array($field_value)) {
            return in_array($value_to_check, $field_value);
        } else {
            // For single checkboxes with specific option value
            return $field_value == $value_to_check;
        }
    }
}

// Helper function to get features by category slug (adjust post type if needed)
function get_features_by_category($category_slug_or_type)
{
    $args = array(
        'post_type' => 'chalet_feature', // Changed from 'feature'
        'posts_per_page' => -1,
        'meta_query' => array( // Query by meta field instead of taxonomy
            array(
                'key' => '_feature_type', // Carbon field key
                'value' => $category_slug_or_type,
            )
        ),
        'orderby' => 'title',
        'order' => 'ASC',
    );
    $features = get_posts($args);
    return $features;
}

?>

<form id="chalet-dashboard-form" method="post" action="" enctype="multipart/form-data">
    <?php wp_nonce_field('chalet_dashboard_nonce', 'chalet_dashboard_nonce_field'); ?>
    <input type="hidden" name="action" value="chalet_dashboard_save">
    <input type="hidden" name="form_action" value="<?php echo $edit_mode ? 'edit_chalet' : 'add_chalet'; ?>">
    <?php if ($edit_mode): ?>
        <input type="hidden" name="chalet_id" value="<?php echo esc_attr($edit_mode); ?>">
    <?php endif; ?>

    <h2><?php echo $edit_mode ? __('Edit Chalet', 'chaletv2') : __('Add New Chalet', 'chaletv2'); ?></h2>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs" id="chalet-dashboard-tabs" role="tablist"
        style="list-style: none; padding: 0; margin-bottom: 15px; border-bottom: 1px solid #ddd;">
        <li role="presentation" class="active" style="display: inline-block; margin-right: 5px;"><a
                href="#tab-information" aria-controls="tab-information" role="tab" data-toggle="tab"
                style="padding: 10px 15px; display: block; border: 1px solid #ddd; border-bottom: none; background: #eee; text-decoration: none;"><?php _e('Information', 'chaletv2'); ?></a>
        </li>
        <li role="presentation" style="display: inline-block; margin-right: 5px;"><a href="#tab-price"
                aria-controls="tab-price" role="tab" data-toggle="tab"
                style="padding: 10px 15px; display: block; border: 1px solid #ddd; border-bottom: none; background: #eee; text-decoration: none;"><?php _e('Price', 'chaletv2'); ?></a>
        </li>
        <li role="presentation" style="display: inline-block; margin-right: 5px;"><a href="#tab-terms"
                aria-controls="tab-terms" role="tab" data-toggle="tab"
                style="padding: 10px 15px; display: block; border: 1px solid #ddd; border-bottom: none; background: #eee; text-decoration: none;"><?php _e('Terms', 'chaletv2'); ?></a>
        </li>
        <li role="presentation" style="display: inline-block; margin-right: 5px;"><a href="#tab-instructions"
                aria-controls="tab-instructions" role="tab" data-toggle="tab"
                style="padding: 10px 15px; display: block; border: 1px solid #ddd; border-bottom: none; background: #eee; text-decoration: none;"><?php _e('Instructions', 'chaletv2'); ?></a>
        </li>
        <li role="presentation" style="display: inline-block; margin-right: 5px;"><a href="#tab-media"
                aria-controls="tab-media" role="tab" data-toggle="tab"
                style="padding: 10px 15px; display: block; border: 1px solid #ddd; border-bottom: none; background: #eee; text-decoration: none;"><?php _e('Media', 'chaletv2'); ?></a>
        </li>
        <li role="presentation" style="display: inline-block; margin-right: 5px;"><a href="#tab-amenities"
                aria-controls="tab-amenities" role="tab" data-toggle="tab"
                style="padding: 10px 15px; display: block; border: 1px solid #ddd; border-bottom: none; background: #eee; text-decoration: none;"><?php _e('Amenities', 'chaletv2'); ?></a>
        </li>
        <li role="presentation" style="display: inline-block; margin-right: 5px;"><a href="#tab-location"
                aria-controls="tab-location" role="tab" data-toggle="tab"
                style="padding: 10px 15px; display: block; border: 1px solid #ddd; border-bottom: none; background: #eee; text-decoration: none;"><?php _e('Location', 'chaletv2'); ?></a>
        </li>
        <!-- <li role="presentation" style="display: inline-block; margin-right: 5px;"><a href="#tab-rules"
                aria-controls="tab-rules" role="tab" data-toggle="tab"
                style="padding: 10px 15px; display: block; border: 1px solid #ddd; border-bottom: none; background: #eee; text-decoration: none;"><?php _e('Rules', 'chaletv2'); ?></a>
        </li> -->
    </ul>

    <!-- Tab Content Wrapper -->
    <div class="tab-content" id="chalet-dashboard-tab-content">

        <!-- Tab: Information -->
        <div role="tabpanel" class="tab-pane active" id="tab-information">
            <h3><?php _e('Information', 'chaletv2'); ?></h3>

            <p>
                <label for="title"><?php _e('Chalet Name:', 'chaletv2'); ?></label><br>
                <input type="text" id="title" name="chalet_title"
                    value="<?php echo get_chalet_field_value('title', $edit_mode ? get_the_title($edit_mode) : ''); ?>"
                    required>
            </p>

            <p>
                <label for="description"><?php _e('Description:', 'chaletv2'); ?></label><br>
                <?php
                wp_editor(
                    isset($chalet_data['description']) ? $chalet_data['description'] : '',
                    'description', // HTML ID
                    array(
                        'textarea_name' => 'description', // POST variable name
                        'media_buttons' => false,
                        'textarea_rows' => 10,
                        'teeny' => false // Use the full editor
                    )
                );
                ?>
            </p>

            <p>
                <label>
                    <input type="checkbox" name="featured" value="yes" <?php checked(get_chalet_field_value('featured'), 'yes'); ?>>
                    <?php _e('Featured Chalet', 'chaletv2'); ?>
                </label>
            </p>

            <p>
                <label for="monthly_rate"><?php _e('Monthly Rate From (€):', 'chaletv2'); ?></label><br>
                <input type="number" id="monthly_rate" name="monthly_rate"
                    value="<?php echo get_chalet_field_value('monthly_rate'); ?>" min="0" step="any">
            </p>

            <p>
                <label for="cleaning_fee"><?php _e('Cleaning Fee (€):', 'chaletv2'); ?></label><br>
                <input type="number" id="cleaning_fee" name="cleaning_fee"
                    value="<?php echo get_chalet_field_value('cleaning_fee'); ?>" min="0" step="any">
            </p>

            <p>
                <label><?php _e('Cleaning Fee Type:', 'chaletv2'); ?></label><br>
                <label style="margin-right: 10px;">
                    <input type="radio" id="cleaning_fee_type_fixed" name="cleaning_fee_type" value="fixed" <?php checked(get_chalet_field_value('cleaning_fee_type', 'fixed'), 'fixed'); ?>>
                    <?php _e('Fixed Amount (€)', 'chaletv2'); ?>
                </label>
                <label style="margin-left: 15px;">
                    <input type="radio" id="cleaning_fee_type_per_stay" name="cleaning_fee_type" value="per_stay" <?php checked(get_chalet_field_value('cleaning_fee_type'), 'per_stay'); ?>>
                    <?php _e('Per Stay', 'chaletv2'); ?>
                </label>
                <?php // TODO: Add logic if needed for 'per_stay' ?>
            </p>

            <p>
                <label for="guest_count"><?php _e('Max Guests:', 'chaletv2'); ?></label><br>
                <input type="number" id="guest_count" name="guest_count"
                    value="<?php echo get_chalet_field_value('guest_count'); ?>" min="1" required>
            </p>

            <p>
                <label for="baths"><?php _e('Number of Baths:', 'chaletv2'); ?></label><br>
                <input type="number" id="baths" name="baths" value="<?php echo get_chalet_field_value('baths', 1); ?>"
                    min="1">
            </p>

            <h4><?php _e('Bedrooms', 'chaletv2'); ?></h4>
            <p>Coming Soon</p>
            <!-- <div id="bedrooms-container">
                <?php
                $bedrooms = isset($chalet_data['bedrooms']) ? $chalet_data['bedrooms'] : [[]]; // Start with one empty if new
                if (empty($bedrooms)) {
                    $bedrooms = [[]];
                } // Ensure at least one row for new posts
                foreach ($bedrooms as $index => $bedroom) {
                    // Ensure $bedroom is an array
                    $bedroom = is_array($bedroom) ? $bedroom : [];
                    $bedroom_type = isset($bedroom['bedroom_type']) ? $bedroom['bedroom_type'] : '';
                    $num_beds = isset($bedroom['num_beds']) ? $bedroom['num_beds'] : 1;
                    ?>
                    <div class="bedroom-entry" style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
                        <p>
                            <label><?php _e('Bedroom Type:', 'chaletv2'); ?></label>
                            <input type="text" name="bedrooms[<?php echo $index; ?>][bedroom_type]"
                                value="<?php echo esc_attr($bedroom_type); ?>" placeholder="e.g., Double bed, Single bed">
                        </p>
                        <p>
                            <label><?php _e('Number of Beds:', 'chaletv2'); ?></label>
                            <input type="number" name="bedrooms[<?php echo $index; ?>][num_beds]"
                                value="<?php echo esc_attr($num_beds); ?>" min="1">
                        </p>
                        <button type="button" class="remove-bedroom-button button"
                            style="color: red;"><?php _e('Remove Bedroom', 'chaletv2'); ?></button>
                    </div>
                <?php } // End foreach loop ?>
            </div>
            <button type="button" id="add-bedroom-button"
                class="button"><?php _e('Add Bedroom', 'chaletv2'); ?></button> -->
            <hr>
            <br>
        </div>

        <!-- Tab: Tariffs (Price) -->
        <div role="tabpanel" class="tab-pane" id="tab-price">
            <h3><?php _e('Price', 'chaletv2'); ?></h3>

            <h4><?php _e('Default Rates', 'chaletv2'); ?></h4>
            <p>
                <label
                    for="default_rate_weekend"><?php _e('Weekend Price per Night (Fri, Sat) (€):', 'chaletv2'); ?></label><br>
                <input type="number" id="default_rate_weekend" name="default_rate_weekend"
                    value="<?php echo get_chalet_field_value('default_rate_weekend'); ?>" min="0" step="any">
            </p>
            <p>
                <label
                    for="default_rate_week"><?php _e('Week Price per Night (Sun-Thu) (€):', 'chaletv2'); ?></label><br>
                <input type="number" id="default_rate_week" name="default_rate_week"
                    value="<?php echo get_chalet_field_value('default_rate_week'); ?>" min="0" step="any">
            </p>
            <p>
                <label
                    for="default_rate_week"><?php _e('Week Price per Night (Sun-Thu) (€):', 'chaletv2'); ?></label><br>
                <input type="number" id="default_rate_week" name="default_rate_week"
                    value="<?php echo get_chalet_field_value('default_rate_week'); ?>" min="0" step="any">
            </p>
            <h4><?php _e('Extra Prices', 'chaletv2'); ?></h4>
            <p>
                <label for="guests_included"><?php _e('Guests Included:', 'chaletv2'); ?></label><br>
                <input type="number" id="guests_included" name="guests_included"
                    value="<?php echo get_chalet_field_value('guests_included'); ?>" min="0" step="any">
            </p>
            <p>
                <label for="extra_price_adult"><?php _e('Extra Price per Adult/Night (€):', 'chaletv2'); ?></label><br>
                <input type="number" id="extra_price_adult" name="extra_price_adult"
                    value="<?php echo get_chalet_field_value('extra_price_adult'); ?>" min="0" step="any">
            </p>
            <p>
                <label
                    for="extra_price_child"><?php _e('Extra Price per Child/Night (3-17) (€):', 'chaletv2'); ?></label><br>
                <input type="number" id="extra_price_child" name="extra_price_child"
                    value="<?php echo get_chalet_field_value('extra_price_child'); ?>" min="0" step="any">
            </p>
            <p>
                <label>
                    <input type="checkbox" name="free_for_babies" value="yes" <?php checked(is_chalet_field_checked('free_for_babies', 'yes')); ?>>
                    <?php _e('Free for Babies (0-2)', 'chaletv2'); ?>
                </label>
                <small><?php _e('If unchecked, child rates apply.', 'chaletv2'); ?></small>
            </p>
            <hr>

            <h4><?php _e('Booking Settings', 'chaletv2'); ?></h4>
            <p>
                <label for="min_nights"><?php _e('Minimum Nights Default:', 'chaletv2'); ?></label><br>
                <input type="number" id="min_nights" name="min_nights"
                    value="<?php echo get_chalet_field_value('min_nights', 1); ?>" min="1">
            </p>
            <h4><?php _e('Taxes & CITQ', 'chaletv2'); ?></h4>
            <p>
                <label for="tax_gst"><?php _e('GST Tax Number:', 'chaletv2'); ?></label><br>
                <input type="text" id="tax_gst" name="tax_gst" value="<?php echo get_chalet_field_value('tax_gst'); ?>">
            </p>
            <p>
                <label for="tax_thq"><?php _e('THQ Tax Number:', 'chaletv2'); ?></label><br>
                <input type="text" id="tax_thq" name="tax_thq" value="<?php echo get_chalet_field_value('tax_thq'); ?>">
            </p>
            <p>
                <label for="tax_qst"><?php _e('QST Tax Number:', 'chaletv2'); ?></label><br>
                <input type="text" id="tax_qst" name="tax_qst" value="<?php echo get_chalet_field_value('tax_qst'); ?>">
            </p>
            <p>
                <label for="citq_document"><?php _e('CITQ Document:', 'chaletv2'); ?></label><br>
                <input type="file" id="citq_document" name="citq_document" disabled>
                <small><?php _e('Coming Soon', 'chaletv2'); ?></small>
            </p>
            <br>
            <hr>
            <br>
            <h4><?php _e('Security Deposit', 'chaletv2'); ?></h4>
            <p>
                <label for="security_deposit"><?php _e('Security Deposit (€):', 'chaletv2'); ?></label><br>
                <input type="number" id="security_deposit" name="security_deposit"
                    value="<?php echo get_chalet_field_value('security_deposit'); ?>" min="0" step="any">
            </p>
            <hr>
            <h4><?php _e('Extra Options', 'chaletv2'); ?></h4>
            <p>
                <label><?php _e('Extra Options:', 'chaletv2'); ?></label><br>
                Coming Soon
            </p>
            <br>
            <hr>
            <br>
            <h4><?php _e('Check-in / Check-out Times & Extras', 'chaletv2'); ?></h4>
            <p>
                <label><?php _e('Default Check-in Time:', 'chaletv2'); ?></label><br>
                <input type="time" id="checkin_time" name="checkin_time"
                    value="<?php echo get_chalet_field_value('checkin_time', '14:00'); ?>">
            </p>
            <p>
                <label><?php _e('Default Check-out Time:', 'chaletv2'); ?></label><br>
                <input type="time" id="checkout_time" name="checkout_time"
                    value="<?php echo get_chalet_field_value('checkout_time', '11:00'); ?>">
            </p>
            <hr>
            <hr>

            <h4><?php _e('Fees and Taxes', 'chaletv2'); ?></h4>
            <p>
                <label for="security_deposit"><?php _e('Security Deposit (€):', 'chaletv2'); ?></label><br>
                <input type="number" id="security_deposit" name="security_deposit"
                    value="<?php echo get_chalet_field_value('security_deposit'); ?>" min="0" step="any">
            </p>
            <p>
                <label>
                    <input type="checkbox" id="include_taxes_in_price" name="include_taxes_in_price" value="yes" <?php checked(is_chalet_field_checked('include_taxes_in_price', 'yes')); ?>>
                    <?php _e('Include Taxes in Price?', 'chaletv2'); ?>
                </label>
            </p>
            <p id="tax-rate-field"
                style="<?php echo is_chalet_field_checked('include_taxes_in_price', 'yes') ? 'display: none;' : ''; ?>">
                <label for="tax_rate_percentage"><?php _e('Tax Rate (%):', 'chaletv2'); ?></label><br>
                <input type="number" id="tax_rate_percentage" name="tax_rate_percentage"
                    value="<?php echo get_chalet_field_value('tax_rate_percentage'); ?>" min="0" max="100" step="any">
            </p>
            <?php /* Basic JS to hide/show tax rate based on checkbox */ ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const taxCheckbox = document.getElementById('include_taxes_in_price');
                    const taxRateField = document.getElementById('tax-rate-field');
                    if (taxCheckbox && taxRateField) {
                        taxCheckbox.addEventListener('change', function () {
                            taxRateField.style.display = this.checked ? 'none' : '';
                        });
                    }
                });
            </script>
            <hr>

            <h4><?php _e('Check-in / Check-out Times', 'chaletv2'); ?></h4>
            <p>
                <label for="checkin_time"><?php _e('Default Check-in Time:', 'chaletv2'); ?></label><br>
                <input type="time" id="checkin_time" name="checkin_time"
                    value="<?php echo get_chalet_field_value('checkin_time', '14:00'); ?>">
            </p>
            <p>
                <label for="checkout_time"><?php _e('Default Check-out Time:', 'chaletv2'); ?></label><br>
                <input type="time" id="checkout_time" name="checkout_time"
                    value="<?php echo get_chalet_field_value('checkout_time', '14:00'); ?>">
            </p>
            <hr>

            <h4><?php _e('Extra Options', 'chaletv2'); ?></h4>
            <p><em><?php _e('Complex field - Coming Soon.', 'chaletv2'); ?></em></p>
            <div id="extra-options-container">
                <!-- Placeholder for complex field: extra_options -->
                <?php /* Loop through existing options here */ ?>
            </div>
            <!-- <button type="button" id="add-extra-option-button"
                class="button"><?php _e('Add Extra Option', 'chaletv2'); ?></button> -->
            <hr>

            <h4><?php _e('Early Check-in / Late Check-out Options', 'chaletv2'); ?></h4>
            <p><em><?php _e('Complex fields - Coming Soon.', 'chaletv2'); ?></em></p>
            <div id="early-checkin-container">
                <!-- Placeholder for complex field: early_checkin_options -->
                <?php /* Loop through existing options here */ ?>
            </div>
            <!-- <button type="button" id="add-early-checkin-button"
                class="button"><?php _e('Add Early Check-in Option', 'chaletv2'); ?></button> -->
            <br><br>
            <div id="late-checkout-container">
                <!-- Placeholder for complex field: late_checkout_options -->
                <?php /* Loop through existing options here */ ?>
            </div>
            <!-- <button type="button" id="add-late-checkout-button"
                class="button"><?php _e('Add Late Check-out Option', 'chaletv2'); ?></button> -->
            <hr>

            <h4><?php _e('Seasonal Rates', 'chaletv2'); ?></h4>
            <p><em><?php _e('Complex field - Coming Soon.', 'chaletv2'); ?></em></p>
            <div id="seasonal-rates-container">
                <!-- Placeholder for complex field: seasonal_rates -->
                <?php /* Loop through existing rates here */ ?>
            </div>
            <!-- <button type="button" id="add-seasonal-rate-button"
                class="button"><?php _e('Add Seasonal Rate Period', 'chaletv2'); ?></button> -->

        </div>
        <!-- Tab: Terms -->
        <div role="tabpanel" class="tab-pane" id="tab-terms">
            <h3><?php _e('Terms', 'chaletv2'); ?></h3>
            <div class="form-group">
                <label for="reservation_policy"><?php _e('Reservation Policy', 'chaletv2'); ?></label>
                <div class="radio-group">
                    <label><input type="radio" name="reservation_policy" value="policy_50_50_3" <?php checked(get_chalet_field_value('reservation_policy'), 'policy_50_50_3'); ?>>
                        <?php _e('Policy 50-50 (3 days before stay)', 'chaletv2'); ?></label>
                    <label><input type="radio" name="reservation_policy" value="policy_50_50_14" <?php checked(get_chalet_field_value('reservation_policy'), 'policy_50_50_14'); ?>>
                        <?php _e('Policy 50-50 (14 days before stay)', 'chaletv2'); ?></label>
                    <label><input type="radio" name="reservation_policy" value="policy_25_25_50_14" <?php checked(get_chalet_field_value('reservation_policy'), 'policy_25_25_50_14'); ?>>
                        <?php _e('Policy 25-25-50 (14 days before stay)', 'chaletv2'); ?></label>
                </div>
            </div>

            <div class="form-group">
                <label for="cancellation_policy"><?php _e('Cancellation Policy', 'chaletv2'); ?></label>
                <div class="radio-group">
                    <label><input type="radio" name="cancellation_policy" value="flexible" <?php checked($chalet_data['cancellation_policy'] ?? '', 'flexible'); ?>>
                        <?php _e('Flexible', 'chaletv2'); ?></label>
                    <label><input type="radio" name="cancellation_policy" value="moderate" <?php checked($chalet_data['cancellation_policy'] ?? '', 'moderate'); ?>>
                        <?php _e('Moderate', 'chaletv2'); ?></label>
                    <label><input type="radio" name="cancellation_policy" value="strict" <?php checked($chalet_data['cancellation_policy'] ?? '', 'strict'); ?>>
                        <?php _e('Strict', 'chaletv2'); ?></label>
                </div>
            </div>

            <div class="form-group">
                <label
                    for="preparation_time"><?php _e('Preparation Time (Nights Blocked Before Stay)', 'chaletv2'); ?></label>
                <input type="number" name="preparation_time" id="preparation_time" min="0"
                    value="<?php echo esc_attr($chalet_data['preparation_time'] ?? ''); ?>">
                <p class="description">
                    <?php _e('Number of nights blocked before the start of the stay for preparation.', 'chaletv2'); ?>
                </p>
            </div>

            <div class="form-group">
                <label for="reservation_window"><?php _e('Reservation Window (Days in Advance)', 'chaletv2'); ?></label>
                <input type="number" name="reservation_window" id="reservation_window" min="0"
                    value="<?php echo esc_attr($chalet_data['reservation_window'] ?? ''); ?>">
                <p class="description"><?php _e('Maximum number of days in advance a user can book.', 'chaletv2'); ?>
                </p>
            </div>
            <div class="form-group">
                <label
                    for="reservation_notice"><?php _e('Reservation Notice (Minimum Days Before Arrival)', 'chaletv2'); ?></label>
                <input type="number" name="reservation_notice" id="reservation_notice" min="0"
                    value="<?php echo esc_attr($chalet_data['reservation_notice'] ?? ''); ?>">
                <p class="description">
                    <?php _e('Minimum number of days notice required to accept a reservation.', 'chaletv2'); ?>
                </p>
            </div>
            <div class="form-group">
                <label for="reservation_contract"><?php _e('Reservation Contract / Policies', 'chaletv2'); ?></label>
                <textarea name="reservation_contract" id="reservation_contract"
                    rows="10"><?php echo esc_textarea($chalet_data['reservation_contract'] ?? ''); ?></textarea>
                <p class="description">
                    <?php _e('Enter the full reservation contract terms and conditions.', 'chaletv2'); ?>
                </p>
            </div>

        </div>

        <!-- Tab: Instructions -->
        <div role="tabpanel" class="tab-pane" id="tab-instructions">
            <h3><?php _e('Instructions', 'chaletv2'); ?></h3>

            <div class="form-group">
                <label for="checkin_instructions"><?php _e('Check-in Instructions', 'chaletv2'); ?></label>
                <textarea name="checkin_instructions" id="checkin_instructions"
                    rows="5"><?php echo esc_textarea($chalet_data['checkin_instructions'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label
                    for="checkin_instructions_days"><?php _e('Send Check-in Instructions (Days Before)', 'chaletv2'); ?></label>
                <input type="number" name="checkin_instructions_days" id="checkin_instructions_days" min="0"
                    value="<?php echo esc_attr($chalet_data['checkin_instructions_days'] ?? ''); ?>">
                <p class="description"><?php _e('How many days before check-in to send instructions.', 'chaletv2'); ?>
                </p>
            </div>

            <div class="form-group">
                <label for="checkout_instructions"><?php _e('Check-out Instructions', 'chaletv2'); ?></label>
                <textarea name="checkout_instructions" id="checkout_instructions"
                    rows="5"><?php echo esc_textarea($chalet_data['checkout_instructions'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label
                    for="checkout_instructions_days"><?php _e('Send Check-out Instructions (Days Before)', 'chaletv2'); ?></label>
                <input type="number" name="checkout_instructions_days" id="checkout_instructions_days" min="0"
                    value="<?php echo esc_attr($chalet_data['checkout_instructions_days'] ?? ''); ?>">
                <p class="description"><?php _e('How many days before check-out to send instructions.', 'chaletv2'); ?>
                </p>
            </div>

            <div class="form-group">
                <label for="itinerary_instructions"><?php _e('Itinerary Instructions', 'chaletv2'); ?></label>
                <textarea name="itinerary_instructions" id="itinerary_instructions"
                    rows="5"><?php echo esc_textarea($chalet_data['itinerary_instructions'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label
                    for="itinerary_instructions_days"><?php _e('Send Itinerary Instructions (Days Before)', 'chaletv2'); ?></label>
                <input type="number" name="itinerary_instructions_days" id="itinerary_instructions_days" min="0"
                    value="<?php echo esc_attr($chalet_data['itinerary_instructions_days'] ?? ''); ?>">
                <p class="description"><?php _e('How many days before check-in to send itinerary.', 'chaletv2'); ?></p>
            </div>

            <div class="form-group">
                <label for="rules_reminder"><?php _e('Reminder of Rules', 'chaletv2'); ?></label>
                <textarea name="rules_reminder" id="rules_reminder"
                    rows="5"><?php echo esc_textarea($chalet_data['rules_reminder'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="rules_reminder_days"><?php _e('Send Rules Reminder (Days Before)', 'chaletv2'); ?></label>
                <input type="number" name="rules_reminder_days" id="rules_reminder_days" min="0"
                    value="<?php echo esc_attr($chalet_data['rules_reminder_days'] ?? ''); ?>">
                <p class="description"><?php _e('How many days before check-in to send rules reminder.', 'chaletv2'); ?>
                </p>
            </div>

            <div class="form-group">
                <label for="local_guide"><?php _e('Local Guide', 'chaletv2'); ?></label>
                <textarea name="local_guide" id="local_guide"
                    rows="5"><?php echo esc_textarea($chalet_data['local_guide'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="local_guide_days"><?php _e('Send Local Guide (Days Before)', 'chaletv2'); ?></label>
                <input type="number" name="local_guide_days" id="local_guide_days" min="0"
                    value="<?php echo esc_attr($chalet_data['local_guide_days'] ?? ''); ?>">
                <p class="description"><?php _e('How many days before check-in to send local guide.', 'chaletv2'); ?>
                </p>
            </div>

            <div class="form-group">
                <label for="emergency_contact"><?php _e('Emergency Contact', 'chaletv2'); ?></label>
                <textarea name="emergency_contact" id="emergency_contact"
                    rows="5"><?php echo esc_textarea($chalet_data['emergency_contact'] ?? ''); ?></textarea>
            </div>

        </div>

        <!-- Tab: Media -->
        <div role="tabpanel" class="tab-pane" id="tab-media">
            <h3><?php _e('Media', 'chaletv2'); ?></h3>
            <p><?php _e('Featured Image (Coming Soon)', 'chaletv2'); ?></p>
            <h4><?php _e('Gallery', 'chaletv2'); ?></h4>
            <p><em><?php _e('Coming Soon.', 'chaletv2'); ?></em></p>
            <!-- Placeholder for Carbon Fields Media Gallery: chalet_gallery -->
            <!-- <input type="hidden" name="chalet_gallery"
                value="<?php /* echo get_chalet_field_value('chalet_gallery'); // Placeholder */ ?>">
            <div id="chalet-gallery-preview"></div>
            <button type="button" id="upload-gallery-button"
                class="button"><?php _e('Manage Gallery', 'chaletv2'); ?></button> -->
        </div>

        <!-- Tab: Amenities -->
        <!-- <div role="tabpanel" class="tab-pane" id="tab-amenities">
            <h3><?php _e('Amenities', 'chaletv2'); ?></h3>
            <p><em><?php _e('Coming Soon.', 'chaletv2'); ?></em></p>
            <?php
            $feature_categories = [
                'amenities_indoor' => __('Indoor Amenities', 'chaletv2'),
                'amenities_outdoor' => __('Outdoor Amenities', 'chaletv2'),
                'amenities_kitchen' => __('Kitchen Amenities', 'chaletv2'),
                'amenities_bathroom' => __('Bathroom Amenities', 'chaletv2'),
                'amenities_other' => __('Other Amenities', 'chaletv2'),
            ];
            $features_by_category = [];
            foreach (array_keys($feature_categories) as $category_slug) {
                $features_by_category[$category_slug] = get_features_by_category($category_slug);
            }

            $selected_features = isset($chalet_data['associated_features']) && is_array($chalet_data['associated_features']) ? $chalet_data['associated_features'] : [];
            if (empty($selected_features) && function_exists('carbon_get_post_meta') && $chalet_id) {
                $cf_features = carbon_get_post_meta($chalet_id, 'associated_features');
                if (is_array($cf_features)) {
                    $selected_features = wp_list_pluck($cf_features, 'id');
                }
            }

            foreach ($features_by_category as $category_slug => $features) {
                if (!empty($features)) {
                    ?>
                    <h4><?php echo esc_html($feature_categories[$category_slug]); ?></h4>
                    <div style="display: flex; flex-wrap: wrap;">
                        <?php
                        if (is_array($features) || is_object($features)) { // Ensure $features is iterable
                            foreach ($features as $feature) {
                                // Double check $feature is a valid WP_Post object
                                if (is_object($feature) && isset($feature->ID) && isset($feature->post_title)) {
                                    ?>
                                    <div style="flex: 1 1 30%; margin-right: 2%; margin-bottom: 10px;">
                                        <label>
                                            <input type="checkbox" name="associated_features[]"
                                                value="<?php echo esc_attr($feature->ID); ?>" <?php checked(in_array($feature->ID, $selected_features)); ?>>
                                            <?php echo esc_html($feature->post_title); ?>
                                        </label>
                                    </div>
                                    <?php
                                } // End check for valid feature object
                            } // End foreach feature
                        } // End if $features is iterable
                        ?>
                    </div>
                    <hr>
                    <?php
                } // End if !empty($features)
            } // End foreach category
            ?>
        </div> -->
        <div class="tab-content tab-pane" role="tabpanel" id="tab-amenities">
            <div class="container">
                <div class="amenties-top-details">
                    <h3>Amenities and Features</h3>
                    <p>Select the amenities and features by
                        clicking on those available at your chalet</p>
                </div>
                <div class="amenities-wrapper">
                    <div class="amenities-left">
                        <h2>Amenities</h2>
                    </div>
                    <div class="amenities-right">
                        <div class="amenities-row">
                            <div class="amenities-details">
                                <h4>Indoor Features</h4>
                                <div class="list-wrapper">
                                    <ul>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="amenities-details">
                                <h4>Indoor Features</h4>
                                <div class="list-wrapper">
                                    <ul>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="amenities-details">
                                <h4>Indoor Features</h4>
                                <div class="list-wrapper">
                                    <ul>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/access.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                        <li>
                                            <div class="list-img">
                                                <img src="./images/list img 2.png" alt="">

                                            </div>
                                            <span>Air Conditioning</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <button class="details-btn main-btn">Save</button>
                    <button class="next-tab-link" data-id="location-tab" onclick="showContent(event, this)">Go to
                        Location settings.</button>
                </div>
            </div>
            <div class="location-section tab-content" id="location-tab" style="display: none;">
                <div class="container">
                    <h2 class="title-xl"> Location</h2>
                    <span class="light-text"> location details</span>
                    <div class="location-details">
                        <div class="information-row">
                            <div class="text-details"><span class="light-text"> Name</span></div>
                            <div class="input-details">
                                <div class="form-detail">
                                    <label class="light-text"> Name</label>
                                    <input type="text" placeholder=" Name" class="big-input">
                                </div>
                            </div>
                        </div>
                        <div class="information-row">
                            <div class="text-details"><span class="light-text">Guest Capacity</span></div>
                            <div class="input-details">
                                <div class="form-detail">
                                    <label class="light-text">Guest No (mandatory)</label>
                                    <select class="details-select">
                                        <option>26</option>
                                        <option>27</option>
                                        <option>28</option>
                                        <option>29</option>
                                    </select>
                                </div>
                                <div class="form-detail">
                                    <label class="light-text">Guest No (mandatory)</label>
                                    <select class="details-select">
                                        <option>26</option>
                                        <option>27</option>
                                        <option>28</option>
                                        <option>29</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="map-details">
                        <button class="details-btn">Place Pin with Address</button>
                    </div>
                </div>
                <div class="container">
                    <button class="details-btn main-btn">Save</button>
                    <button class="next-tab-link" data-id="calendar-tab" onclick="showContent(event, this)">Go to
                        Calendar settings.</button>
                </div>
            </div>
            <div class="tab-content" id="calendar-tab" style="display: none;">
                <div class="container">
                    <h1> calendar comming soon</h1>
                </div>
                <div class="container">
                    <button class="details-btn main-btn">Save</button>
                    <button class="next-tab-link" data-id="information-tab" onclick="showContent(event, this)">back
                        to
                        start</button>
                </div>
            </div>
        </div>
        <!-- Tab: Location -->
        <div role="tabpanel" class="tab-pane" id="tab-location">
            <h3><?php _e('Location', 'chaletv2'); ?></h3>
            <p><em><?php _e('Map integration requires JavaScript. (Coming Soon)', 'chaletv2'); ?></em></p>
            <!-- Placeholder for Carbon Fields Map: chalet_location -->
            <input type="hidden" name="chalet_location"
                value="<?php /* echo get_chalet_field_value('chalet_location'); // Placeholder */ ?>">
            <div id="chalet-map-canvas" style="height: 300px; width: 100%; border: 1px solid #ccc;"></div>
            <div class="form-group">
                <label for="country"><?php _e('Country', 'chaletv2'); ?></label>
                <input type="text" name="country" id="country"
                    value="<?php echo esc_attr(get_chalet_field_value('country')); ?>">
            </div>
            <div class="form-group">
                <label for="province"><?php _e('Province/State', 'chaletv2'); ?></label>
                <input type="text" name="province" id="province"
                    value="<?php echo esc_attr(get_chalet_field_value('province')); ?>">
            </div>
            <div class="form-group">
                <label for="region"><?php _e('Region', 'chaletv2'); ?></label>
                <input type="text" name="region" id="region"
                    value="<?php echo esc_attr(get_chalet_field_value('region')); ?>">
            </div>
            <div class="form-group">
                <label for="full_address"><?php _e('Full Address', 'chaletv2'); ?></label>
                <input type="text" name="full_address" id="full_address"
                    value="<?php echo esc_attr(get_chalet_field_value('full_address')); ?>">
            </div>
        </div>

        <!-- Tab: Rules -->
        <!-- <div role="tabpanel" class="tab-pane" id="tab-rules">
            <h3><?php _e('Rules', 'chaletv2'); ?></h3>

            <?php /* These fields are technically under 'Price' in CF, but separated here for UI */ ?>
            <p>
                <label for="smoking_allowed"><?php _e('Smoking Allowed:', 'chaletv2'); ?></label><br>
                <select id="smoking_allowed" name="smoking_allowed">
                    <option value="no" <?php selected(get_chalet_field_value('smoking_allowed', 'no'), 'no'); ?>>
                        <?php _e('No', 'chaletv2'); ?>
                    </option>
                    <option value="yes" <?php selected(get_chalet_field_value('smoking_allowed'), 'yes'); ?>>
                        <?php _e('Yes', 'chaletv2'); ?>
                    </option>
                    <option value="outside" <?php selected(get_chalet_field_value('smoking_allowed'), 'outside'); ?>>
                        <?php _e('Outside Only', 'chaletv2'); ?>
                    </option>
                </select>
            </p>

            <p>
                <label for="pets_allowed"><?php _e('Pets Allowed:', 'chaletv2'); ?></label><br>
                <select id="pets_allowed" name="pets_allowed">
                    <option value="no" <?php selected(get_chalet_field_value('pets_allowed', 'no'), 'no'); ?>>
                        <?php _e('No', 'chaletv2'); ?>
                    </option>
                    <option value="yes" <?php selected(get_chalet_field_value('pets_allowed'), 'yes'); ?>>
                        <?php _e('Yes', 'chaletv2'); ?>
                    </option>
                    <option value="conditional" <?php selected(get_chalet_field_value('pets_allowed'), 'conditional'); ?>><?php _e('Conditional (Specify in rules)', 'chaletv2'); ?></option>
                </select>
            </p>

            <p>
                <label for="events_allowed"><?php _e('Events/Parties Allowed:', 'chaletv2'); ?></label><br>
                <select id="events_allowed" name="events_allowed">
                    <option value="no" <?php selected(get_chalet_field_value('events_allowed', 'no'), 'no'); ?>>
                        <?php _e('No', 'chaletv2'); ?>
                    </option>
                    <option value="yes" <?php selected(get_chalet_field_value('events_allowed'), 'yes'); ?>>
                        <?php _e('Yes', 'chaletv2'); ?>
                    </option>
                    <option value="conditional" <?php selected(get_chalet_field_value('events_allowed'), 'conditional'); ?>><?php _e('Conditional (Specify in rules)', 'chaletv2'); ?></option>
                </select>
            </p>

            <p>
                <label for="additional_rules"><?php _e('Additional Rules:', 'chaletv2'); ?></label><br>
                <?php
                // Ensure $chalet_data['additional_rules'] exists before passing to wp_editor
                $additional_rules_content = isset($chalet_data['additional_rules']) ? $chalet_data['additional_rules'] : '';
                wp_editor(
                    $additional_rules_content,
                    'additional_rules',
                    array(
                        'textarea_name' => 'additional_rules',
                        'media_buttons' => false,
                        'textarea_rows' => 5,
                        'teeny' => true
                    )
                );
                ?>
            </p>

        </div> -->

    </div> <!-- End Tab Content (#chalet-dashboard-tab-content) -->

    <!-- Submit Button (outside tab content) -->
    <p style="margin-top: 20px;">
        <input type="submit" name="submit_chalet"
            value="<?php echo $edit_mode ? __('Update Chalet', 'chaletv2') : __('Add Chalet', 'chaletv2'); ?>"
            class="button button-primary">
    </p>
</form>