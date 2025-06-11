<?php get_header('dashboard'); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/dashboard/css/subscriptions.css">
<div class="dashboard-content sub_main_section">
    <div class="dashboard-title">
        <button class="menu-btn openPanel"><img src="images/slide-icon.svg" alt=""></button>
        <h2 class="main-title"> Subscribe to a package to proceed </h2>
        <div class="dashboard-title-details">
            <a href="<?= get_home_url() ?>" class="dashboard-top-btn btn-h">Home page</a>
            <button class="shop-btn">
                <img src="<?= get_template_directory_uri() ?>/assets/images/icons/bell.svg" alt="" />
                <span class="notife">2</span>
            </button>
        </div>
    </div>
    <br>
    <br>
    <div class="subs-card-row">
        <?php
        $args = array(
            'post_type' => 'subscription_plan',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'menu_order',
            'order' => 'ASC',
        );

        $plans = new WP_Query($args);

        if ($plans->have_posts()):
            while ($plans->have_posts()):
                $plans->the_post(); ?>
                <!--  -->
                <div class="subs-card">
                    <div class="subs"
                        style="background-color: <?php echo esc_attr(carbon_get_post_meta(get_the_ID(), 'subscription_color')); ?>;">
                        <div class="icon-container">
                            <img src="<?= carbon_get_post_meta(get_the_ID(), 'subscription_icon') ?: get_template_directory_uri() . '/assets/images/icons/check.svg' ?>"
                                alt="House Icon" class="icon" />
                        </div>
                        <div class="subs-content">
                            <h2 class="subs-heading"><?= get_the_title() ?></h2>
                            <?php
                            // $chalets_allowed = carbon_get_post_meta(get_the_ID(), 'chalets_allowed');
                            // $featured_allowed = carbon_get_post_meta(get_the_ID(), 'featured_allowed');
                            echo wp_kses_post(carbon_get_post_meta(get_the_ID(), 'subscription_description'));
                            ?>
                        </div>
                        <button class="subs-btn" onclick="show_secondary(<?= get_the_ID() ?>)">SEE OUR PACKAGES</button>
                    </div>
                </div>
                <!--  -->
            <?php endwhile;
            wp_reset_postdata(); ?>
        <?php else: ?>
            <p>No subscription plans found.</p>
        <?php endif; ?>

    </div>
    <div class="divider"></div>
    <div class="contact-box">
        <div class="contact-box-inner">
            <h3>Need help choosing the perfect package for your chalet(s)?</h3>
            <a href="#">Contact us to discuss!</a>
            <div class="contact-details">
                <a href="#"><img src="<?= get_template_directory_uri() ?>/assets/images/icons/mail.svg"
                        alt="Email Icon">info@booktonchalet.com</a>
                <a href="#" class="phone-link"><img
                        src="<?= get_template_directory_uri() ?>/assets/images/icons/phone.svg" alt="Phone Icon">(581)
                    814-2225</a>
            </div>
        </div>
    </div>
</div>


<!--  -->
<?php
$args = array(
    'post_type' => 'subscription_plan',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'menu_order',
    'order' => 'ASC',
);

$plans = new WP_Query($args);

if ($plans->have_posts()):
    while ($plans->have_posts()):
        $plans->the_post(); ?>
        <section class="inner-details-section dashboard-section dashboard-content sub_secondary_section"
            id="sub_secondary_<?= get_the_ID() ?>" style="display: none;">
            <div class="dashboard-title">
                <button class="menu-btn openPanel"><img src="images/slide-icon.svg" alt=""></button>
                <h2 class="main-title"><?= get_the_title() ?></h2>
                <div class="dashboard-title-details">
                    <a href="<?= get_home_url() ?>" class="dashboard-top-btn btn-h">Home page</a>
                    <button class="shop-btn">
                        <img src="<?= get_template_directory_uri() ?>/assets/images/icons/bell.svg" alt="" />
                        <span class="notife">2</span>
                    </button>
                </div>
            </div>
            <div class="main-inner-details">
                <div class="top-inner-wrapper">
                    <div class="top-logo-bar">
                        <div class="logo-bg-bar " style="background-color:  <?php echo esc_attr(carbon_get_post_meta(get_the_ID(), 'subscription_color')); ?>;"></div>
                        <div class="logo-circle">
                            <img src="<?= carbon_get_post_meta(get_the_ID(), 'subscription_icon') ?: get_template_directory_uri() . '/assets/images/icons/check.svg' ?>" alt="Logo" />
                        </div>
                    </div>
                </div>
                <div class="subs-card">
                    <div class="subs " style="background-color:  <?php echo esc_attr(carbon_get_post_meta(get_the_ID(), 'subscription_color')); ?>;">

                        <div class="subs-content">
                            <h2 class="subs-heading">NON-EXCLUSIVE MANAGEMENT PACKAGE</h2>
                            <?= carbon_get_post_meta(get_the_ID(), 'non_exclusive_description') ?>
                            <button class="subs-btn left-btn" onclick="show_secondary(<?= get_the_ID() ?>, true)">SEE OUR PACKAGES</button>
                        </div>
                        <div class="icon-container">
                            <img src="<?= carbon_get_post_meta(get_the_ID(), 'subscription_icon') ?: get_template_directory_uri() . '/assets/images/icons/check.svg' ?>" alt="House Icon" class="icon" />
                        </div>
                        <div class="subs-content">
                            <h2 class="subs-heading">COMMISSIONS</h2>
                            <?= carbon_get_post_meta(get_the_ID(), 'non_exclusive_commission') ?>
                        </div>

                    </div>
                </div>
            </div>
        </section>
        <!--  -->
        
        <section class="inner-details-section dashboard-section dashboard-content sub_tertiary_section"
            id="sub_tertiary_<?= get_the_ID() ?>" style="display: none;">
            <div class="dashboard-title">
                <button class="menu-btn openPanel"><img src="images/slide-icon.svg" alt=""></button>
                <h2 class="main-title"><?= get_the_title() ?></h2>
                <div class="dashboard-title-details">
                    <a href="<?= get_home_url() ?>" class="dashboard-top-btn btn-h">Home page</a>
                    <button class="shop-btn">
                        <img src="<?= get_template_directory_uri() ?>/assets/images/icons/bell.svg" alt="" />
                        <span class="notife">2</span>
                    </button>
                </div>
            </div>
            <div class="main-inner-details">
                <div class="top-inner-wrapper">
                    <div class="top-logo-bar">
                        <div class="logo-bg-bar " style="background-color:  <?php echo esc_attr(carbon_get_post_meta(get_the_ID(), 'subscription_color')); ?>;"></div>
                        <div class="logo-circle">
                            <img src="<?= carbon_get_post_meta(get_the_ID(), 'subscription_icon') ?: get_template_directory_uri() . '/assets/images/icons/check.svg' ?>" alt="Logo" />
                        </div>
                    </div>
                </div>
                <div class="subs-card">
                    <div class="subs " style="background-color:  <?php echo esc_attr(carbon_get_post_meta(get_the_ID(), 'subscription_color')); ?>;">

                        <div class="subs-content">
                            <h2 class="subs-heading">EXCLUSIVE MANAGEMENT PACKAGE</h2>
                            <?= carbon_get_post_meta(get_the_ID(), 'exclusive_description') ?>
                            <button class="subs-btn left-btn" onclick="window.location='<?= get_the_permalink() ?>'">BUY PACKAGES</button>
                        </div>
                        <div class="icon-container">
                            <img src="<?= carbon_get_post_meta(get_the_ID(), 'subscription_icon') ?: get_template_directory_uri() . '/assets/images/icons/check.svg' ?>" alt="House Icon" class="icon" />
                        </div>
                        <div class="subs-content">
                            <h2 class="subs-heading">COMMISSIONS</h2>
                            <?= carbon_get_post_meta(get_the_ID(), 'exclusive_commission') ?>
                        </div>

                    </div>
                </div>
            </div>
        </section>
        <!--  -->

    <?php endwhile;
    wp_reset_postdata(); ?>
<?php else: ?>
<?php endif; ?>
<script>
    function show_secondary(id, _tertiary=false) {
        var secondary = document.getElementById('sub_secondary_' + id);
        var tertiary = document.getElementById('sub_tertiary_' + id);
        document.querySelector('.sub_main_section').style.display = 'none';
        if(_tertiary) {
            tertiary.style.display = "block";
            secondary.style.display = "none";
        } else {
            secondary.style.display = "block";
            tertiary.style.display = "none";
        }
    }

</script>
<?php get_footer('dashboard') ?>