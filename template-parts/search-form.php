<style>
    .select2-container--default .select2-selection--multiple {
        font-size: 15px;
        font-weight: 400;
        line-height: 28px;
        width: 100%;
        min-height:50px;
        background-color: var(--white);
        border-radius: 50px !important;
        border: 1px solid var(--b-light) !important;
        appearance: none;
        cursor: pointer;
        outline: none;
        background: url(../images/icons/presentation.png) no-repeat 85% center;
        transition: 0.2s all ease-in-out;
    }
</style>
<div class="form-wraper">
    <form action="<?php echo get_home_url() .'/search-chalets'; ?>" method="POST">
        <div class="form-g">
            <select name="region" id="region-select">
                <option value="">Select Region</option>
                <?php
                $query = new WP_Query([
                    'post_type' => 'region',
                    'posts_per_page' => -1,
                    'orderby' => 'name',
                ]);
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        ?>
                        <option value="<?php echo get_the_ID(); ?>">
                            <?php echo get_the_title(); ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="form-g">
            <input type="date" name="date" id="date-select" class="form-control filter_opt" placeholder="Select Date"
                min="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-g">
            <input type="number" name="guests" class="filter_opt" min="1" max="100" placeholder="Guests">
        </div>
        <div class="form-g option">
            <select name="chalet_features[]" id="chalet_features" multiple>
                <?php
                $args = [
                    'post_type' => 'chalet_feature',
                    'posts_per_page' => -1,
                ];
                $query = new WP_Query($args);
                if ($query->have_posts()) {
                    while ($query->have_posts()):
                        $query->the_post();
                        echo '<option value="' . get_the_ID() . '">' . get_the_title() . '</option>';
                    endwhile;
                }
                ?>
            </select>
        </div>
        <div class="form-g">
            <input type="submit" class="btn" />
        </div>
    </form>
</div>
<script>
    $(document).ready(function () {
        $('#chalet_features').select2({
            placeholder: "Options"
        });
    });

</script>