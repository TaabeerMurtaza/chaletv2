<?php
/* Template Name: Dashboard Chats */

get_header(); ?>
<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/dashboard/css/chat-styles.css?v=<?= filemtime(get_template_directory() . '/dashboard/css/chat-styles.css') ?>" />
<main>
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            the_content();
            comments_template();
        endwhile;
    else :
        echo '<p>No content found.</p>';
    endif;
    ?>
</main>

<?php get_footer(); ?>
