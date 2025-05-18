// Initialize cart from localStorage or create empty array
window.cart = JSON.parse(localStorage.getItem('cart')) || [];

// Function to show notification
function showNotification(message, type = 'success', productName = '') {
    // Remove any existing notifications
    const existing = document.querySelector('.add-to-order-notification');
    if (existing) existing.remove();

    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'add-to-order-notification';

    // Icon
    const icon = document.createElement('span');
    icon.className = 'icon';
    icon.innerHTML = `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="12" fill="#fff" fill-opacity="0.15"/><path d="M18 7L10.5 16L6 11.5" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`;

    // Message
    const msg = document.createElement('span');
    msg.textContent = productName ? `Added ${productName} to cart!` : message;

    notification.appendChild(icon);
    notification.appendChild(msg);
    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => notification.classList.add('show'), 10);
    // Animate out after 2.5s
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 2500);
}

// Function to save cart to localStorage
function saveCart() {
    localStorage.setItem('cart', JSON.stringify(window.cart));
}

// Function to update cart UI
function updateCartUI() {
    const cartPopupItems = document.getElementById('cartPopupItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartCount = document.querySelector('.cart-count');
    
    if (!cartPopupItems || !cartTotal || !cartCount) return;
    
    // Update cart count
    const totalItems = window.cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    
    // Update cart items
    cartPopupItems.innerHTML = '';
    let total = 0;
    
    window.cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        cartPopupItems.innerHTML += `
            <div class="cart-popup-item" data-id="${item.id}">
                <div class="cart-item-image">
                    <img src="${item.image}" alt="${item.name}">
                </div>
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p>Php ${item.price.toLocaleString()} × ${item.quantity}</p>
                </div>
                <div class="cart-item-actions">
                    <button class="cart-minus">-</button>
                    <span class="cart-quantity">${item.quantity}</span>
                    <button class="cart-plus">+</button>
                    <button class="cart-item-remove">×</button>
                </div>
            </div>
        `;
    });
    
    // Update total
    cartTotal.textContent = `Php ${total.toLocaleString()}`;
    
    // Update checkout items if checkout modal is open
    const orderItemsList = document.getElementById('orderItemsList');
    if (orderItemsList) {
        orderItemsList.innerHTML = '';
        window.cart.forEach(item => {
            orderItemsList.innerHTML += `
                <div class="cart-item">
                    <div class="item-image">
                        <img src="${item.image}" alt="${item.name}">
                    </div>
                    <div class="item-details">
                        <h4>${item.name}</h4>
                        <p>Php ${item.price.toLocaleString()} × ${item.quantity}</p>
                    </div>
                </div>
            `;
        });
    }
}

// Function to add item to cart
function addToCart(productId, quantity = 1) {
    const product = window.products.find(p => p.id === productId);
    if (!product) {
        showNotification('Product not found', 'error');
        return;
    }
    const existingItem = window.cart.find(item => item.id === productId);
    if (existingItem) {
        existingItem.quantity += quantity;
        showNotification('Updated quantity in cart', 'success', product && product.name ? product.name : '');
    } else {
        window.cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            quantity: quantity
        });
        showNotification('Added to cart', 'success', product && product.name ? product.name : '');
    }
    saveCart();
    updateCartUI();
}

// Function to remove item from cart
function removeFromCart(productId) {
    const item = window.cart.find(item => item.id === productId);
    if (item) {
        showNotification(`Removed ${item.name} from cart`);
    }
    window.cart = window.cart.filter(item => item.id !== productId);
    saveCart();
    updateCartUI();
}

// Function to update cart quantity
function updateCartQuantity(productId, quantity) {
    const item = window.cart.find(item => item.id === productId);
    if (item) {
        item.quantity = quantity;
        showNotification(`Updated quantity of ${item.name} in cart`);
        saveCart();
        updateCartUI();
    }
}

// Function to calculate total
function calculateTotal() {
    return window.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
}

// Add event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded'); // Debug log
    
    // Initialize cart UI
    updateCartUI();
    
    // Add event listeners for "Add to order" buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-to-order')) {
            e.stopPropagation(); // Prevent bubbling to product card
            const productCard = e.target.closest('.product-card');
            if (!productCard) return;
            const productId = parseInt(productCard.getAttribute('data-id'), 10);
            const quantityElem = productCard.querySelector('.quantity');
            const quantity = quantityElem ? parseInt(quantityElem.textContent, 10) : 1;
            addToCart(productId, quantity);
        }
    });
    
    // Add event listeners for quantity buttons
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const quantityElement = this.parentElement.querySelector('.quantity');
            let quantity = parseInt(quantityElement.textContent);
            
            if (this.classList.contains('plus')) {
                quantity++;
            } else if (this.classList.contains('minus') && quantity > 1) {
                quantity--;
            }
            
            quantityElement.textContent = quantity;
        });
    });
});

// Initialize cart on page load
document.addEventListener("DOMContentLoaded", function() {
    console.log("Initializing cart...");
    if (!window.cart) {
        window.cart = [];
    }
    loadCart();
});

// Cart persistence functionality
class CartPersistence {
    constructor() {
        this.cart = [];
        this.loadCartFromStorage();
    }

    // Load cart from localStorage
    loadCartFromStorage() {
        const savedCart = localStorage.getItem('cart');
        if (savedCart) {
            this.cart = JSON.parse(savedCart);
            window.cart = this.cart; // Set the global cart
            if (typeof updateCartUI === 'function') {
                updateCartUI();
            }
        }
    }

    // Save cart to localStorage
    saveCartToStorage() {
        localStorage.setItem('cart', JSON.stringify(this.cart));
        window.cart = this.cart; // Update the global cart
    }

    // Add item to cart
    addToCart(productId, quantity) {
        const product = products.find(p => p.id === productId);
        
        if (!product) return;
        
        // Check if product already exists in cart
        const existingItem = this.cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: quantity
            });
        }
        
        // Update cart UI and save to storage
        this.saveCartToStorage();
        if (typeof updateCartUI === 'function') {
            updateCartUI();
        }
    }

    // Remove item from cart
    removeFromCart(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.saveCartToStorage();
        if (typeof updateCartUI === 'function') {
            updateCartUI();
        }
    }

    // Update cart quantity
    updateCartQuantity(productId, newQuantity) {
        const item = this.cart.find(item => item.id === productId);
        
        if (item) {
            if (newQuantity <= 0) {
                this.removeFromCart(productId);
            } else {
                item.quantity = newQuantity;
                this.saveCartToStorage();
                if (typeof updateCartUI === 'function') {
                    updateCartUI();
                }
            }
        }
    }

    // Get cart items
    getCart() {
        return this.cart;
    }

    // Clear cart
    clearCart() {
        this.cart = [];
        this.saveCartToStorage();
        if (typeof updateCartUI === 'function') {
            updateCartUI();
        }
    }
}

// Initialize cart persistence
const cartPersistence = new CartPersistence();

// Override the global cart functions to use the persistence
window.addToCart = function(productId, quantity) {
    cartPersistence.addToCart(productId, quantity);
}

window.removeFromCart = function(productId) {
    cartPersistence.removeFromCart(productId);
}

window.updateCartQuantity = function(productId, newQuantity) {
    cartPersistence.updateCartQuantity(productId, newQuantity);
}

// Load cart when page loads
document.addEventListener('DOMContentLoaded', function() {
    cartPersistence.loadCartFromStorage();
});

function loadCart() {
    console.log("Loading cart from localStorage...")
    try {
        const cartStr = localStorage.getItem("cart")
        if (cartStr) {
            window.cart = JSON.parse(cartStr)
            console.log("Cart loaded from localStorage:", window.cart)
        } else {
            window.cart = []
            console.log("No cart found in localStorage, initialized empty cart")
        }
    } catch (e) {
        console.error("Error loading cart from localStorage:", e)
        window.cart = []
    }
    window.updateCartUI()
}