<?php
session_start();
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
define('BASE_URL_STYLE', '/field-service-web/employee/source-code');

include PROJECT_ROOT . "/Controllers/CredentialsLogout.php";
include PROJECT_ROOT . "/Controllers/newRequestRecieverController.php";

// Check if session variables are set and not empty
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
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/newRequest.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>New Request</title>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/newRequest/newRequestCreate.js"></script>
    <div class="content">
        <div class="topNavigationBar">
            <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
            <sl-breadcrumb class="topNavbar">
                <sl-breadcrumb-item>
                    <sl-icon slot="prefix" name="check-square"></sl-icon>
                    <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| New Request</label>
                </sl-breadcrumb-item>
            </sl-breadcrumb>
        </div>
        <nav class="sl-theme-dark">
            <div class="filterBar">
                <div class="filterBar1">
                    <sl-input id="createRequest_Title" label="Title" help-text="What's the title of this Request?" size="medium" clearable style="width: 100%;"></sl-input>
                    <sl-divider style="--spacing: 10px; width: 100%;"></sl-divider>
                    <sl-textarea id="createRequest_Description" label="Description" help-text="Give us a brief description of this Request?" size="medium" clearable style="width: 100%;"></sl-textarea>
                    <br>
                    <sl-select id="createRequest_Priority" label="Priority" help-text="How urgent is this Request?" size="medium" style="width: 100%;">
                        <sl-option value="">None</sl-option>
                        <sl-option value="low">Low</sl-option>
                        <sl-option value="medium">Medium</sl-option>
                        <sl-option value="high">High</sl-option>
                        <sl-option value="urget">Urgent</sl-option>
                    </sl-select>
                    <br>
                    <div style="display: flex; width: 100%;">
                        <sl-input id="createRequest_scheduledDate" config-id="date" label="Schedule Date" help-text="Date for this request to be done?" size="medium" clearable style="width: 50%; padding-right: 5px;"></sl-input>
                        <br>
                        <sl-input id="createRequest_scheduledTime" config-id="time" label="Schedule Time" help-text="Time for this request to be done?" size="medium" clearable style="width: 50%; padding-left: 5px;"></sl-input>
                    </div>
                    <br>
                    <sl-input id="createRequest_Location" label="Location" help-text="Where is the location of this Request?" size="medium" clearable style="width: 100%;"></sl-input>
                    <sl-divider style="--spacing: 10px;"></sl-divider>
                    <sl-button id="createRequestSubmitButton" variant="success" outline>Create</sl-button>
                </div>
            </div>
        </nav>
    </div>
    <script>
        document.querySelectorAll('[config-id="date"]').forEach((datePicker) => {
            flatpickr(datePicker, {
                minDate: "today"
            });
        });
        document.querySelectorAll('[config-id="time"]').forEach((datePicker) => {
            flatpickr(datePicker, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
            });
        });
    </script>
</body>

</html>