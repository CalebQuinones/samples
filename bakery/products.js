document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const availabilityFilter = document.getElementById('availabilityFilter');
    const selectAllCheckbox = document.getElementById('selectAll');
    const productModal = document.getElementById('productModal');
    const addProductBtn = document.getElementById('addProductBtn');
    const closeProductModal = document.getElementById('closeProductModal');
    const cancelProductUpdate = document.getElementById('cancelProductUpdate');

    // Event Listeners
    searchInput.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);
    availabilityFilter.addEventListener('change', filterProducts);
    selectAllCheckbox.addEventListener('change', toggleSelectAll);
    addProductBtn.addEventListener('click', showAddProductModal);
    if (closeProductModal) closeProductModal.addEventListener('click', closeModal);
    if (cancelProductUpdate) cancelProductUpdate.addEventListener('click', closeModal);

    // Add event listeners for edit and delete buttons using event delegation
    document.getElementById('productsTableBody').addEventListener('click', function(e) {
        const target = e.target.closest('.action-button');
        if (!target) return;

        const productId = target.dataset.productId;
        
        if (target.classList.contains('edit-button')) {
            showEditProductModal(productId);
        } else if (target.classList.contains('delete-button')) {
            confirmDeleteProduct(productId);
        }
    });

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

    // Show add product modal
    function showAddProductModal() {
        const modalTitle = document.getElementById('productModalTitle');
        if (modalTitle) modalTitle.textContent = 'Add Product';
        if (productModal) {
            productModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }

    // Show edit product modal
    function showEditProductModal(productId) {
        fetch(`get_product.php?id=${productId}`)
            .then(response => response.json())
            .then(product => {
                const modalTitle = document.getElementById('productModalTitle');
                if (modalTitle) modalTitle.textContent = 'Edit Product';
                
                // Fill in the form fields
                const fields = ['name', 'category', 'price', 'availability'];
                fields.forEach(field => {
                    const input = document.getElementById(`edit-product-${field}`);
                    if (input) input.value = product[field];
                });
                
                if (productModal) {
                    productModal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Close modal
    function closeModal() {
        if (productModal) {
            productModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // Confirm and delete product
    function confirmDeleteProduct(productId) {
        if (confirm('Are you sure you want to delete this product?')) {
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
                    location.reload();
                } else {
                    alert(data.message || 'Error deleting product');
                }
            })
            .catch(error => console.error('Error:', error));
        }
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
            selectAllCheckbox.checked = false;
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