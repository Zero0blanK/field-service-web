document.addEventListener("DOMContentLoaded", () => {
    console.log("newRequestCreate.js loaded");
    
    const Title = document.getElementById("createRequest_Title");
    const Description = document.getElementById("createRequest_Description");
    const Priority = document.getElementById("createRequest_Priority");
    const Date = document.getElementById("createRequest_scheduledDate");
    const Time = document.getElementById("createRequest_scheduledTime");
    const Location = document.getElementById("createRequest_Location");
    const Submit = document.getElementById("createRequestSubmitButton");
  
    Submit.addEventListener("click", () => {
      Submit.setAttribute("loading", true);
      const requestData = {
        action: "create",
        title: Title.value,
        description: Description.value,
        priority: Priority.value,
        scheduled_date: Date.value,
        scheduled_time: Time.value,
        location: Location.value,
      };
  
      if (Object.values(requestData).some(value => value === "")) {
        console.log("Empty fields detected!");
        Submit.removeAttribute("loading");
        Submit.setAttribute("variant", "danger");
      } else {
        console.log("Sending data:", requestData);
        sendRequestData(requestData);
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