<?php
define('BASE_URL', '/field-service-web/employee/source-code');
define('SPECIFIC_URL', '/field-service-web/employee')
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/StyleSheet/sidebar.css" />
<link rel="stylesheet" href="<?= BASE_URL ?>/StyleSheet/shoelace/cdn/themes/dark.css" />
<script type="module" src="<?= BASE_URL ?>/StyleSheet/shoelace/cdn/shoelace.js"></script>

<div class="sidebar">
  <nav class="sl-theme-dark">
    <sl-drawer label="Field Service" placement="start" class="drawer-header-actions" style="--size: 300px; font-weight: 500;">
      <sl-icon-button class="new-window" slot="header-actions" name="box-arrow-up-right"></sl-icon-button>
      <sl-menu>
        <label style="padding-left: 10px;">Service Request</label>
        <sl-divider></sl-divider>
        <sl-menu-item>
          <sl-icon slot="prefix" name="check-square"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/newRequest.php">New Request</a>
        </sl-menu-item>
        <sl-menu-item>
          <sl-icon slot="prefix" name="clock-history"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/workOrderHistory.php">Work Order History</a>
        </sl-menu-item>
        <sl-menu-item>
          <sl-icon slot="prefix" name="clipboard-check"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/trackStatus.php">Track Status</a>
        </sl-menu-item>
      </sl-menu>
      <br>
      <sl-menu>
        <label style="padding-left: 10px;">Account Management</label>
        <sl-divider></sl-divider>
        <sl-menu-item>
          <sl-icon slot="prefix" name="gear"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/profileSettings.php">Settings</a>
        </sl-menu-item>
        <sl-menu-item>
          <sl-icon slot="prefix" name="sliders"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/preferences.php">Preferences</a>
        </sl-menu-item>
      </sl-menu>
      <br>
      <sl-button slot="footer" variant="primary">Logout</sl-button>
    </sl-drawer>
    <sl-button class="sidebar-button">
      <sl-icon name="layout-sidebar"></sl-icon>
    </sl-button>

    <script>
      const drawer = document.querySelector('.drawer-header-actions');
      const openButton = drawer.nextElementSibling;
      const closeButton = drawer.querySelector('sl-button[variant="primary"]');
      const newWindowButton = drawer.querySelector('.new-window');

      openButton.addEventListener('click', () => drawer.show());
      closeButton.addEventListener('click', () => drawer.hide());
      newWindowButton.addEventListener('click', () => window.open(location.href));
    </script>
  </nav>
</div>