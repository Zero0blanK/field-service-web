
const Submit = document.getElementById("createRequestSubmitButton");

Submit.addEventListener("click", () => {
Submit.setAttribute("loading", true);

const Title = document.getElementById("createRequest_Title").value;
const Description = document.getElementById("createRequest_Description").value;
const Priority = document.getElementById("createRequest_Priority").value;
const Date = document.getElementById("createRequest_scheduledDate").value;
const Time = document.getElementById("createRequest_scheduledTime").value;
const Location = document.getElementById("createRequest_Location").value;

const requestData = {
    action: "create",
    title: Title,
    description: Description,
    priority: Priority,
    scheduled_date: Date,
    scheduled_time: Time,
    location: Location,
};

if (Object.values(requestData).some((value) => value === "")) {
    Submit.removeAttribute("loading");
    Submit.setAttribute("variant", "warning");
} else {
    console.log("Sending request data:", requestData);
    sendRequestData(requestData);
}
});

function clearInputFields(){
document.getElementById("createRequest_Title").value = "";
document.getElementById("createRequest_Description").value = "";
document.getElementById("createRequest_Priority").value = "";
document.getElementById("createRequest_scheduledDate").value = "";
document.getElementById("createRequest_scheduledTime").value = "";
document.getElementById("createRequest_Location").value = "";
};

async function sendRequestData(data) {
    try {
        const response = await fetch("/user/work-order/create", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
        });

        // Parse JSON response correctly
        const result = await response.json();

        console.log("Response Data:", result);

        if (result.status === "success") {
            clearInputFields();
            Submit.removeAttribute("loading");
            Submit.setAttribute("variant", "success");
            setTimeout(() => {window.location.reload();}, 3000);
        } else if (result.status === "error") {
            console.error("Request failed:", result.message);

            Submit.removeAttribute("loading");
            Submit.setAttribute("variant", "danger");
        }

    } catch (error) {
        console.error("Error sending data:", error);
        Submit.removeAttribute("loading");
        Submit.setAttribute("variant", "danger");
    }
}
