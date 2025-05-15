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
            if (!accountEditModal) return;

            const fields = {
                'editUserId': account.user_id,
                'editFirstName': account.Fname,
                'editLastName': account.Lname,
                'editEmail': account.email,
                'editPhone': account.phone || '',
                'editAddress': account.address || '',
                'editStatus': account.status
            };

            Object.entries(fields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) element.value = value;
            });

            window.showModal(accountEditModal);
        })
        .catch(error => console.error('Error:', error));
}

function confirmDeleteAccount(userId) {
    if (confirm('Are you sure you want to delete this account? This action cannot be undone.')) {
        fetch('delete_account.php', {
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
                alert(data.message || 'Error deleting account');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// Global modal functions
window.showModal = function(modal) {
    if (!modal) return;
    
    const modalOverlay = document.getElementById('modalOverlay');
    if (!modalOverlay) return;

    // Reset any existing modals
    const allModals = document.querySelectorAll('.modal');
    allModals.forEach(m => {
        m.classList.remove('active');
        m.style.display = 'none';
    });

    // Show the modal overlay
    modalOverlay.style.display = 'flex';
    modalOverlay.style.visibility = 'visible';
    setTimeout(() => {
        modalOverlay.classList.add('active');
    }, 10);

    // Show the specific modal
    modal.style.display = 'block';
    setTimeout(() => {
        modal.classList.add('active');
    }, 10);

    // Prevent body scrolling
    document.body.style.overflow = 'hidden';
};

window.closeModal = function() {
    const activeModal = document.querySelector('.modal.active');
    if (activeModal) {
        activeModal.classList.remove('active');
        setTimeout(() => {
            activeModal.style.display = 'none';
            hideOverlayIfNoModal();
        }, 300);
    }

    // Restore body scrolling
    document.body.style.overflow = '';
};

function hideOverlayIfNoModal() {
    const modalOverlay = document.getElementById('modalOverlay');
    if (!modalOverlay) return;
    
    const activeModals = document.querySelectorAll('.modal.active');
    if (activeModals.length === 0) {
        modalOverlay.classList.remove('active');
        setTimeout(() => {
            modalOverlay.style.display = 'none';
            modalOverlay.style.visibility = 'hidden';
        }, 300);
    }
}

document.addEventListener("DOMContentLoaded", function() {
    console.log("accounts.js loaded");
    console.log("DOM fully loaded");

    // Initialize modal overlay
    const modalOverlay = document.getElementById('modalOverlay');
    if (modalOverlay) {
        modalOverlay.style.display = 'none';
        modalOverlay.style.visibility = 'hidden';
    }

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
