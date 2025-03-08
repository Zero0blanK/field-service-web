<?php
session_start();
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
define('BASE_URL_STYLE', '/field-service-web/employee/source-code');

include PROJECT_ROOT . "/Controllers/CredentialsLogout.php";
//include PROJECT_ROOT . "/Controllers/newRequestRecieverController.php";

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
        </nav>
    </div>
</body>

</html>