<?php
include 'config.php';


if (isset($_POST['submit'])) {

    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];
    $email = $_POST['email'];
    $phone = $_POST['pnum'];
    $subject = $_POST['subject'];
    $message = $_POST['msg'];


    $stmt = $conn->prepare("INSERT INTO inquiry (Fname, Lname, email, Pnum, subject, msg) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $Fname, $Lname, $email, $phone, $subject, $message);


    if ($stmt->execute()) {
        // Display popup message
        echo "<script type='text/javascript'>
                alert('Thank you for your message, $Fname! We will get back to you shortly.');
                window.location.href = 'contact.php';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Madimi+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Madimi+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Contact.css">
    <link rel="stylesheet" href="order-confirmation.css">
    <script src="cart-manager.js"></script>

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
                  <span class="cart-count"></span>
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
                            <label for="email">Email Address</label>
                            <input type="email" id="email" placeholder="Enter your email here..." required>
                        </div>

                        <div class="form-group">
                            <label for="fullname">Fullname</label>
                            <input type="text" id="fullname" placeholder="Enter your fullname here..." required>
                        </div>

                        <div class="form-group">
                            <label for="address">Delivery Address</label>
                            <input type="text" id="address" placeholder="Enter your address..." required>
                        </div>

                        <div class="form-group">
                            <label for="deliveryDate">Delivery date/Pick-up date</label>
                            <input type="date" id="deliveryDate" required>
                        </div>

                        <div class="form-group">
                            <label>Payment</label>
                            <div class="payment-options">
                                <div class="payment-option">
                                    <input type="radio" id="cardPayment" name="paymentMethod" value="card" checked>
                                    <label for="cardPayment">Card</label>
                                </div>
                                <div class="payment-option">
                                    <input type="radio" id="gcashPayment" name="paymentMethod" value="gcash">
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
    <section>
           <img src="meltdown1.png" class="melt">
           <div clas="buong-content">
           <div class="contact">
             <h2>Contact Us</h2>
           </div>
    

           <div class="form-panel">
        <form action="" method="post">
        <h1>We'd Love To Hear From You!</h1>
        <p>Need assistance? Please complete our brief inquiry form to contact us. We will respond promptly.</p> <br>
        <br>

        <div class="form-row">
            <div class="form-group"><p>First Name</p>
                <input type="text" name="Fname" placeholder="Enter first name" required>
            </div>
            <div class="form-group"><p>Last Name</p>
                <input type="text" name="Lname" placeholder="Enter last name" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group"><p>Email Address</p>
                <input type="email" name="email" placeholder="Enter your email address" required>
            </div>
            <div class="form-group"><p>Number</p>
                <input type="tel" name="pnum" placeholder="+63" required>
            </div>
        </div>
        <div class="form-group">
            <p>Subject</p>
            <input type="text" name="subject" placeholder="Enter your subject of inquiry here" required>
        </div>
        <div class="form-group">
            <textarea name="msg" placeholder="Enter your message here" required></textarea>
        </div>
        <button type="submit" name="submit" class="submit">Submit</button>
        </form>
        </div>

        <div class = "kingina">
        <div class="business-hours-card">
            <div class="business-hours-title">
                <span class="clock-icon">🕒</span>
                <h2>Business Hours</h2>
            </div>
            <div class="business-hours-content">
                <div class="day">Monday - Friday</div>
                <div class="hours">7:00 AM - 8:00 PM</div>
                <div class="day">Saturday</div>
                <div class="hours">8:00 AM - 9:00 PM</div>
                <div class="day">Sunday</div>
                <div class="hours">9:00 AM - 6:00 PM</div>
            </div>
        </div>
        <h1 class="iframe"> <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48" height="48">
            <path 
              d="M24 4C16.268 4 10 10.268 10 18c0 10.5 14 22 14 22s14-11.5 14-22c0-7.732-6.268-14-14-14zm0 19c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z" 
              fill="#FF1493"
            />
          </svg>
          </svg>Find Us!</h1>
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15452.11962132778!2d121.0519816!3d14.4829723!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cf0c38d4663f%3A0x316dfb3b2003f0ce!2sTriple%20J%20and%20Rose&#39;s%20Bakery!5e0!3m2!1sen!2sph!4v1733152230083!5m2!1sen!2sph" 
    width="650" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
        


    <div class="contact-card">
        <div class="contact-header">
            In case you need more...
        </div>
        <div class="contact-body">
            <div class="contact-item">
                <div class="contact-item-icon">📍</div>
                <div class="contact-item-content">
                    <div class="contact-item-title">ADDRESS</div>
                    <div>345 Faulconer Drive, Suite 4•</div>
                    <div>Charlottesville</div>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-item-icon">✉️</div>
                <div class="contact-item-content">
                    <div class="contact-item-title">EMAIL</div>
                    <div><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="493d3b2039252c232b28222c3b30092e24282025672a2624">[email&#160;protected]</a></div>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-item-icon">📞</div>
                <div class="contact-item-content">
                    <div class="contact-item-title">CONTACT NUMBER</div>
                    <div>(123) 456-7890</div>
                </div>
            </div>
        </div>
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
              <p>Email: bascalmaylene@gmail.com</p>

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
</div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get DOM elements
        const cartIcon = document.getElementById('cartIcon');
        const cartPopup = document.getElementById('cartPopup');
        const closeCart = document.getElementById('closeCart');
        const shopMoreBtn = document.getElementById('shopMoreBtn');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const modalOverlay = document.getElementById('modalOverlay');
        const checkoutModal = document.getElementById('checkoutModal');
        const closeCheckout = document.getElementById('closeCheckout');
        const backToCart = document.querySelector('.back-to-cart');

        // Cart icon click
        cartIcon.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            modalOverlay.style.display = 'block';
            cartPopup.style.display = 'block';
            void cartPopup.offsetWidth;
            modalOverlay.classList.add('active');
            cartPopup.classList.add('active');
            document.body.style.overflow = 'hidden';
            if (typeof updateCartUI === 'function') updateCartUI();
        });

        function closeCartPopup() {
            cartPopup.classList.remove('active');
            modalOverlay.classList.remove('active');
            setTimeout(() => {
                cartPopup.style.display = 'none';
                modalOverlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }

        closeCart.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeCartPopup();
        });

        shopMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeCartPopup();
        });

        // Checkout button opens checkout modal
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
            setTimeout(() => { checkoutModal.classList.add('active'); }, 10);
            modalOverlay.style.display = 'block';
            modalOverlay.classList.add('active');
        });

        // Close checkout modal
        closeCheckout.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            checkoutModal.classList.remove('active');
            modalOverlay.classList.remove('active');
            setTimeout(() => {
                checkoutModal.style.display = 'none';
                modalOverlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        });

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

        // Close cart when clicking outside
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

        cartPopup.addEventListener('click', function(e) { e.stopPropagation(); });
        checkoutModal.addEventListener('click', function(e) { e.stopPropagation(); });

        if (typeof updateCartUI === 'function') updateCartUI();
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
</body>
</html>