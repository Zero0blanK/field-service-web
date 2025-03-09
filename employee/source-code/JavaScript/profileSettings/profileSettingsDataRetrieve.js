document.addEventListener("DOMContentLoaded", function () {

  const fullName = document.getElementById("update_FullName");
  const emailAddress = document.getElementById("update_EmailAddress");
  const phoneNumber = document.getElementById("update_PhoneNumber");
  const address = document.getElementById("update_Address");
  const city = document.getElementById("update_City");
  const zipCode = document.getElementById("update_zipCode");
  const companyName = document.getElementById("update_CompanyName");

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
        fullName.value = result.name || "";
        emailAddress.value = result.email || "";
        phoneNumber.value = result.phone || "";
        address.value = result.address || "";
        companyName.value = result.company || "";
        city.value = result.city || "";
        zipCode.value = result.zip || "";
      } else {
        console.error("Error retrieving data:", result.message);
      }
    } catch (error) {
      console.error("Error sending data:", error);
    }
  }
});
