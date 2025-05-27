// Global functions for account management
function showCustomerDetails(userId) {
    console.log('showCustomerDetails called with userId:', userId);
    
    if (!userId) {
        console.error('No userId provided to showCustomerDetails');
        alert('Error: No user ID provided');
        return;
    }
    
    // Get or create customer details modal
    let customerDetailsModal = document.getElementById('customerDetailsModal');
    if (!customerDetailsModal) {
        console.log('Creating customer details modal element');
        customerDetailsModal = document.createElement('div');
        customerDetailsModal.id = 'customerDetailsModal';
        customerDetailsModal.className = 'modal';
        document.body.appendChild(customerDetailsModal);
    }
    
    // Create modal structure with loading state
    customerDetailsModal.innerHTML = `
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Customer Details</h3>
                <button type="button" class="close-button" onclick="window.closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="text-align: center; padding: 40px;">
                    <div style="width: 40px; height: 40px; border: 4px solid #f3f4f6; border-top: 4px solid #db2777; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 16px;"></div>
                    <p>Loading customer details...</p>
                </div>
            </div>
        </div>
    `;
    
    // Show the modal immediately with loading state
    window.showModal(customerDetailsModal);
    
    // Fetch customer details
    fetch(`./get_customer_details.php?user_id=${encodeURIComponent(userId)}`)
        .then(response => {
            console.log('Customer details response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Customer data received:', data);
            
            if (!data || typeof data !== 'object') {
                throw new Error('Invalid customer data received');
            }
            
            // Check if user was found
            if (data.error || data.message === 'User not found') {
                throw new Error('Customer not found');
            }
            
            // Update modal content with customer details
            const modalBody = customerDetailsModal.querySelector('.modal-body');
            modalBody.innerHTML = `
                <div class="customer-info">
                    <div class="customer-info-item">
                        <div class="customer-info-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="customer-info-text">
                            <strong>Name:</strong> ${data.Fname || ''} ${data.Lname || ''}
                        </div>
                    </div>
                    
                    <div class="customer-info-item">
                        <div class="customer-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="customer-info-text">
                            <strong>Email:</strong> ${data.email || 'Not provided'}
                        </div>
                    </div>
                    
                    <div class="customer-info-item">
                        <div class="customer-info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="customer-info-text">
                            <strong>Phone:</strong> ${data.phone || 'Not provided'}
                        </div>
                    </div>
                    
                    <div class="customer-info-item">
                        <div class="customer-info-icon">
                            <i class="fas fa-birthday-cake"></i>
                        </div>
                        <div class="customer-info-text">
                            <strong>Birthday:</strong> ${data.birthday ? new Date(data.birthday).toLocaleDateString() : 'Not provided'}
                        </div>
                    </div>
                    
                    <div class="customer-info-item">
                        <div class="customer-info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="customer-info-text">
                            <strong>Address:</strong> ${data.address || 'Not provided'}
                        </div>
                    </div>
                    
                    <div class="customer-info-item">
                        <div class="customer-info-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="customer-info-text">
                            <strong>Payment Method:</strong> ${data.payment || 'Not provided'}
                        </div>
                    </div>
                    
                    <div class="customer-info-item">
                        <div class="customer-info-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="customer-info-text">
                            <strong>Member Since:</strong> ${data.created_at ? new Date(data.created_at).toLocaleDateString() : 'Inactive'}
                        </div>
                    </div>
                    
                    <div class="customer-info-item">
                        <div class="customer-info-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="customer-info-text">
                            <strong>Status:</strong> 
                            <span class="status-badge ${data.status === 'active' ? 'status-delivered' : 'status-cancelled'}">
                                ${data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : 'Inactive'}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="modal-button modal-button-secondary" onclick="window.closeModal()">Close</button>
                </div>
            `;

            // Update the modal title
            const modalTitle = customerDetailsModal.querySelector('.modal-title');
            if (modalTitle) {
                const fullName = `${data.Fname || ''} ${data.Lname || ''}`.trim() || 'Customer';
                modalTitle.textContent = `${fullName} - Details`;
            }
            
            console.log('Customer details modal successfully populated');
        })
        .catch(error => {
            console.error('Error in showCustomerDetails:', error);
            
            // Close modal and show error
            window.closeModal();
            alert(`Failed to load customer details: ${error.message}`);
        });
}

function showEditModal(userId) {
    console.log('showEditModal called with userId:', userId);
    
    if (!userId) {
        console.error('No userId provided to showEditModal');
        alert('Error: No user ID provided');
        return;
    }
    
    // Get the modal element
    let accountEditModal = document.getElementById('accountEditModal');
    if (!accountEditModal) {
        console.log('Creating edit modal element');
        accountEditModal = document.createElement('div');
        accountEditModal.id = 'accountEditModal';
        accountEditModal.className = 'modal';
        document.body.appendChild(accountEditModal);
    }
    
    // Create modal structure with loading state
    accountEditModal.innerHTML = `
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Edit Account</h3>
                <button type="button" class="close-button" onclick="window.closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div style="text-align: center; padding: 40px;">
                    <div style="width: 40px; height: 40px; border: 4px solid #f3f4f6; border-top: 4px solid #db2777; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 16px;"></div>
                    <p>Loading account details...</p>
                </div>
            </div>
        </div>
    `;
    
    // Show the modal immediately with loading state
    window.showModal(accountEditModal);
    
    // Fetch account details
    fetch(`./get_account.php?id=${encodeURIComponent(userId)}`)
        .then(response => {
            console.log('Edit modal response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(account => {
            console.log('Account data received:', account);
            
            if (!account || typeof account !== 'object') {
                throw new Error('Invalid account data received');
            }
            
            // Check if account was found
            if (account.error || account.message === 'Account not found') {
                throw new Error('Account not found');
            }
            
            // Update modal content with comprehensive edit form
            const modalBody = accountEditModal.querySelector('.modal-body');
            modalBody.innerHTML = `
                <form id="accountEditForm">
                    <input type="hidden" id="user_id" name="user_id" value="${account.user_id || userId}">
                    
                    <!-- Personal Information -->

                    
                    <!-- Account Settings - ONLY Active/Inactive options -->
                    <div class="form-group">
                        <label for="editStatus">Account Status</label>
                        <select id="editStatus" name="status" class="form-select" required>
                            <option value="active" ${(account.status || 'active') === 'active' ? 'selected' : ''}>Active</option>
                            <option value="inactive" ${account.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="editRole">User Role</label>
                        <select id="editRole" name="role" class="form-select">
                            <option value="customer" ${(account.role || 'customer') === 'customer' ? 'selected' : ''}>Customer</option>
                            <option value="admin" ${account.role === 'admin' ? 'selected' : ''}>Administrator</option>
                            <option value="moderator" ${account.role === 'moderator' ? 'selected' : ''}>Moderator</option>
                        </select>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="form-group">
                        <label for="editAddress">Address</label>
                        <textarea id="editAddress" name="address" class="form-control" rows="3" placeholder="Optional">${account.address || ''}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="editBirthday">Birthday</label>
                        <input type="date" id="editBirthday" name="birthday" class="form-control" value="${account.birthday || ''}" placeholder="Optional">
                    </div>
                    
                    <!-- Account Information (Read-only) -->
                    <div class="form-group">
                        <label>Account Created</label>
                        <input type="text" class="form-control" value="${account.created_at ? new Date(account.created_at).toLocaleDateString() : 'Unknown'}" readonly style="background-color: #f9fafb; color: #6b7280;">
                    </div>
                    
                    <div class="form-group">
                        <label>Last Login</label>
                        <input type="text" class="form-control" value="${account.last_login ? new Date(account.last_login).toLocaleDateString() : 'Never'}" readonly style="background-color: #f9fafb; color: #6b7280;">
                    </div>
                </form>
                
                <div class="modal-footer">
                    <button type="button" class="modal-button modal-button-secondary" onclick="window.closeModal()">Cancel</button>
                    <button type="submit" form="accountEditForm" class="modal-button modal-button-primary">Save Changes</button>
                </div>
            `;

            // Update the modal title
            const modalTitle = accountEditModal.querySelector('.modal-title');
            if (modalTitle) {
                const fullName = `${account.Fname || ''} ${account.Lname || ''}`.trim() || 'User';
                modalTitle.textContent = `Edit Account - ${fullName}`;
            }
            
            // Add form submit handler
            const form = document.getElementById('accountEditForm');
            if (form) {
                form.addEventListener('submit', handleEditFormSubmit);
            }
            
            console.log('Edit modal successfully populated with account data');
        })
        .catch(error => {
            console.error('Error in showEditModal:', error);
            
            // Close modal and show error
            window.closeModal();
            alert(`Failed to load account details: ${error.message}`);
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
        .catch(error => {
            console.error('Error:', error);
            alert('Error archiving account. Please try again.');
        });
    }
}

// Global modal functions that work with your existing CSS
window.showModal = function(modal) {
    if (!modal) {
        console.error('Modal element not provided to showModal');
        return;
    }

    // Get or create modal overlay
    let modalOverlay = document.getElementById('modalOverlay');
    if (!modalOverlay) {
        console.log('Creating modal overlay');
        modalOverlay = document.createElement('div');
        modalOverlay.id = 'modalOverlay';
        modalOverlay.className = 'modal-overlay';
        document.body.appendChild(modalOverlay);
    }

    console.log('Showing modal:', modal.id);

    // Hide all other modals first
    document.querySelectorAll('.modal').forEach(m => {
        if (m !== modal) {
            m.classList.remove('active');
            m.style.display = 'none';
        }
    });

    // Clear overlay and add the modal
    modalOverlay.innerHTML = '';
    modalOverlay.appendChild(modal);

    // Show the overlay and modal
    modalOverlay.style.display = 'flex';
    modal.style.display = 'block';
    
    // Handle body scroll
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = window.innerWidth > document.documentElement.clientWidth ? '15px' : '0';
    
    // Force reflow and add active classes for your CSS transitions
    requestAnimationFrame(() => {
        modalOverlay.classList.add('active');
        modal.classList.add('active');
        
        // Set focus
        setTimeout(() => {
            const focusable = modal.querySelector('button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (focusable) {
                focusable.focus();
            } else {
                modal.focus();
            }
        }, 100);
    });
    
    // Handle click outside to close
    const handleClickOutside = (e) => {
        if (e.target === modalOverlay) {
            e.preventDefault();
            e.stopPropagation();
            window.closeModal();
        }
    };
    
    // Handle ESC key
    const handleEscape = (e) => {
        if (e.key === 'Escape' || e.key === 'Esc') {
            e.preventDefault();
            e.stopPropagation();
            window.closeModal();
        }
    };
    
    // Clean up any existing listeners
    modalOverlay.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleEscape);
    
    // Add new listeners
    modalOverlay.addEventListener('click', handleClickOutside, { passive: false });
    document.addEventListener('keydown', handleEscape, { passive: false });
    
    console.log('Modal shown successfully:', modal.id);
};

window.closeModal = function() {
    console.log('closeModal called');
    
    const modalOverlay = document.getElementById('modalOverlay');
    if (!modalOverlay) {
        console.log('No modal overlay found');
        return;
    }

    // If already hidden, do nothing
    if (!modalOverlay.classList.contains('active')) {
        console.log('Modal already closed');
        return;
    }

    const activeModal = modalOverlay.querySelector('.modal');

    // Remove active classes to trigger your CSS transitions
    modalOverlay.classList.remove('active');
    if (activeModal) {
        activeModal.classList.remove('active');
    }

    // Clean up after the transition (matches your CSS transition duration)
    setTimeout(() => {
        modalOverlay.style.display = 'none';
        if (activeModal) {
            activeModal.style.display = 'none';
        }
        
        // Re-enable body scroll and reset padding
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        console.log('Modal closed successfully');
    }, 200); // Matches your CSS transition duration
};

// Make updateBulkActions a global function
window.updateBulkActions = function() {
    const checkedCount = document.querySelectorAll('.account-checkbox:checked').length;
    const bulkActions = document.getElementById("bulkActions");
    const selectedCount = document.getElementById("selectedCount");

    if (bulkActions) {
        bulkActions.style.display = checkedCount > 0 ? 'flex' : 'none';
        if (selectedCount) selectedCount.textContent = checkedCount;
    }
};

// Enhanced form submission handler for all fields
function handleEditFormSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    
    // Get all form values
    const updateData = {
        user_id: formData.get('user_id'),
        first_name: formData.get('first_name'),
        last_name: formData.get('last_name'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        status: formData.get('status'),
        role: formData.get('role'),
        address: formData.get('address'),
        birthday: formData.get('birthday')
    };

    // Basic validation
    if (!updateData.user_id || !updateData.first_name || !updateData.last_name || !updateData.email) {
        alert('Please fill in all required fields.');
        return;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(updateData.email)) {
        alert('Please enter a valid email address.');
        return;
    }

    // Show loading state
    const submitBtn = document.querySelector('button[type="submit"][form="accountEditForm"]');
    if (!submitBtn) {
        console.error('Submit button not found');
        return;
    }
    
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span style="display: inline-block; width: 16px; height: 16px; border: 2px solid #ffffff; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite; margin-right: 8px;"></span> Saving...';

    // Send update request
    fetch('update_account.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(updateData).toString()
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const error = new Error(data.message || 'Failed to update account');
            error.data = data;
            throw error;
        }

        if (data.success) {
            // Show success message
            const successAlert = `
                <div style="background-color: #d1fae5; color: #065f46; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-check-circle"></i>
                    ${data.message || 'Account updated successfully!'}
                </div>
            `;

            // Insert the alert before the form
            form.insertAdjacentHTML('beforebegin', successAlert);

            // Close the modal after a short delay
            setTimeout(() => {
                window.closeModal();
                location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Error updating account');
        }
    })
    .catch(error => {
        console.error('Error:', error);

        // Show error message
        const errorMessage = error.data?.errors ?
            Object.values(error.data.errors).flat().join('<br>') :
            (error.message || 'An error occurred while updating the account');

        const errorAlert = `
            <div style="background-color: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-exclamation-circle"></i>
                ${errorMessage}
            </div>
        `;

        form.insertAdjacentHTML('beforebegin', errorAlert);
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Initialize everything when DOM is loaded
document.addEventListener("DOMContentLoaded", function() {
    console.log("accounts.js loaded and DOM fully loaded");

    // Add CSS animation for spinner
    if (!document.getElementById('spinner-styles')) {
        const style = document.createElement('style');
        style.id = 'spinner-styles';
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    }

    // DOM Elements
    const selectAll = document.getElementById("selectAll");
    const clearSelection = document.getElementById("clearSelection");
    const addAccountButton = document.getElementById("addAccountButton");
    const addAccountModal = document.getElementById("addAccountModal");

    // Initialize add account button
    if (addAccountButton && addAccountModal) {
        addAccountButton.addEventListener('click', function() {
            const addAccountForm = document.getElementById('addAccountForm');
            if (addAccountForm) {
                addAccountForm.reset();
            }
            window.showModal(addAccountModal);
        });
    }

    // FIXED EVENT DELEGATION - More specific button detection
    document.addEventListener('click', function(e) {
        // Ignore clicks from sidebar
        if (e.target.closest('.sidebar')) {
            return;
        }
        
        // Handle close button clicks
        if (e.target.matches('.close-button, .close-modal') || 
            e.target.closest('.close-button, .close-modal') ||
            e.target.classList.contains('fa-times')) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Close button clicked, closing modal');
            window.closeModal();
            return;
        }
        
        // More specific button detection
        let clickedElement = e.target;
        let buttonType = null;
        let userId = null;
        
        // Check if we clicked on an icon first
        if (clickedElement.classList.contains('fa-eye')) {
            buttonType = 'view';
            clickedElement = clickedElement.closest('button, a, .action-button');
        } else if (clickedElement.classList.contains('fa-pen') || clickedElement.classList.contains('fa-edit')) {
            buttonType = 'edit';
            clickedElement = clickedElement.closest('button, a, .action-button');
        }
        
        // If we didn't find a button type from icon, check the button itself
        if (!buttonType && clickedElement) {
            if (clickedElement.classList.contains('view-button') || 
                clickedElement.classList.contains('view-account-btn') ||
                clickedElement.querySelector('.fa-eye')) {
                buttonType = 'view';
            } else if (clickedElement.classList.contains('edit-button') || 
                       clickedElement.classList.contains('edit-account-btn') ||
                       clickedElement.querySelector('.fa-pen, .fa-edit')) {
                buttonType = 'edit';
            }
        }
        
        // Get user ID if we found a button
        if (buttonType && clickedElement) {
            userId = clickedElement.getAttribute('data-user-id') ||
                     clickedElement.getAttribute('data-id') ||
                     clickedElement.closest('[data-user-id]')?.getAttribute('data-user-id') ||
                     clickedElement.closest('[data-id]')?.getAttribute('data-id') ||
                     clickedElement.closest('tr')?.getAttribute('data-user-id') ||
                     clickedElement.closest('tr')?.getAttribute('data-id');
        }
        
        // Execute the appropriate action
        if (buttonType && userId) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log(`${buttonType} button clicked for user ID:`, userId);
            
            if (buttonType === 'view') {
                showCustomerDetails(userId);
            } else if (buttonType === 'edit') {
                showEditModal(userId);
            }
        } else if (buttonType && userId) {
            console.error(`${buttonType} button found but no user ID`);
            console.log('Button element:', clickedElement);
            alert('Error: Could not find user ID. Please refresh the page and try again.');
        }
    });

    // Bulk selection handlers
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            const accountCheckboxes = document.querySelectorAll('.account-checkbox');
            accountCheckboxes.forEach(checkbox => {
                if (checkbox !== selectAll) {
                    checkbox.checked = isChecked;
                }
            });
            window.updateBulkActions();
        });
    }

    const accountCheckboxes = document.querySelectorAll('.account-checkbox');
    accountCheckboxes.forEach(checkbox => {
        if (checkbox !== selectAll) {
            checkbox.addEventListener('change', function() {
                window.updateBulkActions();
                if (selectAll) {
                    const allSelected = Array.from(accountCheckboxes)
                        .filter(cb => cb !== selectAll)
                        .every(cb => cb.checked);
                    selectAll.checked = allSelected;
                }
            });
        }
    });

    if (clearSelection) {
        clearSelection.addEventListener('click', () => {
            if (selectAll) selectAll.checked = false;
            accountCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            window.updateBulkActions();
        });
    }

    window.updateBulkActions();
    console.log('All event listeners initialized successfully');
});