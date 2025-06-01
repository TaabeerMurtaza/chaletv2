<?php
/* Template Name: Chalet Dashboard */

get_header('dashboard');

?>
<style>
    .add_chalet_wrapper{
        display:flex;
        align-items:flex-end;
        width:100%;
    }
    .add_chalet_wrapper .add-chalet{
        background-color: #004944;
        color: white;
        padding: 15px 48px;
        font-size: 16px;
        font-family: Arial, sans-serif;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }
</style>
<div class="dashboard-content">
    <div class="dashboard-title">
        <button class="menu-btn openPanel"><img
                src="<?= get_template_directory_uri() ?>/dashboard/images/slide-icon.svg" alt=""></button>
        <h2 class="main-title"> Chalets </h2>
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
                <input type="text" name="name" placeholder="Search by chalet name. ">
                <button class="filter-btn">Search</button>
            </div>
        </div>
        <!-- <div class="filter-details">
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
        </div> -->
        <div class="filter-details">
            <h3>Status</h3>
            <div class="filter-bottom-details">
                <select class="filter-select" id="chalet-status">
                    <option value="all">All</option>
                    <option value="publish">Published </option>
                    <option value="pending">Pending</option>
                    <!-- <option value="inactive">Inactive</option> -->
                </select>
                <button class="filter-btn">Filter</button>
            </div>
        </div>

    </div>
    <div class="divider"></div>
    <div class="add_chalet_wrapper">
        <a href="<?= get_home_url() ?>/dashboard-edit-chalet" class="add-chalet" style="margin-left:auto">Add Chalet</a>
    </div>
    <div class="listing-wrapper">
        <div class="listing-head">
            <span>Name</span>
            <span>Reviews </span>
            <span>Status</span>
            <span>Actions</span>
        </div>
        <div id="chalets">

            <?php
            $query = get_my_chalets(true);
            if ($query->have_posts()):
                while ($query->have_posts()):
                    $query->the_post();

                    $tmp = carbon_get_post_meta(get_the_ID(), 'region');
                    if (@$tmp[0]) {
                        $region_id = $tmp[0]['id'];
                        $region = get_the_title($region_id);
                    } else {
                        $region = '';
                    }
                    ?>

                    <div class="listing-body">
                        <div class="name">
                            <div class="name-img">
                                <img src="<?= get_the_post_thumbnail_url() ?>" alt="">
                            </div>
                            <div class="name-details">
                                <h4><a href="<?= get_permalink(); ?>"><?= get_the_title(); ?></a></h4>
                                <p><span>City:</span> Sainte-Adèle</p>
                                <p><span>Region:</span> <?= $region ?></p>
                            </div>
                        </div>
                        <div class="review">
                            <?php
                            $reviews_count = get_comments_number(get_the_ID());
                            ?>
                            <span><?= $reviews_count ?> Review<?= $reviews_count == 1 ? '' : 's' ?></span>
                        </div>
                        <div class="status">
                            <ul>
                                <!-- <li><span><img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/black-home.svg"
                                            alt="">Management</span></li> -->
                                <?php
                                $post_status = get_post_status(get_the_ID());
                                $status_label = '';
                                $status_icon = '';

                                if ($post_status === 'publish') {
                                    $status_label = 'Published';
                                    $status_icon = 'dot.svg';
                                } elseif ($post_status === 'pending') {
                                    $status_label = 'Pending';
                                    // $status_icon = 'pending-dot.svg';
                                    $status_icon = 'dot.svg';
                                } else {
                                    $status_label = ucfirst($post_status);
                                    $status_icon = 'dot.svg';
                                }
                                ?>
                                <li>
                                    <span>
                                        <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/<?= $status_icon ?>" alt="">
                                        <?= $status_label ?>
                                    </span>
                                </li>
                                <li><span>Expires on 2026-02-01</span></li>
                            </ul>
                        </div>
                        <a href="<?= get_home_url() ?>/dashboard-edit-chalet?id=<?= get_the_ID() ?>" class="edit">
                            <img src="<?= get_template_directory_uri() ?>/dashboard/images/icons/edit-pen.svg" alt="">
                        </a>
                    </div>
                <?php endwhile; endif; ?>
        </div>

    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
    function fetchChalets() {
        const name = $('input[name="name"]').val();
        const status = $('#chalet-status').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'filter_chalets',
                name: name,
                status: status,
            },
            beforeSend: function () {
                $('#chalets').html('<p>Loading...</p>');
            },
            success: function (res) {
                $('#chalets').html(res);
            }
        });
    }

    $('.filter-btn').on('click', function (e) {
        e.preventDefault();
        fetchChalets();
    });
});

</script>

<?php get_footer('dashboard') ?>