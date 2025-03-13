<?php require_once VIEWS . 'partials/header.php' ?>
<?php require_once VIEWS . 'partials/customers/sidebar.php'; ?>


<link rel="stylesheet" href="/public/css/customers/work-order-create.css">
<script src="/public/js/customers/create-new-request.js" defer></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.0/cdn/themes/light.css" />
<script type="module" src="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.0/cdn/shoelace-autoloader.js"></script>


<div class="flex-1 ml-64 p-8">
    <div class="topNavigationBar">
        <sl-breadcrumb class="topNavbar">
            <sl-breadcrumb-item>
                <sl-icon slot="prefix" name="check-square"></sl-icon>
                <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| New Request</label>
            </sl-breadcrumb-item>
        </sl-breadcrumb>
    </div>
    <nav class="sl-theme-dark">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 mt-4">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 mt-4">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        <div class="w-[680px] mt-[20px]">
            <div class="filterBar1">
                <sl-input id="createRequest_Title" label="Title" help-text="What's the title of this Request?" size="medium" clearable style="width: 100%;"></sl-input>
                <sl-divider style="--spacing: 10px; width: 100%;"></sl-divider>
                <sl-textarea id="createRequest_Description" label="Description" help-text="Give us a brief description of this Request?" size="medium" clearable style="width: 100%;"></sl-textarea>
                <br>
                <sl-select id="createRequest_Priority" label="Priority" help-text="How urgent is this Request?" size="medium" style="width: 100%;">
                    <sl-option value="low">Low</sl-option>
                    <sl-option value="medium">Medium</sl-option>
                    <sl-option value="high">High</sl-option>
                    <sl-option value="urgent">Urgent</sl-option>
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
                <sl-button class="mt-4" id="createRequestSubmitButton" variant="success" outline>Create</sl-button>
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

<?php require_once VIEWS . 'partials/footer.php'; ?>