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
    <title>Create Account</title>
</head>

<body>
    <script src="<?= BASE_URL ?>/JavaScript/login/loginSender.js"></script>
    <div class="content">
        <nav class="sl-theme-dark">
            <div style="display: flex; flex-direction: row; justify-content: flex-start; align-items: flex-end; 
                border: solid var(--sl-input-border-width) var(--sl-input-border-color); width: 100%;
                border-radius: 10px; padding: 30px;">
                <div style="display: flex;justify-content: center;flex-direction: column;align-items: center;flex-wrap: nowrap;">
                    <h1 style="font-size: 22px; margin: 0px; color: #27BAFD;">Sign Up</h1>
                    <div style="font-size: 14px;">
                        <p style="padding: 0px; margin: 0px;">Have an account? <a style="text-decoration: none;" href="<?= BASE_URL ?>/Webpage/login.php">Sign in</a></p>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>