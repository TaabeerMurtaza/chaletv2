<?php

/**
 * Chalet Generator Admin Page
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Helper function to get random image from media library
function get_random_image_from_media_library()
{
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => 1,
        'orderby' => 'rand',
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $image = $query->posts[0];
        return array(
            'id' => $image->ID,
            'url' => wp_get_attachment_url($image->ID),
            'alt' => get_post_meta($image->ID, '_wp_attachment_image_alt', true)
        );
    }
    return null;
}

// Function to create dummy regions
function create_dummy_regions()
{
    $regions = [
        [
            'title' => 'Charlevoix',
            'description' => 'A picturesque region along the St. Lawrence River',
            'lat' => 46.8139,
            'lng' => -71.2080,
            'zoom' => 10,
            'image' => 'https://example.com/charlevoix.jpg' // Replace with actual image URL
        ],
        [
            'title' => 'Laurentians',
            'description' => 'Mountainous region with ski resorts and lakes',
            'lat' => 46.1333,
            'lng' => -74.5667,
            'zoom' => 9,
            'image' => 'https://example.com/laurentians.jpg' // Replace with actual image URL
        ],
        [
            'title' => 'Eastern Townships',
            'description' => 'Scenic region with vineyards and historic villages',
            'lat' => 45.2167,
            'lng' => -72.6000,
            'zoom' => 9,
            'image' => 'https://example.com/eastern-townships.jpg' // Replace with actual image URL
        ],
        [
            'title' => 'Gaspésie',
            'description' => 'Coastal region with rugged cliffs and beaches',
            'lat' => 48.8500,
            'lng' => -64.5000,
            'zoom' => 8,
            'image' => 'https://example.com/gaspe.jpg' // Replace with actual image URL
        ]
    ];

    foreach ($regions as $region_data) {
        $existing_region = get_page_by_title($region_data['title'], OBJECT, 'region');
        if (!$existing_region) {
            $region_post = array(
                'post_title' => $region_data['title'],
                'post_content' => $region_data['description'],
                'post_type' => 'region',
                'post_status' => 'publish'
            );
            $region_id = wp_insert_post($region_post);
            
            if ($region_id) {
                // Set location data
                carbon_set_post_meta($region_id, 'location', array(
                    'lat' => $region_data['lat'],
                    'lng' => $region_data['lng'],
                    'zoom' => $region_data['zoom']
                ));

                // Set region image
                carbon_set_post_meta($region_id, 'region_image', $region_data['image']);
            }
        }
    }
}

// Add menu item
add_action('admin_menu', 'add_chalet_generator_menu');
function add_chalet_generator_menu()
{
    add_submenu_page(
        'edit.php?post_type=chalet',
        'Chalet Generator',
        'Generator',
        'manage_options',
        'chalet-generator',
        'chalet_generator_page'
    );
}

// Main page function
function chalet_generator_page()
{
    if (isset($_GET['generated'])) {
        echo '<div class="notice notice-success"><p>Successfully generated ' . esc_html($_GET['generated']) . ' chalets!</p></div>';
    }

    // Create dummy regions if they don't exist
    create_dummy_regions();

    // Display the form
    ?>
    <div class="wrap">
        <h1>Chalet Generator</h1>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('generate_chalets', 'chalet_generator_nonce'); ?>
            <input type="hidden" name="action" value="generate_chalets">
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="num_chalets">Number of Chalets</label></th>
                    <td>
                        <input type="number" id="num_chalets" name="num_chalets" min="1" max="100" value="5" class="regular-text">
                        <p class="description">Enter the number of dummy chalets to generate.</p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="generate_chalets" id="generate_chalets" class="button button-primary" value="Generate Chalets">
            </p>
        </form>
    </div>
    <?php
}

// Handle form submission
add_action('admin_post_generate_chalets', 'handle_chalet_generator_submission');
function handle_chalet_generator_submission()
{
    check_admin_referer('generate_chalets', 'chalet_generator_nonce');

    $num_chalets = intval($_POST['num_chalets']);

    // Create dummy regions if they don't exist
    create_dummy_regions();

    if ($num_chalets > 0) {
        for ($i = 1; $i <= $num_chalets; $i++) {
            // Get random images for the chalet
            $chalet_images = [];
            $num_images = rand(3, 8); // Random number of images between 3 and 8
            for ($img = 0; $img < $num_images; $img++) {
                $image = get_random_image_from_media_library();
                if ($image) {
                    $chalet_images[] = $image['id'];
                }
            }

            // Create the post with featured image
            $post_data = array(
                'post_title'    => 'Dummy Chalet ' . $i,
                'post_type'     => 'chalet',
                'post_status'   => 'publish',
                'post_content'  => 'This is a dummy chalet created for testing purposes.',
                'post_thumbnail' => $chalet_images[0] ?? null // Set first image as featured
            );

            $post_id = wp_insert_post($post_data);

            if ($post_id) {
                // Set the gallery images
                carbon_set_post_meta($post_id, 'chalet_images', $chalet_images);

                // Get random region
                $regions = get_posts(array(
                    'post_type' => 'region',
                    'posts_per_page' => -1
                ));
                
                if (!empty($regions)) {
                    $region = $regions[array_rand($regions)];
                    carbon_set_post_meta($post_id, 'region', $region->ID);
                    
                    // Use region's location as base for chalet location
                    $region_location = carbon_get_post_meta($region->ID, 'location');
                    if ($region_location) {
                        carbon_set_post_meta($post_id, 'chalet_location', array(
                            'lat' => $region_location['lat'] + (rand(-100, 100) / 1000),
                            'lng' => $region_location['lng'] + (rand(-100, 100) / 1000),
                            'zoom' => $region_location['zoom']
                        ));
                    }
                }
                
                carbon_set_post_meta($post_id, 'country', 'Canada');
                carbon_set_post_meta($post_id, 'province', 'Quebec');
                carbon_set_post_meta($post_id, 'full_address', sprintf(
                    '%d %s Street, %s, QC, Canada',
                    rand(100, 999),
                    ['Chalet', 'Mountain', 'Pine', 'Spruce', 'Cedar'][array_rand(['Chalet', 'Mountain', 'Pine', 'Spruce', 'Cedar'])],
                    get_the_title(carbon_get_post_meta($post_id, 'region'))
                ));

                // Generate random data
                generate_random_data($post_id);
            }
        }
        wp_redirect(add_query_arg('generated', $num_chalets, admin_url('edit.php?post_type=chalet')));
        exit;
    }
}

// Generate random data
function generate_random_data($post_id) {
    $bed_types = ['double', 'queen', 'king', 'twin'];
    $bedroom_types = ['Master', 'Guest', 'Family', 'Loft'];
    $amenities = [
        'indoor' => ['WiFi', 'TV', 'Fireplace', 'Heating', 'Air Conditioning', 'Washing Machine', 'Dishwasher'],
        'outdoor' => ['BBQ', 'Patio', 'Garden', 'Parking', 'Terrace'],
        'kitchen' => ['Oven', 'Microwave', 'Refrigerator', 'Toaster', 'Coffee Maker'],
        'family' => ['Baby Cot', 'High Chair', 'Board Games', 'Children Books'],
        'sports' => ['Ski Equipment Storage', 'Bike Storage', 'Kayak Storage'],
        'services' => ['Housekeeping', '24/7 Support', 'Shuttle Service'],
        'accessibility' => ['Wheelchair Access', 'Elevator', 'Wide Doorways'],
        'events' => ['Conference Room', 'Banquet Hall', 'Sound System']
    ];

    // Generate random description
    $descriptions = [
        'A charming and cozy chalet nestled in the heart of the mountains.',
        'Luxurious mountain retreat with stunning views and modern amenities.',
        'Perfect family getaway with spacious rooms and outdoor activities.',
        'Traditional chalet with all modern comforts and a warm atmosphere.',
        'Ideal for groups - spacious and beautifully decorated chalet.'
    ];

    // Generate random booking link
    $booking_links = [
        'https://booking.com/chalet',
        'https://airbnb.com/chalet',
        'https://vrbo.com/chalet',
        'https://homeaway.com/chalet',
        'https://expedia.com/chalet'
    ];

    // Generate random rates
    $base_weekend_rate = rand(150, 300);
    $base_weekday_rate = rand(100, 200);

    // Add meta fields using Carbon Fields
    carbon_set_post_meta($post_id, 'description', $descriptions[array_rand($descriptions)]);
    carbon_set_post_meta($post_id, 'affiliate_booking_link', $booking_links[array_rand($booking_links)]);
    carbon_set_post_meta($post_id, 'featured', rand(0, 1));
    carbon_set_post_meta($post_id, 'monthly_rate', rand(1000, 3000));
    carbon_set_post_meta($post_id, 'cleaning_fee', rand(80, 150));
    carbon_set_post_meta($post_id, 'cleaning_fee_type', rand(0, 1) ? 'per_stay' : 'fixed');
    carbon_set_post_meta($post_id, 'guest_count', rand(2, 10));
    carbon_set_post_meta($post_id, 'baths', rand(1, 4));

    // Add bedrooms (1-4 bedrooms with detailed data)
    $bedrooms = array();
    $num_bedrooms = rand(1, 4);
    for ($j = 1; $j <= $num_bedrooms; $j++) {
        $bedroom_name = $bedroom_types[array_rand($bedroom_types)] . ' Bedroom ' . $j;
        $num_beds = rand(1, 2);
        $bed_type = $bed_types[array_rand($bed_types)];
        $bedrooms[] = array(
            'name' => $bedroom_name,
            'guests' => rand(1, $num_beds * 2),
            'beds' => $num_beds,
            'type' => $bed_type
        );
    }
    carbon_set_post_meta($post_id, 'bedrooms', $bedrooms);

    // Add default rates
    carbon_set_post_meta($post_id, 'default_rate_weekend', $base_weekend_rate);
    carbon_set_post_meta($post_id, 'default_rate_weekday', $base_weekday_rate);
    carbon_set_post_meta($post_id, 'min_stay', rand(1, 7));
    carbon_set_post_meta($post_id, 'max_stay', rand(28, 56));

    // Add amenities (random selection from each category)
    foreach ($amenities as $type => $options) {
        $selected = array_rand($options, rand(1, count($options)));
        if (is_array($selected)) {
            $selected = array_map(function ($key) use ($options) {
                return $options[$key];
            }, $selected);
        } else {
            $selected = [$options[$selected]];
        }
        carbon_set_post_meta($post_id, $type . '_features', $selected);
    }

    // Add feature icons
    $feature_icon = get_random_image_from_media_library();
    if ($feature_icon) {
        carbon_set_post_meta($post_id, 'feature_icon', $feature_icon['url']);
    }

    // Add seasonal rates (random periods)
    $seasonal_rates = [];
    $seasons = [
        ['name' => 'Summer', 'start' => '2025-06-15', 'end' => '2025-09-15', 'multiplier' => 1.2],
        ['name' => 'Winter', 'start' => '2025-12-15', 'end' => '2026-03-15', 'multiplier' => 1.5],
        ['name' => 'Spring', 'start' => '2025-03-15', 'end' => '2025-06-15', 'multiplier' => 1.1],
        ['name' => 'Fall', 'start' => '2025-09-15', 'end' => '2025-12-15', 'multiplier' => 1.15]
    ];

    foreach ($seasons as $season) {
        $seasonal_rates[] = [
            'start_date' => $season['start'],
            'end_date' => $season['end'],
            'weekend_rate' => round($base_weekend_rate * $season['multiplier']),
            'weekday_rate' => round($base_weekday_rate * $season['multiplier'])
        ];
    }
    carbon_set_post_meta($post_id, 'seasonal_rates', $seasonal_rates);

    // Add check-in/check-out times
    $check_in_times = [
        ['time' => '15:00', 'price' => 0],
        ['time' => '16:00', 'price' => 20],
        ['time' => '17:00', 'price' => 30]
    ];
    carbon_set_post_meta($post_id, 'check_in_times', $check_in_times);

    $check_out_times = [
        ['time' => '11:00', 'price' => 0],
        ['time' => '12:00', 'price' => 30],
        ['time' => '13:00', 'price' => 50]
    ];
    carbon_set_post_meta($post_id, 'check_out_times', $check_out_times);

    // Add reservation policy
    $policies = [
        'policy_50_50_3' => 'Policy 50-50 (3 days before stay)',
        'policy_30_70_7' => 'Policy 30-70 (7 days before stay)',
        'policy_25_25_50_14' => 'Policy 25-25-50 (14 days before stay)'
    ];
    carbon_set_post_meta($post_id, 'reservation_policy', array_rand($policies));
}