<?php require_once VIEWS . 'partials/header.php' ?>
<?php require_once VIEWS . 'partials/customers/sidebar.php'; ?>


<link rel="stylesheet" href="/public/css/customers/track-status.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.0/cdn/themes/light.css" />
<script type="module" src="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.0/cdn/shoelace-autoloader.js"></script>
<script src="/public/js/customers/track-status-filter.js" defer></script>
<script src="/public/js/customers/track-status-cancel.js" defer></script>

    <div class="flex-1 ml-64 p-8">
        <div class="topNavigationBar">
            <sl-breadcrumb class="topNavbar">
                <sl-breadcrumb-item>
                    <sl-icon slot="prefix" name="clipboard-check"></sl-icon>
                    <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Track Status</label>
                </sl-breadcrumb-item>
            </sl-breadcrumb>
        </div>
        <nav class="sl-theme-dark">
            <div class="mt-[20px]">
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
            <div class="mt-[10px]">
                <div class="requestTable1">
                    <?= $table_content; ?>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>