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

// Update cart UI function
window.updateCartUI = function() {
    // Update cart count
    const cartCount = document.querySelector('.cart-count');
    const totalItems = window.cart ? window.cart.reduce((total, item) => total + item.quantity, 0) : 0;
    if (cartCount) {
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
    }

    // Update cart popup items
    const cartPopupItems = document.getElementById('cartPopupItems');
    if (cartPopupItems) {
        cartPopupItems.innerHTML = '';
        
        if (!window.cart || window.cart.length === 0) {
            cartPopupItems.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>';
        } else {
            window.cart.forEach(item => {
                const cartItem = document.createElement('div');
                cartItem.className = 'cart-popup-item';
                cartItem.dataset.id = item.id;
                
                cartItem.innerHTML = `
                    <div class="cart-item-image">
                        <img src="${item.image}" alt="${item.name}">
                    </div>
                    <div class="cart-item-details">
                        <h4>${item.name}</h4>
                        <p>Php ${item.price.toLocaleString()}</p>
                    </div>
                    <div class="cart-item-quantity">
                        <button class="cart-quantity-btn cart-minus">-</button>
                        <span class="cart-quantity">${item.quantity}</span>
                        <button class="cart-quantity-btn cart-plus">+</button>
                    </div>
                    <button class="cart-item-remove">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 6L18 18" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                `;
                
                cartPopupItems.appendChild(cartItem);
            });
        }
    }

    // Update cart total
    const cartTotal = document.getElementById('cartTotal');
    if (cartTotal && window.cart) {
        const total = window.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        cartTotal.textContent = `Php ${total.toLocaleString()}`;
    }
}

// Event delegation for cart popup items
document.addEventListener('click', function(e) {
    const cartItem = e.target.closest('.cart-popup-item');
    if (!cartItem) return;
    
    const productId = parseInt(cartItem.dataset.id);
    
    // Handle remove button click
    if (e.target.closest('.cart-item-remove')) {
        window.removeFromCart(productId);
    }
    
    // Handle quantity buttons
    if (e.target.classList.contains('cart-plus')) {
        const quantityElement = cartItem.querySelector('.cart-quantity');
        const currentQuantity = parseInt(quantityElement.textContent);
        window.updateCartQuantity(productId, currentQuantity + 1);
    }
    
    if (e.target.classList.contains('cart-minus')) {
        const quantityElement = cartItem.querySelector('.cart-quantity');
        const currentQuantity = parseInt(quantityElement.textContent);
        if (currentQuantity > 1) {
            window.updateCartQuantity(productId, currentQuantity - 1);
        } else {
            window.removeFromCart(productId);
        }
    }
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