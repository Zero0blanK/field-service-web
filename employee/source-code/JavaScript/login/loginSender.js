document.addEventListener("DOMContentLoaded", () => {
  console.log("Login Sender Loaded");

  const emailAddress = document.getElementById("login_emailAddress");
  const emailPassword = document.getElementById("login_emailPassword");
  const loginSubmit = document.getElementById("login_submit");

  loginSubmit.addEventListener("click", () => {
    loginSubmit.setAttribute("loading", true);
    const requestData = {
      action: "login",
      email: emailAddress.value,
      password: emailPassword.value,
    };

    if (Object.values(requestData).some((value) => value === "")) {
      console.log("Empty fields detected!");
      loginSubmit.removeAttribute("loading");
      loginSubmit.setAttribute("variant", "danger");
      clearFields();
    } else {
      console.log("Sending data:", requestData);
      sendRequestData(requestData);
    }
  });

  function clearFields() {
    emailAddress.value = "";
    emailPassword.value = "";
  }

  async function sendRequestData(data) {
    try {
      const response = await fetch("login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      // Parse JSON response correctly
      const result = await response.json();

      console.log("Response Data:", result);

      if (result.status === "success") {
        console.log("Redirecting to:", result.redirect);
        loginSubmit.removeAttribute("loading");
        loginSubmit.setAttribute("variant", "success");
        window.location.href = result.redirect;
      } else if (result.status === "error") {
        console.log("Login failed:", result.message);
        loginSubmit.removeAttribute("loading");
        loginSubmit.setAttribute("variant", "danger");
      }
      
      clearFields();
    } catch (error) {
      console.error("Error sending data:", error);
      loginSubmit.removeAttribute("loading");
      loginSubmit.setAttribute("variant", "danger");
    }
  }
});
