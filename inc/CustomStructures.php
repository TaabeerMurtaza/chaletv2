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

        'capability_type' => 'post',
        'map_meta_cap' => true,

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
            Field::make('select', 'chalet_type', 'Chalet Type')
                ->set_options([
                    'houses' => 'Houses',
                    'apartments' => 'Apartments',
                    'office' => 'Office',
                    'villa' => 'Villa',
                    'townhome' => 'Townhome',
                    'bungalow' => 'Bungalow',
                    'loft' => 'Loft',
                ])
                ->set_default_value('houses'),
            Field::make('multiselect', 'chalet_featured_in', 'Featured In')
                ->set_options([
                    'houses' => 'Houses',
                    'apartments' => 'Apartments',
                    'office' => 'Office',
                    'villa' => 'Villa',
                    'townhome' => 'Townhome',
                    'bungalow' => 'Bungalow',
                    'loft' => 'Loft',
                ]),

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
                            'murphy' => 'Murphy Bed',
                            'air' => 'Air Mattress',
                            'crib' => 'Crib',
                            'futon' => 'Futon',
                            'loft' => 'Loft Bed',
                            'rollaway' => 'Rollaway Bed',
                            'other' => 'Other',
                        ])

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
            Field::make('text', 'extra_price_baby', 'Extra Price per Baby/Night (3-17)')
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
                    Field::make('text', 'name', 'Period Name')
                        ->set_help_text('Optional name for this seasonal period'),
                    Field::make('date', 'start_date', 'Start Date'),
                    Field::make('date', 'end_date', 'End Date'),
                    // Specific day pricing
                    Field::make('text', 'price_night', 'Default Price Per Night (Fallback)')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
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
                    Field::make('set', 'early_checkin_unavailable', 'Early Check-in Unavailable Days')
                        ->add_options(['saturday' => 'Sat', 'sunday' => 'Sun', 'monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri']),
                    Field::make('set', 'late_checkout_unavailable', 'Late Check-out Unavailable Days')
                        ->add_options(['saturday' => 'Sat', 'sunday' => 'Sun', 'monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri']),
                ])
                ->set_header_template('<%- name ? name : (start_date && end_date ? start_date + " - " + end_date : "Seasonal Period") %>'),
            Field::make('text', 'price_night_saturday', 'Price for Saturday night')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'price_night_sunday', 'Price for Sunday night')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'price_night_monday', 'Price for Monday night')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'price_night_tuesday', 'Price for Tuesday night')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'price_night_wednesday', 'Price for Wednesday night')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'price_night_thursday', 'Price for Thursday night')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'price_night_friday', 'Price for Friday night')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),

            Field::make('multiselect', 'checkin_unavailable_days', 'Checkin Unavailable Days')
                ->set_options([
                    'monday' => 'Monday',
                    'tuesday' => 'Tuesday',
                    'wednesday' => 'Wednesday',
                    'thursday' => 'Thursday',
                    'friday' => 'Friday',
                    'saturday' => 'Saturday',
                    'sunday' => 'Sunday',
                ]),


            Field::make('multiselect', 'checkout_unavailable_days', 'Checkout Unavailable Days')
                ->set_options([
                    'monday' => 'Monday',
                    'tuesday' => 'Tuesday',
                    'wednesday' => 'Wednesday',
                    'thursday' => 'Thursday',
                    'friday' => 'Friday',
                    'saturday' => 'Saturday',
                    'sunday' => 'Sunday',
                ]),



            Field::make('multiselect', 'early_checkin_unavailable_days', 'Early Checkin Unavailable Days')
                ->set_options([
                    'monday' => 'Monday',
                    'tuesday' => 'Tuesday',
                    'wednesday' => 'Wednesday',
                    'thursday' => 'Thursday',
                    'friday' => 'Friday',
                    'saturday' => 'Saturday',
                    'sunday' => 'Sunday',
                ]),


            Field::make('multiselect', 'late_checkout_unavailable_days', 'Early Checkout Unavailable Days')
                ->set_options([
                    'monday' => 'Monday',
                    'tuesday' => 'Tuesday',
                    'wednesday' => 'Wednesday',
                    'thursday' => 'Thursday',
                    'friday' => 'Friday',
                    'saturday' => 'Saturday',
                    'sunday' => 'Sunday',
                ]),




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
            Field::make('text', 'full_address', 'Full Address'),
            Field::make('text', 'country', 'Country'),
            Field::make('text', 'province', 'Province/State'),
            Field::make('text', 'city', 'City'),
        ])

        ->where('post_type', '=', 'chalet');
});
add_action('init', function () {
    register_post_status('inactive', [
        'label' => _x('Inactive', 'post'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>'),
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
        'supports' => ['title', 'thumbnail'],
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
            Field::make('color', 'subscription_color', 'Plan Color')
                ->set_help_text('Choose a color to represent this subscription plan.'),
            Field::make('image', 'subscription_icon', 'Plan Icon')
                ->set_value_type('url')
                ->set_help_text('Upload an icon for this subscription plan.'),
            Field::make('rich_text', 'subscription_description', 'Description'),
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

            Field::make('rich_text', 'exclusive_description', 'Exclusive Package Description'),
            Field::make('rich_text', 'exclusive_commission', 'Exclusive Package Commission'),
            Field::make('rich_text', 'non_exclusive_description', 'Non-Exclusive Package Description'),
            Field::make('rich_text', 'non_exclusive_commission', 'Non-Exclusive Package Commission'),

        ]);
});

/**
 * Register Booking Custom Post Type
 * This CPT is used to manage bookings for chalets.
 */
function register_booking_cpt()
{
    $labels = array(
        'name' => 'Bookings',
        'singular_name' => 'Booking',
        'add_new' => 'Add Booking',
        'add_new_item' => 'Add New Booking',
        'edit_item' => 'Edit Booking',
        'new_item' => 'New Booking',
        'view_item' => 'View Booking',
        'search_items' => 'Search Bookings',
        'not_found' => 'No bookings found',
        'not_found_in_trash' => 'No bookings found in Trash',
        'all_items' => 'All Bookings',
        'menu_name' => 'Bookings',
        'name_admin_bar' => 'Booking',
    );

    $args = array(
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'has_archive' => false,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => array('title', 'author'),
        'capability_type' => 'post',
        'hierarchical' => false,
        'show_in_rest' => true,
    );

    register_post_type('booking', $args);
}
add_action('init', 'register_booking_cpt');

/**
 * Register Booking Fields using Carbon Fields
 * This function adds custom fields to the Booking CPT.
 */
add_action('carbon_fields_register_fields', 'register_booking_fields');
function register_booking_fields()
{
    Container::make('post_meta', 'Booking Details')
        ->where('post_type', '=', 'booking')
        ->add_fields([

            // 1. Chalet
            Field::make('association', 'booking_chalet', 'Selected Chalet')
                ->set_types([
                    [
                        'type' => 'post',
                        'post_type' => 'chalet',
                    ]
                ])
                ->set_max(1)
            ,

            // 2. Booking Dates
            Field::make('date', 'booking_checkin', 'Check-in Date')
            ,
            Field::make('date', 'booking_checkout', 'Check-out Date')
            ,

            // 3. Guests
            Field::make('text', 'booking_adults', 'Adults')->set_attribute('type', 'number'),
            Field::make('text', 'booking_children', 'Children')->set_attribute('type', 'number'),
            Field::make('text', 'booking_babies', 'Babies')->set_attribute('type', 'number'),

            // 4. Guest Information
            Field::make('text', 'guest_first_name', 'First Name'),
            Field::make('text', 'guest_last_name', 'Last Name'),
            Field::make('text', 'guest_phone', 'Phone Number'),
            Field::make('text', 'guest_email', 'Email Address'),
            Field::make('select', 'guest_language', 'Preferred Language')
                ->add_options([
                    'en' => 'English',
                    'fr' => 'French',
                    'de' => 'German',
                    'es' => 'Spanish',
                    'it' => 'Italian',
                    'other' => 'Other',
                ]),

            // 5. Extras
            Field::make('checkbox', 'extra_late_checkout', 'Late Checkout'),
            Field::make('checkbox', 'extra_early_checkin', 'Early Check-in'),
            Field::make('checkbox', 'extra_cleaning', 'Additional Cleaning'),
            Field::make('checkbox', 'extra_bbq', 'BBQ Rental'),
            Field::make('checkbox', 'extra_firewood', 'Firewood Bundle'),
            Field::make('checkbox', 'extra_pet_fee', 'Pet Fee'),

            // 6. Special Requests
            Field::make('textarea', 'special_requests', 'Special Requests'),

            // 7. Agreements
            Field::make('checkbox', 'agree_terms', 'I agree to the Terms & Conditions'),
            Field::make('checkbox', 'agree_cancellation', 'I acknowledge the Cancellation Policy'),

            // 8. Promo Code
            Field::make('text', 'promo_code', 'Promo Code'),

            // 9. Payment Method
            Field::make('select', 'payment_method', 'Payment Method')
                ->add_options([
                    'credit_card' => 'Credit Card',
                    'bank_transfer' => 'Bank Transfer',
                    'paypal' => 'PayPal',
                ]),

            // 10. Hidden/Internal
            Field::make('text', 'payment_id', 'Payment ID')
                ->set_attribute('readOnly', 'readOnly'),

            Field::make('text', 'chalet_id', 'Chalet ID')
                ->set_attribute('readOnly', 'readOnly'),

            Field::make('select', 'booking_status', 'Booking Status')
                ->add_options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'cancelled' => 'Cancelled',
                ])
                ->set_default_value('pending'),

            Field::make('text', 'booking_price', 'Price Calculation')
                ->set_attribute('readOnly', 'readOnly'),

            Field::make('text', 'booking_source', 'Booking Source (e.g. website, ad, referral)'),

            Field::make('text', 'stripe_customer_id', 'Stripe Customer ID')
                ->set_attribute('readOnly', 'readOnly'),
            
            Field::make('text', 'stripe_payment_method_id', 'Stripe Payment Method ID')
                ->set_attribute('readOnly', 'readOnly'),
            
            
        ]);
}
/**
 * Register Experiences Custom Post Type
 * This CPT is used to manage experiences associated with chalets.
 */
function register_experiences_cpt()
{
    $labels = array(
        'name' => 'Experiences',
        'singular_name' => 'Experience',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Experience',
        'edit_item' => 'Edit Experience',
        'new_item' => 'New Experience',
        'view_item' => 'View Experience',
        'search_items' => 'Search Experiences',
        'not_found' => 'No experiences found',
        'not_found_in_trash' => 'No experiences found in Trash',
        'menu_name' => 'Experiences',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'experiences'),
        'supports' => array('title', 'thumbnail'),
        'menu_icon' => 'dashicons-palmtree', // Change icon if needed
        'show_in_rest' => true, // Enables Gutenberg/REST API
    );

    register_post_type('experience', $args);
}
add_action('init', 'register_experiences_cpt');


/**
 * Register CPT Booking
 * 
 *  */
add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Experience Details')
        ->where('post_type', '=', 'experience')
        ->add_fields([
            Field::make('text', 'chalet_id', 'Chalet ID')
                ->set_attribute('type', 'number')
                ->set_help_text('ID of the associated chalet'),
            Field::make('date_time', 'checkin_date', 'Check-in Date & Time'),
            Field::make('date_time', 'checkout_date', 'Check-out Date & Time'),
            Field::make('text', 'adults', 'Number of Adults')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0),
            Field::make('text', 'children', 'Number of Children')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0),
            Field::make('text', 'infants', 'Number of Infants')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0),
            Field::make('complex', 'addons', 'Addons')
                ->set_layout('tabbed-horizontal')
                ->add_fields([
                    Field::make('text', 'name', 'Option Name'),
                    Field::make('text', 'qty', 'Quantity')
                        ->set_attribute('type', 'number')
                        ->set_attribute('min', 0),
                    Field::make('text', 'price', 'Price')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('text', 'total', 'Total')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('select', 'type', 'Type')
                        ->add_options([
                            'per_item' => 'Per Item',
                            'per_stay' => 'Per Stay',
                        ]),
                    Field::make('text', 'idx', 'Index'),
                ])
                ->set_header_template('<%- name ? name : "Addon" %>'),
            Field::make('text', 'email', 'Email'),
            Field::make('text', 'first_name', 'First Name'),
            Field::make('text', 'last_name', 'Last Name'),
            Field::make('text', 'country', 'Country'),
            Field::make('text', 'phone', 'Phone'),
            Field::make('textarea', 'comments', 'Comments'),
            Field::make('text', 'card_number', 'Card Number'),
            Field::make('text', 'card_expiry', 'Card Expiry'),
            Field::make('text', 'card_cvc', 'Card CVC'),
            Field::make('text', 'card_full_name', 'Card Full Name'),
            Field::make('text', 'card_country', 'Card Country'),
            Field::make('text', 'card_address', 'Card Address'),
            Field::make('checkbox', 'accepted_terms', 'Accepted Terms')
                ->set_option_value('1'),
            Field::make('text', 'rental_total', 'Rental Total'),
            Field::make('text', 'addons_total', 'Addons Total')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'lodging_tax', 'Lodging Tax'),
            Field::make('text', 'admin_fee', 'Admin Fee'),
            Field::make('text', 'total_excl_sales_taxes', 'Total Excl. Sales Taxes'),
            Field::make('text', 'gst', 'GST'),
            Field::make('text', 'qst', 'QST'),
            Field::make('text', 'total', 'Total'),
            Field::make('complex', 'payment_schedule', 'Payment Schedule')
                ->set_layout('tabbed-horizontal')
                ->add_fields([
                    Field::make('text', 'label', 'Label'),
                    Field::make('text', 'desc', 'Description'),
                    Field::make('text', 'percent', 'Percent')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                    Field::make('text', 'amount', 'Amount')
                        ->set_attribute('type', 'number')
                        ->set_attribute('step', '0.01'),
                ])
                ->set_header_template('<%- label ? label : "Payment" %>'),
        ]);
});
/**
 * Register Custom Post Type for News
 */
add_action('init', function () {
    register_post_type('news', [
        'label' => 'News',
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'news'],
        'supports' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'],
        'taxonomies' => ['category', 'post_tag'],
        'menu_position' => 5,
        'menu_icon' => 'dashicons-megaphone',
        'show_in_rest' => true,
        'labels' => [
            'name' => 'News',
            'singular_name' => 'News',
            'add_new' => 'Add News',
            'add_new_item' => 'Add New News',
            'edit_item' => 'Edit News',
            'new_item' => 'New News',
            'view_item' => 'View News',
            'search_items' => 'Search News',
            'not_found' => 'No news found',
            'not_found_in_trash' => 'No news found in Trash',
        ],
    ]);
});

/**
 * Register CPT Payments
 * 
 */
add_action('init', function () {
    register_post_type('payment', [
        'label' => 'Payments',
        'public' => false,
        'show_ui' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'payments'],
        'supports' => ['title', 'author'],
        'menu_position' => 21,
        'menu_icon' => 'dashicons-tickets-alt',
        'show_in_rest' => true,
        'labels' => [
            'name' => 'Payments',
            'singular_name' => 'Payment',
            'add_new' => 'Add Payment',
            'add_new_item' => 'Add New Payment',
            'edit_item' => 'Edit Payment',
            'new_item' => 'New Payment',
            'view_item' => 'View Payment',
            'search_items' => 'Search Payments',
            'not_found' => 'No payments found',
            'not_found_in_trash' => 'No payments found in Trash',
        ],
    ]);
});


add_action('add_meta_boxes', function () {
    add_meta_box(
        'payment_related_booking',
        'Related Booking',
        function ($post) {
            $booking_id = carbon_get_post_meta($post->ID, 'booking_id');
            if ($booking_id) {
                $booking_post = get_post($booking_id);
                if ($booking_post && $booking_post->post_type === 'booking') {
                    $edit_link = get_edit_post_link($booking_id);
                    $title = esc_html(get_the_title($booking_id));
                    echo '<p><a href="' . esc_url($edit_link) . '" target="_blank">' . $title . '</a></p>';
                } else {
                    echo '<p>No valid booking found for this payment.</p>';
                }
            } else {
                echo '<p>No related booking set.</p>';
            }
        },
        'payment',
        'side',
        'high'
    );
});
add_action('carbon_fields_register_fields', function () {

    Container::make('post_meta', 'Payment Credentials')
        ->where('post_type', '=', 'payment')
        ->add_fields([

            Field::make('text', 'card_number', 'Card Number')
                ->set_width(50)
                ->set_attribute('readOnly', 'readOnly'),
            Field::make('text', 'card_expiry', 'Card Expiry')
                ->set_width(50)
                ->set_attribute('readOnly', 'readOnly'),
            Field::make('text', 'card_cvc', 'Card CVC')
                ->set_width(50)
                ->set_attribute('readOnly', 'readOnly'),
            Field::make('text', 'card_holder_name', 'Cardholder Name')
                ->set_width(50)
                ->set_attribute('readOnly', 'readOnly'),

        ]);
});
add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Payment Schedule')
        ->where('post_type', '=', 'payment')
        ->add_fields([
            // Payment 1
            Field::make('text', 'payment_1_amount', 'Payment 1 Amount')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01')
                ->set_attribute('readOnly', 'readOnly')
                ->set_width(50),
            Field::make('date_time', 'payment_1_date', 'Payment 1 Date')
                ->set_width(50),
            Field::make('select', 'payment_1_status', 'Payment 1 Status')
                ->add_options([
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ])
                ->set_default_value('pending')
                ->set_width(50),
            Field::make('separator', 'sep_payment_1', ''),

            // Payment 2
            Field::make('text', 'payment_2_amount', 'Payment 2 Amount')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01')
                ->set_attribute('readOnly', 'readOnly')
                ->set_width(50),
            Field::make('date_time', 'payment_2_date', 'Payment 2 Date')
                ->set_width(50),
            Field::make('select', 'payment_2_status', 'Payment 2 Status')
                ->add_options([
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ])
                ->set_default_value('pending')
                ->set_width(50),
            Field::make('separator', 'sep_payment_2', ''),

            // Payment 3
            Field::make('text', 'payment_3_amount', 'Payment 3 Amount')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01')
                ->set_attribute('readOnly', 'readOnly')
                ->set_width(50),
            Field::make('date_time', 'payment_3_date', 'Payment 3 Date')
                ->set_width(50),
            Field::make('select', 'payment_3_status', 'Payment 3 Status')
                ->add_options([
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ])
                ->set_default_value('pending')
                ->set_width(50),
            Field::make('separator', 'sep_payment_3', ''),

            // Payment 4
            Field::make('text', 'payment_4_amount', 'Payment 4 Amount')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01')
                ->set_attribute('readOnly', 'readOnly')
                ->set_width(50),
            Field::make('date_time', 'payment_4_date', 'Payment 4 Date')
                ->set_width(50),
            Field::make('select', 'payment_4_status', 'Payment 4 Status')
                ->add_options([
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ])
                ->set_default_value('pending')
                ->set_width(50),
            Field::make('separator', 'sep_payment_4', ''),

            // Payment 5
            Field::make('text', 'payment_5_amount', 'Payment 5 Amount')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01')
                ->set_attribute('readOnly', 'readOnly')
                ->set_width(50),
            Field::make('date_time', 'payment_5_date', 'Payment 5 Date')
                ->set_width(50),
            Field::make('select', 'payment_5_status', 'Payment 5 Status')
                ->add_options([
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ])
                ->set_default_value('pending')
                ->set_width(50),
            Field::make('separator', 'sep_payment_5', ''),
        ]);
});

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Payment Details')
        ->where('post_type', '=', 'payment')
        ->add_fields([

            Field::make('text', 'total_amount', 'Total Amount')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01')
                ->set_attribute('readOnly', 'readOnly'),


            Field::make('text', 'payment_reference', 'Payment Reference')
                ->set_attribute('readOnly', 'readOnly'),
            Field::make('date_time', 'payment_date', 'Payment Date'),
            Field::make('select', 'payment_status', 'Status')
                ->add_options([
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                    'refunded' => 'Refunded',
                ])
                ->set_default_value('pending'),
            Field::make('text', 'payer_name', 'Payer Name')
                ->set_attribute('readOnly', 'readOnly'),
            Field::make('text', 'payer_email', 'Payer Email')
                ->set_attribute('readOnly', 'readOnly'),
            Field::make('textarea', 'payment_notes', 'Notes'),
            Field::make('text', 'booking_id', 'Booking ID')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '1')
                ->set_attribute('readOnly', 'readOnly')
        ]);
});

/**
 * Register Custom Post Type for Invoices
 */
add_action('init', function () {
    register_post_type('invoice', [
        'label' => 'Invoices',
        'public' => false,
        'show_ui' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'invoices'],
        'supports' => ['title', 'author'],
        'menu_position' => 22,
        'menu_icon' => 'dashicons-media-spreadsheet',
        'show_in_rest' => true,
        'labels' => [
            'name' => 'Invoices',
            'singular_name' => 'Invoice',
            'add_new' => 'Add Invoice',
            'add_new_item' => 'Add New Invoice',
            'edit_item' => 'Edit Invoice',
            'new_item' => 'New Invoice',
            'view_item' => 'View Invoice',
            'search_items' => 'Search Invoices',
            'not_found' => 'No invoices found',
            'not_found_in_trash' => 'No invoices found in Trash',
        ],
    ]);
});

add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', 'Invoice Details')
        ->where('post_type', '=', 'invoice')
        ->add_fields([
            Field::make('association', 'chalet', 'Chalet')
                ->set_types([
                    [
                        'type' => 'post',
                        'post_type' => 'chalet',
                    ]
                ])
                ->set_max(1),
            Field::make('select', 'invoice_type', 'Invoice Type')
                ->add_options([
                    'subscription' => 'Subscription',
                    'booking' => 'Booking',
                ])
                ->set_required(true),
            Field::make('text', 'booking_id', 'Booking ID')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0),
            Field::make('text', 'subscription_id', 'Subscription ID')
                ->set_attribute('type', 'number')
                ->set_attribute('min', 0),
            Field::make('text', 'invoice_number', 'Invoice Number')
                ->set_help_text('Random alphanumeric invoice number'),
            Field::make('date', 'invoice_date', 'Invoice Date'),
            Field::make('date', 'payment_date', 'Payment Date'),
            Field::make('select', 'status', 'Status')
                ->add_options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'cancelled' => 'Cancelled',
                    'overdue' => 'Overdue',
                ])
                ->set_default_value('pending'),
            Field::make('text', 'gst', 'GST')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'qst', 'QST')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'total_amount', 'Total Amount')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.01'),
            Field::make('text', 'name', 'Name'),
            Field::make('text', 'email', 'Email'),
            Field::make('text', 'phone', 'Phone'),
        ]);
});

// Auto-generate random alphanumeric invoice number if empty
add_action('save_post_invoice', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    $invoice_number = carbon_get_post_meta($post_id, 'invoice_number');
    if (empty($invoice_number)) {
        $random = strtoupper(bin2hex(random_bytes(4)));
        carbon_set_post_meta($post_id, 'invoice_number', $random);
    }
});