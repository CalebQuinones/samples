document.addEventListener('DOMContentLoaded', function() {
    var modalOverlay = document.getElementById('modalOverlay');
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

    // Guard: Only run if main elements exist
    if (!addProductBtn || !productModal || !modalOverlay) {
        console.warn('Products JS: Required elements not found. Script will not run.');
        return;
    }

    // Debug logging for all main DOM elements
    console.log('searchInput:', searchInput);
    console.log('categoryFilter:', categoryFilter);
    console.log('availabilityFilter:', availabilityFilter);
    console.log('selectAllCheckbox:', selectAllCheckbox);
    console.log('productModal:', productModal);
    console.log('editProductModal:', editProductModal);
    console.log('addProductBtn:', addProductBtn);
    console.log('closeProductModal:', closeProductModal);
    console.log('cancelProduct:', cancelProduct);
    console.log('closeEditProduct:', closeEditProduct);
    console.log('cancelEditProduct:', cancelEditProduct);
    console.log('productsTableBody:', productsTableBody);

    if (modalOverlay) {
        modalOverlay.style.display = 'none';
        modalOverlay.classList.remove('active');
    }
    document.body.style.overflow = 'auto';

    // Event Listeners (all guarded)
    if (searchInput) searchInput.addEventListener('input', filterProducts);
    if (categoryFilter) categoryFilter.addEventListener('change', filterProducts);
    if (availabilityFilter) availabilityFilter.addEventListener('change', filterProducts);
    if (selectAllCheckbox) selectAllCheckbox.addEventListener('change', toggleSelectAll);
    if (addProductBtn) addProductBtn.addEventListener('click', showAddProductModal);
    if (closeProductModal) closeProductModal.addEventListener('click', () => closeModal(productModal));
    if (cancelProduct) cancelProduct.addEventListener('click', () => closeModal(productModal));
    if (closeEditProduct) closeEditProduct.addEventListener('click', () => closeModal(editProductModal));
    if (cancelEditProduct) cancelEditProduct.addEventListener('click', () => closeModal(editProductModal));
    if (productsTableBody) {
        productsTableBody.addEventListener('click', function(e) {
            const target = e.target.closest('.action-button');
            if (!target) return;
            const productId = target.getAttribute('data-product-id');
            if (target.classList.contains('edit-button')) {
                showEditProductModal(productId);
            } else if (target.classList.contains('delete-button')) {
                if (confirm('Are you sure you want to delete this product?')) {
                    deleteProduct(productId);
                }
            }
        });
    }

    function showAddProductModal() {
        console.log('Add Product button clicked!');
        if (productModal) {
            productModal.style.display = 'block';
            if (modalOverlay) {
                modalOverlay.style.display = 'flex';
                modalOverlay.style.visibility = 'visible';
                setTimeout(() => {
                    productModal.classList.add('active');
                    modalOverlay.classList.add('active');
                }, 10);
            }
            document.body.style.overflow = 'hidden';
        }
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
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateBulkActions();
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
                    
                    editProductModal.style.display = 'block';
                    if (modalOverlay) {
                        modalOverlay.style.display = 'flex';
                        modalOverlay.style.visibility = 'visible';
                        setTimeout(() => {
                            editProductModal.classList.add('active');
                            modalOverlay.classList.add('active');
                        }, 10);
                    }
                    document.body.style.overflow = 'hidden';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load product details');
            });
    }

    function closeModal(modal) {
        if (modal) {
            modal.classList.remove('active');
            if (modalOverlay) {
                modalOverlay.classList.remove('active');
                setTimeout(() => {
                    modal.style.display = 'none';
                    modalOverlay.style.display = 'none';
                    modalOverlay.style.visibility = 'hidden';
                    hideOverlayIfNoModal();
                }, 300);
            }
        }
    }

    function hideOverlayIfNoModal() {
        const anyOpen = Array.from(document.querySelectorAll('.modal, .modal-container'))
            .some(m => m.classList.contains('active') || m.style.display === 'block' || m.style.display === 'flex');
        if (modalOverlay) {
            if (!anyOpen) {
                modalOverlay.style.display = 'none';
                modalOverlay.style.visibility = 'hidden';
                modalOverlay.classList.remove('active');
                document.body.style.overflow = 'auto';
            } else {
                modalOverlay.style.display = 'flex';
                modalOverlay.style.visibility = 'visible';
                modalOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }
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

    // Update bulk actions visibility
    function updateBulkActions() {
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.querySelectorAll('.product-checkbox:checked').length;
        
        if (bulkActions) {
            bulkActions.style.display = selectedCount > 0 ? 'flex' : 'none';
            const countSpan = document.getElementById('selectedCount');
            if (countSpan) countSpan.textContent = selectedCount;
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
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    // Add event listener for clear selection button
    const clearSelectionBtn = document.getElementById('clearSelection');
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', () => {
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            document.querySelectorAll('.product-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateBulkActions();
        });
    }

    // Initial setup
    updatePagination();
    updateBulkActions();
});