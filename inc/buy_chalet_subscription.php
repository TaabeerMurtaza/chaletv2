<?php
// buy_chalet_subscription.php

// Load WordPress environment if needed
if ( ! defined( 'ABSPATH' ) ) {
    require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );
}

// Query WooCommerce subscription products
$args = array(
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'meta_query'     => array(
        array(
            'key'     => '_ywsbs_subscription',
            'compare' => 'EXISTS',
        ),
    ),
);

$subscriptions = new WP_Query( $args );

?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Subscriptions</title>
    <style>
        .subscription-list { display: flex; flex-wrap: wrap; gap: 24px; }
        .subscription-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            background: #fff;
        }
        .subscription-card h2 { margin-top: 0; }
        .subscription-card img { max-width: 100%; height: auto; border-radius: 4px; }
        .subscription-price { font-size: 1.2em; color: #2a7d2e; margin: 10px 0; }
        .subscription-period { color: #555; }
        .buy-btn {
            display: inline-block;
            padding: 8px 16px;
            background: #2a7d2e;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Available Subscriptions</h1>
    <div class="subscription-list">
        <?php if ( $subscriptions->have_posts() ) : ?>
            <?php while ( $subscriptions->have_posts() ) : $subscriptions->the_post(); global $product; ?>
                <div class="subscription-card">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'medium' ); ?>
                    <?php endif; ?>
                    <h2><?php the_title(); ?></h2>
                    <div class="subscription-price">
                        <?php echo $product->get_price_html(); ?>
                    </div>
                    <div class="subscription-period">
                        <?php
                        $period = get_post_meta( get_the_ID(), '_subscription_period', true );
                        $interval = get_post_meta( get_the_ID(), '_subscription_period_interval', true );
                        if ( $period && $interval ) {
                            echo esc_html( "Every {$interval} {$period}(s)" );
                        }
                        ?>
                    </div>
                    <div>
                        <?php the_excerpt(); ?>
                    </div>
                    <form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
                        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( get_the_ID() ); ?>">
                        <button type="submit" class="buy-btn">Buy Now</button>
                    </form>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        <?php else : ?>
            <p>No subscription products found.</p>
        <?php endif; ?>
    </div>
</body>
</html>