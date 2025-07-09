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
    <h1>News archive</h1>
    <?php
    // Start the Loop.
    if (have_posts()):
        while (have_posts()):
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>
                style="display: flex; align-items: flex-start; margin-bottom: 2em;">
                <div class="news-thumb" style="flex: 0 0 120px; margin-right: 20px;">
                    <?php
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('thumbnail', ['style' => 'width:100%;height:auto;']);
                    } else {
                        echo '<div style="width:100px;height:100px;background:#eee;display:flex;align-items:center;justify-content:center;">No Image</div>';
                    }
                    ?>
                </div>
                <div class="news-content" style="flex: 1;">
                    <div class="entry-header">
                        <h4 class="entry-title" style="margin:0 0 0.5em 0;">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        <div class="entry-meta" style="font-size:0.9em;color:#888;margin-bottom:0.5em;">
                            <?php echo get_the_date(); ?>
                        </div>
                    </div>
                    <div class="entry-excerpt" style="margin-bottom:0.5em;">
                        <?php the_excerpt(); ?>
                    </div>
                    <div class="entry-tags" style="font-size:0.9em;color:#555;">
                        <?php the_tags('Tags: ', ', ', ''); ?>
                    </div>
                </div>
            </article>
            <?php
        endwhile;
    else:
        echo '<p>No news found.</p>';
    endif;
    ?>

    <?php get_footer('dashboard'); ?>