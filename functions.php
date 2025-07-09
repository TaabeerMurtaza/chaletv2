<?php

// Enqueue theme styles
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('chaletv2-style', get_stylesheet_uri(), [], filemtime(get_stylesheet_directory() . '/style.css'));
});

// Dependencies
require_once get_template_directory() . '/vendor/autoload.php';

// Theme setup
require_once get_template_directory() . '/inc/ThemeSetup.php';

// Helpers
require_once get_template_directory() . '/inc/helpers.php';

// Enqueue scripts and styles
require_once get_template_directory() . '/inc/enqueue.php';

// Custom Structures (CPT, Meta, etc)
require_once get_template_directory() . '/inc/CustomStructures.php';
require_once get_template_directory() . '/inc/admin/chalet-generator.php';

// Handle Form Submissions & Queries
require_once get_template_directory() . '/inc/queries.php';

// Woocommerce
require_once get_template_directory() . '/inc/Woocommerce.php';

// Subscriptions
require_once get_template_directory() . '/inc/Subscriptions.php';

// Middlewares
require_once get_template_directory() . '/inc/middlewares.php';

// Chalet V1 helpers
// require_once get_template_directory() . '/inc/chalet_v1.php';

add_action('admin_init', function () {
    return;
    if (!current_user_can('manage_options'))
        return;

    // DELETE existing chalet_feature posts
    // $existing = get_posts([
    //     'post_type' => 'chalet_feature',
    //     'numberposts' => -1,
    //     'fields' => 'ids',
    // ]);

    // foreach ($existing as $post_id) {
    //     wp_delete_post($post_id, true);
    // }

    // Set base path and icons (cycle these)
    $base_url = content_url('uploads/2025/06/');
    $icons = [
        'a-b.png',
        'abacus.png',
        'abc.png',
        'accessible.png',
        'access-point.png',
        'activity.png',
        'ad.png',
        'address-book.png',
        'adjustments.png',
        'ai.png'
    ];

    $features = [
        'indoor' => [
            'Central Heating',
            'Air Conditioning',
            'Fireplace',
            'Smart Lighting',
            'Ceiling Fans',
            'Hardwood Flooring',
            'Home Theater',
            'Soundproof Walls',
            'Walk-In Closet',
            'Skylight',
            'Mudroom',
            'Laundry Room',
            'Finished Basement',
            'Attic Storage',
            'Indoor Plant Wall',
            'Built-In Shelves',
            'Home Office',
            'Floor-to-Ceiling Windows',
            'Smart Locks',
            'Smart Thermostat'
        ],
        'outdoor' => [
            'Private Garden',
            'Swimming Pool',
            'Hot Tub',
            'Fire Pit',
            'Barbecue Area',
            'Deck',
            'Balcony',
            'Rooftop Terrace',
            'Outdoor Kitchen',
            'Outdoor Shower',
            'Pergola',
            'Hammock Area',
            'Gazebo',
            'Treehouse',
            'Fenced Yard',
            'Water Feature',
            'Greenhouse',
            'Outdoor Lighting',
            'Rock Garden',
            'Lawn Sprinklers'
        ],
        'kitchen' => [
            'Island Counter',
            'Granite Countertops',
            'Walk-In Pantry',
            'Double Oven',
            'Gas Stove',
            'Built-In Microwave',
            'Dishwasher',
            'Coffee Bar',
            'Wine Fridge',
            'Stainless Steel Appliances',
            'Breakfast Nook',
            'Pot Filler Faucet',
            'Soft-Close Cabinets',
            'Trash Compactor',
            'Smart Fridge',
            'Open Shelving',
            'Spice Drawer',
            'Under-Cabinet Lighting',
            'Water Purifier',
            'Range Hood'
        ],
        'family' => [
            'Game Room',
            'Kids’ Playroom',
            'Reading Nook',
            'Bunk Beds',
            'Family Dining Area',
            'Toy Storage',
            'Chalkboard Wall',
            'Indoor Slide',
            'Board Game Shelf',
            'Home Library',
            'High Chair',
            'Kid-Safe Furniture',
            'Movie Setup',
            'Soft Carpeting',
            'Craft Station',
            'Custom Wall Art',
            'Puzzle Table',
            'Study Desk',
            'Bean Bags',
            'Shared Closet'
        ],
        'sports' => [
            'Home Gym',
            'Treadmill',
            'Stationary Bike',
            'Yoga Studio',
            'Table Tennis',
            'Pool Table',
            'Mini Basketball Hoop',
            'Dartboard',
            'Climbing Wall',
            'Outdoor Court',
            'Sauna',
            'Boxing Bag',
            'Rowing Machine',
            'Weight Bench',
            'Resistance Bands',
            'Golf Putting Green',
            'Bike Rack',
            'Smart Mirror Gym',
            'Badminton Net',
            'Jogging Track'
        ],
        'services' => [
            'Housekeeping',
            'Private Chef',
            'Laundry Service',
            'Grocery Delivery',
            'Pet Sitting',
            'Concierge',
            'Driver',
            'Maintenance',
            'Babysitting',
            'Massage Therapist',
            'Tutor',
            'Pool Cleaning',
            'Gardening',
            'Tech Support',
            'Personal Trainer',
            'Dog Walker',
            'Car Wash',
            'Security Monitoring',
            'Package Pickup',
            'Dry Cleaning'
        ],
        'accessibility' => [
            'Ramp Access',
            'Step-Free Entry',
            'Elevator',
            'Wide Doorways',
            'Grab Bars',
            'Shower Seat',
            'Anti-Slip Floors',
            'Lower Counters',
            'Voice Control',
            'Adjustable Lighting',
            'Braille Labels',
            'Stair Lift',
            'Accessible Parking',
            'Smart Thermostat',
            'Door Alert System',
            'Hearing Loop',
            'Touchless Faucets',
            'Auto Blinds',
            'Bed Lift System',
            'Emergency Cord'
        ],
        'events' => [
            'Banquet Space',
            'Dance Floor',
            'DJ Booth',
            'Projector Screen',
            'Sound System',
            'Outdoor Stage',
            'Party Lights',
            'Guest Parking',
            'Buffet Setup',
            'Mobile Bar',
            'Lounge Area',
            'Photo Booth',
            'VIP Section',
            'Tent Space',
            'Decor Arch',
            'Fire Pit Circle',
            'Champagne Fountain',
            'Event Staff',
            'Decor Rentals',
            'Event Planner Access'
        ]
    ];

    $counter = 0;

    foreach ($features as $type => $list) {
        foreach ($list as $name) {
            $icon_filename = $icons[$counter % count($icons)];
            $icon_url = "{$base_url}{$icon_filename}";

            $post_id = wp_insert_post([
                'post_title' => $name,
                'post_type' => 'chalet_feature',
                'post_status' => 'publish',
            ]);

            if ($post_id && !is_wp_error($post_id)) {
                carbon_set_post_meta($post_id, 'feature_type', $type);
                carbon_set_post_meta($post_id, 'feature_icon', $icon_url);
            }

            $counter++;
        }
    }

    echo '<div class="notice notice-success"><p>Old features wiped and 160 new ones created with local icons!</p></div>';
});
add_action('admin_init', function () {
    return;
    if (!current_user_can('manage_options'))
        return;

    $posts = get_posts([
        'post_type' => 'chalet_feature',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $seen_titles = [];
    $deleted = 0;

    foreach ($posts as $post) {
        $title = strtolower(trim($post->post_title));

        if (isset($seen_titles[$title])) {
            wp_delete_post($post->ID, true);
            $deleted++;
        } else {
            $seen_titles[$title] = true;
        }
    }

    echo "<div class='notice notice-warning'><p>🧹 Cleaned up $deleted duplicate feature posts.</p></div>";
});
add_action('init', function () {
    // print_r(carbon_get_post_meta(2697, 'payment_booking'));
    // exit;
    // carbon_set_post_meta(2697, 'payment_booking',  2630);
    // carbon_set_post_meta(2715, "payment_1_date", "2025-07-02 12:00:00");
    check_pending_payments();
});