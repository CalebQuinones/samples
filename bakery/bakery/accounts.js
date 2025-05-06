document.addEventListener("DOMContentLoaded", () => {
    // Sample accounts data
    const accountsData = [
      {
        id: 1,
        name: "John Doe",
        email: "john.doe@example.com",
        role: "Admin",
        status: "Active",
        lastLogin: "Apr 9, 2025 10:30 AM",
        avatar: "JD",
      },
      {
        id: 2,
        name: "Jane Smith",
        email: "jane.smith@example.com",
        role: "Staff",
        status: "Active",
        lastLogin: "Apr 9, 2025 9:15 AM",
        avatar: "JS",
      },
      {
        id: 3,
        name: "Robert Johnson",
        email: "robert.johnson@example.com",
        role: "Staff",
        status: "Active",
        lastLogin: "Apr 8, 2025 4:45 PM",
        avatar: "RJ",
      },
      {
        id: 4,
        name: "Emily Davis",
        email: "emily.davis@example.com",
        role: "Customer",
        status: "Active",
        lastLogin: "Apr 7, 2025 2:30 PM",
        avatar: "ED",
      },
      {
        id: 5,
        name: "Michael Wilson",
        email: "michael.wilson@example.com",
        role: "Customer",
        status: "Inactive",
        lastLogin: "Mar 15, 2025 11:20 AM",
        avatar: "MW",
      },
      {
        id: 6,
        name: "Sarah Thompson",
        email: "sarah.thompson@example.com",
        role: "Customer",
        status: "Active",
        lastLogin: "Apr 6, 2025 8:45 AM",
        avatar: "ST",
      },
      {
        id: 7,
        name: "David Brown",
        email: "david.brown@example.com",
        role: "Staff",
        status: "Active",
        lastLogin: "Apr 8, 2025 1:15 PM",
        avatar: "DB",
      },
      {
        id: 8,
        name: "Jennifer Lee",
        email: "jennifer.lee@example.com",
        role: "Customer",
        status: "Inactive",
        lastLogin: "Mar 20, 2025 3:40 PM",
        avatar: "JL",
      },
    ];
  
    // DOM elements
    const accountsTableBody = document.getElementById("accountsTableBody");
    const searchInput = document.getElementById("searchInput");
    const roleFilter = document.getElementById("roleFilter");
    const statusFilter = document.getElementById("statusFilter");
    const selectAll = document.getElementById("selectAll");
    const bulkActions = document.getElementById("bulkActions");
    const selectedCount = document.getElementById("selectedCount");
    const clearSelection = document.getElementById("clearSelection");
    const prevPage = document.getElementById("prevPage");
    const nextPage = document.getElementById("nextPage");
    const prevPageMobile = document.getElementById("prevPageMobile");
    const nextPageMobile = document.getElementById("nextPageMobile");
    const paginationNav = document.getElementById("paginationNav");
    const startIndex = document.getElementById("startIndex");
    const endIndex = document.getElementById("endIndex");
    const totalItems = document.getElementById("totalItems");
    const addAccountButton = document.getElementById("addAccountButton");
    const addAccountModal = document.getElementById("addAccountModal");
    const viewAccountModal = document.getElementById("viewAccountModal");
    const saveAccount = document.getElementById("saveAccount");
    const cancelAccount = document.getElementById("cancelAccount");
    const saveChanges = document.getElementById("saveChanges");
    const closeAccount = document.getElementById("closeAccount");
    const resetPasswordBtn = document.getElementById("resetPasswordBtn");
  
    // Pagination state
    let currentPage = 1;
    const itemsPerPage = 5;
    let filteredAccounts = [...accountsData];
    let selectedAccounts = [];
    let currentAccount = null;
  
    // Initialize
    updateTable();
  
    // Event listeners
    searchInput.addEventListener("input", () => {
      currentPage = 1;
      updateTable();
    });
  
    roleFilter.addEventListener("change", () => {
      currentPage = 1;
      updateTable();
    });
  
    statusFilter.addEventListener("change", () => {
      currentPage = 1;
      updateTable();
    });
  
    selectAll.addEventListener("change", () => {
      const checkboxes = document.querySelectorAll(".account-checkbox");
      checkboxes.forEach((checkbox) => {
        checkbox.checked = selectAll.checked;
        const accountId = parseInt(checkbox.getAttribute("data-id"));
        if (selectAll.checked) {
          if (!selectedAccounts.includes(accountId)) {
            selectedAccounts.push(accountId);
          }
        } else {
          selectedAccounts = selectedAccounts.filter((id) => id !== accountId);
        }
      });
      updateBulkActions();
    });
  
    clearSelection.addEventListener("click", () => {
      selectedAccounts = [];
      selectAll.checked = false;
      const checkboxes = document.querySelectorAll(".account-checkbox");
      checkboxes.forEach((checkbox) => {
        checkbox.checked = false;
      });
      updateBulkActions();
    });
  
    prevPage.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--;
        updateTable();
      }
    });
  
    nextPage.addEventListener("click", () => {
      const totalPages = Math.ceil(filteredAccounts.length / itemsPerPage);
      if (currentPage < totalPages) {
        currentPage++;
        updateTable();
      }
    });
  
    prevPageMobile.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--;
        updateTable();
      }
    });
  
    nextPageMobile.addEventListener("click", () => {
      const totalPages = Math.ceil(filteredAccounts.length / itemsPerPage);
      if (currentPage < totalPages) {
        currentPage++;
        updateTable();
      }
    });
  
    // Modal event listeners
    addAccountButton.addEventListener("click", () => {
      addAccountModal.style.display = "flex";
    });
  
    cancelAccount.addEventListener("click", () => {
      addAccountModal.style.display = "none";
    });
  
    closeAccount.addEventListener("click", () => {
      viewAccountModal.style.display = "none";
    });
  
    saveAccount.addEventListener("click", () => {
      // Here you would normally save the new account
      addAccountModal.style.display = "none";
      alert("Account added successfully!");
    });
  
    saveChanges.addEventListener("click", () => {
      // Here you would normally save the account changes
      viewAccountModal.style.display = "none";
      alert("Account updated successfully!");
    });
  
    resetPasswordBtn.addEventListener("click", () => {
      alert("Password reset link sent to " + currentAccount.email);
    });
  
    // Close modals when clicking outside
    window.addEventListener("click", (event) => {
      if (event.target === addAccountModal) {
        addAccountModal.style.display = "none";
      }
      if (event.target === viewAccountModal) {
        viewAccountModal.style.display = "none";
      }
    });
  
    // Functions
    function updateTable() {
      // Filter accounts
      filteredAccounts = accountsData.filter((account) => {
        const searchTerm = searchInput.value.toLowerCase();
        const matchesSearch =
          account.name.toLowerCase().includes(searchTerm) ||
          account.email.toLowerCase().includes(searchTerm);
  
        const matchesRole = roleFilter.value === "All Roles" || account.role === roleFilter.value;
        const matchesStatus = statusFilter.value === "All Status" || account.status === statusFilter.value;
  
        return matchesSearch && matchesRole && matchesStatus;
      });
  
      // Update pagination info
      const totalPages = Math.ceil(filteredAccounts.length / itemsPerPage);
      const start = (currentPage - 1) * itemsPerPage + 1;
      const end = Math.min(start + itemsPerPage - 1, filteredAccounts.length);
  
      startIndex.textContent = filteredAccounts.length > 0 ? start : 0;
      endIndex.textContent = end;
      totalItems.textContent = filteredAccounts.length;
  
      // Update pagination buttons
      prevPage.disabled = currentPage === 1;
      nextPage.disabled = currentPage === totalPages || totalPages === 0;
      prevPageMobile.disabled = currentPage === 1;
      nextPageMobile.disabled = currentPage === totalPages || totalPages === 0;
  
      // Generate pagination numbers
      paginationNav.innerHTML = "";
      paginationNav.appendChild(prevPage);
  
      for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement("button");
        pageButton.className = `pagination-button pagination-button-page ${i === currentPage ? "active" : ""}`;
        pageButton.textContent = i;
        pageButton.addEventListener("click", () => {
          currentPage = i;
          updateTable();
        });
        paginationNav.appendChild(pageButton);
      }
  
      paginationNav.appendChild(nextPage);
  
      // Render table rows
      accountsTableBody.innerHTML = "";
  
      const paginatedAccounts = filteredAccounts.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);
  
      paginatedAccounts.forEach((account) => {
        const row = document.createElement("tr");
  
        // Checkbox cell
        const checkboxCell = document.createElement("td");
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.className = "account-checkbox";
        checkbox.setAttribute("data-id", account.id);
        checkbox.checked = selectedAccounts.includes(account.id);
        checkbox.addEventListener("change", () => {
          if (checkbox.checked) {
            if (!selectedAccounts.includes(account.id)) {
              selectedAccounts.push(account.id);
            }
          } else {
            selectedAccounts = selectedAccounts.filter((id) => id !== account.id);
            selectAll.checked = false;
          }
          updateBulkActions();
        });
        checkboxCell.appendChild(checkbox);
        row.appendChild(checkboxCell);
  
        // Name cell
        const nameCell = document.createElement("td");
        nameCell.className = "whitespace-nowrap";
        
        const nameContent = document.createElement("div");
        nameContent.className = "flex items-center";
        
        const avatar = document.createElement("div");
        avatar.className = "h-10 w-10 rounded-full bg-pink-100 flex items-center justify-center mr-3";
        avatar.innerHTML = `<span class="text-pink-600 font-medium">${account.avatar}</span>`;
        
        const nameDiv = document.createElement("div");
        nameDiv.className = "text-sm font-medium text-gray-900";
        nameDiv.textContent = account.name;
        
        nameContent.appendChild(avatar);
        nameContent.appendChild(nameDiv);
        nameCell.appendChild(nameContent);
        row.appendChild(nameCell);
  
        // Email cell
        const emailCell = document.createElement("td");
        emailCell.textContent = account.email;
        row.appendChild(emailCell);
  
        // Role cell
        const roleCell = document.createElement("td");
        roleCell.textContent = account.role;
        row.appendChild(roleCell);
  
        // Status cell
        const statusCell = document.createElement("td");
        const statusBadge = document.createElement("span");
        statusBadge.className = `status-badge ${account.status === "Active" ? "status-completed" : "status-cancelled"}`;
        statusBadge.textContent = account.status;
        statusCell.appendChild(statusBadge);
        row.appendChild(statusCell);
  
        // Last Login cell
        const lastLoginCell = document.createElement("td");
        lastLoginCell.textContent = account.lastLogin;
        row.appendChild(lastLoginCell);
  
        // Actions cell
        const actionsCell = document.createElement("td");
        const actionsDiv = document.createElement("div");
        actionsDiv.className = "actions";
        
        const editButton = document.createElement("button");
        editButton.className = "edit-button";
        editButton.innerHTML = '<i class="fas fa-edit"></i>';
        editButton.title = "Edit Account";
        editButton.addEventListener("click", () => viewAccount(account));
        
        const deleteButton = document.createElement("button");
        deleteButton.className = "delete-button";
        deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
        deleteButton.title = "Delete Account";
        deleteButton.addEventListener("click", () => {
          if (confirm(`Are you sure you want to delete ${account.name}'s account?`)) {
            alert(`Account for ${account.name} has been deleted.`);
            // Here you would normally delete the account
          }
        });
        
        actionsDiv.appendChild(editButton);
        actionsDiv.appendChild(deleteButton);
        actionsCell.appendChild(actionsDiv);
        row.appendChild(actionsCell);
  
        accountsTableBody.appendChild(row);
      });
  
      // If no accounts match the filters
      if (paginatedAccounts.length === 0) {
        const emptyRow = document.createElement("tr");
        const emptyCell = document.createElement("td");
        emptyCell.colSpan = 7;
        emptyCell.className = "no-results";
        emptyCell.textContent = "No accounts found matching your filters.";
        emptyRow.appendChild(emptyCell);
        accountsTableBody.appendChild(emptyRow);
      }
    }
  
    function updateBulkActions() {
      if (selectedAccounts.length > 0) {
        bulkActions.style.display = "flex";
        selectedCount.textContent = selectedAccounts.length;
      } else {
        bulkActions.style.display = "none";
      }
    }
  
    function viewAccount(account) {
      currentAccount = account;
      
      // Populate the modal with account details
      document.getElementById("view-name").value = account.name;
      document.getElementById("view-email").value = account.email;
      document.getElementById("view-role").value = account.role;
      document.getElementById("view-status").value = account.status;
      document.getElementById("lastLogin").textContent = account.lastLogin;
      document.getElementById("accountAvatar").textContent = account.avatar;
      
      // Show the modal
      viewAccountModal.style.display = "flex";
    }
  });
  