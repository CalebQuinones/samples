document.addEventListener("DOMContentLoaded", () => {
  // Notifications system
  setupNotificationSystem()

  // Calendar functionality
  setupCalendar()
})

function setupNotificationSystem() {
  // Sample notification data
  const initialNotifications = [
    {
      id: 1,
      title: "New Order",
      message: "Sarah Johnson placed a new order for a Birthday Cake",
      time: "10 minutes ago",
      read: false,
    },
    {
      id: 2,
      title: "Order Status Update",
      message: "Order #ORD-002 has been updated to 'In Progress'",
      time: "1 hour ago",
      read: false,
    },
    {
      id: 3,
      title: "New Inquiry",
      message: "Jennifer Smith sent an inquiry about a Wedding Cake",
      time: "2 hours ago",
      read: false,
    },
  ]

  let notifications = [...initialNotifications]
  let nextNotificationId = 4

  // DOM elements
  const notificationsButton = document.getElementById("notificationsButton")
  const notificationsDropdown = document.getElementById("notificationsDropdown")
  const notificationsList = notificationsDropdown?.querySelector(".notifications-list")
  const notificationBadge = notificationsButton?.querySelector(".notifications-badge")
  const markAllAsReadButton = notificationsDropdown?.querySelector(".notifications-footer button")

  // Create side popup container if it doesn't exist
  let sidePopupContainer = document.getElementById("sidePopupContainer")
  if (!sidePopupContainer) {
    sidePopupContainer = document.createElement("div")
    sidePopupContainer.id = "sidePopupContainer"
    sidePopupContainer.style.position = "fixed"
    sidePopupContainer.style.top = "5rem"
    sidePopupContainer.style.right = "1rem"
    sidePopupContainer.style.zIndex = "100"
    sidePopupContainer.style.display = "flex"
    sidePopupContainer.style.flexDirection = "column"
    sidePopupContainer.style.gap = "1rem"
    document.body.appendChild(sidePopupContainer)
  }

  // Toggle notifications dropdown
  if (notificationsButton && notificationsDropdown) {
    notificationsButton.addEventListener("click", (event) => {
      event.stopPropagation()
      notificationsDropdown.classList.toggle("show")
    })

    // Close the dropdown when clicking outside
    window.addEventListener("click", (event) => {
      if (!event.target.matches("#notificationsButton") && !event.target.closest("#notificationsDropdown")) {
        if (notificationsDropdown.classList.contains("show")) {
          notificationsDropdown.classList.remove("show")
        }
      }
    })
  }

  // Mark all as read
  if (markAllAsReadButton) {
    markAllAsReadButton.addEventListener("click", () => {
      notifications = notifications.map((notification) => ({
        ...notification,
        read: true,
      }))
      updateNotificationsList()
      updateNotificationBadge()
    })
  }

  // Update notifications list in dropdown
  function updateNotificationsList() {
    if (!notificationsList) return

    notificationsList.innerHTML = ""

    if (notifications.length === 0) {
      const emptyItem = document.createElement("div")
      emptyItem.className = "notification-empty"
      emptyItem.innerHTML = "<p>No notifications</p>"
      notificationsList.appendChild(emptyItem)
      return
    }

    notifications.forEach((notification) => {
      const notificationItem = document.createElement("div")
      notificationItem.className = `notification-item ${notification.read ? "read" : ""}`

      notificationItem.innerHTML = `
        <p class="notification-title">${notification.title}</p>
        <p class="notification-message">${notification.message}</p>
        <p class="notification-time">${notification.time}</p>
      `

      // Add click event to mark as read when clicked
      notificationItem.addEventListener("click", () => {
        const index = notifications.findIndex((n) => n.id === notification.id)
        if (index !== -1) {
          notifications[index].read = true
          updateNotificationsList()
          updateNotificationBadge()
        }
      })

      notificationsList.appendChild(notificationItem)
    })
  }

  // Update notification badge count
  function updateNotificationBadge() {
    if (!notificationBadge) return

    const unreadCount = notifications.filter((notification) => !notification.read).length

    if (unreadCount > 0) {
      notificationBadge.textContent = unreadCount
      notificationBadge.style.display = "flex"
    } else {
      notificationBadge.style.display = "none"
    }
  }

  // Show side popup notification
  function showSidePopup(notification) {
    const popup = document.createElement("div")
    popup.className = "notification-side-popup"
    popup.innerHTML = `
      <div class="notification-side-popup-header">
        <h4>${notification.title}</h4>
        <button class="notification-close-btn">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="notification-side-popup-body">
        <p>${notification.message}</p>
        <p class="notification-time">${notification.time}</p>
      </div>
    `

    // Add close button functionality
    const closeButton = popup.querySelector(".notification-close-btn")
    closeButton.addEventListener("click", () => {
      popup.classList.add("fade-out")
      setTimeout(() => popup.remove(), 300)
    })

    // Add to container
    sidePopupContainer.appendChild(popup)

    // Auto remove after 5 seconds
    setTimeout(() => {
      if (popup && popup.parentNode) {
        popup.classList.add("fade-out")
        setTimeout(() => popup.remove(), 300)
      }
    }, 5000)
  }

  // Add a new notification
  function addNotification(title, message) {
    const newNotification = {
      id: nextNotificationId++,
      title,
      message,
      time: "Just now",
      read: false,
    }

    notifications.unshift(newNotification)
    updateNotificationsList()
    updateNotificationBadge()
    showSidePopup(newNotification)

    return newNotification
  }

  // Initialize
  updateNotificationsList()
  updateNotificationBadge()

  // Simulate new notifications coming in
  setTimeout(() => {
    addNotification("New Order", "Michael Brown placed a new order for a Wedding Cake")
  }, 10000)

  setTimeout(() => {
    addNotification("Low Stock Alert", "Chocolate frosting is running low on stock")
  }, 25000)

  setTimeout(() => {
    addNotification("Order Completed", "Order #ORD-001 has been marked as completed")
  }, 40000)

  // Make functions available globally
  window.bakeryNotifications = {
    addNotification,
  }
}

function setupCalendar() {
  // Calendar functionality
  const calendarGrid = document.getElementById("calendarGrid")
  const currentMonthElement = document.getElementById("currentMonth")
  const prevMonthButton = document.getElementById("prevMonth")
  const nextMonthButton = document.getElementById("nextMonth")

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
    ]

    const currentDate = new Date(2025, 3, 1) // April 2025

    function updateCalendar() {
      // Update month display
      currentMonthElement.textContent = currentDate.toLocaleDateString("en-US", { month: "long", year: "numeric" })

      // Clear existing calendar
      calendarGrid.innerHTML = ""

      const year = currentDate.getFullYear()
      const month = currentDate.getMonth()

      // Get first day of month and number of days
      const firstDay = new Date(year, month, 1).getDay()
      const daysInMonth = new Date(year, month + 1, 0).getDate()

      // Add empty cells for days before the first day of the month
      for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement("div")
        emptyCell.className = "calendar-cell empty"
        calendarGrid.appendChild(emptyCell)
      }

      // Add cells for each day of the month
      for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`
        const ordersForDay = orderData.filter((order) => order.date === dateStr)

        const cell = document.createElement("div")
        cell.className = "calendar-cell"

        const dateHeader = document.createElement("div")
        dateHeader.className = "calendar-date"

        const dateNumber = document.createElement("span")
        dateNumber.className = "calendar-date-number"
        dateNumber.textContent = day
        dateHeader.appendChild(dateNumber)

        if (ordersForDay.length > 0) {
          const dateBadge = document.createElement("span")
          dateBadge.className = "calendar-date-badge"
          dateBadge.textContent = ordersForDay.length
          dateHeader.appendChild(dateBadge)
        }

        cell.appendChild(dateHeader)

        if (ordersForDay.length > 0) {
          const eventsContainer = document.createElement("div")
          eventsContainer.className = "calendar-events"

          ordersForDay.forEach((order) => {
            const event = document.createElement("a")
            event.className = `calendar-event status-${order.status.toLowerCase().replace(" ", "-")}`
            event.href = `order-details.html?id=${order.id}`

            const eventTitle = document.createElement("div")
            eventTitle.className = "calendar-event-title"
            eventTitle.textContent = order.product

            const eventDetails = document.createElement("div")
            eventDetails.className = "calendar-event-details"

            const eventCustomer = document.createElement("span")
            eventCustomer.className = "calendar-event-customer"
            eventCustomer.textContent = order.customer

            eventDetails.appendChild(eventCustomer)
            event.appendChild(eventTitle)
            event.appendChild(eventDetails)

            // Add click event to show notification when clicking on calendar event
            event.addEventListener("click", (e) => {
              e.preventDefault()
              if (window.bakeryNotifications) {
                window.bakeryNotifications.addNotification(
                  "Order Details",
                  `Viewing details for ${order.id}: ${order.product} for ${order.customer}`,
                )
              }
              // You could also navigate to the order details page
              // window.location.href = event.href;
            })

            eventsContainer.appendChild(event)
          })

          cell.appendChild(eventsContainer)
        }

        calendarGrid.appendChild(cell)
      }
    }

    // Initialize calendar
    updateCalendar()

    // Previous month button
    if (prevMonthButton) {
      prevMonthButton.addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() - 1)
        updateCalendar()

        // Show notification when changing month
        if (window.bakeryNotifications) {
          window.bakeryNotifications.addNotification(
            "Calendar Updated",
            `Viewing calendar for ${currentDate.toLocaleDateString("en-US", { month: "long", year: "numeric" })}`,
          )
        }
      })
    }

    // Next month button
    if (nextMonthButton) {
      nextMonthButton.addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() + 1)
        updateCalendar()

        // Show notification when changing month
        if (window.bakeryNotifications) {
          window.bakeryNotifications.addNotification(
            "Calendar Updated",
            `Viewing calendar for ${currentDate.toLocaleDateString("en-US", { month: "long", year: "numeric" })}`,
          )
        }
      })
    }
  }
}

// Add event listeners for table rows to trigger notifications
document.addEventListener("DOMContentLoaded", () => {
  // Add notification triggers for recent orders table
  const orderRows = document.querySelectorAll(".recent-orders table tbody tr")
  orderRows.forEach((row) => {
    row.addEventListener("click", () => {
      const orderId = row.querySelector(".order-id").textContent
      const customerName = row.querySelector("td:nth-child(2)").textContent
      const status = row.querySelector(".status-badge").textContent

      if (window.bakeryNotifications) {
        window.bakeryNotifications.addNotification(
          "Order Selected",
          `You selected ${orderId} for ${customerName} (${status})`,
        )
      }
    })

    // Make rows look clickable
    row.style.cursor = "pointer"
  })

  // Add notification triggers for inquiry items
  const inquiryItems = document.querySelectorAll(".inquiry-item")
  inquiryItems.forEach((item) => {
    item.addEventListener("click", () => {
      const title = item.querySelector(".inquiry-title").textContent
      const customer = item.querySelector(".inquiry-customer").textContent

      if (window.bakeryNotifications) {
        window.bakeryNotifications.addNotification("Inquiry Selected", `You selected "${title}" ${customer}`)
      }
    })

    // Make inquiry items look clickable
    item.style.cursor = "pointer"
  })

  // Add notification triggers for stats cards
  const statsCards = document.querySelectorAll(".stats-card")
  statsCards.forEach((card) => {
    card.addEventListener("click", () => {
      const label = card.querySelector(".stats-card-label").textContent
      const value = card.querySelector(".stats-card-value").textContent

      if (window.bakeryNotifications) {
        window.bakeryNotifications.addNotification("Dashboard Stat", `${label}: ${value}`)
      }
    })

    // Make stats cards look clickable
    card.style.cursor = "pointer"
  })
})

// Function to simulate real-time events
function simulateRealTimeEvents() {
  // Array of possible events
  const events = [
    { title: "New Order", message: "Alex Thompson placed a new order for a Chocolate Cake" },
    { title: "Order Status Update", message: "Order #ORD-007 has been updated to 'Completed'" },
    { title: "Low Stock Alert", message: "Vanilla extract is running low on stock" },
    { title: "New Inquiry", message: "Daniel Lee sent an inquiry about custom cupcakes" },
    { title: "Payment Received", message: "Payment received for Order #ORD-009" },
    { title: "Delivery Scheduled", message: "Delivery for Order #ORD-005 scheduled for tomorrow" },
  ]

  // Generate a random event every 30-60 seconds
  function scheduleRandomEvent() {
    const randomTime = Math.floor(Math.random() * 30000) + 30000 // 30-60 seconds
    setTimeout(() => {
      // Pick a random event
      const randomEvent = events[Math.floor(Math.random() * events.length)]

      // Trigger notification
      if (window.bakeryNotifications) {
        window.bakeryNotifications.addNotification(randomEvent.title, randomEvent.message)
      }

      // Schedule next event
      scheduleRandomEvent()
    }, randomTime)
  }

  // Start the simulation
  scheduleRandomEvent()
}

// Start simulating real-time events after a short delay
setTimeout(simulateRealTimeEvents, 15000)
