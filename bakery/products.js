// Define global variables at the top
let productCheckboxes;
let searchInput;
let categoryFilter;
let availabilityFilter;
let selectAllCheckbox;
let bulkActions;
let selectedCount;

console.log('products.js loaded');

// Define core modal functions at the top level
function showModal(modal) {
    console.log('showModal called', modal);
    const modalOverlay = document.getElementById('modalOverlay');
    if (!modal || !modalOverlay) {
        console.error('Modal or overlay not found');
        return;
    }
    
    // Hide all modals first
    document.querySelectorAll('.modal').forEach(m => {
        m.style.display = 'none';
        m.classList.remove('active');
    });
    
    // Show the overlay and specific modal
    modalOverlay.style.display = 'flex';
    modal.style.display = 'block';
    
    // Force reflow
    void modal.offsetHeight;
    
    // Add active classes
    modalOverlay.classList.add('active');
    modal.classList.add('active');
    
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modalOverlay = document.getElementById('modalOverlay');
    const activeModals = document.querySelectorAll('.modal.active');
    
    if (!modalOverlay) return;
    
    modalOverlay.classList.remove('active');
    activeModals.forEach(modal => {
        modal.classList.remove('active');
    });
    
    setTimeout(() => {
        modalOverlay.style.display = 'none';
        activeModals.forEach(modal => {
            modal.style.display = 'none';
        });
        document.body.style.overflow = '';
    }, 300);
}

// Make functions available globally
window.showModal = showModal;
window.closeModal = closeModal;

function showEditProductModal(productId) {
    const editProductModal = document.getElementById('editProductModal');
    if (!editProductModal) {
        console.error('Edit product modal not found');
        return;
    }
    
    console.log('Opening edit modal for product:', productId);
    
    // Show loading state
    const form = document.getElementById('editProductForm');
    if (form) {
        const saveBtn = form.querySelector('button[type="submit"]');
        if (saveBtn) {
            const originalBtnText = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        }
    }
    
    // Clear any existing image preview
    const previewContainer = document.getElementById('editImagePreview');
    if (previewContainer) {
        previewContainer.style.display = 'none';
    }
    
    // Clear file input
    const imageInput = document.getElementById('editProductImage');
    if (imageInput) {
        imageInput.value = '';
    }
    
    // Fetch product data
    fetch(`get_product.php?id=${productId}`)
        .then(async response => {
            const responseText = await response.text();
            console.log('Raw response:', responseText);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            try {
                return JSON.parse(responseText);
            } catch (err) {
                console.error('Failed to parse JSON:', err);
                throw new Error('Invalid JSON response from server');
            }
        })
        .then(data => {
            if (!data || typeof data !== 'object') {
                throw new Error('Invalid response format');
            }
            
            if (!data.success) {
                throw new Error(data.error || 'Failed to load product data');
            }
            
            const product = data.product;
            if (!product || !form) return;
            
            // Set form values
            form.querySelector('#editProductId').value = product.product_id || '';
            form.querySelector('#editProductName').value = product.name || '';
            form.querySelector('#editProductCategory').value = product.category || '';
            form.querySelector('#editProductPrice').value = product.price || '';
            form.querySelector('#editProductStatus').value = product.availability || 'In Stock';
            
            // Show existing image if available
            if (product.image) {
                const previewImg = document.getElementById('editPreviewImage');
                const previewContainer = document.getElementById('editImagePreview');
                if (previewImg && previewContainer) {
                    // Make sure the image path is absolute
                    const imageUrl = product.image.startsWith('http') ? product.image : 
                                   (product.image.startsWith('/') ? '' : '/') + product.image;
                    previewImg.src = imageUrl;
                    previewContainer.style.display = 'block';
                }
            }
            
            // Show the modal
            showModal(editProductModal);
        })
        .catch(error => {
            console.error('Error fetching product:', error);
            alert('Error: ' + error.message);
            showModal(editProductModal); // Show modal anyway
        })
        .finally(() => {
            // Reset button state
            const saveBtn = form ? form.querySelector('button[type="submit"]') : null;
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'Save Changes';
            }
        });
}

function updateStatusBadge(badge, status) {
    // Trim and normalize the status text
    const normalizedStatus = status.toString().trim().toLowerCase();
    
    // Update the displayed text with proper capitalization
    let displayText = '';
    switch(normalizedStatus) {
        case 'in stock':
            displayText = 'In Stock';
            break;
        case 'low stock':
            displayText = 'Low Stock';
            break;
        case 'out of stock':
            displayText = 'Out of Stock';
            break;
        default:
            displayText = status; // Fallback to original text if unknown status
    }
    
    // Update the badge content and classes
    badge.textContent = displayText;
    badge.className = 'status-badge';
    
    // Add the appropriate status class
    if (normalizedStatus.includes('in stock')) {
        badge.classList.add('status-in-stock');
    } else if (normalizedStatus.includes('low stock')) {
        badge.classList.add('status-low-stock');
    } else if (normalizedStatus.includes('out of stock')) {
        badge.classList.add('status-out-of-stock');
    }
}

function archiveProduct(productId) {
    if (!confirm('Are you sure you want to archive this product?')) return;
    
    fetch('archive_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`tr[data-product-id="${productId}"]`);
            if (row) {
                const statusBadge = row.querySelector('.status-badge');
                if (statusBadge) {
                    updateStatusBadge(statusBadge, 'Archived');
                }
            }
        }
    })
    .catch(error => console.error('Error:', error));
    
}

// Function to handle edit product form submission
function handleEditProductFormSubmit(e) {
    e.preventDefault();
    
    const form = document.getElementById('editProductForm');
    if (!form) {
        console.error('Edit form not found');
        return;
    }
    
    // Show loading state
    const saveBtn = document.getElementById('saveEditProduct');
    const originalBtnText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    const formData = new FormData(form);
    const jsonData = {};
    formData.forEach((value, key) => {
        jsonData[key] = value;
    });
            
    fetch('update_product.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(jsonData)
    })
    .then(async response => {
        const responseText = await response.text();
        console.log('Response status:', response.status);
        console.log('Response text:', responseText);
        
        let responseData;
        try {
            responseData = responseText ? JSON.parse(responseText) : {};
        } catch (e) {
            console.error('Failed to parse response as JSON:', e);
            throw new Error('Invalid response from server');
        }
        
        if (!response.ok) {
            const errorMessage = responseData.error || response.statusText || 'Request failed';
            console.error('Server responded with error:', errorMessage);
            throw new Error(errorMessage);
        }
        
        return responseData;
    })
    .then(data => {
        console.log('Success response:', data);
        if (data.success) {
            // Show success message
            alert('Product updated successfully!');
            // Close the modal
            closeModal();
            // Reload the page to show the updated product
            window.location.reload();
        } else {
            throw new Error(data.error || 'Failed to update product');
        }
    })
    .catch(error => {
        console.error('Error details:', {
            name: error.name,
            message: error.message,
            stack: error.stack
        });
        alert('Error: ' + error.message);
    })
    .finally(() => {
        // Re-enable the save button
        saveBtn.disabled = false;
        saveBtn.innerHTML = 'Save Changes';
    });
}

function handleAddProductFormSubmit(e) {
    e.preventDefault();
    
    const form = document.getElementById('addProductForm');
    const formData = new FormData();
    
    // Add form fields
    formData.append('name', document.getElementById('productName').value);
    formData.append('category', document.getElementById('productCategory').value);
    formData.append('price', document.getElementById('product-price').value);
    formData.append('status', document.getElementById('product-status').value);

    
    // Add image file if selected
    const imageInput = document.getElementById('productImage');
    if (imageInput.files.length > 0) {
        formData.append('productImage', imageInput.files[0]);
    }
    
    // Show loading state
    const saveBtn = document.getElementById('saveProduct');
    const originalBtnText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    // Send the data to the server
    fetch('add_product.php', {
        method: 'POST',
        body: formData,
        // Don't set Content-Type header, let the browser set it with the correct boundary
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Product added successfully!');
            // Close the modal
            closeModal();
            // Reload the page to show the new product
            window.location.reload();
        } else {
            // Show error message
            alert('Error adding product: ' + (data.error || 'Unknown error'));
            // Re-enable the save button
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the product');
        // Re-enable the save button
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalBtnText;
    });
}

// Function to handle image preview
function handleImagePreview(file, previewId = 'previewImage') {
    const preview = document.getElementById(previewId);
    const previewContainer = document.getElementById('imagePreview');
    const uploadArea = document.getElementById('imageUploadArea');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const reader = new FileReader();
    
    reader.onload = function(e) {
        // Update the preview image
        preview.src = e.target.result;
        
        // Show the preview container and hide the placeholder
        previewContainer.style.display = 'block';
        if (uploadPlaceholder) {
            uploadPlaceholder.style.display = 'none';
        }
        
        // Change cursor to default when hovering over the preview
        uploadArea.style.cursor = 'default';
    }
    
    reader.readAsDataURL(file);
}

// Function to handle file selection
function handleFileSelect(event, previewId = 'previewImage') {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file type
    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!validTypes.includes(file.type)) {
        alert('Please select a valid image file (JPEG, PNG, or GIF)');
        return;
    }
    
    // Validate file size (10MB)
    if (file.size > 10 * 1024 * 1024) {
        alert('Image size should not exceed 10MB');
        return;
    }
    
    handleImagePreview(file, previewId);
}

// Function to trigger file input click
document.addEventListener('DOMContentLoaded', function() {
    const imageUploadArea = document.getElementById('imageUploadArea');
    const fileInput = document.getElementById('productImage');
    
    if (imageUploadArea && fileInput) {
        imageUploadArea.addEventListener('click', function(e) {
            // Don't trigger file input if clicking on the preview image
            if (e.target.id !== 'previewImage') {
                fileInput.click();
            }
        });
    }
});

// Function to handle drag and drop
function setupDragAndDrop(dropZoneId, fileInputId) {
    const dropZone = document.getElementById(dropZoneId);
    const fileInput = document.getElementById(fileInputId);
    
    if (!dropZone || !fileInput) return;
    
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    // Highlight drop zone when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    // Handle dropped files
    dropZone.addEventListener('drop', handleDrop, false);
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function highlight() {
        dropZone.style.borderColor = '#9f7aea';
        dropZone.style.backgroundColor = '#faf5ff';
    }
    
    function unhighlight() {
        dropZone.style.borderColor = '';
        dropZone.style.backgroundColor = '';
    }
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            const file = files[0];
            // Create a new FileList with the dropped file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            
            // Trigger change event
            const event = new Event('change');
            fileInput.dispatchEvent(event);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    
    // Initialize DOM elements
    const modalOverlay = document.getElementById('modalOverlay');
    const productModal = document.getElementById('productModal');
    const editProductModal = document.getElementById('editProductModal');
    const addProductBtn = document.getElementById('addProductButton');
    const productImageInput = document.getElementById('productImage');
    const browseFilesBtn = document.getElementById('browseFiles');
    
    // Set up drag and drop for file upload
    setupDragAndDrop('imageUploadArea', 'productImage');
    
    // Handle click on browse files button
    if (browseFilesBtn) {
        browseFilesBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (productImageInput) {
                productImageInput.click();
            }
        });
    }
    
    // Handle file selection
    if (productImageInput) {
        productImageInput.addEventListener('change', function(e) {
            handleFileSelect(e);
        });
    }
    
    // Initialize global variables
    productCheckboxes = document.querySelectorAll('.product-checkbox');
    searchInput = document.getElementById('searchInput');
    categoryFilter = document.getElementById('categoryFilter');
    availabilityFilter = document.getElementById('availabilityFilter');
    selectAllCheckbox = document.getElementById('selectAll');
    bulkActions = document.getElementById('bulkActions');
    selectedCount = document.getElementById('selectedCount');
    const clearSelectionBtn = document.getElementById('clearSelection');

    console.log('Modal elements found:', {
        overlay: !!modalOverlay,
        productModal: !!productModal,
        editModal: !!editProductModal,
        addBtn: !!addProductBtn
    });

    // Initialize modal overlay
    if (modalOverlay) {
        modalOverlay.style.display = 'none';
        modalOverlay.style.visibility = 'visible';
        
        // Overlay click closes modal
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });
    }

    // Add Product Button
    if (addProductBtn && productModal) {
        addProductBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Add product button clicked');
            showModal(productModal);
        });
    } else {
        console.warn('Add product button or modal not found');
    }

    // Modal close buttons - use more specific selectors
    const closeButtons = [
        '#closeProductModal',
        '#cancelProduct', 
        '#closeEditProduct',
        '#cancelEditProduct'
    ];
    
    closeButtons.forEach(selector => {
        const button = document.querySelector(selector);
        if (button) {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('Close button clicked:', selector);
                closeModal();
            });
        }
    });

    // Prevent modal content clicks from closing the modal
    document.querySelectorAll('.modal-content').forEach(content => {
        content.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

    // Form submission handlers
    const editProductForm = document.getElementById('editProductForm');
    const saveProductBtn = document.getElementById('saveProduct');
    const saveEditProductBtn = document.getElementById('saveEditProduct');

    // Add event listener for the save product button
    if (saveProductBtn) {
        saveProductBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Save product clicked');
            
            // Trigger form submission
            handleAddProductFormSubmit(e);
            
            // The form submission is handled by handleAddProductFormSubmit
            // which will show feedback and reload the page on success
            
        });
    }

    if (saveEditProductBtn && editProductForm) {
        // Remove any existing click handlers
        const newSaveBtn = saveEditProductBtn.cloneNode(true);
        saveEditProductBtn.parentNode.replaceChild(newSaveBtn, saveEditProductBtn);
        
        // Add the new event listener to the new button
        newSaveBtn.addEventListener('click', handleEditProductFormSubmit);
        
        // Also add the submit handler to the form itself as a fallback
        editProductForm.addEventListener('submit', handleEditProductFormSubmit);
    }
    // Action button handlers - Fixed event delegation
    document.addEventListener('click', function(e) {
        const button = e.target.closest('button');
        if (!button) return;
        
        // Handle edit buttons
        if (button.classList.contains('edit-button') || button.querySelector('.fa-pen')) {
            e.preventDefault();
            const row = button.closest('tr');
            const productId = row?.dataset.productId;
            
            if (productId) {
                console.log('Edit button clicked for product:', productId);
                showEditProductModal(productId);
            }
        }
        
        // Handle archive buttons  
        if (button.classList.contains('archive-button') || button.querySelector('.fa-box-archive')) {
            e.preventDefault();
            const row = button.closest('tr');
            const productId = row?.dataset.productId;
            
            if (productId) {
                console.log('Archive button clicked for product:', productId);
                archiveProduct(productId);
            }
        }
    });

    // Initialize other functionality
    initializeFilters();
    initializeBulkActions();
    
    console.log('Products.js initialization complete');
});

// Filter and pagination functions
function initializeFilters() {
    let currentPage = 1;
    const itemsPerPage = 8;
    
    function filterProducts() {
        const productsTableBody = document.getElementById('productsTableBody');
        if (!productsTableBody) return;

        const searchTerm = searchInput?.value.toLowerCase() || '';
        const selectedCategory = categoryFilter?.value || '';
        const selectedAvailability = availabilityFilter?.value || '';
        
        const rows = productsTableBody.querySelectorAll('tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            // Skip the "no products" row
            if (row.querySelector('.no-products')) {
                row.style.display = 'none';
                return;
            }
            
            const name = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
            const category = row.querySelector('td:nth-child(5)')?.textContent || '';
            const availability = row.querySelector('.status-badge')?.textContent || '';
            
            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            const matchesAvailability = !selectedAvailability || availability === selectedAvailability;
            
            const isVisible = matchesSearch && matchesCategory && matchesAvailability;
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        updatePagination(visibleCount);
    }
    
    function updatePagination(totalItems) {
        const paginationNav = document.getElementById('paginationNav');
        if (!paginationNav) return;

        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const startIndex = totalItems > 0 ? ((currentPage - 1) * itemsPerPage) + 1 : 0;
        const endIndex = Math.min(startIndex + itemsPerPage - 1, totalItems);

        // Update pagination info
        const totalItemsEl = document.getElementById('totalItems');
        const startIndexEl = document.getElementById('startIndex');
        const endIndexEl = document.getElementById('endIndex');
        
        if (totalItemsEl) totalItemsEl.textContent = totalItems;
        if (startIndexEl) startIndexEl.textContent = startIndex;
        if (endIndexEl) endIndexEl.textContent = endIndex;
    }

    // Add event listeners for filters
    if (searchInput) searchInput.addEventListener('input', filterProducts);
    if (categoryFilter) categoryFilter.addEventListener('change', filterProducts);
    if (availabilityFilter) availabilityFilter.addEventListener('change', filterProducts);
    
    // Initial filter
    filterProducts();
}

function initializeBulkActions() {
    // Update checkbox references
    productCheckboxes = document.querySelectorAll('.product-checkbox');
    
    // Add event listeners to existing checkboxes
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
    
    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', toggleSelectAll);
    }
    
    // Clear selection
    const clearSelectionBtn = document.getElementById('clearSelection');
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', clearSelection);
    }
}

function toggleSelectAll() {
    if (!selectAllCheckbox) return;
    
    const isChecked = selectAllCheckbox.checked;
    const currentCheckboxes = document.querySelectorAll('.product-checkbox');
    
    currentCheckboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
    const checkedCount = checkedBoxes.length;
    
    if (bulkActions) {
        bulkActions.style.display = checkedCount > 0 ? 'flex' : 'none';
    }
    
    if (selectedCount) {
        selectedCount.textContent = checkedCount;
    }
}

function clearSelection() {
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
    }
    
    const currentCheckboxes = document.querySelectorAll('.product-checkbox');
    currentCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    updateBulkActions();
}