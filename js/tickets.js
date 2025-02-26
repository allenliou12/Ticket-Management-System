let currentPage = 1;
let totalPages = 1;
let updatedTickets = [];
let newResolvedTickets = []; // Tracks newly changed resolved tickets

function loadTickets() {
  let searchQuery = $("#search").val(); // Get search input value

  $.ajax({
    url: "fetch_tickets.php", // Calls the backend PHP script to get ticket data
    method: "GET",
    data: {
      page: currentPage,
      search: searchQuery, // Passes the search term to the backend
    },
    success: function (data) {
      let response = JSON.parse(data); // Convert JSON string to JavaScript object
      $("#ticketTable").html(response.html); // Insert ticket data into the table
      totalPages = response.total_pages; // Update total pages
      $("#pageNumber").text(`Page ${currentPage} of ${totalPages}`); // Display current page number

      // Update ticket count
      $("#ticketCount").text(response.total_records);

      // Ensure "Assigned To" remains editable unless "Resolved"
      $(".status").each(function () {
        let row = $(this).closest("tr");
        let ticketNo = row.find(".ticket_no").text();
        let status = $(this).val();

        if (status === "Resolved") {
          $(this).prop("disabled", true);
          row.find(".assigned_to").prop("disabled", true); // Disable only if Resolved
        } else {
          $(this).prop("disabled", false);
          row.find(".assigned_to").prop("disabled", false); // Ensure it's editable for non-Resolved tickets
        }
      });
    },
  });
}

//Server-Sent Events (SSE) for real-time updates
function startSSE() {
  if (!!window.EventSource) {
    let source = new EventSource("realtime_updates.php");

    source.onmessage = function (event) {
      console.log("Received update:", event.data);
      let data = JSON.parse(event.data);
      $("#ticketCount").text(data.total_records);
      loadTickets(); // Refresh table dynamically
    };

    source.onerror = function () {
      console.warn("SSE connection lost, trying to reconnect...");
      source.close();
      setTimeout(startSSE, 3000); // Try reconnecting in 3 seconds
    };
  } else {
    console.error("Your browser does not support SSE.");
  }
}

$(document).ready(function () {
  loadTickets(); // Load tickets when the page loads
  startSSE(); // Start real-time updates

  // Refresh table
  $("#refreshTable").on("click", function () {
    loadTickets();
  });

  $("#search").on("keyup", function () {
    currentPage = 1; // Reset to the first page
    loadTickets(); // Load tickets that match the search query
  });

  $("#prevPage").on("click", function () {
    if (currentPage > 1) {
      currentPage--;
      loadTickets();
    }
  });

  $("#nextPage").on("click", function () {
    if (currentPage < totalPages) {
      currentPage++;
      loadTickets();
    }
  });

  $(document).on("change", ".status, .assigned_to", function () {
    let row = $(this).closest("tr");
    let ticketNo = row.find(".ticket_no").text();
    let status = row.find(".status").val();
    let assignedTo = row.find(".assigned_to").val() || "Unassigned"; // Ensure a default value
    let dateResolvedCell = row.find(".date_resolved");

    // Get original values from `data-original`
    let originalStatus = row.find(".status").attr("data-original") || "";
    let originalAssigned = row.find(".assigned_to").attr("data-original") || "Unassigned";

    console.log(`Ticket: ${ticketNo}, Status: ${status}, Assigned To: ${assignedTo}`);
    console.log(`Original Status: ${originalStatus}, Original Assigned: ${originalAssigned}`);

    if (status === "Resolved") {
      // Show timestamp if changed to "Resolved"
      let now = new Date();
      let malaysiaTime = new Intl.DateTimeFormat("en-GB", {
        timeZone: "Asia/Kuala_Lumpur",
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
      }).format(now);

      let parts = malaysiaTime.match(/(\d{2})\/(\d{2})\/(\d{4}), (\d{2}):(\d{2}):(\d{2})/);
      if (parts) {
        let formattedTime = `${parts[3]}-${parts[2]}-${parts[1]} ${parts[4]}:${parts[5]}:${parts[6]}`;
        dateResolvedCell.text(formattedTime);
      }

      if (!newResolvedTickets.includes(ticketNo)) {
        newResolvedTickets.push(ticketNo);
      }

      // Lock fields only if status is Resolved
      row.find(".assigned_to").prop("disabled", true);
      row.find(".status").prop("disabled", true);
    } else {
      // Ensure "Assigned To" remains editable
      row.find(".assigned_to").prop("disabled", false);
      row.find(".status").prop("disabled", false);
      
      // Clear timestamp if changed back to "Ongoing"
      dateResolvedCell.text("-");
      newResolvedTickets = newResolvedTickets.filter(ticket => ticket !== ticketNo);
    }

    // ✅ Only save updates if they are different from the original values
    if (status !== originalStatus || assignedTo !== originalAssigned) {
      updatedTickets = updatedTickets.filter(t => t.ticket_no !== ticketNo);
      updatedTickets.push({
        ticket_no: ticketNo,
        status: status,
        assigned_to: assignedTo
      });
      console.log("Updated tickets:", updatedTickets);
    } else {
      // ✅ Remove from updates if no real change
      updatedTickets = updatedTickets.filter(t => t.ticket_no !== ticketNo);
    }
  });

  //Refresh button
  $("#refreshTable").on("click", function () {
    loadTickets();
    $("#refreshModal").modal("show");
    setTimeout(function () {
      $("#refreshModal").modal("hide");
    }, 1000); // Modal auto-closes after 1 second
  });

  //Apply changes button
  $("#applyChanges").on("click", function () {
    if (updatedTickets.length === 0) {
      $("#noChangesModal").modal("show");
      return;
    }
    //Sends modified tickets to update_tickets.php if there are changes
    $.ajax({
      url: "update_tickets.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({
        tickets: updatedTickets,
      }),
      success: function (response) {
        let result = JSON.parse(response);
        //Shows the success confirmation box if successful
        if (result.success) {
          $("#successModal .modal-body").text("Changes applied successfully!");
          $("#successModal").modal("show");
        } else {
          //Shows error modal if any error
          $("#errorModal .modal-body").text(
            "Failed to apply changes: " + result.error
          );
          $("#errorModal").modal("show");
        }

        updatedTickets = [];
        newResolvedTickets = [];
        loadTickets();
      },

      error: function () {
        alert("Failed to update tickets.");
      },
    });
  });
});
