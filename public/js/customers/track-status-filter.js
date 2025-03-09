const orderBy = document.getElementById("orderBy");
const filterBy = document.getElementById("filterBy");
const sortBy = document.getElementById("sortBy");
const applyFilter = document.getElementById("filterSubmitButton");

// Apply Filter
applyFilter.addEventListener("click", async () => {
    applyFilter.setAttribute("loading", true);

    const requestData = {
        action: "fetch_orders",
        orderBy: orderBy.value || "work_orders.order_id",
        filterBy: filterBy.value || "all",
        sortBy: sortBy.value || "ASC"
    };

    try {
        const response = await fetch("/controllers/customers/track-status.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(requestData),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.text();
        document.querySelector(".requestTable1").innerHTML = data;
        console.log("SUCCESS!");
    } catch (error) {
        console.error("Error fetching data:", error);
    }

    applyFilter.removeAttribute("loading");
    applyFilter.setAttribute("variant", "success");
});
