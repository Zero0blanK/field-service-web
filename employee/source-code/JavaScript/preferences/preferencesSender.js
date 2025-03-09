document.addEventListener("DOMContentLoaded", () => {
  console.log("Preferences Sender Loaded");

  const TechnicianInput = document.getElementById("preferences_Technician");
  const DayTimeInput = document.getElementById("preferences_DayTime");
  const MethodInput = document.getElementById("preferences_Method");
  const preferencesSubmit = document.getElementById("update_preferences");

  fetch(
    "/field-service-web/employee/source-code/Controllers/newRequestController.php?fetchTechnicianID=true"
  )
    .then((response) => response.json())
    .then((data) => {
      const dropdown = document.getElementById("preferences_Technician");

      // Ensure the response contains data
      if (!data || data.length === 0) {
        console.error("No technician data received.");
        dropdown.innerHTML =
          "<sl-option value=''>No Technicians Available</sl-option>";
        return;
      }

      // Populate the dropdown
      dropdown.innerHTML = "<sl-option value=''>Select Technician</sl-option>";
      data.forEach((technician) => {
        const option = document.createElement("sl-option");
        option.value = technician.tech_id;
        option.textContent = `${technician.tech_id} - ${technician.name}`;
        dropdown.appendChild(option);
      });
    })
    .catch((error) => {
      console.error("Error fetching technician data:", error);
      document.getElementById("preferences_Technician").innerHTML =
        "<sl-option value=''>Error Loading Technicians</sl-option>";
    });

  preferencesSubmit.addEventListener("click", () => {
    preferencesSubmit.setAttribute("loading", true);

    const requestData = {
      action: "update",
      technician: TechnicianInput.value,
      time: DayTimeInput.value,
      method: MethodInput.value,
    };

    // Ensure all fields are filled
    if (Object.values(requestData).some((value) => value.trim() === "")) {
      console.log("Empty fields detected!");
      preferencesSubmit.removeAttribute("loading");
      preferencesSubmit.setAttribute("variant", "danger");
      clearFields();
      return;
    }

    console.log("Sending data:", requestData);
    sendRequestData(requestData);
  });

  function clearFields() {
    TechnicianInput.value = "";
    DayTimeInput.value = "";
    MethodInput.value = "";
  }

  async function sendRequestData(data) {
    try {
      const response = await fetch(window.location.pathname, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const result = await response.json();
      console.log("Response Data:", result);

      if (result.status === "success") {
        clearFields();
        preferencesSubmit.removeAttribute("loading");
        preferencesSubmit.setAttribute("variant", "success");
        setTimeout(() => window.location.reload(), 3000);
      } else {
        console.log("Request failed:", result.message);
        preferencesSubmit.removeAttribute("loading");
        preferencesSubmit.setAttribute("variant", "danger");
      }
    } catch (error) {
      console.error("Error sending data:", error);
      preferencesSubmit.removeAttribute("loading");
      preferencesSubmit.setAttribute("variant", "danger");
    }
  }
});
