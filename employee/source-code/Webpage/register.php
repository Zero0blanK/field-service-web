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
    <link rel="stylesheet" href="<?= BASE_URL ?>/StyleSheet/register.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/StyleSheet/shoelace/cdn/themes/dark.css" />
    <script type="module" src="<?= BASE_URL ?>/StyleSheet/shoelace/cdn/shoelace.js"></script>
    <title>Register</title>
</head>

<body>
    <script src="<?= BASE_URL ?>/JavaScript/register/registerSender.js"></script>
    <script src="<?= BASE_URL ?>/JavaScript/register/registerFunctions.js"></script>
    <div class="content">
        <nav class="sl-theme-dark">
            <div style="display: flex; flex-direction: row; justify-content: flex-start; align-items: flex-end; 
                border: solid var(--sl-input-border-width) var(--sl-input-border-color); width: 100%;
                border-radius: 10px; padding: 30px;">
                <div style="display: flex;justify-content: center;flex-direction: column;align-items: center;flex-wrap: nowrap;">
                    <h1 style="font-size: 22px; margin: 0px; color: #27BAFD;">Sign Up</h1>
                    <p style="font-size: 16px; margin: 0px; padding-top: 10px; padding-bottom: 10px;">Access your field service customer dashboard</p>
                    <br>
                    <sl-radio-group id="register_userType" value="Resident" style="width: 350px;">
                        <sl-radio-button value="Resident" style="width: 175px; padding-right: 10px;">Resident</sl-radio-button>
                        <sl-radio-button value="Company" style="width: 175px;">Company</sl-radio-button>
                    </sl-radio-group>
                    <br>
                    <sl-input id="register_FullName" label="Full Name" clearable type="text" placeholder="Enter your name" style="width: 350px;">
                        <sl-icon name="person-square" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <div style="display: flex;">
                        <sl-input id="register_EmailAddress" label="Email Address" clearable type="email" placeholder="email@example.com" style="width: 175px; padding-right: 10px;">
                            <sl-icon name="envelope" slot="prefix"></sl-icon>
                        </sl-input>
                        <sl-input id="register_PhoneNumber" label="Number" clearable type="text" placeholder="(123) 456-7890" style="width: 175px;">
                            <sl-icon name="phone" slot="prefix"></sl-icon>
                        </sl-input>
                    </div>
                    <br>
                    <sl-input id="register_Address" label="Address" clearable type="text" placeholder="Enter your Address" style="width: 350px;">
                        <sl-icon name="geo-alt" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-input id="register_CompanyName" label="Company Name" clearable type="text" placeholder="Enter your company name" style="width: 350px;" disabled>
                        <sl-icon name="building" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <div style="display: flex;">
                        <sl-input id="register_City" label="City" clearable type="email" placeholder="Enter your city" style="width: 175px; padding-right: 10px;">
                            <sl-icon name="buildings" slot="prefix"></sl-icon>
                        </sl-input>
                        <sl-input id="register_zipCode" label="Zip Code" clearable type="text" placeholder="1234" style="width: 175px;">
                            <sl-icon name="phone" slot="prefix"></sl-icon>
                        </sl-input>
                    </div>
                    <br>
                    <sl-input id="register_Password" label="Password" clearable type="password" placeholder="••••••••" password-toggle style="width: 350px;">
                        <sl-icon name="key" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-button variant="primary" id="register_submit" style="width: 350px;">
                        <sl-icon slot="prefix" name="box-arrow-in-right"></sl-icon>
                        Sign Up
                    </sl-button>
                    <br>
                    <div style="font-size: 14px;">
                        <p style="padding: 0px; margin: 0px;">Have an account? <a style="text-decoration: none;" href="<?= BASE_URL ?>/Webpage/login.php">Log in now</a></p>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>