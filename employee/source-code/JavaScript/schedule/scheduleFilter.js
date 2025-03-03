document.addEventListener("DOMContentLoaded", () => {
  console.log("scheduleFilter.js loaded");
  const orderBy = document.getElementById("dropdownOrderBy");
  const filterBy = document.getElementById("dropdownFilterBy");
  const sortBy = document.getElementById("dropdownSortBy");

    const container = document.querySelector(".alert-countdown");
    const button = container.querySelector("sl-button");
    const alert = container.querySelector("sl-alert");

    button.addEventListener("click", () => alert.show());
    alert.attributes.removeNamedItem("open");
});
