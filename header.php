<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <?php wp_head(); ?>
  <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/awesome-notifications/3.1.0/style.min.css" integrity="sha512-OFAsS5R1Fx+HUK9/h/ChqnFDrJGI0Y7nO05gg9E+Mv1UAzvAMvQdtOuPLhgPgDPHOgKWBvbovxT3eQSCr5hlLw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/awesome-notifications/3.1.0/index.var.min.js" integrity="sha512-gS8jKzzlhaUACXtBbUmj9/ITyZEAMM5TNwcL2Y226Xh6J/xH8mYzm6C/tHFkRVbi+tV1uyW7pIMSTehhBt6sBg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script>
    const home_url = '<?= get_home_url() ?>';
    const theme_url = '<?= get_template_directory_uri() ?>';
    const ajax_url = '<?= admin_url('admin-ajax.php') ?>';
    const site_url = '<?= site_url() ?>';

    let notifier = new AWN({});
  </script>
</head>

<body>
  <!-- header-start -->
  <header>
    <a href="<?= home_url(); ?>" class="logo">
      <img src="<?= get_template_directory_uri(); ?>/assets/images/icons/LOGO.svg" alt="logo">
    </a>

    <nav class="main-nav">
      <?php
      wp_nav_menu([
        'theme_location' => 'main_nav',
        'container' => false,
        'menu_class' => '',
        'items_wrap' => '<ul>%3$s</ul>',
      ]);
      ?>
    </nav>

    <div class="menu-right">
      <a href="#" class="btn">Propriétaires</a>
      <button class="menu-btn">
        <img src="<?= get_template_directory_uri(); ?>/assets/images/icons/Link.svg" alt="">
      </button>
    </div>

    <nav class="extra-nav">
      <div class="inner">

        <?php
        wp_nav_menu([
          'theme_location' => 'extra_nav',
          'container' => false,
          'menu_class' => 'destop-nav',
          'items_wrap' => '<ul class="destop-nav"><li><h3>Main Links</h3></li>%3$s</ul>',
        ]);
        ?>

        <!-- <ul>
          <li>
            <h3>Featured</h3>
          </li>
          <li><a href="#">Houses</a></li>
          <li><a href="#">Apartments</a></li>
          <li><a href="#">Office</a></li>
          <li><a href="#">Villa</a></li>
          <li><a href="#">Townhome</a></li>
          <li><a href="#">Bungalow</a></li>
          <li><a href="#">Loft</a></li>
        </ul>

        <div class="card-d">
          <div class="ts-card-slider" id="ts-slider-1">
            <?php foreach (['card-p1.png', 'card-p2.png', 'card-p3.png'] as $img): ?>
              <div>
                <img src="<?= get_template_directory_uri(); ?>/assets/images/<?= esc_attr($img); ?>"
                  alt="background-image" />
              </div>
            <?php endforeach; ?>
          </div>

          <div class="card-details">
            <div class="cd-header">
              <div class="location">
                <h3>FROM THE ROCK</h3>
                <div class="pin-type">
                  <img src="<?= get_template_directory_uri(); ?>/assets/images/icons/location-pin.svg" alt="location">
                  Laurentians | Val-des-Lacs
                </div>
              </div>
              <a href="#">$100/Night</a>
            </div>
            <ul>
              <li><img src="<?= get_template_directory_uri(); ?>/assets/images/icons/bed.svg" alt="bed">90 Guests</li>
              <li><img src="<?= get_template_directory_uri(); ?>/assets/images/icons/bed.svg" alt="bed">22 Rooms</li>
              <li><img src="<?= get_template_directory_uri(); ?>/assets/images/icons/bed.svg" alt="bed">50 beds</li>
              <li><img src="<?= get_template_directory_uri(); ?>/assets/images/icons/bath.svg" alt="bath">7 Baths</li>
            </ul>
          </div>
        </div> -->

      </div>
    </nav>
  </header>

  <!-- header-end-->
  <div class="main_content">