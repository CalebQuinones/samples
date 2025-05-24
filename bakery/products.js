console.log('products.js loaded');

// Define core modal functions at the top level
function showModal(modal) {
    const modalOverlay = document.getElementById('modalOverlay');
    if (!modal || !modalOverlay) return;
    
    modalOverlay.style.display = 'flex';
    modal.style.display = 'block';
    
    // Force reflow
    void modal.offsetWidth;
    
    document.body.style.overflow = 'hidden';
    modalOverlay.classList.add('active');
    modal.classList.add('active');
}

function closeModal() {
    const modalOverlay = document.getElementById('modalOverlay');
    const activeModal = document.querySelector('.modal.active');
    if (!modalOverlay || !activeModal) return;
    
    modalOverlay.classList.remove('active');
    activeModal.classList.remove('active');
    
    setTimeout(() => {
        modalOverlay.style.display = 'none';
        activeModal.style.display = 'none';
        document.body.style.overflow = '';
    }, 300);
}

// Make functions available globally
window.showModal = showModal;
window.closeModal = closeModal;
window.showEditProductModal = function(productId) {
    const editProductModal = document.getElementById('editProductModal');
    if (!editProductModal) return;
    
    fetch(`get_product.php?id=${productId}`)
        .then(response => response.json())
        .then(product => {
            // Populate form fields
            document.getElementById('editProductId').value = product.product_id;
            document.getElementById('editProductName').value = product.name;
            document.getElementById('editProductCategory').value = product.category;
            document.getElementById('editProductPrice').value = product.price;
            document.getElementById('editProductDescription').value = product.description;
            document.getElementById('editProductStatus').value = product.status;
            
            showModal(editProductModal);
        })
        .catch(error => console.error('Error:', error));
};

window.archiveProduct = function(productId) {
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
                row.querySelector('.status-badge').textContent = 'Archived';
                row.querySelector('.status-badge').className = 'status-badge status-cancelled';
            }
        }
    })
    .catch(error => console.error('Error:', error));
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    
    // DOM Elements with error logging
    console.log('Initializing DOM elements...');
    const modalOverlay = document.getElementById('modalOverlay');
    const productModal = document.getElementById('productModal');
    const editProductModal = document.getElementById('editProductModal');
    const addProductBtn = document.getElementById('addProductButton');

    // Log modal-related elements
    console.log('Modal elements:', {
        overlay: modalOverlay,
        productModal: productModal,
        editModal: editProductModal,
        addBtn: addProductBtn
    });

    // Initialize modal overlay
    if (modalOverlay) {
        modalOverlay.style.display = 'none';
        modalOverlay.style.visibility = 'hidden';
    }

    // Add Product button event listener with proper modal handling
    if (addProductBtn && productModal) {
        addProductBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Add Product button clicked');
            if (!productModal) {
                console.error('Product modal not found');
                return;
            }
            showModal(productModal);
        });
    }

    // Cleanup existing event listeners and add new ones
    if (addProductBtn && productModal) {
        addProductBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            showModal(productModal);
        });
    }

    // DOM Elements - Add missing productModal
    const closeProductModal = document.getElementById('closeProductModal');
    const cancelProduct = document.getElementById('cancelProduct');
    const closeEditProduct = document.getElementById('closeEditProduct');
    const cancelEditProduct = document.getElementById('cancelEditProduct');
    const productsTableBody = document.getElementById('productsTableBody');
    const paginationNav = document.getElementById('paginationNav');

    // Guard: Only run if main elements exist
    if (!addProductBtn) {
        console.warn('Products JS: Add Product button not found.');
    }
    if (!modalOverlay) {
        console.warn('Products JS: Modal overlay not found.');
    }
    // Don't return early: allow partial functionality if some elements are missing.

    // Debug logging for all main DOM elements
    const debugElements = [
        'searchInput',
        'categoryFilter',
        'availabilityFilter',
        'selectAll',
        'productModal',
        'editProductModal',
        'addProductButton',
        'closeProductModal',
        'cancelProduct',
        'closeEditProduct',
        'cancelEditProduct',
        'productsTableBody'
    ];

    debugElements.forEach(id => {
        const el = document.getElementById(id);
        if (el) console.log(`${id}:`, el);
        else console.warn(`${id} not found`);
    });

    // Initialize global variables
    let productCheckboxes = document.querySelectorAll('.product-checkbox');
    let searchInput = document.getElementById('searchInput');
    let categoryFilter = document.getElementById('categoryFilter');
    let availabilityFilter = document.getElementById('availabilityFilter');
    let selectAllCheckbox = document.getElementById('selectAll');
    let bulkActions = document.getElementById('bulkActions');
    let selectedCount = document.getElementById('selectedCount');

    // Event Listeners (all guarded)
    if (searchInput) searchInput.addEventListener('input', filterProducts);
    if (categoryFilter) categoryFilter.addEventListener('change', filterProducts);
    if (availabilityFilter) availabilityFilter.addEventListener('change', filterProducts);
    if (selectAllCheckbox) selectAllCheckbox.addEventListener('change', toggleSelectAll);
    if (addProductBtn && productModal) {
        addProductBtn.addEventListener('click', () => {
            console.log('Opening add product modal');
            showModal(productModal);
        });
    }
    if (closeProductModal) closeProductModal.addEventListener('click', () => closeModal());
    if (cancelProduct) cancelProduct.addEventListener('click', () => closeModal());
    if (closeEditProduct) closeEditProduct.addEventListener('click', () => closeModal());
    if (cancelEditProduct) cancelEditProduct.addEventListener('click', () => closeModal());

    // Initialize productCheckboxes after DOM is loaded
    productCheckboxes = document.querySelectorAll('.product-checkbox');

    // Initialize state
    let currentPage = 1;
    const itemsPerPage = 8;

    // Improved filter function
    function filterProducts() {
        if (!productsTableBody) return;

        const searchTerm = searchInput?.value.toLowerCase() || '';
        const selectedCategory = categoryFilter?.value || '';
        const selectedAvailability = availabilityFilter?.value || '';
        
        const rows = productsTableBody.querySelectorAll('tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
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
        updateBulkActions();
    }

    // Improved pagination
    function updatePagination(totalItems) {
        if (!paginationNav) return;

        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const startIndex = totalItems > 0 ? ((currentPage - 1) * itemsPerPage) + 1 : 0;
        const endIndex = Math.min(startIndex + itemsPerPage - 1, totalItems);

        // Update count displays
        document.getElementById('totalItems')?.textContent = totalItems;
        document.getElementById('startIndex')?.textContent = startIndex;
        document.getElementById('endIndex')?.textContent = endIndex;

        // Generate pagination buttons
        let paginationHTML = `
            <button class="pagination-button pagination-button-prev" ${currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left"></i>
            </button>
        `;
        
        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += `
                <button class="pagination-button ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>
            `;
        }
        
        paginationHTML += `
            <button class="pagination-button pagination-button-next" ${currentPage === totalPages ? 'disabled' : ''}>
                <i class="fas fa-chevron-right"></i>
            </button>
        `;
        
        paginationNav.innerHTML = paginationHTML;

        // Add click handlers for pagination
        paginationNav.querySelectorAll('.pagination-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const page = e.target.dataset.page;
                if (page) {
                    currentPage = parseInt(page);
                    filterProducts();
                }
            });
        });
    }

    // Initialize event listeners
    function initializeEventListeners() {
        // Filter events
        searchInput?.addEventListener('input', filterProducts);
        categoryFilter?.addEventListener('change', filterProducts);
        availabilityFilter?.addEventListener('change', filterProducts);
        
        // Modal events
        addProductBtn?.addEventListener('click', () => showModal(productModal));
        closeProductModal?.addEventListener('click', closeModal);
        cancelProduct?.addEventListener('click', closeModal);
        closeEditProduct?.addEventListener('click', closeModal);
        cancelEditProduct?.addEventListener('click', closeModal);
        
        // Bulk action events
        selectAllCheckbox?.addEventListener('change', toggleSelectAll);
        clearSelection?.addEventListener('click', clearSelection);
    }

    // Initialize the page
    initializeEventListeners();
    filterProducts();
});

// Define these before DOMContentLoaded
let productCheckboxes;
let searchInput;
let categoryFilter;
let availabilityFilter;
let selectAllCheckbox;
let bulkActions;
let selectedCount;

function filterProducts() {
    if (!searchInput || !categoryFilter || !availabilityFilter) return;
    
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
    if (!selectAllCheckbox || !productCheckboxes) return;
    
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
    const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
    
    if (bulkActions) {
        bulkActions.style.display = checkedCount > 0 ? 'flex' : 'none';
        if (selectedCount) {
            selectedCount.textContent = checkedCount;
        }
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

// Add event listeners for edit and archive buttons
document.addEventListener('click', function(e) {
    if (e.target.closest('.edit-button')) {
        const productId = e.target.closest('tr')?.dataset.productId;
        if (productId) showEditProductModal(productId);
    }
    if (e.target.closest('.archive-button')) {
        const productId = e.target.closest('tr')?.dataset.productId;
        if (productId) archiveProduct(productId);
    }
});

// Initial setup
updatePagination();
updateBulkActions();
