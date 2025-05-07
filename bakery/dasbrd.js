document.addEventListener("DOMContentLoaded", () => {
  // Notifications system
  setupNotificationSystem()

  // Calendar functionality
  setupCalendar()
})

function setupNotificationSystem() {
  let notifications = []
  let nextNotificationId = 1
  let lastFetchTime = Date.now()

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

      // When opening the dropdown, mark notifications as seen on the server
      if (notificationsDropdown.classList.contains("show")) {
        markNotificationsAsSeen()
      }
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
      markAllAsRead()
    })
  }

  // Fetch notifications from database
  async function fetchNotifications() {
    try {
      // Replace with your actual API endpoint
      const response = await fetch(`/api/notifications?since=${lastFetchTime}`)

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`)
      }

      const data = await response.json()

      if (data.notifications && data.notifications.length > 0) {
        // Process new notifications
        data.notifications.forEach((newNotif) => {
          // Check if notification already exists
          const exists = notifications.some((existingNotif) => existingNotif.id === newNotif.id)
          if (!exists) {
            notifications.unshift(newNotif)
            // Show popup for new notification
            showSidePopup(newNotif)
          }
        })

        // Update UI
        updateNotificationsList()
        updateNotificationBadge()
      }

      // Update last fetch time
      lastFetchTime = Date.now()
    } catch (error) {
      console.error("Error fetching notifications:", error)
    }
  }

  // Mark notifications as seen (viewed but not necessarily read)
  async function markNotificationsAsSeen() {
    try {
      // Replace with your actual API endpoint
      await fetch("/api/notifications/seen", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
      })
    } catch (error) {
      console.error("Error marking notifications as seen:", error)
    }
  }

  // Mark a notification as read
  async function markAsRead(id) {
    try {
      // Replace with your actual API endpoint
      const response = await fetch("/api/notifications/read", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id }),
      })

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`)
      }

      // Update local state
      const index = notifications.findIndex((n) => n.id === id)
      if (index !== -1) {
        notifications[index].read = true
        updateNotificationsList()
        updateNotificationBadge()
      }
    } catch (error) {
      console.error("Error marking notification as read:", error)
    }
  }

  // Mark all notifications as read
  async function markAllAsRead() {
    try {
      // Replace with your actual API endpoint
      const response = await fetch("/api/notifications/read-all", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
      })

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`)
      }

      // Update local state
      notifications = notifications.map((notification) => ({
        ...notification,
        read: true,
      }))
      updateNotificationsList()
      updateNotificationBadge()
    } catch (error) {
      console.error("Error marking all notifications as read:", error)
    }
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
        markAsRead(notification.id)
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

    // Add to container and handle multiple popups
    const existingPopups = sidePopupContainer.querySelectorAll(".notification-side-popup")
    if (existingPopups.length >= 3) {
      // Remove the oldest popup if we have more than 3
      const oldestPopup = existingPopups[existingPopups.length - 1]
      oldestPopup.classList.add("fade-out")
      setTimeout(() => oldestPopup.remove(), 300)
    }

    // Add the new popup
    sidePopupContainer.insertBefore(popup, sidePopupContainer.firstChild)

    // Auto remove after 5 seconds
    setTimeout(() => {
      if (popup && popup.parentNode) {
        popup.classList.add("fade-out")
        setTimeout(() => popup.remove(), 300)
      }
    }, 5000)
  }

  // Add a new notification (client-side function that also sends to server)
  async function addNotification(title, message) {
    try {
      // Send notification to server
      const response = await fetch("/api/notifications/add", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ title, message }),
      })

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`)
      }

      // Get the created notification from response
      const newNotification = await response.json()

      // Add to local state
      notifications.unshift(newNotification)
      updateNotificationsList()
      updateNotificationBadge()
      showSidePopup(newNotification)

      return newNotification
    } catch (error) {
      console.error("Error adding notification:", error)

      // Fallback to local-only notification if server request fails
      const fallbackNotification = {
        id: nextNotificationId++,
        title,
        message,
        time: "Just now",
        read: false,
      }

      notifications.unshift(fallbackNotification)
      updateNotificationsList()
      updateNotificationBadge()
      showSidePopup(fallbackNotification)

      return fallbackNotification
    }
  }

  // Initialize - fetch notifications from server
  fetchNotifications()

  // Set up polling to check for new notifications every 30 seconds
  setInterval(fetchNotifications, 30000)

  // Make functions available globally
  window.bakeryNotifications = {
    addNotification,
    fetchNotifications,
  }
}

async function setupCalendar() {
  // Calendar functionality
  const calendarGrid = document.getElementById("calendarGrid")
  const currentMonthElement = document.getElementById("currentMonth")
  const prevMonthButton = document.getElementById("prevMonth")
  const nextMonthButton = document.getElementById("nextMonth")

  if (calendarGrid && currentMonthElement) {
    const currentDate = new Date()
    let isLoading = false

    async function fetchOrderData() {
      try {
        isLoading = true
        calendarGrid.classList.add("loading")
        
        const response = await fetch(`calendar.php?month=${currentDate.getMonth() + 1}&year=${currentDate.getFullYear()}`)
        if (!response.ok) {
          throw new Error('Failed to fetch order data')
        }
        return await response.json()
      } catch (error) {
        console.error('Error fetching calendar data:', error)
        if (window.bakeryNotifications) {
          window.bakeryNotifications.addNotification(
            "Calendar Error",
            "Failed to load calendar data. Please try again."
          )
        }
        return []
      } finally {
        isLoading = false
        calendarGrid.classList.remove("loading")
      }
    }

    let orderData = await fetchOrderData()

    async function updateCalendar() {
      if (isLoading) return

      // Update month display
      currentMonthElement.textContent = currentDate.toLocaleDateString("en-US", { month: "long", year: "numeric" })

      // Clear existing calendar
      calendarGrid.innerHTML = ""

      const year = currentDate.getFullYear()
      const month = currentDate.getMonth()

      // Get first day of month and number of days
      const firstDay = new Date(year, month, 1).getDay()
      const daysInMonth = new Date(year, month + 1, 0).getDate()

      // Fetch new order data for the current month
      orderData = await fetchOrderData()

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
            event.href = `order-details.php?id=${order.id}`

            const eventTitle = document.createElement("div")
            eventTitle.className = "calendar-event-title"
            eventTitle.textContent = order.products

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
              if (window.bakeryNotifications) {
                window.bakeryNotifications.addNotification(
                  "Order Details",
                  `Viewing details for ${order.id}: ${order.products} for ${order.customer}`,
                )
              }
              // Navigate to the order details page after a short delay
              setTimeout(() => {
                window.location.href = event.href
              }, 300)
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
        if (isLoading) return
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
        if (isLoading) return
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

// Function to initialize database connection check
function initDatabaseConnectionCheck() {
  // Check database connection on page load
  checkDatabaseConnection()

  // Check connection every 2 minutes
  setInterval(checkDatabaseConnection, 120000)
}

// Function to check database connection
async function checkDatabaseConnection() {
  try {
    // Replace with your actual API endpoint
    const response = await fetch("/api/database/status")

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`)
    }

    const data = await response.json()

    if (data.connected) {
      console.log("Database connection: OK")

      // If we were previously disconnected, show reconnection notification
      if (window.wasDisconnected) {
        if (window.bakeryNotifications) {
          window.bakeryNotifications.addNotification(
            "Database Connected",
            "Connection to the database has been restored. Your data is now up-to-date.",
          )
        }
        window.wasDisconnected = false

        // Refresh notifications to get any we missed while disconnected
        if (window.bakeryNotifications) {
          window.bakeryNotifications.fetchNotifications()
        }
      }
    } else {
      console.error("Database connection: Failed")
      window.wasDisconnected = true

      if (window.bakeryNotifications) {
        window.bakeryNotifications.addNotification(
          "Database Disconnected",
          "Connection to the database has been lost. Some features may be unavailable.",
        )
      }
    }
  } catch (error) {
    console.error("Error checking database connection:", error)
    window.wasDisconnected = true

    if (window.bakeryNotifications) {
      window.bakeryNotifications.addNotification(
        "Database Error",
        "Unable to connect to the database. Please check your connection.",
      )
    }
  }
}

// Start database connection check
document.addEventListener("DOMContentLoaded", initDatabaseConnectionCheck)
