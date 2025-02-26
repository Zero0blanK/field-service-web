let activeMenu = null;
let submenuOpened = false; // Track if a submenu is open

function toggleSidebar(forceExpand = false) {
  let sidebar = document.getElementById("sidebar");
  let content = document.getElementById("content");
  let toggleBtnContainer = document.getElementById("toggle-btn-container");

  // If the sidebar is being collapsed, close the active submenu first
  if (!forceExpand && submenuOpened) {
    document.getElementById(`submenu-${activeMenu}`).style.display = "none";
    activeMenu = null;  // Clear the active menu
    submenuOpened = false;  // Set the flag to false as no submenu is open
  }

  if (forceExpand) {
    sidebar.classList.add("expanded");
    content.classList.add("expanded");
    toggleBtnContainer.classList.add("expanded");

    // Restore the last opened submenu when expanding again
    if (submenuOpened && activeMenu) {
      document.getElementById(`submenu-${activeMenu}`).style.display = "block";
    }
  } else {
    sidebar.classList.toggle("expanded");
    content.classList.toggle("expanded");
    toggleBtnContainer.classList.toggle("expanded");

    // If the sidebar is being collapsed, hide any open submenus
    if (!sidebar.classList.contains("expanded")) {
      let submenus = document.querySelectorAll(".submenu");
      submenus.forEach(submenu => (submenu.style.display = "none"));
      submenuOpened = false; // Reset the opened state
      activeMenu = null; // Reset the active menu
    } else {
      // If expanding the sidebar, restore submenu if previously opened
      if (submenuOpened && activeMenu) {
        document.getElementById(`submenu-${activeMenu}`).style.display = "block";
      }
    }
  }
}

function toggleSubmenu(id) {
  let sidebar = document.getElementById("sidebar");

  // If the sidebar is collapsed, expand it and open the submenu
  if (!sidebar.classList.contains("expanded")) {
    toggleSidebar(true);
  }

  if (activeMenu && activeMenu !== id) {
    document.getElementById(`submenu-${activeMenu}`).style.display = "none";
  }

  let submenu = document.getElementById(`submenu-${id}`);
  if (submenu.style.display === "block") {
    submenu.style.display = "none";
    activeMenu = null;
    submenuOpened = false; // No submenu is open
  } else {
    submenu.style.display = "block";
    activeMenu = id;
    submenuOpened = true; // A submenu is now open
  }
}
