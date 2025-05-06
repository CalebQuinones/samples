<?php
include 'loginchecker.php';
include 'config.php';

redirectToLogin();

function updateValue($conn, $query, $param_type, $param_value)
{
    $stmt = $conn->prepare($query);
    if ($stmt) {
        // Corrected: Pass $param_value directly, not as spread
        $stmt->bind_param($param_type, ...$param_value);
        $stmt->execute();
        $stmt->close();
        return true; // Indicate success
    } else {
        return false; // Indicate failure
    }
}

// Start session if it's not already started
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];
    $phone = $_POST['phone'];
    $birthday = $_POST['birthday'];
    $address = $_POST['address'];
    $payment = $_POST['payment'];

    $loginUpdateFields = [];
    $loginParamTypes = "";
    $loginParamValues = [];

    $customerInfoUpdateFields = [];
    $customerInfoParamTypes = "";
    $customerInfoParamValues = [];

    if (!empty($Fname)) {
        $loginUpdateFields[] = "Fname = ?";
        $loginParamTypes .= "s";
        $loginParamValues[] = $Fname;
    }
    if (!empty($Lname)) {
        $loginUpdateFields[] = "Lname = ?";
        $loginParamTypes .= "s";
        $loginParamValues[] = $Lname;
    }

    if (!empty($phone)) {
        $customerInfoUpdateFields[] = "phone = ?";
        $customerInfoParamTypes .= "s";
        $customerInfoParamValues[] = $phone;
    }
    if (!empty($birthday)) {
        $customerInfoUpdateFields[] = "birthday = ?";
        $customerInfoParamTypes .= "s";
        $customerInfoParamValues[] = $birthday;
    }
    if (!empty($address)) {
        $customerInfoUpdateFields[] = "address = ?";
        $customerInfoParamTypes .= "s";
        $customerInfoParamValues[] = $address;
    }
    if (!empty($payment)) {
        $customerInfoUpdateFields[] = "payment = ?";
        $customerInfoParamTypes .= "s";
        $customerInfoParamValues[] = $payment;
    }

    $updateSuccess = true;
    $messages = [];

    // Update login table if there are changes
    if (count($loginUpdateFields) > 0) {
        $loginUpdateQuery = "UPDATE login SET " . implode(", ", $loginUpdateFields) . " WHERE user_id = ?";
        $loginParamTypes .= "i"; // Assuming user_id is an integer
        $loginParamValues[] = $user_id;
        if (!updateValue($conn, $loginUpdateQuery, $loginParamTypes, $loginParamValues)) { // Corrected
            $updateSuccess = false;
            $messages[] = "Failed to update login information.";
        }
    }

    // Update customerinfo table if there are changes
    if (count($customerInfoUpdateFields) > 0) {
        // Check if a record exists for the user in customerinfo
        $checkQuery = "SELECT COUNT(*) FROM customerinfo WHERE user_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("i", $user_id); // Assuming user_id is an integer
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            $customerInfoUpdateQuery = "UPDATE customerinfo SET " . implode(", ", $customerInfoUpdateFields) . " WHERE user_id = ?";
            $customerInfoParamTypes .= "i"; // Assuming user_id is an integer
            $customerInfoParamValues[] = $user_id;
            if (!updateValue($conn, $customerInfoUpdateQuery, $customerInfoParamTypes, $customerInfoParamValues)) { // Corrected
                $updateSuccess = false;
                $messages[] = "Failed to update profile information.";
            }
        } else {
            // If no record exists, insert a new one
            $insertFields = implode(", ", array_merge(['user_id'], array_map(function($field){ return str_replace(' = ?', '', $field); }, $customerInfoUpdateFields)));
            $placeholders = implode(", ", array_fill(0, count($customerInfoUpdateFields) + 1, '?'));
            $insertQuery = "INSERT INTO customerinfo ($insertFields) VALUES ($placeholders)";
            $insertParamTypes =  "i" . $customerInfoParamTypes;
            $insertParamValues = array_merge([$user_id], $customerInfoParamValues);

            $stmt = $conn->prepare($insertQuery);
            if ($stmt) {
                $stmt->bind_param($insertParamTypes, ...$insertParamValues);
                if (!$stmt->execute()) {
                    $updateSuccess = false;
                    $messages[] = "Failed to create profile information.";
                }
                $stmt->close();
            } else {
                $updateSuccess = false;
                $messages[] = "Failed to prepare insert statement for profile information.";
            }
        }
    }

    if ($updateSuccess) {
        $popupMessage = "Profile updated successfully!";
    } elseif (!empty($messages)) {
        $popupMessage = implode("<br>", $messages);
    } else {
        $popupMessage = "No changes were made to your profile.";
    }
}

if (isset($popupMessage)) {
    ?>
    <script type="text/javascript">
        alert("<?php echo addslashes($popupMessage); ?>");
        window.location.href = "account.php";
    </script>
    <?php
    exit();
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
    <link rel="stylesheet" href="updateaccount.css">
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
            <form action=" " method="POST" enctype="multipart/form-data">
            <div class="main-content">
                <!-- Personal Info Tab -->
                <div id="personal-info-tab" class="tab-content active">
                    <div class="profile-picture-container">
                        <label for="profile-picture-input">
                            <div class="profile-picture">
                                <span class="sr-only">Profile picture</span>
                                <img id="profile-picture-preview" src="" alt="" style="display: none; width: 100%; height: 100%; border-radius: 9999px; object-fit: cover;">
                            </div>
                        </label>

                    </div>
                    
                    <div class="form-container">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first-name">First name</label>
                                <input id="first-name" name="Fname" type="text" class="input">
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name</label>
                                <input id="last-name" name="Lname" type="text" class="input">
                            </div>
                        </div>
                    
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" type="tel" class="input">
                        </div>
                        
                        <div class="form-group">
                            <label for="birthday">Birthday</label>
                            <input id="birthday" name="birthday" type="date" class="input">
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" class="input textarea" rows="3" placeholder="Enter your address"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment-method">Payment Method</label>
                            <select id="payment-method" name="payment" class="input select">
                                <option value="" disabled selected>Select payment method</option>
                                <option value="credit-card">Credit Card</option>
                                <option value="debit-card">Debit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="bank-transfer">Bank Transfer</option>
                                <option value="cash">Cash on Delivery</option>
                            </select>
                        </div>
                        
                        <div class="button-container">
                            <button name="update" class="primary-button">Update Changes</button>
                        </div>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div id="orders-tab" class="tab-content">
                    <div class="orders-header">
                        <h2>Orders (2)</h2>
                    </div>
                    
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-header-item">
                                <div class="order-header-label">Order ID</div>
                                <div class="order-header-value">#ADJW349J</div>
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
                                <div class="order-header-value">30 July 2025</div>
                            </div>
                        </div>
                        
                        <div class="order-items">
                            <div class="order-item">
                                <img src="https://via.placeholder.com/60" alt="Strawberry Short Cake" class="order-item-image">
                                <div class="order-item-details">
                                    <div class="order-item-name">Strawberry Short Cake</div>
                                    <div class="order-item-meta">Php 700.00 | 1 Qty</div>
                                </div>
                            </div>
                            <div class="order-item">
                                <img src="https://via.placeholder.com/60" alt="1 Pack Chocolate Cookies" class="order-item-image">
                                <div class="order-item-details">
                                    <div class="order-item-name">1 Pack Chocolate Cookies</div>
                                    <div class="order-item-meta">Php 300 | 1 Qty</div>
                                </div>
                            </div>
                            <div class="order-item">
                                <img src="https://via.placeholder.com/60" alt="1 Pack Chocolate Cookies" class="order-item-image">
                                <div class="order-item-details">
                                    <div class="order-item-name">1 Pack Chocolate Cookies</div>
                                    <div class="order-item-meta">Php 300 | 1 Qty</div>
                                </div>
                            </div>
                            <div class="order-item">
                                <img src="https://via.placeholder.com/60" alt="Strawberry Short Cake" class="order-item-image">
                                <div class="order-item-details">
                                    <div class="order-item-name">Strawberry Short Cake</div>
                                    <div class="order-item-meta">Php 700.00 | 1 Qty</div>
                                </div>
                            </div>
                            
                            <div class="order-status">
                                <span class="status-badge">Delivered</span>
                                <span class="status-text">Your Order Status</span>
                            </div>
                            
                            <div class="order-actions">
                                <button class="primary-button">Track Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
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
                <a href="TriplesJ'sandroseBakery.php"><img src="logo.png" alt="Triple J & Rose's Bakery Logo"></a>
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

    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script>
        const profilePictureInput = document.getElementById('profile-picture-input');
        const profilePicturePreview = document.getElementById('profile-picture-preview');

        document.querySelector('.profile-picture').addEventListener('click', () => {
            profilePictureInput.click();
        });

        profilePictureInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
            
                    profilePicturePreview.src = e.target.result;
                    profilePicturePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
        
                profilePicturePreview.src = '';
                profilePicturePreview.style.display = 'none';
            }
        });
    </script>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Get DOM elements
        const cartIcon = document.getElementById('cartIcon');
        const cartPopup = document.getElementById('cartPopup');
        const closeCart = document.getElementById('closeCart');
        const shopMoreBtn = document.getElementById('shopMoreBtn');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const modalOverlay = document.getElementById('modalOverlay');

        // Add click event listeners
        cartIcon.addEventListener('click', function() {
            cartPopup.style.display = 'block';
            modalOverlay.style.display = 'block';
            updateCartUI();
        });

        closeCart.addEventListener('click', function() {
            cartPopup.style.display = 'none';
            modalOverlay.style.display = 'none';
        });

        shopMoreBtn.addEventListener('click', function() {
            cartPopup.style.display = 'none';
            modalOverlay.style.display = 'none';
        });

        checkoutBtn.addEventListener('click', function() {
            window.location.href = 'checkout.php';
        });

        // Initialize cart UI
        updateCartUI();
    });
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

