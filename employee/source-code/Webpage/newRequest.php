<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/field-service-web/employee/source-code');
define('BASE_URL_STYLE', '/field-service-web/employee/source-code');

include PROJECT_ROOT . "/Controllers/newRequestController.php";
include PROJECT_ROOT . "/Controllers/newRequestRecieverController.php";
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
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/newRequest/newRequestFilter.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/newRequest/newRequestCreate.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/newRequest/newRequestCancel.js"></script>
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
                        <sl-dialog label="Request" class="dialog-deny-close" id="requestDialog">
                            <div class="centerDialog">
                                <sl-dialog label="Create Request" class="dialog-deny-close" id="createRequestDialog">
                                    <sl-button slot="footer" variant="primary" id="createRequestClose">Close</sl-button>
                                    <sl-input id="createRequest_Title" label="Title" help-text="What's the title of this Request?" size="small" clearable></sl-input>
                                    <sl-divider style="--spacing: 10px;"></sl-divider>
                                    <sl-textarea id="createRequest_Description" label="Description" help-text="Give us a brief description of this Request?" size="small" clearable></sl-textarea>
                                    <br>
                                    <sl-select id="createRequest_Priority" label="Priority" help-text="How urgent is this Request?" size="small">
                                        <sl-option value="">None</sl-option>
                                        <sl-option value="low">Low</sl-option>
                                        <sl-option value="medium">Medium</sl-option>
                                        <sl-option value="high">High</sl-option>
                                        <sl-option value="urget">Urgent</sl-option>
                                    </sl-select>
                                    <br>
                                    <sl-input id="createRequest_scheduledDate" config-id="date" label="Schedule Date" help-text="Date for this request to be done?" size="small" clearable></sl-input>
                                    <br>
                                    <sl-input id="createRequest_scheduledTime" config-id="time" label="Schedule Time" help-text="Time for this request to be done?" size="small" clearable></sl-input>
                                    <br>
                                    <sl-input id="createRequest_Location" label="Location" help-text="Where is the location of this Request?" size="small" clearable></sl-input>
                                    <sl-divider style="--spacing: 10px;"></sl-divider>
                                    <sl-button id="createRequestSubmitButton" variant="success" outline>Create</sl-button>

                                </sl-dialog>
                                <sl-button id="createRequestOpen" size="large">Create Request</sl-button>

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
                                <sl-button id="cancelRequestOpen" size="large">Cancel Request</sl-button>

                            </div>
                            <sl-button slot="footer" variant="primary" id="requestClose">Close</sl-button>
                        </sl-dialog>
                        <sl-button id="requestOpen" size="small">Request</sl-button>
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