<?php

/**
 * Template Name: Add new Booking
 *  */
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New booking</title>
    <link rel="stylesheet" href="<?= get_template_directory_uri() ?>/dashboard/css/new-booking.css?v=<?= filemtime(get_template_directory() . '/dashboard/css/new-booking.css') ?>" />
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
    <section class="now-set dates-section active" data-id="dates">
        <div class="container">
            <h2 class="inner-heading">Dates</h2>
            <div class="date-details-row">
                <div class="dates-left">
                    <div class="date-selector">
                        <div class="time-details">
                            <span class="label">Check-in</span>
                            <span class="date">8/22/2025</span>
                        </div>
                        <div class="time-details">
                            <span class="label">Check-out</span>
                            <span class="date">8/24/2025</span>
                        </div>
                        <button class="close-btn">&times;</button>
                    </div>
                </div>
                <div class="dates-mid">
                    <div class="guest-dropdown" onclick="togglePopup()">
                        <div class="guest-label">Guests</div>
                        <div class="guest-summary-container">
                            <div class="guest-summary" id="guestSummary">18 adults, 2 children</div>
                            <div id="dropdownIcon" class="dropdown-icon">▼</div>
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
                <div class="dates-right">
                    <div class="summary-card">
                        <h2>Reservation summary</h2>
                        <hr>
                        <div class="summary-header">
                            <div class="summary-left">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/form-img.jpg" alt="Chalet Image">
                                <span class="small-text logo-name">Chalet name</span>
                            </div>
                            <button>More details</button>
                        </div>
                        <hr>
                        <div class="section">
                            <h4 class="section-title">Dates</h4>
                            <div class="row flex-row">
                                <span>
                                    <b>Check-in</b>
                                </span>
                                <P>Aug 22, 2025 - 5 pm</P>
                            </div>
                            <div class="row flex-row">
                                <span>
                                    <b>Check-out</b>

                                </span>
                                <P>Aug 24, 2025 - 11 am</P>
                            </div>
                            <span class="small-text">18 adults, 2 children</span>
                        </div>

                        <hr>

                        <div class="section">
                            <h4 class="section-title">Rental</h4>
                            <div class="row">
                                <span>1000,00$ X 2 nights</span>
                                <span>2000,00$</span>
                            </div>
                        </div>

                        <div class="section">
                            <h4 class="section-title">Lodging tax</h4>
                            <div class="row">
                                <span>Lodging tax: 3,5%</span>
                                <span>70,00$</span>
                            </div>
                        </div>

                        <div class="section">
                            <h4 class="section-title">Administration fees</h4>
                            <div class="row">
                                <span>Administration fees: 3%</span>
                                <span>60,00$</span>
                            </div>
                        </div>

                        <div class="section flex-row-section">
                            <h4 class="section-title">Total excl. sales taxes</h4>
                            <div class="row total-bold">
                                <span>2130,00$</span>
                            </div>
                        </div>

                        <div class="section">
                            <div class="row">
                                <span>GST: 5%</span>
                                <div>106,50$</div>
                            </div>
                            <div class="row">
                                <span>QST: 9.975%</span>
                                <span>212,47$</span>
                            </div>
                        </div>

                        <div class="section">
                            <div class="row total-bold">
                                <span>Total</span>
                                <span>2448,97$</span>
                            </div>
                        </div>

                        <hr>

                        <div class="section">
                            <div class="row">
                                <h4 class="section-title">Payment schedule</h4>
                                <span class="small-text">(2 payments)</span>
                            </div>
                            <div class="row">
                                <span>
                                    <strong>Payment 1: 50%</strong><br>
                                    On agreement
                                </span>
                                <span>1224,48$</span>
                            </div>
                            <div class="row">
                                <span>
                                    <strong>Payment 2: 50%</strong><br>
                                    Day/Month/Year
                                </span>
                                <span>1224,49$</span>
                            </div>
                        </div>


                    </div>
                    <button class="continue-btn" data-id="add-ons" onclick="showPromoContent(event, this)">Continue</button>
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
                    <div class="option-card">
                        <div class="option-image">
                            <img src="<?= get_template_directory_uri() ?>/dashboard/images/form-img.jpg" alt="">
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
                    </div>

                    <!-- Kayak location option -->
                    <div class="option-card">
                        <div class="option-image">
                            <img src="<?= get_template_directory_uri() ?>/dashboard/images/form-img.jpg" alt="">
                        </div>
                        <div class="option-content">
                            <div class="option-header">
                                <h3 class="option-title">Kayak location</h3>
                                <div class="option-price">25,00$/Kayak</div>
                            </div>
                            <div class="option-description">
                                Rent a kayak for your stay!
                            </div>
                            <div class="option-bottom">
                                <div class="quantity-selector">
                                    <select id="kayak-quantity">
                                        <option value="1">1</option>
                                        <option value="2" selected>2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                                <button class="add-button">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dates-right">
                    <div class="summary-card">
                        <h2>Reservation summary</h2>
                        <hr>
                        <div class="summary-header">
                            <div class="summary-left">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/form-img.jpg" alt="Chalet Image">
                                <span class="small-text logo-name">Chalet name</span>
                            </div>
                            <button>More details</button>
                        </div>
                        <hr>
                        <div class="section">
                            <h4 class="section-title">Dates</h4>
                            <div class="row flex-row">
                                <span>
                                    <b>Check-in</b>
                                </span>
                                <P>Aug 22, 2025 - 5 pm</P>
                            </div>
                            <div class="row flex-row">
                                <span>
                                    <b>Check-out</b>

                                </span>
                                <P>Aug 24, 2025 - 11 am</P>
                            </div>
                            <span class="small-text">18 adults, 2 children</span>
                        </div>

                        <hr>

                        <div class="section">
                            <h4 class="section-title">Rental</h4>
                            <div class="row">
                                <span>1000,00$ X 2 nights</span>
                                <span>2000,00$</span>
                            </div>
                        </div>

                        <div class="section">
                            <h4 class="section-title">Lodging tax</h4>
                            <div class="row">
                                <span>Lodging tax: 3,5%</span>
                                <span>70,00$</span>
                            </div>
                        </div>

                        <div class="section">
                            <h4 class="section-title">Administration fees</h4>
                            <div class="row">
                                <span>Administration fees: 3%</span>
                                <span>60,00$</span>
                            </div>
                        </div>

                        <div class="section flex-row-section">
                            <h4 class="section-title">Total excl. sales taxes</h4>
                            <div class="row total-bold">
                                <span>2130,00$</span>
                            </div>
                        </div>

                        <div class="section">
                            <div class="row">
                                <span>GST: 5%</span>
                                <div>106,50$</div>
                            </div>
                            <div class="row">
                                <span>QST: 9.975%</span>
                                <span>212,47$</span>
                            </div>
                        </div>

                        <div class="section">
                            <div class="row total-bold">
                                <span>Total</span>
                                <span>2448,97$</span>
                            </div>
                        </div>

                        <hr>

                        <div class="section">
                            <div class="row">
                                <h4 class="section-title">Payment schedule</h4>
                                <span class="small-text">(2 payments)</span>
                            </div>
                            <div class="row">
                                <span>
                                    <strong>Payment 1: 50%</strong><br>
                                    On agreement
                                </span>
                                <span>1224,48$</span>
                            </div>
                            <div class="row">
                                <span>
                                    <strong>Payment 2: 50%</strong><br>
                                    Day/Month/Year
                                </span>
                                <span>1224,49$</span>
                            </div>
                        </div>


                    </div>
                    <button class="continue-btn" data-id="contact-set" onclick="showPromoContent(event, this)">Continue</button>
                </div>
            </div>
        </div>
    </section>
    <section class="now-set add-section " id="contact-set" >
        <div class="container">
            <h2 class="inner-heading">Your contact details</h2>
            <div class="inner-divider"></div>
            <div class="date-details-row">
                <div class="dates-left">
                    <div class="form-container">
                        <form id="contactForm">
                            <!-- Email -->
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="test@test.com">
                            </div>

                            <!-- First name and Last name -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">First name</label>
                                    <input type="text" id="firstName" name="firstName" value="John">
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last name</label>
                                    <input type="text" id="lastName" name="lastName" value="Doe">
                                </div>
                            </div>

                            <!-- Country and Phone number -->
                            <div class="form-row">
                                <div class="form-group">
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
                                <div class="form-group">
                                    <label for="phone">Phone number</label>
                                    <div class="phone-input">
                                        <div class="flag-select">
                                            <span class="flag-icon"></span>
                                        </div>
                                        <input type="text" id="phone" name="phone" class="phone-number"
                                            value="+1 (819) 514-0000">
                                    </div>
                                </div>
                            </div>

                            <!-- Comments -->
                            <div class="form-group">
                                <label for="comments">Comments (optional)</label>
                                <textarea id="comments" name="comments"
                                    placeholder="Write any additional questions or let us know about anything else you may need here."></textarea>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="dates-right">
                    <div class="summary-card">
                        <h2>Reservation summary</h2>
                        <hr>
                        <div class="summary-header">
                            <div class="summary-left">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/form-img.jpg" alt="Chalet Image">
                                <span class="small-text logo-name">Chalet name</span>
                            </div>
                            <button>More details</button>
                        </div>
                        <hr>
                        <div class="section">
                            <h4 class="section-title">Dates</h4>
                            <div class="row flex-row">
                                <span>
                                    <b>Check-in</b>
                                </span>
                                <P>Aug 22, 2025 - 5 pm</P>
                            </div>
                            <div class="row flex-row">
                                <span>
                                    <b>Check-out</b>

                                </span>
                                <P>Aug 24, 2025 - 11 am</P>
                            </div>
                            <span class="small-text">18 adults, 2 children</span>
                        </div>

                        <hr>

                        <div class="section">
                            <h4 class="section-title">Rental</h4>
                            <div class="row">
                                <span>1000,00$ X 2 nights</span>
                                <span>2000,00$</span>
                            </div>
                        </div>

                        <div class="section">
                            <h4 class="section-title">Lodging tax</h4>
                            <div class="row">
                                <span>Lodging tax: 3,5%</span>
                                <span>70,00$</span>
                            </div>
                        </div>

                        <div class="section">
                            <h4 class="section-title">Administration fees</h4>
                            <div class="row">
                                <span>Administration fees: 3%</span>
                                <span>60,00$</span>
                            </div>
                        </div>

                        <div class="section flex-row-section">
                            <h4 class="section-title">Total excl. sales taxes</h4>
                            <div class="row total-bold">
                                <span>2130,00$</span>
                            </div>
                        </div>

                        <div class="section">
                            <div class="row">
                                <span>GST: 5%</span>
                                <div>106,50$</div>
                            </div>
                            <div class="row">
                                <span>QST: 9.975%</span>
                                <span>212,47$</span>
                            </div>
                        </div>

                        <div class="section">
                            <div class="row total-bold">
                                <span>Total</span>
                                <span>2448,97$</span>
                            </div>
                        </div>

                        <hr>

                        <div class="section">
                            <div class="row">
                                <h4 class="section-title">Payment schedule</h4>
                                <span class="small-text">(2 payments)</span>
                            </div>
                            <div class="row">
                                <span>
                                    <strong>Payment 1: 50%</strong><br>
                                    On agreement
                                </span>
                                <span>1224,48$</span>
                            </div>
                            <div class="row">
                                <span>
                                    <strong>Payment 2: 50%</strong><br>
                                    Day/Month/Year
                                </span>
                                <span>1224,49$</span>
                            </div>
                        </div>


                    </div>
                    <button class="continue-btn" data-id="payment" onclick="showPromoContent(event, this)">Continue</button>
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
                        <hr>
                        <div class="google-pay">
                            <img src="images/g-pay.png" alt="GPay">
                            Google Pay
                        </div>

                    </div>
                    <div class="form-group credit-form">
                        <label for="firstName">First name</label>
                        <input type="text" id="firstName" name="firstName" value="John">
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
                        <label for="firstName">First name</label>
                        <input type="text" id="firstName" name="firstName" value="John">
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
                        <button class="continue-btn" data-id="thankyou-section" onclick="showPromoContent(event, this)">Book Now</button>
                    </div>
                </div>
                <div class="dates-right">
                    <div class="summary-card">
                        <h2>Reservation summary</h2>
                        <hr>
                        <div class="summary-header">
                            <div class="summary-left">
                                <img src="<?= get_template_directory_uri() ?>/dashboard/images/form-img.jpg" alt="Chalet Image">
                                <span class="small-text logo-name">Chalet name</span>
                            </div>
                            <button>More details</button>
                        </div>
                        <hr>
                        <div class="section">
                            <h4 class="section-title">Dates</h4>
                            <div class="row flex-row">
                                <span>
                                    <b>Check-in</b>
                                </span>
                                <P>Aug 22, 2025 - 5 pm</P>
                            </div>
                            <div class="row flex-row">
                                <span>
                                    <b>Check-out</b>

                                </span>
                                <P>Aug 24, 2025 - 11 am</P>
                            </div>
                            <span class="small-text">18 adults, 2 children</span>
                        </div>

                        <hr>

                        <div class="section">
                            <h4 class="section-title">Rental</h4>
                            <div class="row">
                                <span>1000,00$ X 2 nights</span>
                                <span>2000,00$</span>
                            </div>
                        </div>

                        <div class="section">
                            <h4 class="section-title">Lodging tax</h4>
                            <div class="row">
                                <span>Lodging tax: 3,5%</span>
                                <span>70,00$</span>
                            </div>
                        </div>

                        <div class="section">
                            <h4 class="section-title">Administration fees</h4>
                            <div class="row">
                                <span>Administration fees: 3%</span>
                                <span>60,00$</span>
                            </div>
                        </div>

                        <div class="section flex-row-section">
                            <h4 class="section-title">Total excl. sales taxes</h4>
                            <div class="row total-bold">
                                <span>2130,00$</span>
                            </div>
                        </div>

                        <div class="section">
                            <div class="row">
                                <span>GST: 5%</span>
                                <div>106,50$</div>
                            </div>
                            <div class="row">
                                <span>QST: 9.975%</span>
                                <span>212,47$</span>
                            </div>
                        </div>

                        <div class="section">
                            <div class="row total-bold">
                                <span>Total</span>
                                <span>2448,97$</span>
                            </div>
                        </div>

                        <hr>

                        <div class="section">
                            <div class="row">
                                <h4 class="section-title">Payment schedule</h4>
                                <span class="small-text">(2 payments)</span>
                            </div>
                            <div class="row">
                                <span>
                                    <strong>Payment 1: 50%</strong><br>
                                    On agreement
                                </span>
                                <span>1224,48$</span>
                            </div>
                            <div class="row">
                                <span>
                                    <strong>Payment 2: 50%</strong><br>
                                    Day/Month/Year
                                </span>
                                <span>1224,49$</span>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="now-set add-section " id="thankyou-section">
        <div class="container summary-card">
            <div class="thankyou-wrapper">
                <div class="thankyou-logo">
                    <img src="<?= get_template_directory_uri() ?>/dashboard/images/LOGO.svg" alt="">
                </div>
                <h2 class="inner-heading">Booking confirmation</h2>
                <p class="section-para">Congratulations Guest Name, your booking is now confirmed!
See below for your reservation details.
A confirmation email will also be sent to you
                </p>
                <div class="date-details-row">
                    <div class="dates-right">
                        <div class="summary-card">
                            <hr>
                            <div class="summary-header">
                                <div class="summary-left">
                                    <img src="<?= get_template_directory_uri() ?>/dashboard/images/form-img.jpg" alt="Chalet Image">
                                    <div class="summary-adress-details">
                                        <span class="small-text logo-name">Chalet name</span>
                                        <span class="small-text logo-name">Chalet adress</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="section">
                                <h4 class="section-title">Dates</h4>
                                <div class="row flex-row">
                                    <span>
                                        <b>Check-in</b>
                                    </span>
                                    <P>Aug 22, 2025 - 5 pm</P>
                                </div>
                                <div class="row flex-row">
                                    <span>
                                        <b>Check-out</b>

                                    </span>
                                    <P>Aug 24, 2025 - 11 am</P>
                                </div>
                                <span class="small-text">18 adults, 2 children</span>
                            </div>

                            <hr>

                            <div class="section">
                                <h4 class="section-title">Add-ons</h4>
                                <div class="row">
                                    <span>Late check-out</span>
                                </div>
                                <div class="row">
                                    <span>Late check-out</span>
                                </div>
                            </div>


                            <hr>

                            <div class="section">
                                <div class="row">
                                    <h4 class="section-title">Payment schedule</h4>
                                    <span class="small-text">(2 payments)</span>
                                </div>
                                <div class="row">
                                    <span>
                                        <strong>Payment 1: 50%</strong><br>
                                        On agreement
                                    </span>
                                    <span>1224,48$</span>
                                </div>
                                <div class="row">
                                    <span>
                                        <strong>Payment 2: 50%</strong><br>
                                        Day/Month/Year
                                    </span>
                                    <span>1224,49$</span>
                                </div>
                            </div>

                            <div class="summary-adress-bottom">
                                <p class="section-para">Please feel free to contact us with any questions.
                                    Thank you for your trust and have a nice day!
                                </p>
                                <a href="#"> <svg width="100" height="100" viewBox="0 0 100 100"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <!-- Circle with green stroke only -->
                                        <circle cx="50" cy="50" r="45" fill="none" stroke="#00aa00" stroke-width="5" />

                                        <!-- Mail Icon (Envelope) filled with green -->
                                        <path d="M30 40 h40 a5 5 0 0 1 5 5 v20 a5 5 0 0 1 -5 5 h-40 a5 5 0 0 1 -5 -5 v-20 a5 5 0 0 1 5 -5 z
           M30 40 l20 15 l20 -15" fill="none" stroke="#00aa00" stroke-width="3" />
                                    </svg>info@booktonchalet.com
                                </a>
                                <a href="#"><svg width="100" height="100" viewBox="0 0 100 100"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <!-- Circle with green stroke -->
                                        <circle cx="50" cy="50" r="45" fill="none" stroke="#00aa00" stroke-width="5" />

                                        <!-- Phone Icon -->
                                        <path d="M61.8,67.4c-5.9-2.9-11-7.7-15.1-13.6c-2.5-3.5-4.4-7-5.5-10.3
           c-0.4-1.2,0-2.4,0.9-3.3l4.8-4.8c1.1-1.1,1.2-2.9,0.2-4.1L41,24.3
           c-1-1.2-2.8-1.4-4.1-0.5l-6.4,4.5c-1.6,1.1-2.5,3-2.3,5.3
           c0.4,5.3,2.3,11.3,5.6,17.3c3.3,6,7.3,11,11.9,15.1
           c4.5,4.1,9.6,7.5,15.1,9.8c2.1,0.9,4.2,0.7,5.7-0.7l5.2-5.2
           c1.1-1.1,1.1-2.8,0.1-3.9l-7.1-7.1c-1-1-2.5-1.2-3.6-0.5l-4.7,3.2
           C63.8,68,62.7,67.8,61.8,67.4z" fill="none" stroke="#00aa00" stroke-width="2" />
                                    </svg>( 5 8 1 ) 8 1 4 - 2 2 2 5
                                </a>
                            </div>
                        </div>
                        <a class="continue-btn" href="dashboard">Back to website</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function showPromoContent(event, button) {
            event.preventDefault();

            // Hide all tab contents
            document.querySelectorAll('.now-set').forEach(el => {
                el.style.display = 'none';
            });

            // Remove 'active' from all tab links
            const allTabLinks = document.querySelectorAll('fill-bb');
            allTabLinks.forEach(link => link.classList.remove('active'));

            // Get target content and tab link
            const targetId = button.getAttribute('data-id');
            const targetElement = document.getElementById(targetId);
            const targetTab = document.getElementById(targetId + '-link');

            // Show the selected tab content
            if (targetElement) {
                targetElement.style.display = 'block';
                targetTab.style.color = '#004d40';
            }

            // Add 'active' to all tabs before and including the clicked one
            let activate = true;
            allTabLinks.forEach(link => {
                if (activate) link.classList.add('active');
                if (link === targetTab) activate = false;
            });
        }
    </script>