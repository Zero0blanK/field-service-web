<?php
session_start();
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
define('BASE_URL_STYLE', '/field-service-web/employee/source-code');

include PROJECT_ROOT . "/Controllers/CredentialsLogout.php";
include PROJECT_ROOT . "/Controllers/profileSettingsController.php";

if (
    !isset($_SESSION['user_id'], $_SESSION['name'], $_SESSION['role']) ||
    empty($_SESSION['user_id']) ||
    empty($_SESSION['name']) ||
    empty($_SESSION['role'])
) {
    header("Location: /field-service-web/employee/source-code/Webpage/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/profileSettings.css">
    <title>Profile Settings</title>
</head>

<body>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/profileSettings/profileSettingsFunctions.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/profileSettings/profileSettingsSender.js"></script>
    <div class="content">
        <div class="topNavigationBar">
            <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
            <sl-breadcrumb class="topNavbar">
                <sl-breadcrumb-item>
                    <sl-icon slot="prefix" name="gear"></sl-icon>
                    <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Settings</label>
                </sl-breadcrumb-item>
            </sl-breadcrumb>
        </div>
        <nav class="sl-theme-dark">
            <div class="filterBar">
                <div class="filterBar1">
                    <br>
                    <div style="display: flex; justify-content: center; width: 100%;">
                        <h1 style="font-size: 18px; margin: 0px; color: #27BAFD;">Update Account Information</h1>
                    </div>
                    <br>
                    <sl-radio-group id="update_UserType" value="Resident" style="width: 100%; display: flex; justify-content: center;">
                        <sl-radio-button value="Resident" style="width: 200px; padding-right: 10px;">Resident</sl-radio-button>
                        <sl-radio-button value="Company" style="width: 200px;">Company</sl-radio-button>
                    </sl-radio-group>
                    <br>
                    <sl-input id="update_FullName" label="New Full Name" clearable type="text" placeholder="Enter your name" style="width: 100%;">
                        <sl-icon name="person-square" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-input id="update_EmailAddress" label="New Email Address" clearable type="email" placeholder="email@example.com" style="width: 100%;">
                        <sl-icon name="envelope" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-input id="update_PhoneNumber" label="New Number" clearable type="text" placeholder="(123) 456-7890" style="width: 100%;">
                        <sl-icon name="phone" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-input id="update_Address" label="New Address" clearable type="text" placeholder="Enter your Address" style="width: 100%;">
                        <sl-icon name="geo-alt" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-input id="update_CompanyName" label="New Company Name" clearable type="text" placeholder="Enter your company name" style="width: 100%;" disabled>
                        <sl-icon name="building" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-input id="update_City" label="New City" clearable type="email" placeholder="Enter your city" style="width: 100%;">
                        <sl-icon name="buildings" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-input id="update_zipCode" label="New Zip Code" clearable type="text" placeholder="1234" style="width: 100%;">
                        <sl-icon name="phone" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-input id="update_Password" label="New Password" clearable type="password" placeholder="••••••••" password-toggle style="width: 100%;">
                        <sl-icon name="key" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-button variant="primary" id="update_submit" style="width: 100%;">
                        <sl-icon slot="prefix" name="box-arrow-in-right"></sl-icon>
                        Update
                    </sl-button>
                    <br>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>