<?php
session_start();
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
define('BASE_URL_STYLE', '/field-service-web/employee/source-code');

include PROJECT_ROOT . "/Controllers/trackStatusController.php";
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
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/trackStatus.css">
    <title>Work Order History</title>
</head>

<body>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/trackStatus/trackStatusFilter.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/trackStatus/trackStatusCancel.js"></script>
    <div class="content">
        <div class="topNavigationBar">
            <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
            <sl-breadcrumb class="topNavbar">
                <sl-breadcrumb-item>
                    <sl-icon slot="prefix" name="clipboard-check"></sl-icon>
                    <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Track Status</label>
                </sl-breadcrumb-item>
            </sl-breadcrumb>
        </div>
        <nav class="sl-theme-dark">
            <div class="filterBar">
                <div class="filterBar1">
                    <div class="column">
                        <sl-select label="Order By" id="orderBy" size="small">
                            <sl-option value="">None</sl-option>
                            <sl-option value="work_orders.order_id">Ticket</sl-option>
                            <sl-option value="work_orders.priority">Priority</sl-option>
                            <sl-option value="work_orders.scheduled_date">Date</sl-option>
                        </sl-select>
                    </div>
                    <div class="column">
                        <sl-select label="Filter by" id="filterBy" size="small">
                            <sl-option value="">None</sl-option>
                            <sl-option value="pending">Pending</sl-option>
                            <sl-option value="assigned">Assigned</sl-option>
                            <sl-option value="in-progress">In-Progress</sl-option>
                            <sl-option value="completed">Completed</sl-option>
                            <sl-option value="cancelled">Cancelled</sl-option>
                        </sl-select>
                    </div>
                    <div class="column">
                        <sl-select label="Sort by" id="sortBy" size="small">
                            <sl-option value="">None</sl-option>
                            <sl-option value="ASC">Ascending</sl-option>
                            <sl-option value="DESC">Descending</sl-option>
                        </sl-select>
                    </div>
                    <div class="column">
                        <sl-button variant="primary" id="filterSubmitButton" size="small">Submit</sl-button>
                    </div>
                    <div class="column1">
                        <sl-dialog label="Cancel Request" class="dialog-deny-close" id="cancelRequestDialog">
                            <sl-button slot="footer" variant="primary" id="cancelRequestClose">Close</sl-button>
                            <sl-select id="cancelRequest_ID" label="Request ID" help-text="Select which request ID you want to Cancel" size="small">
                                <sl-option value="">Loading...</sl-option>
                            </sl-select>
                            <br>
                            <sl-input id="cancelRequest_Confirmation" label="Confirmation" help-text='Type "Cancel" to confirm Cancellation' size="small" clearable></sl-input>
                            <sl-divider style="--spacing: 10px;"></sl-divider>
                            <sl-button id="cancelRequestSubmitButton" variant="warning" outline>Delete</sl-button>
                        </sl-dialog>
                        <sl-button id="cancelRequestOpen" size="small">Cancel Request</sl-button>
                    </div>
                </div>
            </div>
            <div class="requestTable">
                <div class="requestTable1">
                    <?= $table_content; ?>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>