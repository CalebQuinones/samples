// Cart functionality
document.addEventListener("DOMContentLoaded", () => {
    // Sample product data (replace with your actual product data source)
    const products = [
      { id: 1, name: "Product 1", price: 250, image: "product1.jpg" },
      { id: 2, name: "Product 2", price: 300, image: "product2.jpg" },
      { id: 3, name: "Product 3", price: 400, image: "product3.jpg" },
    ]
  
    // Initialize cart from localStorage if available
    let cart = JSON.parse(localStorage.getItem("cart")) || []
  
    // Get DOM elements
    const cartIcon = document.getElementById("cartIcon")
    const cartPopup = document.getElementById("cartPopup")
    const closeCart = document.getElementById("closeCart")
    const cartPopupItems = document.getElementById("cartPopupItems")
    const cartTotal = document.getElementById("cartTotal")
    const shopMoreBtn = document.getElementById("shopMoreBtn")
    const checkoutBtn = document.getElementById("checkoutBtn")
    const cartCount = document.querySelector(".cart-count")
    const checkoutModal = document.getElementById("checkoutModal")
    const closeCheckout = document.getElementById("closeCheckout")
  
    // Function to update cart UI
    function updateCartUI() {
      // Update cart count
      const totalItems = cart.reduce((total, item) => total + item.quantity, 0)
      cartCount.textContent = totalItems
      cartCount.style.display = totalItems > 0 ? "flex" : "none"
  
      // Update cart popup items
      cartPopupItems.innerHTML = ""
  
      if (cart.length === 0) {
        cartPopupItems.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>'
      } else {
        cart.forEach((item) => {
          const cartItem = document.createElement("div")
          cartItem.className = "cart-popup-item"
          cartItem.dataset.id = item.id
  
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
                  `
  
          cartPopupItems.appendChild(cartItem)
        })
      }
  
      // Update cart total
      const total = cart.reduce((total, item) => total + item.price * item.quantity, 0)
      cartTotal.textContent = `Php ${total.toLocaleString()}`
  
      // Also update checkout items list
      updateCheckoutItems()
  
      // Save cart to localStorage
      localStorage.setItem("cart", JSON.stringify(cart))
    }
  
    // Function to update checkout items
    function updateCheckoutItems() {
      const orderItemsList = document.getElementById("orderItemsList")
  
      if (orderItemsList) {
        orderItemsList.innerHTML = ""
  
        cart.forEach((item) => {
          const cartItem = document.createElement("div")
          cartItem.className = "cart-item"
  
          cartItem.innerHTML = `
                      <div class="item-image">
                          <img src="${item.image}" alt="${item.name}">
                      </div>
                      <div class="item-details">
                          <h4>${item.name}</h4>
                          <p>Php ${item.price.toLocaleString()} × ${item.quantity}</p>
                      </div>
                  `
  
          orderItemsList.appendChild(cartItem)
        })
      }
    }
  
    // Function to add item to cart
    function addToCart(productId, quantity) {
      const product = products.find((p) => p.id === productId)
  
      if (!product) return
  
      // Check if product already exists in cart
      const existingItem = cart.find((item) => item.id === productId)
  
      if (existingItem) {
        existingItem.quantity += quantity
      } else {
        cart.push({
          id: product.id,
          name: product.name,
          price: product.price,
          image: product.image,
          quantity: quantity,
        })
      }
  
      // Update cart UI
      updateCartUI()
  
      // Add bounce animation to cart icon
      cartIcon.classList.add("bounce")
  
      // Remove the class after animation completes
      setTimeout(() => {
        cartIcon.classList.remove("bounce")
      }, 500)
    }
  
    // Function to remove item from cart
    function removeFromCart(productId) {
      cart = cart.filter((item) => item.id !== productId)
      updateCartUI()
    }
  
    // Function to update cart quantity
    function updateCartQuantity(productId, newQuantity) {
      const item = cart.find((item) => item.id === productId)
  
      if (item) {
        if (newQuantity <= 0) {
          removeFromCart(productId)
        } else {
          item.quantity = newQuantity
          updateCartUI()
        }
      }
    }
  
    // Event listener for cart icon
    cartIcon.addEventListener("click", () => {
      cartPopup.style.display = "block"
      setTimeout(() => {
        cartPopup.classList.add("active")
      }, 10)
    })
  
    // Event listener for close cart button
    closeCart.addEventListener("click", () => {
      cartPopup.classList.remove("active")
      setTimeout(() => {
        cartPopup.style.display = "none"
      }, 300)
    })
  
    // Event listener for shop more button
    shopMoreBtn.addEventListener("click", () => {
      cartPopup.classList.remove("active")
      setTimeout(() => {
        cartPopup.style.display = "none"
      }, 300)
    })
  
    // Event listener for checkout button
    checkoutBtn.addEventListener("click", () => {
      if (cart.length === 0) {
        alert("Your cart is empty. Please add items before checking out.")
        return
      }
  
      // Close cart popup
      cartPopup.classList.remove("active")
      setTimeout(() => {
        cartPopup.style.display = "none"
      }, 300)
  
      // Open checkout modal
      const modalOverlay = document.getElementById("modalOverlay")
      modalOverlay.style.display = "block"
      void modalOverlay.offsetWidth
      modalOverlay.classList.add("active")
  
      checkoutModal.style.display = "block"
      void checkoutModal.offsetWidth
      checkoutModal.classList.add("active")
  
      document.body.style.overflow = "hidden"
    })
  
    // Event delegation for cart popup items
    cartPopupItems.addEventListener("click", (e) => {
      const cartItem = e.target.closest(".cart-popup-item")
      if (!cartItem) return
  
      const productId = Number.parseInt(cartItem.dataset.id)
  
      // Handle remove button click
      if (e.target.closest(".cart-item-remove")) {
        removeFromCart(productId)
      }
  
      // Handle quantity buttons
      if (e.target.classList.contains("cart-plus")) {
        const quantityElement = cartItem.querySelector(".cart-quantity")
        const currentQuantity = Number.parseInt(quantityElement.textContent)
        updateCartQuantity(productId, currentQuantity + 1)
      }
  
      if (e.target.classList.contains("cart-minus")) {
        const quantityElement = cartItem.querySelector(".cart-quantity")
        const currentQuantity = Number.parseInt(quantityElement.textContent)
        if (currentQuantity > 1) {
          updateCartQuantity(productId, currentQuantity - 1)
        } else {
          removeFromCart(productId)
        }
      }
    })
  
    // Close checkout modal
    if (closeCheckout) {
      closeCheckout.addEventListener("click", () => {
        const modalOverlay = document.getElementById("modalOverlay")
        modalOverlay.classList.remove("active")
        checkoutModal.classList.remove("active")
  
        setTimeout(() => {
          modalOverlay.style.display = "none"
          checkoutModal.style.display = "none"
          document.body.style.overflow = "auto"
        }, 300)
      })
    }
  
    // Back to cart button in checkout modal
    const backToCartBtn = document.querySelector(".back-to-cart")
    if (backToCartBtn) {
      backToCartBtn.addEventListener("click", () => {
        // Close checkout modal
        const modalOverlay = document.getElementById("modalOverlay")
        modalOverlay.classList.remove("active")
        checkoutModal.classList.remove("active")
  
        setTimeout(() => {
          modalOverlay.style.display = "none"
          checkoutModal.style.display = "none"
        }, 300)
  
        // Open cart popup
        cartPopup.style.display = "block"
        setTimeout(() => {
          cartPopup.classList.add("active")
        }, 10)
      })
    }
  
    // Initialize cart UI
    updateCartUI()
  })
  
  