<?php

/**
 * Template for displaying the news archive.
 *  */
get_header('dashboard');

?>
<style>
    .news-archive {
        display: flex;
        flex-direction: column;
    }
</style>
<div class="news-archive">
    <h1 style="margin-bottom:2rem;"><?php the_title(); ?></h1>
    <?php
    the_post_thumbnail('full', ['style' => 'width:80%;height:auto;']);
    // Start the Loop.
    the_content();
    ?>
</div>
    <?php get_footer('dashboard'); ?>