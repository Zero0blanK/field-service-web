<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
define('BASE_URL_STYLE', '/Coolant/source-code');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/schedule.css">
    <title>Schedule</title>
</head>

<body>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/schedule/scheduleFilter.js"></script>
    <div class="content">
        <div class="topNavigationBar">
            <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
            <sl-breadcrumb class="topNavbar">
                <sl-breadcrumb-item>
                    <sl-icon slot="prefix" name="calendar-date"></sl-icon>
                    <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Schedule</label>
                </sl-breadcrumb-item>
            </sl-breadcrumb>
        </div>
        <nav class="sl-theme-dark">
            <div class="filterBar">
                <sl-select label="Order By" id="orderBy">
                    <sl-option value="appointment.id">Ticket</sl-option>
                    <sl-option value="Priority">Priority</sl-option>
                    <sl-option value="Date">Date</sl-option>
                </sl-select>
                <sl-select label="Filter by" id="filterBy">
                    <sl-option value="Pending">Pending</sl-option>
                    <sl-option value="Working">Working</sl-option>
                    <sl-option value="Completed">Completed</sl-option>
                    <sl-option value="Cancelled">Cancelled</sl-option>
                </sl-select>
                <sl-select label="Sort by" id="sortBy">
                    <sl-option value="ASC">Ascending</sl-option>
                    <sl-option value="DESC">Descending</sl-option>
                </sl-select>
                <div class="alert-countdown">
                    <sl-button variant="primary" id="filterApplyButton">Show Alert</sl-button>

                    <sl-alert variant="primary" duration="10000" countdown="rtl" closable>
                        <sl-icon slot="icon" name="info-circle"></sl-icon>
                        You're not stuck, the alert will close after a pretty long duration.
                    </sl-alert>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>