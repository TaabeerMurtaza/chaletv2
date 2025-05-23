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
    <title>Dashboard</title>
    <link rel="stylesheet" href="<?= get_template_directory_uri() ?>/dashboard/css/style.css" />
</head>

<body>
    <header>
        <div class="header-inner">
            <a href="#" class="logo "><img src="<?= get_template_directory_uri() ?>/dashboard/images/LOGO.svg" alt=""></a>
            <nav id="main-nav-db">
                <ul>
                    <li><a href="#">home</a></li>
                    <li><a href="#">Chalets</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Faq</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>

            <div class="profile-col">
                <div class="pro-icon"></div>
                <span class="pro-name">my account</span>
            </div>
            <div class="menu-icon mb" onclick="toggleMenu()"><img src="<?= get_template_directory_uri() ?>/dashboard/images/menu.svg" alt=""></div>
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
                        <div class="summary-detail">
                            <span>21</span>
                            <p>Chalets Published</p>
                        </div>
                        <div class="summary-detail">
                            <span>16</span>
                            <p>Chalets in Management</p>
                        </div>
                        <div class="summary-detail">
                            <span>2</span>
                            <p>Pending chalets</p>
                        </div>
                        <div class="summary-detail">
                            <span>23</span>
                            <p>Services Published</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-tab-links">
                    <ul>
                        <li>
                            <button data-tab="tab1" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Chart.svg" alt="tab-icon">
                                Dashboard
                            </button>
                        </li>
                        <li>
                            <button data-tab="tab2" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/User_cicrle.svg" alt="tab-icon">
                                My Profile
                            </button>
                        </li>
                        <li>
                            <button data-tab="tab3" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Chield_check.svg" alt="tab-icon">
                                Subscriptions
                            </button>
                        </li>
                        <li>
                            <button data-tab="tab4" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Home.svg" alt="tab-icon">
                                Chalets
                            </button>
                        </li>
                        <li>
                            <button data-tab="tab5" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Calendar_add.svg" alt="tab-icon">
                                Booking Calendar
                            </button>
                        </li>
                        <li>
                            <button data-tab="tab6" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Book_check.svg" alt="tab-icon">
                                My Bookings
                            </button>
                        </li>
                        <li>
                            <button data-tab="tab7" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/thumb_up.svg" alt="tab-icon">
                                My Reviews
                            </button>
                        </li>
                        <li>
                            <button data-tab="tab8" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Chat_plus.svg" alt="tab-icon">
                                My Inbox
                            </button>
                        </li>
                        <li>
                            <button data-tab="tab9" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Paper.svg" alt="tab-icon">
                                Invoices
                            </button>
                        </li>
                        <li>
                            <button data-tab="tab10" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Group.svg" alt="tab-icon">
                                Partners and services
                            </button>
                        </li>
                        <li>
                            <button data-tab="tab11" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Subttasks.svg" alt="tab-icon">
                                Attractions and activities
                            </button>
                        </li>
                        <li>
                            <button data-tab="tab12" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Bag_alt.svg" alt="tab-icon">
                                Tool box
                            </button>
                        </li>
                        <li>
                            <button data-tab="tab13" class="tab-link">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/Sign_in_squre.svg" alt="tab-icon">
                                Log Out
                            </button>
                        </li>
                    </ul>
                </div>
            </div>