<?php
define('BASE_URL', '/field-service-web/employee');
define('SPECIFIC_URL', '/field-service-web')
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/sidebar.css">
<script src="<?= BASE_URL ?>/js/sidebar.js" defer></script>

<div class="sidebar" id="sidebar">

    <a href="<?= SPECIFIC_URL ?>/employee/webpage/home.php" class="logo" id="logo">
        <img class="logo_icon" src="<?= BASE_URL ?>/assets/default_profile.svg">
        <span class="logo-text">Customer</span>
    </a>

    <div class="menu">
        <div class="menu-item" onclick="toggleSubmenu(1)">
            <img src="<?= BASE_URL ?>/assets/schedule-and-appointment.svg">
            <span class="menu-text">Service Request</span>
        </div>
        <div class="submenu" id="submenu-1">
            <a href="<?= BASE_URL ?>/webpage/serviceRequest_NewRequest.php">New Request</a>
            <a href="<?= BASE_URL ?>/webpage/serviceRequest_WorkOrderHistory.php">Work Order History</a>
            <a href="<?= BASE_URL ?>/webpage/serviceRequest_TrackStatus.php">Track Status</a>
        </div>
        <div class="menu-item" onclick="toggleSubmenu(2)">
            <img src="<?= BASE_URL ?>/assets/account.svg">
            <span class="menu-text">Account Management</span>
        </div>
        <div class="submenu" id="submenu-2">
            <a href="<?= BASE_URL ?>/webpage/accountManagement_ProfileSettings.php">Profile Settings</a>
            <a href="<?= BASE_URL ?>/webpage/accountManagement_Preferences.php">Preferences</a>
        </div>
    </div>

    <a href="<?= SPECIFIC_URL ?>/employee/webpage/logout.php" class="menuitem_logout" id="logout">
        <img src="<?= BASE_URL ?>/assets/logout.svg">
        <span class="menu-text">Log-out</span>
    </a>
</div>