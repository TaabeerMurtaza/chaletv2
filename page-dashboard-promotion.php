<?php

/**
 * Template Name: Dashboard Promotion
 *  */
?>
<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/dashboard/css/promotion.css?v=<?= filemtime(get_template_directory() . '/dashboard/css/promotion.css') ?>" />
   <section class="promotion-list-section">
        <div class="promotion-container">
            <div class="promotion-header">
                <h1>Promotions</h1>
                <span class="add-promotion" id="add-promotion">+ Add Promotion</span>
            </div>
            <p>Create special promotions for specific periods or offer discounts to certain customers.</p>

            <div class="promotion">
                <div class="promotion-title">3rd night free - Summer</div>
                <div class="promotion-description">3 nights for the price of 2</div><br>
                <div class="date-tag-details">
                    <span class="date-tag">20 May 2025 - 4 June 2025 <img src="<?= get_template_directory_uri() ?>/dashboard/images/cross.png" alt=""></span>
                    <span class="date-tag">20 June 2025 - 26 June 2025 <img src="<?= get_template_directory_uri() ?>/dashboard/images/cross.png" alt=""></span>
                </div>
                <div class="dot-menu">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>


        </div>
    </section>
    <section class="pf-section" id="promotion-popup">
        <div class="hide-overlay"></div>
  
        <div class="promotion-list-row">
            <div class="promotion-left">
                <div class="invoice-container">
                    <div class="section-mb">
                        <h2 class="section-title">Rental</h2>
                        <div class="flex-row">
                            <span class="item-label">1000,00$ X 2 nights</span>
                            <span class="item-value">2000,00$</span>
                        </div>
                    </div>

                    <div class="section-mb">
                        <h2 class="section-title discount">Discount</h2>
                        <div class="flex-row">
                            <span class="item-label">15% Off - Low season</span>
                            <span class="item-value discount-value">-300,00$</span>
                        </div>
                    </div>

                    <div class="section-mb">
                        <h2 class="section-title">Add-ons</h2>
                        <div class="flex-row">
                            <span class="item-label">Late check-out</span>
                            <span class="item-value">200,00$</span>
                        </div>
                    </div>

                    <div class="section-mb">
                        <h2 class="section-title">Lodging tax</h2>
                        <div class="flex-row">
                            <span class="item-label">Lodging tax : 3,5%</span>
                            <span class="item-value">66,50$</span>
                        </div>
                    </div>

                    <div class="section-mb">
                        <h2 class="section-title">Administration fees</h2>
                        <div class="flex-row">
                            <span class="item-label">Administration fees: 3%</span>
                            <span class="item-value">57,00$</span>
                        </div>
                    </div>

                    <div class="total-excl-taxes">
                        <div class="flex-row">
                            <span>Total excl. sales taxes</span>
                            <span>2023,50$</span>
                        </div>
                    </div>

                    <div class="section-mb">
                        <div class="flex-row mb-2">
                            <span class="item-label">GST : 5%</span>
                            <span class="item-value">101,18$</span>
                        </div>
                        <div class="flex-row">
                            <span class="item-label">QST : 9,975%</span>
                            <span class="item-value">201,84$</span>
                        </div>
                    </div>

                    <div class="final-total">
                        <div class="flex-row">
                            <span>Total</span>
                            <span>2326,52$</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hide-overlay hide-promo" id="promo-popup"></div>
            <div class="promotion-mid" id="promo-mid">
                <div class="form-container">
                    <h2 class="form-title">Promotion</h2>

                    <div class="toggle-group">
                        <label class="label">Type of promotions</label>

                        <div class="toggle-item">
                            <div class="toggle-row">
                                <span class="toggle-label">Last minute</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="input-group">
                                <input class="input" type="number" value="14">
                                <span class="input-group-text">nights or less before arrival</span>
                            </div>
                        </div>

                        <div class="toggle-item">
                            <div class="toggle-row">
                                <span class="toggle-label">Early booking</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="input-group">
                                <input class="input" type="number" value="365">
                                <span class="input-group-text">nights or more before arrival</span>
                            </div>
                        </div>

                        <div class="toggle-item">
                            <div class="toggle-row">
                                <span class="toggle-label">Minimum length of stay</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            <div class="input-group">
                                <input class="input" type="number" value="3">
                                <span class="input-group-text">minimum nights of stay</span>
                            </div>
                        </div>
                    </div>
                    <button class="promotion-btn promotion-btn-save" id="close-promo">Save</button>
                </div>
            </div>
            <div class="promotion-right">
                <div class="promotion-form-container">
                    <h2>Promotion</h2>

                    <label class="promotion-label">Type</label>
                    <select class="promotion-select">
                        <option>% Percentage</option>
                        <option>$ Amount</option>
                    </select>

                    <label class="promotion-label">Value</label>
                    <div class="promotion-input-group">
                        <input class="promotion-input" type="number" value="15">
                        <span>%</span>
                    </div>
                    <small class="promotion-note">% discount applied to the accommodation rate.</small>

                    <label class="promotion-label">Promo code</label>
                    <div class="promo-details">
                        <input class="promotion-input" type="text" placeholder="Enter code" maxlength="15">
                        <button class="promotion-btn-small">Add promo code</button>
                    </div>
                    <div class="promotion-code-list">
                        BTC3fr <span>&#128465;</span>
                    </div>

                    <label class="promotion-label">Stay dates</label>
                    <div class="promotion-input-group">
                        <input class="promotion-input" type="text" placeholder="From">
                        <span>→</span>
                        <input class="promotion-input" type="text" placeholder="To">
                    </div>
                    <div class="date-tag-details">
                        <span class="date-tag">20 May 2025 - 4 June 2025 <img src="<?= get_template_directory_uri() ?>/dashboard/images/cross.png" alt=""></span>
                        <span class="date-tag">20 June 2025 - 26 June 2025 <img src="<?= get_template_directory_uri() ?>/dashboard/images/cross.png" alt=""></span>
                    </div>

                    <label class="promotion-label">Reservation dates</label>
                    <div class="promotion-input-group">
                        <input class="promotion-input" type="text" placeholder="From">
                        <span>→</span>
                        <input class="promotion-input" type="text" placeholder="To">
                    </div>
                    <div class="date-tag-details">
                        <span class="date-tag">20 May 2025 - 4 June 2025 <img src="<?= get_template_directory_uri() ?>/dashboard/images/cross.png" alt=""></span>
                    </div>

                    <div class="promotion-toggle-group">
                        <label class="promotion-label">Type of promotions</label>

                        <div class="promotion-toggle-item">
                            Last minute
                            <label class="promotion-toggle-switch top-click">
                                <input type="checkbox">
                                <span class="promotion-slider"></span>
                            </label>
                        </div>
                        <div class="promotion-toggle-item">
                            Early booking
                            <label class="promotion-toggle-switch top-click">
                                <input type="checkbox">
                                <span class="promotion-slider"></span>
                            </label>
                        </div>
                        <div class="promotion-toggle-item">
                            Minimum length of stay
                            <label class="promotion-toggle-switch top-click">
                                <input type="checkbox">
                                <span class="promotion-slider"></span>
                            </label>
                        </div>
                    </div>

                    <label class="promotion-label">Name of the promotion</label>
                    <input class="promotion-input" type="text" value="15% Off - Low season">

                    <div class="promotion-footer-buttons">
                        <button class="promotion-btn promotion-btn-back" >Back</button>
                        <button class="promotion-btn promotion-btn-save" id="close-popup">Save</button>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <script>
        document.getElementById('add-promotion').addEventListener('click', function () {
            document.getElementById('promotion-popup').style.display = 'flex';
        });

        document.getElementById('close-popup').addEventListener('click', function () {
            document.getElementById('promotion-popup').style.display = 'none';
        });



        Array.from(document.getElementsByClassName('top-click')).forEach(function (el) {
            el.addEventListener('click', function () {
                document.getElementById('promo-mid').style.display = 'flex';
                document.getElementById('promo-popup').style.display = 'block';
            });
        });

        // Close popup on close button click
        document.getElementById('close-promo').addEventListener('click', function () {
            document.getElementById('promo-mid').style.display = 'none';
            document.getElementById('promo-popup').style.display = 'none';
        });

    </script>
<?php get_footer('dashboard'); ?>