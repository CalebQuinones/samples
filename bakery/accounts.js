// Global functions for account management
function showCustomerDetails(userId) {
    fetch(`./get_customer_details.php?user_id=${userId}`)
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
    console.log('showEditModal called with userId:', userId);
    
    // Get the modal
    const accountEditModal = document.getElementById('accountEditModal');
    if (!accountEditModal) {
        console.error('Edit modal not found');
        return;
    }
    
    // Show loading state immediately
    const modalBody = accountEditModal.querySelector('.modal-body');
    const originalBodyContent = modalBody.innerHTML;
    
    modalBody.innerHTML = `
        <div style="padding: 40px; text-align: center; min-height: 150px; display: flex; align-items: center; justify-content: center;">
            <div>
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading account details...</p>
            </div>
        </div>`;
    
    // Show the modal immediately with loading state
    window.showModal(accountEditModal);
    
    // Fetch account details
    fetch(`./get_account.php?id=${userId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(account => {
            console.log('Account data received:', account);
            
            // Restore original modal body content
            modalBody.innerHTML = originalBodyContent;
            
            // Set the user_id field
            const userIdField = document.getElementById('user_id');
            if (!userIdField) {
                console.error('User ID field not found');
                throw new Error('Required form elements not found');
            }
            userIdField.value = account.user_id;

            // Set the status, defaulting to 'active' if not set
            const status = account.status || 'active';
            const statusSelect = document.getElementById('editStatus');
            if (!statusSelect) {
                console.error('Status select element not found');
                throw new Error('Required form elements not found');
            }
            
            // Set the selected status
            statusSelect.value = status;

            // Update the modal title to show which account is being edited
            const modalTitle = accountEditModal.querySelector('.modal-title');
            if (modalTitle) {
                const fullName = `${account.Fname || ''} ${account.Lname || ''}`.trim() || 'User';
                modalTitle.textContent = `Update Status for ${fullName}`;
            }
            
            // Reinitialize the form submit handler
            const form = document.getElementById('accountEditForm');
            if (form) {
                // Remove existing event listeners by cloning the form
                const newForm = form.cloneNode(true);
                form.parentNode.replaceChild(newForm, form);
                
                // Add new event listener
                newForm.addEventListener('submit', handleEditFormSubmit);
            }
            
            // Reinitialize close button handlers
            initializeCloseButtons(accountEditModal);
        })
        .catch(error => {
            console.error('Error in showEditModal:', error);
            alert('Failed to load account details. Please try again. ' + error.message);
            window.closeModal();
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

// Global variables to store event handlers for proper removal
let currentModalOverlayClickHandler = null;
let currentKeydownHandler = null;

// Global modal functions
window.showModal = function(modal) {
    if (!modal) {
        console.error('Modal not found');
        return;
    }

    const modalOverlay = document.getElementById('modalOverlay');
    if (!modalOverlay) {
        console.error('Modal overlay not found');
        return;
    }

    console.log('Showing modal:', modal.id);

    // Hide all other modals first
    document.querySelectorAll('.modal').forEach(m => {
        if (m !== modal) {
            m.classList.remove('active');
            m.style.display = 'none';
            m.setAttribute('aria-hidden', 'true');
        }
    });

    // Show the overlay
    modalOverlay.style.display = 'flex';
    
    // Show the modal
    modal.style.display = 'block';
    
    // Trigger reflow to enable CSS transitions
    void modal.offsetHeight;
    
    // Add active classes to trigger the transition
    modalOverlay.classList.add('active');
    modal.classList.add('active');
    
    // Set modal attributes for accessibility
    modal.setAttribute('aria-hidden', 'false');
    modal.setAttribute('tabindex', '-1');
    
    // Handle body scroll
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = window.innerWidth > document.documentElement.clientWidth ? '15px' : '0';
    
    // Set focus to the modal for better accessibility
    setTimeout(() => {
        const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (focusable) focusable.focus();
        else modal.focus();
    }, 100);
    
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
    if (currentModalOverlayClickHandler) {
        modalOverlay.removeEventListener('click', currentModalOverlayClickHandler);
    }
    if (currentKeydownHandler) {
        document.removeEventListener('keydown', currentKeydownHandler);
    }
    
    // Store and add new listeners
    currentModalOverlayClickHandler = handleClickOutside;
    currentKeydownHandler = handleEscape;
    
    modalOverlay.addEventListener('click', handleClickOutside, { passive: false });
    document.addEventListener('keydown', handleEscape, { passive: false });
    
    // Initialize close buttons
    initializeCloseButtons(modal);
    
    console.log('Modal shown:', {
        id: modal.id,
        display: window.getComputedStyle(modal).display,
        visibility: window.getComputedStyle(modal).visibility,
        opacity: window.getComputedStyle(modal).opacity,
        zIndex: window.getComputedStyle(modal).zIndex
    });
};

window.closeModal = function() {
    console.log('closeModal called');
    
    const modalOverlay = document.getElementById('modalOverlay');
    const activeModal = document.querySelector('.modal.active');

    if (!modalOverlay) {
        console.log('No modal overlay found');
        return;
    }

    // If already hidden, do nothing
    if (!modalOverlay.classList.contains('active')) {
        return;
    }

    // Remove active classes to trigger the transition
    modalOverlay.classList.remove('active');
    if (activeModal) {
        activeModal.classList.remove('active');
    }

    // Clean up event listeners first to prevent multiple triggers
    if (currentModalOverlayClickHandler) {
        modalOverlay.removeEventListener('click', currentModalOverlayClickHandler);
        currentModalOverlayClickHandler = null;
    }
    if (currentKeydownHandler) {
        document.removeEventListener('keydown', currentKeydownHandler);
        currentKeydownHandler = null;
    }

    // Clean up after the transition
    const cleanup = () => {
        // Hide elements
        modalOverlay.style.display = 'none';
        if (activeModal) {
            activeModal.style.display = 'none';
        }
        
        // Re-enable body scroll and reset padding
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        console.log('Modal closed successfully');
    };
    
    // Use requestAnimationFrame to ensure the transition starts
    requestAnimationFrame(() => {
        // Handle the transition end event
        const onTransitionEnd = () => {
            modalOverlay.removeEventListener('transitionend', onTransitionEnd);
            cleanup();
        };
        
        modalOverlay.addEventListener('transitionend', onTransitionEnd, { once: true });
        
        // Fallback in case transitionend doesn't fire
        setTimeout(() => {
            modalOverlay.removeEventListener('transitionend', onTransitionEnd);
            cleanup();
        }, 300);
    });
};

// Helper function to initialize close buttons
function initializeCloseButtons(modal) {
    // Get all possible close buttons in the modal
    const closeSelectors = [
        '.close-button',
        '[id*="close"]',
        '[id*="cancel"]',
        '[data-dismiss="modal"]',
        '[onclick*="closeModal"]'
    ];
    
    closeSelectors.forEach(selector => {
        const closeButtons = modal.querySelectorAll(selector);
        closeButtons.forEach(btn => {
            // Remove any existing click handlers
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            
            // Add new click handler
            newBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                window.closeModal();
                return false;
            });
        });
    });
}

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

// Handle form submission for editing an account
function handleEditFormSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const status = formData.get('status');
    const userId = formData.get('user_id');

    if (!userId || !status) {
        alert('Invalid form data');
        return;
    }

    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

    fetch('update_account.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `user_id=${encodeURIComponent(userId)}&status=${encodeURIComponent(status)}`
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
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    ${data.message}
                </div>
            `;

            // Insert the alert before the form
            form.insertAdjacentHTML('beforebegin', successAlert);

            // Reset form
            form.reset();

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

        // Show error message in a more user-friendly way
        const errorMessage = error.data?.errors ?
            Object.values(error.data.errors).flat().join('<br>') :
            (error.message || 'An error occurred while updating the account');

        showFormError(form, errorMessage);
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Helper function to show error messages under form fields
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    // Remove any existing error for this field
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }

    // Add error message
    const errorElement = document.createElement('div');
    errorElement.className = 'error-message';
    errorElement.style.color = '#dc3545';
    errorElement.style.fontSize = '0.875rem';
    errorElement.style.marginTop = '0.25rem';
    errorElement.textContent = message;

    field.parentNode.insertBefore(errorElement, field.nextSibling);
    field.focus();
    field.classList.add('is-invalid');
}

// Helper function to show form-wide error messages
function showFormError(form, message) {
    // Remove any existing error messages
    const existingAlert = form.previousElementSibling;
    if (existingAlert && existingAlert.classList.contains('alert')) {
        existingAlert.remove();
    }

    // Add error message
    const errorAlert = document.createElement('div');
    errorAlert.className = 'alert alert-danger';
    errorAlert.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        ${message}
    `;

    form.insertAdjacentElement('beforebegin', errorAlert);

    // Scroll to the error message
    errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Handle form submission for adding a new account
function handleAddAccountForm(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');

    // Add null check for submit button
    if (!submitBtn) {
        console.error('Submit button not found in form');
        return;
    }

    const originalBtnText = submitBtn.innerHTML;

    // Client-side validation
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');

    // Clear previous error messages
    document.querySelectorAll('.error-message').forEach(el => el.remove());

    let isValid = true;

    // Validate password match
    if (password !== confirmPassword) {
        showError('confirm_password', 'Passwords do not match');
        isValid = false;
    }

    // Validate password strength
    if (password.length < 8) {
        showError('password', 'Password must be at least 8 characters long');
        isValid = false;
    } else if (!/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
        showError('password', 'Password must contain at least one uppercase letter and one number');
        isValid = false;
    }

    // Validate email format
    const email = formData.get('email');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showError('email', 'Please enter a valid email address');
        isValid = false;
    }

    if (!isValid) {
        return;
    }

    // Disable submit button to prevent double submission
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...';

    // Get all form data
    const formDataObj = {};

    // Convert FormData to object and handle checkboxes
    for (let [key, value] of formData.entries()) {
        // Skip confirm_password field as it's not needed in the backend
        if (key === 'confirm_password') continue;

        // Handle checkboxes
        if (formData.getAll(key).length > 1) {
            if (!formDataObj[key]) {
                formDataObj[key] = [];
            }
            formDataObj[key].push(value);
        } else {
            formDataObj[key] = value;
        }
    }

    // Set status based on checkbox
    formDataObj['status'] = formDataObj['status'] || 'inactive';

    // Clear any previous error messages
    const errorMessages = form.querySelectorAll('.alert');
    errorMessages.forEach(el => el.remove());

    // Get CSRF token from form
    const csrfToken = form.querySelector('input[name="csrf_token"]').value;

    fetch('add_account.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': csrfToken
        },
        body: new URLSearchParams(formDataObj).toString()
    })
    .then(async response => {
        // First, get the response text for debugging
        const responseText = await response.text();
        console.log('Raw response:', responseText);

        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('Failed to parse response as JSON:', e);
            console.error('Response headers:', Object.fromEntries([...response.headers]));
            throw new Error(`Invalid JSON response from server: ${responseText.substring(0, 100)}...`);
        }

        if (!response.ok) {
            const error = new Error(data.message || `Server responded with status ${response.status}`);
            error.data = data;
            error.status = response.status;
            throw error;
        }

        if (!data || typeof data !== 'object') {
            throw new Error('Invalid response format from server');
        }

        return data;
    })
    .then(data => {
        if (data.success) {
            // Show success message
            const successHtml = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    ${data.message || 'Account created successfully!'}
                </div>`;

            form.innerHTML = successHtml;

            // Close the modal and refresh the page after a short delay
            setTimeout(() => {
                window.closeModal();
                location.reload();
            }, 2000);
        } else {
            throw new Error(data.message || 'Error creating account');
        }
    })
    .catch(error => {
        console.error('Error:', error);

        // Show error message in a user-friendly way
        const errorMessage = error.data?.message || error.message || 'An error occurred while creating the account';
        const errorHtml = `
            <div class="alert alert-danger error-message">
                <i class="fas fa-exclamation-circle me-2"></i>
                ${errorMessage}
            </div>`;

        // Insert error message at the top of the form
        form.insertAdjacentHTML('afterbegin', errorHtml);

        // Scroll to the error message
        const errorElement = form.querySelector('.error-message');
        if (errorElement) {
            errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    })
    .finally(() => {
        // Re-enable the submit button
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
}

// Initialize everything when DOM is loaded
document.addEventListener("DOMContentLoaded", function() {
    console.log("accounts.js loaded");
    console.log("DOM fully loaded");

    // DOM Elements
    const selectAll = document.getElementById("selectAll");
    const clearSelection = document.getElementById("clearSelection");
    const addAccountButton = document.getElementById("addAccountButton");
    const addAccountModal = document.getElementById("addAccountModal");
    const addAccountForm = document.getElementById('addAccountForm');

    // Initialize form handlers
    function initFormHandlers() {
        // Remove any existing event listeners from addAccountForm
        if (addAccountForm) {
            const newForm = addAccountForm.cloneNode(true);
            addAccountForm.parentNode.replaceChild(newForm, addAccountForm);
            newForm.addEventListener('submit', handleAddAccountForm);
        }
    }

    // Initialize add account button
    if (addAccountButton && addAccountModal) {
        addAccountButton.addEventListener('click', function() {
            if (addAccountForm) {
                addAccountForm.reset();
            }
            window.showModal(addAccountModal);
        });
    }

    // Initialize all form handlers
    initFormHandlers();

    // Add event delegation for edit buttons
    console.log('Setting up click event listener for edit buttons');
    document.addEventListener('click', function(e) {
        console.log('Click event triggered on:', e.target);
        
        // Ignore clicks that originate from the sidebar
        if (e.target.closest('.sidebar')) {
            console.log('Click originated from sidebar, ignoring');
            return;
        }
        
        // Handle close button clicks
        if (e.target.closest('.close-button') || 
            e.target.classList.contains('fa-times') ||
            e.target.id === 'closeEditModal' ||
            e.target.id === 'cancelEdit' ||
            e.target.id === 'cancelModal') {
            e.preventDefault();
            e.stopPropagation();
            console.log('Close button clicked, closing modal');
            window.closeModal();
            return;
        }
        
        // Check if the clicked element is an edit button
        let editBtn = null;
        
        // Check all possible ways the edit button could be clicked
        const checkEditButton = (element) => {
            if (!element) return null;
            
            // Direct click on edit button
            if (element.classList && 
                (element.classList.contains('edit-account-btn') || 
                 element.classList.contains('edit-button') ||
                 element.closest('.edit-account-btn') ||
                 element.closest('.edit-button'))) {
                return element.classList.contains('edit-account-btn') || element.classList.contains('edit-button') ? 
                       element : 
                       (element.closest('.edit-account-btn') || element.closest('.edit-button'));
            }
            
            // Click on icon inside edit button
            if (element.classList && 
                (element.classList.contains('fa-pen') || 
                 element.querySelector('.fa-pen'))) {
                const penIcon = element.classList.contains('fa-pen') ? element : element.querySelector('.fa-pen');
                return penIcon.closest('.edit-account-btn') || penIcon.closest('.edit-button') || penIcon.closest('.action-button');
            }
            
            return null;
        };
        
        // Check the clicked element and its parents
        let currentElement = e.target;
        while (currentElement && currentElement !== document.documentElement) {
            editBtn = checkEditButton(currentElement);
            if (editBtn) break;
            currentElement = currentElement.parentElement;
        }
        
        if (editBtn) {
            console.log('Edit button found:', editBtn);
            e.preventDefault();
            e.stopPropagation();
            
            // Get user ID from data attribute or closest parent with data-user-id
            let userId = editBtn.getAttribute('data-user-id');
            if (!userId) {
                const parentWithId = editBtn.closest('[data-user-id]');
                if (parentWithId) {
                    userId = parentWithId.getAttribute('data-user-id');
                }
            }
            
            if (userId) {
                console.log('Edit button clicked for user ID:', userId);
                showEditModal(userId);
            } else {
                console.error('No user ID found on edit button or its parents');
                console.log('Edit button HTML:', editBtn.outerHTML);
                console.log('Edit button parents:', Array.from(document.elementsFromPoint(e.clientX, e.clientY)));
            }
        } else {
            console.log('No edit button found in click path');
        }
    });

    // Event Listeners for other elements
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

    // Add change event listeners to individual checkboxes
    const accountCheckboxes = document.querySelectorAll('.account-checkbox');
    accountCheckboxes.forEach(checkbox => {
        if (checkbox !== selectAll) {
            checkbox.addEventListener('change', function() {
                window.updateBulkActions();
                // Update select all checkbox state
                if (selectAll) {
                    const allSelected = Array.from(accountCheckboxes)
                        .filter(cb => cb !== selectAll)
                        .every(cb => cb.checked);
                    selectAll.checked = allSelected;
                }
            });
        }
    });

    // Clear selection button
    if (clearSelection) {
        clearSelection.addEventListener('click', () => {
            if (selectAll) selectAll.checked = false;
            accountCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            window.updateBulkActions();
        });
    }

    // Initialize on page load
    window.updateBulkActions();
});