<?php
function chalet_dashboard_middleware() {
    if (is_page_template('page-chalet-dashboard.php')) {
        // Example checks
        if (true) {
            // User not logged in, show login template
            include get_template_directory() . '/inc/buy_chalet_subscription.php';
            exit;
        }

        // Add more checks as needed
        // if (some_other_condition()) {
        //     include get_template_directory() . '/some-other-template.php';
        //     exit;
        // }
    }
}
add_action('template_redirect', 'chalet_dashboard_middleware');