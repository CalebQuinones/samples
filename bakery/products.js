document.addEventListener("DOMContentLoaded", () => {
    // Sample products data
    const productsData = [
      {
        id: "CAKE-001",
        name: "Chocolate Celebration Cake",
        image: "placeholder.svg",
        category: "Cakes",
        price: "$45.99",
        status: "In Stock",
      },
      {
        id: "CAKE-002",
        name: "Vanilla Birthday Cake",
        image: "placeholder.svg",
        category: "Cakes",
        price: "$39.99",
        status: "In Stock",
      },
      {
        id: "CAKE-003",
        name: "Strawberry Shortcake",
        image: "placeholder.svg",
        category: "Cakes",
        price: "$42.99",
        status: "In Stock",
      },
      {
        id: "CUP-001",
        name: "Red Velvet Cupcakes (12 pcs)",
        image: "placeholder.svg",
        category: "Cupcakes",
        price: "$36.99",
        status: "Low Stock",
      },
      {
        id: "BRD-001",
        name: "Artisan Sourdough Bread",
        image: "placeholder.svg",
        category: "Breads",
        price: "$8.99",
        status: "In Stock",
      },
      {
        id: "CUP-002",
        name: "Chocolate Cupcakes (12 pcs)",
        image: "placeholder.svg",
        category: "Cupcakes",
        price: "$34.99",
        status: "In Stock",
      },
      {
        id: "CAKE-004",
        name: "Carrot Cake",
        image: "placeholder.svg",
        category: "Cakes",
        price: "$38.99",
        status: "In Stock",
      },
      {
        id: "PAST-001",
        name: "Chocolate Croissants (4 pcs)",
        image: "placeholder.svg",
        category: "Pastries",
        price: "$12.99",
        status: "Out of Stock",
      },
      {
        id: "CAKE-005",
        name: "Lemon Drizzle Cake",
        image: "placeholder.svg",
        category: "Cakes",
        price: "$36.99",
        status: "In Stock",
      },
      {
        id: "BRD-002",
        name: "Ciabatta Bread",
        image: "placeholder.svg",
        category: "Breads",
        price: "$7.99",
        status: "In Stock",
      },
      {
        id: "PAST-002",
        name: "Almond Croissants (4 pcs)",
        image: "placeholder.svg",
        category: "Pastries",
        price: "$14.99",
        status: "In Stock",
      },
      {
        id: "CUP-003",
        name: "Vanilla Cupcakes (12 pcs)",
        image: "placeholder.svg",
        category: "Cupcakes",
        price: "$34.99",
        status: "In Stock",
      },
    ]
  
    // DOM elements
    const productsTableBody = document.getElementById("productsTableBody")
    const searchInput = document.getElementById("searchInput")
    const categoryFilter = document.getElementById("categoryFilter")
    const statusFilter = document.getElementById("statusFilter")
    const sortBy = document.getElementById("sortBy")
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
    const productModal = document.getElementById("productModal")
    const productModalTitle = document.getElementById("productModalTitle")
    const addProductButton = document.getElementById("addProductButton")
    const saveProduct = document.getElementById("saveProduct")
    const cancelProduct = document.getElementById("cancelProduct")
  
    // Pagination state
    let currentPage = 1
    const itemsPerPage = 5
    let filteredProducts = [...productsData]
    let selectedProducts = []
  
    // Initialize
    updateTable()
  
    // Event listeners
    searchInput.addEventListener("input", () => {
      currentPage = 1
      updateTable()
    })
  
    categoryFilter.addEventListener("change", () => {
      currentPage = 1
      updateTable()
    })
  
    statusFilter.addEventListener("change", () => {
      currentPage = 1
      updateTable()
    })
  
    sortBy.addEventListener("change", () => {
      currentPage = 1
      updateTable()
    })
  
    selectAll.addEventListener("change", () => {
      const checkboxes = document.querySelectorAll(".product-checkbox")
      checkboxes.forEach((checkbox) => {
        checkbox.checked = selectAll.checked
        const productId = checkbox.getAttribute("data-id")
        if (selectAll.checked) {
          if (!selectedProducts.includes(productId)) {
            selectedProducts.push(productId)
          }
        } else {
          selectedProducts = selectedProducts.filter((id) => id !== productId)
        }
      })
      updateBulkActions()
    })
  
    clearSelection.addEventListener("click", () => {
      selectedProducts = []
      selectAll.checked = false
      const checkboxes = document.querySelectorAll(".product-checkbox")
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
      const totalPages = Math.ceil(filteredProducts.length / itemsPerPage)
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
      const totalPages = Math.ceil(filteredProducts.length / itemsPerPage)
      if (currentPage < totalPages) {
        currentPage++
        updateTable()
      }
    })
  
    addProductButton.addEventListener("click", () => {
      productModal.style.display = "flex"
    })
  
    cancelProduct.addEventListener("click", () => {
      productModal.style.display = "none"
    })
  
    saveProduct.addEventListener("click", () => {
      alert("Product added successfully!")
      productModal.style.display = "none"
    })
  
    // Close modal when clicking outside
    window.addEventListener("click", (event) => {
      if (event.target === productModal) {
        productModal.style.display = "none"
      }
    })
  
    // Functions
    function updateTable() {
      // Filter products
      filteredProducts = productsData.filter((product) => {
        const searchTerm = searchInput.value.toLowerCase()
        const matchesSearch =
          product.name.toLowerCase().includes(searchTerm) || product.id.toLowerCase().includes(searchTerm)
  
        const matchesCategory = categoryFilter.value === "All Categories" || product.category === categoryFilter.value
        const matchesStatus = statusFilter.value === "All Status" || product.status === statusFilter.value
  
        return matchesSearch && matchesCategory && matchesStatus
      })
  
      // Sort products
      filteredProducts.sort((a, b) => {
        const sortOption = sortBy.value
  
        if (sortOption === "Sort By: Price (Low to High)") {
          return Number.parseFloat(a.price.replace("$", "")) - Number.parseFloat(b.price.replace("$", ""))
        } else if (sortOption === "Sort By: Price (High to Low)") {
          return Number.parseFloat(b.price.replace("$", "")) - Number.parseFloat(a.price.replace("$", ""))
        } else if (sortOption === "Sort By: Name (A-Z)") {
          return a.name.localeCompare(b.name)
        } else if (sortOption === "Sort By: Name (Z-A)") {
          return b.name.localeCompare(a.name)
        }
  
        // Default: Sort By: Newest (we'll use ID as a proxy for newness)
        return a.id.localeCompare(b.id)
      })
  
      // Update pagination info
      const totalPages = Math.ceil(filteredProducts.length / itemsPerPage)
      const start = (currentPage - 1) * itemsPerPage + 1
      const end = Math.min(start + itemsPerPage - 1, filteredProducts.length)
  
      startIndex.textContent = filteredProducts.length > 0 ? start : 0
      endIndex.textContent = end
      totalItems.textContent = filteredProducts.length
  
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
      productsTableBody.innerHTML = ""
  
      const paginatedProducts = filteredProducts.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage)
  
      paginatedProducts.forEach((product) => {
        const row = document.createElement("tr")
  
        // Checkbox cell
        const checkboxCell = document.createElement("td")
        const checkbox = document.createElement("input")
        checkbox.type = "checkbox"
        checkbox.className = "product-checkbox"
        checkbox.setAttribute("data-id", product.id)
        checkbox.checked = selectedProducts.includes(product.id)
        checkbox.addEventListener("change", () => {
          if (checkbox.checked) {
            if (!selectedProducts.includes(product.id)) {
              selectedProducts.push(product.id)
            }
          } else {
            selectedProducts = selectedProducts.filter((id) => id !== product.id)
            selectAll.checked = false
          }
          updateBulkActions()
        })
        checkboxCell.appendChild(checkbox)
        row.appendChild(checkboxCell)
  
        // Product cell
        const productCell = document.createElement("td")
        const productDiv = document.createElement("div")
        productDiv.style.display = "flex"
        productDiv.style.alignItems = "center"
  
        const imageDiv = document.createElement("div")
        imageDiv.style.width = "40px"
        imageDiv.style.height = "40px"
        imageDiv.style.borderRadius = "6px"
        imageDiv.style.backgroundColor = "var(--pink-100)"
        imageDiv.style.display = "flex"
        imageDiv.style.alignItems = "center"
        imageDiv.style.justifyContent = "center"
        imageDiv.style.marginRight = "12px"
  
        const img = document.createElement("img")
        img.src = product.image
        img.alt = product.name
        img.style.width = "40px"
        img.style.height = "40px"
        img.style.borderRadius = "6px"
        img.style.objectFit = "cover"
  
        imageDiv.appendChild(img)
  
        const textDiv = document.createElement("div")
  
        const nameDiv = document.createElement("div")
        nameDiv.style.fontSize = "0.875rem"
        nameDiv.style.fontWeight = "500"
        nameDiv.style.color = "var(--gray-900)"
        nameDiv.textContent = product.name
  
        const idDiv = document.createElement("div")
        idDiv.style.fontSize = "0.875rem"
        idDiv.style.color = "var(--gray-500)"
        idDiv.textContent = `SKU: ${product.id}`
  
        textDiv.appendChild(nameDiv)
        textDiv.appendChild(idDiv)
  
        productDiv.appendChild(imageDiv)
        productDiv.appendChild(textDiv)
  
        productCell.appendChild(productDiv)
        row.appendChild(productCell)
  
        // Category cell
        const categoryCell = document.createElement("td")
        categoryCell.style.whiteSpace = "nowrap"
        categoryCell.style.fontSize = "0.875rem"
        categoryCell.style.color = "var(--gray-700)"
        categoryCell.textContent = product.category
        row.appendChild(categoryCell)
  
        // Price cell
        const priceCell = document.createElement("td")
        priceCell.style.whiteSpace = "nowrap"
        priceCell.style.fontSize = "0.875rem"
        priceCell.style.color = "var(--gray-700)"
        priceCell.textContent = product.price
        row.appendChild(priceCell)
  
        // Status cell
        const statusCell = document.createElement("td")
        const statusBadge = document.createElement("span")
        statusBadge.className = `status-badge status-${product.status.toLowerCase().replace(" ", "-")}`
        statusBadge.textContent = product.status
        statusCell.appendChild(statusBadge)
        row.appendChild(statusCell)
  
        // Actions cell
        const actionsCell = document.createElement("td")
        actionsCell.style.whiteSpace = "nowrap"
        actionsCell.style.fontSize = "0.875rem"
        actionsCell.style.fontWeight = "500"
  
        const editButton = document.createElement("button")
        editButton.style.color = "var(--blue-600)"
        editButton.style.background = "none"
        editButton.style.border = "none"
        editButton.style.cursor = "pointer"
        editButton.style.marginRight = "0.75rem"
        editButton.innerHTML = '<i class="fas fa-edit"></i>'
  
        const viewButton = document.createElement("button")
        viewButton.style.color = "var(--green-600)"
        viewButton.style.background = "none"
        viewButton.style.border = "none"
        viewButton.style.cursor = "pointer"
        viewButton.style.marginRight = "0.75rem"
  
        const deleteButton = document.createElement("button")
        deleteButton.style.color = "var(--red-600)"
        deleteButton.style.background = "none"
        deleteButton.style.border = "none"
        deleteButton.style.cursor = "pointer"
        deleteButton.innerHTML = '<i class="fas fa-trash"></i>'
  
        actionsCell.appendChild(editButton)
        actionsCell.appendChild(viewButton)
        actionsCell.appendChild(deleteButton)
        row.appendChild(actionsCell)
  
        productsTableBody.appendChild(row)
      })
  
      // If no products match the filters
      if (paginatedProducts.length === 0) {
        const emptyRow = document.createElement("tr")
        const emptyCell = document.createElement("td")
        emptyCell.colSpan = 6
        emptyCell.textContent = "No products found matching your filters."
        emptyCell.style.textAlign = "center"
        emptyCell.style.padding = "2rem 1rem"
        emptyCell.style.color = "var(--gray-500)"
        emptyRow.appendChild(emptyCell)
        productsTableBody.appendChild(emptyRow)
      }
    }
  
    function updateBulkActions() {
      if (selectedProducts.length > 0) {
        bulkActions.style.display = "flex"
        selectedCount.textContent = selectedProducts.length
      } else {
        bulkActions.style.display = "none"
      }
    }
  })
  