document.addEventListener("DOMContentLoaded", () => {
  console.log("Update Sender Loaded");

  const userType = document.getElementById("update_UserType");
  const fullName = document.getElementById("update_FullName");
  const emailAddress = document.getElementById("update_EmailAddress");
  const phoneNumber = document.getElementById("update_PhoneNumber");
  const address = document.getElementById("update_Address");
  const city = document.getElementById("update_City");
  const zipCode = document.getElementById("update_zipCode");
  const password = document.getElementById("update_Password");
  const updateSubmit = document.getElementById("update_submit");
  const companyName = document.getElementById("update_CompanyName");

  updateSubmit.addEventListener("click", () => {
    updateSubmit.setAttribute("loading", true);

    const requiredFields = [
      fullName.value,
      emailAddress.value,
      phoneNumber.value,
      address.value,
      city.value,
      zipCode.value,
      password.value,
    ];

    if (userType.value === "Company") {
      if (companyName.value.trim() === "") {
        console.log("Company Name is required!");
        updateSubmit.removeAttribute("loading");
        updateSubmit.setAttribute("variant", "danger");
        return;
      }
    }

    const requestData = {
      action: "update",
      userType: userType.value,
      fullname: fullName.value,
      email: emailAddress.value,
      phone: phoneNumber.value,
      address: address.value,
      company: companyName.value,
      city: city.value,
      zip: zipCode.value,
      password: password.value,
    };

    if (requiredFields.some((value) => value.trim() === "")) {
      console.log("Empty fields detected!");
      updateSubmit.removeAttribute("loading");
      updateSubmit.setAttribute("variant", "danger");
    } else {
      console.log("Sending data:", requestData);
      sendRequestData(requestData);
    }
  });

  function clearInputFields() {
    fullName.value = "";
    emailAddress.value = "";
    phoneNumber.value = "";
    address.value = "";
    companyName.value = "";
    city.value = "";
    zipCode.value = "";
    password.value = "";
  }

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
        clearInputFields();
        updateSubmit.removeAttribute("loading");
        updateSubmit.setAttribute("variant", "success");
        setTimeout(() => {window.location.reload();}, 3000);
      } else if (result.status === "error") {
        console.log("Login failed:", result.message);
        updateSubmit.removeAttribute("loading");
        updateSubmit.setAttribute("variant", "danger");
      }
    } catch (error) {
      console.error("Error sending data:", error);
      updateSubmit.removeAttribute("loading");
      updateSubmit.setAttribute("variant", "danger");
    }
  }
});
