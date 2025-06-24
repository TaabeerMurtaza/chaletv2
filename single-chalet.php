<?php
the_post();
get_header();
$chalet_id = get_the_ID();
?>
<style>
  .form-right {
    margin-left: 5rem;
  }

  .form-right form {
    border: 1px solid #ddd;
    padding: 16px;
  }

  .nightly_price {
    display: block;
    font-family: "Roboto";
    font-weight: bold;
    font-size: 20px;
    color: var(--primary);
    line-height: 52px;
  }

  .minimum_stay {
    font-size: 12px;
    margin-bottom: 16px;
    color: #666;
  }

  .arrival_departure {
    display: flex;
    gap: 10px;
  }

  .guests {
    position: relative;
    margin-bottom: 16px;
  }

  .guests input[type="number"] {
    width: 100%;
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
    cursor: pointer;
  }

  .guests_popup {
    position: absolute;
    top: 100%;
    left: 0;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    display: none;
    z-index: 1000;
  }

  .guests_popup .popup-content {
    display: flex;
    justify-content: space-between;
  }

  .guests_popup .popup-content div {
    flex: 1;
    margin-right: 10px;
  }

  .guests_popup .popup-content div:last-child {
    margin-right: 0;
  }

  .guests_popup .popup-content #apply_guests {
    background: #7b8cff;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 10px 32px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    height: max-content;
    margin-top: auto;
    margin-bottom: auto;
    transition: background 0.2s;
  }
</style>
<!-- header-bottom-start -->
<section class="header-bottom">
  <div class="container">
    <span>Details -</span>
    <span> Availability -</span>
    <span> Nearby attractions -</span>
    <span>Event Service </span>
  </div>
  <div class="image-slider">
    <?php
    $images = carbon_get_post_meta($chalet_id, 'chalet_images');
    if ($images) {
      foreach ($images as $image) {
        $img = wp_get_attachment_url($image);
        echo '<div class="slide"><img src="' . esc_url($img) . '" alt="Chalet Image" /></div>';
      }
    }
    ?>
  </div>
</section>
<!-- header-bottom-end -->

<!-- overview-section-start -->
<section class="overview">
  <div class="container">
    <div class="main-content-left">
      <div class="heading">
        <img class="overview-logo" src="<?= get_template_directory_uri() ?>/assets/images/icons/image 21.svg" alt="" />
        <span>Hosted by <?= get_the_author() ?></span>
        <h2><?php the_title(); ?></h2>
        <p><?= esc_html(carbon_get_post_meta($chalet_id, 'location')) ?></p>
      </div>
      <!-- <div class="overview-content">
        <h3>Overview</h3>
        <div class="icons">
          <?php
          $features = carbon_get_post_meta($chalet_id, 'indoor_features');
          if ($features) {
            foreach ($features as $feature) {
              $icon = carbon_get_post_meta($feature['id'], 'feature_icon');
              $title = get_the_title($feature['id']);
              ?>
              <div class="icon">
                <div class="img">
                  <img src="<?= esc_url($icon) ?>" alt="<?= esc_attr($title) ?>" />
                </div>
                <div class="content">
                  <span><?= esc_html($title) ?></span>
                </div>
              </div>
              <?php
            }
          }
          ?>
        </div>
      </div> -->
      <div class="description">
        <h3>Description</h3>
        <p><?= wp_kses_post(carbon_get_post_meta($chalet_id, 'description')) ?></p>
      </div>
      <div class="lists">
        <h3>Indoor Features</h3>
        <div class="list">
          <ul>
            <?php
            if ($features) {
              foreach ($features as $feature) {
                $icon = carbon_get_post_meta($feature['id'], 'feature_icon');
                $title = get_the_title($feature['id']);
                ?>
                <li>
                  <img src="<?= esc_url($icon) ?>" alt="<?= esc_attr($title) ?>" /><?= esc_html($title) ?>
                </li>
                <?php
              }
            }
            ?>
          </ul>
          <div class="spacer"></div>
        </div>
        <h3>Kitchen</h3>
        <div class="list">
          <ul>
            <?php
            $kitchen_features = carbon_get_post_meta($chalet_id, 'kitchen_features');
            if ($kitchen_features) {
              foreach ($kitchen_features as $feature) {
                $icon = carbon_get_post_meta($feature['id'], 'feature_icon');
                $title = get_the_title($feature['id']);
                ?>
                <li>
                  <img src="<?= esc_url($icon) ?>" alt="<?= esc_attr($title) ?>" /><?= esc_html($title) ?>
                </li>
                <?php
              }
            }
            ?>
          </ul>
          <div class="spacer"></div>
        </div>
        <h3>Outdoor Features</h3>
        <div class="list">
          <ul>
            <?php
            $outdoor_features = carbon_get_post_meta($chalet_id, 'outdoor_features');
            if ($outdoor_features) {
              foreach ($outdoor_features as $feature) {
                $icon = carbon_get_post_meta($feature['id'], 'feature_icon');
                $title = get_the_title($feature['id']);
                ?>
                <li>
                  <img src="<?= esc_url($icon) ?>" alt="<?= esc_attr($title) ?>" /><?= esc_html($title) ?>
                </li>
                <?php
              }
            }
            ?>
          </ul>
          <div class="spacer"></div>
        </div>
        <div class="beds">
          <h3>Bed Layout</h3>
          <div class="cards">
            <?php
            $bedrooms = carbon_get_post_meta($chalet_id, 'bedrooms');
            if ($bedrooms) {
              foreach ($bedrooms as $bedroom) {
                ?>
                <div class="bed-layout">
                  <img src="<?= get_template_directory_uri() ?>/assets/images/icons/wc-icon-beds-1.png" alt="" />
                  <span><?= esc_html($bedroom['name']) ?></span>
                  <p><?= esc_html($bedroom['type']) ?></p>
                </div>
                <?php
              }
            }
            ?>
          </div>
        </div>
      </div>
      <div class="prices">
        <div class="price">
          <h3>Prices</h3>
          <div class="content">
            <p>
              Enter your exact dates and number of guests to get an accurate
              price for your stay.
            </p>
            <p>The refundable deposit for this property is $<?= esc_html(carbon_get_post_meta($chalet_id, 'deposit')) ?>
              CAD.</p>
            <span>Always communicate and pay through WeChalet</span>
            <p>
              To protect your payment, never send money or communicate
              outside of the WeChalet website or app.
            </p>
          </div>
        </div>
        <div class="rules">
          <h3>House Rules</h3>
          <div class="content">
            <ul>
              <?php
              $rules = carbon_get_post_meta($chalet_id, 'house_rules');
              if ($rules) {
                if (in_array('children_allowed', $rules)) {
                  echo '<li>Children are welcome (2 to 12 years old)</li>';
                }
                if (in_array('infants_allowed', $rules)) {
                  echo '<li>Infants under 2 years old are welcome</li>';
                }
                if (in_array('pets_allowed', $rules)) {
                  echo '<li>Pets are not allowed</li>';
                }
                if (in_array('smoking_allowed', $rules)) {
                  echo '<li>Smoking inside is not allowed</li>';
                }
                if (in_array('events_allowed', $rules)) {
                  echo '<li>Parties and events are not allowed</li>';
                }
              }
              ?>
              <li>Check-in Hour : <?= esc_html(carbon_get_post_meta($chalet_id, 'checkin_time')) ?></li>
              <li>Check-out Hour : <?= esc_html(carbon_get_post_meta($chalet_id, 'checkout_time')) ?></li>
            </ul>
          </div>
        </div>
        <div class="cancelation">
          <h3>Cancellations</h3>
          <div class="content">
            <p><?= wp_kses_post(carbon_get_post_meta($chalet_id, 'cancellation_policy')) ?></p>
          </div>
        </div>
        <div class="policy">
          <h3>Reservation Policy</h3>
          <div class="content">
            <ul>
              <?php
              $policy_key = carbon_get_post_meta($chalet_id, 'reservation_policy');
              $policy_map = [
                'policy_50_50_3' => 'Policy 50-50 (3 days before stay)',
                'policy_50_50_14' => 'Policy 50-50 (14 days before stay)',
                'policy_25_25_50_14' => 'Policy 25-25-50 (14 days before stay)',
              ];
              ?>
              <li><?= isset($policy_map[$policy_key]) ? esc_html($policy_map[$policy_key]) : esc_html($policy_key) ?>
              </li>

              <li>Security deposit</li>
              <li>Payment method accepted</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="accessibility">
        <div class="access">
          <h3>Accessibility</h3>
          <ul style="display: flex; flex-wrap: wrap;gap: 10px;">
            <?php
            $accessibility_features = carbon_get_post_meta($chalet_id, 'accessibility_features');
            if ($accessibility_features) {
              foreach ($accessibility_features as $feature) {
                $icon = carbon_get_post_meta($feature['id'], 'feature_icon');
                if (!$icon) {
                  continue;
                }
                ?>
                <li>
                  <img src="<?= esc_url($icon) ?>" alt="<?= esc_attr($title) ?>" />
                </li>
                <?php
              }
            }
            ?>
          </ul>
        </div>
      </div>
      <div class="map">
        <h3>Map</h3>
        <img src="<?= get_template_directory_uri() ?>/assets/images/map.png" style="max-width:100%;" />

      </div>
      <?php
      // Show reviews
      comments_template();

      ?>
      <br>
      <br>
      <br>
    </div>
    <div class="form-right">
      <!-- <img src="<?= get_template_directory_uri() ?>/assets/images/form-top.png" alt="" /> -->
      <form id="booking_form" method="post" action="<?= get_home_url() . '/add-booking?id=' . get_the_ID() ?>"
        novalidate>
        <div class="form-top">
          <div style="display:flex;align-items:center;">
            <span
              class="nightly_price">$<?= carbon_get_post_meta(get_the_ID(), 'default_rate_weekday'); ?>CAD</span>&nbsp;
            / night
          </div>
          <div class="minimum_stay">
            <span>Minimum stay</span>
            <span><?= carbon_get_post_meta(get_the_ID(), 'min_nights') ?> nights</span>
          </div>
        </div>
        <div class="form-details">
            <div class="arrival_departure" style="margin-bottom: 16px;">
            <div class="arrival">
              <label for="arrival_date">Arrival</label>
              <input type="text" id="arrival_date" name="arrival_date" placeholder="Select arrival date & time"
              autocomplete="off"
              style="width:100%;padding:8px;border-radius:4px;border:1px solid #ccc;margin-bottom:10px;" readonly
              required>
              <span class="error" id="arrival_error" style="color:red;display:none;font-size:12px;">Please select an
              arrival date and time.</span>
            </div>
            <div class="departure">
              <label for="departure_date">Departure</label>
              <input type="text" id="departure_date" name="departure_date" placeholder="Select departure date & time"
              autocomplete="off" style="width:100%;padding:8px;border-radius:4px;border:1px solid #ccc;" readonly
              required>
              <span class="error" id="departure_error" style="color:red;display:none;font-size:12px;">Please select a
              departure date and time.</span>
            </div>
            </div>
          <div class="guests">
            <label for="guests">Guests</label>
            <input type="text" name="guests" id="guests_total" readonly required>
            <input type="hidden" name="adult_guests" id="hidden_adult_guests" autocomplete="off">
            <input type="hidden" name="child_guests" id="hidden_child_guests" autocomplete="off">
            <span class="error" id="guests_error" style="color:red;display:none;font-size:12px;">Please select at least
              1 guest.</span>
            <div class="guests_popup">
              <div class="popup-content">
                <div class="adults">
                  <span>Adults</span>
                  <input type="number" id="adult_guests" name="adult_guests" value="1" min="1" max="10" />
                </div>
                <div class="children">
                  <span>Children</span>
                  <input type="number" id="child_guests" name="child_guests" value="0" min="0" max="10" />
                </div>
                <button type="button" id="apply_guests">Apply</button>
              </div>
            </div>
          </div>
          <!-- <img src="<?= get_template_directory_uri() ?>/assets/images/Section.png" alt="" /> -->
          <div class="form-bottom">
            <span>
              We detect frauds <br />
              Your identity will be verified
            </span>
            <button type="submit" class="btn">Book</button>
            <br>
            <!-- <a href="<?= get_home_url() . '/booking?id=' . get_the_ID() ?>" class="book_button">Book</a> -->
            <div class="btn-row">
              <a href="#" class="share-btn">
                <img src="<?= get_template_directory_uri() ?>/assets/images/share-icon.png" alt="" />Share
              </a>
              <a href="#" class="share-btn">
                <img src="<?= get_template_directory_uri() ?>/assets/images/heart.png" alt="" />Save
              </a>
            </div>
          </div>
      </form>
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          var form = document.getElementById('booking_form');
          var arrival = document.getElementById('arrival_date');
          var departure = document.getElementById('departure_date');
          var guestsTotal = document.getElementById('guests_total');
          var hiddenAdult = document.getElementById('hidden_adult_guests');
          var hiddenChild = document.getElementById('hidden_child_guests');
          var arrivalError = document.getElementById('arrival_error');
          var departureError = document.getElementById('departure_error');
          var guestsError = document.getElementById('guests_error');

          if (form) {
            form.addEventListener('submit', function (e) {
              var valid = true;

              // Validate arrival date
              if (!arrival.value) {
                arrivalError.style.display = 'block';
                valid = false;
              } else {
                arrivalError.style.display = 'none';
              }

              // Validate departure date
              if (!departure.value) {
                departureError.style.display = 'block';
                valid = false;
              } else {
                departureError.style.display = 'none';
              }

              // Validate guests
              var adults = parseInt(hiddenAdult.value, 10) || 0;
              var children = parseInt(hiddenChild.value, 10) || 0;
              if ((adults + children) < 1) {
                guestsError.style.display = 'block';
                valid = false;
              } else {
                guestsError.style.display = 'none';
              }

              if (!valid) {
                e.preventDefault();
              }
            });
          }
        });
      </script>
    </div>
  </div>
</section>
<!-- <script>
  function initMap() {
    const location = { lat: 33.6844, lng: 73.0479 }; // Example: Islamabad
    const map = new google.maps.Map(document.getElementById("map"), {
      zoom: 10,
      center: location,
    });

    new google.maps.Marker({
      position: location,
      map: map,
    });
  }
</script> -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const arrival = flatpickr("#arrival_date", {
      enableTime: true,
      minDate: "today",
      dateFormat: "Y-m-d H:i",
      time_24hr: true,
      onChange: function (selectedDates, dateStr, instance) {
      if (selectedDates.length) {
        departure.set('minDate', dateStr);
      }
      }
    });
    const departure = flatpickr("#departure_date", {
      enableTime: true,
      minDate: "today",
      dateFormat: "Y-m-d H:i",
      time_24hr: true,
      onChange: function (selectedDates, dateStr, instance) {
      if (selectedDates.length) {
        arrival.set('maxDate', dateStr);
      }
      }
    });

    // 
    var guestsInput = document.getElementById('guests_total');
    var guestsPopup = document.querySelector('.guests_popup');
    var adultInput = document.getElementById('adult_guests');
    var childInput = document.getElementById('child_guests');
    var applyBtn = document.getElementById('apply_guests');
    var hiddenAdult = document.getElementById('hidden_adult_guests');
    var hiddenChild = document.getElementById('hidden_child_guests');
    var guestsDiv = document.querySelector('.guests');

    function updateGuestsInput() {
      var adults = parseInt(adultInput.value, 10) || 0;
      var children = parseInt(childInput.value, 10) || 0;
      guestsInput.value = adults + children + ' Guests';
      hiddenAdult.value = adults;
      hiddenChild.value = children;
    }

    // Show popup when clicking on guests input
    guestsInput.addEventListener('click', function (e) {
      guestsPopup.style.display = 'block';
    });

    // Hide popup when clicking outside
    document.addEventListener('mousedown', function (e) {
      if (!guestsDiv.contains(e.target)) {
        guestsPopup.style.display = 'none';
      }
    });

    // Apply button sets values and closes popup
    applyBtn.addEventListener('click', function () {
      updateGuestsInput();
      guestsPopup.style.display = 'none';
    });

    // Initialize values
    updateGuestsInput();
  });
</script>
<?php get_footer(); ?>