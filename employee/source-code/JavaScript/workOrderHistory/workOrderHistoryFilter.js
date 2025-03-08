document.addEventListener("DOMContentLoaded", () => {
  console.log("scheduleFilter.js loaded");
  const orderBy = document.getElementById("orderBy");
  const filterBy = document.getElementById("filterBy");
  const sortBy = document.getElementById("sortBy");
  const applyFilter = document.getElementById("filterSubmitButton");

  // Filter Functions
  applyFilter.addEventListener("click", () => {
    applyFilter.setAttribute("loading", true);
    const orderValue = orderBy.value;
    const filterValue = filterBy.value;
    const sortValue = sortBy.value;

    if (orderValue === "" || filterValue === "" || sortValue === "") {
      console.log("Empty String!");
      applyFilter.removeAttribute("loading");
      applyFilter.setAttribute("variant", "success");
      let query =
        "WHERE work_orders.customer_id = :customer_id ORDER BY work_orders.status ASC";
      fetchFilteredData(query);
    } else {
      let query = `WHERE work_orders.status = '${filterValue}' AND work_orders.customer_id = :customer_id ORDER BY ${orderValue} ${sortValue}`;

      console.log(`${query}`);

      fetchFilteredData(query);
      applyFilter.removeAttribute("loading");
      applyFilter.setAttribute("variant", "success");
    }
  });
  async function fetchFilteredData(query) {
    console.log("Query Sent:", query);

    try {
      const response = await fetch("workOrderHistory.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "sql_query=" + encodeURIComponent(query),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.text();
      document.querySelector(".work_orders-table").innerHTML = data;
      console.log("SUCCESS!");
    } catch (error) {
      console.error("Error fetching data:", error);
    }
  }
});
