<?php
// Subscriptions.php

add_action('init', 'register_chalet_subscription_meta');
function register_chalet_subscription_meta() {
    \Carbon_Fields\Container::make('post_meta', 'Chalet Subscription')
        ->where('post_type', '=', 'chalet')
        ->add_fields([
            \Carbon_Fields\Field::make('association', 'chalet_subscriptions', 'Linked Subscription Products')
                ->set_types([
                    ['type' => 'post', 'post_type' => 'product'],
                ])
                ->set_max(5)
                ->set_help_text('Link WooCommerce subscription products to this chalet.'),
        ]);
}

// Display subscription products on single chalet page
add_action('the_content', 'display_chalet_subscriptions');
function display_chalet_subscriptions($content) {
    if (!is_singular('chalet')) return $content;

    $linked = carbon_get_the_post_meta('chalet_subscriptions');
    if (!$linked || !is_array($linked)) return $content;

    $output = '<h3>Subscribe to this Chalet:</h3><ul class="chalet-sub-list">';
    foreach ($linked as $product_id) {
        $product = wc_get_product($product_id);
        if ($product && $product->is_type('simple')) {
            $output .= '<li>' . $product->get_name() . ' - ' . $product->get_price_html();
            $output .= do_shortcode('[add_to_cart id="' . $product_id . '"]') . '</li>';
        }
    }
    $output .= '</ul>';

    return $content . $output;
}
