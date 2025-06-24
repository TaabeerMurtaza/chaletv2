<?php
if (!is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php wp_title('|', true, 'right');
    bloginfo('name'); ?></title>
    <link rel="stylesheet"
        href="<?= get_template_directory_uri() ?>/dashboard/css/style.css?v=<?= filemtime(get_template_directory() . '/dashboard/css/style.css') ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/awesome-notifications/3.1.0/style.min.css"
        integrity="sha512-OFAsS5R1Fx+HUK9/h/ChqnFDrJGI0Y7nO05gg9E+Mv1UAzvAMvQdtOuPLhgPgDPHOgKWBvbovxT3eQSCr5hlLw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/awesome-notifications/3.1.0/index.var.min.js"
        integrity="sha512-gS8jKzzlhaUACXtBbUmj9/ITyZEAMM5TNwcL2Y226Xh6J/xH8mYzm6C/tHFkRVbi+tV1uyW7pIMSTehhBt6sBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        const home_url = '<?= get_home_url() ?>';
        const theme_url = '<?= get_template_directory_uri() ?>';
        const ajax_url = '<?= admin_url('admin-ajax.php') ?>';
        const site_url = '<?= site_url() ?>';

        let notifier = new AWN({});
    </script>
    <?php wp_head(); ?>
</head>

<body>
    <header class="dashboard-header">
        <div class="header-inner">
            <a href="#" class="logo "><img src="<?= get_template_directory_uri() ?>/dashboard/images/LOGO.svg"
                    alt=""></a>
            <nav id="main-nav-db">
                <?php
                wp_nav_menu([
                    'theme_location' => 'main_nav',
                    'container' => false,
                    'menu_class' => '',
                    'items_wrap' => '<ul>%3$s</ul>',
                ]);
                ?>
            </nav>

            <div class="profile-col">
                <div class="pro-icon"></div>
                <span class="pro-name">my account</span>
            </div>
            <div class="menu-icon mb" onclick="toggleMenu()"><img
                    src="<?= get_template_directory_uri() ?>/dashboard/images/menu.svg" alt=""></div>
        </div>
    </header>
    <div class="dashboard-section">
        <div class="tab-overlay"></div>
        <div class="container">
            <div class="sildepanel">
                <div class="top-logo">
                    <img src="<?= get_template_directory_uri() ?>/dashboard/images/sidepanel-logo.png" alt="" />
                </div>
                <span class="eyebrow-text">Hello, Book Ton Chalet</span>
                <div class="side-top-details">
                    <span class="dashboard-span">Book Ton chalets Admin</span>
                    <h3 class="dashboard-sub-title">Summary</h3>
                    <div class="summary-details-row">
                        <?php
                        // Get current user ID
                        $user_id = get_current_user_id();

                        // Count published chalets (assuming 'chalet' is the post type)
                        $published_chalets = new WP_Query([
                            'post_type' => 'chalet',
                            'post_status' => 'publish',
                            'author' => $user_id,
                            'posts_per_page' => -1,
                            'fields' => 'ids'
                        ]);
                        $published_chalets_count = $published_chalets->found_posts;

                        // Count chalets in management (custom logic, adjust as needed)
                        $managed_chalets = new WP_Query([
                            'post_type' => 'chalet',
                            'post_status' => 'any',
                            'author' => $user_id,
                            'posts_per_page' => -1,
                            'fields' => 'ids'
                        ]);
                        $managed_chalets_count = $managed_chalets->found_posts;

                        // Count pending chalets
                        $pending_chalets = new WP_Query([
                            'post_type' => 'chalet',
                            'post_status' => 'pending',
                            'author' => $user_id,
                            'posts_per_page' => -1,
                            'fields' => 'ids'
                        ]);
                        $pending_chalets_count = $pending_chalets->found_posts;

                        // Count published services (assuming 'service' is the post type)
                        $published_services = new WP_Query([
                            'post_type' => 'service',
                            'post_status' => 'publish',
                            'author' => $user_id,
                            'posts_per_page' => -1,
                            'fields' => 'ids'
                        ]);
                        $published_services_count = $published_services->found_posts;
                        ?>

                        <div class="summary-detail">
                            <span><?= esc_html($published_chalets_count); ?></span>
                            <p>Chalets Published</p>
                        </div>
                        <div class="summary-detail">
                            <span><?= esc_html($managed_chalets_count); ?></span>
                            <p>Chalets in Management</p>
                        </div>
                        <div class="summary-detail">
                            <span><?= esc_html($pending_chalets_count); ?></span>
                            <p>Pending chalets</p>
                        </div>
                        <div class="summary-detail">
                            <span><?= esc_html($published_services_count); ?></span>
                            <p>Services Published</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-tab-links">
                    <ul>
                        <li>
                        <li class="">
                            <a href="<?= get_home_url() ?>/dashboard">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Chart.svg"
                                    alt="tab-icon">
                                Dashboard
                            </a>
                        </li>
                        </li>
                        <li>
                            <a href="<?= get_home_url() ?>/dashboard-profile" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/User_cicrle.svg"
                                    alt="tab-icon">
                                My Profile
                            </a>
                        </li>
                        <li>
                            <a class="tab-link" href="<?= get_home_url() ?>/dashboard-subscriptions">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Chield_check.svg"
                                    alt="tab-icon">
                                Subscriptions
                            </a>
                        </li>
                        <li>
                            <a href="<?= get_home_url() ?>/dashboard-chalets" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Home.svg"
                                    alt="tab-icon">
                                Chalets
                            </a>
                        </li>
                        <li>
                            <a href="<?= get_home_url() ?>/dashboard-calender" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Calendar_add.svg"
                                    alt="tab-icon">
                                Booking Calendar
                            </a>
                        </li>
                        <li>
                            <a href="<?= get_home_url() ?>/booking" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Book_check.svg"
                                    alt="tab-icon">
                                My Bookings
                            </a>
                        </li>
                        <li>
                            <a href="<?= get_home_url() ?>/dashboard-reviews" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/thumb_up.svg"
                                    alt="tab-icon">
                                My Reviews
                            </a>
                        </li>
                        <li>
                            <a href="<?= get_home_url() ?>/dashboard-chats" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Chat_plus.svg"
                                    alt="tab-icon">
                                My Inbox
                            </a>
                        </li>
                        <li>
                            <a class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Paper.svg"
                                    alt="tab-icon">
                                Invoices
                            </a>
                        </li>
                        <li>
                            <a class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Group.svg"
                                    alt="tab-icon">
                                Partners and services
                            </a>
                        </li>
                        <li>
                            <a class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Subttasks.svg"
                                    alt="tab-icon">
                                Attractions and activities
                            </a>
                        </li>
                        <li>
                            <a class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Bag_alt.svg"
                                    alt="tab-icon">
                                Tool box
                            </a>
                        </li>
                        <li>
                            <a class="tab-link" href="<?= esc_url(wp_logout_url(home_url('/login'))) ?>">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Sign_in_squre.svg"
                                    alt="tab-icon">
                                Log Out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>