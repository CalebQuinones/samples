<!DOCTYPE html>
<?php
session_start();
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Triple J's Bakery</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Madimi+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Madimi+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Menu3.css">
    <link rel="stylesheet" href="order-confirmation.css">
    <link rel="stylesheet" href="aicakes.css">
    <script src="cart-persistence.js"></script>
    <script src="product-popup.js"></script>
    <script src="aicakes.js"></script>
</head>
<body>
    <div class="top-banner">
       
    </div>
    <header>
        <nav class="container">
            <div class="logo">
                <img src="logo.png">
                <span>Triple J & Rose's Bakery</span>
            </div>
            <input type="checkbox" id="nav-toggle" class="nav-toggle">
            <label for="nav-toggle" class="nav-toggle-label">
                <span></span>
            </label>
            <div class="nav-links">
                <ul>
                    <li><a href="TriplesJ_sandroseBakery.php" class="active">Home</a></li>
                    <li><a href="MenuSection.php">Menu</a></li>
                    <li><a href="Abouts.php">About</a></li>
                    <li><a href="Contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="cart-profile">
              <div class="cart-icon" id="cartIcon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M9 22C9.55228 22 10 21.5523 10 21C10 20.4477 9.55228 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z" stroke="#E44486" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M20 22C20.5523 22 21 21.5523 21 21C21 20.4477 20.5523 20 20 20C19.4477 20 19 20.4477 19 21C19 21.5523 19.4477 22 20 22Z" stroke="#E44486" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6" stroke="#E44486" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <span class="cart-count">0</span>
              </div>
               <a href="account.php"><div class="profile-icon">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="#E44486" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="#E44486" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
              </div>
            </a>
          </div>
        </nav>
    </header>

    <!-- Cart Popup -->
    <div class="cart-popup" id="cartPopup">
        <div class="cart-popup-content">
            <div class="cart-popup-header">
                <h3>Your Cart</h3>
                <button class="close-cart" id="closeCart">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 6L18 18" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <div class="cart-popup-items" id="cartPopupItems">
                <!-- Cart items will be dynamically inserted here -->
            </div>
            <div class="cart-popup-footer">
                <div class="cart-total">
                    <span>Total:</span>
                    <span id="cartTotal">Php 0</span>
                </div>
                <button class="shop-more-btn" id="shopMoreBtn">Shop more</button>
                <button class="checkout-btn" id="checkoutBtn">Go to Checkout</button>
            </div>
        </div>
    </div>

    <!-- Modal Overlay -->
    <div class="modal-overlay" id="modalOverlay"></div>

    <!-- Checkout Modal -->
    <div class="checkout-modal" id="checkoutModal">
        <div class="checkout-content">
            <div class="checkout-header">
                <h2>Checkout</h2>
                <button class="close-checkout" id="closeCheckout">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 6L18 18" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

            <div class="checkout-body">
                <div class="checkout-left">
                    <div class="back-to-cart">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 12H5" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 19L5 12L12 5" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Back to cart</span>
                    </div>

                    <div class="checkout-section">
                        <h3>Review your purchases</h3>
                        <div class="cart-items-container">
                            <div class="cart-items" id="orderItemsList">
                                <!-- Cart items will be dynamically inserted here -->
                            </div>
                        </div>
                    </div>

                    <div class="checkout-section">
                        <h3>Delivery Options</h3>
                        <div class="delivery-options">
                            <div class="delivery-option">
                                <input type="radio" id="standardDelivery" name="deliveryOption" value="standard" checked>
                                <label for="standardDelivery">
                                    <div class="option-title">Php 50 - Standard delivery</div>
                                    <div class="option-subtitle">Get it tomorrow, 12 Dec 23</div>
                                </label>
                            </div>
                            <div class="delivery-option">
                                <input type="radio" id="pickupOption" name="deliveryOption" value="pickup">
                                <label for="pickupOption">
                                    <div class="option-title">No cost - Pick-up</div>
                                    <div class="option-subtitle">Get it tomorrow, 12 Dec 23</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="checkout-right">
                    <div class="payment-details">
                        <h3>Payment Details</h3>
                        <p class="payment-subtitle">Complete your order by providing your payment details</p>

                        <form id="paymentForm">
                            <div class="form-group">
                                <label for="fullname">Fullname</label>
                                <input type="text" id="fullname" name="fullname" autocomplete="name" placeholder="Enter your fullname here..." required>
                            </div>

                            <div class="form-group">
                                <label for="address">Delivery Address</label>
                                <input type="text" id="address" name="address" autocomplete="street-address" placeholder="Enter your address..." required>
                            </div>

                            <div class="form-group">
                                <label for="deliveryDate">Delivery date/Pick-up date</label>
                                <input type="date" id="deliveryDate" name="deliveryDate" autocomplete="off" required>
                            </div>

                            <div class="form-group">
                                <label>Payment</label>
                                <div class="payment-options">
                                    <div class="payment-option">
                                        <input type="radio" id="cardPayment" name="paymentMethod" value="card" autocomplete="off" checked>
                                        <label for="cardPayment">Card</label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="radio" id="gcashPayment" name="paymentMethod" value="gcash" autocomplete="off">
                                        <label for="gcashPayment">
                                            <img src="https://www.gcash.com/wp-content/uploads/2019/04/gcash-logo.png" alt="GCash" class="payment-icon">
                                        </label>
                                    </div>
                                    <div class="view-all-options">
                                        <span>View all options</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="confirm-button">CONFIRM</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Confirmation Modal -->
    <div class="order-confirmation-modal" id="orderConfirmationModal">
        <div class="order-confirmation-content">
            <div class="confirmation-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" fill="#E84B8A" />
                    <path d="M8 12L11 15L16 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2>Order Confirmed!</h2>
            <p>Thank you for your order. We've received your payment and are preparing your items.</p>
            <div class="order-details">
                <div class="order-id">
                    <span>Order ID:</span>
                    <span id="confirmationOrderId">ORD-12345</span>
                </div>
                <div class="order-date">
                    <span>Date:</span>
                    <span id="confirmationOrderDate">April 1, 2025</span>
                </div>
            </div>
            <p class="confirmation-message">A confirmation email has been sent to your email address.</p>
            <button class="check-status-btn" id="checkStatusBtn">Check Order Status</button>
        </div>
    </div>

    <span class="pangbg"></span>
    </section>
    <section class="buong-content">
        <img src="meltdown2.png" class="melt">
        <div class="Menu">
          <h2>Our Menu</h2>
        </div>
       


        <?php
        // Get category counts
        $category_counts = array();
        $total_items = 0;
        
        $sql = "SELECT category, COUNT(*) as count FROM products GROUP BY category";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $category_counts[strtolower($row['category'])] = $row['count'];
                $total_items += $row['count'];
            }
        }
        ?>
        <div class="categories">
            <div class="category-card" id="color">
                <span class="category-icon">📅</span>
                <span class="category-name">All</span>
                <span class="category-items"><?php echo $total_items; ?> Items</span>
            </div>
            <div class="category-card">
                <span class="category-icon">🎂</span>
                <span class="category-name">Birthday Cakes</span>
                <span class="category-items"><?php echo isset($category_counts['birthday cakes']) ? $category_counts['birthday cakes'] : 0; ?> Items</span>
            </div>
            <div class="category-card">
                <span class="category-icon">💝</span>
                <span class="category-name">Wedding Cakes</span>
                <span class="category-items"><?php echo isset($category_counts['wedding cakes']) ? $category_counts['wedding cakes'] : 0; ?> Items</span>
            </div>
            <div class="category-card">
                <span class="category-icon">🚿</span>
                <span class="category-name">Shower Cakes</span>
                <span class="category-items"><?php echo isset($category_counts['shower cakes']) ? $category_counts['shower cakes'] : 0; ?> Items</span>
            </div>
            <div class="category-card">
                <span class="category-icon">🧁</span>
                <span class="category-name">Cupcakes</span>
                <span class="category-items"><?php echo isset($category_counts['cupcakes']) ? $category_counts['cupcakes'] : 0; ?> Items</span>
            </div>
            <div class="category-card">
                <span class="category-icon">🥖</span>
                <span class="category-name">Breads</span>
                <span class="category-items"><?php echo isset($category_counts['breads']) ? $category_counts['breads'] : 0; ?> Items</span>
            </div>
            <div class="category-card">
                <span class="category-icon">🍳</span>
                <span class="category-name">Celebration</span>
                <span class="category-items"><?php echo isset($category_counts['celebration']) ? $category_counts['celebration'] : 0; ?> Items</span>
            </div>
        </div>

        <div class="search-section">
            <div class="search-container">
                <input type="text" class="search-input" placeholder="  Search something sweet on your mind">
                <img src="searchicon.png" class="pota">
            </div>
            
            <div class="filter-section">
                <span>Filter:</span>
                <select id="filterAvailability" class="filter-dropdown">
                    <option value="availability">Availability</option>
                    <option value="in stock">In Stock</option>
                    <option value="out of stock">Out of Stock</option>
                </select>
                <select id="filterSize" class="filter-dropdown">
                    <option value="size">Size</option>
                    <option value="small">Small (Under 200)</option>
                    <option value="medium">Medium (200-500)</option>
                    <option value="large">Large (500+)</option>
                </select>
            </div>
        </div>
        <section>
        <div class="custom-cake-banner">
            <h3>Customize your own cake!</h3>
            <button class="custom-order-btn" id="customOrderBtn">CUSTOM ORDER</button>
        </div>
    
        <!-- Custom Cake Modal -->
        <div class="custom-cake-modal" id="customCakeModal">
            <button class="close-modal" id="closeModal">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 6L18 18" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            
            <h2>How would you like to order your cake?</h2>
            <p class="modal-subtitle">Choose an option below to get started with your custom cake order</p>
            
            <div class="order-options">
                <div class="order-option ai-option">
                    <div class="option-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 21H16" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 17V21" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17 3H7C5.89543 3 5 3.89543 5 5V13C5 14.1046 5.89543 15 7 15H17C18.1046 15 19 14.1046 19 13V5C19 3.89543 18.1046 3 17 3Z" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 7H15" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 11H15" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="new-feature-badge">NEW</div>
                    <h3>Build with AICakes</h3>
                    <p>Generate a cake design with AICakes our AI-powered cake builder</p>
                </div>
                
                <div class="order-option">
                    <div class="option-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17 8L12 3L7 8" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 3V15" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3>Upload a Photo</h3>
                    <p>Share a photo of a cake design you'd like us to create for you</p>
                </div>
            </div>
            
            <div class="modal-actions">
                <button class="start-customizing-btn">START CUSTOMIZING</button>
                <button class="choose-later-btn">Choose Later</button>
            </div>
        </div>

        <!-- Photo Upload Modal -->
        <div class="photo-upload-modal" id="photoUploadModal">
            <div class="modal-content">
                <button class="close-modal" id="closePhotoModal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 6L18 18" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                
                <h2>Upload Your Dream Cake</h2>
                <p class="modal-subtitle">Choose your perfect reference image - we'll bring your dream design to life!</p>
                
                <div class="upload-area" id="uploadArea">
                    <div class="upload-placeholder">
                        <div class="upload-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="#E84B8A" stroke-width="2"/>
                                <path d="M12 8V16" stroke="#E84B8A" stroke-width="2" stroke-linecap="round"/>
                                <path d="M8 12H16" stroke="#E84B8A" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <p>Click to Upload or Drag and Drop</p>
                    </div>
                    <input type="file" id="cakePhotoInput" accept="image/*" class="file-input">
                    <div class="preview-container" id="previewContainer"></div>
                </div>
                
                <div class="cake-details-section">
                    <h3>Cake Details</h3>
                    <form id="cakeDetailsForm">
                        <div class="form-group">
                            <label for="cakeType">Cake Type</label>
                            <select id="cakeType" required>
                                <option value="" disabled selected>Select type</option>
                                <option value="wedding">Wedding Cake</option>
                                <option value="birthday">Birthday Cake</option>
                                <option value="anniversary">Anniversary Cake</option>
                                <option value="custom">Custom Cake</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cakeTiers">Number of Tiers</label>
                            <select id="cakeTiers" required>
                                <option value="" disabled selected>Select tiers</option>
                                <option value="1">Single Tier</option>
                                <option value="2">Two Tiers</option>
                                <option value="3">Three Tiers</option>
                                <option value="4">Four Tiers</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cakeSize">Cake Size</label>
                            <select id="cakeSize" required>
                                <option value="" disabled selected>Select size</option>
                                <option value="6">6 inches</option>
                                <option value="8">8 inches</option>
                                <option value="10">10 inches</option>
                                <option value="12">12 inches</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="cakeFlavor">Cake Flavor</label>
                            <select id="cakeFlavor" required>
                                <option value="" disabled selected>Select flavor</option>
                                <option value="vanilla">Vanilla</option>
                                <option value="chocolate">Chocolate</option>
                                <option value="redvelvet">Red Velvet</option>
                                <option value="carrot">Carrot</option>
                                <option value="ube">Ube</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="fillingType">Filling Type</label>
                            <select id="fillingType" required>
                                <option value="" disabled selected>Select filling</option>
                                <option value="buttercream">Buttercream</option>
                                <option value="ganache">Chocolate Ganache</option>
                                <option value="fruit">Fresh Fruit</option>
                                <option value="custard">Custard</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="frostingType">Frosting Type</label>
                            <select id="frostingType" required>
                                <option value="" disabled selected>Select frosting</option>
                                <option value="buttercream">Buttercream</option>
                                <option value="fondant">Fondant</option>
                                <option value="whipped">Whipped Cream</option>
                                <option value="ganache">Ganache</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="specialInstructions">Special Instructions</label>
                            <textarea id="specialInstructions" placeholder="Any special requests or details about your cake design..."></textarea>
                        </div>
                        
                        <div class="price-estimate-section">
                            <h4>Estimated Price Range</h4>
                            <div class="price-range">
                                <span id="estimatedPrice">₱0.00</span>
                                <span class="plus-icon">+</span>
                            </div>
                            <p class="price-note">* Final price may vary based on design complexity</p>
                        </div>

                        <button type="submit" class="submit-order-btn">ADD TO CART</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- AICakes Modal -->
        <div class="aicakes-modal" id="aiCakesModal">
            <div class="modal-content">
                <button class="close-modal" id="closeAiModal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L6 18" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 6L18 18" stroke="#E84B8A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                
                <h2>Design Your Dream Cake with AI</h2>
                <p class="modal-subtitle">Tell us about your perfect cake and let our AI bring your vision to life!</p>
                
                <div class="cake-details-section">
                    <form id="aiCakeForm">
                        <div class="form-group">
                            <label for="aiCakeType">Cake Type</label>
                            <select id="aiCakeType" name="cakeType" required>
                                <option value="">Select type</option>
                                <option value="wedding">Wedding Cake</option>
                                <option value="birthday">Birthday Cake</option>
                                <option value="anniversary">Anniversary Cake</option>
                                <option value="custom">Custom Cake</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="aiCakeTiers">Number of Tiers</label>
                            <select id="aiCakeTiers" name="cakeTiers" required>
                                <option value="">Select tiers</option>
                                <option value="1">Single Tier</option>
                                <option value="2">Two Tiers</option>
                                <option value="3">Three Tiers</option>
                                <option value="4">Four Tiers</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="aiCakeSize">Cake Size</label>
                            <select id="aiCakeSize" name="cakeSize" required>
                                <option value="">Select a size</option>
                                <option value="6inch">6 inch (serves 8-10)</option>
                                <option value="8inch">8 inch (serves 12-15)</option>
                                <option value="10inch">10 inch (serves 20-25)</option>
                                <option value="12inch">12 inch (serves 30-35)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="aiCakeFlavor">Cake Flavor</label>
                            <select id="aiCakeFlavor" name="cakeFlavor" required>
                                <option value="">Select a flavor</option>
                                <option value="vanilla">Vanilla</option>
                                <option value="chocolate">Chocolate</option>
                                <option value="redvelvet">Red Velvet</option>
                                <option value="carrot">Carrot</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="aiFillingType">Filling Type</label>
                            <select id="aiFillingType" name="fillingType" required>
                                <option value="">Select a filling</option>
                                <option value="buttercream">Buttercream</option>
                                <option value="chocolate">Chocolate Ganache</option>
                                <option value="fruit">Fresh Fruit</option>
                                <option value="custard">Custard</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="aiFrostingType">Frosting Type</label>
                            <select id="aiFrostingType" name="frostingType" required>
                                <option value="">Select a frosting</option>
                                <option value="buttercream">Buttercream</option>
                                <option value="fondant">Fondant</option>
                                <option value="ganache">Ganache</option>
                                <option value="nakedcake">Naked Cake</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="aiDescription">Describe Your Dream Cake</label>
                            <textarea id="aiDescription" name="cakeDescription" placeholder="Describe the design, colors, decorations, and any special elements you'd like on your cake..." required></textarea>
                        </div>

                        <div class="price-estimate-section">
                            <h4>Estimated Price</h4>
                            <div class="price-range">
                                <span id="aiEstimatedPrice">₱0.00</span>
                                <span class="plus-icon">+</span>
                            </div>
                            <p class="price-note">* Final price may vary based on design complexity</p>
                        </div>

                        <button type="submit" class="magic-button">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        </section>
        <div class="product-grid">
            <?php
            // Get products from database
            $sql = "SELECT * FROM products ORDER BY created_at DESC";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Get category and availability directly from database
                    $category = strtolower($row['category']);
                    $availability = strtolower($row['availability']);

                    echo '<div class="product-card" 
                              data-id="' . $row['product_id'] . '"
                              data-category="' . $category . '"
                              data-availability="' . $availability . '">';
                    echo '<div class="product-image" style="background-image: url(\'' . $row['image'] . '\')"></div>';
                    echo '<div class="product-details">';
                    echo '<h3 class="product-name">' . $row['name'] . '</h3>';
                    echo '<div class="product-rating">';
                    echo '<span class="star filled">★</span>';
                    echo '<span class="star filled">★</span>';
                    echo '<span class="star filled">★</span>';
                    echo '<span class="star filled">★</span>';
                    echo '<span class="star">★</span>';
                    echo '</div>';
                    echo '<p class="product-price">Php ' . number_format($row['price'], 2) . '</p>';
                    echo '<div class="quantity-control">';
                    echo '<button class="quantity-btn minus" type="button">-</button>';
                    echo '<span class="quantity">1</span>';
                    echo '<button class="quantity-btn plus" type="button">+</button>';
                    echo '</div>';
                    echo '<button class="add-to-order" type="button" 
                                  data-product-id="' . $row['product_id'] . '" 
                                  data-product-name="' . htmlspecialchars($row['name']) . '" 
                                  data-product-price="' . $row['price'] . '" 
                                  data-product-image="' . $row['image'] . '">Add to order</button>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products found</p>';
            }
            ?>
        </div>
    </section>
    <footer>
      <div class="footer-content">
          <div class="footer-logo">
              <a href="MenuSection.php"><img src="logo.png"></a>
          </div>
          <div class="footer-contact">
            <p>001 Road 10 Joseph Sitt Bagumbayan, Taguig City</p>
            <p>Call us: +63 918 746 4342</p>
            <p>Email: <a href="mailto:info@triplejsbakery.com">info@triplejsbakery.com</a></p>
          </div>
          <div class="footer-social">
              <a href="#"><i class="fab fa-facebook"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-pinterest"></i></a>
          </div>
          <div class="footer-links">
              <a href="Abouts.php">About Us</a>
              <a href="Contact.php">Contact Us</a>
              <a href="#help">Help</a>
              <a href="#privacy">Privacy Policy</a>
              <a href="">Sitemap</a>
          </div>
      </div>
      <div class="footer-copyright">
          <p>&copy; 2024 Triple J's Bakery. All rights reserved.</p>
      </div>
  </footer>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const meltElement = document.querySelector('.melt');
      
      if (meltElement) {
        // Initial position (slightly up)
        meltElement.style.transform = 'translateY(-30px)';
        
        // Maximum scroll position where the effect should complete
        const maxScrollPosition = 200; // Adjust this value as needed
        
        window.addEventListener('scroll', function() {
          // Get current scroll position
          const scrollPosition = window.scrollY;
          
          // Calculate how far to move the element (from -30px to 0px)
          if (scrollPosition <= maxScrollPosition) {
            // Calculate percentage of scroll progress
            const scrollPercentage = scrollPosition / maxScrollPosition;
            
            // Calculate new Y position (from -30px to 0px)
            const newYPosition = -30 + (scrollPercentage * 30);
            
            // Apply the transform
            meltElement.style.transform = `translateY(${newYPosition}px)`;
          } else {
            // Keep it at final position once scroll exceeds max
            meltElement.style.transform = 'translateY(0)';
          }
        });
      }
    });
  </script>    
  <script>
    // Intersection Observer to detect when elements enter the viewport
document.addEventListener('DOMContentLoaded', function() {
  // Elements to animate - EXCLUDING hero section and meltdown
  const animatedElements = [
    { selector: '.Menu h2', animation: 'fade-up', delay: 0.1 },
    { selector: '.category-card', animation: 'fade-in-scale', delay: 0.3 },
    { selector: '.search-section', animation: 'fade-up', delay: 0.1 },
    { selector: '.custom-cake-banner', animation: 'fade-in', delay: 0.2 },
    { selector: '.product-card', animation: 'fade-up', delay: 0},
    { selector: '.back-to-top', animation: 'fade-in', delay: 0.1 }
  ];

  // Create animation classes for each element
  animatedElements.forEach(item => {
    const elements = document.querySelectorAll(item.selector);
    elements.forEach((element, index) => {
      // Add initial state class
      element.classList.add('animate-on-scroll');
      element.classList.add(item.animation);
      
      // Add staggered delay for groups of elements
      if (elements.length > 1) {
        element.style.transitionDelay = `${item.delay * (index + 1)}s`;
      } else {
        element.style.transitionDelay = `${item.delay}s`;
      }
    });
  });

  // Create the Intersection Observer
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      // Add the visible class when element enters viewport
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        // Unobserve after animation is triggered
        observer.unobserve(entry.target);
      }
    });
  }, {
    root: null, // viewport
    threshold: 0.1, // trigger when 10% of the element is visible
    rootMargin: '0px 0px -50px 0px' // trigger slightly before element enters viewport
  });

  // Observe all elements with animate-on-scroll class
  document.querySelectorAll('.animate-on-scroll').forEach(element => {
    observer.observe(element);
  });

  // Special handling for product cards that are added dynamically
  // This will be called after products are displayed
  window.animateNewProducts = function() {
    const productCards = document.querySelectorAll('.product-card:not(.animate-on-scroll)');
    
    productCards.forEach((card, index) => {
      card.classList.add('animate-on-scroll');
      card.classList.add('fade-up');
      card.style.transitionDelay = `${0.1 * (index % 4 + 1)}s`; // Stagger by row (assuming 4 cards per row)
      observer.observe(card);
    });
  };
});
  </script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Custom Cake Modal logic
    const customOrderBtn = document.getElementById('customOrderBtn');
    const customCakeModal = document.getElementById('customCakeModal');
    const closeModal = document.getElementById('closeModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const orderOptions = document.querySelectorAll('.order-option');
    const startCustomizingBtn = document.querySelector('.start-customizing-btn');
    const chooseLaterBtn = document.querySelector('.choose-later-btn');
    const photoUploadModal = document.getElementById('photoUploadModal');
    const closePhotoModal = document.getElementById('closePhotoModal');
    const aiCakesModal = document.getElementById('aiCakesModal');
    const closeAiModal = document.getElementById('closeAiModal');

    // Helper to open a modal with overlay
    function openModal(modal) {
        if (!modal) return;
        modalOverlay.style.display = 'block';
        modal.style.display = 'block';
        // Force reflow
        void modal.offsetWidth;
        modalOverlay.classList.add('active');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Helper to close all modals
    function closeAllModals() {
        const modals = [customCakeModal, photoUploadModal, aiCakesModal];
        modals.forEach(modal => {
            if (modal) {
                modal.classList.remove('active');
            }
        });
        if (modalOverlay) {
            modalOverlay.classList.remove('active');
        }
        setTimeout(() => {
            modals.forEach(modal => {
                if (modal) modal.style.display = 'none';
            });
            if (modalOverlay) modalOverlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // Open custom order modal
    if (customOrderBtn) {
        customOrderBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openModal(customCakeModal);
            // Pre-select the first option
            if (orderOptions.length > 0) {
                orderOptions[0].classList.add('selected');
                if (startCustomizingBtn) {
                    startCustomizingBtn.textContent = 'START CUSTOMIZING';
                }
            }
        });
    }

    // Close modals
    [closeModal, closePhotoModal, closeAiModal].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                closeAllModals();
            });
        }
    });

    // Overlay click closes modals
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                closeAllModals();
            }
        });
    }

    // Option selection logic
    orderOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            // Remove selected class from all options
            orderOptions.forEach(opt => opt.classList.remove('selected'));
            // Add selected class to clicked option
            this.classList.add('selected');
            // Update button text based on selection
            const title = this.querySelector('h3')?.textContent?.trim();
            if (startCustomizingBtn) {
                startCustomizingBtn.textContent = (title === 'Build with AICakes') ? 'START CUSTOMIZING' : 'UPLOAD PHOTO';
            }
        });
    });

    // Start customizing button logic
    if (startCustomizingBtn) {
        startCustomizingBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const selected = document.querySelector('.order-option.selected');
            if (!selected) {
                alert('Please select an option to continue.');
                return;
            }

            const title = selected.querySelector('h3')?.textContent?.trim();
            if (title === 'Build with AICakes') {
                // Switch to AI cakes modal
                customCakeModal.classList.remove('active');
                setTimeout(() => {
                    customCakeModal.style.display = 'none';
                    openModal(aiCakesModal);
                }, 300);
            } else if (title === 'Upload a Photo') {
                // Switch to photo upload modal
                customCakeModal.classList.remove('active');
                setTimeout(() => {
                    customCakeModal.style.display = 'none';
                    openModal(photoUploadModal);
                }, 300);
            }
        });
    }

    // Choose later button
    if (chooseLaterBtn) {
        chooseLaterBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeAllModals();
        });
    }

    // AI Cake Form submission is handled by aicakes.js
});
  </script>
<script>
window.isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
window.userId = <?php echo isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 'null'; ?>;
window.userRole = <?php echo isset($_SESSION['role']) ? json_encode($_SESSION['role']) : 'null'; ?>;
  </script>
<script>
// Debug logging
console.log('Script starting...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Initialize products array
    window.products = [];
    <?php
    // Get products from database
    $sql = "SELECT * FROM products ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "window.products.push({
                id: '" . $row['product_id'] . "',
                name: '" . addslashes($row['name']) . "',
                price: " . $row['price'] . ",
                image: '" . $row['image'] . "'
            });\n";
        }
    }
    ?>
    console.log('Products loaded:', window.products);

    // Cart elements
    const cartIcon = document.getElementById('cartIcon');
    const cartPopup = document.getElementById('cartPopup');
    const closeCart = document.getElementById('closeCart');
    const shopMoreBtn = document.getElementById('shopMoreBtn');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const modalOverlay = document.getElementById('modalOverlay');
    const checkoutModal = document.getElementById('checkoutModal');
    const closeCheckout = document.getElementById('closeCheckout');
    const backToCart = document.querySelector('.back-to-cart');
    const cartPopupItems = document.getElementById('cartPopupItems');

    // Open cart popup
    if (cartIcon && cartPopup && modalOverlay) {
        cartIcon.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modalOverlay.style.display = 'block';
            cartPopup.style.display = 'block';
            void cartPopup.offsetWidth;
            modalOverlay.classList.add('active');
            cartPopup.classList.add('active');
            document.body.style.overflow = 'hidden';
            updateCartUI();
        });
    }

    function closeCartPopup() {
        cartPopup.classList.remove('active');
        modalOverlay.classList.remove('active');
        setTimeout(() => {
            cartPopup.style.display = 'none';
            modalOverlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }

    if (closeCart) {
        closeCart.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeCartPopup();
        });
    }

    if (shopMoreBtn) {
        shopMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeCartPopup();
        });
    }

    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!window.cart || window.cart.length === 0) {
                alert('Your cart is empty. Please add items before checking out.');
                return;
            }
            cartPopup.classList.remove('active');
            cartPopup.style.display = 'none';
            checkoutModal.style.display = 'block';
            setTimeout(() => checkoutModal.classList.add('active'), 10);
            modalOverlay.style.display = 'block';
            modalOverlay.classList.add('active');
            updateCartUI(); // Update checkout items
        });
    }

    // Back to cart from checkout
    if (backToCart) {
        backToCart.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            checkoutModal.classList.remove('active');
            setTimeout(() => { checkoutModal.style.display = 'none'; }, 300);
            cartPopup.style.display = 'block';
            setTimeout(() => { cartPopup.classList.add('active'); }, 10);
            modalOverlay.style.display = 'block';
            modalOverlay.classList.add('active');
        });
    }

    // Close cart or checkout when clicking outside
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                if (cartPopup.classList.contains('active')) closeCartPopup();
                if (checkoutModal.classList.contains('active')) {
                    checkoutModal.classList.remove('active');
                    setTimeout(() => {
                        checkoutModal.style.display = 'none';
                        modalOverlay.style.display = 'none';
                        document.body.style.overflow = 'auto';
                    }, 300);
                }
            }
        });
    }

    // Event delegation for cart popup items
    if (cartPopupItems) {
        cartPopupItems.addEventListener('click', function(e) {
            const cartItem = e.target.closest('.cart-popup-item');
            if (!cartItem) return;

            // Get either the index (for custom cakes) or id (for regular products)
            const index = cartItem.dataset.index;
            const productId = cartItem.dataset.id;

            // Handle remove button click
            if (e.target.closest('.cart-item-remove')) {
                console.log('Remove clicked - index:', index, 'productId:', productId);
                
                if (index !== undefined && index !== null) {
                    // Remove custom cake using index
                    const indexNum = parseInt(index);
                    console.log('Removing custom cake at index:', indexNum);
                    window.cart.splice(indexNum, 1);
                    localStorage.setItem('cart', JSON.stringify(window.cart));
                    updateCartUI();
                    updateCartCount(); // Update the cart count after removal
                } else if (productId) {
                    // Remove regular product using id
                    removeFromCart(productId);
                }
            }

            // Handle quantity buttons
            if (e.target.classList.contains('cart-plus')) {
                const quantityElement = cartItem.querySelector('.cart-quantity');
                const currentQuantity = parseInt(quantityElement.textContent);
                if (index !== undefined && index !== null) {
                    // Update custom cake quantity
                    const indexNum = parseInt(index);
                    window.cart[indexNum].quantity = currentQuantity + 1;
                    localStorage.setItem('cart', JSON.stringify(window.cart));
                    updateCartUI();
                    updateCartCount();
                } else if (productId) {
                    // Update regular product quantity
                    updateCartQuantity(productId, currentQuantity + 1);
                }
            }

            if (e.target.classList.contains('cart-minus')) {
                const quantityElement = cartItem.querySelector('.cart-quantity');
                const currentQuantity = parseInt(quantityElement.textContent);
                if (currentQuantity > 1) {
                    if (index !== undefined && index !== null) {
                        // Update custom cake quantity
                        const indexNum = parseInt(index);
                        window.cart[indexNum].quantity = currentQuantity - 1;
                        localStorage.setItem('cart', JSON.stringify(window.cart));
                        updateCartUI();
                        updateCartCount();
                    } else if (productId) {
                        // Update regular product quantity
                        updateCartQuantity(productId, currentQuantity - 1);
                    }
                } else {
                    if (index !== undefined && index !== null) {
                        // Remove custom cake
                        const indexNum = parseInt(index);
                        window.cart.splice(indexNum, 1);
                        localStorage.setItem('cart', JSON.stringify(window.cart));
                        updateCartUI();
                        updateCartCount();
                    } else if (productId) {
                        // Remove regular product
                        removeFromCart(productId);
                    }
                }
            }
        });
    }

    // Add to cart from product card
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-to-order')) {
            e.stopPropagation(); // Prevent triggering product card click (modal)
            const productCard = e.target.closest('.product-card');
            if (!productCard) return;
            const productId = productCard.getAttribute('data-id');
            // Get the quantity from the sibling .quantity element
            const quantityElem = productCard.querySelector('.quantity');
            // FIX: Always parseInt and ensure only 1 add per click
            const quantity = quantityElem ? parseInt(quantityElem.textContent, 10) : 1;
            // Prevent double add: only call addToCart here, not in any other handler for this button
            addToCart(productId, quantity);
            alert('Added to cart!');
        }
    });

    // Product card click: open modal only if not clicking on .add-to-order or .quantity-btn
    const productCards = document.querySelectorAll('.product-card');
    const productModal = document.getElementById('productModal');
    const productDetailImage = document.getElementById('productDetailImage');
    const productDetailTitle = document.getElementById('productDetailTitle');
    const productDetailSubtitle = document.getElementById('productDetailSubtitle');
    const productDetailDescription = document.getElementById('productDetailDescription');
    const productDetailPrice = document.getElementById('productDetailPrice');
    const productDetailAvailability = document.getElementById('productDetailAvailability');
    const productDetailAddToCart = document.getElementById('productDetailAddToCart');
    const backToMenu = document.getElementById('backToMenu');

    productCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Prevent modal if clicking on add-to-order or quantity buttons
            if (
                e.target.classList.contains('add-to-order') ||
                e.target.classList.contains('quantity-btn') ||
                e.target.closest('.add-to-order') ||
                e.target.closest('.quantity-btn')
            ) {
                return;
            }
            const productId = this.getAttribute('data-id');
            const product = window.products.find(p => p.id == productId);
            if (!product) return;

            // Fill modal content
            productDetailImage.src = product.image;
            productDetailTitle.textContent = product.name;
            productDetailSubtitle.textContent = product.subtitle || '';
            productDetailDescription.textContent = product.description || 'No description available.';
            productDetailPrice.textContent = `Php ${product.price.toLocaleString()}`;
            productDetailAvailability.textContent = product.availability ? 'In Stock' : 'Out of Stock';

            // Reset modal quantity to 1
            const modalQtyElem = document.querySelector('.product-detail-quantity-value');
            if (modalQtyElem) modalQtyElem.textContent = '1';

            // Set product id for modal add-to-cart button
            if (productDetailAddToCart) productDetailAddToCart.setAttribute('data-product-id', product.id);

            // Show modal
            productModal.style.display = 'flex';
            setTimeout(() => productModal.classList.add('active'), 10);
            document.body.style.overflow = 'hidden';
        });
    });

    // Modal add to cart: use modal quantity
    if (productDetailAddToCart) {
        productDetailAddToCart.addEventListener('click', function(e) {
            e.stopPropagation();
            const productId = this.getAttribute('data-product-id');
            const qtyElem = document.querySelector('.product-detail-quantity-value');
            const quantity = qtyElem ? parseInt(qtyElem.textContent) : 1;
            addToCart(productId, quantity);
            alert('Added to cart!');
            // Close modal
            productModal.classList.remove('active');
            setTimeout(() => {
                productModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        });
    }

    // Close modal and go back to menu
    if (backToMenu) {
        backToMenu.addEventListener('click', function(e) {
            e.preventDefault();
            productModal.classList.remove('active');
            setTimeout(() => {
                productModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        });
    }
});
  </script>
<!-- Add this after your checkout modal and before </body> -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Checkout form submission
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // --- Prevent admin from placing orders ---
            if (window.userRole === 'admin') {
                alert('Admin accounts cannot place orders. Please log in as a customer.');
                return;
            }

            // Gather form data
            const fullname = document.getElementById('fullname').value.trim();
            const address = document.getElementById('address').value.trim();
            const deliveryDate = document.getElementById('deliveryDate').value;
            const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || '';
            const deliveryOption = document.querySelector('input[name="deliveryOption"]:checked')?.value || '';
            const cart = window.cart || [];

            // Basic validation
            if (!fullname || !address || !deliveryDate || !paymentMethod || cart.length === 0) {
                alert('Please fill in all required fields and add at least one item to your cart.');
                return;
            }

            // Calculate total (in case backend expects it)
            let total = 0;
            cart.forEach(item => {
                total += (parseFloat(item.price) || 0) * (parseInt(item.quantity) || 1);
            });

            // --- Ensure user_id is sent and matches logged-in user ---
            const user_id = window.userId !== undefined && window.userId !== null ? window.userId : null;

            // Prepare order data (with both flat and nested for backend compatibility)
            const orderData = {
                user_id, // <-- always send user_id
                fullname,
                address,
                deliveryDate,
                paymentMethod,
                deliveryOption,
                cart: cart,
                total: total,
                customer: {
                    fullname,
                    address,
                    deliveryDate,
                    paymentMethod,
                    deliveryMethod: deliveryOption
                },
                items: cart
            };

            // Check for custom cakes in the cart
            const hasCustomCakes = cart.some(item => item.type === 'custom' && item.details?.isAIGenerated);
            
            fetch('process_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Clear cart
                    window.cart = [];
                    localStorage.removeItem('cart');
                    if (typeof updateCartUI === 'function') updateCartUI();

                    // Show confirmation modal
                    document.getElementById('orderConfirmationModal').style.display = 'block';
                    document.getElementById('orderConfirmationModal').classList.add('active');
                    document.getElementById('confirmationOrderId').textContent = result.order_id || result.orderId || 'N/A';
                    document.getElementById('confirmationOrderDate').textContent = result.order_date || new Date().toLocaleDateString();

                    // Hide checkout modal
                    document.getElementById('checkoutModal').classList.remove('active');
                    setTimeout(() => {
                        document.getElementById('checkoutModal').style.display = 'none';
                        document.body.style.overflow = 'auto';
                    }, 300);
                } else {
                    console.error("Order failed:", result);
                    alert(result.message || 'Order failed. Please try again. There might be an issue with image size for custom cakes.');
                }
            })
            .catch(err => {
                console.error("Order error:", err);
                alert('Order processing failed. This may be due to large custom cake images. Please try again or contact support.');
                console.error(err);
            });
        });
    }

    // Close confirmation modal on button click
    const checkStatusBtn = document.getElementById('checkStatusBtn');
    if (checkStatusBtn) {
        checkStatusBtn.addEventListener('click', function() {
            // Redirect to account.php#myorders
            window.location.href = 'account.php#myorders';
        });
    }

    // ...existing code...
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all category cards and product cards
    const categoryCards = document.querySelectorAll('.category-card');
    const productCards = document.querySelectorAll('.product-card');
    const filterAvailability = document.getElementById('filterAvailability');
    const filterSize = document.getElementById('filterSize');
    const searchInput = document.querySelector('.search-input');

    // Store original products for reset
    const originalProducts = Array.from(productCards);

    // Add click event to category cards
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove active class from all cards
            categoryCards.forEach(c => c.style.backgroundColor = '#F3F4F6');
            // Add active class to clicked card
            this.style.backgroundColor = '#FFD7E9';

            const category = this.querySelector('.category-name').textContent.toLowerCase();
            filterProducts();
        });
    });

    // Add change event to filter dropdowns
    filterAvailability.addEventListener('change', filterProducts);
    filterSize.addEventListener('change', filterProducts);

    // Add input event to search
    searchInput.addEventListener('input', filterProducts);

    function filterProducts() {
        const selectedCategory = Array.from(categoryCards).find(card => 
            card.style.backgroundColor === 'rgb(255, 215, 233)')?.querySelector('.category-name').textContent.toLowerCase() || 'all';
        const selectedAvailability = filterAvailability.value.toLowerCase();
        const selectedSize = filterSize.value.toLowerCase();
        const searchTerm = searchInput.value.toLowerCase();

        originalProducts.forEach(product => {
            const productName = product.querySelector('.product-name').textContent.toLowerCase();
            const productPrice = parseFloat(product.querySelector('.product-price').textContent.replace(/[^0-9.]/g, ''));
            
            // Get product category (you'll need to add data-category to your PHP product output)
            const productCategory = product.getAttribute('data-category')?.toLowerCase() || 'all';

            // Category filter
            const matchesCategory = selectedCategory === 'all' || productCategory === selectedCategory;

            // Availability filter (you'll need to add data-availability to your PHP product output)
            const productAvailability = product.getAttribute('data-availability')?.toLowerCase() || 'in stock';
            const matchesAvailability = selectedAvailability === 'availability' || productAvailability === selectedAvailability;

            // Size/Price filter
            let matchesSize = true;
            if (selectedSize !== 'size') {
                if (selectedSize === 'small') matchesSize = productPrice < 200;
                else if (selectedSize === 'medium') matchesSize = productPrice >= 200 && productPrice <= 500;
                else if (selectedSize === 'large') matchesSize = productPrice > 500;
            }

            // Search filter
            const matchesSearch = productName.includes(searchTerm);

            // Show/hide product based on all filters
            if (matchesCategory && matchesAvailability && matchesSize && matchesSearch) {
                product.style.display = '';
                product.style.animation = 'fadeIn 0.5s ease-out';
            } else {
                product.style.display = 'none';
            }
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Price calculation for custom cake
    const basePrices = {
        '6': 500.00,
        '8': 800.00,
        '10': 1200.00,
        '12': 1500.00
    };

    const flavorPrices = {
        'Vanilla': 0,
        'Chocolate': 50,
        'Red Velvet': 100,
        'Carrot': 80,
        'Ube': 70
    };

    const fillingPrices = {
        'Buttercream': 0,
        'Chocolate Ganache': 100,
        'Fresh Fruit': 150,
        'Custard': 80
    };

    const frostingPrices = {
        'Buttercream': 0,
        'Fondant': 200,
        'Whipped Cream': 50,
        'Ganache': 150
    };

    function updateEstimatedPrice() {
        const size = document.getElementById('cakeSize').value;
        const flavor = document.getElementById('cakeFlavor').value;
        const filling = document.getElementById('fillingType').value;
        const frosting = document.getElementById('frostingType').value;
        const type = document.getElementById('cakeType').value;
        const tiers = parseInt(document.getElementById('cakeTiers').value) || 0;

        let basePrice = 0;
        
        // Base price by size
        switch(size) {
            case '6': basePrice = 800; break;
            case '8': basePrice = 1200; break;
            case '10': basePrice = 1800; break;
            case '12': basePrice = 2500; break;
            default: basePrice = 0;
        }

        // Additional cost for premium flavors
        if (['redvelvet', 'carrot', 'ube'].includes(flavor)) {
            basePrice += 200;
        }

        // Additional cost for premium fillings
        if (['ganache', 'fruit'].includes(filling)) {
            basePrice += 150;
        }

        // Additional cost for premium frostings
        if (['fondant', 'ganache'].includes(frosting)) {
            basePrice += 300;
        }

        // Additional cost based on cake type
        switch(type) {
            case 'wedding': basePrice *= 2; break; // Wedding cakes are most premium
            case 'anniversary': basePrice *= 1.5; break; // Anniversary cakes are special
            case 'custom': basePrice *= 1.3; break; // Custom cakes need extra attention
            // Birthday cakes use base price
        }

        // Additional cost for multiple tiers (exponential increase for wedding cakes)
        if (tiers > 1) {
            if (type === 'wedding') {
                basePrice *= (1 + ((tiers - 1) * 1.0)); // Each tier adds 100% for wedding cakes
            } else {
                basePrice *= (1 + ((tiers - 1) * 0.8)); // Each tier adds 80% for other cakes
            }
        }

        // Update the price display
        const priceElement = document.getElementById('estimatedPrice');
        priceElement.textContent = `₱${basePrice.toFixed(2)}`;
    }

    // Add event listeners for price updates
    ['cakeSize', 'cakeFlavor', 'fillingType', 'frostingType', 'cakeType', 'cakeTiers'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', updateEstimatedPrice);
        }
    });

    // Handle custom order form submission
    const cakeDetailsForm = document.getElementById('cakeDetailsForm');
    if (cakeDetailsForm) {
        cakeDetailsForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Check if user is logged in
            if (!window.isLoggedIn) {
                alert('Please log in to place a custom order.');
                return;
            }

            // Get form data
            const formData = new FormData(this);
            const calculatedPrice = parseFloat(this.dataset.calculatedPrice || 0);
            
            // Get the uploaded image
            const fileInput = document.getElementById('cakePhotoInput');
            if (!fileInput.files.length) {
                alert('Please upload a reference photo for your cake.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                // Create custom cake cart item
                const customCake = {
                    type: 'custom',
                    name: 'Custom Cake',
                    price: calculatedPrice,
                    quantity: 1,
                    image: e.target.result,
                    details: {
                        size: document.getElementById('cakeSize').value,
                        flavor: document.getElementById('cakeFlavor').value,
                        filling: document.getElementById('fillingType').value,
                        frosting: document.getElementById('frostingType').value,
                        instructions: document.getElementById('specialInstructions').value
                    }
                };

                // Add to cart
                if (!window.cart) window.cart = [];
                window.cart.push(customCake);
                
                // Save cart to localStorage
                localStorage.setItem('cart', JSON.stringify(window.cart));

                // Update cart UI
                updateCartCount();
                
                // Close photo upload modal
                const photoUploadModal = document.getElementById('photoUploadModal');
                photoUploadModal.classList.remove('active');
                modalOverlay.classList.remove('active');
                setTimeout(() => {
                    photoUploadModal.style.display = 'none';
                    modalOverlay.style.display = 'none';
                }, 300);

                alert('Custom cake added to cart!');
            };
            reader.readAsDataURL(fileInput.files[0]);
        });
    }

    // Update cart UI function
    function updateCartUI() {
        const cartPopupItems = document.getElementById('cartPopupItems');
        const orderItemsList = document.getElementById('orderItemsList');
        let total = 0;

        if (!window.cart || !window.cart.length) {
            if (cartPopupItems) cartPopupItems.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
            if (orderItemsList) orderItemsList.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
            document.getElementById('cartTotal').textContent = '₱0.00';
            return;
        }

        let cartHTML = '';
        let checkoutHTML = '';

        window.cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            if (item.type === 'custom') {
                // Get cake details
                const isAIGenerated = item.details.isAIGenerated ? 'AI-Generated' : '';
                const cakeType = item.details.cakeType ? `${item.details.cakeType.charAt(0).toUpperCase() + item.details.cakeType.slice(1)}` : '';
                const tiers = item.details.tiers ? `${item.details.tiers} tier` : '';
                
                // Custom cake display in cart
                cartHTML += `
                    <div class="cart-popup-item" data-index="${index}" data-type="custom">
                        <img src="${item.image}" alt="Custom Cake" class="cart-item-image">
                        <div class="cart-item-details">
                            <h4>${item.name}</h4>
                            <p>${item.details.size}" ${item.details.flavor} ${isAIGenerated}</p>
                            <p class="cart-item-price">₱${item.price.toFixed(2)}</p>
                        </div>
                        <div class="cart-item-quantity">
                            <button class="cart-minus">-</button>
                            <span class="cart-quantity">${item.quantity}</span>
                            <button class="cart-plus">+</button>
                        </div>
                        <button class="cart-item-remove">&times;</button>
                    </div>
                `;

                // Custom cake display in checkout
                checkoutHTML += `
                    <div class="checkout-item">
                        <img src="${item.image}" alt="Custom Cake" class="checkout-item-image">
                        <div class="checkout-item-details">
                            <h4>${item.name} ${isAIGenerated}</h4>
                            <p>${cakeType} ${tiers} ${item.details.size}" ${item.details.flavor} cake</p>
                            <p>Filling: ${item.details.filling}</p>
                            <p>Frosting: ${item.details.frosting}</p>
                            <p class="checkout-item-price">₱${item.price.toFixed(2)} x ${item.quantity}</p>
                        </div>
                    </div>
                `;
            } else {
                // Regular product display (existing code)
                cartHTML += `
                    <div class="cart-popup-item" data-index="${index}">
                        <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                        <div class="cart-item-details">
                            <h4>${item.name}</h4>
                            <p class="cart-item-price">₱${item.price.toFixed(2)}</p>
                        </div>
                        <div class="cart-item-quantity">
                            <button class="cart-minus">-</button>
                            <span class="cart-quantity">${item.quantity}</span>
                            <button class="cart-plus">+</button>
                        </div>
                        <button class="cart-item-remove">&times;</button>
                    </div>
                `;

                checkoutHTML += `
                    <div class="checkout-item">
                        <img src="${item.image}" alt="${item.name}" class="checkout-item-image">
                        <div class="checkout-item-details">
                            <h4>${item.name}</h4>
                            <p class="checkout-item-price">₱${item.price.toFixed(2)} x ${item.quantity}</p>
                        </div>
                    </div>
                `;
            }
        });

        if (cartPopupItems) cartPopupItems.innerHTML = cartHTML;
        if (orderItemsList) orderItemsList.innerHTML = checkoutHTML;
        document.getElementById('cartTotal').textContent = `₱${total.toFixed(2)}`;
    }

    // Update cart count function
    function updateCartCount() {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            const count = window.cart ? window.cart.reduce((total, item) => total + item.quantity, 0) : 0;
            cartCount.textContent = count.toString();
        }
    }

    // Load cart from localStorage on page load
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        window.cart = JSON.parse(savedCart);
        updateCartCount();
    }
});
</script>
<style>
.price-estimate-section {
    margin: 20px 0;
    padding: 15px;
    background-color: #f8f8f8;
    border-radius: 8px;
    text-align: center;
}

.price-estimate-section h4 {
    margin-bottom: 10px;
    color: #333;
}

.price-range {
    font-size: 24px;
    font-weight: bold;
    color: #E84B8A;
    margin: 10px 0;
}

.price-note {
    font-size: 12px;
    color: #666;
    font-style: italic;
}

.preview-container {
    display: none;
    position: relative;
    width: 100%;
    max-width: 300px;
    margin: 0 auto;
}

.preview-image {
    width: 100%;
    height: auto;
    max-height: 300px;
    object-fit: contain;
    border-radius: 8px;
}

.remove-image-btn {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background-color: #E84B8A;
    color: white;
    border: none;
    font-size: 16px;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.remove-image-btn:hover {
    background-color: #d83790;
}

.upload-area.dragover {
    background-color: #fce7f3;
    border-color: #E84B8A;
}

.file-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // File upload and preview functionality
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('cakePhotoInput');
    const previewContainer = document.getElementById('previewContainer');
    const uploadPlaceholder = document.querySelector('.upload-placeholder');

    // Handle drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.classList.remove('dragover');
        });
    });

    // Handle file drop
    uploadArea.addEventListener('drop', handleDrop);
    fileInput.addEventListener('change', handleFiles);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles({ target: { files: files } });
    }

    function handleFiles(e) {
        const files = e.target.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    displayPreview(e.target.result);
                };
                reader.readAsDataURL(file);
            } else {
                alert('Please upload an image file.');
            }
        }
    }

    function displayPreview(imageUrl) {
        // Clear previous preview
        previewContainer.innerHTML = '';
        
        // Create preview elements
        const previewImage = document.createElement('img');
        previewImage.src = imageUrl;
        previewImage.className = 'preview-image';
        
        const removeButton = document.createElement('button');
        removeButton.className = 'remove-image-btn';
        removeButton.innerHTML = '×';
        removeButton.onclick = removePreview;
        
        // Add elements to container
        previewContainer.appendChild(previewImage);
        previewContainer.appendChild(removeButton);
        
        // Hide placeholder, show preview
        uploadPlaceholder.style.display = 'none';
        previewContainer.style.display = 'block';
    }

    function removePreview() {
        // Clear file input
        fileInput.value = '';
        
        // Clear preview
        previewContainer.innerHTML = '';
        previewContainer.style.display = 'none';
        
        // Show placeholder
        uploadPlaceholder.style.display = 'flex';
    }
});
</script>
<script>
// AICakes Modal Price Estimation
function updateAICakesPrice() {
    const size = document.getElementById('aiCakeSize').value;
    const flavor = document.getElementById('aiCakeFlavor').value;
    const filling = document.getElementById('aiFillingType').value;
    const frosting = document.getElementById('aiFrostingType').value;
    const type = document.getElementById('aiCakeType').value;
    const tiers = parseInt(document.getElementById('aiCakeTiers').value) || 0;

    let basePrice = 0;
    
    // Base price by size
    switch(size) {
        case '6inch': basePrice = 800; break;
        case '8inch': basePrice = 1200; break;
        case '10inch': basePrice = 1800; break;
        case '12inch': basePrice = 2500; break;
        default: basePrice = 0;
    }

    // Additional cost for premium flavors
    if (['redvelvet', 'carrot'].includes(flavor)) {
        basePrice += 200;
    }

    // Additional cost for premium fillings
    if (['chocolate', 'fruit'].includes(filling)) {
        basePrice += 150;
    }

    // Additional cost for premium frostings
    if (['fondant', 'ganache'].includes(frosting)) {
        basePrice += 300;
    }

    // Additional cost based on cake type
    switch(type) {
        case 'wedding': basePrice *= 2; break; // Wedding cakes are most premium
        case 'anniversary': basePrice *= 1.5; break; // Anniversary cakes are special
        case 'custom': basePrice *= 1.3; break; // Custom cakes need extra attention
        // Birthday cakes use base price
    }

    // Additional cost for multiple tiers (exponential increase for wedding cakes)
    if (tiers > 1) {
        if (type === 'wedding') {
            basePrice *= (1 + ((tiers - 1) * 1.0)); // Each tier adds 100% for wedding cakes
        } else {
            basePrice *= (1 + ((tiers - 1) * 0.8)); // Each tier adds 80% for other cakes
        }
    }

    // Add AI design fee
    basePrice += 500;

    // Update the price display
    const priceElement = document.getElementById('aiEstimatedPrice');
    priceElement.textContent = `₱${basePrice.toFixed(2)}`;
}

// Add event listeners for AICakes form fields
document.getElementById('aiCakeSize')?.addEventListener('change', updateAICakesPrice);
document.getElementById('aiCakeFlavor')?.addEventListener('change', updateAICakesPrice);
document.getElementById('aiFillingType')?.addEventListener('change', updateAICakesPrice);
document.getElementById('aiFrostingType')?.addEventListener('change', updateAICakesPrice);
document.getElementById('aiCakeType')?.addEventListener('change', updateAICakesPrice);
document.getElementById('aiCakeTiers')?.addEventListener('change', updateAICakesPrice);
</script>
</body>
</html>