<?php
ob_clean();
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
define('BASE_URL', '/field-service-web/employee/source-code');
define('SPECIFIC_URL', '/field-service-web/employee');

include PROJECT_ROOT . "/Controllers/CredentialsValidator.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/StyleSheet/login.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/StyleSheet/shoelace/cdn/themes/dark.css" />
    <script type="module" src="<?= BASE_URL ?>/StyleSheet/shoelace/cdn/shoelace.js"></script>
    <title>Login</title>
</head>

<body>
    <script src="<?= BASE_URL ?>/JavaScript/login/loginSender.js"></script>
    <div class="content">
        <nav class="sl-theme-dark">
            <div style="display: flex; flex-direction: row; justify-content: flex-start; align-items: flex-end; 
                border: solid var(--sl-input-border-width) var(--sl-input-border-color); width: 100%;
                border-radius: 10px; padding: 30px;">
                <div style="display: flex;justify-content: center;flex-direction: column;align-items: center;flex-wrap: nowrap;">
                    <h1 style="font-size: 22px; margin: 0px; color: #27BAFD;">Sign In</h1>
                    <p style="font-size: 16px; margin: 0px; padding-top: 10px; padding-bottom: 20px;">Access your field service customer dashboard</p>
                    <br>
                    <sl-input id="login_emailAddress" label="Email Address" clearable type="email" placeholder="email@example.com" style="width: 350px;">
                        <sl-icon name="envelope" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-input id="login_emailPassword" label="Password" clearable type="password" placeholder="••••••••" password-toggle style="width: 350px;">
                        <sl-icon name="key" slot="prefix"></sl-icon>
                    </sl-input>
                    <div style="width: 100%; display: flex; align-items: flex-end; flex-direction: column;">
                        <a style="text-decoration: none; font-size: 14px;" href="<?= BASE_URL ?>/Webpage/forgotPassword.php">Forgot password?</a>
                    </div>
                    <div style="width: 100%; display: flex; align-items: flex-start; flex-direction: column;">
                        <sl-checkbox id="login_rememberMe">Remember me</sl-checkbox>
                    </div>
                    <br>
                    <sl-button variant="primary" id="login_submit" style="width: 350px;">
                        <sl-icon slot="prefix" name="box-arrow-in-right"></sl-icon>
                        Sign In
                    </sl-button>
                    <br>
                    <div style="font-size: 14px;">
                        <p style="padding: 0px; margin: 0px;">Don't have an account? <a style="text-decoration: none;" href="<?= BASE_URL ?>/Webpage/createAccount.php">Create one now</a></p>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>