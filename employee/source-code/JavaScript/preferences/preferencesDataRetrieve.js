document.addEventListener("DOMContentLoaded", function () {
  const DayTimeInput = document.getElementById("preferences_DayTime");
  const MethodInput = document.getElementById("preferences_Method");

  const requestData = {
    action: "retrieve",
  };

  sendRequestData(requestData);

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
        console.log("Data received successfully!");
        // Update fields only if values exist (otherwise, keep them blank)
        DayTimeInput.value = result.time || "";
        MethodInput.value = result.method || "";
      } else {
        console.error("Error retrieving data:", result.message);
      }
    } catch (error) {
      console.error("Error sending data:", error);
    }
  }
});
