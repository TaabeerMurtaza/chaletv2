<?php

/**
 * Template Name: Dashboard Reviews
 *  */
get_header('dashboard');

?>
<style>
  .pro-wrap .pro-img {
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    border: none;
  }
</style>
<div class="dashboard-content">
  <div class="dashboard-title">
    <button class="menu-btn openPanel"><img src="<?= get_template_directory_uri() ?>/dashboard/images/slide-icon.svg"
        alt=""></button>
    <h2 class="main-title">My Reviews</h2>
    <div class="dashboard-title-details">
      <a href="" class="dashboard-top-btn btn-h">Home page</a>
      <button class="shop-btn">
        <img src="<?= get_template_directory_uri() ?>/dashboard/images/Bell.svg" alt="" />
        <span class="notife">2</span>
      </button>
    </div>
  </div>
  <div class="reviews-wraper">
    <!-- <div class="filter-row">
      
      <div class="filter-details">
        <h3>Listing type </h3>
        <div class="filter-bottom-details">
          <select class="filter-select">
            <option value="volvo">Management </option>
            <option value="saab">Saab</option>
            <option value="mercedes">Mercedes</option>
            <option value="audi">Audi</option>
          </select>
          <button class="filter-btn">Filter</button>
        </div>
      </div>
      <div class="filter-details">
        <h3>Status</h3>
        <div class="filter-bottom-details">
          <select class="filter-select">
            <option value="volvo">Status </option>
            <option value="saab">Saab</option>
            <option value="mercedes">Mercedes</option>
            <option value="audi">Audi</option>
          </select>
          <button class="filter-btn">Filter</button>
        </div>
      </div>

    </div> -->
    <div class="spacer-tl"></div>

    <?php
    // Get current user ID
    $current_user_id = get_current_user_id();
    $profile_image_id = get_user_meta($current_user_id, 'profile_image', true);
    $profile_image_url = $profile_image_id ? wp_get_attachment_image_url($profile_image_id, [100, 100]) : get_avatar_url($user_id);


    if (user_can($current_user_id, 'administrator')) {
      // Admin: get all chalet posts
      $args = array(
        'post_type' => 'chalet',
        'posts_per_page' => -1,
        'fields' => 'ids',
      );
    } else {
      // Non-admin: only chalets owned by current user
      $args = array(
        'post_type' => 'chalet',
        'author' => $current_user_id,
        'posts_per_page' => -1,
        'fields' => 'ids',
      );
    }
    $chalet_posts = get_posts($args);

    if ($chalet_posts) {
      $paged = max(1, get_query_var('cpage') ? get_query_var('cpage') : get_query_var('paged'));
      $comments_per_page = 5; // or whatever number you want
    
      // Get comments for these posts
      $comments = get_comments(array(
        'post__in' => $chalet_posts,
        'status' => 'approve',
        'order' => 'DESC',
        'parent' => 0,
        'number' => $comments_per_page,
        'paged' => $paged,
      ));

      if ($comments) {
        foreach ($comments as $comment) {
          // Get rating if stored as comment meta (e.g., 'rating')
          $rating = get_comment_meta($comment->comment_ID, 'rating', true);
          $rating = $rating ? intval($rating) : 0;

          // Get related chalet post
          $chalet_title = get_the_title($comment->comment_post_ID);
          $chalet_link = get_permalink($comment->comment_post_ID);

          // Get commenter info
          $commenter_name = $comment->comment_author;
          $comment_date = get_comment_date('F j, Y', $comment);

          // Get reply if exists
          $replies = get_comments(array(
            'parent' => $comment->comment_ID,
            'status' => 'approve',
          ));
          ?>
          <div class="review-item">
            <h4 class="posted-date">Posted on <?= esc_html($comment_date); ?></h4>
            <h5><a href="<?= esc_url($chalet_link); ?>"><?= esc_html($chalet_title); ?></a></h5>
            <div class="pro-wrap">
              <div class="pro-img" style="background-image: url('<?= esc_url($profile_image_url); ?>');">
              </div>
              <div class="pro-detail">
                <span class="person-name"><?= esc_html($commenter_name); ?></span>
                <span class="date"><?= esc_html($comment_date); ?></span>
                <div class="rating-value" rating-value="<?= esc_attr($rating); ?>">
                  <?php
                  $star_url = get_template_directory_uri() . '/dashboard/images/icons/star.svg';
                  for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $rating) {
                      echo '<img src="' . esc_url($star_url) . '" alt="">';
                    } else {
                      echo '<img src="' . esc_url($star_url) . '" alt="" style="opacity:0.3;">';
                    }
                  }
                  ?>
                  <span>(<?= esc_html($rating); ?> of 5)</span>
                </div>
              </div>
            </div>
            <div class="massage">
              <p><?= esc_html($comment->comment_content); ?></p>
            </div>
            <div class="reply-warp">
              <?php if ($replies): ?>
                <?php foreach ($replies as $reply): ?>
                  <p><strong>Your reply:</strong> <?= esc_html($reply->comment_content); ?></p>
                <?php endforeach; ?>
              <?php else: ?>
                <p>Your reply</p>
                <form method="post" action="<?= admin_url('admin-post.php'); ?>" class="reply-form">
                  <textarea name="reply_content" required></textarea>
                  <input type="hidden" name="parent_comment_id" value="<?= esc_attr($comment->comment_ID); ?>">
                  <input type="hidden" name="action" value="reply_to_review">
                  <?php wp_nonce_field('reply_to_review_action', 'reply_to_review_nonce'); ?>
                  <button type="submit">Reply to Review</button>
                </form>

              <?php endif; ?>
            </div>
          </div>
          <br>
          <div class="spacer-tl"></div>
          <br>
          <?php
        }
      } else {
        echo '<p>No reviews found.</p>';
      }
    } else {
      echo '<p>No chalets found.</p>';
    }
    ?>

  </div>
  <div class="pagination">
    <?php
    $total_comments = get_comments(array(
      'post__in' => $chalet_posts,
      'status' => 'approve',
      'parent' => 0,
      'count' => true,
    ));

    $total_pages = ceil($total_comments / $comments_per_page);

    echo paginate_links(array(
      'base' => get_pagenum_link(1) . '%_%',
      'format' => (get_option('permalink_structure') ? 'page/%#%/' : '&cpage=%#%'),
      'current' => $paged,
      'total' => $total_pages,
      'prev_text' => '<',
      'next_text' => '>',
    ));
    ?>
  </div>

</div>
<?php get_footer('dashboard'); ?>