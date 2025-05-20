<?php

/**
 * Template Name: Booking
 */

get_header();
?>
<style>
    .error {
        background-color: #f8d7da;
        color: #721c24;
        padding: 20px;
        border-radius: 5px;
        margin: 30vh auto;
        max-width: min(600px, 100%);
    }

    .error h1 {
        margin: 0 0 10px;
    }

    .error p {
        margin: 0;
    }
</style>
<?php

$chalet_id = @$_GET['chalet_id'];
if (!$chalet_id && false) {
    echo "<div class=\"error\"><h1>Chalet ID not provided</h1><p>Please provide a valid chalet ID in the URL.</p></div>";
    get_footer();
    exit;
}
?>
<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/inc/css/page-booking.css" />
<div class="booking-page">


        <form id="bookingForm" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <input type="hidden" name="action" value="submit_booking_form" />

            <!-- Step indicators -->
            <div class="step-indicators">
            <span class="step active"></span>
            <span class="step"></span>
            <span class="step"></span>
            <span class="step"></span>
            </div>

            <!-- Step 1: Chalet & Dates -->
            <div class="tab active">
            <h3>Chalet Name</h3>

            <label for="checkin">Check-in Date</label>
            <input type="date" id="checkin" name="checkin" required />

            <label for="checkout">Check-out Date</label>
            <input type="date" id="checkout" name="checkout" required />
            </div>

            <!-- Step 2: Guests -->
            <div class="tab">
            <label for="adults">Adults</label>
            <input type="number" id="adults" name="adults" min="1" value="1" required />

            <label for="children">Children</label>
            <input type="number" id="children" name="children" min="0" value="0" />

            <label for="babies">Babies</label>
            <input type="number" id="babies" name="babies" min="0" value="0" />
            </div>

            <!-- Step 3: Guest Information -->
            <div class="tab">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required />

            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" />

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" required />

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required />

            <label for="language">Preferred Language</label>
            <select id="language" name="language">
                <option value="">-- Select Language --</option>
                <?php 
                $data = json_decode(file_get_contents(get_template_directory() . '/assets/json/languages.json'), true);
                if (is_array($data)) {
                foreach ($data as $lang) {
                    if (!empty($lang['code']) && !empty($lang['name'])) {
                    echo '<option value="' . esc_attr($lang['code']) . '">' . esc_html($lang['name']) . '</option>';
                    }
                }
                }
                ?>
            </select>
            </div>

            <!-- Step 4: Extras -->
            <div class="tab">
            <label><input type="checkbox" name="extras[]" value="late_checkout" /> Late Checkout</label>
            <label><input type="checkbox" name="extras[]" value="early_checkin" /> Early Check-in</label>
            <label><input type="checkbox" name="extras[]" value="extra_cleaning" /> Additional Cleaning</label>
            <label><input type="checkbox" name="extras[]" value="bbq_rental" /> BBQ Rental</label>
            <label><input type="checkbox" name="extras[]" value="firewood_bundle" /> Firewood Bundle</label>
            <label><input type="checkbox" name="extras[]" value="pet_fee" /> Pet Fee</label>
            </div>

            <div class="btn-container">
            <button type="button" id="prevBtn" onclick="nextPrev(-1)" disabled>Previous</button>
            <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
            </div>
        </form>

        <script>
            let currentTab = 0;
            const tabs = document.querySelectorAll(".tab");
            const steps = document.querySelectorAll(".step");

            function showTab(n) {
                tabs.forEach((tab, i) => {
                    tab.classList.toggle("active", i === n);
                    steps[i].classList.toggle("active", i === n);
                });

                document.getElementById("prevBtn").disabled = n === 0;
                document.getElementById("nextBtn").innerText = n === (tabs.length - 1) ? "Submit" : "Next";
            }

            function validateForm() {
                const inputs = tabs[currentTab].querySelectorAll("input, select");
                for (let input of inputs) {
                    if (input.hasAttribute("required") && !input.value) {
                        notifier.alert(`Please fill out the "${input.labels[0]?.innerText || input.name}" field.`);
                        return false;
                    }
                    if (input.type === "email") {
                        const re = /\S+@\S+\.\S+/;
                        if (!re.test(input.value)) {
                            notifier.alert("Please enter a valid email address.");
                            return false;
                        }
                    }
                }
                return true;
            }

            function nextPrev(n) {
                if (n === 1 && !validateForm()) return;

                currentTab += n;

                if (currentTab >= tabs.length) {
                    document.getElementById("bookingForm").submit();
                    return;
                }

                showTab(currentTab);
            }

            showTab(currentTab);
        </script>

</div>

<?php
get_footer();

