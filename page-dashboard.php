<?php

/**
 * Template Name: Dashboard
 *  */
get_header('dashboard');
$bookings = get_my_bookings();
?>
<style>
    .fc-resourceTimelineDay-button.fc-button.fc-button-primary {
  margin-right: 5px;
}
</style>
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
        <?php
        // Query latest 5 bookings (assuming 'booking' is a custom post type)
        $args = array(
            'post_type' => 'booking',
            'posts_per_page' => 5,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        );
        $latest_bookings = new WP_Query($args);

        if ($latest_bookings->have_posts()):
            while ($latest_bookings->have_posts()):
                $latest_bookings->the_post();
                // Get related chalet (assuming a post meta 'chalet_id' stores the chalet post ID)
                $chalet = @carbon_get_post_meta(get_the_ID(), 'booking_chalet')[0] ?? null;
                $chalet_id = is_array($chalet) && isset($chalet['id']) ? $chalet['id'] : null;
                $chalet_title = $chalet_id ? get_the_title($chalet_id) : 'Unknown Chalet';
                $chalet_link = $chalet_id ? get_permalink($chalet_id) : '#';

                // Get booking dates (assuming 'start_date' and 'end_date' meta fields, format: Y-m-d)
                $start_date = carbon_get_post_meta(get_the_ID(), 'booking_checkin');
                $end_date = carbon_get_post_meta(get_the_ID(), 'booking_checkout');

                // Format dates
                $start_fmt = $start_date ? date('F d Y', strtotime($start_date)) : '';
                $end_fmt = $end_date ? date('F d Y', strtotime($end_date)) : '';
                ?>
                <span>
                    <a href="<?php echo esc_url($chalet_link); ?>"><?php echo esc_html($chalet_title); ?></a>
                    <?php if ($start_fmt && $end_fmt): ?>
                        from <?php echo esc_html($start_fmt); ?> to <?php echo esc_html($end_fmt); ?>
                    <?php endif; ?>
                </span>
                <?php
            endwhile;
            wp_reset_postdata();
        else:
            ?>
            <span>No bookings found.</span>
        <?php endif; ?>
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
            $recent_chalets = get_my_chalets();

            if ( count($recent_chalets) ): ?>
                <div class="main-detail-row">
                    <?php foreach ($recent_chalets as $chalet):
                        // make sure $chalet is a post object; if it's just an ID, fetch it
                        $post = is_object($chalet) ? $chalet : get_post($chalet);
                        setup_postdata($post);
                    ?>
                        <div class="main-detail">
                            <div class="img-wrapper">
                                <?php if (has_post_thumbnail($post)): ?>
                                    <img src="<?php echo get_the_post_thumbnail_url($post, 'medium'); ?>" alt="<?php echo esc_attr(get_the_title($post)); ?>" />
                                <?php else: ?>
                                    <img src="<?= get_template_directory_uri() ?>/dashboard/images/chalet.jpeg" alt="" />
                                <?php endif; ?>
                            </div>
                            <div class="detail">
                                <a href="<?php echo get_permalink($post); ?>"><?php echo get_the_title($post); ?></a>
                                <?php
                                $views = get_post_meta($post->ID, 'view_count', true);
                                ?>
                                <span><?php echo $views ? esc_html($views) : '0'; ?> Views</span>
                            </div>
                        </div>
                    <?php endforeach;
                    wp_reset_postdata(); ?>
                </div>
            <?php else: ?>
                <p>No recent chalets found.</p>
            <?php endif; ?>

        </div>
        <div class="main-booking">
            <h3 class="dashboard-sub-title">Your most booked chalets</h3>
            <?php
            // Get all bookings with 'booking_chalet' meta set
            $booking_args = array(
                'post_type' => 'booking',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'booking_chalet',
                        'compare' => 'EXISTS',
                    ),
                ),
                'fields' => 'ids',
            );
            if(!current_user_can('manage_options')) {
                $booking_args['author'] = get_current_user_id(); // Only show bookings for the current user
            }
            $booking_query = new WP_Query($booking_args);


            $chalet_counts = array();

            if ($booking_query->have_posts()) {
                foreach ($booking_query->posts as $booking_id) {
                    $chalet_meta = @carbon_get_post_meta($booking_id, 'booking_chalet');
                    if (is_array($chalet_meta) && isset($chalet_meta[0]['id'])) {
                        $chalet_id = $chalet_meta[0]['id'];
                        if ($chalet_id) {
                            if (!isset($chalet_counts[$chalet_id])) {
                                $chalet_counts[$chalet_id] = 0;
                            }
                            $chalet_counts[$chalet_id]++;
                        }
                    }
                }
            }

            // Sort chalets by booking count, descending
            arsort($chalet_counts);
            $top_chalets = array_slice(array_keys($chalet_counts), 0, 5, true);

            if (!empty($top_chalets)): ?>
                <div class="main-detail-row">
                    <?php foreach ($top_chalets as $chalet_id): ?>
                        <div class="main-detail">
                            <div class="img-wrapper">
                                <?php if (has_post_thumbnail($chalet_id)): ?>
                                    <img src="<?php echo get_the_post_thumbnail_url($chalet_id, 'medium'); ?>"
                                        alt="<?php echo esc_attr(get_the_title($chalet_id)); ?>" />
                                <?php else: ?>
                                    <img src="<?= get_template_directory_uri() ?>/dashboard/images/chalet.jpeg" alt="" />
                                <?php endif; ?>
                            </div>
                            <div class="detail">
                                <a href="<?php echo get_permalink($chalet_id); ?>"><?php echo get_the_title($chalet_id); ?></a>
                                <span><?php echo intval($chalet_counts[$chalet_id]); ?> Bookings</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No booked chalets found.</p>
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

        // Booked events (from PHP)
        const bookedEvents = [
            <?php
             foreach ($bookings as $booking) {
                $checkin = carbon_get_post_meta($booking->ID, 'booking_checkin');
                $checkout = carbon_get_post_meta($booking->ID, 'booking_checkout');
                $chalet_id = @carbon_get_post_meta($booking->ID, 'booking_chalet')[0]['id'] ?? null;
                $title = esc_js(get_the_title($booking->ID));
                $color = get_random_color();

                if ($checkin && $checkout && $chalet_id) {
                    echo "{";
                    echo "title: '{$title}',";
                    echo "start: '{$checkin}',";
                    echo "end: '{$checkout}',";
                    echo "color: '{$color}',";
                    echo "resourceId: '{$chalet_id}'";
                    echo "},";
                }
            }
            ?>
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

        // ==== PHP: Generate RESOURCES (chalets) ====
        const resources = [
            <?php
            $chalets = get_my_chalets();
            foreach ($chalets as $chalet) {
                $title = esc_js(get_the_title($chalet->ID));
                echo "{ id: '{$chalet->ID}', title: '{$title}' },";
            }
            ?>
        ];

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'resourceTimelineMonth',
            selectable: true,
            editable: false,
            height: 600,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'resourceTimelineMonth,resourceTimelineWeek,resourceTimelineDay,dayGridMonth,timeGridWeek,listWeek'
            },

            views: {
                resourceTimelineDay: {
                    buttonText: 'Timeline Day'
                },
                resourceTimelineWeek: {
                    buttonText: 'Timeline Week'
                },
                resourceTimelineMonth: {
                    buttonText: 'Timeline Month'
                },
                dayGridMonth: {
                    buttonText: 'Month'
                },
                timeGridWeek: {
                    buttonText: 'Week'
                },
                listWeek: {
                    buttonText: 'List'
                }
            },

            navLinks: true,
            nowIndicator: true,
            events: events,
            resources: resources,

            // dateClick: function (info) {
            //     const clicked = info.dateStr;

            //     const isPast = clicked < today;
            //     const isInDisabledRange = disabledRanges.some(range =>
            //         clicked >= range.start && clicked <= range.end
            //     );

            //     const booked = bookedEvents.find(event =>
            //         clicked >= event.start && clicked < event.end
            //     );

            //     if (isPast || isInDisabledRange || booked) {
            //         if (booked) {
            //             alert(`Already Booked by ${booked.title}`);
            //         } else {
            //             alert('You cannot book this date.');
            //         }
            //         return;
            //     }

            //     alert(`You clicked: ${clicked}`);
            // },
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives'
        });

        calendar.render();
    });
</script>

<?php get_footer('dashboard'); ?>