<?php
/**
 * Template Name: Dashboard Subscribe
 *  */
get_header('dashboard');
?>

<div class="dashboard-subscribe">
    <style>
        .subscription-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
            margin: 2rem 0;
        }
        .subscription-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 2rem;
            width: 320px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.2s;
        }
        .subscription-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        }
        .plan-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        .plan-content {
            font-size: 1rem;
            color: #555;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .plan-price {
            font-size: 2rem;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 1.5rem;
        }
        .subscribe-btn {
            background: #27ae60;
            color: #fff;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            transition: background 0.2s;
        }
        .subscribe-btn:hover {
            background: #219150;
        }
    </style>
    <?php
    $args = array(
        'post_type'      => 'subscription_plan',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    );

    $plans = new WP_Query($args);

    if ($plans->have_posts()) : ?>
        <div class="subscription-cards">
            <?php while ($plans->have_posts()) : $plans->the_post(); ?>
            <div class="subscription-card">
                <h2 class="plan-title"><?php the_title(); ?></h2>
                <div class="plan-content">
                <?php
                $description = carbon_get_post_meta(get_the_ID(), 'subscription_description');
                echo esc_html($description);
                ?>
                </div>
                <?php
                $price = carbon_get_post_meta(get_the_ID(), 'subscription_price');
                $interval = carbon_get_post_meta(get_the_ID(), 'subscription_interval');
                $interval_duration = carbon_get_post_meta(get_the_ID(), 'subscription_interval_duration');
                if ($price && $interval && $interval_duration) :
                $interval_label = ucfirst($interval);
                ?>
                <div class="plan-price">
                    <?php
                    $currency = get_option('woocommerce_currency');
                    $currency_symbol = get_woocommerce_currency_symbol($currency);
                    echo esc_html($currency_symbol . ' ' . $price);
                    ?>
                    <span style="font-size:1rem;font-weight:normal;color:#888;">
                    / <?php echo esc_html($interval_duration . ' ' . $interval_label . ($interval_duration > 1 ? 's' : '')); ?>
                    </span>
                </div>
                <?php endif; ?>
                <div style="margin-bottom:1rem;">
                <?php
                $chalets_allowed = carbon_get_post_meta(get_the_ID(), 'chalets_allowed');
                $featured_allowed = carbon_get_post_meta(get_the_ID(), 'featured_allowed');
                ?>
                <div>Chalets Allowed: <strong><?php echo esc_html($chalets_allowed); ?></strong></div>
                <div>Featured Allowed: <strong><?php echo esc_html($featured_allowed); ?></strong></div>
                </div>
                <a href="<?= get_the_permalink() ?>" class="subscribe-btn">Subscribe</a>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    <?php else : ?>
        <p>No subscription plans found.</p>
    <?php endif; ?>

<?php
get_footer('dashboard');