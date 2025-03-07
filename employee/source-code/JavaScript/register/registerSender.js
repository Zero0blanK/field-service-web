document.addEventListener("DOMContentLoaded", () => {
  console.log("Register Sender Loaded");

  const userType = document.getElementById("register_userType");
  const fullName = document.getElementById("register_FullName");
  const emailAddress = document.getElementById("register_EmailAddress");
  const phoneNumber = document.getElementById("register_PhoneNumber");
  const address = document.getElementById("register_Address");
  const city = document.getElementById("register_City");
  const zipCode = document.getElementById("register_zipCode");
  const password = document.getElementById("register_Password");
  const registerSubmit = document.getElementById("register_submit");
  const companyName = document.getElementById("register_CompanyName");

  registerSubmit.addEventListener("click", () => {
    registerSubmit.setAttribute("loading", true);

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
        registerSubmit.removeAttribute("loading");
        registerSubmit.setAttribute("variant", "danger");
        return;
      }
    }
    
    
    const requestData = {
      action: "register",
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
      registerSubmit.removeAttribute("loading");
      registerSubmit.setAttribute("variant", "danger");
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
      const response = await fetch("register.php", {
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
        registerSubmit.removeAttribute("loading");
        registerSubmit.setAttribute("variant", "success");
        window.location.href = result.redirect;
      } else if (result.status === "error") {
        console.log("Login failed:", result.message);
        registerSubmit.removeAttribute("loading");
        registerSubmit.setAttribute("variant", "danger");
      }
    } catch (error) {
      console.error("Error sending data:", error);
      registerSubmit.removeAttribute("loading");
      registerSubmit.setAttribute("variant", "danger");
    }
  }
});
