<?php
define('BASE_URL', '/Coolant/source-code');
define('SPECIFIC_URL', '/Coolant')
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/StyleSheet/sidebar.css" />
<link rel="stylesheet" href="<?= BASE_URL ?>/StyleSheet/shoelace/cdn/themes/dark.css" />
<script type="module" src="<?= BASE_URL ?>/StyleSheet/shoelace/cdn/shoelace.js"></script>

<div class="sidebar">
  <nav class="sl-theme-dark">
    <sl-drawer label="Coolant" placement="start" class="drawer-header-actions" style="--size: 300px; font-weight: 500;">
      <sl-icon-button class="new-window" slot="header-actions" name="box-arrow-up-right"></sl-icon-button>
      <sl-menu>
        <label style="padding-left: 10px;">Service Report</label>
        <sl-divider></sl-divider>
        <sl-menu-item>
          <sl-icon slot="prefix" name="columns-gap"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/dashboard.php">Dashboard</a>
        </sl-menu-item>
        <sl-menu-item>
          <sl-icon slot="prefix" name="calendar-date"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/schedule.php">Schedule</a>
        </sl-menu-item>
        <sl-menu-item>
          <sl-icon slot="prefix" name="clipboard-check"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/appointment.php">Appointment</a>
        </sl-menu-item>
      </sl-menu>
      <br>
      <sl-menu>
        <label style="padding-left: 10px;">Payments</label>
        <sl-divider></sl-divider>
        <sl-menu-item>
          <sl-icon slot="prefix" name="layout-text-sidebar-reverse"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/quotation.php">Quotation</a>
        </sl-menu-item>
        <sl-menu-item>
          <sl-icon slot="prefix" name="clipboard2-data"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/services-report.php">Services Report</a>
        </sl-menu-item>
        <sl-menu-item>
          <sl-icon slot="prefix" name="receipt-cutoff"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/billing-statement.php">Billing Statement</a>
        </sl-menu-item>
      </sl-menu>
      <br>
      <sl-menu>
        <label style="padding-left: 10px;">Reports</label>
        <sl-divider></sl-divider>
        <sl-menu-item>
          <sl-icon slot="prefix" name="file-person"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/customer.php">Customer</a>
        </sl-menu-item>
        <sl-menu-item>
          <sl-icon slot="prefix" name="person-circle"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/Employee.php">Employee</a>
        </sl-menu-item>
        <sl-menu-item>
          <sl-icon slot="prefix" name="substack"></sl-icon>
          <a href="<?= BASE_URL ?>/Webpage/Employee-Log.php">Employee Log</a>
        </sl-menu-item>
      </sl-menu>
      <sl-button slot="footer" variant="primary">Close</sl-button>
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