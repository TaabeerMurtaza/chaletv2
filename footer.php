</div>
    <!-- footer-start -->
    <footer>
      <div class="container">
        <div class="footer-top">
          <a href="#" class="logo"
            ><img src="<?= get_template_directory_uri() ?>/assets/images/icons/LOGO-white.svg" alt="logo"
          /></a>
          <ul class="social-links">
            <li>Follow Us</li>
            <li>
              <a href="#"
                ><img src="<?= get_template_directory_uri() ?>/assets/images/icons/facebook.svg" alt="facebook"
              /></a>
            </li>
            <li>
              <a href="#"
                ><img src="<?= get_template_directory_uri() ?>/assets/images/icons/insta.svg" alt="Instagrame"
              /></a>
            </li>
          </ul>
        </div>
        <div class="divider-2"></div>
        <div class="footer-content">
          <div class="f-col">
            <h6>Regions</h6>
            <ul>
              <?php
              $regions = get_posts([
                'post_type' => 'region',
                'posts_per_page' => 9,
                'orderby' => 'title',
                'order' => 'ASC'
              ]);
              if ($regions) :
                foreach ($regions as $region) : ?>
                  <li><a href="<?= get_permalink($region->ID); ?>"><?= esc_html(get_the_title($region->ID)); ?></a></li>
                <?php endforeach;
              endif;
              ?>
            </ul>
          </div>
          <div class="f-col">
            <h6>Quick Links</h6>
            <ul>
              <li><a href="#">Owners</a></li>
              <li><a href="#">Corporate</a></li>
              <li><a href="#">Marriage</a></li>
              <li><a href="#">Promotions</a></li>
              <li><a href="#">Blog</a></li>
              <li><a href="#">Contact</a></li>
            </ul>
          </div>
          <div class="f-col">
            <h6>Contact Us</h6>
            <ul>
              <li><a href="#">info@booktonchalet.com</a></li>
              <li><a href="#"> 581-814-2225</a></li>
            </ul>
          </div>
          <div class="f-col">
            <h6>Quick Links</h6>
            <ul>
              <li>
                Charlevoix Regional County<br />
                Municipality, Quebec, Canada
              </li>
            </ul>
          </div>
        </div>
        <div class="divider-2"></div>
        <div class="footer-bottom">
          <p class="p-text">© 2024 by LA BOÎTE | CREATIVE MANAGEMENT</p>
          <ul>
            <li><a href="#">Privacy</a></li>
            <li>•</li>
            <li><a href="#">Terms</a></li>
          </ul>
        </div>
      </div>
    </footer>
    <!-- footer-end -->
    <!-- js start-->
    <script src="<?= get_template_directory_uri() ?>/assets/js/Jquery.js"></script>
    <script src="<?= get_template_directory_uri() ?>/assets/js/slick.min.js"></script>
    <script src="<?= get_template_directory_uri() ?>/assets/js/custom.js"></script>
    <?php wp_footer() ?>
    <!-- js end-->
  </body>
</html>
