document.addEventListener("DOMContentLoaded", () => {
    // Add the CSS directly to the head
    const style = document.createElement("style")
    style.textContent = `
      /* Product Popup Modal Styles */
      .product-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
      }
      
      .product-modal-container {
        width: 90%;
        max-width: 1000px;
        max-height: 90vh;
        overflow-y: auto;
        background-color: #fff5f7;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        position: relative;
        transform: translateY(50px);
        transition: transform 0.4s ease-out;
      }
  
      .product-modal-overlay.active {
        opacity: 1;
        visibility: visible;
      }
  
      .product-modal-overlay.active .product-modal-container {
        transform: translateY(0);
      }
      
      
      /* Back to menu link */
      .product-back-link {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #555;
        margin: 20px;
        font-size: 16px;
        transition: color 0.3s;
        text-decoration: none;
        cursor: pointer;
      }
      
      .product-back-link:hover {
        color: #e44486;
      }
      
      .product-back-link svg {
        width: 20px;
        height: 20px;
      }
      
      /* Product section */
      .product-detail-section {
        background-color: white;
        border-radius: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin: 20px;
        overflow: hidden;
      }
      
      .product-detail-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
        padding: 24px;
      }
      
      @media (min-width: 768px) {
        .product-detail-grid {
          grid-template-columns: 1fr 1fr;
        }
      }
      
      /* Product image */
      .product-detail-image {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        background-color: #fff5f7;
        aspect-ratio: 1 / 1;
      }
      
      .product-detail-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      
      .product-favorite-button {
        position: absolute;
        top: 16px;
        right: 16px;
        background-color: white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s;
        border: none;
        cursor: pointer;
      }
      
      .product-favorite-button:hover {
        background-color: #fff5f7;
      }
      
      .product-favorite-button svg {
        fill: none;
        stroke: #e44486;
        width: 20px;
        height: 20px;
      }
      
      /* Product info */
      .product-detail-info {
        display: flex;
        flex-direction: column;
        gap: 24px;
      }
      
      .product-detail-rating {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
      }
      
      .product-detail-stars {
        display: flex;
      }
      
      .product-detail-stars .star {
        width: 16px;
        height: 16px;
        color: #ffc107;
      }
      
      .product-detail-stars .star.empty {
        color: #ddd;
      }
      
      .product-detail-review-count {
        color: #777;
        font-size: 14px;
      }
      
      .product-detail-title {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        font-family: "Poppins", sans-serif;
      }
      
      .product-detail-subtitle {
        color: #e44486;
        font-weight: 500;
        margin-top: 4px;
      }
      
      .product-detail-description {
        color: #555;
        line-height: 1.7;
      }
      
      .product-detail-price-box {
        background-color: #fff5f7;
        padding: 16px;
        border-radius: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      
      .product-detail-price-label {
        color: #777;
        font-size: 14px;
      }
      
      .product-detail-price-value {
        font-size: 28px;
        font-weight: 700;
        color: #e44486;
      }
      
      .product-detail-availability {
        text-align: right;
      }
      
      .product-detail-in-stock {
        color: #28a745;
        font-weight: 500;
      }
      
      /* Size selection */
      .product-detail-size-selection {
        margin-bottom: 20px;
      }
      
      .product-detail-option-label {
        font-weight: 500;
        margin-bottom: 8px;
        display: block;
      }
      
      .product-detail-size-options {
          display: flex;
          gap: 12px;
          flex-wrap: wrap;
      }
      
      .product-detail-size-option {
          padding: 8px 16px;
          border: 2px solid #E44486;
          border-radius: 50px;
          font-size: 14px;
          background: none;
          transition: all 0.3s;
          cursor: pointer;
          color: #333;
      }
      
      .product-detail-size-option:hover {
        border-color: #e44486;
        background-color: #fff5f7;
      }
      
      .product-detail-size-option.active {
          background-color: #E44486;
          color: white;
          font-weight: 500;
      }
      
      /* Quantity selector */
      .product-detail-quantity-selector {
          display: flex;
          align-items: center;
          border: 2px solid #E44486;
          border-radius: 50px;
          width: 120px;
          overflow: hidden;
      }
      
      .product-detail-quantity-button {
          width: 40px;
          height: 40px;
          display: flex;
          align-items: center;
          justify-content: center;
          background: none;
          color: #E44486;
          transition: background-color 0.3s;
          border: none;
          cursor: pointer;
      }
      
      .product-detail-quantity-button:hover {
        background-color: #fff5f7;
      }
      
      .product-detail-quantity-value {
        flex: 1;
        text-align: center;
        font-weight: 500;
      }
      
      /* Action buttons */
      .product-detail-action-buttons {
        display: flex;
        gap: 16px;
        margin-top: 16px;
      }
      
      .product-detail-add-to-cart,
      .product-detail-buy-now {
          flex: 1;
          padding: 16px;
          border-radius: 50px;
          font-weight: 600;
          font-size: 16px;
          transition: all 0.3s;
          display: flex;
          align-items: center;
          justify-content: center;
          border: none;
          cursor: pointer;
      }
      
      .product-detail-add-to-cart {
          background-color: #E44486;
          color: white;
      }
      
      .product-detail-add-to-cart:hover {
        background-color: #d3277a;
      }
      
      .product-detail-buy-now {
          background-color: #333;
          color: white;
      }
      
      .product-detail-buy-now:hover {
        background-color: #222;
      }
      
      /* Security info */
      .product-detail-security-info {
        display: flex;
        gap: 24px;
        color: #777;
        font-size: 14px;
        margin-top: 8px;
      }
      
      .product-detail-security-item {
        display: flex;
        align-items: center;
        gap: 8px;
      }
      
      .product-detail-security-item svg {
        width: 16px;
        height: 16px;
      }
      
      /* Tabs */
      .product-detail-tabs {
          border-top: 1px solid #f0f0f0;
          padding: 32px 24px;
      }
      
      .product-detail-tabs-list {
          display: flex;
          background-color: #fff5f7;
          border-radius: 50px;
          padding: 4px;
          margin-bottom: 24px;
          overflow-x: auto;
          white-space: nowrap;
      }
      
      .product-detail-tab-trigger {
          padding: 12px 24px;
          text-align: center;
          border-radius: 50px;
          background: none;
          transition: all 0.3s;
          font-size: 16px;
          border: none;
          cursor: pointer;
          color: #333;
      }
      
      .product-detail-tab-trigger.active {
          background-color: white;
          color: #E44486;
          font-weight: 500;
      }
      
      .product-detail-tab-content {
          display: none;
          padding: 20px;
          color: #333;
          line-height: 1.6;
      }
      
      .product-detail-tab-content.active {
          display: block;
      }
      
      .product-detail-tab-content p {
          margin-bottom: 16px;
      }
      
      /* Features */
      .product-detail-features {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 16px;
          margin-top: 24px;
      }
      
      .product-detail-feature {
          background-color: #fff5f7;
          padding: 16px;
          border-radius: 16px;
          display: flex;
          align-items: center;
          gap: 12px;
      }
      
      .product-detail-feature-icon {
          background-color: #ffc2d7;
          padding: 12px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
      }
      
      .product-detail-feature-icon svg {
          width: 24px;
          height: 24px;
          color: #E44486;
      }
      
      .product-detail-feature-label {
        color: #777;
        font-size: 14px;
      }
      
      .product-detail-feature-value {
          font-weight: 500;
          font-size: 16px;
      }
      
      /* Responsive adjustments */
      @media (max-width: 767px) {
        .product-detail-tabs-list {
          grid-template-columns: repeat(2, 1fr);
          gap: 8px;
        }
      
        .product-detail-features {
          grid-template-columns: 1fr;
        }
      }
      `
    document.head.appendChild(style)
  
    // Add the HTML directly to the body
    const popupHTML = `
      <!-- Product Modal Overlay -->
      <div class="product-modal-overlay" id="productModal">
          <div class="product-modal-container">
              
  
              <!-- Back to menu link -->
              <a href="#" class="product-back-link" id="backToMenu">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="m15 18-6-6 6-6"></path>
                  </svg>
                  Back to menu
              </a>
  
              <!-- Product Section -->
              <section class="product-detail-section">
                  <div class="product-detail-grid">
                      <!-- Product Image -->
                      <div class="product-detail-image">
                          <img src="/placeholder.svg" alt="Product Image" id="productDetailImage">
                          <button class="product-favorite-button" aria-label="Add to favorites">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                  <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                              </svg>
                          </button>
                      </div>
  
                      <!-- Product Info -->
                      <div class="product-detail-info">
                          <div>
                              <div class="product-detail-rating">
                                  <div class="product-detail-stars">
                                      <span class="star">★</span>
                                      <span class="star">★</span>
                                      <span class="star">★</span>
                                      <span class="star">★</span>
                                      <span class="star empty">★</span>
                                  </div>
                                  <span class="product-detail-review-count">(24 reviews)</span>
                              </div>
                              <h1 class="product-detail-title" id="productDetailTitle">Product Name</h1>
                              <p class="product-detail-subtitle" id="productDetailSubtitle">Product Subtitle</p>
                          </div>
  
                          <p class="product-detail-description" id="productDetailDescription">
                              Product description will appear here.
                          </p>
  
                          <div class="product-detail-price-box">
                              <div>
                                  <p class="product-detail-price-label">Price</p>
                                  <p class="product-detail-price-value" id="productDetailPrice">Php 0</p>
                              </div>
                              <div class="product-detail-availability">
                                  <p class="product-detail-price-label">Availability</p>
                                  <p class="product-detail-in-stock" id="productDetailAvailability">In Stock</p>
                              </div>
                          </div>
  
                          <div>
                              <label class="product-detail-option-label">Size</label>
                              <div class="product-detail-size-options">
                                  <button class="product-detail-size-option">6" (serves 8)</button>
                                  <button class="product-detail-size-option active">8" (serves 12)</button>
                                  <button class="product-detail-size-option">10" (serves 16)</button>
                              </div>
                          </div>
  
                          <div>
                              <label class="product-detail-option-label">Quantity</label>
                              <div class="product-detail-quantity-selector">
                                  <button class="product-detail-quantity-button decrease">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                          <path d="M5 12h14"></path>
                                      </svg>
                                  </button>
                                  <span class="product-detail-quantity-value">1</span>
                                  <button class="product-detail-quantity-button increase">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                          <path d="M12 5v14"></path>
                                          <path d="M5 12h14"></path>
                                      </svg>
                                  </button>
                              </div>
                          </div>
  
                          <div class="product-detail-action-buttons">
                              <button class="product-detail-add-to-cart" id="productDetailAddToCart">Add to Cart</button>
                              <button class="product-detail-buy-now">Buy Now</button>
                          </div>
  
                          <div class="product-detail-security-info">
                              <div class="product-detail-security-item">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                      <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"></path>
                                  </svg>
                                  <span>Secure checkout</span>
                              </div>
                              <div class="product-detail-security-item">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                      <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                      <path d="M12 12a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path>
                                      <path d="M6 12h.01M18 12h.01"></path>
                                  </svg>
                                  <span>Various payment options</span>
                              </div>
                          </div>
                      </div>
                  </div>
  
                  <!-- Product Details Tabs -->
                  <div class="product-detail-tabs">
                      <div class="product-detail-tabs-list">
                          <button class="product-detail-tab-trigger active" data-tab="description">Description</button>
                          <button class="product-detail-tab-trigger" data-tab="ingredients">Ingredients</button>
                          <button class="product-detail-tab-trigger" data-tab="reviews">Reviews (24)</button>
                      </div>
  
                      <div class="product-detail-tab-content active" id="description">
                          <p>Our delicious cake is the perfect centerpiece for any celebration. This delightful cake is handcrafted with love and decorated with care, making it not only delicious but also visually stunning.</p>
                          <p>Each cake is handcrafted with love and decorated with fresh fruits and edible flowers, making it not only delicious but also visually stunning. The light and fluffy texture of the cake pairs perfectly with the sweet and tangy filling.</p>
                          <p>This cake is perfect for birthdays, anniversaries, or any special occasion that calls for something sweet and beautiful.</p>
                          
                          <div class="product-detail-features">
                              <div class="product-detail-feature">
                                  <div class="product-detail-feature-icon">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                          <path d="M12 8v4l3 3"></path>
                                          <circle cx="12" cy="12" r="10"></circle>
                                      </svg>
                                  </div>
                                  <div>
                                      <p class="product-detail-feature-label">Preparation Time</p>
                                      <p class="product-detail-feature-value">4-6 hours</p>
                                  </div>
                              </div>
                              <div class="product-detail-feature">
                                  <div class="product-detail-feature-icon">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                          <path d="M5 22h14"></path>
                                          <path d="M5 2h14"></path>
                                          <path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V2"></path>
                                          <path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"></path>
                                      </svg>
                                  </div>
                                  <div>
                                      <p class="product-detail-feature-label">Shelf Life</p>
                                      <p class="product-detail-feature-value">3-4 days refrigerated</p>
                                  </div>
                              </div>
                          </div>
                      </div>
                      
                      <div class="product-detail-tab-content" id="ingredients">
                          <p>Our cakes are made with the highest quality ingredients:</p>
                          <ul style="margin-left: 20px; margin-bottom: 20px;">
                              <li>Organic all-purpose flour</li>
                              <li>Free-range eggs</li>
                              <li>Pure cane sugar</li>
                              <li>Real vanilla extract</li>
                              <li>Fresh fruits</li>
                              <li>Cream cheese</li>
                              <li>Heavy cream</li>
                              <li>Unsalted butter</li>
                              <li>Edible flowers (for decoration)</li>
                          </ul>
                          <div style="background-color: #fff8e1; padding: 16px; border-radius: 16px; margin-top: 16px;">
                              <p style="font-weight: 500; color: #b7791f;">Allergy Information</p>
                              <p style="font-size: 14px; color: #b7791f; margin-top: 4px;">Contains: Wheat, Dairy, Eggs</p>
                              <p style="font-size: 14px; color: #b7791f; margin-top: 4px;">Made in a facility that also processes nuts.</p>
                          </div>
                      </div>
                      
                      <div class="product-detail-tab-content" id="reviews">
                          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                              <div>
                                  <div style="display: flex; align-items: center; gap: 8px;">
                                      <div style="display: flex;">
                                          <span style="color: #ffc107;">★</span>
                                          <span style="color: #ffc107;">★</span>
                                          <span style="color: #ffc107;">★</span>
                                          <span style="color: #ffc107;">★</span>
                                          <span style="color: #ddd;">★</span>
                                      </div>
                                      <span style="font-weight: 500; margin-left: 8px;">4.2 out of 5</span>
                                  </div>
                                  <p style="font-size: 14px; color: #777;">Based on 24 reviews</p>
                              </div>
                              <button style="background-color: #E44486; color: white; padding: 10px 20px; border-radius: 50px; font-weight: 500; border: none; cursor: pointer;">Write a Review</button>
                          </div>
                          
                          <!-- Sample reviews -->
                          <div style="border-bottom: 1px solid #f0f0f0; padding-bottom: 16px; margin-bottom: 16px;">
                              <div style="display: flex; justify-content: space-between;">
                                  <p style="font-weight: 500;">Maria S.</p>
                                  <div style="display: flex; color: #ffc107;">
                                      <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                  </div>
                              </div>
                              <p style="font-size: 14px; color: #777; margin-bottom: 8px;">Purchased: May 15, 2023</p>
                              <p>This cake was absolutely delicious! The frosting was perfect and not too sweet. Everyone at the party loved it!</p>
                          </div>
                          
                          <div style="border-bottom: 1px solid #f0f0f0; padding-bottom: 16px; margin-bottom: 16px;">
                              <div style="display: flex; justify-content: space-between;">
                                  <p style="font-weight: 500;">John D.</p>
                                  <div style="display: flex; color: #ffc107;">
                                      <span>★</span><span>★</span><span>★</span><span>★</span><span style="color: #ddd;">★</span>
                                  </div>
                              </div>
                              <p style="font-size: 14px; color: #777; margin-bottom: 8px;">Purchased: April 3, 2023</p>
                              <p>Great cake, very moist and flavorful. Delivery was on time and the cake looked exactly like the pictures. Would order again!</p>
                          </div>
                      </div>
                  </div>
              </section>
          </div>
      </div>
      `
    document.body.insertAdjacentHTML("beforeend", popupHTML)
  
    // Initialize the popup functionality
    initProductPopup()
  
    function initProductPopup() {
      console.log("Initializing product popup...")
  
      // Get DOM elements
      const productModal = document.getElementById("productModal")
  
      const backToMenu = document.getElementById("backToMenu")
      const productDetailAddToCart = document.getElementById("productDetailAddToCart")
  
      if (!productModal || !backToMenu || !productDetailAddToCart) {
        console.error("One or more required elements not found!")
        return
      }
  
      // Tab functionality
      const tabTriggers = document.querySelectorAll(".product-detail-tab-trigger")
      const tabContents = document.querySelectorAll(".product-detail-tab-content")
  
      tabTriggers.forEach((trigger) => {
        trigger.addEventListener("click", () => {
          // Remove active class from all triggers and contents
          tabTriggers.forEach((t) => t.classList.remove("active"))
          tabContents.forEach((c) => c.classList.remove("active"))
  
          // Add active class to clicked trigger
          trigger.classList.add("active")
  
          // Show corresponding content
          const tabId = trigger.getAttribute("data-tab")
          document.getElementById(tabId).classList.add("active")
        })
      })
  
      // Size selection
      const sizeOptions = document.querySelectorAll(".product-detail-size-option")
      sizeOptions.forEach((option) => {
        option.addEventListener("click", () => {
          sizeOptions.forEach((o) => o.classList.remove("active"))
          option.classList.add("active")
        })
      })
  
      // Quantity selector
      const quantityValue = document.querySelector(".product-detail-quantity-value")
      const decreaseBtn = document.querySelector(".product-detail-quantity-button.decrease")
      const increaseBtn = document.querySelector(".product-detail-quantity-button.increase")
  
      decreaseBtn.addEventListener("click", () => {
        let quantity = Number.parseInt(quantityValue.textContent)
        if (quantity > 1) {
          quantity--
          quantityValue.textContent = quantity
        }
      })
  
      increaseBtn.addEventListener("click", () => {
        let quantity = Number.parseInt(quantityValue.textContent)
        quantity++
        quantityValue.textContent = quantity
      })
  
      // Close modal when clicking the back to menu link
      backToMenu.addEventListener("click", (e) => {
        e.preventDefault()
        closeProductModalFunc()
      })
  
      // Close modal when clicking outside the modal content
      productModal.addEventListener("click", (e) => {
        if (e.target === productModal) {
          closeProductModalFunc()
        }
      })
  
      // Add to cart functionality
      productDetailAddToCart.addEventListener("click", function () {
        const productId = this.getAttribute("data-product-id")
        const quantity = Number.parseInt(document.querySelector(".product-detail-quantity-value").textContent)
  
        // Call the existing addToCart function from your menu page
        if (typeof window.addToCart === "function") {
          window.addToCart(Number.parseInt(productId), quantity)
        } else {
          console.log(`Adding product ${productId} with quantity ${quantity} to cart`)
        }
  
        // Show success message
        const productName = document.getElementById("productDetailTitle").textContent
        alert(`${productName} has been successfully added to your order!`)
  
        // Close the modal
        closeProductModalFunc()
      })
  
      // Function to close the product modal
      function closeProductModalFunc() {
        productModal.classList.remove("active")
        setTimeout(() => {
          document.body.style.overflow = "auto" // Re-enable scrolling
        }, 300)
      }
  
      // Function to open the product modal with product details
      const openProductModal = (productId) => {
        console.log("Opening product modal for product ID:", productId)
  
        // Get product data from your existing products array
        let product
  
        if (typeof window.products !== "undefined" && Array.isArray(window.products)) {
          product = window.products.find((p) => p.id === productId)
          console.log("Found product:", product)
        } else {
          // Fallback for testing
          console.log("Using fallback product data")
          product = {
            id: productId,
            name: "Product " + productId,
            subtitle: "Product Subtitle",
            price: 200,
            image: "https://via.placeholder.com/600",
            availability: "In Stock",
            description: "This is a sample product description. Replace this with your actual product description.",
          }
        }
  
        if (!product) {
          console.error("Product not found for ID:", productId)
          return
        }
  
        // Update product details in the modal
        document.getElementById("productDetailTitle").textContent = product.name
        document.getElementById("productDetailSubtitle").textContent = product.subtitle || ""
        document.getElementById("productDetailPrice").textContent = `Php ${product.price.toLocaleString()}`
        document.getElementById("productDetailImage").src = product.image
        document.getElementById("productDetailAvailability").textContent = product.availability || "In Stock"
        document.getElementById("productDetailDescription").textContent = product.description || ""
  
        // Set the product ID for the add to cart button
        document.getElementById("productDetailAddToCart").setAttribute("data-product-id", product.id)
  
        // Reset quantity to 1
        document.querySelector(".product-detail-quantity-value").textContent = "1"
  
        // Reset to first tab
        const tabTriggers = document.querySelectorAll(".product-detail-tab-trigger")
        const tabContents = document.querySelectorAll(".product-detail-tab-content")
  
        tabTriggers.forEach((t) => t.classList.remove("active"))
        tabContents.forEach((c) => c.classList.remove("active"))
  
        tabTriggers[0].classList.add("active")
        document.getElementById("description").classList.add("active")
  
        // Show the modal
        productModal.style.display = "flex"
        document.body.style.overflow = "hidden" // Prevent scrolling behind modal
  
        // Force reflow before adding active class for animation
        void productModal.offsetWidth
  
        // Add active class to trigger animation
        productModal.classList.add("active")
      }
  
      // Add click event to product cards to open the modal
      const productCards = document.querySelectorAll(".product-card")
      console.log("Found", productCards.length, "product cards")
  
      productCards.forEach((card) => {
        card.addEventListener("click", function (e) {
          // Don't trigger if clicking on buttons inside the card
          if (e.target.closest(".quantity-btn") || e.target.closest(".add-to-order")) {
            return
          }
  
          const productId = Number.parseInt(this.getAttribute("data-id"))
          console.log("Product card clicked, ID:", productId)
          openProductModal(productId)
        })
      })
  
      // Add a test button for debugging
      const testButton = document.createElement("button")
      testButton.textContent = "Test Product Popup"
      testButton.style.position = "fixed"
      testButton.style.bottom = "20px"
      testButton.style.right = "20px"
      testButton.style.zIndex = "999"
      testButton.style.padding = "10px 20px"
      testButton.style.backgroundColor = "#E44486"
      testButton.style.color = "white"
      testButton.style.border = "none"
      testButton.style.borderRadius = "5px"
      testButton.style.cursor = "pointer"
  
      testButton.addEventListener("click", () => {
        openProductModal(1)
      })
  
      document.body.appendChild(testButton)
    }
  })
  
  