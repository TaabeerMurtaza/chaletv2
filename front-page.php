<?php get_header(); ?>
<!-- banner-start -->
<div class="banner-section"
  style="background-image: url(<?= get_template_directory_uri() ?>/assets/images/banner-bg.jpg)">
  <div class="banner-inner">
    <h1 class="main-title">AT THE HEART OF YOUR HOLIDAYS</h1>
    <p>Discover our chalets for rent in Quebec</p>
    <div class="form-wraper">
      <form action="">
        <div class="form-g">
          <select name="region" id="region-select">
            <option value="">Select Region</option>
            <?php
            $query = new WP_Query([
              'post_type' => 'region',
              'posts_per_page' => -1,
              'orderby' => 'name',
            ]);
            if ($query->have_posts()) {

              while ($query->have_posts()) {
                $query->the_post();
                ?>
                <option value="<?php echo get_the_ID(); ?>">
                  <?php echo get_the_title(); ?>
                </option>
                <?php
              }
            }
            ?>
          </select>
        </div>
        <div class="form-g">
          <select name="When" id="cars">
            <option value="Region">Region</option>
            <option value="saab">Saab</option>
            <option value="mercedes">Mercedes</option>
            <option value="audi">Audi</option>
          </select>
        </div>
        <div class="form-g">
          <select name="Guests" id="cars">
            <option value="Region">Region</option>
            <option value="saab">Saab</option>
            <option value="mercedes">Mercedes</option>
            <option value="audi">Audi</option>
          </select>
        </div>
        <div class="form-g option">
          <select name="Options" id="cars">
            <option value="Region">Region</option>
            <option value="saab">Saab</option>
            <option value="mercedes">Mercedes</option>
            <option value="audi">Audi</option>
          </select>
        </div>
        <div class="form-g">
          <input type="submit" class="btn" />
        </div>
      </form>
    </div>
  </div>
</div>
<!-- banner-end -->

<!-- tabs-section-start -->
<section class="tabs-section">
  <div class="container">
    <h2>Featured</h2>
    <div class="tabs-grid" id="featured_chalets_types">
      <div class="tab">
        <div class="icon">
          <img src="<?= get_template_directory_uri() ?>/assets/images/icons/tb-Icon1.svg" alt="tab-icon" />
        </div>
        Houses
      </div>
      <div class="tab">
        <div class="icon">
          <img src="<?= get_template_directory_uri() ?>/assets/images/icons/tb-Icon2.svg" alt="tab-icon" />
        </div>
        Apartments
      </div>
      <div class="tab">
        <div class="icon">
          <img src="<?= get_template_directory_uri() ?>/assets/images/icons/tb-Icon3.svg" alt="tab-icon" />
        </div>
        Office
      </div>
      <div class="tab">
        <div class="icon">
          <img src="<?= get_template_directory_uri() ?>/assets/images/icons/tb-Icon4.svg" alt="tab-icon" />
        </div>
        Villa
      </div>
      <div class="tab">
        <div class="icon">
          <img src="<?= get_template_directory_uri() ?>/assets/images/icons/tb-Icon5.svg" alt="tab-icon" />
        </div>
        Townhome
      </div>
      <div class="tab">
        <div class="icon">
          <img src="<?= get_template_directory_uri() ?>/assets/images/icons/tb-Icon6.svg" alt="tab-icon" />
        </div>
        Bungalow
      </div>
      <div class="tab">
        <div class="icon">
          <img src="<?= get_template_directory_uri() ?>/assets/images/icons/tb-Icon7.svg" alt="tab-icon" />
        </div>
        Loft
      </div>
    </div>
    <div class="divider-1"></div>

    <!-- Featured Chalets Section -->
    <div class="featured-chalets">
      <div class="chalets-grid" id="featured-chalets">
      </div>
    </div>
  </div>
</section>
<?php if (0): ?>
  <div class="container">
    <h2>Featured Chalets by Region</h2>

    <div class="ts-grid-cards">
      <?php
      $args = array(
        'post_type' => 'chalet',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC'
      );
      $chalets = new WP_Query($args);

      if ($chalets->have_posts()) {
        $i = 1;
        while ($chalets->have_posts()) {
          $chalets->the_post();
          $guest_count = carbon_get_post_meta(get_the_ID(), 'guest_count');
          $baths = carbon_get_post_meta(get_the_ID(), 'baths');
          $bedrooms = carbon_get_post_meta(get_the_ID(), 'bedrooms');
          $weekday_rate = carbon_get_post_meta(get_the_ID(), 'default_rate_weekday');
          // Get region association from Carbon Fields
          $region = carbon_get_post_meta(get_the_ID(), 'region');
          $location = !empty($region) ? get_the_title($region[0]['id']) : 'Location not specified';
          $featured_image = get_the_post_thumbnail_url();
          ?>
          <div class="card-d">
            <div class="ts-card-slider" id="ts-slider-<?php echo $i; ?>">
              <?php
              // Get gallery images if available
              $gallery = carbon_get_post_meta(get_the_ID(), 'chalet_images');
              if (!empty($gallery)) {
                foreach ($gallery as $id) {
                  $image = wp_get_attachment_image_src($id, 'full')[0];
                  echo '<div><img class="card_image" src="' . esc_url($image) . '" alt="' . esc_attr(get_the_title()) . '" /></div>';
                }
              } else {
                // Fallback to featured image if exists
                if (!empty($featured_image)) {
                  echo '<div><img class="card_image" src="' . esc_url($featured_image) . '" alt="' . get_the_title() . '" /></div>';
                } else {
                  // Fallback to default image
                  $default_image = get_template_directory_uri() . '/assets/images/card-p1.png';
                  echo '<div><img class="card_image" src="' . esc_url($default_image) . '" alt="' . get_the_title() . '" /></div>';
                }
              }
              ?>
            </div>
            <div class="card-details">
              <div class="cd-header">
                <div class="location">
                  <h3><a href="<?= get_permalink() ?>" class="card_anchor"><?php the_title(); ?></a></h3>
                  <div class="pin-type">
                    <img src="<?= get_template_directory_uri() ?>/assets/images/icons/location-pin.svg"
                      alt="location" /><?php echo esc_html($location); ?>
                  </div>
                </div>
                <a>$<?php echo $weekday_rate; ?>/Night</a>
              </div>

              <ul>
                <li><img src="<?= get_template_directory_uri() ?>/assets/images/icons/bed.svg"
                    alt="bed" /><?php echo $guest_count; ?> Guests</li>
                <li><img src="<?= get_template_directory_uri() ?>/assets/images/icons/bed.svg"
                    alt="bed" /><?php echo count($bedrooms); ?> Rooms</li>
                <li><img src="<?= get_template_directory_uri() ?>/assets/images/icons/bed.svg" alt="bed" /><?php
                  $total_beds = 0;
                  foreach ($bedrooms as $room) {
                    $total_beds += $room['beds'];
                  }
                  echo $total_beds; ?> beds</li>
                <li><img src="<?= get_template_directory_uri() ?>/assets/images/icons/bath.svg"
                    alt="bed" /><?php echo $baths; ?> Baths</li>
              </ul>
            </div>

          </div>
          <?php
          $i++;
        }
        wp_reset_postdata();
      }
      ?>
    </div>
  </div>
<?php endif; ?>
</div>
<br>
<br>

</section>
<!-- tabs-section-end -->
<!-- banner-text-start -->
<section class="banner-text"
  style="background-image: url(<?= get_template_directory_uri() ?>/assets/images/banner-bg2.png)">
  <h2>WHERE TO TRAVEL?</h2>
</section>
<!-- banner-text-end -->
<section class="landscapes-section">
  <div class="container">
    <h2>Explore the region's beautiful landscapes</h2>
    <div class="grid-gallery">
      <?php
      $regions = new WP_Query([
        'post_type' => 'region',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
      ]);

      if ($regions->have_posts()) {
        $c = 0;
        while ($regions->have_posts()) {
          $c++;
          if ($c > 6) {
            break;
          }
          $regions->the_post();
          ?>
          <a href="<?= get_the_permalink() ?>" class="gg-card gg-<?php echo $c; ?>">
            <h5><?php the_title(); ?></h5>
            <img style=""
              src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full') ?: get_template_directory_uri() . '/assets/images/gg-card.png'; ?>"
              alt="<?php the_title(); ?>" />
          </a>
          <?php
        }
        wp_reset_postdata();
      } else {
        echo '<p>No regions found.</p>';
      }
      ?>
    </div>
  </div>
</section>

<section class="uniqe-slider-section">
  <div class="container">
    <h2>Live a unique experience at the chalet </h2>
    <div class="slider-gallery gall-slider">
      <?php
      $experiences = new WP_Query([
        'post_type' => 'experience',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
      ]);
      if ($experiences->have_posts()) {
        while ($experiences->have_posts()) {
          $experiences->the_post();
          ?>
          <div class="">
            <div class="us-card">
              <h5><?php the_title(); ?></h5>
              <img
                src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full') ?: get_template_directory_uri() . '/assets/images/gg-card.png'; ?>"
                alt="<?php the_title(); ?>" />
            </div>
          </div>
          <?php
        }
        wp_reset_postdata();
      } else {
        echo '<p>No experiences found.</p>';
      }
      ?>
    </div>
  </div>
</section>
<!-- banner-text-start -->
<section class="banner-text"
  style="background-image: url(<?= get_template_directory_uri() ?>/assets/images/banner-bg2.png)">
  <h2>AMENITIES & FEATURES</h2>
</section>
<!-- banner-text-end -->
<section class="uniqe-slider-section">
  <div class="container">
    <h2>FIND A CHALET WITH YOUR FAVOURITE AMENITIES </h2>
    <div class="slider-gallery gall-slider">
      <?php
      $features = new WP_Query([
        'post_type' => 'chalet_feature',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
      ]);
      if ($features->have_posts()) {
        while ($features->have_posts()) {
          $features->the_post();
          ?>
          <div class="">
            <div class="us-card">
              <h5><?php the_title(); ?></h5>
              <img
                src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full') ?: get_template_directory_uri() . '/assets/images/gg-card.png'; ?>"
                alt="<?php the_title(); ?>" />
            </div>
          </div>
          <?php
        }
        wp_reset_postdata();
      } else {
        echo '<p>No features found.</p>';
      }
      ?>
    </div>
  </div>
</section>

<section class="three-column">
  <div class="container">
    <div class="thc-card">
      <img src="<?= get_template_directory_uri() ?>/assets/images/icons/thc-1.svg" alt="card-icons" />
      <h5>Agency Approved</h5>
      <p>
        At "<span> Book Ton Chalet</span> ", each advert is carefully <span>checked & validated</span> to
        guarantee compliance , security and an exceptional stay 
      </p>
    </div>
    <div class="thc-card">
      <img src="<?= get_template_directory_uri() ?>/assets/images/icons/thc-2.svg" alt="card-icons" />
      <h5>Online booking</h5>
      <p>
        Visit our
        <span>' Online Booking ' section to easily book a chalet via
          our 100%</span> secure payment platform !
      </p>
    </div>
    <div class="thc-card">
      <img src="<?= get_template_directory_uri() ?>/assets/images/icons/thc-3.svg" alt="card-icons" />
      <h5>CITQ</h5>
      <p>
        All chalets displayed <span>have a certificate</span>  from
        the Corporation de l'Industrie Tourismique du Québec.
      </p>
    </div>
  </div>
</section>
<?php

// Add script to handle region selection
add_action('wp_footer', function () { ?>
  <script>
    const featuredChalets = document.getElementById('featured-chalets');
    function loadChalets(type = 'all') {
      const data = {
        action: 'get_chalets_by_type',
        type: type
      };
      // loading animation
      featuredChalets.innerHTML = '<div class="loading"></div>';
      fetch(ajaxurl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(data)
      })
        .then(response => response.text())
        .then(html => {
          featuredChalets.innerHTML = html;
          $('#featured-chalets .ts-card-slider').each(function () {
            $(this).slick({
              infinite: true,
              speed: 500,
              fade: true,
              cssEase: "linear",
              prevArrow:
                "<button type='button' class='slick-prev cs-arrow'><img src='" + theme_url + "/assets/images/icons/arrow-l.svg' alt='icons' /></button>",
              nextArrow:
                "<button type='button' class='slick-next cs-arrow'><img src='" + theme_url + "/assets/images/icons/arrow-r.svg' alt='icons' /></button>",
            });
          });
        })
        .catch(error => console.error('Error:', error));
    }
    document.addEventListener('DOMContentLoaded', function () {

      // Load initial featured chalets
      loadChalets();


    });
    document.querySelectorAll('#featured_chalets_types .tab').forEach(tab => {
      tab.addEventListener('click', function () {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const type = this.textContent.trim().toLowerCase();
        loadChalets(type);
      });
    });
  </script>
<?php });

get_footer();