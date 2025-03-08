class CancelRequest {
  constructor(submitButtonId) {
    this.submitButton = document.getElementById(submitButtonId);

    if (!this.submitButton) {
      console.error("Submit button not found.");
      return;
    }

    this.submitButton.addEventListener("click", () => this.handleSubmit());
  }

  getFormData() {
    let requestID = document.getElementById("cancelRequest_ID")?.value;
    let confirmation = document.getElementById("cancelRequest_Confirmation")?.value;

    if (confirmation !== "Cancel" || !requestID) {
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "warning");
      return null;
    }

    return {
      action: "cancel",
      requestID: requestID,
    };
  }

  async sendRequestData(data) {
    try {
      const response = await fetch("workOrderHistory.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const result = await response.json();
      console.log("Response Data:", result);

      if (result.status === "success") {
        this.clearInputFields();
        this.submitButton.removeAttribute("loading");
        this.submitButton.setAttribute("variant", "success");
        setTimeout(() => window.location.reload(), 3000);
      } else {
        console.error("Request failed:", result.message);
        this.submitButton.removeAttribute("loading");
        this.submitButton.setAttribute("variant", "danger");
      }
    } catch (error) {
      console.error("Error sending data:", error);
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "danger");
    }
  }

  clearInputFields() {
    document.getElementById("cancelRequest_ID").value = "";
    document.getElementById("cancelRequest_Confirmation").value = "";
  }

  handleSubmit() {
    console.log("Submit button clicked.");
    this.submitButton.setAttribute("loading", true);

    const formData = this.getFormData();
    if (formData) this.sendRequestData(formData);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  new CancelRequest("cancelRequestSubmitButton");
  console.log("Request Cancel JS Loaded!");

  fetch("/field-service-web/employee/source-code/Controllers/newRequestController.php?fetchRequestID=true")
    .then((response) => response.json())
    .then((data) => {
      const dropdown = document.getElementById("cancelRequest_ID");
      dropdown.innerHTML = "<sl-option value=''>Select Request ID</sl-option>";

      data.forEach((appointment) => {
        const option = document.createElement("sl-option");
        option.value = appointment.order_id;
        option.textContent = `${appointment.order_id} - ${appointment.title}`;
        dropdown.appendChild(option);
      });
    })
    .catch((error) => console.error("Error fetching appointments:", error));
});
