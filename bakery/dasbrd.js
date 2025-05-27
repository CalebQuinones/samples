document.addEventListener("DOMContentLoaded", () => {
  setupNotificationSystem();
  setupCalendar();
  setupLogout();

  // Add notification triggers for recent orders table
  const orderRows = document.querySelectorAll(".recent-orders table tbody tr");
  orderRows.forEach((row) => {
    row.addEventListener("click", () => {
      const orderId = row.querySelector(".order-id").textContent;
      const customerName = row.querySelector("td:nth-child(2)").textContent;
      const status = row.querySelector(".status-badge").textContent;

      if (window.bakeryNotifications) {
        window.bakeryNotifications.addNotification(
          "Order Selected",
          `You selected ${orderId} for ${customerName} (${status})`,
        );
      }
    });

    // Make rows look clickable
    row.style.cursor = "pointer";
  });

  // Add notification triggers for inquiry items
  const inquiryItems = document.querySelectorAll(".inquiry-item");
  inquiryItems.forEach((item) => {
    item.addEventListener("click", () => {
      const title = item.querySelector(".inquiry-title").textContent;
      const customer = item.querySelector(".inquiry-customer").textContent;

      if (window.bakeryNotifications) {
        window.bakeryNotifications.addNotification("Inquiry Selected", `You selected "${title}" ${customer}`);
      }
    });

    // Make inquiry items look clickable
    item.style.cursor = "pointer";
  });

  // Add notification triggers for stats cards
  const statsCards = document.querySelectorAll(".stats-card");
  statsCards.forEach((card) => {
    card.addEventListener("click", () => {
      const label = card.querySelector(".stats-card-label").textContent;
      const value = card.querySelector(".stats-card-value").textContent;

      if (window.bakeryNotifications) {
        window.bakeryNotifications.addNotification("Dashboard Stat", `${label}: ${value}`);
      }
    });

    // Make stats cards look clickable
    card.style.cursor = "pointer";
  });

  // Profile dropdown toggle
  const profileButton = document.getElementById('profileButton');
  const profileDropdown = document.getElementById('profileDropdown');
  
  if (profileButton && profileDropdown) {
    profileButton.addEventListener('click', (e) => {
      e.stopPropagation();
      profileDropdown.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
        profileDropdown.classList.remove('show');
      }
    });
  }
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
      const response = await fetch(`api/notifications.php?since=${lastFetchTime}`)

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
      await fetch("api/notifications.php?action=seen", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
      })
    } catch (error) {
      console.error("Error marking notifications as seen:", error)
    }
  }

  // Mark a notification as read
  async function markAsRead(id) {
    try {
      const response = await fetch("api/notifications.php?action=read", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
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
      const response = await fetch("api/notifications.php?action=add", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
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

function setupLogout() {
  const logoutButton = document.getElementById('logoutButton');
  const logoutModal = document.getElementById('logoutModal');
  
  if (!logoutModal || !logoutButton) {
    console.warn('Logout modal elements not found');
    return;
  }

  const closeLogoutModal = document.getElementById('closeLogoutModal');
  const cancelLogout = document.getElementById('cancelLogout');
  const confirmLogout = document.getElementById('confirmLogout');
  const modalBox = logoutModal.querySelector('.modal');

  const showModal = () => {
    logoutModal.classList.add('active');
    if (modalBox) modalBox.classList.add('active');
    document.body.style.overflow = 'hidden';
  };

  const hideModal = () => {
    logoutModal.classList.remove('active');
    if (modalBox) modalBox.classList.remove('active');
    document.body.style.overflow = '';
  };

  // Show modal
  logoutButton.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    showModal();
  });

  // Close modal handlers
  [closeLogoutModal, cancelLogout].forEach(element => {
    if (element) {
      element.addEventListener('click', (e) => {
        e.preventDefault();
        hideModal();
      });
    }
  });

  // Confirm logout handler
  if (confirmLogout) {
    confirmLogout.addEventListener('click', async (e) => {
      e.preventDefault();
      hideModal();
      
      if (window.bakeryNotifications) {
        await window.bakeryNotifications.addNotification(
          "Logging Out",
          "You are being logged out..."
        );
      }
      
      setTimeout(() => {
        window.location.href = 'logout.php';
      }, 1000);
    });
  }

  // Close modal when clicking outside
  logoutModal.addEventListener('click', (e) => {
    if (e.target === logoutModal) {
      hideModal();
    }
  });
}

async function setupCalendar() {
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

        const contentType = response.headers.get("content-type") || ""
        if (!response.ok) {
          throw new Error('Failed to fetch order data')
        }
        if (!contentType.includes("application/json")) {
          const text = await response.text()
          // Show only the first line of the error for clarity
          const firstLine = text.split('\n')[0]
          throw new Error("Server did not return JSON. First line: " + firstLine)
        }

        const data = await response.json()
        if (!Array.isArray(data)) {
          throw new Error('Invalid data format received')
        }

        return data
      } catch (error) {
        console.error('Error fetching calendar data:', error)
        if (window.bakeryNotifications) {
          window.bakeryNotifications.addNotification(
            "Calendar Error",
            `Failed to load calendar data: ${error.message}`
          )
        }
        return []
      } finally {
        isLoading = false
        calendarGrid.classList.remove("loading")
      }
    }

    async function updateCalendar() {
      if (isLoading) return

      // Clear the grid first
      calendarGrid.innerHTML = ""

      currentMonthElement.textContent = currentDate.toLocaleDateString("en-US", { month: "long", year: "numeric" })
      
      const year = currentDate.getFullYear()
      const month = currentDate.getMonth()
      const firstDay = new Date(year, month, 1).getDay()
      const daysInMonth = new Date(year, month + 1, 0).getDate()
      const totalCells = firstDay + daysInMonth

      const orderData = await fetchOrderData()

      // Create all necessary cells in one loop
      for (let i = 0; i < totalCells; i++) {
        const cell = document.createElement("div")
        cell.className = "calendar-cell"

        if (i < firstDay) {
          // Empty cells before first day of month
          cell.classList.add("empty")
        } else {
          const day = i - firstDay + 1
          const dateHeader = document.createElement("div")
          dateHeader.className = "date-header"
          
          const dayNumber = document.createElement("span")
          dayNumber.textContent = day

          const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`
          const ordersForDay = orderData.filter(order => order.date === dateStr)
          
          if (ordersForDay.length > 0) {
            const orderCount = document.createElement("span")
            orderCount.className = "order-count"
            orderCount.textContent = ordersForDay.length
            dateHeader.appendChild(dayNumber)
            dateHeader.appendChild(orderCount)
          } else {
            dateHeader.appendChild(dayNumber)
          }

          cell.appendChild(dateHeader)

          // Add order items if any exist
          if (ordersForDay.length > 0) {
            const ordersList = document.createElement("div")
            ordersList.className = "orders-list"

            ordersForDay.forEach(order => {
              const orderItem = document.createElement("div")
              orderItem.className = `order-item ${order.status.toLowerCase()}`

              const productName = document.createElement("div")
              productName.className = "product-name"
              productName.textContent = order.products

              const customerName = document.createElement("div")
              customerName.className = "customer-name"
              customerName.textContent = order.customer

              orderItem.appendChild(productName)
              orderItem.appendChild(customerName)
              ordersList.appendChild(orderItem)
            })

            cell.appendChild(ordersList)
          }
        }

        calendarGrid.appendChild(cell)
      }
    }

    // Initialize calendar
    updateCalendar()

    // Add event listeners for navigation buttons
    if (prevMonthButton) {
      prevMonthButton.addEventListener("click", () => {
        if (isLoading) return
        currentDate.setMonth(currentDate.getMonth() - 1)
        updateCalendar()
      })
    }

    if (nextMonthButton) {
      nextMonthButton.addEventListener("click", () => {
        if (isLoading) return
        currentDate.setMonth(currentDate.getMonth() + 1)
        updateCalendar()
      })
    }
  }
}
