<?php

/**
 * Template Name: Dashboard
 *  */
get_header('dashboard');

?>
<div class="dashboard-content">
    <div class="dashboard-title">
        <button class="menu-btn openPanel"><img
                src="<?= get_template_directory_uri() ?>/dashboard/images/slide-icon.svg" alt=""></button>
        <h2 class="main-title">Dashboard</h2>
        <div class="dashboard-title-details">
            <a href="" class="dashboard-top-btn btn-h">Home page</a>
            <button class="shop-btn">
                <img src="<?= get_template_directory_uri() ?>/dashboard/images/Bell.svg" alt="" />
                <span class="notife">2</span>
            </button>
        </div>
    </div>
    <div class="booking-details">
        <h3 class="dashboard-sub-title">Next Bookings</h3>
        <span><a href="#">Huge Sunny Villa</a> from March 07 2025 to March 09
            2025</span>
        <span><a href="#">Huge Sunny Villa</a> from March 07 2025 to March 09
            2025</span>
    </div>
    <div class="dashboard-main-details">
        <h3 class="dashboard-sub-title">Bookings - next 30 days</h3>
        <div class="calender-details">
            <div id="calendar"></div>
            <div class="booking-btns-row">
                <div class="booking-btn-detail">
                    <a href="">
                        <img src="<?= get_template_directory_uri() ?>/dashboard/images/booking-btn.png" alt="" />
                        Internal Booking
                    </a>
                </div>
                <div class="booking-btn-detail">
                    <a href="">
                        <img src="<?= get_template_directory_uri() ?>/dashboard/images/booking-btn2.png" alt="" />
                        External Booking
                    </a>
                </div>
                <div class="booking-btn-detail">
                    <div class="select-details">
                        <input type="radio" class="square-radio" />
                        <span>Free</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="booking-row">
        <div class="main-booking">
            <h3 class="dashboard-sub-title">Your most visited chalets</h3>
            <?php
            // Query recent chalets (assuming 'chalet' is a custom post type)
            $args = array(
                'post_type' => 'chalet',
                'posts_per_page' => 3,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
            );
            $recent_chalets = new WP_Query($args);

            if ($recent_chalets->have_posts()): ?>
                <div class="main-detail-row">
                    <?php while ($recent_chalets->have_posts()):
                        $recent_chalets->the_post(); ?>
                        <div class="main-detail">
                            <div class="img-wrapper">
                                <?php if (has_post_thumbnail()): ?>
                                    <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title_attribute(); ?>" />
                                <?php else: ?>
                                    <img src="<?= get_template_directory_uri() ?>/dashboard/images/chalet.jpeg" alt="" />
                                <?php endif; ?>
                            </div>
                            <div class="detail">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                <?php
                                // Example: get view count from post meta (replace 'view_count' with your actual meta key)
                                $views = get_post_meta(get_the_ID(), 'view_count', true);
                                ?>
                                <span><?php echo $views ? esc_html($views) : '0'; ?> Views</span>
                            </div>
                        </div>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </div>
            <?php else: ?>
                <p>No recent chalets found.</p>
            <?php endif; ?>
        </div>
        <div class="main-booking">
            <h3 class="dashboard-sub-title">Your most booked chalets</h3>
            <?php
            // Query recent chalets (assuming 'chalet' is a custom post type)
            $args = array(
                'post_type' => 'chalet',
                'posts_per_page' => 3,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
            );
            $recent_chalets = new WP_Query($args);

            if ($recent_chalets->have_posts()): ?>
                <div class="main-detail-row">
                    <?php while ($recent_chalets->have_posts()):
                        $recent_chalets->the_post(); ?>
                        <div class="main-detail">
                            <div class="img-wrapper">
                                <?php if (has_post_thumbnail()): ?>
                                    <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title_attribute(); ?>" />
                                <?php else: ?>
                                    <img src="<?= get_template_directory_uri() ?>/dashboard/images/chalet.jpeg" alt="" />
                                <?php endif; ?>
                            </div>
                            <div class="detail">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                <?php
                                // Example: get view count from post meta (replace 'view_count' with your actual meta key)
                                $views = get_post_meta(get_the_ID(), 'view_count', true);
                                ?>
                                <span><?php echo $views ? esc_html($views) : '0'; ?> Views</span>
                            </div>
                        </div>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </div>
            <?php else: ?>
                <p>No recent chalets found.</p>
            <?php endif; ?>
        </div>
    </div>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        const today = new Date().toISOString().split('T')[0];

        // Disabled date ranges
        const disabledRanges = [
            { start: '2025-06-10', end: '2025-06-15' },
            { start: '2025-07-01', end: '2025-07-05' }
        ];

        // Past date blocking
        const blockPastDates = {
            start: '1900-01-01',
            end: today,
            display: 'background',
            color: '#cccccc60'
        };

        // Booked events
        const bookedEvents = [
            {
                title: 'Ali Khan',
                start: '2025-06-20',
                end: '2025-06-23',
                color: '#2196f3'
            },
            {
                title: 'Zara Malik',
                start: '2025-06-25',
                end: '2025-06-28',
                color: '#4caf50'
            },
            {
                title: 'Usman Tariq',
                start: '2025-07-10',
                end: '2025-07-13',
                color: '#ff9800'
            },
            {
                title: 'Fatima Shah',
                start: '2025-07-20',
                end: '2025-07-22',
                color: '#9c27b0'
            }
        ];

        // All events merged
        const events = [
            ...disabledRanges.map(range => ({
                start: range.start,
                end: range.end,
                display: 'background',
                color: '#ff000040'
            })),
            blockPastDates,
            ...bookedEvents
        ];

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            editable: false,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            navLinks: true,
            nowIndicator: true,
            events: events,
            dateClick: function (info) {
                const clicked = info.dateStr;

                const isPast = clicked < today;
                const isInDisabledRange = disabledRanges.some(range =>
                    clicked >= range.start && clicked <= range.end
                );

                const booked = bookedEvents.find(event =>
                    clicked >= event.start && clicked < event.end
                );

                if (isPast || isInDisabledRange || booked) {
                    if (booked) {
                        alert(`Already Booked by ${booked.title}`);
                    } else {
                        alert('You cannot book this date.');
                    }
                    return;
                }

                alert(`You clicked: ${clicked}`);
            }
        });

        calendar.render();
    });
</script>
<?php get_footer('dashboard'); ?>