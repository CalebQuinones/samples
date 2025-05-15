console.log('products.js loaded');
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    const modalOverlay = document.getElementById('modalOverlay');
    
    // Initialize modal overlay
    if (modalOverlay) {
        modalOverlay.style.display = 'none';
        modalOverlay.style.visibility = 'hidden';
    }

    // DOM Elements
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const availabilityFilter = document.getElementById('availabilityFilter');
    const selectAllCheckbox = document.getElementById('selectAll');
    const productModal = document.getElementById('productModal');
    const editProductModal = document.getElementById('editProductModal');
    const addProductBtn = document.getElementById('addProductButton');
    const closeProductModal = document.getElementById('closeProductModal');
    const cancelProduct = document.getElementById('cancelProduct');
    const closeEditProduct = document.getElementById('closeEditProduct');
    const cancelEditProduct = document.getElementById('cancelEditProduct');
    const productsTableBody = document.getElementById('productsTableBody');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    const clearSelection = document.getElementById('clearSelection');
    const sortBy = document.getElementById('sortBy');

    // Guard: Only run if main elements exist
    if (!addProductBtn) {
        console.warn('Products JS: Add Product button not found.');
    }
    if (!productModal) {
        console.warn('Products JS: Add Product modal not found.');
    }
    if (!modalOverlay) {
        console.warn('Products JS: Modal overlay not found.');
    }
    // Don't return early: allow partial functionality if some elements are missing.

    // Debug logging for all main DOM elements
    if (searchInput) console.log('searchInput:', searchInput);
    else console.warn('searchInput not found');
    if (categoryFilter) console.log('categoryFilter:', categoryFilter);
    else console.warn('categoryFilter not found');
    if (availabilityFilter) console.log('availabilityFilter:', availabilityFilter);
    else console.warn('availabilityFilter not found');
    if (selectAllCheckbox) console.log('selectAllCheckbox:', selectAllCheckbox);
    else console.warn('selectAllCheckbox not found');
    if (productModal) console.log('productModal:', productModal);
    else console.warn('productModal not found');
    if (editProductModal) console.log('editProductModal:', editProductModal);
    else console.warn('editProductModal not found');
    if (addProductBtn) console.log('addProductBtn:', addProductBtn);
    else console.warn('addProductBtn not found');
    if (closeProductModal) console.log('closeProductModal:', closeProductModal);
    else console.warn('closeProductModal not found');
    if (cancelProduct) console.log('cancelProduct:', cancelProduct);
    else console.warn('cancelProduct not found');
    if (closeEditProduct) console.log('closeEditProduct:', closeEditProduct);
    else console.warn('closeEditProduct not found');
    if (cancelEditProduct) console.log('cancelEditProduct:', cancelEditProduct);
    else console.warn('cancelEditProduct not found');
    if (productsTableBody) console.log('productsTableBody:', productsTableBody);
    else console.warn('productsTableBody not found');

    // Event Listeners (all guarded)
    if (searchInput) searchInput.addEventListener('input', filterProducts);
    if (categoryFilter) categoryFilter.addEventListener('change', filterProducts);
    if (availabilityFilter) availabilityFilter.addEventListener('change', filterProducts);
    if (selectAllCheckbox) selectAllCheckbox.addEventListener('change', toggleSelectAll);
    if (addProductBtn) addProductBtn.addEventListener('click', () => showModal(productModal));
    if (closeProductModal) closeProductModal.addEventListener('click', () => closeModal());
    if (cancelProduct) cancelProduct.addEventListener('click', () => closeModal());
    if (closeEditProduct) closeEditProduct.addEventListener('click', () => closeModal());
    if (cancelEditProduct) cancelEditProduct.addEventListener('click', () => closeModal());

    // Close modal when clicking outside
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });
    }

    // Close modal when clicking close button
    const closeButtons = document.querySelectorAll('.close-modal');
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            closeModal();
        });
    });

    // Modal handling functions
    window.showModal = function(modal) {
        if (!modal) return;
        
        // Reset any existing modals
        const allModals = document.querySelectorAll('.modal');
        allModals.forEach(m => {
            m.classList.remove('active');
            m.style.display = 'none';
        });

        // Show the modal overlay
        if (modalOverlay) {
            modalOverlay.style.display = 'flex';
            modalOverlay.style.visibility = 'visible';
            setTimeout(() => {
                modalOverlay.classList.add('active');
            }, 10);
        }

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
        const activeModals = document.querySelectorAll('.modal.active');
        if (activeModals.length === 0 && modalOverlay) {
            modalOverlay.classList.remove('active');
            setTimeout(() => {
                modalOverlay.style.display = 'none';
                modalOverlay.style.visibility = 'hidden';
            }, 300);
        }
    }

    function showAddProductModal() {
        console.log('Add Product button clicked!');
        if (!productModal) {
            console.error('productModal not found!');
            return;
        }
        showModal(productModal);
    }

    function showEditProductModal(productId) {
        fetch(`get_product.php?id=${productId}`)
            .then(response => response.json())
            .then(product => {
                if (editProductModal) {
                    // Fill in the form fields
                    const fields = ['name', 'category', 'price', 'status'];
                    fields.forEach(field => {
                        const input = document.getElementById(`edit-product-${field}`);
                        if (input) input.value = product[field];
                    });
                    
                    showModal(editProductModal);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load product details');
            });
    }

    function deleteProduct(productId) {
        fetch('delete_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                if (row) {
                    row.remove();
                    updateTable();
                }
            } else {
                alert('Error deleting product: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the product');
        });
    }

    // Filter products based on search input and filters
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const selectedAvailability = availabilityFilter.value;
        
        const rows = document.querySelectorAll('#productsTableBody tr');
        
        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
            const category = row.querySelector('td:nth-child(5)')?.textContent || '';
            const availability = row.querySelector('.status-badge')?.textContent || '';
            
            const matchesSearch = name.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            const matchesAvailability = !selectedAvailability || availability === selectedAvailability;
            
            row.style.display = matchesSearch && matchesCategory && matchesAvailability ? '' : 'none';
        });

        updatePagination();
    }

    // Toggle select all checkboxes
    function toggleSelectAll() {
        const isChecked = selectAllCheckbox.checked;
        productCheckboxes.forEach(checkbox => {
            if (checkbox !== selectAllCheckbox) {
                checkbox.checked = isChecked;
            }
        });
        updateBulkActions();
    }

    // Update bulk actions visibility
    function updateBulkActions() {
        const selectedCount = document.querySelectorAll('.product-checkbox:checked').length;
        
        if (bulkActions) {
            bulkActions.style.display = selectedCount > 0 ? 'flex' : 'none';
            if (selectedCountSpan) selectedCountSpan.textContent = selectedCount;
        }
    }

    // Update pagination
    function updatePagination() {
        const visibleRows = document.querySelectorAll('#productsTableBody tr:not([style*="display: none"])');
        const totalItems = visibleRows.length;
        const itemsPerPage = 8;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        
        // Update counts
        const elements = ['totalItems', 'startIndex', 'endIndex'].map(id => document.getElementById(id));
        if (elements[0]) elements[0].textContent = totalItems;
        if (elements[1]) elements[1].textContent = totalItems > 0 ? '1' : '0';
        if (elements[2]) elements[2].textContent = Math.min(itemsPerPage, totalItems);
        
        // Update pagination buttons
        const paginationNav = document.getElementById('paginationNav');
        if (!paginationNav) return;
        
        const currentPage = 1; // You can make this dynamic if needed
        
        let paginationHTML = `
            <button class="pagination-button pagination-button-prev" ${currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left"></i>
            </button>
        `;
        
        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += `
                <button class="pagination-button ${i === currentPage ? 'active' : ''}">${i}</button>
            `;
        }
        
        paginationHTML += `
            <button class="pagination-button pagination-button-next" ${currentPage === totalPages ? 'disabled' : ''}>
                <i class="fas fa-chevron-right"></i>
            </button>
        `;
        
        paginationNav.innerHTML = paginationHTML;
    }

    // Add event listener for bulk action buttons
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    // Add event listener for clear selection button
    if (clearSelection) {
        clearSelection.addEventListener('click', () => {
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateBulkActions();
        });
    }

    // Initial setup
    updatePagination();
    updateBulkActions();
});
