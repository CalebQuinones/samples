document.addEventListener("DOMContentLoaded", () => {
    // DOM elements
    const editButton = document.getElementById("editButton")
    const updateStatusButton = document.getElementById("updateStatusButton")
    const statusContainer = document.getElementById("statusContainer")
    const statusModal = document.getElementById("statusModal")
    const statusSelect = document.getElementById("status")
    const messageTextarea = document.getElementById("message")
    const updateStatusConfirm = document.getElementById("updateStatusConfirm")
    const cancelStatusUpdate = document.getElementById("cancelStatusUpdate")
  
    let isEditing = false
  
    editButton.addEventListener("click", () => {
      isEditing = !isEditing
  
      if (isEditing) {
        // Switch to editing mode
        editButton.innerHTML = '<i class="fas fa-times"></i> Cancel'
        editButton.classList.remove("edit-button-edit")
        editButton.classList.add("edit-button-cancel")
        updateStatusButton.style.display = "block"
      } else {
        // Switch back to view mode
        editButton.innerHTML = '<i class="fas fa-edit"></i> Edit Order'
        editButton.classList.remove("edit-button-cancel")
        editButton.classList.add("edit-button-edit")
        updateStatusButton.style.display = "none"
      }
    })
  
    updateStatusButton.addEventListener("click", () => {
      statusModal.style.display = "flex"
    })
  
    cancelStatusUpdate.addEventListener("click", () => {
      statusModal.style.display = "none"
    })
  
    updateStatusConfirm.addEventListener("click", () => {
      const newStatus = statusSelect.value
      const message = messageTextarea.value
  
      // Update the status badge
      statusContainer.innerHTML = ""
      const statusBadge = document.createElement("span")
      statusBadge.className = `status-badge status-${newStatus.toLowerCase().replace(" ", "-")}`
      statusBadge.textContent = newStatus
      statusContainer.appendChild(statusBadge)
  
      // Close the modal
      statusModal.style.display = "none"
  
      alert(`Order status updated to: ${newStatus}${message ? "\nMessage: " + message : ""}`)
    })
  
    // Close modal when clicking outside
    window.addEventListener("click", (event) => {
      if (event.target === statusModal) {
        statusModal.style.display = "none"
      }
    })
  
    function getOrderIdFromUrl() {
      const urlParams = new URLSearchParams(window.location.search)
      return urlParams.get("id")
    }
  
    // For demonstration purposes
    console.log("Order ID:", getOrderIdFromUrl() || "ORD-002")
  })
  