<?php
/**
 * Template Name: Dashboard Subscriptions
 *  */
get_header('dashboard');
?>

<?php
$current_user_id = get_current_user_id();

// Example: Fetch user subscriptions (replace with your actual logic)
$user_subscriptions = get_user_subscriptions();


// Example: Fetch chalets posted by user (replace with your actual logic)
$user_chalets = get_my_chalets();

$total_chalets_posted = count($user_chalets);
$total_slots = total_chalet_slots($current_user_id); // Assuming this function exists to calculate total slots
$total_chalet_slots_available = get_available_chalet_slots($current_user_id); // Assuming this function exists to calculate available chalet slots


?>
<style>
.dashboard-subscriptions-cute {
    width: 100%;
    margin: 2rem;
    padding: 2rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px #eee;
}
.dashboard-subscriptions-cute h2 {
    text-align: center;
    margin-bottom: 1.5rem;
}
.dashboard-subscriptions-cute .dashboard-stats {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
}
.dashboard-subscriptions-cute .dashboard-stat {
    text-align: center;
}
.dashboard-subscriptions-cute .dashboard-stat .stat-value {
    font-size: 2rem;
    font-weight: bold;
}
.dashboard-subscriptions-cute .dashboard-stat .stat-label {
    color: #888;
}
.dashboard-subscriptions-cute .no-subscriptions {
    text-align: center;
    color: #aaa;
}
.dashboard-subscriptions-cute table {
    width: 100%;
    border-collapse: collapse;
    background: #fafbfc;
}
.dashboard-subscriptions-cute thead tr {
    background: #f0f4f8;
}
.dashboard-subscriptions-cute th,
.dashboard-subscriptions-cute td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}
.dashboard-subscriptions-cute th {
    text-align: left;
}
.dashboard-subscriptions-cute th:not(:first-child),
.dashboard-subscriptions-cute td:not(:first-child) {
    text-align: center;
}
</style>
<div class="dashboard-subscriptions-cute">
    <h2>Your Subscriptions</h2>
    <div class="dashboard-stats">
        <div class="dashboard-stat">
            <div class="stat-value"><?php echo $total_chalets_posted; ?></div>
            <div class="stat-label">Total chalets posted</div>
        </div>
        <div class="dashboard-stat">
            <div class="stat-value"><?php echo $total_slots; ?></div>
            <div class="stat-label">Total Slots</div>
        </div>
        <div class="dashboard-stat">
            <div class="stat-value"><?php echo $total_chalet_slots_available; ?></div>
            <div class="stat-label">Total chalet slots available</div>
        </div>
    </div>
    <?php if (empty($user_subscriptions)): ?>
        <div class="no-subscriptions">You have no active subscriptions.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Subscription</th>
                    <th>Chalet Slots</th>
                    <th>Featured Slots</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user_subscriptions as $sub):
                    $sub_name = get_subscription_name($sub);
                    $sub_slots = get_subscription_slots($sub);
                    $feature_slots = get_featured_slots($sub);
                    ?>
                    <tr>
                        <td><?php echo esc_html($sub_name ?? 'N/A'); ?></td>
                        <td><?php echo intval($sub_slots ?? 0); ?></td>
                        <td><?php echo intval($featured_slots ?? 0); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
get_footer('dashboard');