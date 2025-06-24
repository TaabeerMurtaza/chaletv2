<?php

/**
 * Template Name: Add new Booking
 *  */

$chalet_id = $_GET['id'] ?? null;
if (!$chalet_id) {
    wp_redirect(home_url(''));
    exit;
}
$chalet = get_post($chalet_id);
if (!$chalet) {
    wp_redirect(home_url(''));
    exit;
}
// print_r($_POST);
// exit;
// Array ( [arrival_date] => 2025-06-17 [departure_date] => 2025-06-28 [guests] => 1 Guests [adult_guests] => 1 [child_guests] => 0 ) 
$checkin_date = $_POST['arrival_date'] ?? false;
$checkout_date = $_POST['departure_date'] ?? false;
$adults = $_POST['adult_guests'] ?? 0;
$children = $_POST['child_guests'] ?? 0;
$errors = [];
$gst = carbon_get_post_meta($chalet_id, 'tax_gst') ?: 0; // Default GST rate
$thq = carbon_get_post_meta($chalet_id, 'tax_thq') ?: 0; // Default THQ rate
$qst = carbon_get_post_meta($chalet_id, 'tax_qst') ?: 0; //

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$checkin_date) {
        $errors[] = 'Check-in date is required.';
    }
    if (!$checkout_date) {
        $errors[] = 'Check-out date is required.';
    }
    if (!is_numeric($adults) || $adults < 1) {
        $errors[] = 'At least one adult is required.';
    }
    if (!is_numeric($children) || $children < 0) {
        $errors[] = 'Children count must be zero or more.';
    }
    if ($checkin_date && $checkout_date && strtotime($checkin_date) >= strtotime($checkout_date)) {
        $errors[] = 'Check-out date must be after check-in date.';
    }
}
if ($errors) {
    wp_redirect(get_permalink($chalet_id) ?: home_url(''));
    exit;
}
$guests_summary = "{$adults} adults, {$children} children";
$per_night = carbon_get_post_meta($chalet_id, 'default_rate_weekday');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= get_the_title() ?></title>
    <link rel="stylesheet"
        href="<?= get_template_directory_uri() ?>/dashboard/css/new-booking.css?v=<?= filemtime(get_template_directory() . '/dashboard/css/new-booking.css') ?>" />
    <style>
        section.now-set {
            display: none;
        }

        section.now-set.active {
            display: block;
        }

        .fixed_main {
            display: grid;
            grid-template-columns: 2fr 1fr;
            padding: 0 2rem 0 0;
        }
    </style>
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
</head>
<div class="filler-row">
    <div class="container">
        <div class="filler-left">
            <span class="fil-bb active dates-link">Dates</span>
            <p>></p>
            <span class="fil-bb add-ons-link">Add-ons</span>
            <p>></p>
            <span class="fil-bb contact-set-link">Contact</span>
            <p>></p>
            <span class="fil-bb payment-link">Payment</span>
        </div>
    </div>
</div>
<div class="fixed_main">
    <section class="now-set dates-section active" data-id="dates">
        <div class="container">
            <h2 class="inner-heading">Dates</h2>
            <div class="date-details-row">
                <div class="dates-left">
                    <div class="date-selector">
                        <div class="time-details">
                            <span class="label">Check-in</span>
                            <span class="date"><?= $checkin_date ?></span>
                        </div>
                        <div class="time-details">
                            <span class="label">Check-out</span>
                            <span class="date"><?= $checkout_date ?></span>
                        </div>
                        <!-- <button class="close-btn">&times;</button> -->
                    </div>
                </div>
                <div class="dates-mid">
                    <div class="guest-dropdown" onclick="togglePopup()">
                        <div class="guest-label">Guests</div>
                        <div class="guest-summary-container">
                            <div class="guest-summary" id="guestSummary"><?= $guests_summary ?></div>
                            <!-- <div id="dropdownIcon" class="dropdown-icon">▼</div> -->
                        </div>
                    </div>

                    <div class="guest-popup" id="guestPopup">
                        <div class="guest-row">
                            <div class="guest-info">
                                <span class="title">adults</span>
                                <span class="desc">Ages 13 or above</span>
                            </div>
                            <div class="controls">
                                <button onclick="changeCount('adults', -1)">-</button>
                                <input type="text" id="adults" value="18" readonly />
                                <button onclick="changeCount('adults', 1)">+</button>
                            </div>
                        </div>
                        <div class="guest-row">
                            <div class="guest-info">
                                <span class="title">children</span>
                                <span class="desc">Ages 2-12</span>
                            </div>
                            <div class="controls">
                                <button onclick="changeCount('children', -1)">-</button>
                                <input type="text" id="children" value="2" readonly />
                                <button onclick="changeCount('children', 1)">+</button>
                            </div>
                        </div>
                        <div class="guest-row">
                            <div class="guest-info">
                                <span class="title">infants</span>
                                <span class="desc">Under 2</span>
                            </div>
                            <div class="controls">
                                <button onclick="changeCount('infants', -1)">-</button>
                                <input type="text" id="infants" value="0" readonly />
                                <button onclick="changeCount('infants', 1)">+</button>
                            </div>
                        </div>
                        <div class="guest-row disabled">
                            <div class="guest-info">
                                <span class="title">pets</span>
                                <span class="desc">Not allowed</span>
                            </div>
                            <div class="controls">
                                <button disabled>-</button>
                                <input type="text" value="0" readonly />
                                <button disabled>+</button>
                            </div>
                        </div>
                        <button class="done-btn" onclick="togglePopup()">Done</button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="now-set add-section " id="add-ons">
        <div class="container">
            <h2 class="inner-heading">Add-ons</h2>
            <p class="section-para">Enjoy an exceptional stay by adding extras.</p>
            <div class="date-details-row">
                <div class="dates-left">
                    <!-- Early check-in option -->
                    <!-- <div class="option-card">
                        <div class="option-image">
                            <img src="/images/form-img.jpg" alt="">
                        </div>
                        <div class="option-content">
                            <div class="option-header">
                                <h3 class="option-title">Early check-in</h3>
                                <div class="option-price">200,00$</div>
                            </div>
                            <div class="option-description">
                                <div class="desc-left">
                                    <p>Want to enjoy the chalet longer?</p>
                                    <p>Add <strong>late check-out</strong> to your stay for a <strong>5 pm</strong>
                                        departure.</p>
                                </div>
                                <div class="option-bottom">
                                    <div></div>
                                    <button class="add-button">+</button>
                                </div>

                            </div>

                        </div>
                    </div> -->

                    <!-- Kayak location option -->
                    <?php
                    // Fetch extra options for this chalet (Carbon Fields 'complex' field)
                    $extra_options = carbon_get_post_meta($chalet_id, 'extra_options');
                    if (!empty($extra_options)):
                        foreach ($extra_options as $idx => $option):
                            $name = esc_html($option['name'] ?? 'Extra Option');
                            $price = isset($option['price']) ? number_format((float) $option['price'], 2, ',', ' ') : '0,00';
                            $type = $option['type'] ?? 'per_stay';
                            ?>
                            <div class="option-card" data-addon-idx="<?= $idx ?>">
                                <div class="option-image">
                                    <img src="<?= get_template_directory_uri(); ?>/assets/images/kayak.jpg" alt="">
                                </div>
                                <div class="option-content">
                                    <div class="option-header">
                                        <h3 class="option-title"><?= $name ?></h3>
                                        <div class="option-price price_<?= $type ?>">
                                            <?= $price ?>$<?= $type === 'per_item' ? '/item' : '' ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($option['description'])): ?>
                                        <div class="option-description"><?= esc_html($option['description']) ?></div>
                                    <?php endif; ?>
                                    <div class="option-bottom">
                                        <?php if ($type === 'per_item'): ?>
                                            <div class="quantity-selector">
                                                <select id="extra-qty-<?= $idx ?>" name="extra_qty[<?= $idx ?>]">
                                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                                        <option value="<?= $i ?>"><?= $i ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        <?php else: ?>
                                            <div class="blank"></div>
                                        <?php endif; ?>
                                        <button class="remove-button" data-extra-idx="<?= $idx ?>"
                                            data-extra-name="<?= esc_attr($name) ?>" style="display:none;"
                                            onclick="remove_addon(this)">-</button>
                                        <button class="add-button" data-extra-idx="<?= $idx ?>"
                                            data-extra-name="<?= esc_attr($name) ?>"
                                            data-extra-price="<?= esc_attr($option['price']) ?>"
                                            data-extra-type="<?= esc_attr($type) ?>" onclick="add_addon(this)">+</button>
                                    </div>
                                </div>
                            </div>
                            <?php
                        endforeach;
                    else:
                        ?>
                        <p>No add-ons available for this chalet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <section class="now-set add-section " id="contact-set">
        <div class="container">
            <h2 class="inner-heading">Your contact details</h2>
            <div class="inner-divider"></div>
            <div class="date-details-row">
                <div class="dates-left">
                    <div class="form-container">
                        <form id="contactForm">
                            <!-- Email -->
                            <div class="form-group" style="height:max-content;">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="" required>
                            </div>

                            <!-- First name and Last name -->
                            <div class="form-row">
                                <div class="form-group" style="height:max-content;">
                                    <label for="firstName">First name</label>
                                    <input type="text" id="firstName" name="firstName" value="" required>
                                </div>
                                <div class="form-group" style="height:max-content;">
                                    <label for="lastName">Last name</label>
                                    <input type="text" id="lastName" name="lastName" value="" required>
                                </div>
                            </div>

                            <!-- Country and Phone number -->
                            <div class="form-row">
                                <div class="form-group" style="height:max-content;">
                                    <label for="country">Country</label>
                                    <div class="country-select">
                                        <select id="country" name="country">
                                            <option value="canada" selected>Canada</option>
                                            <option value="usa">United States</option>
                                            <option value="uk">United Kingdom</option>
                                            <option value="france">France</option>
                                            <option value="germany">Germany</option>
                                            <option value="australia">Australia</option>
                                            <option value="japan">Japan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" style="height:max-content;">
                                    <label for="phone">Phone number</label>
                                    <div class="phone-input">
                                        <div class="flag-select">
                                            <span class="flag-icon"></span>
                                        </div>
                                        <input type="text" id="phone" name="phone" class="phone-number" value=""
                                            required>
                                    </div>
                                </div>
                            </div>

                            <!-- Comments -->
                            <div class="form-group" style="height:max-content;">
                                <label for="comments">Comments (optional)</label>
                                <textarea id="comments" name="comments"
                                    placeholder="Write any additional questions or let us know about anything else you may need here."></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="now-set add-section " id="payment">
        <div class="container">
            <h2 class="inner-heading">Payment details-Credit card</h2>
            <div class="inner-divider"></div>
            <div class="date-details-row">
                <div class="dates-left">
                    <div class="payment-container">
                        <div class="payment-title">Card</div>

                        <div class="relative-wrapper">
                            <input type="text" class="card-input" placeholder="Card number">
                            <div class="card-icons">
                                <img src="https://img.icons8.com/color/48/000000/visa.png" />
                                <img src="https://img.icons8.com/color/48/000000/mastercard.png" />
                                <img src="https://img.icons8.com/color/48/000000/amex.png" />
                                <img src="https://img.icons8.com/color/48/000000/unionpay.png" />
                            </div>
                        </div>

                        <div class="input-row">
                            <input type="text" class="card-input" placeholder="Expiration date">
                            <input type="text" class="card-input security-icon" placeholder="Security code">
                        </div>

                        <div class="note">
                            By providing your card information, you allow Logo to charge your card for future payments
                            in accordance with their terms.
                        </div>
                        <!-- <hr> -->
                        <!-- <div class="google-pay">
                            <img src="images/g-pay.png" alt="GPay">
                            Google Pay
                        </div> -->

                    </div>
                    <div class="form-group credit-form">
                        <label for="fullName">Full name (on card)</label>
                        <input type="text" id="fullName" name="fullName" value="">
                    </div>
                    <div class="form-group credit-form">
                        <label for="country">Country</label>
                        <div class="country-select">
                            <select id="country" name="country">
                                <option value="canada" selected>Canada</option>
                                <option value="usa">United States</option>
                                <option value="uk">United Kingdom</option>
                                <option value="france">France</option>
                                <option value="germany">Germany</option>
                                <option value="australia">Australia</option>
                                <option value="japan">Japan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group credit-form">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" value="">
                    </div>
                    <div class="policy-container">

                        <h4>Cancellation policy</h4>
                        <ul>
                            <li>100% of paid prepayments refundable when canceled 90 day(s) before arrival or earlier.
                            </li>
                            <li>0% refundable if canceled after.</li>
                        </ul>

                        <h4>Security deposit policy</h4>
                        <p>A pre-authorization of CAD 1000.00 is held on 1 day(s) before arrival and voided on 7 day(s)
                            after departure</p>

                        <div class="checkbox-section">
                            <input type="checkbox" id="accept-checkbox">
                            <label for="accept-checkbox">
                                I have read and accept the following information: <a href="#">Rental agreement</a>.
                            </label>
                        </div>

                        <div class="divider"></div>

                        <div class="security-info">
                            <b>Security & Payments</b>
                            <p>This site is protected by reCAPTCHA. Google's <a href="#">Privacy policy</a> and <a
                                    href="#">Terms of service</a> apply.</p>
                            <div class="security-icons">
                                <img src="images/g-shild.png" alt="Google Shield">
                                <!-- <img src="https://www.pcisecuritystandards.org/images/pci_logo.png" alt="PCI DSS"> -->
                            </div>
                        </div>
                        <button class="continue-btn" onclick="bookNow()">Book Now</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="fixed_right">
        <div class="dates-right">
            <div class="summary-card">
                <h2>Reservation summary</h2>
                <hr>
                <div class="summary-header">
                    <div class="summary-left">
                        <img src="<?= get_the_post_thumbnail_url($chalet) ?>" alt="Chalet Image">
                        <span class="small-text logo-name"><?= get_the_title($chalet) ?></span>
                    </div>
                    <button>More details</button>
                </div>
                <hr>
                <div class="section">
                    <h4 class="section-title">Dates</h4>
                    <?php
                    // Format check-in and check-out dates
                    $checkin_formatted = '';
                    $checkout_formatted = '';

                    if ($checkin_date) {
                        $checkin_dt = new DateTime($checkin_date);
                        $checkin_formatted = $checkin_dt->format('M j, Y') . ' - ' . $checkin_dt->format('g:i a');
                    }

                    if ($checkout_date) {
                        $checkout_dt = new DateTime($checkout_date);
                        $checkout_formatted = $checkout_dt->format('M j, Y') . ' - ' . $checkout_dt->format('g:i a');
                    }
                    ?>
                    <div class="row flex-row">
                        <span>
                            <b>Check-in</b>
                        </span>
                        <p><?= esc_html($checkin_formatted) ?></p>
                    </div>
                    <div class="row flex-row">
                        <span>
                            <b>Check-out</b>
                        </span>
                        <p><?= esc_html($checkout_formatted) ?></p>
                    </div>
                    <span class="small-text"><?= $guests_summary ?></span>
                </div>

                <hr>

                <?php
                // Calculate number of nights
                $nights = 0;
                if ($checkin_date && $checkout_date) {
                    $checkin = new DateTime($checkin_date);
                    $checkout = new DateTime($checkout_date);
                    $interval = $checkin->diff($checkout);
                    $nights = $interval->days;
                }

                // Get per night price (already fetched as $per_night)
                $total_rental = $per_night * $nights;
                ?>
                <div class="section rental-row">
                    <h4 class="section-title">Rental</h4>
                    <div class="row">
                        <span><?= number_format($per_night, 2, ',', ' ') ?>$ X <?= $nights ?>
                            night<?= $nights == 1 ? '' : 's' ?></span>
                        <span class="rental-amount"><?= number_format($total_rental, 2, ',', ' ') ?>$</span>
                    </div>
                </div>
                <div class="section addons_section">
                    <h4 class="section-title">Add-ons</h4>
                    <!-- Add-ons rows will be injected by JS -->
                </div>

                <div class="section lodging-tax-row">
                    <h4 class="section-title">Lodging tax</h4>
                    <div class="row">
                        <span>Lodging tax: 3,5%</span>
                        <span class="lodging-tax-amount">0,00$</span>
                    </div>
                </div>

                <div class="section admin-fee-row">
                    <h4 class="section-title">Administration fees</h4>
                    <div class="row">
                        <span>Administration fees: 3%</span>
                        <span class="admin-fee-amount">0,00$</span>
                    </div>
                </div>

                <div class="section total-excl-row flex-row-section">
                    <h4 class="section-title">Total excl. sales taxes</h4>
                    <div class="row total-bold">
                        <span class="total-excl-amount">0,00$</span>
                    </div>
                </div>

                <div class="section">
                    <div class="row gst-row">
                        <span>GST: <?= $gst ?>%</span>
                        <span class="gst-amount">0,00$</span>
                    </div>
                    <div class="row qst-row">
                        <span>QST: <?= $qst ?>%</span>
                        <span class="qst-amount">0,00$</span>
                    </div>
                </div>

                <div class="section total-row">
                    <div class="row total-bold">
                        <span>Total</span>
                        <span class="total-amount">0,00$</span>
                    </div>
                </div>

                <hr>

                <?php
                // Fetch reservation policy from Carbon Fields
                $reservation_policy = carbon_get_post_meta($chalet_id, 'reservation_policy');

                // Default: 50-50 (3 days before stay)
                $payments = [];
                $total = 0; // Will be filled by JS, but for PHP fallback, keep as 0
                
                switch ($reservation_policy) {
                    case 'policy_50_50_14':
                        $payments = [
                            [
                                'label' => 'Payment 1: 50%',
                                'desc' => 'On agreement',
                                'percent' => 50,
                                'date' => 'On agreement'
                            ],
                            [
                                'label' => 'Payment 2: 50%',
                                'desc' => '14 days before arrival',
                                'percent' => 50,
                                'date' => '14 days before arrival'
                            ]
                        ];
                        break;
                    case 'policy_25_25_50_14':
                        $payments = [
                            [
                                'label' => 'Payment 1: 25%',
                                'desc' => 'On agreement',
                                'percent' => 25,
                                'date' => 'On agreement'
                            ],
                            [
                                'label' => 'Payment 2: 25%',
                                'desc' => '14 days before arrival',
                                'percent' => 25,
                                'date' => '14 days before arrival'
                            ],
                            [
                                'label' => 'Payment 3: 50%',
                                'desc' => 'On arrival',
                                'percent' => 50,
                                'date' => 'On arrival'
                            ]
                        ];
                        break;
                    case 'policy_50_50_3':
                    default:
                        $payments = [
                            [
                                'label' => 'Payment 1: 50%',
                                'desc' => 'On agreement',
                                'percent' => 50,
                                'date' => 'On agreement'
                            ],
                            [
                                'label' => 'Payment 2: 50%',
                                'desc' => '3 days before arrival',
                                'percent' => 50,
                                'date' => '3 days before arrival'
                            ]
                        ];
                        break;
                }
                ?>
                <div class="section">
                    <div class="row">
                        <h4 class="section-title">Payment schedule</h4>
                        <span class="small-text">(<?= count($payments) ?> payments)</span>
                    </div>
                    <?php foreach ($payments as $i => $payment): ?>
                        <div class="row payment<?= $i + 1 ?>-row">
                            <span>
                                <strong><?= esc_html($payment['label']) ?></strong><br>
                                <?= esc_html($payment['desc']) ?>
                            </span>
                            <span class="payment<?= $i + 1 ?>-amount">0,00$</span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <script>
                    // Update JS payment schedule logic to match dynamic PHP
                    document.addEventListener('DOMContentLoaded', function () {
                        // Find payment rows and update amounts
                        const totalRow = document.querySelector('.total-row .total-amount');
                        let total = 0;
                        if (totalRow) {
                            // Remove $ and spaces, convert to float
                            total = parseFloat(totalRow.textContent.replace(/[^\d,\.]/g, '').replace(',', '.')) || 0;
                        }
                        // Payment schedule
                        const payments = <?php echo json_encode($payments); ?>;
                        let paid = 0;
                        payments.forEach(function (payment, idx) {
                            let amount = 0;
                            if (idx === payments.length - 1) {
                                // Last payment: remainder
                                amount = total - paid;
                            } else {
                                amount = Math.round((total * payment.percent / 100) * 100) / 100;
                                paid += amount;
                            }
                            const row = document.querySelector('.payment' + (idx + 1) + '-row .payment' + (idx + 1) + '-amount');
                            if (row) {
                                row.textContent = amount.toLocaleString('fr-CA', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '$';
                            }
                        });
                    });
                </script>
            </div>
            <div class="nav_buttons">
                <button class="back-btn" style="display:none;" onclick="prevSection(this)">Back</button>
                <button class="continue-btn next_btn" style="margin-left:auto;" data-id="add-ons"
                    onclick="nextSection(this)">Continue</button>
            </div>
        </div>
    </section>
</div>
<section class="now-set add-section" id="thankyou-section" style="display:none;">
    <div class="container summary-card">
        <div class="thankyou-wrapper">
            <div class="thankyou-logo">
                <img src="<?= esc_url(get_the_post_thumbnail_url($chalet)) ?>" alt="">
            </div>
            <h2 class="inner-heading">Thank you!</h2>
            <p class="section-para">
                Congratulations <b id="thankyou-guest-name">Guest Name</b>, your booking is now <b>confirmed!</b>
                See below for your reservation details.
                A confirmation email will also be sent to you.
            </p>
            <div class="date-details-row" style="display:block;">
                <div class="dates-right">
                    <div class="summary-card" id="thankyou-summary-card">
                        <hr>
                        <div class="summary-header">
                            <div class="summary-left">
                                <img src="<?= esc_url(get_the_post_thumbnail_url($chalet)) ?>" alt="Chalet Image">
                                <div class="summary-adress-details">
                                    <span class="small-text logo-name"><?= esc_html(get_the_title($chalet)) ?></span>
                                    <span
                                        class="small-text logo-name"><?= esc_html(get_post_meta($chalet_id, 'address', true)) ?></span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="section">
                            <h4 class="section-title">Dates</h4>
                            <div class="row flex-row">
                                <span><b>Check-in</b></span>
                                <p id="thankyou-checkin">
                                    <?php
                                    if ($checkin_date) {
                                        $dt = new DateTime($checkin_date);
                                        echo esc_html($dt->format('M j, Y') . ' - ' . $dt->format('g:i a'));
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="row flex-row">
                                <span><b>Check-out</b></span>
                                <p id="thankyou-checkout">
                                    <?php
                                    if ($checkout_date) {
                                        $dt = new DateTime($checkout_date);
                                        echo esc_html($dt->format('M j, Y') . ' - ' . $dt->format('g:i a'));
                                    }
                                    ?>
                                </p>
                            </div>
                            <span class="small-text" id="thankyou-guests"><?= esc_html($guests_summary) ?></span>
                        </div>
                        <hr>
                        <div class="section">
                            <h4 class="section-title">Add-ons</h4>
                            <div id="thankyou-addons-list">
                                <!-- Add-ons will be injected by JS -->
                            </div>
                        </div>
                        <hr>
                        <div class="section">
                            <div class="row">
                                <h4 class="section-title">Payment schedule</h4>
                                <span class="small-text">(<?= count($payments) ?> payments)</span>
                            </div>
                            <?php foreach ($payments as $i => $payment): ?>
                                <div class="row">
                                    <span>
                                        <strong><?= esc_html($payment['label']) ?></strong><br>
                                        <?= esc_html($payment['desc']) ?>
                                    </span>
                                    <span id="thankyou-payment<?= $i + 1 ?>-amount">0,00$</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="summary-adress-bottom">
                            <p class="section-para">Please feel free to contact us with any questions.
                                Thank you for your trust and have a nice day!
                            </p>
                            <a href="mailto:info@booktonchalet.com">
                                <svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="50" cy="50" r="45" fill="none" stroke="#00aa00" stroke-width="5" />
                                    <path d="M30 40 h40 a5 5 0 0 1 5 5 v20 a5 5 0 0 1 -5 5 h-40 a5 5 0 0 1 -5 -5 v-20 a5 5 0 0 1 5 -5 z
                                        M30 40 l20 15 l20 -15" fill="none" stroke="#00aa00" stroke-width="3" />
                                </svg>
                                info@booktonchalet.com
                            </a>
                            <a href="tel:5818142225">
                                <svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="50" cy="50" r="45" fill="none" stroke="#00aa00" stroke-width="5" />
                                    <path d="M61.8,67.4c-5.9-2.9-11-7.7-15.1-13.6c-2.5-3.5-4.4-7-5.5-10.3
                                        c-0.4-1.2,0-2.4,0.9-3.3l4.8-4.8c1.1-1.1,1.2-2.9,0.2-4.1L41,24.3
                                        c-1-1.2-2.8-1.4-4.1-0.5l-6.4,4.5c-1.6,1.1-2.5,3-2.3,5.3
                                        c0.4,5.3,2.3,11.3,5.6,17.3c3.3,6,7.3,11,11.9,15.1
                                        c4.5,4.1,9.6,7.5,15.1,9.8c2.1,0.9,4.2,0.7,5.7-0.7l5.2-5.2
                                        c1.1-1.1,1.1-2.8,0.1-3.9l-7.1-7.1c-1-1-2.5-1.2-3.6-0.5l-4.7,3.2
                                        C63.8,68,62.7,67.8,61.8,67.4z" fill="none" stroke="#00aa00" stroke-width="2" />
                                </svg>
                                (581) 814-2225
                            </a>
                        </div>
                    </div>
                    <button class="continue-btn" onclick = "window.location = '<?= get_home_url() ?>'">Back to Website</button>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    var current = 1;
    const gst = <?= carbon_get_post_meta($chalet_id, 'tax_gst') ?: 0 ?>; // in %
    const thq = <?= carbon_get_post_meta($chalet_id, 'tax_thq') ?: 0 ?>; // in %
    const qst = <?= carbon_get_post_meta($chalet_id, 'tax_qst') ?: 0 ?>; // in %
    const reservation_policy = '<?= carbon_get_post_meta($chalet_id, 'reservation_policy') ?>';
    const per_night = <?= $per_night ?>;
    const nights = <?= (int) $nights ?>;
    const admin_fee_percent = 3; // as per your markup
    const lodging_tax_percent = 3.5; // as per your markup
    const addons = [];

    // Payment schedule logic based on reservation_policy
    function getPaymentSchedule(policy, total) {
        let schedule = [];
        switch (policy) {
            case 'policy_50_50_14':
                schedule = [
                    { label: 'Payment 1: 50%', desc: 'On agreement', percent: 50 },
                    { label: 'Payment 2: 50%', desc: '14 days before arrival', percent: 50 }
                ];
                break;
            case 'policy_25_25_50_14':
                schedule = [
                    { label: 'Payment 1: 25%', desc: 'On agreement', percent: 25 },
                    { label: 'Payment 2: 25%', desc: '14 days before arrival', percent: 25 },
                    { label: 'Payment 3: 50%', desc: 'On arrival', percent: 50 }
                ];
                break;
            case 'policy_50_50_3':
            default:
                schedule = [
                    { label: 'Payment 1: 50%', desc: 'On agreement', percent: 50 },
                    { label: 'Payment 2: 50%', desc: '3 days before arrival', percent: 50 }
                ];
                break;
        }
        // Calculate payment amounts
        let paid = 0;
        schedule.forEach((p, idx) => {
            if (idx === schedule.length - 1) {
                p.amount = total - paid;
            } else {
                p.amount = Math.round((total * p.percent / 100) * 100) / 100;
                paid += p.amount;
            }
        });
        return schedule;
    }

    function nextSection(button) {
        const sections = document.querySelectorAll('.now-set');
        const mainWrapper = document.querySelector('.fixed_main');
        const thankyouSection = document.getElementById('thankyou-section');

        // If moving from Contact to Payment, validate contact form
        if (sections[current - 1].id === 'contact-set') {
            const form = document.getElementById('contactForm');
            if (form) {
                // Check required fields
                const requiredFields = ['email', 'firstName', 'lastName', 'country', 'phone'];
                let valid = true;
                requiredFields.forEach(function (field) {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (!input || !input.value.trim()) {
                        valid = false;
                        input && input.classList.add('input-error');
                    } else {
                        input.classList.remove('input-error');
                    }
                });
                // Simple email validation
                const emailInput = form.querySelector('[name="email"]');
                if (emailInput && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
                    valid = false;
                    emailInput.classList.add('input-error');
                }
                if (!valid) {
                    alert('Please fill in all required contact details correctly.');
                    return;
                }
            }
        }

        if (current < sections.length) {
            sections[current - 1].classList.remove('active');
            sections[current - 1].style.display = 'none';
            sections[current].classList.add('active');
            sections[current].style.display = 'block';
            current++;

            if (current === sections.length) {
                if (mainWrapper) mainWrapper.style.display = 'none';
                if (thankyouSection) {
                    thankyouSection.style.display = 'block';

                    // --- Update Thank You Section Fields ---
                    // Guest name
                    const form = document.getElementById('contactForm');
                    let guestName = '';
                    if (form) {
                        const firstName = form.querySelector('[name="firstName"]')?.value || '';
                        const lastName = form.querySelector('[name="lastName"]')?.value || '';
                        guestName = (firstName + ' ' + lastName).trim();
                    }
                    document.getElementById('thankyou-guest-name').textContent = guestName || 'Guest';

                    // Dates
                    const checkin = document.querySelector('.dates-section .date-selector .time-details .date')?.textContent || '';
                    const checkout = document.querySelectorAll('.dates-section .date-selector .time-details .date')[1]?.textContent || '';
                    if (checkin) document.getElementById('thankyou-checkin').textContent = checkin;
                    if (checkout) document.getElementById('thankyou-checkout').textContent = checkout;

                    // Guests summary
                    const guestsSummary = document.getElementById('guestSummary')?.textContent || '';
                    document.getElementById('thankyou-guests').textContent = guestsSummary;

                    // Add-ons
                    const addonsList = document.getElementById('thankyou-addons-list');
                    if (addonsList) {
                        addonsList.innerHTML = '';
                        if (addons.length > 0) {
                            addons.forEach(addon => {
                                const div = document.createElement('div');
                                div.className = 'row';
                                div.innerHTML = `<span>${addon.name} (${addon.qty})</span><span>${addon.total.toFixed(2)}$</span>`;
                                addonsList.appendChild(div);
                            });
                        } else {
                            addonsList.innerHTML = '<span>No add-ons selected.</span>';
                        }
                    }

                    // Payment schedule
                    const totalRow = document.querySelector('.summary-card .total-row .total-amount');
                    let total = 0;
                    if (totalRow) {
                        total = parseFloat(totalRow.textContent.replace(/[^\d,\.]/g, '').replace(',', '.')) || 0;
                    }
                    const schedule = getPaymentSchedule(reservation_policy, total);
                    schedule.forEach((payment, idx) => {
                        const paySpan = document.getElementById(`thankyou-payment${idx + 1}-amount`);
                        if (paySpan) {
                            paySpan.textContent = `${payment.amount.toLocaleString('fr-CA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}$`;
                        }
                    });
                }
            }
        }
        const backBtn = document.querySelector('.back-btn');
        if (backBtn) {
            backBtn.style.display = (current === 1) ? 'none' : 'inline-block';
        }

        // Hide continue button if at payment section
        if (sections[current - 1].id === 'payment') {
            const continueBtn = document.querySelector('.continue-btn.next_btn');
            if (continueBtn) continueBtn.style.display = 'none';
        }
    }

    function prevSection(button) {
        const sections = document.querySelectorAll('.now-set');
        const mainWrapper = document.querySelector('.fixed_main');
        const thankyouSection = document.getElementById('thankyou-section');

        if (current > 1) {
            sections[current - 1].classList.remove('active');
            sections[current - 1].style.display = 'none';
            sections[current - 2].classList.add('active');
            sections[current - 2].style.display = 'block';
            current--;
            if (mainWrapper) mainWrapper.style.display = 'grid';
            if (thankyouSection) thankyouSection.style.display = 'none';
        }
        const backBtn = document.querySelector('.back-btn');
        if (backBtn) {
            backBtn.style.display = (current === 1) ? 'none' : 'inline-block';
        }
    }

    // Attach event listeners to all back buttons
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.back-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                prevSection(this);
            });
        });
    });

    function add_addon(button) {
        const extraIdx = button.getAttribute('data-extra-idx');
        const extraName = button.getAttribute('data-extra-name');
        const extraPrice = parseFloat(button.getAttribute('data-extra-price'));
        const extraType = button.getAttribute('data-extra-type');
        const qtySelect = document.getElementById(`extra-qty-${extraIdx}`);
        let qty = qtySelect ? parseInt(qtySelect.value) : 1;

        if (!qtySelect) {
            if (addons.some(addon => addon.name === extraName)) {
                alert('This add-on can only be added once.');
                return;
            }
        }

        const existingAddonIndex = addons.findIndex(addon => addon.name === extraName);
        if (existingAddonIndex !== -1) {
            if (qtySelect) {
                addons[existingAddonIndex].qty += qty;
                addons[existingAddonIndex].total += extraPrice * qty;
            }
        } else {
            addons.push({
                name: extraName,
                price: extraPrice,
                type: extraType,
                qty: qty,
                total: extraPrice * qty,
                idx: extraIdx
            });
        }

        const addBtn = document.querySelector(`.add-button[data-extra-idx="${extraIdx}"]`);
        const removeBtn = document.querySelector(`.remove-button[data-extra-idx="${extraIdx}"]`);
        if (removeBtn) removeBtn.style.display = 'inline-block';

        update_summary();
    }

    function remove_addon(button) {
        const extraIdx = button.getAttribute('data-extra-idx');
        const extraName = button.getAttribute('data-extra-name');
        const addBtn = document.querySelector(`.add-button[data-extra-idx="${extraIdx}"]`);
        const removeBtn = document.querySelector(`.remove-button[data-extra-idx="${extraIdx}"]`);
        if (addBtn) addBtn.style.display = 'inline-block';

        const idx = addons.findIndex(addon => addon.name === extraName);
        if (idx !== -1) {
            if (addons[idx].qty > 1) {
                addons[idx].qty -= 1;
                addons[idx].total -= addons[idx].price;
            } else {
                addons.splice(idx, 1);
            }
            if (typeof addons[idx] === "undefined" || addons[idx].qty === 0) {
                if (removeBtn) removeBtn.style.display = 'none';
            }
        }
        update_summary();
    }

    function update_summary() {
        const summaryCard = document.querySelector('.summary-card');
        if (!summaryCard) return;


        // Add-ons section
        const addonsSection = summaryCard.querySelector('.addons_section');
        addonsSection.innerHTML = '<h4 class="section-title">Add-ons</h4>';
        addons.forEach(addon => {
            const row = document.createElement('div');
            row.className = 'row';
            row.innerHTML = `<span>${addon.name} (${addon.qty})</span><span>${addon.total.toFixed(2)}$</span>`;
            addonsSection.appendChild(row);
        });

        // Calculate totals
        const rental = per_night * nights;
        const addons_total = addons.reduce((sum, addon) => sum + addon.total, 0);
        const subtotal = rental + addons_total;

        const lodging_tax = subtotal * (lodging_tax_percent / 100);
        const admin_fee = subtotal * (admin_fee_percent / 100);

        const total_excl_sales_taxes = subtotal + lodging_tax + admin_fee;

        const gst_amt = total_excl_sales_taxes * (gst / 100);
        const qst_amt = total_excl_sales_taxes * (qst / 100);
        const thq_amt = total_excl_sales_taxes * (thq / 100);

        // const total = total_excl_sales_taxes + gst_amt + qst_amt + thq_amt;
        const total = total_excl_sales_taxes + gst_amt + qst_amt;

        // Rental row
        const rentalRow = summaryCard.querySelector('.rental-row .rental-amount');
        if (rentalRow) rentalRow.textContent = `${rental.toLocaleString('fr-CA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}$`;

        // Lodging tax
        const lodgingTaxRow = summaryCard.querySelector('.lodging-tax-row .lodging-tax-amount');
        if (lodgingTaxRow) lodgingTaxRow.textContent = `${lodging_tax.toLocaleString('fr-CA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}$`;

        // Admin fees
        const adminFeeRow = summaryCard.querySelector('.admin-fee-row .admin-fee-amount');
        if (adminFeeRow) adminFeeRow.textContent = `${admin_fee.toLocaleString('fr-CA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}$`;

        // Total excl. sales taxes
        const totalExclRow = summaryCard.querySelector('.total-excl-row .total-excl-amount');
        if (totalExclRow) totalExclRow.textContent = `${total_excl_sales_taxes.toLocaleString('fr-CA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}$`;

        // GST
        const gstRow = summaryCard.querySelector('.gst-row .gst-amount');
        if (gstRow) gstRow.textContent = `${gst_amt.toLocaleString('fr-CA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}$`;

        // QST
        const qstRow = summaryCard.querySelector('.qst-row .qst-amount');
        if (qstRow) qstRow.textContent = `${qst_amt.toLocaleString('fr-CA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}$`;

        // Total (final)
        const totalRow = summaryCard.querySelector('.total-row .total-amount');
        if (totalRow) totalRow.textContent = `${total.toLocaleString('fr-CA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}$`;

        // Payment schedule (dynamic)
        const paymentRows = summaryCard.querySelectorAll('[class^="payment"][class$="-row"]');
        paymentRows.forEach(row => {
            const amountSpan = row.querySelector('span[class$="-amount"]');
            if (amountSpan) amountSpan.textContent = '0,00$';
        });

        const schedule = getPaymentSchedule(reservation_policy, total);
        schedule.forEach((payment, idx) => {
            const row = summaryCard.querySelector(`.payment${idx + 1}-row .payment${idx + 1}-amount`);
            if (row) {
                row.textContent = `${payment.amount.toLocaleString('fr-CA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}$`;
            }
        });
    }
    function bookNow() {
        // Gather booking data from all sections
        const form = document.getElementById('contactForm');
        const bookingData = {};

        // Dates and guests
        bookingData.chalet_id = <?= json_encode($chalet_id) ?>;
        bookingData.checkin_date = document.querySelector('.dates-section .date-selector .time-details .date')?.textContent || '';
        bookingData.checkout_date = document.querySelectorAll('.dates-section .date-selector .time-details .date')[1]?.textContent || '';
        bookingData.adults = parseInt(document.getElementById('adults')?.value || '1', 10);
        bookingData.children = parseInt(document.getElementById('children')?.value || '0', 10);
        bookingData.infants = parseInt(document.getElementById('infants')?.value || '0', 10);

        // Add-ons
        bookingData.addons = addons.map(addon => ({
            name: addon.name,
            qty: addon.qty,
            price: addon.price,
            total: addon.total,
            type: addon.type,
            idx: addon.idx
        }));

        // Contact details
        if (form) {
            bookingData.email = form.querySelector('[name="email"]')?.value || '';
            bookingData.firstName = form.querySelector('[name="firstName"]')?.value || '';
            bookingData.lastName = form.querySelector('[name="lastName"]')?.value || '';
            bookingData.country = form.querySelector('[name="country"]')?.value || '';
            bookingData.phone = form.querySelector('[name="phone"]')?.value || '';
            bookingData.comments = form.querySelector('[name="comments"]')?.value || '';
        }

        // Payment details (card fields)
        bookingData.card_number = document.querySelector('.payment-container input.card-input[placeholder="Card number"]')?.value || '';
        bookingData.card_expiry = document.querySelector('.payment-container input.card-input[placeholder="Expiration date"]')?.value || '';
        bookingData.card_cvc = document.querySelector('.payment-container input.card-input[placeholder="Security code"]')?.value || '';
        bookingData.card_full_name = document.getElementById('fullName')?.value || '';
        bookingData.card_country = document.querySelector('.credit-form select[name="country"]')?.value || '';
        bookingData.card_address = document.getElementById('address')?.value || '';
        bookingData.accepted_terms = document.getElementById('accept-checkbox')?.checked ? 1 : 0;

        // Totals
        const summaryCard = document.querySelector('.summary-card');
        bookingData.rental_total = summaryCard?.querySelector('.rental-row .rental-amount')?.textContent || '';
        bookingData.addons_total = addons.reduce((sum, addon) => sum + addon.total, 0);
        bookingData.lodging_tax = summaryCard?.querySelector('.lodging-tax-row .lodging-tax-amount')?.textContent || '';
        bookingData.admin_fee = summaryCard?.querySelector('.admin-fee-row .admin-fee-amount')?.textContent || '';
        bookingData.total_excl_sales_taxes = summaryCard?.querySelector('.total-excl-row .total-excl-amount')?.textContent || '';
        bookingData.gst = summaryCard?.querySelector('.gst-row .gst-amount')?.textContent || '';
        bookingData.qst = summaryCard?.querySelector('.qst-row .qst-amount')?.textContent || '';
        bookingData.total = summaryCard?.querySelector('.total-row .total-amount')?.textContent || '';

        // Payment schedule
        bookingData.payment_schedule = getPaymentSchedule(reservation_policy, parseFloat((bookingData.total || '0').replace(/[^\d,\.]/g, '').replace(',', '.')));


        const formData = new FormData();
        formData.append('action', 'chalet_booking');
        formData.append('booking', JSON.stringify(bookingData));
        notifier.tip("Sending your booking request. Please wait...");
        // AJAX submit to WordPress action hook
        fetch(ajax_url, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // Optionally handle response (show success, error, etc)
                if (data.success) {
                    // alert('Booking submitted successfully!');
                    nextSection();
                    notifier.success("Booking submitted successfully!");
                } else {
                    alert(data && data.data && data.data.message ? data.data.message : 'Booking submission failed. Please try again.');
                    current = 1;
                    const sections = document.querySelectorAll('.now-set');
                    sections.forEach((section, idx) => {
                        section.classList.remove('active');
                        section.style.display = idx === 0 ? 'block' : 'none';
                    });
                    const mainWrapper = document.querySelector('.fixed_main');
                    const thankyouSection = document.getElementById('thankyou-section');
                    if (mainWrapper) mainWrapper.style.display = 'grid';
                    if (thankyouSection) thankyouSection.style.display = 'none';
                    const backBtn = document.querySelector('.back-btn');
                    if (backBtn) backBtn.style.display = 'none';
                    const continueBtn = document.querySelector('.continue-btn.next_btn');
                    if (continueBtn) continueBtn.style.display = 'inline-flex';
                }
            })
            .catch(() => {
                alert('Booking submission failed. Please try again.');
            });
    }
    // Initial summary update on page load
    document.addEventListener('DOMContentLoaded', update_summary);
</script>