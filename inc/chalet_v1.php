<?php
function homey_enqueue_styles()
{
    // enqueue parent styles
    wp_enqueue_style('homey-parent-theme', get_template_directory_uri() . '/style.css');

    // enqueue child styles
    wp_enqueue_style('homey-child-theme', get_stylesheet_directory_uri() . '/style.css', array('homey-parent-theme'));

}
add_action('wp_enqueue_scripts', 'homey_enqueue_styles');

/***============================================
 * =========================================***/
function homey_listing_cities_grid_shortcode($atts)
{
    // Get cities
    $listing_cities = get_terms(array(
        'taxonomy' => 'listing_city',
        'hide_empty' => false,
        'number' => 0,
    ));

    ob_start();
    ?>
    <div class="row mycutomGrid">
        <?php
        if (!empty($listing_cities)) {
            foreach ($listing_cities as $city) {
                $term_id = $city->term_id;
                $post_count = $city->count;
                $thumbnail_id = get_term_meta($term_id, 'homey_taxonomy_img', true);
                $image_url = wp_get_attachment_image_src($thumbnail_id, array(360, 360));
                if (!$image_url) {
                    $image_url = [get_stylesheet_directory_uri() . '/assets/img/city_placeholder.jpg'];
                }
                ?>
                <div class="col-sm-4 col-xs-6 home_city_card <?= $post_count == 0 ? 'grayed' : '' ?>" style="margin-bottom: 30px;">
                    <div class="taxonomy-item taxonomy-card">
                        <a class="taxonomy-link hover-effect" href="<?php echo esc_url(get_term_link($city)); ?>">
                            <div class="glryContent">
                                <div class="taxonomy-title"><?php echo esc_html($city->name); ?></div>
                                <span class="rhea_pc_counter" style="align-self: flex-end">
                                    <span class="rhea_pc_count"><?= $post_count ?></span>
                                    <span class="rhea_pc_label">Chalet</span>
                                </span>
                            </div>

                            <?php if ($image_url) { ?>
                                <img loading="lazy" decoding="async" class="img-responsive" src="<?php echo esc_url($image_url[0]); ?>"
                                    width="360" height="360" alt="listing_city">
                            <?php } ?>
                        </a>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('listing_cities_grid', 'homey_listing_cities_grid_shortcode');
/***============================================
 * =========================================***/


function add_custom_styles_to_head()
{
    echo '<style>
        input#homey_country {
            pointer-events: none;
            background: #ccc;
        }
    </style>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var homeyCountryField = document.getElementById("homey_country");
            if (homeyCountryField) {
                homeyCountryField.value = "Canada"; // Set the value
            }
        });
    </script>';
}
add_action('wp_head', 'add_custom_styles_to_head');


add_action('init', function () {
    global $wp_rewrite;

    // Change the author base to 'host'
    $wp_rewrite->author_base = 'host';
    $wp_rewrite->flush_rules(); // Flush rewrite rules to apply the changes
});

// 
add_action('init', function () {
    if (isset($_GET['clear-cart']) && $_GET['clear-cart'] == '1') {
        // Clear the cart
        WC()->cart->empty_cart();

        // Redirect to the specified URL
        if (isset($_GET['redirect'])) {
            $redirect_url = esc_url_raw($_GET['redirect']);
            wp_safe_redirect($redirect_url);
            exit;
        }
    }
});

// Redirect
add_action('template_redirect', function () {
    if (is_page('memberships') && isset($_GET['new-user']) && $_GET['new-user'] == '1') {
        wp_redirect(get_home_url() . '/packages');
        exit;
    }
});
///////////////////////////////////////////////////////////////
// Register the admin menu page
add_action('admin_menu', function () {
    add_menu_page(
        'User Subscriptions', // Page title
        'Subscriptions',     // Menu title
        'manage_options',    // Capability
        'user-subscriptions', // Menu slug
        'render_user_subscriptions_page', // Callback function
        'dashicons-admin-users', // Icon
        25                    // Position
    );
});

function render_user_subscriptions_page()
{
    global $wpdb;

    // Fetch all users
    $users = get_users();

    // Fetch subscription packages (WooCommerce products)
    $packages = wc_get_products(['limit' => -1, 'type' => 'simple']); // Adjust product type if needed

    echo '<div class="wrap">';
    echo '<h1>User Subscriptions</h1>';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['package_id'])) {
        $user_id = intval($_POST['user_id']);
        $package_id = intval($_POST['package_id']);
        $start_date = current_time('mysql');
        $expiration_date = date('Y-m-d H:i:s', strtotime('+1 month')); // Example duration

        // Insert or update subscription in the custom table
        $wpdb->replace(
            $wpdb->prefix . 'pms_member_subscriptions',
            [
                'user_id' => $user_id,
                'subscription_plan_id' => $package_id,
                'start_date' => $start_date,
                'expiration_date' => $expiration_date,
                'status' => 'active',
            ],
            ['%d', '%d', '%s', '%s', '%s']
        );

        echo '<div class="updated"><p>Subscription assigned successfully!</p></div>';
    }

    // Display user subscriptions
    echo '<table class="widefat fixed" cellspacing="0">';
    echo '<thead><tr><th>User</th><th>Email</th><th>Subscription</th><th>Action</th></tr></thead>';
    echo '<tbody>';

    foreach ($users as $user) {
        // Fetch subscription from the custom table
        $subscription = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}subscriptions WHERE user_id = %d AND status = 'active'",
                $user->ID
            )
        );

        echo '<tr>';
        echo '<td>' . esc_html($user->display_name) . '</td>';
        echo '<td>' . esc_html($user->user_email) . '</td>';

        if ($subscription) {
            $package = wc_get_product($subscription->subscription_plan_id);
            echo '<td>' . esc_html($package ? $package->get_name() : 'Unknown Package') . '</td>';
            echo '<td>Assigned</td>';
        } else {
            echo '<td>None</td>';
            echo '<td>';
            echo '<form method="post" action="">';
            echo '<select name="package_id">';

            foreach ($packages as $package) {
                echo '<option value="' . esc_attr($package->get_id()) . '">' . esc_html($package->get_name()) . '</option>';
            }

            echo '</select>';
            echo '<input type="hidden" name="user_id" value="' . esc_attr($user->ID) . '">';
            echo '<button type="submit" class="button button-primary">Assign</button>';
            echo '</form>';
            echo '</td>';
        }

        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}




/* overide and add sub */
function create_vip_order() {

    global $woocommerce;
  
    $address = array(
        'first_name' => '111Joe',
        'last_name'  => 'Conlin',
        'company'    => 'Speed Society',
        'email'      => 'test@mail.com',
        'phone'      => '760-555-1212',
        'address_1'  => '123 Main st.',
        'address_2'  => '104',
        'city'       => 'San Diego',
        'state'      => 'Ca',
        'postcode'   => '92121',
        'country'    => 'US'
    );
  
    // Now we create the order
    $order = wc_create_order();
  
    // The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
    $order->add_product( get_product('4742'), 1); // This is an existing SIMPLE product
    $order->set_address( $address, 'billing' );
    //
    $order->calculate_totals();
    $order->update_status("Completed", 'Imported order', TRUE);  
  }
add_action('template_redirect', function () {
    function gift_product_to_user($product_id, $user_id) {
        // Get the WooCommerce instance
        $woocommerce = WC();

        // Check if the user is logged in
        if (is_user_logged_in() && get_current_user_id() == $user_id) {
            // Create a new coupon with 100% discount
            $coupon_code = 'GIFT_PRODUCT'; // Replace with your desired coupon code
            
            // Check if coupon already exists
            $existing_coupon = new WC_Coupon($coupon_code);
            if (!$existing_coupon->get_id()) {
                // Create the coupon if it doesn't exist
                $coupon = new WC_Coupon();
                $coupon->set_code($coupon_code);
                $coupon->set_amount(100); // Set discount to 100%
                $coupon->set_discount_type('percent');
                $coupon->set_individual_use(true);
                $coupon->set_usage_limit(1); // Limit to one use
                $coupon->set_product_ids(array($product_id)); // Assign the product to the coupon
                $coupon->save();
            }

            // Add coupon to the user's cart
            $woocommerce->cart->add_coupon($coupon_code);
        }
    }

    // Example usage:
    $gift_product_id = 4742; // Replace with your product ID
    $user_id = 10; // Replace with the user ID
    // gift_product_to_user($gift_product_id, $user_id);
    // create_vip_order();
    $tmp = new Subscriptions_For_Woocommerce();

    $sub = new Subscriptions_For_Woocommerce_Public($tmp->sfw_get_plugin_name(), $tmp->sfw_get_version());
    
    $sub->wps_sfw_process_checkout_hpos(wc_get_order(5014));
    wps_sfw_update_meta_data( 5021, 'wps_subscription_status', 'active' );
    $subscriptionInfo = array(
        'ID' => '',
        'post_title' => 'test@mail.com',
        'post_content' => '',
        'post_status' => 'publish',
        'post_author' => $user_id,
        'post_type' => "hm_subscriptions"
    );

    //inserting post wp_insert_post() will return ID of inserted post
    $subscription_ID = wp_insert_post($subscriptionInfo);
    $now = time();
    $expiry_date = $now + (1 * 30 * 24 * 60 * 60);

    // update post meta for saved subscription in homey DB
    add_post_meta($subscription_ID, 'hm_subscription_detail_status', 'active');
    // add_post_meta($subscription_ID, 'hm_subscription_detail_payment_gateway', 'stripe');
    // add_post_meta($subscription_ID, 'hm_subscription_detail_order_number', $stripeInvoiceNumber);
    // add_post_meta($subscription_ID, 'hm_subscription_detail_session_id', $_REQUEST['session_id']);
    add_post_meta($subscription_ID, 'hm_subscription_detail_plan_id', '12345');
    add_post_meta($subscription_ID, 'hm_subscription_detail_sub_id', $subscription_ID);
    add_post_meta($subscription_ID, 'hm_subscription_detail_total_listings', 10);
    add_post_meta(12345, 'hm_settings_listings_included', 10);
    add_post_meta($subscription_ID, 'hm_subscription_detail_remaining_listings', 10);
    add_post_meta($subscription_ID, 'hm_subscription_detail_purchase_date', date('d/M/Y h:i:s', $now));
    add_post_meta($subscription_ID, 'hm_subscription_detail_expiry_date', date('d/M/Y h:i:s', $expiry_date));
    //end of save subscription

});

