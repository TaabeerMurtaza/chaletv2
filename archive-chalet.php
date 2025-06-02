<?php get_header(); ?>

<!-- banner-start -->
<div class="banner-section search-banner" style="background-image: none">
    <div class="banner-inner">
        <h1 class="main-title">DISCOVER OUR CHALETS FOR RENT</h1>
        <?php get_template_part('template-parts/search-form') ?>
    </div>
</div>
<!-- banner-end -->

<!-- tabs-section-start -->
<section class="tabs-section wtt-section">
    <div class="container">
        <div class="tabs-grid" style="display: none;">
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
        <h2>Chalets</h2>
        <div class="ts-grid-cards">
            <?php

            $args = array(
                'post_type' => 'chalet',
                'posts_per_page' => -1,
                'orderby' => 'date',
                'order' => 'DESC',

            );

            $region = @$_POST['region'] ?? '';
            $date = @$_POST['date'] ?? '';
            $guests = @$_POST['guests'] ?? '';
            $options = @$_POST['chalet_features'] ?? '';

            // Update $args based on filters
            if (!empty($region)) {
                $args['meta_query'][] = array(
                    'key' => 'region',
                    'value' => $region,
                    'compare' => 'LIKE',
                );
            }

            if (!empty($guests)) {
                $args['meta_query'][] = array(
                    'key' => 'guest_count',
                    'value' => $guests,
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                );
            }

            if (!empty($options)) {
                $args['meta_query'][] = array(
                    'key' => 'chalet_features',
                    'value' => $options,
                    'compare' => 'LIKE',
                );
            }

            // Ensure meta_query is set if any filters are used
            if (!empty($args['meta_query'])) {
                $args['meta_query']['relation'] = 'AND';
            }
            
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
</section>
<!-- tabs-section-end -->

<?php get_footer(); ?>