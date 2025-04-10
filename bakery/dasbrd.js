document.addEventListener("DOMContentLoaded", () => {
    // Notifications dropdown toggle
    const notificationsButton = document.getElementById("notificationsButton");
    const notificationsDropdown = document.getElementById("notificationsDropdown");
  
    if (notificationsButton && notificationsDropdown) {
      notificationsButton.addEventListener("click", () => {
        notificationsDropdown.classList.toggle("show");
      });
  
      // Close the dropdown when clicking outside
      window.addEventListener("click", (event) => {
        if (!event.target.matches("#notificationsButton") && !event.target.closest("#notificationsDropdown")) {
          if (notificationsDropdown.classList.contains("show")) {
            notificationsDropdown.classList.remove("show");
          }
        }
      });
    }
  
    // Calendar functionality
    const calendarGrid = document.getElementById("calendarGrid");
    const currentMonthElement = document.getElementById("currentMonth");
    const prevMonthButton = document.getElementById("prevMonth");
    const nextMonthButton = document.getElementById("nextMonth");
  
    if (calendarGrid && currentMonthElement) {
      // Sample order data
      const orderData = [
        { id: "ORD-001", customer: "Sarah Johnson", product: "Birthday Cake", date: "2025-04-08", status: "Completed" },
        { id: "ORD-002", customer: "Michael Brown", product: "Wedding Cake", date: "2025-04-09", status: "In Progress" },
        { id: "ORD-003", customer: "Emily Davis", product: "Cupcakes", date: "2025-04-09", status: "Pending" },
        { id: "ORD-004", customer: "David Wilson", product: "Bread Assortment", date: "2025-04-10", status: "Cancelled" },
        {
          id: "ORD-005",
          customer: "Jessica Martinez",
          product: "Birthday Cake",
          date: "2025-04-10",
          status: "In Progress",
        },
        { id: "ORD-006", customer: "Robert Taylor", product: "Assorted Pastries", date: "2025-04-11", status: "Pending" },
        { id: "ORD-007", customer: "Amanda Lee", product: "Baby Shower Cake", date: "2025-04-12", status: "In Progress" },
        {
          id: "ORD-008",
          customer: "Thomas Anderson",
          product: "Chocolate Chip Cookies",
          date: "2025-04-12",
          status: "Pending",
        },
        { id: "ORD-009", customer: "Jennifer Smith", product: "Wedding Cake", date: "2025-04-15", status: "Pending" },
        {
          id: "ORD-010",
          customer: "Christopher Davis",
          product: "Anniversary Cake",
          date: "2025-04-18",
          status: "Pending",
        },
        { id: "ORD-011", customer: "Lisa Brown", product: "Cupcakes", date: "2025-04-20", status: "Pending" },
        { id: "ORD-012", customer: "Kevin Wilson", product: "Birthday Cake", date: "2025-04-22", status: "Pending" },
      ];
  
      const currentDate = new Date(2025, 3, 1); // April 2025
  
      function updateCalendar() {
        // Update month display
        currentMonthElement.textContent = currentDate.toLocaleDateString("en-US", { month: "long", year: "numeric" });
  
        // Clear existing calendar
        calendarGrid.innerHTML = "";
  
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
  
        // Get first day of month and number of days
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
  
        // Add empty cells for days before the first day of the month
        for (let i = 0; i < firstDay; i++) {
          const emptyCell = document.createElement("div");
          emptyCell.className = "calendar-cell empty";
          calendarGrid.appendChild(emptyCell);
        }
  
        // Add cells for each day of the month
        for (let day = 1; day <= daysInMonth; day++) {
          const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
          const ordersForDay = orderData.filter((order) => order.date === dateStr);
  
          const cell = document.createElement("div");
          cell.className = "calendar-cell";
  
          const dateHeader = document.createElement("div");
          dateHeader.className = "calendar-date";
  
          const dateNumber = document.createElement("span");
          dateNumber.className = "calendar-date-number";
          dateNumber.textContent = day;
          dateHeader.appendChild(dateNumber);
  
          if (ordersForDay.length > 0) {
            const dateBadge = document.createElement("span");
            dateBadge.className = "calendar-date-badge";
            dateBadge.textContent = ordersForDay.length;
            dateHeader.appendChild(dateBadge);
          }
  
          cell.appendChild(dateHeader);
  
          if (ordersForDay.length > 0) {
            const eventsContainer = document.createElement("div");
            eventsContainer.className = "calendar-events";
  
            ordersForDay.forEach((order) => {
              const event = document.createElement("a");
              event.className = `calendar-event status-${order.status.toLowerCase().replace(" ", "-")}`;
              event.href = `order-details.html?id=${order.id}`;
  
              const eventTitle = document.createElement("div");
              eventTitle.className = "calendar-event-title";
              eventTitle.textContent = order.product;
  
              const eventDetails = document.createElement("div");
              eventDetails.className = "calendar-event-details";
  
              const eventCustomer = document.createElement("span");
              eventCustomer.className = "calendar-event-customer";
              eventCustomer.textContent = order.customer;
  
              eventDetails.appendChild(eventCustomer);
              event.appendChild(eventTitle);
              event.appendChild(eventDetails);
  
              eventsContainer.appendChild(event);
            });
  
            cell.appendChild(eventsContainer);
          }
  
          calendarGrid.appendChild(cell);
        }
      }
  
      // Initialize calendar
      updateCalendar();
  
      // Previous month button
      if (prevMonthButton) {
        prevMonthButton.addEventListener("click", () => {
          currentDate.setMonth(currentDate.getMonth() - 1);
          updateCalendar();
        });
      }
  
      // Next month button
      if (nextMonthButton) {
        nextMonthButton.addEventListener("click", () => {
          currentDate.setMonth(currentDate.getMonth() + 1);
          updateCalendar();
        });
      }
    }
  });
  