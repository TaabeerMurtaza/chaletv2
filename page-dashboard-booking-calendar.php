<?php

/**
 * Template Name: Dashboard Booking Calender
 *  */
get_header('dashboard');

?>
  <style>
    .sildepanel{
      display: none !important;
    }
  </style>
  <div class="booking-section dashboard-content">

    <button id="toggleSidebarBtn" class="toggle-btn">☰ My Bookings</button>
    <div class="booking-sidebar sidebar-hidden" id="bookingSidebar">
      <button id="closeSidebarBtn" class="close-btn">✕</button>
      <h2>My Bookings</h2>
      <div class="filter">
        <img src="<img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/filter.svg" alt="filter">
        <p>Filter by booking date from newest to oldest</p>
      </div>
      <div class="search-bar">
        <img src="<img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/search.svg" alt="search">
        <input type="search">
      </div>
      <div class="inbox-chat-listing">
        <div class="icl-inner">
          <div class="chat-item">
            <div class="pro-detail">
              <div class="pro-img">
                <img src="<img src="<?= get_template_directory_uri() ?>/dashboard/images/chalet.jpeg" alt="profile image">
              </div>
              <div class="pro-content">
                <span class="status booked">Booked</span>
                <h4>Guest name</h4>
                <span class="in-out-status">Check-in check-out</span>
              </div>
            </div>
            <div class="info-cell">
              <span class="date">Booking date</span>
              <span class="price">1 400$</span>
              <span class="notification ">2</span>
            </div>
          </div>
          <div class="chat-item">
            <div class="pro-detail">
              <div class="pro-img">
              <img src="<img src="<?= get_template_directory_uri() ?>/dashboard/images/chalet.jpeg" alt="profile image">
              </div>
              <div class="pro-content">
                <span class="status booked">Booked</span>
                <h4>Guest name</h4>
                <span class="in-out-status">Check-in check-out</span>
              </div>
            </div>
            <div class="info-cell">
              <span class="date">Booking date</span>
              <span class="price">1 400$</span>
              <span class="notification ">2</span>
            </div>
          </div>
          <div class="chat-item">
            <div class="pro-detail">
              <div class="pro-img">
              <img src="<img src="<?= get_template_directory_uri() ?>/dashboard/images/chalet.jpeg" alt="profile image">
              </div>
              <div class="pro-content">
                <span class="status booked">Booked</span>
                <h4>Guest name</h4>
                <span class="in-out-status">Check-in check-out</span>
              </div>
            </div>
            <div class="info-cell">
              <span class="date">Booking date</span>
              <span class="price">1 400$</span>
              <span class="notification ">2</span>
            </div>
          </div>
          <div class="chat-item">
            <div class="pro-detail">
              <div class="pro-img">
              <img src="<img src="<?= get_template_directory_uri() ?>/dashboard/images/chalet.jpeg" alt="profile image">
              </div>
              <div class="pro-content">
                <span class="status booked">Booked</span>
                <h4>Guest name</h4>
                <span class="in-out-status">Check-in check-out</span>
              </div>
            </div>
            <div class="info-cell">
              <span class="date">Booking date</span>
              <span class="price">1 400$</span>
              <span class="notification ">2</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="booking-content dashboard-section">
     <div class="dashboard-title">
        <button class="menu-btn openPanel"><img src="images/slide-icon.svg" alt=""></button>
        <h2 class="main-title">My Reviews</h2>
        <div class="dashboard-title-details">
          <a href="" class="dashboard-top-btn btn-h">Home page</a>
          <button class="shop-btn">
            <img src="./images/Bell.svg" alt="" />
            <span class="notife">2</span>
          </button>
        </div>
      </div>
      <span class="booking-span"><b>#Booking number</b> - Created <b>Date created</b> </span>
      <div class="booking-top-bottom-details">
        <div class="booking-top-left">
          <button class="book-btn"><span></span> Booked</button>
          <span class="booking-span"><b>Book Name</b> - Number of guest</span>
          <span class="booking-span"><b>Check-in date Check-out date -</b> - Number of guests</span>
        </div>
        <div class="booking-top-right">
          <div class="total-details-row">
            <div class="total">
              <span>Paid</span>
              <span>1530,60$ </span>
            </div>
            <div class="total">
              <span>Paid</span>
              <span>1530,60$ </span>
            </div>
            <div class="total">
              <span>Paid</span>
              <span><b>1530,60$</b> </span>
            </div>
          </div>
        </div>
      </div>
      <hr class="booking-border">  
      <div class="booking-card-row">
        <div class="booking-wrapper">
          <div class="booking-card">
            <div class="booking-card-header">
              <p class="booking-label">Status</p>
              <span class="booking-status-badge">Accepted ✓</span>
            </div>

            <div class="booking-card-details">
              <h2 class="booking-bold">Chalet name</h3>

                <div class="booking-row">
                  <p class="booking-label">
                    Accommodation price/Night (<em>2 nights</em>)
                  </p>
                  <div class="booking-bottom-row">
                    <span>1250,00 $</span>
                    <div class="edit-logo">
                      <img src="./images/icons/edit-pen.svg" alt="">
                    </div>
                  </div>
                </div>
            </div>

            <div class="booking-card-details">
              <h3 class="booking-bold">Extras</h3>
              <div class="booking-row-with-icon">
                <div class="booking-left-side">
                  <span class="booking-plus-circle">+</span>
                  <p class="booking-label">Late Check-out</p>
                </div>
                <div class="booking-bottom-row">
                  <span>1250,00 $</span>
                  <div class="edit-logo">
                    <img src="./images/icons/edit-pen.svg" alt="">
                  </div>
                </div>
              </div>
            </div>

            <div class="booking-card-details">
              <h3 class="booking-bold">Fees</h3>

              <div class="booking-row">
                <p class="booking-label">Accommodation taxes (3,5%)</p>
                <div class="booking-bottom-row">
                  <span>1250,00 $</span>
                  <div class="edit-logo">
                    <img src="./images/icons/edit-pen.svg" alt="">
                  </div>
                </div>
              </div>

              <div class="booking-row">
                <p class="booking-label">Administration fees (3%)</p>
                <div class="booking-bottom-row">
                  <span>1250,00 $</span>
                  <div class="edit-logo">
                    <img src="./images/icons/edit-pen.svg" alt="">
                  </div>
                </div>
              </div>
            </div>

            <div class="booking-card-details">
              <h3 class="booking-bold">Taxes</h3>

              <div class="booking-row">
                <p class="booking-label">GST (5%)</p>
                <div class="booking-bottom-row">
                  <span>1250,00 $</span>
                  <div class="edit-logo">
                    <img src="./images/icons/edit-pen.svg" alt="">
                  </div>
                </div>
              </div>

              <div class="booking-row">
                <p class="booking-label">QST (9.975%)</p>
                <div class="booking-bottom-row">
                  <span>1250,00 $</span>
                  <div class="edit-logo">
                    <img src="./images/icons/edit-pen.svg" alt="">
                  </div>
                </div>
              </div>
            </div>

            <div class="booking-card-divider"></div>

            <div class="booking-row booking-total">
              <p class="booking-label">TOTAL</p>
              <span class="booking-bold booking-span-right">2938,76 $</span>
            </div>
          </div>

        </div>
        <div class="booking-wrapper">
          <div class="info-card-wrapper">
            <div class="card-header-section">
              <h2 class="card-header-title">Guest informations</h2>
              <div class="edit-logo">
                <img src="./images/icons/edit-pen.svg" alt="">
              </div>
            </div>
            <form>
              <div class="detail-entry-row">
                <label class="entry-label-text">Name</label>
                <input type="text" placeholder="Guest full name">
              </div>
              <div class="detail-entry-row">
                <label class="entry-label-text">Email</label>
                <input type="email" placeholder="Guest Email">
              </div>
              <div class="detail-entry-row">
                <label class="entry-label-text">Phone</label>
                <input type="number" placeholder="Guest Phone">
              </div>
              <div class="detail-entry-row">
                <label class="entry-label-text">Language</label>
                <input type="text" placeholder="Guest Language">
              </div>
              <div class="detail-entry-row">
                <label class="entry-label-text">Name</label>
                <input type="text" placeholder="Guest full name">
              </div>
              <div class="detail-entry-row">
                <label class="entry-label-text">Number of bookings with us</label>
                <input type="number" placeholder="2">
              </div>
              <div class="detail-entry-row">
                <label class="entry-label-text">TCP/IP</label>
                <p class="entry-value-text"><b>::ffff:10.103.13.17 / -</b></p>
              </div>
            </form>
          </div>
        </div>
        <div class="booking-wrapper">
          <div class="payment-container">
            <div class="payment-header">
              <h2>Payment collection</h2>
            </div>

            <div class="scheduled-payments-section">
              <div class="section-title">Scheduled payments</div>
              <div class="payment-item">
                <div class="payment-item-label">
                  <span>Paiement date - <span class="green">Paid</span></span>

                </div>
                <span class="payment-value">1469,38 $</span>
              </div>
              <div class="payment-item">
                <div class="payment-item-label">
                  <span>Paiement date - <span class="status-program">Program</span></span>

                </div>
                <span class="payment-value">1469,38 $</span>
              </div>
              <div class="payment-item payment-item-bold">
                <span>Total scheduled payments</span>
                <span class="payment-value-total">2938,76 $</span>
              </div>
            </div>



            <div class="transactions-section">
              <div class="section-title">Transactions</div>
              <div class="payment-item">
                <span>Paiement date - <span class="green">Paid</span></span>
                <span class="payment-value">1469,38 $</span>
              </div>
              <div class="payment-item payment-item-bold">
                <span>Total transactions</span>
                <span class="payment-value-total">2938,76 $</span>
              </div>
            </div>



            <div class="remaining-amount">
              <span>Amount remaining to be paid</span>
              <span>1469,38 $</span>
            </div>
          </div>
        </div>
        <div class="booking-wrapper">
          <textarea class="booking-textarea" placeholder="Notes"></textarea>
        </div>
        <div class="booking-wrapper">
          <div class="security-card">
            <div class="payment-header">
              <h2>Payment collection</h2>
            </div>
            <div class="section-title">Deposit payments</div>
            <p class="security-para">Pre-authorize the guest's credit card for $1,000.00 starting July 2, 2025.Release
              any remaining pre-authorizations on July 13, 2025.</p>
            <h4>Amount of security deposit</h4>
            <p>$1,000.00 pre-authorization scheduled for Jul 2, 2025</p>
          </div>
        </div>
        <div class="booking-wrapper">
          <div class="guest-card">
            <div class="card-header-section">
              <h2 class="card-header-title">Guest informations</h2>
              <div class="edit-logo">
                <img src="./images/icons/edit-pen.svg" alt="">
              </div>
            </div>
            <span>**** **** **** 3730</span>
            <ul>
              <li>Guest Name</li>
              <li>Guest full adresse</li>
              <li>Expiry date: 10/2028 Card type: Visa</li>
            </ul>
          </div>
        </div>

      </div>
    </div>
  </div> 
  <script>
  const toggleBtn = document.getElementById("toggleSidebarBtn");
  const closeBtn = document.getElementById("closeSidebarBtn");
  const sidebar = document.getElementById("bookingSidebar");

  toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("sidebar-hidden");
    sidebar.classList.toggle("sidebar-visible");
  });

  closeBtn.addEventListener("click", () => {
    sidebar.classList.remove("sidebar-visible");
    sidebar.classList.add("sidebar-hidden");
  });

  // Close on outside click
  document.addEventListener("click", (e) => {
    const isInside = sidebar.contains(e.target) || toggleBtn.contains(e.target);
    if (!isInside && sidebar.classList.contains("sidebar-visible")) {
      sidebar.classList.remove("sidebar-visible");
      sidebar.classList.add("sidebar-hidden");
    }
  });
  </script>
  <?php get_footer('dashboard'); ?>