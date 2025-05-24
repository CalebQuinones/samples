// Global functions for account management
function showCustomerDetails(userId) {
    fetch(`get_customer_details.php?user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            const elements = {
                'customerPhone': data.phone || 'Not provided',
                'customerBirthday': data.birthday ? new Date(data.birthday).toLocaleDateString() : 'Not provided',
                'customerAddress': data.address || 'Not provided',
                'customerPayment': data.payment || 'Not provided',
                'customerCreatedAt': new Date(data.created_at).toLocaleDateString()
            };

            Object.entries(elements).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) element.textContent = value;
            });

            const customerDetailsModal = document.getElementById('customerDetailsModal');
            if (customerDetailsModal) {
                window.showModal(customerDetailsModal);
            }
        })
        .catch(error => {
            console.error('Error fetching customer details:', error);
            alert('Failed to load customer details. Please try again.');
        });
}

function showEditModal(userId) {
    fetch(`get_account.php?id=${userId}`)
        .then(response => response.json())
        .then(account => {
            const accountEditModal = document.getElementById('accountEditModal');
            if (!accountEditModal) {
                console.error('Edit modal not found');
                return;
            }

            // Fill in the form fields
            Object.entries({
                'editUserId': account.user_id,
                'editFirstName': account.Fname,
                'editLastName': account.Lname,
                'editEmail': account.email,
                'editPhone': account.phone || '',
                'editAddress': account.address || '',
                'editStatus': account.status
            }).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) element.value = value;
            });

            window.showModal(accountEditModal);
        })
        .catch(error => {
            console.error('Error fetching account details:', error);
            alert('Failed to load account details. Please try again.');
        });
}

function confirmArchiveAccount(userId) {
    if (confirm('Are you sure you want to archive this account? The user will no longer be able to access their account.')) {
        fetch('archive_account.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error archiving account');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// Global modal functions
window.showModal = function(modal) {
    if (!modal) {
        console.error('Modal not found');
        return;
    }
    
    const modalOverlay = document.getElementById('modalOverlay');
    const bulkActions = document.getElementById('bulkActions');
    
    if (!modalOverlay) {
        console.error('Modal overlay not found');
        return;
    }

    // Hide all modals and bulk actions first
    document.querySelectorAll('.modal').forEach(m => {
        m.style.display = 'none';
        m.classList.remove('active');
    });
    if (bulkActions) bulkActions.style.display = 'none';
    
    // Show the specific modal and overlay
    modal.style.display = 'block';
    modalOverlay.style.display = 'flex';
    
    // Trigger reflow
    void modal.offsetHeight;
    
    // Add active classes
    modal.classList.add('active');
    modalOverlay.classList.add('active');
    
    document.body.style.overflow = 'hidden';
};

window.closeModal = function() {
    const modalOverlay = document.getElementById('modalOverlay');
    const activeModals = document.querySelectorAll('.modal.active');
    
    if (modalOverlay) modalOverlay.classList.remove('active');
    activeModals.forEach(modal => modal.classList.remove('active'));
    
    setTimeout(() => {
        if (modalOverlay) modalOverlay.style.display = 'none';
        activeModals.forEach(modal => modal.style.display = 'none');
        // Update bulk actions visibility after modal closes
        updateBulkActions();
    }, 300);
    
    document.body.style.overflow = '';
};

document.addEventListener("DOMContentLoaded", function() {
    console.log("accounts.js loaded");
    console.log("DOM fully loaded");

    // DOM Elements
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
    const accountEditModal = document.getElementById('accountEditModal');
    const accountEditForm = document.getElementById('accountEditForm');
    const closeEditModal = document.getElementById('closeEditModal');
    const customerDetailsModal = document.getElementById('customerDetailsModal');
    const closeCustomerDetails = document.getElementById('closeCustomerDetails');
    const closeCustomerDetailsBtn = document.getElementById('closeCustomerDetailsBtn');

    // Event Listeners
    if (addAccountButton) {
        addAccountButton.addEventListener('click', () => window.showModal(addAccountModal));
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            const accountCheckboxes = document.querySelectorAll('.account-checkbox');
            accountCheckboxes.forEach(checkbox => {
                if (checkbox !== selectAll) {
                    checkbox.checked = isChecked;
                }
            });
            updateBulkActions();
        });
    }

    // Add change event listeners to individual checkboxes
    const accountCheckboxes = document.querySelectorAll('.account-checkbox');
    accountCheckboxes.forEach(checkbox => {
        if (checkbox !== selectAll) {
            checkbox.addEventListener('change', function() {
                updateBulkActions();
                // Update select all checkbox state
                if (selectAll) {
                    const allChecked = Array.from(accountCheckboxes)
                        .filter(cb => cb !== selectAll)
                        .every(cb => cb.checked);
                    selectAll.checked = allChecked;
                }
            });
        }
    });

    // Close modal when clicking outside
    const modalOverlay = document.getElementById('modalOverlay');
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                window.closeModal();
            }
        });
    }

    // Close modal when clicking close buttons
    const closeButtons = document.querySelectorAll('.close-modal');
    closeButtons.forEach(button => {
        button.addEventListener('click', () => window.closeModal());
    });

    // Modal close buttons
    const allCloseButtons = document.querySelectorAll('.close-modal, .modal-button-secondary');
    allCloseButtons.forEach(button => {
        button.addEventListener('click', window.closeModal);
    });

    // Edit buttons event delegation
    document.addEventListener('click', function(e) {
        const editButton = e.target.closest('.edit-button');
        if (editButton && !editButton.id) { // Ignore the Add Account button
            const userId = editButton.closest('tr').querySelector('.account-checkbox').dataset.id;
            showEditModal(userId);
        }
    });

    // View buttons event delegation
    document.addEventListener('click', function(e) {
        const viewButton = e.target.closest('.view-button');
        if (viewButton) {
            const userId = viewButton.closest('tr').querySelector('.account-checkbox').dataset.id;
            showCustomerDetails(userId);
        }
    });

    // ESC key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeModal();
        }
    });

    // Update bulk actions visibility
    function updateBulkActions() {
        const checkedCount = document.querySelectorAll('.account-checkbox:checked').length;
        if (bulkActions) {
            bulkActions.style.display = checkedCount > 0 ? 'flex' : 'none';
            if (selectedCount) selectedCount.textContent = checkedCount;
        }
    }

    // Clear selection button
    if (clearSelection) {
        clearSelection.addEventListener('click', () => {
            if (selectAll) selectAll.checked = false;
            accountCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateBulkActions();
        });
    }

    // Save account button
    if (saveAccount) {
        saveAccount.addEventListener('click', () => {
            window.closeModal();
            alert("Account added successfully!");
        });
    }

    // Save changes button
    if (saveChanges) {
        saveChanges.addEventListener('click', () => {
            window.closeModal();
            alert("Account updated successfully!");
        });
    }

    // Reset password button
    if (resetPasswordBtn) {
        resetPasswordBtn.addEventListener('click', () => {
            alert("Password reset link sent!");
        });
    }

    // Initialize
    updateBulkActions();
});
