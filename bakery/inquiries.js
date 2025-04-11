document.addEventListener("DOMContentLoaded", () => {
    // Sample inquiries data
    const inquiriesData = [
      {
        id: 1,
        subject: "Wedding Cake Inquiry",
        customer: "Jennifer Smith",
        email: "jennifer.smith@example.com",
        phone: "+1 (555) 123-4567",
        date: "Apr 9, 2025",
        status: "New",
        message:
          "I'm interested in ordering a 3-tier wedding cake for my upcoming wedding on June 15th. Do you offer tastings? I'd like to discuss design options and flavors. My fiancé and I are thinking of a semi-naked cake with fresh flowers. Please let me know what information you need from me to get started.",
      },
      {
        id: 2,
        subject: "Custom Birthday Cake",
        customer: "Robert Johnson",
        email: "robert.johnson@example.com",
        phone: "+1 (555) 234-5678",
        date: "Apr 9, 2025",
        status: "In Progress",
        message:
          "Do you offer superhero-themed cakes? My son is turning 8 and loves Spider-Man. I'd like to order a cake that looks like Spider-Man or has Spider-Man decorations. The party is on April 20th, and we'll have about 15 kids attending. Please let me know if this is possible and what sizes you recommend.",
      },
      {
        id: 3,
        subject: "Corporate Order",
        customer: "Sarah Williams (Acme Corp)",
        email: "sarah.williams@acmecorp.com",
        phone: "+1 (555) 345-6789",
        date: "Apr 8, 2025",
        status: "In Progress",
        message:
          "We're hosting a company event next month and would like to order assorted pastries for approximately 50 people. The event is on May 15th from 9 AM to 12 PM. We're interested in a mix of croissants, muffins, and Danish pastries. Do you offer delivery services for corporate orders? Please provide information about your corporate catering options.",
      },
      {
        id: 4,
        subject: "Gluten-Free Options",
        customer: "Michael Davis",
        email: "michael.davis@example.com",
        phone: "+1 (555) 456-7890",
        date: "Apr 7, 2025",
        status: "Resolved",
        message:
          "Do you offer any gluten-free cake options? I have celiac disease but would love to order a birthday cake for my wife's upcoming birthday on April 30th. Are there any flavors that you can make gluten-free? Also, do you have a dedicated area for preparing gluten-free items to prevent cross-contamination?",
      },
      {
        id: 5,
        subject: "Delivery Question",
        customer: "Lisa Thompson",
        email: "lisa.thompson@example.com",
        phone: "+1 (555) 567-8901",
        date: "Apr 6, 2025",
        status: "Resolved",
        message:
          "What's your delivery radius? I live about 30 miles outside the city and wanted to check if you deliver to my area. I'm planning to order a cake for my daughter's graduation party on May 25th. If you don't deliver to my location, do you offer any pickup options? Thank you for your help!",
      },
      {
        id: 6,
        subject: "Vegan Cake Options",
        customer: "Daniel Wilson",
        email: "daniel.wilson@example.com",
        phone: "+1 (555) 678-9012",
        date: "Apr 5, 2025",
        status: "New",
        message:
          "I'm looking for vegan cake options for my anniversary. Do you offer any vegan cakes? If so, what flavors are available? I'm particularly interested in chocolate or vanilla options. The cake would be for 2 people, so I don't need anything large. Our anniversary is on April 18th.",
      },
      {
        id: 7,
        subject: "Custom Cupcake Order",
        customer: "Amanda Brown",
        email: "amanda.brown@example.com",
        phone: "+1 (555) 789-0123",
        date: "Apr 4, 2025",
        status: "In Progress",
        message:
          "I'd like to order custom cupcakes for a baby shower. We're expecting about 20 guests. I'm interested in cupcakes with baby-themed decorations in pink and white colors. The shower is on April 22nd. Do you offer custom decorations for cupcakes? What flavors do you recommend for a baby shower?",
      },
      {
        id: 8,
        subject: "Wholesale Pricing",
        customer: "James Miller (Cafe Delight)",
        email: "james.miller@cafedelight.com",
        phone: "+1 (555) 890-1234",
        date: "Apr 3, 2025",
        status: "New",
        message:
          "I own a small cafe and I'm interested in your wholesale pricing for pastries. We'd like to offer your baked goods in our cafe. We're particularly interested in croissants, muffins, and cookies. Could you please provide information about your wholesale program, minimum order quantities, and delivery options?",
      },
    ]
  
    // DOM elements
    const inquiriesTableBody = document.getElementById("inquiriesTableBody")
    const searchInput = document.getElementById("searchInput")
    const statusFilter = document.getElementById("statusFilter")
    const dateFilter = document.getElementById("dateFilter")
    const selectAll = document.getElementById("selectAll")
    const bulkActions = document.getElementById("bulkActions")
    const selectedCount = document.getElementById("selectedCount")
    const clearSelection = document.getElementById("clearSelection")
    const prevPage = document.getElementById("prevPage")
    const nextPage = document.getElementById("nextPage")
    const prevPageMobile = document.getElementById("prevPageMobile")
    const nextPageMobile = document.getElementById("nextPageMobile")
    const paginationNav = document.getElementById("paginationNav")
    const startIndex = document.getElementById("startIndex")
    const endIndex = document.getElementById("endIndex")
    const totalItems = document.getElementById("totalItems")
  
    // Modal elements
    const inquiryModal = document.getElementById("inquiryModal")
    const inquirySubject = document.getElementById("inquirySubject")
    const inquiryStatusBadge = document.getElementById("inquiryStatusBadge")
    const inquiryCustomer = document.getElementById("inquiryCustomer")
    const inquiryDate = document.getElementById("inquiryDate")
    const inquiryMessage = document.getElementById("inquiryMessage")
    const inquiryEmail = document.getElementById("inquiryEmail")
    const inquiryPhone = document.getElementById("inquiryPhone")
    const replyText = document.getElementById("replyText")
    const sendReply = document.getElementById("sendReply")
    const closeInquiry = document.getElementById("closeInquiry")
  
    // Pagination state
    let currentPage = 1
    const itemsPerPage = 5
    let filteredInquiries = [...inquiriesData]
    let selectedInquiries = []
    let currentInquiry = null
  
    // Initialize
    updateTable()
  
    // Event listeners
    searchInput.addEventListener("input", () => {
      currentPage = 1
      updateTable()
    })
  
    statusFilter.addEventListener("change", () => {
      currentPage = 1
      updateTable()
    })
  
    dateFilter.addEventListener("change", () => {
      currentPage = 1
      updateTable()
    })
  
    selectAll.addEventListener("change", () => {
      const checkboxes = document.querySelectorAll(".inquiry-checkbox")
      checkboxes.forEach((checkbox) => {
        checkbox.checked = selectAll.checked
        const inquiryId = Number.parseInt(checkbox.getAttribute("data-id"))
        if (selectAll.checked) {
          if (!selectedInquiries.includes(inquiryId)) {
            selectedInquiries.push(inquiryId)
          }
        } else {
          selectedInquiries = selectedInquiries.filter((id) => id !== inquiryId)
        }
      })
      updateBulkActions()
    })
  
    clearSelection.addEventListener("click", () => {
      selectedInquiries = []
      selectAll.checked = false
      const checkboxes = document.querySelectorAll(".inquiry-checkbox")
      checkboxes.forEach((checkbox) => {
        checkbox.checked = false
      })
      updateBulkActions()
    })
  
    prevPage.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--
        updateTable()
      }
    })
  
    nextPage.addEventListener("click", () => {
      const totalPages = Math.ceil(filteredInquiries.length / itemsPerPage)
      if (currentPage < totalPages) {
        currentPage++
        updateTable()
      }
    })
  
    prevPageMobile.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--
        updateTable()
      }
    })
  
    nextPageMobile.addEventListener("click", () => {
      const totalPages = Math.ceil(filteredInquiries.length / itemsPerPage)
      if (currentPage < totalPages) {
        currentPage++
        updateTable()
      }
    })
  
    closeInquiry.addEventListener("click", () => {
      inquiryModal.style.display = "none"
      replyText.value = ""
    })
  
    sendReply.addEventListener("click", () => {
      if (replyText.value.trim() === "") {
        alert("Please enter a reply message.")
        return
      }
  
      // In a real app, this would send the reply to the server
      alert(`Reply sent to ${currentInquiry.customer}:\n\n${replyText.value}`)
  
      inquiryModal.style.display = "none"
      replyText.value = ""
    })
  
    // Close modal when clicking outside
    window.addEventListener("click", (event) => {
      if (event.target === inquiryModal) {
        inquiryModal.style.display = "none"
      }
    })
  
    // Functions
    function updateTable() {
      // Filter inquiries
      filteredInquiries = inquiriesData.filter((inquiry) => {
        const searchTerm = searchInput.value.toLowerCase()
        const matchesSearch =
          inquiry.subject.toLowerCase().includes(searchTerm) ||
          inquiry.customer.toLowerCase().includes(searchTerm) ||
          inquiry.message.toLowerCase().includes(searchTerm)
  
        const matchesStatus = statusFilter.value === "All Status" || inquiry.status === statusFilter.value
  
        return matchesSearch && matchesStatus
      })
  
      // Update pagination info
      const totalPages = Math.ceil(filteredInquiries.length / itemsPerPage)
      const start = (currentPage - 1) * itemsPerPage + 1
      const end = Math.min(start + itemsPerPage - 1, filteredInquiries.length)
  
      startIndex.textContent = filteredInquiries.length > 0 ? start : 0
      endIndex.textContent = end
      totalItems.textContent = filteredInquiries.length
  
      // Update pagination buttons
      prevPage.disabled = currentPage === 1
      nextPage.disabled = currentPage === totalPages || totalPages === 0
      prevPageMobile.disabled = currentPage === 1
      nextPageMobile.disabled = currentPage === totalPages || totalPages === 0
  
      // Generate pagination numbers
      paginationNav.innerHTML = ""
      paginationNav.appendChild(prevPage)
  
      for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement("button")
        pageButton.className = `pagination-button pagination-button-page ${i === currentPage ? "active" : ""}`
        pageButton.textContent = i
        pageButton.addEventListener("click", () => {
          currentPage = i
          updateTable()
        })
        paginationNav.appendChild(pageButton)
      }
  
      paginationNav.appendChild(nextPage)
  
      // Render table rows
      inquiriesTableBody.innerHTML = ""
  
      const paginatedInquiries = filteredInquiries.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage)
  
      paginatedInquiries.forEach((inquiry) => {
        const row = document.createElement("tr")
  
        // Checkbox cell
        const checkboxCell = document.createElement("td")
        const checkbox = document.createElement("input")
        checkbox.type = "checkbox"
        checkbox.className = "inquiry-checkbox"
        checkbox.setAttribute("data-id", inquiry.id)
        checkbox.checked = selectedInquiries.includes(inquiry.id)
        checkbox.addEventListener("change", () => {
          if (checkbox.checked) {
            if (!selectedInquiries.includes(inquiry.id)) {
              selectedInquiries.push(inquiry.id)
            }
          } else {
            selectedInquiries = selectedInquiries.filter((id) => id !== inquiry.id)
            selectAll.checked = false
          }
          updateBulkActions()
        })
        checkboxCell.appendChild(checkbox)
        row.appendChild(checkboxCell)
  
        // Subject cell
        const subjectCell = document.createElement("td")
        const subjectDiv = document.createElement("div")
        subjectDiv.style.fontSize = "0.875rem"
        subjectDiv.style.fontWeight = "500"
        subjectDiv.style.color = "var(--gray-900)"
        subjectDiv.textContent = inquiry.subject
  
        const previewDiv = document.createElement("div")
        previewDiv.style.fontSize = "0.875rem"
        previewDiv.style.color = "var(--gray-500)"
        previewDiv.style.whiteSpace = "nowrap"
        previewDiv.style.overflow = "hidden"
        previewDiv.style.textOverflow = "ellipsis"
        previewDiv.style.maxWidth = "300px"
        previewDiv.textContent = inquiry.message.substring(0, 60) + "..."
  
        subjectCell.appendChild(subjectDiv)
        subjectCell.appendChild(previewDiv)
        row.appendChild(subjectCell)
  
        // Customer cell
        const customerCell = document.createElement("td")
        customerCell.style.whiteSpace = "nowrap"
        customerCell.style.fontSize = "0.875rem"
        customerCell.style.color = "var(--gray-700)"
        customerCell.textContent = inquiry.customer
        row.appendChild(customerCell)
  
        // Date cell
        const dateCell = document.createElement("td")
        dateCell.style.whiteSpace = "nowrap"
        dateCell.style.fontSize = "0.875rem"
        dateCell.style.color = "var(--gray-700)"
        dateCell.textContent = inquiry.date
        row.appendChild(dateCell)
  
        // Status cell
        const statusCell = document.createElement("td")
        const statusBadge = document.createElement("span")
        statusBadge.className = `status-badge status-${inquiry.status.toLowerCase().replace(" ", "-")}`
        statusBadge.textContent = inquiry.status
        statusCell.appendChild(statusBadge)
        row.appendChild(statusCell)
  
        // Actions cell
        const actionsCell = document.createElement("td")
        actionsCell.style.whiteSpace = "nowrap"
        actionsCell.style.fontSize = "0.875rem"
        actionsCell.style.fontWeight = "500"
  
        const viewButton = document.createElement("button")
        viewButton.style.color = "var(--pink-600)"
        viewButton.style.background = "none"
        viewButton.style.border = "none"
        viewButton.style.cursor = "pointer"
        viewButton.style.marginRight = "0.75rem"
        viewButton.textContent = "View"
        viewButton.addEventListener("click", () => {
          viewInquiry(inquiry)
        })
  
        const resolveButton = document.createElement("button")
        resolveButton.style.color = "var(--green-800)"
        resolveButton.style.background = "none"
        resolveButton.style.border = "none"
        resolveButton.style.cursor = "pointer"
        resolveButton.style.marginRight = "0.75rem"
        resolveButton.innerHTML = '<i class="fas fa-check"></i>'
  
        const deleteButton = document.createElement("button")
        deleteButton.style.color = "var(--red-800)"
        deleteButton.style.background = "none"
        deleteButton.style.border = "none"
        deleteButton.style.cursor = "pointer"
        deleteButton.innerHTML = '<i class="fas fa-times"></i>'
  
        actionsCell.appendChild(viewButton)
        actionsCell.appendChild(resolveButton)
        actionsCell.appendChild(deleteButton)
        row.appendChild(actionsCell)
  
        inquiriesTableBody.appendChild(row)
      })
  
      // If no inquiries match the filters
      if (paginatedInquiries.length === 0) {
        const emptyRow = document.createElement("tr")
        const emptyCell = document.createElement("td")
        emptyCell.colSpan = 6
        emptyCell.textContent = "No inquiries found matching your filters."
        emptyCell.style.textAlign = "center"
        emptyCell.style.padding = "2rem 1rem"
        emptyCell.style.color = "var(--gray-500)"
        emptyRow.appendChild(emptyCell)
        inquiriesTableBody.appendChild(emptyRow)
      }
    }
  
    function updateBulkActions() {
      if (selectedInquiries.length > 0) {
        bulkActions.style.display = "flex"
        selectedCount.textContent = selectedInquiries.length
      } else {
        bulkActions.style.display = "none"
      }
    }
  
    function viewInquiry(inquiry) {
      currentInquiry = inquiry
  
      // Update modal content
      inquirySubject.textContent = inquiry.subject
      inquiryStatusBadge.className = `status-badge status-${inquiry.status.toLowerCase().replace(" ", "-")}`
      inquiryStatusBadge.textContent = inquiry.status
      inquiryCustomer.textContent = inquiry.customer
      inquiryDate.textContent = inquiry.date
      inquiryMessage.textContent = inquiry.message
      inquiryEmail.textContent = inquiry.email
      inquiryPhone.textContent = inquiry.phone
  
      // Show modal
      inquiryModal.style.display = "flex"
    }
  })
  