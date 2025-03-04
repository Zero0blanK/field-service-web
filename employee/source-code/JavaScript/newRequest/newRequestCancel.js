document.addEventListener("DOMContentLoaded", () => {
  console.log("newRequestCreate.js loaded");

  const RequestID = document.getElementById("cancelRequest_ID");
  const Confirmation = document.getElementById("cancelRequest_Confirmation");
  const Submit = document.getElementById("cancelRequestSubmitButton");

  Submit.addEventListener("click", () => {
    Submit.setAttribute("loading", true);

    if (Confirmation.value === "Cancel") {
      const requestData = {
        action: "cancel",
        requestID: RequestID.value,
      };

      if (Object.values(requestData).some((value) => value === "")) {
        console.log("Empty fields detected!");
        Submit.removeAttribute("loading");
        Submit.setAttribute("variant", "danger");
      } else {
        console.log("Sending data:", requestData);
        sendRequestData(requestData);
      }
    } else {
      console.log("Confirmation is not 'Cancel'");
      Submit.removeAttribute("loading");
      Submit.setAttribute("variant", "danger");
    }
  });

  async function sendRequestData(data) {
    try {
      const response = await fetch("newRequest.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const responseData = await response.text();
      document.querySelector(".work_orders-table").innerHTML = responseData;
      console.log("SUCCESS!");
      Submit.removeAttribute("loading");
      Submit.setAttribute("variant", "success");
    } catch (error) {
      console.error("Error sending data:", error);
      Submit.removeAttribute("loading");
      Submit.setAttribute("variant", "danger");
    }
  }
});
