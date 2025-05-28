<?php

// namespace ChaletV2\CustomStructures;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Register Chalet Custom Post Type
 */
function register_chalet_cpt(): void
{
    $labels = [
        'name' => 'Chalets',
        'singular_name' => 'Chalet',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Chalet',
        'edit_item' => 'Edit Chalet',
        'new_item' => 'New Chalet',
        'view_item' => 'View Chalet',
        'search_items' => 'Search Chalets',
        'not_found' => 'No Chalets found',
        'not_found_in_trash' => 'No Chalets found in Trash',
        'all_items' => 'All Chalets',
        'menu_name' => 'Chalets',
        'name_admin_bar' => 'Chalet'
    ];

    $args = [
        'label' => 'Chalet',
        'labels' => $labels,
        'public' => true,
        'show_in_menu' => true,
        'supports' => ['title', 'thumbnail', 'excerpt', 'author', 'custom-fields', 'comments'],
        'has_archive' => true,
        'comment_status' => 'open',
        'comments' => true,
        'rewrite' => ['slug' => 'chalets'],
        'menu_icon' => 'dashicons-admin-home',
        'show_in_rest' => true,
    ];

    register_post_type('chalet', $args);
}

add_action('init', __NAMESPACE__ . '\register_chalet_cpt');

// Add rating field
function chalet_add_rating_field()
{
    if (get_post_type() === 'chalet') {
        echo '<p><label for="rating">Rating: </label>';
        echo '<select name="rating" id="rating" required>';
        echo '<option value="">Rate this chalet</option>';
        for ($i = 5; $i >= 1; $i--) {
            echo "<option value=\"$i\">$i Stars</option>";
        }
        echo '</select></p>';
    }
}
add_action('comment_form_logged_in_after', 'chalet_add_rating_field');
add_action('comment_form_after_fields', 'chalet_add_rating_field');

// Save rating as comment meta
function chalet_save_rating_meta($comment_id)
{
    if (isset($_POST['rating']) && get_post_type($_POST['comment_post_ID']) === 'chalet') {
        $rating = intval($_POST['rating']);
        if ($rating >= 1 && $rating <= 5) {
            add_comment_meta($comment_id, 'rating', $rating);
        }
    }
}
add_action('comment_post', 'chalet_save_rating_meta');
function chalet_review_callback($comment, $args, $depth)
{
    $rating = get_comment_meta($comment->comment_ID, 'rating', true);
    ?>
    <div class="chalet-review" id="comment-<?php comment_ID(); ?>">
        <p><strong><?php comment_author(); ?></strong> —
            <?php if ($rating): ?>
                <span>
                    <?php
                    $filled = intval($rating);
                    $empty = 5 - $filled;

                    echo str_repeat('<i class="fas fa-star" style="color: #f5c518;"></i>', $filled);
                    echo str_repeat('<i class="far fa-star" style="color: #ccc;"></i>', $empty);
                    ?>
                    (<?php echo $rating; ?>/5)
                </span>
            <?php endif; ?>
        </p>

        <p><?php comment_text(); ?></p>
    </div>
    <hr>
    <?php
}
add_filter('wp_list_comments_args', function ($args) {
    if (get_post_type() === 'chalet') {
        $args['callback'] = 'chalet_review_callback';
    }
    return $args;
});
function default_comments_on_chalet($data, $postarr)
{
    if ($data['post_type'] == 'chalet' && $data['post_status'] == 'publish') {
        $data['comment_status'] = 'open';
    }
    return $data;
}
add_filter('wp_insert_post_data', 'default_comments_on_chalet', 10, 2);


/**
 * Register Chalet Category Taxonomy
 */
add_action('init', function (): void {
    register_taxonomy('chalet_category', ['chalet'], [
        'label' => 'Chalet Categories',
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'chalet-category'],
    ]);
});

/**
 * Register Chalet Meta Fields using Carbon Fields
 */
add_action('carbon_fields_register_fields', function (): void {
    Container::make('post_meta', 'Chalet Details')
        ->where('post_type', '=', 'chalet')
        ->add_tab('Information', [
            // Chalet Name uses the default Post Title
            // Field::make('text', 'chalet_title', 'Name of cottage')
            //     ->set_required(true),
            Field::make('rich_text', 'description', 'Description'),
            Field::make('text', 'affiliate_booking_link', 'Reservation link')
                ->set_help_text('Enter the booking link (URL)'),
            Field::make('checkbox', 'featured', 'Featured'),
            Field::make('text', 'monthly_rate', 'Monthly rate from')
                ->set_attribute('type', 'number')
                ->set_help_text('Enter the fare from 1 month'),
            Field::make('text', 'cleaning_fee', 'Household costs')
                ->set_attribute('type', 'number')
                ->set_help_text('Enter the housework fee'),
            Field::make('radio', 'cleaning_fee_type', 'Cleaning fee type')
                ->add_options([
                    'fixed' => 'Fixed Amount (€)',
                    'per_stay' => 'By stay',
                ])
                ->set_default_value('per_stay'),
            Field::make('text', 'guest_count', 'Guest Count')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 1),
            Field::make('text', 'baths', 'Baths')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0),
            Field::make('complex', 'bedrooms', 'Bedrooms')
                ->set_layout('tabbed-horizontal')
                ->add_fields([
                    Field::make('text', 'name', 'Bedroom Name/Number'),
                    Field::make('text', 'guests', 'Sleeps Guests')
                        ->set_attribute('type', 'number')
                        ->set_attribute('min', 1),
                    Field::make('text', 'beds', 'Number of Beds')
                        ->set_attribute('type', 'number')
                        ->set_attribute('min', 1),
                    Field::make('select', 'type', 'Bed Type(s)')
                        ->add_options([
                            'king' => 'King',
                            'queen' => 'Queen',
                            'double' => 'Double',
                            'twin' => 'Twin/Single',
                            'bunk' => 'Bunk Bed',
                            'sofa' => 'Sofa Bed',
                            'other' => 'Other',
                        ]),
                ])
                ->set_header_template('<%- name ? name : "Bedroom" %>'),
        ])
        ->add_tab('Price', [
            // Default Rates
            Field::make('separator', 'sep_default_rates', 'Default Rates'),
            Field::make('text', 'default_rate_weekend', 'Weekend Price per Night (Fri, Sat)')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'default_rate_weekday', 'Weekday Price per Night (Sun-Thu)')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'default_rate_week', 'Price per Week (7 days)')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),

            // Extra Guests
            Field::make('separator', 'sep_extra_guests', 'Extra Guests'),
            Field::make('text', 'guests_included', 'Guests Included in Base Rate')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0),
            Field::make('text', 'extra_price_adult', 'Extra Price per Adult/Night (18+)')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'extra_price_child', 'Extra Price per Child/Night (3-17)')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('checkbox', 'free_for_babies', 'Free for Babies (0-2)')
                ->set_option_value('yes')
                ->set_help_text('If checked, babies are free. If unchecked, child rates apply.'),

            // Minimum Nights
            Field::make('separator', 'sep_min_nights', 'Minimum Stay'),
            Field::make('text', 'min_nights', 'Minimum Nights of Booking')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 1)
                ->set_default_value(1),

            // Taxes & Documents
            Field::make('separator', 'sep_taxes', 'Taxes & CITQ'),
            Field::make('text', 'tax_gst', 'GST Tax Number'),
            Field::make('text', 'tax_thq', 'THQ Tax Number'), // Mandatory
            Field::make('text', 'tax_qst', 'QST Tax Number'),
            Field::make('file', 'citq_document', 'CITQ Document')
                ->set_value_type('url'), // Store URL

            // Security Deposit
            Field::make('separator', 'sep_deposit', 'Security Deposit'),
            Field::make('text', 'security_deposit', 'Security Deposit Amount')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),

            // Extra Options
            Field::make('separator', 'sep_extra_options', 'Extra Options'),
            Field::make('complex', 'extra_options', 'Extra Options')
                ->set_layout('tabbed-horizontal')
                ->add_fields([
                    Field::make('text', 'name', 'Option Name'),
                    Field::make('text', 'price', 'Price')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('select', 'type', 'Type of Fee')
                        ->add_options([
                            'per_stay' => 'Per Stay',
                            'per_item' => 'Per Item',
                        ]),
                ])
                ->set_header_template('<%- name ? name : "Option" %>'),

            // Checkin/Checkout Extras
            Field::make('separator', 'sep_checkin_checkout', 'Check-in / Check-out Times & Extras'),
            Field::make('time', 'checkin_time', 'Default Check-in Time')->set_storage_format('H:i'),
            Field::make('time', 'checkout_time', 'Default Check-out Time')->set_storage_format('H:i'),
            Field::make('complex', 'early_checkin_options', 'Early Check-in Options')
                ->set_layout('tabbed-horizontal')
                ->add_fields([
                    Field::make('time', 'time', 'Early Check-in Time')->set_storage_format('H:i')->set_required(true),
                    Field::make('text', 'price', 'Price')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                ])
                ->set_header_template('<%- time ? "Early Check-in: " + time : "Option" %>'),
            Field::make('complex', 'late_checkout_options', 'Late Check-out Options')
                ->set_layout('tabbed-horizontal')
                ->add_fields([
                    Field::make('time', 'time', 'Late Check-out Time')->set_storage_format('H:i')->set_required(true),
                    Field::make('text', 'price', 'Price')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01')
                    // ->set_required(true),
                ])
                ->set_header_template('<%- time ? "Late Check-out: " + time : "Option" %>'),

            // Seasonal Rates
            Field::make('separator', 'sep_seasonal_rates', 'Seasonal Rates (Overrides Default Rates)'),
            Field::make('complex', 'seasonal_rates', 'Seasonal Rate Periods')
                ->set_layout('tabbed-vertical') // Vertical might be better for many fields
                ->add_fields([
                    Field::make('date', 'start_date', 'Start Date'),
                    Field::make('date', 'end_date', 'End Date'),
                    // Specific day pricing
                    Field::make('text', 'price_night', 'Default Price Per Night (Fallback)')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01')
                    // ->set_required(true)
                    ,
                    Field::make('text', 'price_saturday', 'Price for Saturday')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('text', 'price_sunday', 'Price for Sunday')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('text', 'price_monday', 'Price for Monday')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('text', 'price_tuesday', 'Price for Tuesday')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('text', 'price_wednesday', 'Price for Wednesday')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('text', 'price_thursday', 'Price for Thursday')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('text', 'price_friday', 'Price for Friday')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    // Seasonal Extra Guests
                    Field::make('separator', 'sep_seasonal_extra', 'Fee for Additional Guests (Seasonal)'),
                    Field::make('text', 'charge_after_guests', 'Charge After # Guests')
                        ->set_attribute('type', 'number')
                        ->set_attribute('min', 0)
                        // ->set_required(true)
                        ->set_help_text('These charges apply after exceeding this number.'),
                    Field::make('text', 'extra_adult', 'Extra Price/Adult/Night (18+)')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('text', 'extra_child', 'Extra Price/Child/Night (3-17)')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('text', 'extra_baby', 'Extra Price/Baby/Night (0-2)')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    // Seasonal Restrictions
                    Field::make('text', 'min_stay', 'Minimum Length of Stay (nights)')
                        ->set_attribute('type', 'number')
                        ->set_attribute('min', 1),
                    Field::make('set', 'checkin_unavailable', 'Check-in Unavailable Days')
                        ->add_options(['saturday' => 'Sat', 'sunday' => 'Sun', 'monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri']),
                    Field::make('set', 'checkout_unavailable', 'Check-out Unavailable Days')
                        ->add_options(['saturday' => 'Sat', 'sunday' => 'Sun', 'monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri']),
                    // Note: dev_notes mentions unavailable early check-in/late check-out days, but maybe handle this via booking logic instead of specific fields?
                    // Field::make('set', 'early_checkin_unavailable', 'Early Check-in Unavailable Days') ...
                    // Field::make('set', 'late_checkout_unavailable', 'Late Check-out Unavailable Days') ...

                ])
                ->set_header_template('<%- start_date && end_date ? start_date + " - " + end_date : "Seasonal Period" %>'),
        ])
        ->add_tab('Terms', [
            Field::make('radio', 'reservation_policy', 'Reservation Policy')
                ->add_options([
                    'policy_50_50_3' => 'Policy 50-50 (3 days before stay)',
                    'policy_50_50_14' => 'Policy 50-50 (14 days before stay)',
                    'policy_25_25_50_14' => 'Policy 25-25-50 (14 days before stay)',
                    // Add more policies as needed
                ]),
            Field::make('radio', 'cancellation_policy', 'Cancellation Policy')
                ->add_options([
                    'flexible' => 'Flexible',
                    'moderate' => 'Moderate',
                    'strict' => 'Strict',
                    // Add more policies as needed
                ]),
            Field::make('text', 'preparation_time', 'Preparation Time (Nights Blocked Before Stay)')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0)
                ->set_help_text('Number of nights blocked before the start of the stay for preparation.'),
            Field::make('text', 'reservation_window', 'Reservation Window (Days in Advance)')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0)
                ->set_help_text('Maximum number of days in advance a user can book.'),
            Field::make('text', 'reservation_notice', 'Reservation Notice (Minimum Days Before Arrival)')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0)
                ->set_help_text('Minimum number of days notice required to accept a reservation.'),
            Field::make('rich_text', 'reservation_contract', 'Reservation Contract / Policies')
                ->set_help_text('Enter the full reservation contract terms and conditions.'),
        ])
        ->add_tab('Instructions', [
            Field::make('rich_text', 'checkin_instructions', 'Check-in Instructions'),
            Field::make('text', 'checkin_instructions_days', 'Send Check-in Instructions (Days Before)')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0)
                ->set_help_text('How many days before check-in to send instructions.'),
            Field::make('rich_text', 'checkout_instructions', 'Check-out Instructions'),
            Field::make('text', 'checkout_instructions_days', 'Send Check-out Instructions (Days Before)')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0)
                ->set_help_text('How many days before check-out to send instructions.'),
            Field::make('rich_text', 'itinerary_instructions', 'Itinerary Instructions'),
            Field::make('text', 'itinerary_instructions_days', 'Send Itinerary Instructions (Days Before)')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0)
                ->set_help_text('How many days before check-in to send itinerary.'),
            Field::make('rich_text', 'rules_reminder', 'Reminder of Rules'),
            Field::make('text', 'rules_reminder_days', 'Send Rules Reminder (Days Before)')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0)
                ->set_help_text('How many days before check-in to send rules reminder.'),
            Field::make('rich_text', 'local_guide', 'Local Guide'),
            Field::make('text', 'local_guide_days', 'Send Local Guide (Days Before)')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0)
                ->set_help_text('How many days before check-in to send local guide.'),
            Field::make('text', 'emergency_contact', 'Emergency Contact Info')
                ->set_help_text('Phone number or contact details for emergencies.'),
        ])
        ->add_tab('Media', [
            Field::make('media_gallery', 'chalet_images', 'Chalet Images')
                ->set_type('image') // Changed from ['image'] to string to satisfy linter
            // ->set_required(true)
            ,
            Field::make('text', 'video_link', 'Video Link (YouTube/Vimeo)')
                ->set_help_text('Enter the URL of the video.'),
            // Consider Field::make('oembed', 'video', 'Video') for better integration
        ])
        ->add_tab('Amenities', [
            Field::make('association', 'indoor_features', 'Indoor Features')
                ->set_types([
                    [
                        'type' => 'post',
                        'post_type' => 'chalet_feature',
                        'query_args' => [
                            'meta_query' => [
                                [
                                    'key' => '_feature_type', // Note the underscore prefix
                                    'value' => 'indoor',
                                ]
                            ]
                        ]
                    ]
                ])
                ->set_max(999),
            Field::make('association', 'outdoor_features', 'Outdoor Features')
                ->set_types([
                    [
                        'type' => 'post',
                        'post_type' => 'chalet_feature',
                        'query_args' => [
                            'meta_query' => [
                                [
                                    'key' => '_feature_type',
                                    'value' => 'outdoor',
                                ]
                            ]
                        ]
                    ]
                ])
                ->set_max(999),
            Field::make('association', 'kitchen_features', 'Kitchen Features')
                ->set_types([
                    [
                        'type' => 'post',
                        'post_type' => 'chalet_feature',
                        'query_args' => [
                            'meta_query' => [
                                [
                                    'key' => '_feature_type',
                                    'value' => 'kitchen',
                                ]
                            ]
                        ]
                    ]
                ])
                ->set_max(999),
            Field::make('association', 'family_features', 'Family Features')
                ->set_types([
                    [
                        'type' => 'post',
                        'post_type' => 'chalet_feature',
                        'query_args' => [
                            'meta_query' => [
                                [
                                    'key' => '_feature_type',
                                    'value' => 'family',
                                ]
                            ]
                        ]
                    ]
                ])
                ->set_max(999),
            Field::make('association', 'sports_features', 'Sports Features')
                ->set_types([
                    [
                        'type' => 'post',
                        'post_type' => 'chalet_feature',
                        'query_args' => [
                            'meta_query' => [
                                [
                                    'key' => '_feature_type',
                                    'value' => 'sports',
                                ]
                            ]
                        ]
                    ]
                ])
                ->set_max(999),
            Field::make('association', 'services_features', 'Services')
                ->set_types([
                    [
                        'type' => 'post',
                        'post_type' => 'chalet_feature',
                        'query_args' => [
                            'meta_query' => [
                                [
                                    'key' => '_feature_type',
                                    'value' => 'services',
                                ]
                            ]
                        ]
                    ]
                ])
                ->set_max(999),
            Field::make('association', 'accessibility_features', 'Accessibility Features')
                ->set_types([
                    [
                        'type' => 'post',
                        'post_type' => 'chalet_feature',
                        'query_args' => [
                            'meta_query' => [
                                [
                                    'key' => '_feature_type',
                                    'value' => 'accessibility',
                                ]
                            ]
                        ]
                    ]
                ])
                ->set_max(999),
            Field::make('association', 'events_features', 'Suitable for Events')
                ->set_types([
                    [
                        'type' => 'post',
                        'post_type' => 'chalet_feature',
                        'query_args' => [
                            'meta_query' => [
                                [
                                    'key' => '_feature_type',
                                    'value' => 'events',
                                ]
                            ]
                        ]
                    ]
                ])
                ->set_max(999),
        ])
        ->add_tab('Location', [
            Field::make('association', 'region', 'Region')
                ->set_types([
                    [
                        'type' => 'post',
                        'post_type' => 'region',
                    ],
                ])
                ->set_max(1)
                ->set_help_text('Select the region where this chalet is located'),
            Field::make('map', 'chalet_location', 'Chalet Location')
                ->set_position(46.8139, -71.2080, 10) // Default to Quebec City
                ->set_help_text('Place the pin accurately on the map. Address, Latitude, and Longitude will be saved.'),
            // Add separate fields for country/province/region if needed for easier filtering,
            // though the map field stores structured address data.
            Field::make('text', 'country', 'Country'),
            Field::make('text', 'province', 'Province/State'),
            Field::make('text', 'full_address', 'Full Address'),
        ])

        ->where('post_type', '=', 'chalet');
});
add_action('init', function () {
    register_post_status('inactive', [
        'label'                     => _x('Inactive', 'post'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>'),
    ]);
});

// =============================
// Register Region Custom Post Type
// =============================
function register_region_cpt()
{
    $labels = array(
        'name' => 'Chalet Regions',
        'singular_name' => 'Chalet Region',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Chalet Region',
        'edit_item' => 'Edit Chalet Region',
        'new_item' => 'New Chalet Region',
        'view_item' => 'View Chalet Region',
        'search_items' => 'Search Chalet Regions',
        'not_found' => 'No Chalet Regions found',
        'not_found_in_trash' => 'No Chalet Regions found in Trash',
        'all_items' => 'All Chalet Regions',
        'menu_name' => 'Chalet Regions',
        'name_admin_bar' => 'Chalet Region'
    );
    $args = array(
        'label' => 'Chalet Region',
        'labels' => $labels,
        'public' => true,
        'show_in_menu' => false,
        'show_in_admin_bar' => false,
        'supports' => array('title', 'thumbnail'),
        'has_archive' => true,
        'rewrite' => array('slug' => 'regions'),
        'show_in_rest' => true,
    );
    register_post_type('region', $args);
}
add_action('init', 'register_region_cpt');

// Add Chalet Regions as a submenu under Chalets
add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=chalet', // Parent menu slug (Chalets)
        'Chalet Regions',            // Page title
        'Chalet Regions',                   // Menu title
        'edit_posts',               // Capability required
        'edit.php?post_type=region' // Menu slug
    );
});

/* Chalet Featue */
add_action('init', function () {
    register_post_type('chalet_feature', [
        'label' => 'Chalet Features',
        'public' => true,
        'menu_icon' => 'dashicons-list-view',
        'supports' => ['title'],
        'show_in_rest' => true,
        'show_in_menu' => 'edit.php?post_type=chalet',
    ]);
});

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Feature Details')
        ->where('post_type', '=', 'chalet_feature')
        ->add_fields([
            Field::make('select', 'feature_type', 'Feature Type')
                ->add_options([
                    'indoor' => 'Indoor',
                    'outdoor' => 'Outdoor',
                    'kitchen' => 'Kitchen',
                    'family' => 'Family',
                    'sports' => 'Sports',
                    'services' => 'Services',
                    'accessibility' => 'Accessibility',
                    'events' => 'Events',
                ])
            // ->set_required(true)
            ,

            Field::make('image', 'feature_icon', 'Feature Icon')
                ->set_value_type('url')
            // ->set_required(true),
        ]);
});


/* * 
    Subscription PLans
 */

 add_action('init', function () {
    register_post_type('subscription_plan', [
        'label' => 'Subscription Plans',
        'public' => true,
        'menu_icon' => 'dashicons-money-alt',
        'supports' => ['title'],
        'show_in_rest' => true,
    ]);
});

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Subscription Details')
        ->where('post_type', '=', 'subscription_plan')
        ->add_fields([
            Field::make('textarea', 'subscription_description', 'Description'),
            Field::make('text', 'subscription_price', 'Price')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('select', 'subscription_interval', 'Interval')
                ->add_options([
                    'day' => 'Day',
                    'week' => 'Week',
                    'month' => 'Month',
                    'year' => 'Year',
                ]),
            Field::make('text', 'subscription_interval_duration', 'Interval Duration')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 1),
            Field::make('text', 'chalets_allowed', 'Chalets Allowed')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0),
            Field::make('text', 'featured_allowed', 'Featured Allowed')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0),
        ]);
});