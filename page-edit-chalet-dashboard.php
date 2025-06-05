<?php
/**
 * Template Name: Edit Chalet Dashboard
 * Description: A custom page template for editing chalet dashboard.
 *
 * @package WordPress
 * @subpackage Your_Theme_Name
 * @since Your_Theme_Version
 */
$chalet_id = $edit_mode = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
if(!$edit_mode){
    // Check for subscription
    if(!has_subscription()){
        wp_redirect(home_url('/dashboard-subscribe'));
        exit;
    }
}
get_header('dashboard');
// Ensure $chalet_data and $edit_mode are available
// These variables should be passed from page-chalet-dashboard.php
$chalet_data = get_chalet_data($chalet_id);
// If $chalet_id is set, we are in edit mode
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
<style>
    .seasonal_rates_container {
        padding: 2rem;
        background: #f4f4f4;
        border-radius: 10px;
        margin-bottom: 2rem!important;
    }
</style>
<link rel="stylesheet"
    href="<?= get_template_directory_uri() ?>/assets/css/edit-style.css?v=<?= filemtime(get_template_directory() . '/assets/css/edit-style.css') ?>" />
<!-- <link rel="stylesheet" href="<?= get_template_directory_uri() ?>/assets/css/edit-style.css"> -->
<form id="chalet-dashboard-form" method="post" action="<?= admin_url('admin-post.php') ?>"
    enctype="multipart/form-data">
    <?php wp_nonce_field('chalet_dashboard_nonce', 'chalet_dashboard_nonce_field'); ?>
    <input type="hidden" name="action" value="chalet_dashboard_save">
    <input type="hidden" name="form_action" value="<?php echo $edit_mode ? 'edit' : 'create'; ?>">
    <?php if ($edit_mode): ?>
        <input type="hidden" name="chalet_id" value="<?php echo esc_attr($edit_mode); ?>">
    <?php endif; ?>

    <!-- <h2><?php echo $edit_mode ? __('Edit Chalet', 'chaletv2') : __('Add New Chalet', 'chaletv2'); ?></h2> -->

    <ul class="nav dashboard-form-tabs " id="chalet-dashboard-tabs">
        <li class="tab-link-list active" id="information-tab-link">
            Information
        </li>
        <li class="tab-link-list" id="price-tab-link">
            Price

        </li>
        <li class="tab-link-list" id="terms-tab-link">
            Terms

        </li>
        <li class="tab-link-list " id="instructions-tab-link">
            Instructions

        </li>
        <li class="tab-link-list " id="media-tab-link">
            Media

        </li>
        <li class="tab-link-list " id="amenities-tab-link">
            Amenities

        </li>
        <li class="tab-link-list " id="location-tab-link">
            Location

        </li>
        <li class="tab-link-list " id="calendar-tab-link">
            Calendar
        </li>
    </ul>
    <div class="information-section tab-content" id="information-tab" style="display: block;">
        <div class="container">
            <h2 class="title-xl"><?php _e('Information', 'chaletv2'); ?></h2>
            <div class="information-row">
                <div class="text-details"><span class="light-text"> <?php _e('Chalet Name:', 'chaletv2'); ?></span>
                </div>

                <div class="input-details">
                    <div class="form-detail">
                        <label class="light-text"> <?php _e('Name:', 'chaletv2'); ?></label>
                        <input type="text" id="title" class="big-input" name="chalet_title"
                            value="<?php echo @$chalet_data['title']; ?>" required>
                    </div>
                </div>
            </div>
            <div class="sm-divider"></div>
            <div class="information-row">
                <div class="text-details"><span class="light-text"></span></div>
                <div class="input-details">
                    <div class="form-detail">
                        <label class="light-text"><?php _e('Chalet Type:', 'chaletv2'); ?></label>
                        <select id="chalet_type" name="chalet_type" class="details-select" required>
                            <?php
                            $chalet_type_options = [
                                'houses' => __('Houses', 'chaletv2'),
                                'apartments' => __('Apartments', 'chaletv2'),
                                'office' => __('Office', 'chaletv2'),
                                'villa' => __('Villa', 'chaletv2'),
                                'townhome' => __('Townhome', 'chaletv2'),
                                'bungalow' => __('Bungalow', 'chaletv2'),
                                'loft' => __('Loft', 'chaletv2'),
                            ];
                            $selected_type = get_chalet_field_value('chalet_type', 'houses');
                            foreach ($chalet_type_options as $value => $label) {
                                printf(
                                    '<option value="%s"%s>%s</option>',
                                    esc_attr($value),
                                    selected($selected_type, $value, false),
                                    esc_html($label)
                                );
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="information-row">
                <div class="text-details"><span class="light-text">Guest Capacity</span></div>
                <div class="input-details">
                    <div class="form-detail">
                        <label for="guest_count" class="light-text"><?php _e('Max Guests:', 'chaletv2'); ?></label>

                        <select id="guest_count" class="details-select" name="guest_count" required>
                            <?php
                            $selected_guest_count = get_chalet_field_value('guest_count');
                            for ($i = 1; $i <= 10; $i++) {
                                echo '<option value="' . $i . '"' . selected($selected_guest_count, $i, false) . '>' . $i . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="sm-divider"></div>
            <div class="information-row">
                <div class="text-details">
                    <span class="light-text">Description</span>
                </div>
                <div class="input-details">
                    <div class="form-detail">
                        <label for="description" class="light-text"> <?php _e('Description:', 'chaletv2'); ?></label>
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
                    </div>
                </div>
            </div>
            <div class="border"></div>
            <h2>Listing details</h2>
            <div class="information-row ">
                <div class="text-details"><span
                        class="light-text bold-text"><?php _e('Bathrooms:', 'chaletv2'); ?></span>
                </div>
                <div class="input-details">
                    <div class="double-col">
                        <div class="form-detail">
                            <label class="light-text bold-text"
                                for="baths"><?php _e('Number of Baths:', 'chaletv2'); ?></label>
                            <input type="number" id="baths" name="baths"
                                value="<?php echo get_chalet_field_value('baths', 1); ?>" min="1">
                            <!-- <select class="details-select">
                                <option>26</option>
                                <option>27</option>
                                <option>28</option>
                                <option>29</option>
                            </select> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="information-row ">
                <div class="text-details"><span class="light-text bold-text"><?php _e('Bedrooms', 'chaletv2'); ?></span>
                </div>


                <div id="bedrooms-container" class="">
                    <p class="light-text">Here you can add your room details including number of guests, beds and
                        bed</p>
                    <?php
                    $bedrooms = isset($chalet_data['bedrooms']) ? $chalet_data['bedrooms'] : [[]]; // Start with one empty if new
                    if (empty($bedrooms)) {
                        $bedrooms = [[]];
                    } // Ensure at least one row for new posts
                    foreach ($bedrooms as $index => $bedroom) {
                        // Ensure $bedroom is an array
                        $bedroom = is_array($bedroom) ? $bedroom : [];
                        $bedroom_name = isset($bedroom['name']) ? $bedroom['name'] : '';
                        $bedroom_type = isset($bedroom['type']) ? $bedroom['type'] : '';
                        $bedroom_guests = isset($bedroom['guests']) ? $bedroom['guests'] : '';
                        $num_beds = isset($bedroom['beds']) ? $bedroom['beds'] : 1;
                        ?>
                        <div class=" input-details double-col bedroom_repeater_item"
                            style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">

                            <div class="form-detail">
                                <label><?php _e('Bedroom name:', 'chaletv2'); ?></label>
                                <input type="text" name="bedrooms[<?php echo $index; ?>][name]"
                                    value="<?php echo esc_attr($bedroom_name); ?>"
                                    placeholder="Guests Room, Main bedroom, etc">
                            </div>
                            <div class="form-detail">
                                <label><?php _e('Number of guests:', 'chaletv2'); ?></label>
                                <input type="number" name="bedrooms[<?php echo $index; ?>][guests]"
                                    value="<?php echo esc_attr($bedroom_guests); ?>" min="1">
                            </div>
                            <div class="blank-col"></div>
                            <div class="form-detail">
                                <label><?php _e('Number of Beds:', 'chaletv2'); ?></label>
                                <input type="number" name="bedrooms[<?php echo $index; ?>][beds]"
                                    value="<?php echo esc_attr($num_beds); ?>" min="1">
                            </div>
                            <div class="form-detail">
                                <label><?php _e('Bedroom Type:', 'chaletv2'); ?></label>
                                <select name="bedrooms[<?php echo $index; ?>][type]" class="details-select">
                                    <?php
                                    $bed_types = [
                                        'king' => 'King',
                                        'queen' => 'Queen',
                                        'double' => 'Double',
                                        'twin' => 'Twin/Single',
                                        'bunk' => 'Bunk Bed',
                                        'sofa' => 'Sofa Bed',
                                        'murphy' => 'Murphy Bed',
                                        'air' => 'Air Mattress',
                                        'crib' => 'Crib',
                                        'futon' => 'Futon',
                                        'loft' => 'Loft Bed',
                                        'rollaway' => 'Rollaway Bed',
                                        'other' => 'Other',
                                    ];
                                    foreach ($bed_types as $value => $label) {
                                        printf(
                                            '<option value="%s"%s>%s</option>',
                                            esc_attr($value),
                                            selected($bedroom_type, $value, false),
                                            esc_html($label)
                                        );
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-detail btn-detail">
                                <label class="light-text bold-text"></label>
                                <button type="button"
                                    class="remove-bedroom-button details-btn button"><?php _e('Remove Bedroom', 'chaletv2'); ?></button>
                            </div>
                            <script>
                                function add_bedroom(btn) {
                                    var container = btn.closest('#bedrooms-container');
                                    var items = container.querySelectorAll('.bedroom_repeater_item');
                                    var last = items[items.length - 1];
                                    console.log('items');
                                    console.log(items);
                                    var clone = last.cloneNode(true);
                                    // Reset input values
                                    clone.querySelectorAll('input').forEach(function (input) {
                                        if (input.type === 'number') input.value = 1;
                                        else input.value = '';
                                    });
                                    // Update name attributes with new index
                                    // Find the next available index (max index + 1)
                                    var maxIndex = -1;
                                    items.forEach(function (item) {
                                        item.querySelectorAll('input').forEach(function (input) {
                                            var match = input.name.match(/\[(\d+)\]/);
                                            if (match && parseInt(match[1]) > maxIndex) {
                                                maxIndex = parseInt(match[1]);
                                            }
                                        });
                                    });
                                    var newIndex = maxIndex + 1;
                                    clone.querySelectorAll('input').forEach(function (input) {
                                        input.name = input.name.replace(/\[\d+\]/, '[' + newIndex + ']');
                                    });
                                    last.after(clone);
                                    // Re-attach remove button event
                                    clone.querySelector('.remove-bedroom-button').addEventListener('click', function () {
                                        var all = container.querySelectorAll('.bedroom_repeater_item');
                                        if (all.length > 1) {
                                            clone.remove();
                                        } else {
                                            alert('At least one bedroom is required.');
                                        }
                                    });
                                }
                                document.addEventListener('DOMContentLoaded', function () {
                                    // Remove bedroom row
                                    document.querySelectorAll('.remove-bedroom-button').forEach(function (btn) {
                                        btn.addEventListener('click', function (e) {
                                            var item = btn.closest('.bedroom_repeater_item');
                                            if (item) {
                                                // Only remove if more than one bedroom remains
                                                var all = document.querySelectorAll('.bedroom_repeater_item');
                                                if (all.length > 1) {
                                                    item.remove();
                                                } else {
                                                    alert('At least one bedroom is required.');
                                                }
                                            }
                                        });
                                    });
                                });
                            </script>
                        </div>
                    <?php } // End foreach loop ?>
                    <button type="button" class="add-bedroom-button details-btn button"
                        onclick="add_bedroom(this)"><?php _e('Add Bedroom', 'chaletv2'); ?></button>
                </div>

            </div>
        </div>
        <div class="container">
            <button class="details-btn main-btn">Save</button>
            <button class="details-btn sec-btn" data-id="price-tab" onclick="showContent(event, this)">Next</button>
            <button class="next-tab-link" data-id="price-tab" onclick="showContent(event ,this)">Go to Price
                settings.</button>
        </div>
    </div>
    <div class="tab-content" id="price-tab" style="display: none;">
        <div class="form-container container-style edit-listing-price">
            <h2 class="title-xl"><?php _e('Listing Price', 'chaletv2'); ?></h2>
            <div class="listing-wrap">
                <div class="left-col">
                    <div class="form-group">
                        <label>Default rate $ (only numbers)</label>
                    </div>
                </div>

                <div class="form-grid tree-col-temp">
                    <div class="form-group">
                        <label
                            for="default_rate_weekend"><?php _e('Weekend Price per Night (Fri, Sat) (€):', 'chaletv2'); ?></label>
                        <input type="number" id="default_rate_weekend" name="default_rate_weekend"
                            value="<?php echo get_chalet_field_value('default_rate_weekend'); ?>" min="0" step="any">
                    </div>

                    <div class="form-group">
                        <label
                            for="default_rate_weekday"><?php _e('Weekday Price per Night (Mon - Thu) (€):', 'chaletv2'); ?></label>

                        <input type="number" id="default_rate_weekday" name="default_rate_weekday"
                            value="<?php echo get_chalet_field_value('default_rate_weekday'); ?>" min="0" step="any">
                    </div>

                    <div class="form-group">
                        <label
                            for="default_rate_week"><?php _e('Week Price per Night (Mon-Sat) (€):', 'chaletv2'); ?></label>
                        <input type="number" id="default_rate_week" name="default_rate_week"
                            value="<?php echo get_chalet_field_value('default_rate_week'); ?>" min="0" step="any">
                    </div>
                </div>
            </div>
            <!-- Confirm this with client -->
            <!-- <div class="listing-wrap">
                <div class="left-col">
                    <div class="form-group">
                        <label><?php _e('Extra Prices', 'chaletv2'); ?></label>
                    </div>
                </div>

                <div class="form-grid tree-col-temp">
                    <div class="form-group">
                        <label for="guests_included"><?php _e('Number of guests included:', 'chaletv2'); ?></label>
                        <input type="number" id="guests_included" name="guests_included"
                            value="<?php echo get_chalet_field_value('guests_included'); ?>" min="0" step="any">
                    </div>

                    <div class="form-group">
                        <label
                            for="extra_price_adult"><?php _e('Extra Price per Adult/Night (€):', 'chaletv2'); ?></label>
                        <input type="number" id="extra_price_adult" name="extra_price_adult"
                            value="<?php echo get_chalet_field_value('extra_price_adult'); ?>" min="0" step="any">
                    </div>

                    <div class="form-group">
                        <label
                            for="extra_price_child"><?php _e('Extra Price per children (3 to 17) per night in $', 'chaletv2'); ?></label>
                        <input type="number" id="extra_price_child" name="extra_price_child"
                            value="<?php echo get_chalet_field_value('extra_price_child'); ?>" min="0" step="any">
                    </div>
                </div>
            </div> -->
            <div class="listing-wrap">
                <div class="left-col">
                    <div class="form-group">
                        <label>Minimum nights of booking</label>
                    </div>

                </div>

                <div class="form-grid tree-col-temp">
                    <div class="form-group">
                        <label for="min_nights"><?php _e('Minimum Nights Default:', 'chaletv2'); ?></label>
                        <input type="number" id="min_nights" name="min_nights"
                            value="<?php echo get_chalet_field_value('min_nights', 1); ?>" min="1">
                    </div>
                    <div class="checkbox-group">

                        <label>
                            <input type="checkbox" name="free_for_babies" value="yes" <?php checked(is_chalet_field_checked('free_for_babies', 'yes')); ?>>
                            <?php _e('Click to make it free for babies 2 years and under.', 'chaletv2'); ?>
                        </label>
                        <small><?php _e('If not selected, the child rate will be applied for babies.', 'chaletv2'); ?></small>
                    </div>

                </div>
            </div>
            <div class="listing-wrap">
                <div class="left-col">
                    <div class="form-group">
                        <label><?php _e('Taxes & CITQ', 'chaletv2'); ?></label>
                    </div>

                </div>

                <div class="form-grid tree-col-temp">
                    <div class="form-group">
                        <label for="tax_gst"><?php _e('GST Tax Number:', 'chaletv2'); ?></label>
                        <input type="text" id="tax_gst" name="tax_gst"
                            value="<?php echo get_chalet_field_value('tax_gst'); ?>">
                        <div class="note">Leave blank if you do not charge GST and QST taxes</div>
                    </div>

                    <div class="form-group">
                        <label for="tax_thq"><?php _e('THQ Tax Number:', 'chaletv2'); ?></label>
                        <input type="text" id="tax_thq" name="tax_thq"
                            value="<?php echo get_chalet_field_value('tax_thq'); ?>">
                    </div>
                    <div class="blank-col"></div>
                    <div class="form-group">
                        <label for="tax_qst"><?php _e('QST Tax Number:', 'chaletv2'); ?></label>
                        <input type="text" id="tax_qst" name="tax_qst"
                            value="<?php echo get_chalet_field_value('tax_qst'); ?>">
                    </div>
                    <div class="file-upload form-group">
                        <?php
                        if (!empty($chalet_data['citq_document']) && is_numeric($chalet_data['citq_document'])) {
                            $attachment_url = wp_get_attachment_url($chalet_data['citq_document']);
                            if ($attachment_url) {
                                echo '<a href="' . esc_url($attachment_url) . '" class="button" target="_blank">' . __('Download CITQ Document', 'chaletv2') . '</a>';
                            }
                        }
                        ?>
                        <label><?php _e('Upload Document (pdf)', 'chaletv2'); ?></label>
                        <input type="file" id="citq_document" name="citq_document" accept="application/pdf" />
                        <label for="citq_docum" class="upload-label" onclick="this.previousElementSibling.click()">
                            <?php _e('CITQ Document:', 'chaletv2'); ?> <i class="fa-solid fa-arrow-up-from-bracket"></i>
                        </label>
                    </div>

                </div>
            </div>
            <div class="listing-wrap">
                <div class="left-col">
                    <div class="form-group">
                        <label><?php _e('Security Deposit', 'chaletv2'); ?></label>

                        <div class="note">Amount blocked on the guest's credit card 1 day before arrival and
                            automatically
                            unblocked 5 days after departure if no claim is made.</div>
                    </div>

                </div>

                <div class="form-grid tree-col-temp">
                    <div class="form-group">
                        <label for="security_deposit"><?php _e('Security Deposit (€):', 'chaletv2'); ?></label>
                        <input type="number" id="security_deposit" name="security_deposit"
                            value="<?php echo get_chalet_field_value('security_deposit'); ?>" min="0" step="any">
                        <div class="note">Leave blank if you wish to include cleaning fee in your rate</div>
                    </div>
                </div>
            </div>
            <div class="listing-wrap">
                <div class="left-col">
                    <div class="form-group">
                        <label>Cleaning Fee</label>
                    </div>

                </div>

                <div class="form-grid tree-col-temp">
                    <div class="form-group">
                        <label>Cleaning Fee in $ (only numbers)</label>
                        <input type="number" name="cleaning_fee"
                            value="<?php echo get_chalet_field_value('cleaning_fee'); ?>" min="0" step="any">
                        <div class="note">Leave blank if you wish to include cleaning fee in your rate</div>
                    </div>
                </div>
            </div>


            <div class="wide-col-temp listing-wrap">
                <div class="left-col">
                    <div class="form-group">
                        <label><?php _e('Extra Options', 'chaletv2'); ?></label>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label><?php _e('Extra Options:', 'chaletv2'); ?></label>
                        <?php
                        // Get existing extra options from $chalet_data if available
                        $extra_options = isset($chalet_data['extra_options']) && is_array($chalet_data['extra_options']) && count($chalet_data['extra_options']) > 0
                            ? $chalet_data['extra_options']
                            : [];
                        ?>
                        <div id="extra-options-list">
                            <?php if (!empty($extra_options)): ?>
                                <?php foreach ($extra_options as $idx => $option): ?>
                                    <div class="inner-form-row extra-option-row" style="opacity: 0.7;">
                                        <input type="text" name="extra_options[<?php echo $idx; ?>][name]" placeholder="Name"
                                            value="<?php echo esc_attr($option['name'] ?? ''); ?>">
                                        <input type="number" name="extra_options[<?php echo $idx; ?>][price]"
                                            placeholder="Price" value="<?php echo esc_attr($option['price'] ?? ''); ?>">
                                        <select name="extra_options[<?php echo $idx; ?>][type]">
                                            <option value="per_stay" <?php selected($option['type'] ?? '', 'per_stay'); ?>>Per
                                                Stay</option>
                                            <option value="per_item" <?php selected($option['type'] ?? '', 'per_item'); ?>>Per
                                                Item</option>
                                        </select>
                                        <button type="button" class="remove-extra-option-btn">Remove</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="inner-form-row extra-option-row" style="opacity: 0.7;">
                                    <input type="text" name="extra_options[0][name]" placeholder="Name" value="">
                                    <input type="number" name="extra_options[0][price]" placeholder="Price" value="">
                                    <select name="extra_options[0][type]">
                                        <option value="per_stay">Per Stay</option>
                                        <option value="per_item">Per Item</option>
                                    </select>
                                    <button type="button" class="remove-extra-option-btn">Remove</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <br>
                        <div class="add_wrapper">
                            <button type="button" id="add-extra-option-btn">Add Extra</button>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                var list = document.getElementById('extra-options-list');
                                var addBtn = document.getElementById('add-extra-option-btn');

                                function getNextIndex() {
                                    var rows = list.querySelectorAll('.extra-option-row');
                                    var max = -1;
                                    rows.forEach(function (row) {
                                        var input = row.querySelector('input[name^="extra_options["]');
                                        if (input) {
                                            var match = input.name.match(/\[(\d+)\]/);
                                            if (match && parseInt(match[1]) > max) max = parseInt(match[1]);
                                        }
                                    });
                                    return max + 1;
                                }

                                function addRow(option) {
                                    var idx = getNextIndex();
                                    var div = document.createElement('div');
                                    div.className = 'inner-form-row extra-option-row';
                                    div.style.opacity = 0.7;
                                    div.innerHTML =
                                        '<input type="text" name="extra_options[' + idx + '][name]" placeholder="Name" value="' + (option && option.name ? option.name : '') + '">' +
                                        '<input type="number" name="extra_options[' + idx + '][price]" placeholder="Price" value="' + (option && option.price ? option.price : '') + '">' +
                                        '<select name="extra_options[' + idx + '][type]">' +
                                        '<option value="per_stay"' + ((option && option.type === 'per_stay') ? ' selected' : '') + '>Per Stay</option>' +
                                        '<option value="per_item"' + ((option && option.type === 'per_item') ? ' selected' : '') + '>Per Item</option>' +
                                        '</select>' +
                                        '<button type="button" class="remove-extra-option-btn">Remove</button>';
                                    list.appendChild(div);
                                    attachRemove(div.querySelector('.remove-extra-option-btn'));
                                }

                                function attachRemove(btn) {
                                    btn.addEventListener('click', function () {
                                        var row = btn.closest('.extra-option-row');
                                        if (row) {
                                            // Only remove if more than one row remains
                                            if (list.querySelectorAll('.extra-option-row').length > 1) {
                                                row.remove();
                                            } else {
                                                // Optionally clear the fields instead of removing the last row
                                                row.querySelectorAll('input, select').forEach(function (el) {
                                                    if (el.tagName === 'INPUT') el.value = '';
                                                    if (el.tagName === 'SELECT') el.selectedIndex = 0;
                                                });
                                            }
                                        }
                                    });
                                }

                                // Attach remove to all existing
                                list.querySelectorAll('.remove-extra-option-btn').forEach(attachRemove);

                                addBtn.addEventListener('click', function () {
                                    addRow();
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="wide-col-temp listing-wrap">
                <div class="left-col">
                    <div class="form-group">
                        <label><?php _e('Check-in / Check-out Times & Extras', 'chaletv2'); ?></label>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label><?php _e('Default Check-in / Check-out Time:', 'chaletv2'); ?></label>
                        <div class="inner-form-row" style="opacity: 0.7;">
                            <input type="time" id="checkin_time" name="checkin_time"
                                value="<?php echo get_chalet_field_value('checkin_time', '14:00'); ?>">
                            <input type="time" id="checkout_time" name="checkout_time"
                                value="<?php echo get_chalet_field_value('checkout_time', '14:00'); ?>">

                            <!-- <button>Add Extra</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Empty Seson -->
        <!-- Empty seasonal rate template for JS cloning -->
        <div class="form-container container-style price-periods seasonal_rates_container" style="display:none!important;">
            <h2><?php _e('Seasonal Rates', 'chaletv2'); ?></h2>
            <div class="spcaeer-xs"></div>
            <?php $index = '__INDEX__'; // Placeholder for JS to replace ?>
            <div class="period-row">
            <div class="form-group">
                <label>Period name </label>
                <input type="text" value="" name="seasonal_rates[<?= $index ?>][name]" placeholder="Summer 2025">
            </div>
            <div class="form-group">
                <label>Period dates</label>
                <input type="date" value="" name="seasonal_rates[<?= $index ?>][start_date]">
                <input type="date" value="" name="seasonal_rates[<?= $index ?>][end_date]">
            </div>
            <div class="form-group">
                <label>Default Price/night </label>
                <input type="number" value="" name="seasonal_rates[<?= $index ?>][price_night]">
            </div>
            </div>
            <div class="period-labels"></div>
            <div class="divider-xl"></div>
            <h2>Price per day of the week</h2>
            <p>Set a different night rate here depending on the day of the week</p>
            <div class="spcaeer-xs"></div>
            <div class="period-row days-veiw">
            <div class="form-group">
                <label>MON</label>
                <input type="number" value="" name="seasonal_rates[<?= $index ?>][price_monday]">
            </div>
            <div class="form-group">
                <label>TUE</label>
                <input type="number" value="" name="seasonal_rates[<?= $index ?>][price_tuesday]">
            </div>
            <div class="form-group">
                <label>WED</label>
                <input type="number" value="" name="seasonal_rates[<?= $index ?>][price_wednesday]">
            </div>
            <div class="form-group">
                <label>THURS</label>
                <input type="number" value="" name="seasonal_rates[<?= $index ?>][price_thursday]">
            </div>
            <div class="form-group">
                <label>FRI</label>
                <input type="number" value="" name="seasonal_rates[<?= $index ?>][price_friday]">
            </div>
            <div class="form-group">
                <label>SAT</label>
                <input type="number" value="" name="seasonal_rates[<?= $index ?>][price_saturday]">
            </div>
            <div class="form-group">
                <label>SUN</label>
                <input type="number" value="" name="seasonal_rates[<?= $index ?>][price_sunday]">
            </div>
            </div>
            <div class="divider-xl"></div>
            <h2>Fees for additional guests</h2>
            <p>Set a supplement per guest per night. The number of guests includes the minimum number of adults for the stay. You can adjust the additional pricing for adults, children and babies</p>
            <div class="spcaeer-xs"></div>
            <div class="period-row days-veiw">
            <div class="form-group">
                <label>Adults</label>
                <input type="number" name="seasonal_rates[<?= $index ?>][extra_adult]" value="">
            </div>
            <div class="form-group">
                <label>Children</label>
                <input type="number" name="seasonal_rates[<?= $index ?>][extra_child]" value="">
            </div>
            <div class="form-group">
                <label>Babies</label>
                <input type="number" name="seasonal_rates[<?= $index ?>][extra_baby]" value="">
            </div>
            <div class="form-group dvd-label">
                <label>after </label>
            </div>
            <div class="form-group">
                <label></label>
                <select name="seasonal_rates[<?= $index ?>][charge_after_guests]">
                <?php for ($i = 0; $i <= 10; $i++) {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                } ?>
                </select>
            </div>
            </div>
            <div class="divider-xl"></div>
            <div class="form-group mim-night">
            <label>Minimum length of stay (nights)</label>
            <input type="number" value="" name="seasonal_rates[<?= $index ?>][min_stay]">
            </div>
            <div class="spcaeer-xs"></div>
            <h2>Arrival/departure days unavailable</h2>
            <p>Determine which days of the week are not available for arrival and departure</p>
            <div class="checkin-days-wrap">
            <div class="labels-wrap">
                <p>Check-in</p>
                <p>Check-out</p>
            </div>
            <div class="checkin-check">
                <?php
                $days = [
                'monday' => 'Mon',
                'tuesday' => 'Tue',
                'wednesday' => 'Wed',
                'thursday' => 'Thu',
                'friday' => 'Fri',
                'saturday' => 'Sat',
                'sunday' => 'Sun',
                ];
                foreach ($days as $day_key => $day_label): ?>
                <div class="cc-col">
                    <label for="checkin_<?php echo $day_key; ?>"><?php echo $day_label; ?></label>
                    <input type="checkbox" name="seasonal_rates[<?= $index ?>][checkin_unavailable][]" id="checkin_<?php echo $day_key; ?>" value="<?php echo $day_key; ?>">
                    <input type="checkbox" name="seasonal_rates[<?= $index ?>][checkout_unavailable][]" id="checkout_<?php echo $day_key; ?>_out" value="<?php echo $day_key; ?>">
                </div>
                <?php endforeach; ?>
            </div>
            </div>
            <h2>Early Check-in and Late Check-out unavailable</h2>
            <p>Determine which days of the week are not available for arrival and departure</p>
            <div class="checkin-days-wrap">
            <div class="labels-wrap">
                <p>Check-in</p>
                <p>Check-out</p>
            </div>
            <div class="checkin-check">
                <?php
                foreach ($days as $day_key => $day_label): ?>
                <div class="cc-col">
                    <label for="early_checkin_<?php echo $day_key; ?>"><?php echo $day_label; ?></label>
                    <input type="checkbox" name="seasonal_rates[<?= $index ?>][early_checkin_unavailable][]" id="early_checkin_<?php echo $day_key; ?>" value="<?php echo $day_key; ?>">
                    <input type="checkbox" name="seasonal_rates[<?= $index ?>][late_checkout_unavailable][]" id="late_checkout_<?php echo $day_key; ?>" value="<?php echo $day_key; ?>">
                </div>
                <?php endforeach; ?>
            </div>
            </div>
            <button type="button green_button" onclick="remove_season(this);">Remove Season</button>
        </div>
        <!-- End empty seasonal rate template -->
        <div class="form-container container-style price-periods seasonal_rates_container">
            <h2><?php _e('Seasonal Rates', 'chaletv2'); ?></h2>
            <div class="spcaeer-xs"></div>
            <?php $index = 0; ?>
            <div class="period-row">
                <div class="form-group">
                    <label>Period name </label>
                    <input type="text" value="" name="seasonal_rates[<?= $index ?>][name]"
                        placeholder="Summer 2025">
                </div>

                <div class="form-group">
                    <label>Period dates
                    </label>
                    <input type="date" value="" name="seasonal_rates[<?= $index ?>][start_date]">
                    <input type="date" value="" name="seasonal_rates[<?= $index ?>][end_date]">
                </div>
                <div class="form-group">
                    <label>Default Price/night </label>
                    <input type="number" value="" name="seasonal_rates[<?= $index ?>][price_night]">
                </div>

            </div>
            <div class="period-labels">
                <div class="date-label"> <span> 20 May 2025 - 4 June 20205 </span> <button type="button">x</button>
                </div>
                <div class="date-label"> <span> 20 May 2025 - 4 June 20205 </span> <button type="button">x</button>
                </div>
                <div class="date-label"> <span> 20 May 2025 - 4 June 20205 </span> <button type="button">x</button>
                </div>
                <div class="date-label"> <span> 20 May 2025 - 4 June 20205 </span> <button type="button">x</button>
                </div>
            </div>
            <div class="divider-xl"></div>
            <h2>Price per day of the week</h2>
            <p>Set a different night rate here depending on the day of the week</p>
            <div class="spcaeer-xs"></div>
            <div class="period-row days-veiw">
                <div class="form-group">
                    <label>MON</label>
                    <input type="number" value="<?php echo get_chalet_field_value('price_monday'); ?>"
                        name="seasonal_rates[<?= $index ?>][price_monday]">
                </div>
                <div class="form-group">
                    <label>TUE</label>
                    <input type="number" value="<?php echo get_chalet_field_value('price_tuesday'); ?>"
                        name="seasonal_rates[<?= $index ?>][price_tuesday]">
                </div>
                <div class="form-group">
                    <label>WED</label>
                    <input type="number" value="<?php echo get_chalet_field_value('price_wednesday'); ?>"
                        name="seasonal_rates[<?= $index ?>][price_wednesday]">
                </div>
                <div class="form-group">
                    <label>THURS</label>
                    <input type="number" value="<?php echo get_chalet_field_value('price_thursday'); ?>"
                        name="seasonal_rates[<?= $index ?>][price_thursday]">
                </div>
                <div class="form-group">
                    <label>FRI</label>
                    <input type="number" value="<?php echo get_chalet_field_value('price_friday'); ?>"
                        name="seasonal_rates[<?= $index ?>][price_friday]">
                </div>
                <div class="form-group">
                    <label>SAT</label>
                    <input type="number" value="<?php echo get_chalet_field_value('price_saturday'); ?>"
                        name="seasonal_rates[<?= $index ?>][price_saturday]">
                </div>
                <div class="form-group">
                    <label>SUN</label>
                    <input type="number" value="<?php echo get_chalet_field_value('price_sunday'); ?>"
                        name="seasonal_rates[<?= $index ?>][price_sunday]">
                </div>
            </div>
            <div class="divider-xl"></div>
            <h2>Fees for additional guests</h2>
            <p>Set a supplement per guest per night. The number of guests includes the minimum number of adults for
                the
                stay. You can adjust the additional pricing for adults, children and babies</p>
            <div class="spcaeer-xs"></div>
            <div class="period-row days-veiw">
                <div class="form-group">
                    <label>Adults</label>
                    <input type="number" name="seasonal_rates[<?= $index ?>][extra_adult]"
                        value="<?php echo get_chalet_field_value('extra_adult'); ?>">
                </div>
                <div class="form-group">
                    <label>Children</label>
                    <input type="number" name="seasonal_rates[<?= $index ?>][extra_child]"
                        value="<?php echo get_chalet_field_value('extra_child'); ?>">
                </div>
                <div class="form-group">
                    <label>Babies</label>
                    <input type="number" name="seasonal_rates[<?= $index ?>][extra_baby]"
                        value="<?php echo get_chalet_field_value('extra_child'); ?>">
                </div>
                <div class="form-group dvd-label">
                    <label>after </label>
                </div>
                <div class="form-group">
                    <label></label>
                    <select name="seasonal_rates[<?= $index ?>][charge_after_guests]">
                        <?php
                        $selected_guests_included = get_chalet_field_value('charge_after_guests', 0);
                        for ($i = 0; $i <= 10; $i++) {
                            echo '<option value="' . $i . '" ' . selected($selected_guests_included, $i, false) . '>' . $i . '</option>';
                        } ?>
                    </select>
                </div>
            </div>
            <div class="divider-xl"></div>

            <div class="form-group mim-night">
                <label>Minimum length of stay (nights)</label>
                <input type="number" value="" name="seasonal_rates[<?= $index ?>][min_stay]">
            </div>
            <div class="spcaeer-xs"></div>
            <h2>Arrival/departure days unavailable
            </h2>
            <p>Determine which days of the week are not available for arrival and departure</p>

            <!-- TM: Done Till Here -->
            <div class=" checkin-days-wrap">
                <div class="labels-wrap">
                    <p>Check-in</p>
                    <p>Check-out</p>
                </div>
                <div class="checkin-check">
                    <?php
                    // Days of the week, lowercase
                    $days = [
                        'monday' => 'Mon',
                        'tuesday' => 'Tue',
                        'wednesday' => 'Wed',
                        'thursday' => 'Thu',
                        'friday' => 'Fri',
                        'saturday' => 'Sat',
                        'sunday' => 'Sun',
                    ];

                    // Get saved values if available
                    $checkin_unavailable = isset($chalet_data['checkin_unavailable']) ? (array) $chalet_data['checkin_unavailable'] : [];
                    $checkout_unavailable = isset($chalet_data['checkout_unavailable']) ? (array) $chalet_data['checkout_unavailable'] : [];

                    foreach ($days as $day_key => $day_label): ?>
                        <div class="cc-col">
                            <label for="checkin_<?php echo $day_key; ?>"><?php echo $day_label; ?></label>
                            <input type="checkbox" name="seasonal_rates[<?= $index ?>][checkin_unavailable][]"
                                id="checkin_<?php echo $day_key; ?>" value="<?php echo $day_key; ?>" <?php checked(in_array($day_key, $checkin_unavailable)); ?>>
                            <input type="checkbox" name="seasonal_rates[<?= $index ?>][checkout_unavailable][]"
                                id="checkout_<?php echo $day_key; ?>_out" value="<?php echo $day_key; ?>" <?php checked(in_array($day_key, $checkout_unavailable)); ?>>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
            <h2>Early Check-in and Late Check-out unavailable
            </h2>
            <p>Determine which days of the week are not available for arrival and departure</p>


            <div class=" checkin-days-wrap">
                <div class="labels-wrap">
                    <p>Check-in</p>
                    <p>Check-out</p>
                </div>
                <div class="checkin-check">
                    <?php
                    // Days of the week, lowercase
                    $days = [
                        'monday' => 'Mon',
                        'tuesday' => 'Tue',
                        'wednesday' => 'Wed',
                        'thursday' => 'Thu',
                        'friday' => 'Fri',
                        'saturday' => 'Sat',
                        'sunday' => 'Sun',
                    ];

                    // Get saved values if available
                    $early_checkin_unavailable = isset($chalet_data['early_checkin_unavailable']) ? (array) $chalet_data['early_checkin_unavailable'] : [];
                    $late_checkout_unavailable = isset($chalet_data['late_checkout_unavailable']) ? (array) $chalet_data['late_checkout_unavailable'] : [];

                    foreach ($days as $day_key => $day_label): ?>
                        <div class="cc-col">
                            <label for="early_checkin_<?php echo $day_key; ?>"><?php echo $day_label; ?></label>
                            <input type="checkbox" name="seasonal_rates[<?= $index ?>][early_checkin_unavailable][]"
                                id="early_checkin_<?php echo $day_key; ?>" value="<?php echo $day_key; ?>" <?php checked(in_array($day_key, $early_checkin_unavailable)); ?>>
                            <input type="checkbox" name="seasonal_rates[<?= $index ?>][late_checkout_unavailable][]"
                                id="late_checkout_<?php echo $day_key; ?>" value="<?php echo $day_key; ?>" <?php checked(in_array($day_key, $late_checkout_unavailable)); ?>>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button type="button green_button" onclick="remove_season(this);">Remove Season</button>
        </div>
        <button type="button" class="add-seasonal-rate-btn details-btn button"
        onclick="addSeasonalRate(this)"><?php _e('Add Seasonal Rate', 'chaletv2'); ?></button>
        <br>
        <br>
        <div class="container">
            <button class="details-btn main-btn">Save</button>
            <button class="details-btn sec-btn" data-id="terms-tab" onclick="showContent(event, this)">Next</button>
            <button class="next-tab-link" data-id="terms-tab" onclick="showContent(event, this)">Go to Images
                settings.</button>
        </div>
    </div>
    <div class="tab-content" id="terms-tab" style="display: none;">
        <div class="condition-section">
            <div class="container">
                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text title-xl"><?php _e('Terms and Conditions', 'chaletv2'); ?> </h2>
                    </div>
                    <div class="">
                        <h3 class="light-text bold-text"><?php _e('Reservation Policy', 'chaletv2'); ?></h3>

                        <div class="policy-detail-wrapper radio-group">
                            <label><input type="radio" name="reservation_policy" value="policy_50_50_3" <?php checked(get_chalet_field_value('reservation_policy'), 'policy_50_50_3'); ?>>
                                <?php _e('Policy 50-50 (3 days before stay)', 'chaletv2'); ?></label>
                            <label><input type="radio" name="reservation_policy" value="policy_50_50_14" <?php checked(get_chalet_field_value('reservation_policy'), 'policy_50_50_14'); ?>>
                                <?php _e('Policy 50-50 (14 days before stay)', 'chaletv2'); ?></label>
                            <label><input type="radio" name="reservation_policy" value="policy_25_25_50_14" <?php checked(get_chalet_field_value('reservation_policy'), 'policy_25_25_50_14'); ?>>
                                <?php _e('Policy 25-25-50 (14 days before stay)', 'chaletv2'); ?></label>
                        </div>
                    </div>
                </div>
                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text title-xl"></h2>
                    </div>
                    <div class="">
                        <h3 class="light-text bold-text"><?php _e('Cancellation Policy', 'chaletv2'); ?></h3>

                        <div class="policy-detail-wrapper radio-group">
                            <label><input type="radio" name="cancellation_policy" value="flexible" <?php checked($chalet_data['cancellation_policy'] ?? '', 'flexible'); ?>>
                                <?php _e('Flexible', 'chaletv2'); ?></label>
                            <label><input type="radio" name="cancellation_policy" value="moderate" <?php checked($chalet_data['cancellation_policy'] ?? '', 'moderate'); ?>>
                                <?php _e('Moderate', 'chaletv2'); ?></label>
                            <label><input type="radio" name="cancellation_policy" value="strict" <?php checked($chalet_data['cancellation_policy'] ?? '', 'strict'); ?>>
                                <?php _e('Strict', 'chaletv2'); ?></label>
                        </div>
                    </div>
                </div>
                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text title-xl"></h2>
                    </div>
                    <div class="">
                        <!-- <h3 class="light-text bold-text"><?php _e('Cancellation Policy', 'chaletv2'); ?></h3> -->

                        <div class="policy-detail-wrapper radio-group">
                            <label
                                for="preparation_time"><?php _e('Preparation Time (Nights Blocked Before Stay)', 'chaletv2'); ?></label>
                            <input type="number" name="preparation_time" id="preparation_time" min="0"
                                value="<?php echo esc_attr($chalet_data['preparation_time'] ?? ''); ?>">
                            <p class="description">
                                <?php _e('Number of nights blocked before the start of the stay for preparation.', 'chaletv2'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text title-xl"></h2>
                    </div>
                    <div class="">
                        <!-- <h3 class="light-text bold-text"><?php _e('Cancellation Policy', 'chaletv2'); ?></h3> -->

                        <div class="policy-detail-wrapper radio-group">
                            <label
                                for="reservation_window"><?php _e('Reservation Window (Days in Advance)', 'chaletv2'); ?></label>
                            <input type="number" name="reservation_window" id="reservation_window" min="0"
                                value="<?php echo esc_attr($chalet_data['reservation_window'] ?? ''); ?>">
                            <p class="description">
                                <?php _e('Maximum number of days in advance a user can book.', 'chaletv2'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text title-xl"></h2>
                    </div>
                    <div class="">
                        <!-- <h3 class="light-text bold-text"><?php _e('Cancellation Policy', 'chaletv2'); ?></h3> -->

                        <div class="policy-detail-wrapper radio-group">
                            <label
                                for="reservation_notice"><?php _e('Reservation Notice (Minimum Days Before Arrival)', 'chaletv2'); ?></label>
                            <input type="number" name="reservation_notice" id="reservation_notice" min="0"
                                value="<?php echo esc_attr($chalet_data['reservation_notice'] ?? ''); ?>">
                            <p class="description">
                                <?php _e('Minimum number of days notice required to accept a reservation.', 'chaletv2'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text title-xl"></h2>
                    </div>
                    <div class="">
                        <!-- <h3 class="light-text bold-text"><?php _e('Cancellation Policy', 'chaletv2'); ?></h3> -->

                        <div class="policy-detail-wrapper radio-group">
                            <label
                                for="reservation_contract"><?php _e('Reservation Contract / Policies', 'chaletv2'); ?></label>
                            <textarea name="reservation_contract" id="reservation_contract"
                                rows="10"><?php echo esc_textarea($chalet_data['reservation_contract'] ?? ''); ?></textarea>
                            <p class="description">
                                <?php _e('Enter the full reservation contract terms and conditions.', 'chaletv2'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <button class="details-btn main-btn">Save</button>
            <button class="details-btn sec-btn" data-id="instructions-tab"
                onclick="showContent(event, this)">Next</button>
            <button class="next-tab-link" data-id="instructions-tab" onclick="showContent(event, this)">Go to
                instructions
                settings.</button>
        </div>
    </div>
    <div class="tab-content" id="instructions-tab" style="display: none;">
        <div class="condition-section">
            <div class="container">
                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text title-xl"><?php _e('Instructions', 'chaletv2'); ?></h2>
                    </div>
                    <div class="">
                        <!-- <p class="light-text">Here you can create the content of the instructions automatically sent
                            to your guests via email notification.
                        </p> -->
                    </div>
                </div>
                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text">Check-in Instructions</h2>
                        <div class="form-detail small-select">
                            <label class="light-text"
                                for="checkin_instructions_days"><?php _e('Send Check-in Instructions', 'chaletv2'); ?></label>
                            <input type="number" name="checkin_instructions_days" id="checkin_instructions_days" min="0"
                                value="<?php echo esc_attr($chalet_data['checkin_instructions_days'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="">
                        <!-- <h3 class="light-text bold-text">Reservation Policies</h3> -->
                        <label class="light-text"
                            for="checkin_instructions"><?php _e('How many days before check-in to send instructions', 'chaletv2'); ?>
                        </label>
                        <div class="policy-detail-wrapper">
                            <!-- <div class="icon-wrapper">
                                 <img src="<?= get_template_directory_uri() ?>/dashboard/images/edit-icon.png" alt="">
                            </div> -->
                            <textarea name="checkin_instructions" class="details-textarea" id="checkin_instructions"
                                rows="5"><?php echo esc_textarea($chalet_data['checkin_instructions'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text"><?php _e('Check-out Instructions', 'chaletv2'); ?></h2>
                        <div class="form-detail small-select">
                            <label class="light-text"
                                for="checkin_instructions_days"><?php _e('Send Check-out Instructions', 'chaletv2'); ?></label>
                            <input type="number" name="checkout_instructions_days" id="checkout_instructions_days"
                                min="0"
                                value="<?php echo esc_attr($chalet_data['checkout_instructions_days'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="">
                        <!-- <h3 class="light-text bold-text">Reservation Policies</h3> -->
                        <label class="light-text"
                            for="checkout_instructions"><?php _e('How many days before check-out to send instructions.', 'chaletv2'); ?>
                        </label>
                        <div class="policy-detail-wrapper">
                            <!-- <div class="icon-wrapper">
                                 <img src="<?= get_template_directory_uri() ?>/dashboard/images/edit-icon.png" alt="">
                            </div> -->
                            <textarea name="checkout_instructions" class="details-textarea" id="itinerary_instructions"
                                rows="5"><?php echo esc_textarea($chalet_data['checkout_instructions'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>


                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text"><?php _e('Itinerary Instructions', 'chaletv2'); ?></h2>
                        <div class="form-detail small-select">
                            <label class="light-text"
                                for="itinerary_instructions_days"><?php _e('Send Itinerary Instructions', 'chaletv2'); ?></label>
                            <input type="number" name="itinerary_instructions_days" id="itinerary_instructions_days"
                                min="0"
                                value="<?php echo esc_attr($chalet_data['itinerary_instructions_days'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="">
                        <!-- <h3 class="light-text bold-text">Reservation Policies</h3> -->
                        <label class="light-text"
                            for="itinerary_instructions"><?php _e('How many days before check-in to send itinerary.', 'chaletv2'); ?>
                        </label>
                        <div class="policy-detail-wrapper">
                            <!-- <div class="icon-wrapper">
                                 <img src="<?= get_template_directory_uri() ?>/dashboard/images/edit-icon.png" alt="">
                            </div> -->
                            <textarea name="itinerary_instructions" id="itinerary_instructions" class="details-textarea"
                                rows="5"><?php echo esc_textarea($chalet_data['itinerary_instructions'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text"><?php _e('Reminder of Rules', 'chaletv2'); ?></h2>
                        <div class="form-detail small-select">
                            <label class="light-text"
                                for="rules_reminder"><?php _e('Reminder of Rules', 'chaletv2'); ?></label>
                            <input type="number" name="rules_reminder_days" id="rules_reminder_days" min="0"
                                value="<?php echo esc_attr($chalet_data['rules_reminder_days'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="">
                        <!-- <h3 class="light-text bold-text">Reservation Policies</h3> -->
                        <label class="light-text"
                            for="rules_reminder"><?php _e('How many days before check-in to send rules reminder.', 'chaletv2'); ?>
                        </label>
                        <div class="policy-detail-wrapper">
                            <!-- <div class="icon-wrapper">
                                 <img src="<?= get_template_directory_uri() ?>/dashboard/images/edit-icon.png" alt="">
                            </div> -->
                            <textarea name="rules_reminder" id="rules_reminder" class="details-textarea"
                                rows="5"><?php echo esc_textarea($chalet_data['rules_reminder'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text"><?php _e('Local Guide', 'chaletv2'); ?></h2>
                        <div class="form-detail small-select">
                            <label class="light-text"
                                for="local_guide_days"><?php _e('Send Local Guide (Days Before)', 'chaletv2'); ?>
                                <input type="number" name="local_guide_days" id="local_guide_days" min="0"
                                    value="<?php echo esc_attr($chalet_data['local_guide_days'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="">
                        <!-- <h3 class="light-text bold-text">Reservation Policies</h3> -->
                        <label class="light-text"
                            for="local_guide"><?php _e('How many days before check-in to send local guide.', 'chaletv2'); ?>
                        </label>
                        <div class="policy-detail-wrapper">
                            <!-- <div class="icon-wrapper">
                                 <img src="<?= get_template_directory_uri() ?>/dashboard/images/edit-icon.png" alt="">
                            </div> -->
                            <textarea name="local_guide" id="local_guide" class="details-textarea"
                                rows="5"><?php echo esc_textarea($chalet_data['local_guide'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="wide-col-trem">
                    <div class="">
                        <h2 class="light-text bold-text"><?php _e('Emergency', 'chaletv2'); ?></h2>

                    </div>
                    <div class="">
                        <!-- <h3 class="light-text bold-text">Reservation Policies</h3> -->
                        <label for="emergency_contact"><?php _e('Emergency Contact', 'chaletv2'); ?></label>
                        <div class="policy-detail-wrapper">
                            <textarea name="emergency_contact" id="emergency_contact" class="details-textarea"
                                rows="5"><?php echo esc_textarea($chalet_data['emergency_contact'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="container">
            <button class="details-btn main-btn">Save</button>
            <button class="details-btn sec-btn" data-id="media-tab" onclick="showContent(event, this)">Next</button>
            <button class="next-tab-link" data-id="media-tab" onclick="showContent(event, this)">Go to instructions
                settings.</button>
        </div>
    </div>
    <div class="tab-content" id="media-tab" style="display: none;">
        <div class="images-section">
            <div class="container">
                <h2 class="title-xl">Images</h2>
                <div class="images-wrapper-row" id="image-preview-wrapper">
                    <?php
                    // Show previously uploaded images (dynamic)
                    if (!empty($chalet_data['chalet_images']) && is_array($chalet_data['chalet_images'])):
                        foreach ($chalet_data['chalet_images'] as $img_idx => $img):
                            // If using Carbon Fields, $img may be an array with 'id' or just an attachment ID
                            $img_id = is_array($img) && isset($img['id']) ? $img['id'] : $img;
                            $img_url = wp_get_attachment_image_url($img_id, 'medium');
                            if (!$img_url)
                                continue;
                            // Check if this is the featured image
                            $is_featured = isset($chalet_data['featured_image']) && $chalet_data['featured_image'] == $img_id;
                            ?>
                            <div class="img-detail" data-img-id="<?php echo esc_attr($img_id); ?>">
                                <div class="top-detail">
                                    <img src="<?= get_template_directory_uri() ?>/dashboard/images/star.png" alt=""
                                        class="star-icon<?php echo $is_featured ? ' featured' : ''; ?>"
                                        title="<?php echo $is_featured ? esc_attr__('Featured', 'chaletv2') : esc_attr__('Set as featured', 'chaletv2'); ?>">
                                    <button type="button" class="remove-image-btn"
                                        title="<?php esc_attr_e('Remove', 'chaletv2'); ?>">&times;</button>
                                </div>
                                <img src="<?php echo esc_url($img_url); ?>" alt="" class="bedroom-img">
                                <input type="hidden" name="chalet_images_existing[]" value="<?php echo esc_attr($img_id); ?>">
                            </div>
                            <?php
                        endforeach;
                    endif;
                    ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            // Remove image button
                            document.querySelectorAll('.remove-image-btn').forEach(function (btn) {
                                btn.addEventListener('click', function () {
                                    var imgDetail = btn.closest('.img-detail');
                                    if (imgDetail) imgDetail.remove();
                                });
                            });
                            // Set featured image
                            document.querySelectorAll('.star-icon').forEach(function (star) {
                                star.addEventListener('click', function () {
                                    document.querySelectorAll('.star-icon').forEach(function (s) {
                                        s.classList.remove('featured');
                                    });
                                    star.classList.add('featured');
                                    // Optionally set a hidden input for featured image
                                    var imgId = star.closest('.img-detail').getAttribute('data-img-id');
                                    let featuredInput = document.querySelector('input[name="featured_image"]');
                                    if (!featuredInput) {
                                        featuredInput = document.createElement('input');
                                        featuredInput.type = 'hidden';
                                        featuredInput.name = 'featured_image';
                                        document.getElementById('chalet-dashboard-form').appendChild(featuredInput);
                                    }
                                    featuredInput.value = imgId;
                                });
                            });
                        });
                    </script>
                </div>

                <div class="sm-divider"></div>

                <div class="btn-details-row">
                    <div class="images-btn-details">
                        <div id="drop-area" class="drop-zone"
                            style="border: 2px dashed #ccc; padding: 20px; text-align: center;">
                            <input id="chalet-image-input" type="file" name="chalet_images[]" style="display:none;"
                                multiple accept="image/*">
                            <span class="light-text">Drag and Drop images or</span><br>
                            <button type="button" class="details-btn" id="select-images-btn">Select Media</button>
                        </div>
                    </div>
                </div>

                <span class="light-text"> * Click on the ‘star’ on the image to select featured</span>
                <span class="light-text"> **Change images order with Drag & Drop.</span>
                <div class="sm-divider"></div>
                <h2>Video</h2>
                <input type="text" name="video_link" class="big-input sm-input"
                    placeholder="Add a video link for your chalet from Youtube or other video platform"
                    value="<?php echo esc_attr(get_chalet_field_value('video_link')); ?>">
            </div>
        </div>
        <div class="container">
            <button class="details-btn main-btn">Save</button>
            <button class="details-btn sec-btn" data-id="tab-amenities" onclick="showContent(event, this)">Next</button>
            <button class="next-tab-link" data-id="tab-amenities" onclick="showContent(event, this)">Go to
                Amenities settings.</button>
        </div>
    </div>
    <div class="tab-content" id="tab-amenities" style="display: none;">
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
                        <?php
                        // Define the feature categories and their labels
                        $feature_categories = [
                            'indoor' => __('Indoor Features', 'chaletv2'),
                            'outdoor' => __('Outdoor Features', 'chaletv2'),
                            'kitchen' => __('Kitchen', 'chaletv2'),
                            'family' => __('Family', 'chaletv2'),
                            'sports' => __('Sports', 'chaletv2'),
                            'services' => __('Services', 'chaletv2'),
                            'accessibility' => __('Accessibility', 'chaletv2'),
                            'events' => __('Events', 'chaletv2'),
                        ];

                        // Get selected features for this chalet (array of post IDs)
                        
                        foreach ($feature_categories as $cat_slug => $cat_label):
                            $selected_features = isset($chalet_data[$cat_slug . '_features']) && is_array($chalet_data[$cat_slug . '_features']) ? $chalet_data[$cat_slug . '_features'] : [];
                            $IDs = [];
                            foreach ($selected_features as $feature) {
                                $IDs[] = $feature['id'];
                            }
                            // Query features by category (meta field '_feature_type')
                            $features = get_features_by_category($cat_slug);
                            if (!$features)
                                continue;
                            ?>
                            <div class="amenities-details">
                                <h4><?php echo esc_html($cat_label); ?></h4>
                                <div class="list-wrapper">
                                    <ul>
                                        <?php foreach ($features as $feature):
                                            $icon_url = carbon_get_post_meta($feature->ID, 'feature_icon');
                                            $checked = in_array($feature->ID, $IDs) ? 'checked' : '';
                                            ?>
                                            <li>
                                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                                    <input type="checkbox" name="<?= $cat_slug ?>_features[]"
                                                        value="<?php echo esc_attr($feature->ID); ?>" <?php echo $checked; ?>
                                                        style="margin-right:6px;">
                                                    <?php if ($icon_url): ?>
                                                        <div class="list-img">
                                                            <img src="<?php echo esc_url($icon_url); ?>"
                                                                alt="<?php echo esc_attr($feature->post_title); ?>">
                                                        </div>
                                                    <?php endif; ?>
                                                    <span><?php echo esc_html($feature->post_title); ?></span>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="container">
                <button class="details-btn main-btn">Save</button>
                <button class="details-btn sec-btn" data-id="location-tab"
                    onclick="showContent(event, this)">Next</button>
                <button class="next-tab-link" data-id="location-tab" onclick="showContent(event, this)">Go to
                    Location settings.</button>
            </div>
        </div>
    </div>

    <div class="location-section tab-content" id="location-tab" style="display: none;">
        <div class="container">
            <h2 class="title-xl"> Location</h2>
            <!-- <span class="light-text"> location details</span> -->
            <div class="location-details">
                <div class="information-row">
                    <div class="text-details"><span class="light-text"> Chalet full address</span></div>
                    <div class="input-details">
                        <div class="form-detail">
                            <label class="light-text"> Full Address</label>
                            <input type="text" placeholder="Full Address" class="big-input" name="full_address"
                                value="<?php echo esc_attr(get_chalet_field_value('full_address')); ?>">
                        </div>
                    </div>
                </div>

                <div class="information-row">
                    <div class="text-details"><span class="light-text"> Country and Province</span></div>
                    <div class="input-details">
                        <div class="form-detail">
                            <label class="light-text"> Country</label>
                            <select name="country" id="country">
                                <?php
                                $countries = ['Canada'];
                                $selected_country = get_chalet_field_value('country', 'Canada');
                                foreach ($countries as $country) {
                                    printf(
                                        '<option value="%s"%s>%s</option>',
                                        esc_attr($country),
                                        selected($selected_country, $country, false),
                                        esc_html($country)
                                    );
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-detail">
                            <label class="light-text"> Province</label>
                            <select name="province" id="province">
                                <?php
                                $provinces = [ 'Ontario', 'Quebec', 'Nova Scotia', 'New Brunswick', 'Manitoba', 'British Columbia', 'Prince Edward Island', 'Saskatchewan', 'Alberta', 'Newfoundland and Labrador', 'Yukon', 'Northwest Territories', 'Nunavut', ];
                                $selected_province = get_chalet_field_value('province', 'Quebec');
                                foreach ($provinces as $province) {
                                    printf(
                                        '<option value="%s"%s>%s</option>',
                                        esc_attr($province),
                                        selected($selected_province, $province, false),
                                        esc_html($province)
                                    );
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="information-row">
                    <div class="text-details"><span class="light-text"> City and Region</span></div>
                    <div class="input-details">
                        <div class="form-detail">
                            <label class="light-text"> City</label>
                            <select name="city" id="city">
                                <?php
                                $cities = [
                                    '' => 'Select City',
                                    'Montreal' => 'Montreal',
                                    'Toronto' => 'Toronto',
                                    'Vancouver' => 'Vancouver',
                                    'Calgary' => 'Calgary',
                                    'Quebec City' => 'Quebec City',
                                    'Ottawa' => 'Ottawa',
                                    'Edmonton' => 'Edmonton',
                                    'Halifax' => 'Halifax',
                                    'Victoria' => 'Victoria'
                                ];
                                $selected_city = get_chalet_field_value('city', '');
                                foreach ($cities as $value => $label) {
                                    printf(
                                        '<option value="%s"%s>%s</option>',
                                        esc_attr($value),
                                        selected($selected_city, $value, false),
                                        esc_html($label)
                                    );
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-detail">
                            <label class="light-text"> Region</label>
                            <select name="region" id="region">
                                <?php
                                $regions = [
                                    '' => 'Select Region',
                                    'Abitibi-Témiscamingue' => 'Abitibi-Témiscamingue',
                                    'Bas-Saint-Laurent' => 'Bas-Saint-Laurent',
                                    'Capitale-Nationale' => 'Capitale-Nationale',
                                    'Centre-du-Québec' => 'Centre-du-Québec',
                                    'Chaudière-Appalaches' => 'Chaudière-Appalaches',
                                    'Côte-Nord' => 'Côte-Nord',
                                    'Estrie' => 'Estrie',
                                    'Gaspésie–Îles-de-la-Madeleine' => 'Gaspésie–Îles-de-la-Madeleine',
                                    'Lanaudière' => 'Lanaudière',
                                    'Laurentides' => 'Laurentides',
                                    'Laval' => 'Laval',
                                    'Mauricie' => 'Mauricie',
                                    'Montérégie' => 'Montérégie',
                                    'Montréal' => 'Montréal',
                                    'Nord-du-Québec' => 'Nord-du-Québec',
                                    'Outaouais' => 'Outaouais',
                                    'Saguenay–Lac-Saint-Jean' => 'Saguenay–Lac-Saint-Jean'
                                ];
                                $selected_region = get_chalet_field_value('region', '');
                                foreach ($regions as $value => $label) {
                                    printf(
                                        '<option value="%s"%s>%s</option>',
                                        esc_attr($value),
                                        selected($selected_region, $value, false),
                                        esc_html($label)
                                    );
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="map-details">
                <button class="details-btn">Place Pin with Address</button>
            </div>
            <br>
            <img src="<?= get_template_directory_uri() ?>/assets/images/map.png" style="max-width:100%;" />
        </div>
        <div class="container">
            <button class="details-btn main-btn">Save</button>
            <button class="details-btn sec-btn" data-id="calendar-tab" onclick="showContent(event, this)">Next</button>
            <button class="next-tab-link" data-id="calendar-tab" onclick="showContent(event, this)">Go to
                Calendar settings.</button>
        </div>
    </div>
    <div class="tab-content" id="calendar-tab" style="display: none;">
        <div class="container">
            <div id="calendar"></div>
        </div>
        <div class="container">
            <button class="details-btn main-btn">Save</button>
            <button class="details-btn sec-btn" data-id="information-tab"
                onclick="showContent(event, this)">Next</button>
            <button class="next-tab-link" data-id="information-tab" onclick="showContent(event, this)">back to
                start</button>
        </div>
    </div>
    <!-- Submit Button (outside tab content) -->
    <p style="margin-top: 20px;">
        <!-- <input type="submit" name="submit_chalet"
            value="<?php echo $edit_mode ? __('Update Chalet', 'chaletv2') : __('Add Chalet', 'chaletv2'); ?>"
            class="button button-primary"> -->
    </p>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        return;
        const calendarEl = document.getElementById('calendar');
        const today = new Date().toISOString().split('T')[0];

        // Disabled date ranges
        const disabledRanges = [
            { start: '2025-06-10', end: '2025-06-15' },
            { start: '2025-07-01', end: '2025-07-05' }
        ];

        // Past date blocking
        const blockPastDates = {
            start: '1900-01-01',
            end: today,
            display: 'background',
            color: '#cccccc60'
        };

        // Booked events (from PHP)
        const bookedEvents = [
            <?php
            foreach ($bookings as $booking) {
                $checkin = carbon_get_post_meta($booking->ID, 'booking_checkin');
                $checkout = carbon_get_post_meta($booking->ID, 'booking_checkout');
                $chalet_id = @carbon_get_post_meta($booking->ID, 'booking_chalet')[0]['id'] ?? null;
                $title = esc_js(get_the_title($booking->ID));
                $color = get_random_color();

                if ($checkin && $checkout && $chalet_id) {
                    echo "{";
                    echo "title: '{$title}',";
                    echo "start: '{$checkin}',";
                    echo "end: '{$checkout}',";
                    echo "color: '{$color}',";
                    echo "resourceId: '{$chalet_id}'";
                    echo "},";
                }
            }
            ?>
        ];

        // All events merged
        const events = [
            ...disabledRanges.map(range => ({
                start: range.start,
                end: range.end,
                display: 'background',
                color: '#ff000040'
            })),
            blockPastDates,
            ...bookedEvents
        ];

        // ==== PHP: Generate RESOURCES (chalets) ====
        const resources = [
            <?php
            $chalets = get_my_chalets();
            foreach ($chalets as $chalet) {
                $title = esc_js(get_the_title($chalet->ID));
                echo "{ id: '{$chalet->ID}', title: '{$title}' },";
            }
            ?>
        ];

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'resourceTimelineMonth',
            selectable: true,
            editable: false,
            height: 600,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'resourceTimelineMonth,resourceTimelineWeek,resourceTimelineDay,dayGridMonth,timeGridWeek,listWeek'
            },

            views: {
                resourceTimelineDay: {
                    buttonText: 'Timeline Day'
                },
                resourceTimelineWeek: {
                    buttonText: 'Timeline Week'
                },
                resourceTimelineMonth: {
                    buttonText: 'Timeline Month'
                },
                dayGridMonth: {
                    buttonText: 'Month'
                },
                timeGridWeek: {
                    buttonText: 'Week'
                },
                listWeek: {
                    buttonText: 'List'
                }
            },

            navLinks: true,
            nowIndicator: true,
            events: events,
            resources: resources,

            // dateClick: function (info) {
            //     const clicked = info.dateStr;

            //     const isPast = clicked < today;
            //     const isInDisabledRange = disabledRanges.some(range =>
            //         clicked >= range.start && clicked <= range.end
            //     );

            //     const booked = bookedEvents.find(event =>
            //         clicked >= event.start && clicked < event.end
            //     );

            //     if (isPast || isInDisabledRange || booked) {
            //         if (booked) {
            //             alert(`Already Booked by ${booked.title}`);
            //         } else {
            //             alert('You cannot book this date.');
            //         }
            //         return;
            //     }

            //     alert(`You clicked: ${clicked}`);
            // },
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives'
        });

        calendar.render();
    });
</script>
<script>
    const dropArea = document.getElementById('drop-area');
    const fileInput = document.getElementById('chalet-image-input');
    const previewWrapper = document.getElementById('image-preview-wrapper');
    const selectBtn = document.getElementById('select-images-btn');

    // Trigger file input
    selectBtn.addEventListener('click', function (e) {
        e.preventDefault();
        fileInput.click();
    });

    // Handle file input change
    fileInput.addEventListener('change', function (e) {
        handleFiles(e.target.files);
    });

    // Prevent default behavior for drag events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, e => e.preventDefault(), false);
        dropArea.addEventListener(eventName, e => e.stopPropagation(), false);
    });

    // Visual feedback
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
    });

    // Handle dropped files
    dropArea.addEventListener('drop', e => {
        const dt = e.dataTransfer;
        const files = dt.files;

        // Bind dropped files to actual input (makes them appear in $_FILES)
        const dataTransfer = new DataTransfer(); // for compatibility and creating fresh FileList
        Array.from(files).forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;

        handleFiles(files);
    });

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                const html = `
                <div class="img-detail">
                    <div class="top-detail">
                        <img src="<?= get_template_directory_uri() ?>/dashboard/images/star.png" alt="" class="star-icon">
                    </div>
                    <img src="${e.target.result}" alt="" class="bedroom-img">
                </div>`;
                previewWrapper.insertAdjacentHTML('beforeend', html);
            };
            reader.readAsDataURL(file);
        });
    }
    function addSeasonalRate(button){
        const container = document.querySelector('.seasonal_rates_container');
        if (!container) return;

        // Clone the container
        const clone = container.cloneNode(true);

        // Find all input, select, and textarea elements and clear their values
        clone.querySelectorAll('input, select, textarea').forEach(el => {
            if (el.type === 'checkbox' || el.type === 'radio') {
                el.checked = false;
            } else if (el.tagName === 'SELECT') {
                el.selectedIndex = 0;
            } else {
                el.value = '';
            }
        });
        clone.style.display = 'block'; // Ensure the clone is visible

        // Optionally, update the name attributes to use a new index
        // Find the highest existing index
        let maxIndex = 0;
        document.querySelectorAll('.seasonal_rates_container input[name^="seasonal_rates["]').forEach(input => {
            const match = input.name.match(/seasonal_rates\[(\d+)\]/);
            if (match && parseInt(match[1]) > maxIndex) maxIndex = parseInt(match[1]);
        });
        const newIndex = maxIndex + 1;

        // Update all name attributes in the clone
        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/seasonal_rates\[\d+\]/, `seasonal_rates[${newIndex}]`);
        });

        // Remove any .period-labels in the clone (if you want to start fresh)
        clone.querySelectorAll('.period-labels').forEach(el => el.innerHTML = '');

        // Insert the clone after the original container
        container.after(clone);
    }
    function remove_season(button){
        const container = button.closest('.seasonal_rates_container');
        if (container) {
            container.remove();
        }
    }
</script>

<?php get_footer('dashboard') ?>