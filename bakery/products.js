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
    
    // For now, just show the modal with empty form
    // You'll need to implement get_product.php to fetch actual data
    console.log('Opening edit modal for product:', productId);
    
    // Set the product ID in the hidden field
    const productIdField = document.getElementById('editProductId');
    if (productIdField) {
        productIdField.value = productId;
    }
    
    showModal(editProductModal);
    

    
    fetch(`get_product.php?id=${productId}`)
        .then(response => response.json())
        .then(product => {
            const form = document.getElementById('editProductForm');
            if (!form) return;
            
            form.querySelector('#editProductId').value = product.product_id;
            form.querySelector('#editProductName').value = product.name;
            form.querySelector('#editProductCategory').value = product.category;
            form.querySelector('#editProductPrice').value = product.price;
            form.querySelector('#editProductDescription').value = product.description || '';
            form.querySelector('#editProductStatus').value = product.status || 'active';
            
            showModal(editProductModal);
        })
        .catch(error => {
            console.error('Error fetching product:', error);
            showModal(editProductModal); // Show modal anyway
        });
    
}

function archiveProduct(productId) {
    if (!confirm('Are you sure you want to archive this product?')) return;
    
    // For now, just update the UI
    console.log('Archiving product:', productId);
    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
    if (row) {
        const statusBadge = row.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.textContent = 'Archived';
            statusBadge.className = 'status-badge status-cancelled';
        }
    }
    

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
                    statusBadge.textContent = 'Archived';
                    statusBadge.className = 'status-badge status-cancelled';
                }
            }
        }
    })
    .catch(error => console.error('Error:', error));
    
}

// Make functions available to the global scope
window.showEditProductModal = showEditProductModal;
window.archiveProduct = archiveProduct;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    
    // Initialize DOM elements
    const modalOverlay = document.getElementById('modalOverlay');
    const productModal = document.getElementById('productModal');
    const editProductModal = document.getElementById('editProductModal');
    const addProductBtn = document.getElementById('addProductButton');

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
    const addProductForm = document.getElementById('addProductForm');
    const editProductForm = document.getElementById('editProductForm');
    const saveProductBtn = document.getElementById('saveProduct');
    const saveEditProductBtn = document.getElementById('saveEditProduct');

    if (saveProductBtn && addProductForm) {
        saveProductBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Save product clicked');
            
            // For now, just close the modal and reset form
            closeModal();
            addProductForm.reset();
            

            const formData = new FormData(addProductForm);
            
            fetch('add_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    addProductForm.reset();
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
            
        });
    }

    if (saveEditProductBtn && editProductForm) {
        saveEditProductBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Save edit product clicked');
            
            // For now, just close the modal
            closeModal();
            editProductForm.reset();
            

            const formData = new FormData(editProductForm);
            
            fetch('update_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    editProductForm.reset();
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
            
        });
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