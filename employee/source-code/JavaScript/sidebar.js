document.addEventListener("DOMContentLoaded", () => {
  const drawer = document.querySelector(".drawer-header-actions");
  const openButton = document.querySelector(".sidebar-button");
  const closeButton = drawer.querySelector('sl-button[variant="primary"]');
  const newWindowButton = drawer.querySelector(".new-window");

  openButton.addEventListener("click", () => drawer.show());
  closeButton.addEventListener("click", () => drawer.hide());
  newWindowButton.addEventListener("click", () => window.open(location.href));

  closeButton.addEventListener("click", function () {
    const requestData = {
      action: "logout",
      confirmation: true,
    };

    sendRequestData(requestData);
  });

  async function sendRequestData(data) {
    try {
      const response = await fetch(window.location.pathname, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      // Parse JSON response correctly
      const result = await response.json();

      console.log("Response Data:", result);

      if (result.status === "success") {
        console.log("Redirecting to:", result.redirect);
        closeButton.setAttribute("variant", "success");
        window.location.href = result.redirect;
      } else if (result.status === "error") {
        console.log("Login failed:", result.message);
        closeButton.setAttribute("variant", "danger");
      }
    } catch (error) {
      console.error("Error sending data:", error);
      closeButton.setAttribute("variant", "danger");
    }
  }
});
