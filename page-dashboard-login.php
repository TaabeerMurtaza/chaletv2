<?php

/**
 * Template Name: login sign up
 *  */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup</title>
    <link rel="stylesheet" href="<?= get_template_directory_uri() ?>/dashboard/css/login.css?v=<?= filemtime(get_template_directory() . '/dashboard/css/login.css') ?>" />
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form id="login-form" class="form active">
                <h2>Login</h2>
                <div class="input-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                <button type="submit">Login</button>
                <p class="switch-form">Don't have an account? <span id="show-signup">Sign Up</span></p>
            </form>

            <form id="signup-form" class="form">
                <h2>Sign Up</h2>
                <div class="input-group">
                    <label for="signup-username">Username</label>
                    <input type="text" id="signup-username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="signup-email">Email</label>
                    <input type="email" id="signup-email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="signup-password">Password</label>
                    <input type="password" id="signup-password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="signup-confirm-password">Confirm Password</label>
                    <input type="password" id="signup-confirm-password" name="confirm-password" required>
                </div>
                <button type="submit">Sign Up</button>
                <p class="switch-form">Already have an account? <span id="show-login">Login</span></p>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>