<?php
session_start();
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
define('BASE_URL_STYLE', '/field-service-web/employee/source-code');

include PROJECT_ROOT . "/Controllers/CredentialsLogout.php";
include PROJECT_ROOT . "/Controllers/PreferencesController.php";

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
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/preferences.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Preferences</title>
</head>

<body>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/preferences/preferencesDataRetrieve.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/preferences/preferencesSender.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <div class="content">
        <div class="topNavigationBar">
            <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
            <sl-breadcrumb class="topNavbar">
                <sl-breadcrumb-item>
                    <sl-icon slot="prefix" name="sliders"></sl-icon>
                    <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Preferences</label>
                </sl-breadcrumb-item>
            </sl-breadcrumb>
        </div>
        <nav class="sl-theme-dark">
            <div class="filterBar">
                <div class="filterBar1">
                    <br>
                    <div style="display: flex; justify-content: center; width: 100%;">
                        <h1 style="font-size: 18px; margin: 0px; color: #27BAFD;">Account Preferences</h1>
                    </div>
                    <sl-select id="preferences_Technician" label="Preferred Technician" size="medium" style="width: 100%;">
                        <sl-option value="">Loading...</sl-option>
                    </sl-select>
                    <br>
                    <sl-input id="preferences_DayTime" label="Preferred Day/Time" config-id="time" clearable type="text" placeholder="Day/Time" style="width: 100%;">
                        <sl-icon name="calendar3" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-input id="preferences_Method" label="Preferred Method" clearable type="text" placeholder="Ex. Phone Number" style="width: 100%;">
                        <sl-icon name="person-rolodex" slot="prefix"></sl-icon>
                    </sl-input>
                    <br>
                    <sl-button variant="primary" id="update_preferences" style="width: 100%;">
                        <sl-icon slot="prefix" name="box-arrow-in-right"></sl-icon>
                        Set Preferences
                    </sl-button>
                    <br>
                </div>
            </div>
        </nav>
    </div>
    <script>
        document.querySelectorAll('[config-id="time"]').forEach((datePicker) => {
            flatpickr(datePicker, {
                enableTime: true,
                minDate: "today",
                format: "Y-m-d H:i:s"
            });
        });
    </script>
</body>

</html>