<?php 
/**
 * Template Name: Edit Chalet Dashboard
 * Description: A custom page template for editing chalet dashboard.
 *
 * @package WordPress
 * @subpackage Your_Theme_Name
 * @since Your_Theme_Version
 */
get_header('dashboard');

// Ensure $chalet_data and $edit_mode are available
// These variables should be passed from page-chalet-dashboard.php
$chalet_id = $edit_mode = isset($_GET['id']) ? intval($_GET['id']) : 0;
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
<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/assets/css/edit-style.css?v=<?= filemtime(get_template_directory() . '/assets/css/edit-style.css') ?>" />
<!-- <link rel="stylesheet" href="<?= get_template_directory_uri() ?>/assets/css/edit-style.css"> -->
<form id="chalet-dashboard-form" method="post" action="<?= admin_url('admin-post.php') ?>" enctype="multipart/form-data">
    <?php wp_nonce_field('chalet_dashboard_nonce', 'chalet_dashboard_nonce_field'); ?>
    <input type="hidden" name="action" value="chalet_dashboard_save">
    <input type="hidden" name="form_action" value="<?php echo $edit_mode ? 'edit_chalet' : 'add_chalet'; ?>">
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
                            value="<?php echo $chalet_data['title']; ?>"
                            required>
                    </div>
                </div>
            </div>
            <div class="sm-divider"></div>
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
                    <span  class="light-text">Description</span>
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
                        bed
                        type per room.
                        Chalet Name
                        Chalet Description
                        Guest No (mandatory)
                        Number of bedrooms</p>
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
                    <div class=" input-details double-col"
                        style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">

                        <div class="form-detail">
                            <label><?php _e('Bedroom name:', 'chaletv2'); ?></label>
                            <input type="text" name="bedrooms[<?php echo $index; ?>][bedroom_type]"
                                value="<?php echo esc_attr($bedroom_type); ?>"
                                placeholder="e.g., Double bed, Single bed">
                        </div>
                        <div class="form-detail">
                            <label><?php _e('Number of gest:', 'chaletv2'); ?></label>
                            <input type="number" name="bedrooms[<?php echo $index; ?>][num_beds]"
                                value="<?php echo esc_attr($num_beds); ?>" min="1">
                        </div>
                        <div class="blank-col"></div>
                        <div class="form-detail">
                            <label><?php _e('Number of Beds:', 'chaletv2'); ?></label>
                            <input type="number" name="bedrooms[<?php echo $index; ?>][num_beds]"
                                value="<?php echo esc_attr($num_beds); ?>" min="1">
                        </div>
                        <div class="form-detail">
                            <label><?php _e('Bedroom Type:', 'chaletv2'); ?></label>
                            <input type="text" name="bedrooms[<?php echo $index; ?>][bedroom_type]"
                                value="<?php echo esc_attr($bedroom_type); ?>"
                                placeholder="e.g., Double bed, Single bed">
                        </div>
                        <div class="form-detail btn-detail">
                            <label class="light-text bold-text"></label>
                            <button type="button"
                                class="remove-bedroom-button details-btn button"><?php _e('Remove Bedroom', 'chaletv2'); ?></button>
                        </div>
                    </div>
                    <?php } // End foreach loop ?>
                </div>
                <!-- <div class="input-details double-col">
                <p class="light-text">Here you can add your room details including number of guests, beds and
                    bed
                    type per room.
                    Chalet Name
                    Chalet Description
                    Guest No (mandatory)
                    Number of bedrooms</p>
                <div class="form-detail">
                    <label class="light-text bold-text">Bedrooms name</label>
                    <select class="details-select">
                        <option>26</option>
                        <option>27</option>
                        <option>28</option>
                        <option>29</option>
                    </select>
                </div>
                <div class="form-detail">

                    <label class="light-text bold-text"><?php _e('Number of Beds:', 'chaletv2'); ?></label>
                    <select class="details-select">
                        <option>26</option>
                        <option>27</option>
                        <option>28</option>
                        <option>29</option>
                    </select>
                </div>
               
                <div class="form-detail">
                    <label class="light-text bold-text"><?php _e('Bedroom Type:', 'chaletv2'); ?></label>
                    <select class="details-select">
                        <option>26</option>
                        <option>27</option>
                        <option>28</option>
                        <option>29</option>
                    </select>
                </div>
                <div class="form-detail">

                    <label class="light-text bold-text">Bedrooms</label>
                    <select class="details-select">
                        <option>26</option>
                        <option>27</option>
                        <option>28</option>
                        <option>29</option>
                    </select>
                </div>
                <div class="form-detail btn-detail">
                    <label class="light-text bold-text"></label>
                    <button class="details-btn">Delete room</button>
                </div>
            </div> -->
            </div>
        </div>
        <div class="container">
            <button class="details-btn main-btn">Save</button>
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
                            for="default_rate_week"><?php _e('Week Price per Night (Sun-Thu) (€):', 'chaletv2'); ?></label>

                        <input type="number" id="default_rate_week" name="default_rate_week"
                            value="<?php echo get_chalet_field_value('default_rate_week'); ?>" min="0" step="any">
                    </div>

                    <div class="form-group">
                        <label
                            for="default_rate_week"><?php _e('Week Price per Night (Sun-Thu) (€):', 'chaletv2'); ?></label>
                        <input type="number" id="default_rate_week" name="default_rate_week"
                            value="<?php echo get_chalet_field_value('default_rate_week'); ?>" min="0" step="any">
                    </div>
                </div>
            </div>

            <div class="listing-wrap">
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
            </div>
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
                            <input type="checkbox" name="free_for_babies" value="yes"
                                <?php checked(is_chalet_field_checked('free_for_babies', 'yes')); ?>>
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
                        <label><?php _e('Upload Document', 'chaletv2'); ?></label>
                        <input type="file" id="citq_document" name="citq_document" disabled />
                        <label for="citq_docum" class="upload-label">
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
                        <input type="number" value="200">
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
                        <div class="inner-form-row" style="opacity: 0.7;">
                            <input type="number" value="200">
                            <select id="fruit" name="fruit">
                                <option value="apple">Apple</option>
                                <option value="banana">Banana</option>
                                <option value="orange">Orange</option>
                                <option value="grape">Grape</option>
                            </select>
                            <button>Add Extra</button>
                        </div>
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
                            <input type="time" id="checkin_time" name="checkin_time"
                                value="<?php echo get_chalet_field_value('checkin_time', '14:00'); ?>">
                            <!-- <select id="fruit" name="fruit">
                                <option value="apple">Apple</option>
                                <option value="banana">Banana</option>
                                <option value="orange">Orange</option>
                                <option value="grape">Grape</option>
                            </select> -->
                            <button>Add Extra</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-container container-style price-periods">
            <h2><?php _e('Seasonal Rates', 'chaletv2'); ?></h2>
            <p><?php _e('Complex field - Coming Soon.', 'chaletv2'); ?></p>
            <div class="spcaeer-xs"></div>
            <div class="period-row">
                <div class="form-group">
                    <label>Period name </label>
                    <input type="number" value="750">
                </div>
                <div class="form-group">
                    <label>Period dates
                    </label>
                    <input type="number" value="750">
                </div>
            </div>
            <div class="period-labels">
                <div class="date-label"> <span> 20 May 2025 - 4 June 20205 </span> <button>x</button></div>
                <div class="date-label"> <span> 20 May 2025 - 4 June 20205 </span> <button>x</button></div>
                <div class="date-label"> <span> 20 May 2025 - 4 June 20205 </span> <button>x</button></div>
                <div class="date-label"> <span> 20 May 2025 - 4 June 20205 </span> <button>x</button></div>
            </div>
            <div class="divider-xl"></div>
            <h2>Price per day of the week</h2>
            <p>Set a different night rate here depending on the day of the week</p>
            <div class="spcaeer-xs"></div>
            <div class="period-row days-veiw">
                <div class="form-group">
                    <label>MON</label>
                    <input type="number" value="800$">
                </div>
                <div class="form-group">
                    <label>TUE</label>
                    <input type="number" value="800$">
                </div>
                <div class="form-group">
                    <label>WED</label>
                    <input type="number" value="800$">
                </div>
                <div class="form-group">
                    <label>Thurs </label>
                    <input type="number" value="800$">
                </div>
                <div class="form-group">
                    <label>fri</label>
                    <input type="number" value="800$">
                </div>
                <div class="form-group">
                    <label>sat</label>
                    <input type="number" value="800$">
                </div>
                <div class="form-group">
                    <label>sun </label>
                    <input type="number" value="800$">
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
                    <input type="number" value="800$">
                </div>
                <div class="form-group">
                    <label>Children</label>
                    <input type="number" value="800$">
                </div>
                <div class="form-group">
                    <label>Babies</label>
                    <input type="number" value="800$">
                </div>
                <div class="form-group dvd-label">
                    <label>after </label>
                </div>
                <div class="form-group">
                    <label>...</label>
                    <select name="fruits">
                        <option value="apple">Apple</option>
                        <option value="banana">Banana</option>
                        <option value="orange">Orange</option>
                    </select>
                </div>
            </div>
            <div class="divider-xl"></div>
            <div class="form-group mim-night">
                <label>Minimum length of stay</label>
                <input type="number" value="800$">
            </div>
            <div class="spcaeer-xs"></div>
            <h2>Arrival/departure days unavailable
            </h2>
            <p>Determine which days of the week are not available for arrival and departure</p>


            <div class=" checkin-days-wrap">
                <div class="labels-wrap">
                    <p>Check-in</p>
                    <p>Check-out</p>
                </div>
                <div class="checkin-check">
                    <div class="cc-col">
                        <label for="mon">mon</label>
                        <input type="checkbox" name="mon">
                        <input type="checkbox" name="mon">
                    </div>
                    <div class="cc-col">
                        <label for="tue">tue</label>
                        <input type="checkbox" name="tue">
                        <input type="checkbox" name="tue">
                    </div>
                    <div class="cc-col">
                        <label for="wed">wed</label>
                        <input type="checkbox" name="wed">
                        <input type="checkbox" name="wed">
                    </div>
                    <div class="cc-col">
                        <label for="Thurs">Thurs</label>
                        <input type="checkbox" name="Thurs">
                        <input type="checkbox" name="Thurs">
                    </div>
                    <div class="cc-col">
                        <label for="fri">fri</label>
                        <input type="checkbox" name="fri">
                        <input type="checkbox" name="fri">
                    </div>
                    <div class="cc-col">
                        <label for="fri">fri</label>
                        <input type="checkbox" name="fri">
                        <input type="checkbox" name="fri">
                    </div>
                    <div class="cc-col">
                        <label for="sat">sat</label>
                        <input type="checkbox" name="sat">
                        <input type="checkbox" name="sat">
                    </div>
                    <div class="cc-col">
                        <label for="Sun">Sun</label>
                        <input type="checkbox" name="Sun">
                        <input type="checkbox" name="Sun">
                    </div>

                </div>
            </div>
            <h2>Arrival/departure days unavailable
            </h2>
            <p>Determine which days of the week are not available for arrival and departure</p>


            <div class=" checkin-days-wrap">
                <div class="labels-wrap">
                    <p>Check-in</p>
                    <p>Check-out</p>
                </div>
                <div class="checkin-check">
                    <div class="cc-col">
                        <label for="mon">mon</label>
                        <input type="checkbox" name="mon">
                        <input type="checkbox" name="mon">
                    </div>
                    <div class="cc-col">
                        <label for="tue">tue</label>
                        <input type="checkbox" name="tue">
                        <input type="checkbox" name="tue">
                    </div>
                    <div class="cc-col">
                        <label for="wed">wed</label>
                        <input type="checkbox" name="wed">
                        <input type="checkbox" name="wed">
                    </div>
                    <div class="cc-col">
                        <label for="Thurs">Thurs</label>
                        <input type="checkbox" name="Thurs">
                        <input type="checkbox" name="Thurs">
                    </div>
                    <div class="cc-col">
                        <label for="fri">fri</label>
                        <input type="checkbox" name="fri">
                        <input type="checkbox" name="fri">
                    </div>
                    <div class="cc-col">
                        <label for="fri">fri</label>
                        <input type="checkbox" name="fri">
                        <input type="checkbox" name="fri">
                    </div>
                    <div class="cc-col">
                        <label for="sat">sat</label>
                        <input type="checkbox" name="sat">
                        <input type="checkbox" name="sat">
                    </div>
                    <div class="cc-col">
                        <label for="Sun">Sun</label>
                        <input type="checkbox" name="Sun">
                        <input type="checkbox" name="Sun">
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <button class="details-btn main-btn">Save</button>
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
                            <label><input type="radio" name="reservation_policy" value="policy_50_50_3"
                                    <?php checked(get_chalet_field_value('reservation_policy'), 'policy_50_50_3'); ?>>
                                <?php _e('Policy 50-50 (3 days before stay)', 'chaletv2'); ?></label>
                            <label><input type="radio" name="reservation_policy" value="policy_50_50_14"
                                    <?php checked(get_chalet_field_value('reservation_policy'), 'policy_50_50_14'); ?>>
                                <?php _e('Policy 50-50 (14 days before stay)', 'chaletv2'); ?></label>
                            <label><input type="radio" name="reservation_policy" value="policy_25_25_50_14"
                                    <?php checked(get_chalet_field_value('reservation_policy'), 'policy_25_25_50_14'); ?>>
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
                            <label><input type="radio" name="cancellation_policy" value="flexible"
                                    <?php checked($chalet_data['cancellation_policy'] ?? '', 'flexible'); ?>>
                                <?php _e('Flexible', 'chaletv2'); ?></label>
                            <label><input type="radio" name="cancellation_policy" value="moderate"
                                    <?php checked($chalet_data['cancellation_policy'] ?? '', 'moderate'); ?>>
                                <?php _e('Moderate', 'chaletv2'); ?></label>
                            <label><input type="radio" name="cancellation_policy" value="strict"
                                    <?php checked($chalet_data['cancellation_policy'] ?? '', 'strict'); ?>>
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
            <div class="container">
                <div class="left-details">
                    <span class="light-text bold-text">Cancellation Policies</span>
                    <p class="light-text">Assign a cancellation policy for your chalet from our pre-established list
                    </p>
                    <div class="sm-divider"></div>
                    <div class="policy-detail-wrapper">
                        <div class="icon-wrapper">
                            <img src="./images/edit-icon.png" alt="">
                        </div>
                        <textarea class="details-textarea"></textarea>
                    </div>
                </div>
                <div class="right-details">
                    <h2>Assign a policy</h2>
                    <span class="light-text">Assign a cancellation policy to this chalet</span>
                    <p class="light-text">*If you change the cancellation policy, it will apply to new
                        bookings and not to bookings already made.</p>
                    <div class="policy-details">
                        <input type="radio" name="policy" value="flexible" class="radio-input">
                        <div class="policy-detail">
                            <h3 class="light-text bold-text">Flexible Policy</h3>
                            <p class="light-text">100% of payments are refundable if cancelled 30 days before
                                arrival or
                                earlier.
                                50% refundable if cancelled between 14 and 30 days before arrival date.
                                0% refundable if cancelled less than 14 days before arrival date.
                            </p>
                        </div>
                    </div>
                    <div class="policy-details">
                        <input type="radio" name="policy" value="flexible" class="radio-input">
                        <div class="policy-detail">
                            <h3 class="light-text bold-text">Moderate Policy</h3>
                            <p class="light-text">100% of payments are refundable if cancelled 90 days before
                                arrival or
                                earlier.
                                50% refundable if cancelled between 30 and 90 days before arrival date.
                                0% refundable if cancelled less than 30 days before arrival date.

                            </p>
                        </div>
                    </div>
                    <div class="policy-details">
                        <input type="radio" name="policy" value="flexible" class="radio-input">
                        <div class="policy-detail">
                            <h3 class="light-text bold-text">Strict Policy</h3>
                            <p class="light-text">All prepaid payments are non-refundable.
                                Payments received can be used for another rental at the same chalet if the
                                cancellation is made more than 90 days from the arrival date.
                                No refund or payment usable for another reservation if cancellation is made less
                                than 90 days from the arrival date.

                            </p>
                        </div>
                    </div>
                    <div class="btn-row">
                        <button class="details-btn main-condition-btn">Cancel</button>
                        <button class="details-btn">Save</button>
                    </div>

                </div>
            </div>
        </div>
        <div class="container">
            <button class="details-btn main-btn">Save</button>
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
                                <img src="./images/edit-icon.png" alt="">
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
                                <img src="./images/edit-icon.png" alt="">
                            </div> -->
                            <textarea name="itinerary_instructions" class="details-textarea" id="itinerary_instructions"
                                rows="5"><?php echo esc_textarea($chalet_data['itinerary_instructions'] ?? ''); ?></textarea>
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
                                <img src="./images/edit-icon.png" alt="">
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
                            <input type="number" name="itinerary_instructions_days" id="itinerary_instructions_days"
                                min="0"
                                value="<?php echo esc_attr($chalet_data['itinerary_instructions_days'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="">
                        <!-- <h3 class="light-text bold-text">Reservation Policies</h3> -->
                        <label class="light-text"
                            for="itinerary_instructions"><?php _e('How many days before check-in to send rules reminder.', 'chaletv2'); ?>
                        </label>
                        <div class="policy-detail-wrapper">
                            <!-- <div class="icon-wrapper">
                                <img src="./images/edit-icon.png" alt="">
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
                                <img src="./images/edit-icon.png" alt="">
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
            <button class="next-tab-link" data-id="media-tab" onclick="showContent(event, this)">Go to instructions
                settings.</button>
        </div>
    </div>
    <div class="tab-content" id="media-tab" style="display: none;">
        <div class="images-section">
            <div class="container">
                <h2 class="title-xl">Images</h2>
                <div class="images-wrapper-row">
                    <div class="img-detail">
                        <div class="top-detail">
                            <img src="./images/star.png" alt="" class="star-icon">
                        </div>
                        <img src="./images/bedroom.jpg" alt="" class="bedroom-img">
                    </div>
                    <div class="img-detail">
                        <div class="top-detail">
                            <img src="./images/star.png" alt="" class="star-icon">
                        </div>
                        <img src="./images/bedroom.jpg" alt="" class="bedroom-img">
                    </div>
                    <div class="img-detail">
                        <div class="top-detail">
                            <img src="./images/star.png" alt="" class="star-icon">
                        </div>
                        <img src="./images/bedroom.jpg" alt="" class="bedroom-img">
                    </div>
                    <div class="img-detail">
                        <div class="top-detail">
                            <img src="./images/star.png" alt="" class="star-icon">
                        </div>
                        <img src="./images/bedroom.jpg" alt="" class="bedroom-img">
                    </div>
                    <div class="img-detail">
                        <div class="top-detail">
                            <img src="./images/star.png" alt="" class="star-icon">
                        </div>
                        <img src="./images/bedroom.jpg" alt="" class="bedroom-img">
                    </div>
                    <div class="img-detail">
                        <div class="top-detail">
                            <img src="./images/star.png" alt="" class="star-icon">
                        </div>
                        <img src="./images/bedroom.jpg" alt="" class="bedroom-img">
                    </div>
                    <div class="img-detail">
                        <div class="top-detail">
                            <img src="./images/star.png" alt="" class="star-icon">
                        </div>
                        <img src="./images/bedroom.jpg" alt="" class="bedroom-img">
                    </div>
                    <div class="img-detail">
                        <div class="top-detail">
                            <img src="./images/star.png" alt="" class="star-icon">
                        </div>
                        <img src="./images/bedroom.jpg" alt="" class="bedroom-img">
                    </div>
                    <div class="img-detail">
                        <div class="top-detail">
                            <img src="./images/star.png" alt="" class="star-icon">
                        </div>
                        <img src="./images/bedroom.jpg" alt="" class="bedroom-img">
                    </div>
                </div>
                <div class="sm-divider"></div>
                <div class="btn-details-row">
                    <div class="images-btn-details">
                        <span class="light-text">Drag and Drop images or</span>
                        <button class="details-btn">Select Media</button>

                    </div>
                </div>
                <span class="light-text"> * Click on the ‘star’ on the image to select featured</span>
                <span class="light-text"> **Change images order with Drag & Drop.</span>
                <div class="sm-divider"></div>
                <h2>Video</h2>
                <input type="text" class="big-input sm-input"
                    placeholder="Add a video link for your chalet from Youtube or other video platform">
            </div>
        </div>
        <div class="container">
            <button class="details-btn main-btn">Save</button>
            <button class="next-tab-link" data-id="amenities-tab" onclick="showContent(event, this)">Go to
                Amenities settings.</button>
        </div>
    </div>
    <!-- <div class="tab-content" id="amenities-tab" style="display: none;">
        <div class="container">
            <h1> amenities comming soon</h1>
        </div>
        <div class="container">
            <button class="details-btn main-btn">Save</button>
            <button class="next-tab-link" data-id="location-tab" onclick="showContent(event, this)">Go to
                Location settings.</button>
        </div>
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

<?php get_footer('dashboard') ?>