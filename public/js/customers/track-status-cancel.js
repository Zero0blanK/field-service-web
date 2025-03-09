const cancelDialog = document.getElementById("cancelRequestDialog");
const cancelOpenButton = document.getElementById("cancelRequestOpen");
const cancelCloseButton = document.getElementById("cancelRequestClose");
const cancelSubmitButton = document.getElementById("cancelRequestSubmitButton");
const cancelRequestID = document.getElementById("cancelRequest_ID");
const cancelConfirmation = document.getElementById("cancelRequest_Confirmation");

// Open and Close Dialog
cancelOpenButton.addEventListener("click", () => cancelDialog.show());
cancelCloseButton.addEventListener("click", () => cancelDialog.hide());

// Submit Cancellation Request
cancelSubmitButton.addEventListener("click", async () => {
    if (cancelConfirmation.value.toLowerCase() !== "cancel") {
        alert("Please type 'Cancel' to confirm.");
        return;
    }

    const requestID = cancelRequestID.value;
    if (!requestID) {
        alert("Invalid Request ID!");
        return;
    }

    try {
        const response = await fetch("/controllers/customers/track-status.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "cancel_order", requestID }),
        });

        const data = await response.json();
        if (data.status === "success") {
            alert("Request cancelled successfully!");
            cancelDialog.hide();
            location.reload();
        } else {
            alert("Failed to cancel request: " + data.message);
        }
    } catch (error) {
        console.error("Error cancelling request:", error);
    }
});
