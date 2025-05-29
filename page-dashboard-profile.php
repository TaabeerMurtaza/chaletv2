<?php 
/* Template Name: Profile Dashboard */

get_header('dashboard');
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


<div class="dashboard-content">
    <div class="dashboard-title">
        <button class="menu-btn openPanel"><img src="<?= get_template_directory_uri() ?>/dashboard/images/slide-icon.svg" alt=""></button>
        <h2 class="main-title">Profile</h2>
        <div class="dashboard-title-details">
            <a href="" class="dashboard-top-btn btn-h">Home page</a>
            <button class="shop-btn">
                <img src="<?= get_template_directory_uri() ?>/dashboard/images/Bell.svg" alt="" />
                <span class="notife">2</span>
            </button>
        </div>
    </div>
    <div class="profile-row">
        <div class="profile-top-wrapper">
            <div class="profile-top-details">
                <h3 class="dashboard-sub-title">Details</h3>
                <div class="profile-divider"></div>
                <form method="post" action="<?= admin_url('admin-post.php') ?>" class="profile-update-form"
                    enctype="multipart/form-data">
                    <?php wp_nonce_field('update_profile_action', 'update_profile_nonce'); ?>
                    <input type="hidden" name="action" value="custom_profile_update">
                    <input type="file" name="profile_image" id="profile_image" accept="image/*" style="display: none;" />

                    <div class="col-50">
                        <div class="profile-input-details">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name"
                                value="<?php echo esc_attr($current_user->first_name); ?>" required />
                        </div>
                        <div class="profile-input-details">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name"
                                value="<?php echo esc_attr($current_user->last_name); ?>" required />
                        </div>
                        <div class="profile-input-details">
                            <label for="email">Email</label>
                            <input type="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>"
                                required />
                        </div>
                        <div class="profile-input-details">
                            <label for="company_name">Company Name</label>
                            <input type="text" name="company_name"
                                value="<?php echo esc_attr($meta_values['company_name']); ?>" />
                        </div>
                        <div class="profile-input-details">
                            <label for="phone_number">Phone</label>
                            <input type="text" name="phone_number"
                                value="<?php echo esc_attr($meta_values['phone_number']); ?>" />
                        </div>
                        <div class="profile-input-details">
                            <label for="address">Address</label>
                            <input type="text" name="address"
                                value="<?php echo esc_attr($meta_values['address']); ?>" />
                        </div>
                        <div class="profile-input-details">
                            <label for="about_me">About Me</label>
                            <textarea name="about_me"><?php echo esc_textarea($meta_values['about_me']); ?></textarea>
                        </div>
                    </div>

                    <div class="col-50">
                        <div class="profile-input-details">
                            <label for="fb_url">Facebook Url</label>
                            <input type="url" name="fb_url" value="<?php echo esc_url($meta_values['fb_url']); ?>" />
                        </div>
                        <div class="profile-input-details">
                            <label for="insta_url">Instagram Url</label>
                            <input type="url" name="insta_url"
                                value="<?php echo esc_url($meta_values['insta_url']); ?>" />
                        </div>
                        <div class="profile-input-details">
                            <label for="youtube_url">YouTube Url</label>
                            <input type="url" name="youtube_url"
                                value="<?php echo esc_url($meta_values['youtube_url']); ?>" />
                        </div>
                        <div class="profile-input-details">
                            <label for="linkedin_url">LinkedIn Url</label>
                            <input type="url" name="linkedin_url"
                                value="<?php echo esc_url($meta_values['linkedin_url']); ?>" />
                        </div>
                        <div class="profile-input-details">
                            <label for="tiktok_url">TikTok Url</label>
                            <input type="url" name="tiktok_url"
                                value="<?php echo esc_url($meta_values['tiktok_url']); ?>" />
                        </div>
                        <div class="profile-input-details">
                            <label for="pinterest_url">Pinterest Url</label>
                            <input type="url" name="pinterest_url"
                                value="<?php echo esc_url($meta_values['pinterest_url']); ?>" />
                        </div>
                    </div>

                    <div class="profile-divider"></div>
                    <div class="profile-btn-row">
                        <button type="submit" class="profile-btn">Update profile</button>
                        <!-- <a href="<?php echo get_author_posts_url($current_user->ID); ?>" class="profile-btn">View public profile</a> -->
                    </div>
                </form>
            </div>

            <div class="profile-bottom-details">
                <h3 class="dashboard-sub-title">Change Password</h3>
                <span>*After you change the password you will have to login again.</span>
                <form method="post" action="<?= admin_url('admin-post.php') ?>">
                    <?php wp_nonce_field('change_password_action', 'change_password_nonce'); ?>
                    <input type="hidden" name="action" value="custom_change_password">
                    <div class="profile-input-details">
                        <label for="old_password">Old Password</label>
                        <input type="password" name="old_password" required />
                    </div>
                    <div class="profile-input-details">
                        <label for="new_password">New Password</label>
                        <input type="password" name="new_password" required />
                    </div>
                    <div class="profile-input-details">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" name="confirm_password" required />
                    </div>
                    <button type="submit" class="profile-btn">Reset Password</button>
                </form>
            </div>
        </div>

        <div class="profile-upload-wrapper">
            <div class="top-img">
                <img src="<?= esc_url($profile_image_url)?: get_template_directory_uri() . '/dashboard/images/upload.png' ?>" alt="">
            </div>
            <button type="button" class="profile-btn" onclick="document.getElementById('profile_image').click()" id="triggerUpload">Upload Image</button>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const uploadBtn = document.getElementById('triggerUpload');
                    const fileInput = document.getElementById('profile_image');
                    const imgPreview = document.querySelector('.top-img img');

                    
                    fileInput.addEventListener('change', function () {
                        if (fileInput.files && fileInput.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                imgPreview.src = e.target.result;
                            };
                            reader.readAsDataURL(fileInput.files[0]);
                        }
                    });
                });
            </script>
            <span>* recommended size: minimum 550px</span>
        </div>

    </div>
</div>

<?php get_footer('dashboard') ?>