<?php
/* Tem Name: Dashboard Profile */
get_header();

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

$profile_image_id = get_user_meta($user_id, 'profile_image', true);
$profile_image_url = $profile_image_id ? wp_get_attachment_url($profile_image_id) : get_avatar_url($user_id);

$updated = isset($_GET['profile-updated']) && $_GET['profile-updated'] === '1';
$error = isset($_GET['error']) ? sanitize_text_field($_GET['error']) : '';

$meta_fields = [
    'company_name',
    'phone_number',
    'about_me',
    'address',
    'fb_url',
    'insta_url',
    'youtube_url',
    'linkedin_url',
    'tiktok_url',
    'pinterest_url',
];

$meta_values = [];
foreach ($meta_fields as $field) {
    $meta_values[$field] = get_user_meta($user_id, $field, true);
}
?>

<div class="container profile-dashboard-container" style="margin-top: 20px; margin-bottom: 20px;">
    <h1>Profile</h1>
    <?php if ($updated): ?>
        <div class="notice notice-success" style="color: green; margin-bottom: 15px;">Profile updated successfully.</div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="notice notice-error" style="color: red; margin-bottom: 15px;"><?php echo esc_html($error); ?></div>
    <?php endif; ?>

    <?php if (is_user_logged_in()): ?>
        <form method="post" action="<?= admin_url('admin-post.php') ?>" class="profile-update-form" style="max-width: 600px;" enctype="multipart/form-data">
            <?php wp_nonce_field('update_profile_action', 'update_profile_nonce'); ?>
            <input type="hidden" name="action" value="custom_profile_update">

            <div style="margin-bottom: 15px;">
                <img src="<?php echo esc_url($profile_image_url); ?>" alt="Profile Image" style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
            </div>

            <label for="profile_image">Profile Image</label>
            <input type="file" name="profile_image" id="profile_image" accept="image/*" />

            <label for="first_name">First Name</label>
            <input type="text" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" required />

            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" required />

            <label for="email">Email</label>
            <input type="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" required />

            <label for="company_name">Company Name</label>
            <input type="text" name="company_name" value="<?php echo esc_attr($meta_values['company_name']); ?>" />

            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" value="<?php echo esc_attr($meta_values['phone_number']); ?>" />

            <label for="address">Address</label>
            <input type="text" name="address" value="<?php echo esc_attr($meta_values['address']); ?>" />

            <label for="about_me">About Me</label>
            <textarea name="about_me"><?php echo esc_textarea($meta_values['about_me']); ?></textarea>

            <label for="fb_url">Facebook URL</label>
            <input type="url" name="fb_url" value="<?php echo esc_url($meta_values['fb_url']); ?>" />

            <label for="insta_url">Instagram URL</label>
            <input type="url" name="insta_url" value="<?php echo esc_url($meta_values['insta_url']); ?>" />

            <label for="youtube_url">YouTube URL</label>
            <input type="url" name="youtube_url" value="<?php echo esc_url($meta_values['youtube_url']); ?>" />

            <label for="linkedin_url">LinkedIn URL</label>
            <input type="url" name="linkedin_url" value="<?php echo esc_url($meta_values['linkedin_url']); ?>" />

            <label for="tiktok_url">TikTok URL</label>
            <input type="url" name="tiktok_url" value="<?php echo esc_url($meta_values['tiktok_url']); ?>" />

            <label for="pinterest_url">Pinterest URL</label>
            <input type="url" name="pinterest_url" value="<?php echo esc_url($meta_values['pinterest_url']); ?>" />

            <button type="submit" style="margin-top: 15px;">Update Profile</button>
        </form>
    <?php else: ?>
        <p>You must be logged in to view this page.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
