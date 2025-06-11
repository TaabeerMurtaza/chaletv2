<?php get_header('dashboard'); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/dashboard/css/subscriptions.css">
<div class="dashboard-content">
    <div class="dashboard-title">
        <button class="menu-btn openPanel"><img src="images/slide-icon.svg" alt=""></button>
        <h2 class="main-title"> --adons </h2>
        <div class="dashboard-title-details">
            <a href="" class="dashboard-top-btn btn-h">Home page</a>
            <button class="shop-btn">
                <img src="<?= get_template_directory_uri() ?>/assets/images/icons/bell.svg" alt="" />
                <span class="notife">2</span>
            </button>
        </div>
    </div>
    <div class="divider"></div>
    <div class="filter-row">
        <div class="filter-details">
            <h3>Chalets</h3>
            <div class="filter-bottom-details">
                <input type="text" placeholder="Search by chalet name. ">
                <button class="filter-btn">Search</button>
            </div>
        </div>
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

    </div>
    <!-- <div class="divider"></div> -->
    <!-- <div class="listing-wrapper">
        <div class="listing-head">
            <span>Name</span>
            <span>Reviews </span>
            <span>Status</span>
            <span>Actions</span>
        </div>
        <div class="listing-body">
            <div class="name">
                <div class="name-img">
                    <img src="./images/bedroom.jpg" alt="">
                </div>
                <div class="name-details">
                    <h4>Name</h4>
                    <p><span>City:</span> Sainte-Adèle</p>
                    <p><span>Region:</span> Laurentides</p>
                </div>
            </div>
            <div class="review">
                <span>2 Reviews</span>
            </div>
            <div class="status">
                <ul>
                    <li><span><img src="./images/icons/black-home.svg" alt="">Management</span></li>
                    <li><span><img src="./images/icons/dot.svg" alt="">Published</span></li>
                    <li><span>Expires on 2026-02-01</span></li>
                </ul>
            </div>
            <div class="edit">
                <img src="./images/icons/edit-pen.svg" alt="">
            </div>
        </div>
    </div> -->
    <div class="divider"></div>
    <h3 class="light-heading">Add a chalet</h3>
    <span>Want to add a new chalet to our site?</span>
    <p>Select one of our 3 package types below.</p>
    <div class="divider"></div>
    <div class="subs-card-row">
        <div class="subs-card">
            <div class="subs">
                <div class="icon-container">
                    <img src="./images/tick.png" alt="House Icon" class="icon" />
                </div>
                <div class="subs-content">
                    <h2 class="subs-heading">RENTAL MANAGEMENT</h2>
                    <ul>
                        <li>Complete management of reservations</li>
                        <li>Professional photoshoot of your chalet</li>
                        <li>Page dedicated to your chalet</li>
                    </ul>
                </div>
                <button class="subs-btn">SEE OUR PACKAGES</button>
            </div>
        </div>
        <div class="subs-card">
            <div class="subs light-clr">
                <div class="icon-container">
                    <img src="./images/key.png" alt="House Icon" class="icon" />
                </div>
                <div class="subs-content">
                    <h2 class="subs-heading">RENTAL MANAGEMENT</h2>
                    <ul>
                        <li>Complete management of reservations</li>
                        <li>Professional photoshoot of your chalet</li>
                        <li>Page dedicated to your chalet</li>
                    </ul>
                </div>
                <button class="subs-btn">SEE OUR PACKAGES</button>
            </div>
        </div>
        <div class="subs-card ">
            <div class="subs orange-clr">
                <div class="icon-container">
                    <img src="./images/home-orng.png" alt="House Icon" class="icon" />
                </div>
                <div class="subs-content">
                    <h2 class="subs-heading">RENTAL MANAGEMENT</h2>
                    <ul>
                        <li>Complete management of reservations</li>
                        <li>Professional photoshoot of your chalet</li>
                        <li>Page dedicated to your chalet</li>
                    </ul>
                </div>
                <button class="subs-btn">SEE OUR PACKAGES</button>
            </div>
        </div>
    </div>
    <div class="divider"></div>
    <div class="contact-box">
        <div class="contact-box-inner">
            <h3>Need help choosing the perfect package for your chalet(s)?</h3>
            <a href="#">Contact us to discuss!</a>
            <div class="contact-details">
                <a href="#"><img src="email-icon.png" alt="Email Icon">info@booktonchalet.com</a>
                <a href="#" class="phone-link"><img src="phone-icon.png" alt="Phone Icon">(581) 814-2225</a>
            </div>
        </div>
    </div>
</div>

<?php get_footer('dashboard') ?>