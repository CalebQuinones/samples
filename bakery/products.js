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
    console.log('showModal called', modal); // Debug line
    const modalOverlay = document.getElementById('modalOverlay');
    if (!modal || !modalOverlay) {
        console.error('Modal or overlay not found');
        return;
    }
    
    // Reset any existing styles
    modal.style.display = 'block';
    modalOverlay.style.display = 'flex';
    
    // Force reflow
    void modal.offsetHeight;
    
    // Add active classes
    modalOverlay.classList.add('active');
    modal.classList.add('active');
    
    document.body.style.overflow = 'hidden';
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

// Make these functions available globally at the top of the file
function showEditProductModal(productId) {
    const editProductModal = document.getElementById('editProductModal');
    if (!editProductModal) return;
    
    fetch(`get_product.php?id=${productId}`)
        .then(response => response.json())
        .then(product => {
            const form = document.getElementById('editProductForm');
            if (!form) return;
            
            // Update hidden ID field and other form fields
            form.querySelector('#editProductId').value = product.product_id;
            form.querySelector('#editProductName').value = product.name;
            form.querySelector('#editProductCategory').value = product.category;
            form.querySelector('#editProductPrice').value = product.price;
            form.querySelector('#editProductDescription').value = product.description;
            form.querySelector('#editProductStatus').value = product.status;
            
            showModal(editProductModal);
        })
        .catch(error => console.error('Error:', error));
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
                row.querySelector('.status-badge').textContent = 'Archived';
                row.querySelector('.status-badge').className = 'status-badge status-cancelled';
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

// Explicitly make functions available to the global scope
window.showEditProductModal = showEditProductModal;
window.archiveProduct = archiveProduct;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    
    // DOM Elements with error logging
    console.log('Initializing DOM elements...');
    const modalOverlay = document.getElementById('modalOverlay');
    const productModal = document.getElementById('productModal');
    const editProductModal = document.getElementById('editProductModal');
    const addProductBtn = document.getElementById('addProductButton');

    // Initialize these values instead of redeclaring with let
    productCheckboxes = document.querySelectorAll('.product-checkbox');
    searchInput = document.getElementById('searchInput');
    categoryFilter = document.getElementById('categoryFilter');
    availabilityFilter = document.getElementById('availabilityFilter');
    selectAllCheckbox = document.getElementById('selectAll');
    bulkActions = document.getElementById('bulkActions');
    selectedCount = document.getElementById('selectedCount');
    const clearSelectionBtn = document.getElementById('clearSelection');

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
        modalOverlay.style.visibility = 'visible';
    }

    // Add Product Button - open modal
    if (addProductBtn && productModal) {
        addProductBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showModal(productModal);
        });
    }

    // Modal close buttons
    document.querySelectorAll('.close-modal, .modal-button-secondary').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal();
        });
    });

    // Overlay click closes modal
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });
    }

    // Prevent modal content clicks from closing the modal
    document.querySelectorAll('.modal-content').forEach(content => {
        content.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

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

    // Move currentPage inside DOMContentLoaded scope
    let currentPage = 1;
    const itemsPerPage = 8;

    // Improved filter function (scoped)
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

    // Improved pagination (scoped)
    function updatePagination(totalItems) {
        if (!paginationNav) return;

        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const startIndex = totalItems > 0 ? ((currentPage - 1) * itemsPerPage) + 1 : 0;
        const endIndex = Math.min(startIndex + itemsPerPage - 1, totalItems);

        document.getElementById('totalItems')?.textContent = totalItems;
        document.getElementById('startIndex')?.textContent = startIndex;
        document.getElementById('endIndex')?.textContent = endIndex;

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

        // Pagination click handlers
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
        clearSelectionBtn?.addEventListener('click', clearSelection);
    }

    // Form submissions
    const addProductForm = document.getElementById('addProductForm');
    const editProductForm = document.getElementById('editProductForm');
    const saveProductBtn = document.getElementById('saveProduct');
    const saveEditProductBtn = document.getElementById('saveEditProduct');

    if (addProductForm && saveProductBtn) {
        saveProductBtn.addEventListener('click', (e) => {
            e.preventDefault();
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
                    // Optionally refresh the page or update the table
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    if (editProductForm && saveEditProductBtn) {
        saveEditProductBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const formData = new FormData(editProductForm);
            
            fetch('update_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then((data) => {
                if (data.success) {
                    closeModal();
                    editProductForm.reset();
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }); // Added missing closing bracket
    }

    // Debug click handlers
    document.querySelectorAll('.edit-button, .action-button').forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Button clicked', e.target);
            const productId = this.closest('tr')?.dataset.productId;
            if (productId) {
                console.log('Product ID:', productId);
                showEditProductModal(productId);
            }
        });
    });
    
    // Initialize the page
    initializeEventListeners();
    filterProducts();

    // Initialize product checkbox event listeners
    productCheckboxes = document.querySelectorAll('.product-checkbox');
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

// Keep only one copy of these functions outside DOMContentLoaded
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

function updateBulkActions() {
    const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
    if (bulkActions) {
        bulkActions.style.display = checkedCount > 0 ? 'flex' : 'none';
        if (selectedCount) {
            selectedCount.textContent = checkedCount;
        }
    }
}

function clearSelection() {
    if (selectAllCheckbox) selectAllCheckbox.checked = false;
    productCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateBulkActions();
}
