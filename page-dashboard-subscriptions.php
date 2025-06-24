<?php get_header('dashboard');
if(isset($_GET['cancel_subscription']) && isset($_GET['subscription_id'])) {
    $subscription_id = intval($_GET['subscription_id']);
    if (cancel_subscription($subscription_id)) {
        echo '<script>notifier.success("Subscription cancelled successfully.");</script>';
    } else {
        echo '<script>notifier.alert("Failed to cancel subscription. Please try again.");</script>';
    }
    // remove the GET params from url without reloading the page
    echo '<script>history.pushState({}, "", window.location.pathname);</script>';

}
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/dashboard/css/subscriptions.css">
<div class="dashboard-content">
    <div class="dashboard-title">
        <button class="menu-btn openPanel"><img src="images/slide-icon.svg" alt=""></button>
        <h2 class="main-title"> Active Subscriptions </h2>
        <div class="dashboard-title-details">
            <a href="" class="dashboard-top-btn btn-h">Home page</a>
            <button class="shop-btn">
                <img src="<?= get_template_directory_uri() ?>/assets/images/icons/bell.svg" alt="" />
                <span class="notife">2</span>
            </button>
        </div>
    </div>

    <div class="divider"></div>
    <div class="subs-card-row">
        <?php
        $user_subscriptions = get_user_subscriptions(get_current_user_id());
        if (!empty($user_subscriptions)):
            foreach ($user_subscriptions as $sub_id):
                $details = get_subscription_details($sub_id);
                $cpt = get_subscription_cpt($sub_id);
                ?>
                <div class="subs-card">
                    <div class="subs"
                        style="background-color: <?= esc_attr(carbon_get_post_meta($cpt->ID, 'subscription_color')); ?>;">
                        <div class="icon-container">
                            <img src="<?= carbon_get_post_meta($cpt->ID, 'subscription_icon') ?: get_template_directory_uri() . '/assets/images/icons/check.svg' ?>"
                                alt="House Icon" class="icon" />
                        </div>
                        <div class="subs-content">
                            <h2 class="subs-heading"><?= get_the_title($cpt->ID) ?></h2>
                            <?= wp_kses_post(carbon_get_post_meta($cpt->ID, 'subscription_description')) ?>
                        </div>
                        <button class="subs-btn" onclick="cancel_subscription(<?= $sub_id ?>)">Cancel Subscription</button>
                    </div>
                </div>

            <?php endforeach; endif; ?>

    </div>
    <div class="divider"></div>

</div>
<script>
function cancel_subscription(subscriptionId) {
    if (confirm("Are you sure you want to cancel this subscription?")) {
        window.location.href = `?cancel_subscription&subscription_id=${subscriptionId}`;
    }
}
</script>
<?php get_footer('dashboard') ?>