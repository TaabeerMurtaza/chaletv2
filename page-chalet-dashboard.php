<?php
/* Template Name: Chalet Dashboard */

get_header('dashboard');
$query = new WP_Query([
    'post_type' => 'chalet',
    'posts_per_page' => -1,
]);
?>

<div class="dashboard-content">
    <div class="dashboard-title">
        <button class="menu-btn openPanel"><img
                src="<?= get_template_directory_uri() ?>/dashboard/images/slide-icon.svg" alt=""></button>
        <h2 class="main-title"> --adons </h2>
        <div class="dashboard-title-details">
            <a href="" class="dashboard-top-btn btn-h">Home page</a>
            <button class="shop-btn">
                <img src="<?= get_template_directory_uri() ?>/dashboard/images/Bell.svg" alt="" />
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
    <div class="divider"></div>
    <div class="listing-wrapper">
        <div class="listing-head">
            <span>Name</span>
            <span>Reviews </span>
            <span>Status</span>
            <span>Actions</span>
        </div>

        <?php
        if ($query->have_posts()):
            while ($query->have_posts()):
                $query->the_post(); 

                $tmp = carbon_get_post_meta(get_the_ID(), 'region');
                if(@$tmp[0]){
                    $region_id = $tmp[0]['id'];
                    $region = get_the_title($region_id);
                }else{
                    $region = '';
                }
                ?>

                <div class="listing-body">
                    <div class="name">
                        <div class="name-img">
                            <img src="<?= get_the_post_thumbnail_url() ?>" alt="">
                        </div>
                        <div class="name-details">
                            <h4><?= the_title(); ?></h4>
                            <p><span>City:</span> Sainte-Adèle</p>
                            <p><span>Region:</span> <?= $region ?></p>
                        </div>
                    </div>
                    <div class="review">
                        <span>2 Reviews</span>
                    </div>
                    <div class="status">
                        <ul>
                            <li><span><img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/black-home.svg"
                                        alt="">Management</span></li>
                            <li><span><img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/dot.svg"
                                        alt="">Published</span></li>
                            <li><span>Expires on 2026-02-01</span></li>
                        </ul>
                    </div>
                    <div class="edit">
                        <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/edit-pen.svg" alt="">
                    </div>
                </div>
            <?php endwhile; endif; ?>
    </div>
</div>


<?php get_footer('dashboard') ?>