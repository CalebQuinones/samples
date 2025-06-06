<?php
include 'loginchecker.php';
include 'config.php';


redirectToLogin();


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_email = $_SESSION['user_email'];
$user_id = null; // Initialize user_id

// Fetch the user_id from the login table based on the email
$getIdQuery = "SELECT user_id FROM login WHERE email = ?";
$getIdStmt = $conn->prepare($getIdQuery);
if ($getIdStmt) {
    $getIdStmt->bind_param("s", $user_email);
    $getIdStmt->execute();
    $getIdStmt->bind_result($user_id);
    $getIdStmt->fetch();
    $getIdStmt->close();
}

if (!$user_id) {
    $popupMessage = "Error: User not found.";
    ?>
    <script type="text/javascript">
        alert("<?php echo addslashes($popupMessage); ?>");
        window.location.href = "account.php"; // Or wherever you want to redirect
    </script>
    <?php
    exit();
}



$Fname = "";
$Lname = "";
$email = "";
// Fetch data from login table
$query = "SELECT Fname, Lname, email FROM login WHERE email = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) { //check if there are any results
        $stmt->bind_result($Fname, $Lname, $email);
        $stmt->fetch();
    }
    $stmt->close();
}

$phone = "";
$birthday = "";
$address = "";
$payment = "";
$profpic = "";
// Fetch data from customerinfo table
$query = "SELECT phone, birthday, address, payment, profpic FROM customerinfo WHERE user_id = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
     if ($stmt->num_rows > 0) {  //check if there are any results
        $stmt->bind_result($phone, $birthday, $address, $payment, $profpic);
        $stmt->fetch();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Madimi+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Madimi+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="account.css">
    <link rel="stylesheet" href="order-confirmation.css">
    <script src="cart-manager.js"></script>
</head>
<body>
    <div class="top-banner">
    </div>
    <header>
        <nav class="container">
            <div class="logo">
                <img src="logo.png" alt="Triple J & Rose's Bakery Logo">
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
                <div class="profile-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="#E44486" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="#E44486" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
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
                <p class="empty-cart-message">Your cart is empty</p>
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
                <!-- Checkout content -->
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
                <span id="confirmationOrderId">TJR-12345</span>
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

    <!-- Modal Overlay -->
    <div class="modal-overlay" id="modalOverlay"></div>

    <!-- Melting Header Section -->
    <section class="header-section">
        <div class="melting-header">
            <img src="meltdown1.png" class="melt" alt="Melting Pink Background">
            <div class="page-title">
                <h2>My Account</h2>
            </div>
        </div>
    </section>

    <!-- Account Section - Updated to match new design -->
    <div class="conti">
        <div class="account-grid">
            <!-- Replace the current sidebar HTML with this -->
<div class="sidebar">
    <button id="personal-info-btn" class="sidebar-button active">Personal Information</button>
    <button id="orders-btn" class="sidebar-button">My Orders</button>
    <button id="logout-btn" class="sidebar-button">Log Out</button>
</div>


            <!-- Main Content -->
            <div class="main-content">
                <!-- Personal Info Tab -->
                <div id="personal-info-tab" class="tab-content active">
                    <div class="profile-picture-container" style="text-align: left; margin-left: 0;">
                        <div class="profile-initials" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; font-size: 2em; border-radius: 50%; margin-left: 0;">
                            <?php 
                                $initials = strtoupper(substr($Fname, 0, 1) . substr($Lname, 0, 1));
                                echo htmlspecialchars($initials);
                            ?>
                        </div>
                    </div>
                    
                    <div class="form-container">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first-name">First name</label>
                                <p class="first-name"><?php echo $Fname; ?></p>
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name</label>
                                <p class="last-name"><?php echo $Lname; ?></p>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <p class="email"><?php echo $email; ?></p>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <p id="phone"><?php echo $phone; ?></p>
                        </div>
                        
                        <div class="form-group">
                            <label for="birthday">Birthday</label>
                            <p id="birthday"><?php echo $birthday; ?></p>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <p id="address"><?php echo $address; ?></p>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment-method">Payment Method</label>
                            <p id="payment-method"><?php echo ucwords("$payment"); ?></p>
                        </div>
                        
                        <div class="button-container">
                            <a href= "updateaccount.php"><button class="primary-button">Edit Information</button><a>
                        </div>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div id="orders-tab" class="tab-content">
                    <div class="orders-header">
                        <h2>Orders (4)</h2>
                    </div>
                    
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-header-item">
                                <div class="order-header-label">Order ID</div>
                                <div class="order-header-value">ORD-025</div>
                            </div>
                            <div class="order-header-item">
                                <div class="order-header-label">Total Payment</div>
                                <div class="order-header-value">Php 1,300.00</div>
                            </div>
                            <div class="order-header-item">
                                <div class="order-header-label">Payment Method</div>
                                <div class="order-header-value">Cash</div>
                            </div>
                            <div class="order-header-item">
                                <div class="order-header-label">Estimated Delivery</div>
                                <div class="order-header-value">26 May 2025</div>
                            </div>
                        </div>
                        
                        <div class="order-items">
                            <div class="order-item">
                                <img src="1.png" alt="Strawberry Short Cake" class="order-item-image">
                                <div class="order-item-details">
                                    <div class="order-item-name">Wedding Cake</div>
                                    <div class="order-item-meta">Php 600.00 | 1 Qty</div>
                                </div>
                            </div>
                            <div class="order-item">
                                <img src="8.png" alt="1 Pack Chocolate Cookies" class="order-item-image">
                                <div class="order-item-details">
                                    <div class="order-item-name">Dog Cake</div>
                                    <div class="order-item-meta">Php 300 | 1 Qty</div>
                                </div>
                            </div>
                            <div class="order-item">
                                <img src="11.png" alt="1 Pack Chocolate Cookies" class="order-item-image">
                                <div class="order-item-details">
                                    <div class="order-item-name">Strawberry Cupcakes</div>
                                    <div class="order-item-meta">Php 100 | 1 Qty</div>
                                </div>
                            </div>
                            <div class="order-item">
                                <img src="4.png" alt="Strawberry Short Cake" class="order-item-image">
                                <div class="order-item-details">
                                    <div class="order-item-name">Father's Day</div>
                                    <div class="order-item-meta">Php 300.00 | 1 Qty</div>
                                </div>
                            </div>
                            
                            <div class="order-status">
                                <span class="status-badge">Delivered</span>
                                <span class="status-text">Your Order Status</span>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logout-modal" class="modal">
        <div class="modal-content">
            <h3>Confirm Logout</h3>
            <p>Are you sure you want to log out?</p>
            <div class="modal-actions">
                <button id="cancel-logout" class="outline-button">Cancel</button>
                <a href='logout.php'><button id="confirm-logout" name="logout" class="primary-button">Confirm</button></a>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        const personalInfoBtn = document.getElementById('personal-info-btn');
        const ordersBtn = document.getElementById('orders-btn');
        const personalInfoTab = document.getElementById('personal-info-tab');
        const ordersTab = document.getElementById('orders-tab');

        personalInfoBtn.addEventListener('click', () => {
            personalInfoBtn.classList.add('active');
            ordersBtn.classList.remove('active');
            personalInfoTab.classList.add('active');
            ordersTab.classList.remove('active');
        });

        ordersBtn.addEventListener('click', () => {
            ordersBtn.classList.add('active');
            personalInfoBtn.classList.remove('active');
            ordersTab.classList.add('active');
            personalInfoTab.classList.remove('active');
        });

        // Logout confirmation functionality
        const logoutBtn = document.getElementById('logout-btn');
        const logoutModal = document.getElementById('logout-modal');
        const cancelLogoutBtn = document.getElementById('cancel-logout');
        const confirmLogoutBtn = document.getElementById('confirm-logout');

        logoutBtn.addEventListener('click', () => {
            logoutModal.style.display = 'flex';
        });

        cancelLogoutBtn.addEventListener('click', () => {
            logoutModal.style.display = 'none';
        });

        confirmLogoutBtn.addEventListener('click', () => {
            // Here you would typically redirect to logout endpoint
            alert('Logged out successfully!');
            logoutModal.style.display = 'none';
        });

        // Close modal when clicking outside
        window.addEventListener('click', (event) => {
            if (event.target === logoutModal) {
                logoutModal.style.display = 'none';
            }
        });
    </script>

    <footer>
        <div class="burdir"></div>
        <div class="footer-content">
            <div class="footer-logo">
                <a href="TriplesJ'sandroseBakery.html"><img src="logo.png" alt="Triple J & Rose's Bakery Logo"></a>
            </div>
            <div class="footer-contact">
                <p>001 Road 10 Joseph Sitt Bagumbayan, Taguig City</p>
                <p>Call us: +63 918 746 4342</p>
                <p>Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="8fedeefceceee3e2eef6e3eae1eacfe8e2eee6e3a1ece0e2">[email&#160;protected]</a></p>
            </div>
            <div class="footer-social">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-pinterest"></i></a>
            </div>
            <div class="footer-links">
                <a href="Abouts.html">About Us</a>
                <a href="Contact.html">Contact Us</a>
                <a href="#help">Help</a>
                <a href="#privacy">Privacy Policy</a>
                <a href="">Sitemap</a>
            </div>
        </div>
        <div class="footer-copyright">
            <p>&copy; 2024 Triple J's Bakery. All rights reserved.</p>
        </div>
    </footer>

    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navigation toggle
            const navToggle = document.querySelector('.nav-toggle');
            const navLinks = document.querySelector('.nav-links');
    
            if (navToggle && navLinks) {
                navToggle.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                    
                    // Toggle hamburger to X
                    this.classList.toggle('active');
                });
            
                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    const isClickInsideNav = navLinks.contains(event.target) || navToggle.contains(event.target);
                    if (!isClickInsideNav && navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                        navToggle.classList.remove('active');
                    }
                });
            }

            // Account tabs functionality
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Hide all tab contents
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Show the corresponding tab content
                    const tabName = this.getAttribute('data-tab');
                    document.getElementById(`${tabName}-content`).classList.add('active');
                });
            });

            // Cart functionality
            const cartIcon = document.getElementById('cartIcon');
            const cartPopup = document.getElementById('cartPopup');
            const closeCart = document.getElementById('closeCart');
            
            if (cartIcon && cartPopup && closeCart) {
                cartIcon.addEventListener('click', function() {
                    cartPopup.style.display = 'block';
                    setTimeout(() => {
                        cartPopup.classList.add('active');
                    }, 10);
                });
                
                closeCart.addEventListener('click', function() {
                    cartPopup.classList.remove('active');
                    setTimeout(() => {
                        cartPopup.style.display = 'none';
                    }, 300);
                });
            }
        });
    </script>
<script>
    // Cart functionality
    let cart = [];

    // Get DOM elements
    const cartIcon = document.getElementById('cartIcon');
    const cartPopup = document.getElementById('cartPopup');
    const closeCart = document.getElementById('closeCart');
    const cartPopupItems = document.getElementById('cartPopupItems');
    const cartTotal = document.getElementById('cartTotal');
    const shopMoreBtn = document.getElementById('shopMoreBtn');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const cartCount = document.querySelector('.cart-count');
    
    // Function to add item to cart
    function addToCart(productId, quantity) {
        const product = products.find(p => p.id === productId);
        
        if (!product) return;
        
        // Check if product already exists in cart
        const existingItem = cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: quantity
            });
        }
        
        // Update cart UI
        updateCartUI();
        
        // Add bounce animation to cart icon
        cartIcon.classList.add('bounce');
        
        // Remove the class after animation completes
        setTimeout(() => {
            cartIcon.classList.remove('bounce');
        }, 500);
    }

    // Function to remove item from cart
    function removeFromCart(productId) {
        cart = cart.filter(item => item.id !== productId);
        updateCartUI();
    }

    // Function to update cart quantity
    function updateCartQuantity(productId, newQuantity) {
        const item = cart.find(item => item.id === productId);
        
        if (item) {
            if (newQuantity <= 0) {
                removeFromCart(productId);
            } else {
                item.quantity = newQuantity;
                updateCartUI();
            }
        }
    }

    // Function to calculate cart total
    function calculateTotal() {
        return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    // Function to update cart UI
    function updateCartUI() {
        // Update cart count
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
        
        // Update cart popup items
        cartPopupItems.innerHTML = '';
        
        if (cart.length === 0) {
            cartPopupItems.innerHTML = '<p class="empty-cart-message">Your cart is empty</p>';
        } else {
            cart.forEach(item => {
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
        
        // Update cart total
        cartTotal.textContent = `Php ${calculateTotal().toLocaleString()}`;
        
        // Also update checkout items list
        updateCheckoutItems();
    }

    // Function to update checkout items
    function updateCheckoutItems() {
        const orderItemsList = document.getElementById('orderItemsList');
        
        if (orderItemsList) {
            orderItemsList.innerHTML = '';
            
            cart.forEach(item => {
                const cartItem = document.createElement('div');
                cartItem.className = 'cart-item';
                
                cartItem.innerHTML = `
                    <div class="item-image">
                        <img src="${item.image}" alt="${item.name}">
                    </div>
                    <div class="item-details">
                        <h4>${item.name}</h4>
                        <p>Php ${item.price.toLocaleString()} × ${item.quantity}</p>
                    </div>
                `;
                
                orderItemsList.appendChild(cartItem);
            });
        }
    }

    // Event listener for cart icon
    cartIcon.addEventListener('click', function() {
        cartPopup.style.display = 'block';
        setTimeout(() => {
            cartPopup.classList.add('active');
        }, 10);
    });

    // Event listener for close cart button
    closeCart.addEventListener('click', function() {
        cartPopup.classList.remove('active');
        setTimeout(() => {
            cartPopup.style.display = 'none';
        }, 300);
    });

    // Event listener for shop more button
    shopMoreBtn.addEventListener('click', function() {
        cartPopup.classList.remove('active');
        setTimeout(() => {
            cartPopup.style.display = 'none';
        }, 300);
    });

    // Event listener for checkout button
    checkoutBtn.addEventListener('click', function() {
        if (cart.length === 0) {
            alert('Your cart is empty. Please add items before checking out.');
            return;
        }
        
        // Close cart popup
        cartPopup.classList.remove('active');
        setTimeout(() => {
            cartPopup.style.display = 'none';
        }, 300);
        
        // Open checkout modal
        const modalOverlay = document.getElementById('modalOverlay');
        const checkoutModal = document.getElementById('checkoutModal');
        
        if (modalOverlay) {
            modalOverlay.style.display = 'block';
            void modalOverlay.offsetWidth;
            modalOverlay.classList.add('active');
        }
        
        checkoutModal.style.display = 'block';
        void checkoutModal.offsetWidth;
        checkoutModal.classList.add('active');
        
        document.body.style.overflow = 'hidden';
    });

    // Event delegation for cart popup items
    cartPopupItems.addEventListener('click', function(e) {
        const cartItem = e.target.closest('.cart-popup-item');
        if (!cartItem) return;
        
        const productId = parseInt(cartItem.dataset.id);
        
        // Handle remove button click
        if e.target.closest('.cart-item-remove')) {
            removeFromCart(productId);
        }
        
        // Handle quantity buttons
        if (e.target.classList.contains('cart-plus')) {
            const quantityElement = cartItem.querySelector('.cart-quantity');
            const currentQuantity = parseInt(quantityElement.textContent);
            updateCartQuantity(productId, currentQuantity + 1);
        }
        
        if (e.target.classList.contains('cart-minus')) {
            const quantityElement = cartItem.querySelector('.cart-quantity');
            const currentQuantity = parseInt(quantityElement.textContent);
            if (currentQuantity > 1) {
                updateCartQuantity(productId, currentQuantity - 1);
            } else {
                removeFromCart(productId);
            }
        }
    });

    // Close checkout modal
    const closeCheckout = document.getElementById('closeCheckout');
    if (closeCheckout) {
        closeCheckout.addEventListener('click', function() {
            const modalOverlay = document.getElementById('modalOverlay');
            const checkoutModal = document.getElementById('checkoutModal');
            
            if (modalOverlay) {
                modalOverlay.classList.remove('active');
            }
            
            checkoutModal.classList.remove('active');
            
            setTimeout(() => {
                if (modalOverlay) {
                    modalOverlay.style.display = 'none';
                }
                checkoutModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        });
    }

    // Sample products data (you can replace this with your actual products)
    const products = [
        {
            id: 1,
            name: "Wedding Cake",
            price: 600,
            category: "Wedding Cakes",
            availability: "In Stock",
            image: "1.png"
        },
        {
            id: 2,
            name: "Number Cake",
            price: 300,
            category: "Birthday Cakes",
            availability: "In Stock",
            image: "17.png"
        },
        {
            id: 3,
            name: "Dog Cake",
            price: 1000,
            category: "Birthday Cakes",
            availability: "In Stock",
            image: "8.png"
        }
        // Add more products as needed
    ];

    // Initialize cart UI
    updateCartUI();
</script>
<script>
    // This assumes you're storing cart items in localStorage
function updateCartCount() {
    // Get cart items from localStorage (or your preferred storage method)
    const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    
    // Update the cart count display
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = cartItems.length;
        
        // Hide the count if cart is empty
        if (cartItems.length === 0) {
            cartCountElement.style.display = 'none';
        } else {
            cartCountElement.style.display = 'flex';
        }
    }
}

// Call this function when the page loads
document.addEventListener('DOMContentLoaded', updateCartCount);

// Call this function whenever items are added to or removed from the cart
// For example: after adding an item to cart
function addToCart(item) {
    // Get current cart
    const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    
    // Add new item
    cartItems.push(item);
    
    // Save updated cart
    localStorage.setItem('cartItems', JSON.stringify(cartItems));
    
    // Update the display
    updateCartCount();
}
</script>
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
<!-- Add this before your closing </body> tag -->
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
    
</body>
</html>

