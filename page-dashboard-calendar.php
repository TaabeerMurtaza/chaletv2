<?php

/**
 * Template Name: Dashboard Calender
 *  */
get_header('dashboard');

$bookings = get_my_bookings();


?>
<div class="dashboard-content">
  <div class="dashboard-title">
    <button class="menu-btn openPanel"><img src="<?= get_template_directory_uri() ?>/dashboard/images/slide-icon.svg"
        alt=""></button>
    <h2 class="main-title">
      Booking Calendar
    </h2>
    <div class="dashboard-title-details">
      <a href="" class="dashboard-top-btn btn-h">Home page</a>
      <button class="shop-btn">
        <img src="<?= get_template_directory_uri() ?>/dashboard/images/Bell.svg" alt="" />
        <span class="notife">2</span>
      </button>
    </div>
  </div>
  <div class="divider"></div>
  <div class="dashboard-main-details">
    <h3 class="dashboard-sub-title">Bookings - next 30 days</h3>
    <div class="calender-details">
      <div id="calendar"></div>
      <div class="booking-btns-row">
        <div class="booking-btn-detail">
          <a href="javascript:void()" id="openModal">
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
  <div class="configuration-popup" id="configModal">
    <div class="close-overlay"></div>
    <div class="popup-tabs">
      <div class="dashboard-tab-links">
        <ul>
          <li>
            <button data-tab="edite-popup" class="tab-link">
              <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/c2.svg" alt="tab-icon">
              Create a reservation
            </button>
          </li>
          <li>
            <button data-tab="unavalible-contenty" class="tab-link">
              <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/c1.svg" alt="tab-icon">
              Create an unavailable period
            </button>
          </li>
          <li>
            <button data-tab="price-content" class="tab-link">
              <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/c3.svg" alt="tab-icon">
              Change the price per night
            </button>
          </li>
          <li>
        </ul>
      </div>
    </div>
    <div class="popup-content" id="edite-popup">
      <div class="popup-form">
        <h3>BOOKING</h3>
        <select>
          <option>Chalets</option>
          <option>Saab</option>
          <option>Mercedes</option>
          <option>Audi</option>
        </select>
        <input type="date">
        <div class="popup-divider"></div>
        <h3>Guests</h3>
        <div class="popup-detail-row">
          <div class="popup-detail">
            <label>Adults</label>
            <select>
              <option>4</option>
              <option>Saab</option>
              <option>Mercedes</option>
              <option>Audi</option>
            </select>
          </div>
          <div class="popup-detail">
            <label>Adults</label>
            <select>
              <option>4</option>
              <option>Saab</option>
              <option>Mercedes</option>
              <option>Audi</option>
            </select>
          </div>
          <div class="popup-detail">
            <label>Adults</label>
            <select>
              <option>4</option>
              <option>Saab</option>
              <option>Mercedes</option>
              <option>Audi</option>
            </select>
          </div>
        </div>
        <h3>Guests informations</h3>
        <div class="information-row">
          <div class="information-details">
            <label> First name</label>
            <input type="text" placeholder=" John">
          </div>
          <div class="information-details">
            <label> Last name</label>
            <input type="text" placeholder="Dow">
          </div>
          <div class="information-details">
            <label> Phone</label>
            <input type="number" placeholder=" 819-222-3333">
          </div>
          <div class="information-details">
            <label> Email</label>
            <input type="email" placeholder="john@test.com">
          </div>
          <div class="information-details">
            <label> Language</label>
            <select>
              <option>French</option>
              <option>Saab</option>
              <option>Mercedes</option>
              <option>Audi</option>
            </select>
          </div>
        </div>
        <div class="divider"></div>
        <div class="pricing-row">
          <span>TOTAL</span>
          <span> 2938,76 $</span>
        </div>
        <div class="popup-divider"></div>
        <div class="form-btn-row">
          <button class="grey">Cancel</button>
          <button>Next</button>
        </div>
      </div>
    </div>
    <div class="registration-content popup-content" id="registration-content">
      <div class="popup-form">
        <div class="popup-top-details">
          <h3> Chalet Name</h3>
          <div class="popup-name-details">
            <span>Accommodation price/Night (2 nights)</span>
            <span> 1150,00 $</span>
            <span><img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/edit-pen.svg" alt=""></span>
          </div>
          <div class="popup-name-details">
            <span>Accommodation price/Night (2 nights)</span>
            <span> 1150,00 $</span>
            <span><img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/edit-pen.svg" alt=""></span>
          </div>
        </div>
        <div class="sm-divider"></div>
        <div class="popup-top-details">
          <h3> Chalet Name</h3>
          <div class="popup-name-details">
            <span>Accommodation price/Night (2 nights)</span>
            <span> 1150,00 $</span>
            <span><img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/edit-pen.svg" alt=""></span>
          </div>
          <div class="popup-name-details">
            <span>Accommodation price/Night (2 nights)</span>
            <span> 1150,00 $</span>
            <span><img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/edit-pen.svg" alt=""></span>
          </div>
        </div>
        <div class="sm-divider"></div>
        <div class="popup-top-details">
          <h3> Chalet Name</h3>
          <div class="popup-name-details">
            <span>Accommodation price/Night (2 nights)</span>
            <span> 1150,00 $</span>
            <span><img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/edit-pen.svg" alt=""></span>
          </div>
          <div class="popup-name-details">
            <span>Accommodation price/Night (2 nights)</span>
            <span> 1150,00 $</span>
            <span><img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/edit-pen.svg" alt=""></span>
          </div>
        </div>
        <div class="divider"></div>
        <div class="pricing-row">
          <span>TOTAL</span>
          <span> 2938,76 $</span>
        </div>
        <div class="popup-divider"></div>
        <div class="form-btn-row">
          <button class="grey">Cancel</button>
          <button>Next</button>
        </div>
      </div>
    </div>
    <div class="unavalible-contenty popup-content" id="unavalible-contenty">
      <div class="popup-form">
        <h3>BOOKING</h3>
        <select>
          <option>Chalets</option>
          <option>Saab</option>
          <option>Mercedes</option>
          <option>Audi</option>
        </select>
        <input type="date">
        <textarea placeholder="Details"></textarea>
        <div class="popup-divider"></div>

        <div class="popup-divider"></div>
        <div class="form-btn-row">
          <button class="grey">Cancel</button>
          <button>Next</button>
        </div>
      </div>
    </div>
    <div class="price-content popup-content" id="price-content">
      <div class="popup-form">
        <h3>Price per night</h3>
        <div class="price-detail-row">
          <div class="price-detail " id="first">
            <label>Date period</label>
            <input type="date">
          </div>
          <div class="price-detail" id="second">
            <label> New price per night</label>
            <input type="number">
          </div>
          <div class="price-detail" id="third">
            <label>Minimum stay</label>
            <input type="number">
          </div>
        </div>
        <div class="divider"></div>
        <div class="form-btn-row">
          <button class="grey">Cancel</button>
          <button>Next</button>
        </div>
      </div>
    </div>
  </div>
  <script>
    <?php $chalets = get_my_chalets(); ?>
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
                $my_chalet = false;
                foreach($chalets as $c){
                  if($c->ID == $chalet_id) $my_chalet = true;
                }
                if(!$my_chalet) continue;
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
  <!-- <script>
    const openBtn = document.getElementById('openModal');
    const modal = document.getElementById('configModal');
    const overlay = modal.querySelector('.close-overlay');

    openBtn.addEventListener('click', () => {
      modal.style.display = 'flex';
    });

    overlay.addEventListener('click', () => {
      modal.style.display = 'none';
    });


    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.popup-content');

    let currentTab = null;

    tabLinks.forEach(link => {
      link.addEventListener('click', () => {
        const targetId = link.getAttribute('data-tab');
        const targetContent = document.getElementById(targetId);

        if (!targetContent || currentTab === targetContent) return;

        // Hide all tabs
        tabContents.forEach(tab => tab.classList.remove('active'));

        // Show selected tab
        targetContent.classList.add('active');

        // Set current tab
        currentTab = targetContent;
      });
    });

    // Set default active tab
    document.querySelector('[data-tab="edite-popup"]').click();

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
      // Dynamically generate bookedEvents from PHP bookings
      const bookedEvents = [
        <?php
        // Helper: generate a random color with good contrast for white text

        foreach ($bookings as $booking) {
          $checkin = carbon_get_post_meta($booking->ID, 'booking_checkin');
          $checkout = carbon_get_post_meta($booking->ID, 'booking_checkout');
          $title = esc_js(get_the_title($booking->ID));
          $color = get_random_color();

          // Only output if both dates exist
          if ($checkin && $checkout) {
            echo "{";
            echo "title: '{$title}',";
            echo "start: '{$checkin}',";
            echo "end: '{$checkout}',";
            echo "color: '{$color}'";
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
        // dateClick: function (info) {
        //   const clicked = info.dateStr;

        //   const isPast = clicked < today;
        //   const isInDisabledRange = disabledRanges.some(range =>
        //     clicked >= range.start && clicked <= range.end
        //   );

        //   const booked = bookedEvents.find(event =>
        //     clicked >= event.start && clicked < event.end
        //   );

        //   if (isPast || isInDisabledRange || booked) {
        //     if (booked) {
        //       alert(`Already Booked by ${booked.title}`);
        //     } else {
        //       alert('You cannot book this date.');
        //     }
        //     return;
        //   }

        //   alert(`You clicked: ${clicked}`);
        // }
      });

      calendar.render();
    });
  </script> -->
  <?php get_footer('dashboard'); ?>