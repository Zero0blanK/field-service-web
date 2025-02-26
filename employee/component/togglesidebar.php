<?php
// Get absolute path
$fullPath = $_SERVER['SCRIPT_NAME']; // Example: "/webpage/job-scheduling/Sub1.php"

// Extract directory and file name
$pathParts = explode('/', trim($fullPath, '/'));
$directory = $pathParts[count($pathParts) - 2] ?? ''; // Get the parent directory (e.g., "job-scheduling")
$currentPage = end($pathParts); // Get the file name (e.g., "Sub1.php")

// Define paths based on directory and file
$paths = [
    'webpage' => [
        'serviceRequest_NewRequest.php' => " | Service Requests > New Request",
        'serviceRequest_TrackStatus.php' => " | Service Requests > Track Status",
        'serviceRequest_WorkOrderHistory.php' => " | Service Requests > Work Order History",
        'accountManagement_ProfileSettings.php' => " | Account Management > Profile Settings",
        'accountManagement_Preferences.php' => " | Account Management > Preferences",
    ]
];

// Determine path text
$pathText = $paths[$directory][$currentPage] ?? " | Home";

?>

<div class="toggle-btn-container" id="toggle-btn-container">
    <button class="toggle-btn" onclick="toggleSidebar()">
        <img src="<?= BASE_URL ?>/assets/expand_navbar.svg" alt="Expand Navbar">
    </button>
    <p><?= $pathText; ?></p>
</div>
