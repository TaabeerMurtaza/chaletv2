<?php
the_post();
get_header();
$chalet_id = get_the_ID();
?>

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
                if(!$icon){continue;}
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
    </div>
    <div class="form-right">
      <form>
        <div class="form-top">
          <img src="<?= get_template_directory_uri() ?>/assets/images/form-top.png" alt="" />
        </div>
        <div class="form-details">
          <img src="<?= get_template_directory_uri() ?>/assets/images/Section.png" alt="" />
        </div>
        <div class="form-bottom">
          <span>
            We detect frauds <br />
            Your identity will be verified
          </span>
          <button type="submit" class="btn">Book</button>
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
    </div>
  </div>
</section>
<?php get_footer(); ?>