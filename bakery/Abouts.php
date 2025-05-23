<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Madimi+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Madimi+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <title class="ito may bago">About</title>
    <link rel="stylesheet" href="Abouts.css">
    <link rel="stylesheet" href="order-confirmation.css">
    <script src="cart-confirmation.js"></script>
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
                <!-- Rest of checkout modal content -->
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
        <div class="none">
            <svg class="background" width="100%" height="100%" viewBox="0 0 1440 597" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <rect width="100%" height="100%" fill="url(#pattern0_546_19)"/>
                <defs>
                <pattern id="pattern0_546_19" patternContentUnits="objectBoundingBox" width="1" height="1">
                <use xlink:href="#image0_546_19" transform="matrix(0.000732064 0 0 0.00176578 0 -0.178061)"/>
                </pattern>
 <image id="image0_546_19" width="100%" height="768" xlink:href="data:image/png;base64,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"/></defs>
                </svg>
            <h1 class="text">ABOUT US</h1>
            <div class="content">
                <p> At Triple J and Rose's Bakery, we understand that every celebration deserves a unique touch. Our passion for baking is matched only by our dedication to crafting customized cakes that reflect your personal style and taste.</p>
                <img src="Logo.png" class="logof">
            </div>
        </div>
    </div>
    <img src="meltdown1.png" class="melt">
    </section>
    <div class="buong-content">
    <h1 class="ulo">OUR STORY</h1>
    <section class="history">
            <img src="mama.jpg" class="mama">
            <img src="papa.jpg" class="papa">
            <img src="oblong.png" class="oblong">
            <p class="ptext">Triple J & Rose's Bakery was invested and founded by the Andrade family; Roberto Andrade his wife Cleofe Andrade, Jojo Andrade, and Crizza Andrade.</p>
    </section>
    <section class="history1">
            <img src="pinsan.jpg" class="pinsan">
            <img src="oblong2.png" class="oblong2">
        <div class="htext2">
            <p>This bakery are managed by Maylene Bascal and Rowel Bascal. Founded in 2008, this bakery is still rising sales up today.</p>
        </div>
    </section>
    <section class="celebrate">
        <div class="banner-container">
            <div class="scrolling-text">
              <div class="text-content">
                <span class="text-item">A THANK YOU TO YOUR COLLEAGUES</span>
                <span class="separator"></span>
                <span class="text-item">MEETING THE IN-LAWS</span>
                <span class="separator"></span>
                <span class="text-item">REUNIONS</span>
                <span class="separator"></span>
                <span class="text-item">WELCOME BACK TO THE OFFICE</span>
                <span class="separator"></span>
                <span class="text-item">THINKING OF YOU</span>
                <span class="separator"></span>
                <span class="text-item">CELEBRATING A BIG WIN</span>
                <span class="separator"></span>
                <span class="text-item">BIRTHDAYS</span>
                <span class="separator"></span>
                <span class="text-item">HOLIDAYS</span>
                <span class="separator"></span>
                <span class="text-item">RETIREMENTS</span>
              </div>
              <div class="text-content">
                <span class="text-item">A THANK YOU TO YOUR COLLEAGUES</span>
                <span class="separator"></span>
                <span class="text-item">MEETING THE IN-LAWS</span>
                <span class="separator"></span>
                <span class="text-item">REUNIONS</span>
                <span class="separator"></span>
                <span class="text-item">WELCOME BACK TO THE OFFICE</span>
                <span class="separator"></span>
                <span class="text-item">THINKING OF YOU</span>
                <span class="separator"></span>
                <span class="text-item">CELEBRATING A BIG WIN</span>
                <span class="separator"></span>
                <span class="text-item">BIRTHDAYS</span>
                <span class="separator"></span>
                <span class="text-item">HOLIDAYS</span>
                <span class="separator"></span>
                <span class="text-item">RETIREMENTS</span>
              </div>
            </div>
            </section>

            <section class="over">
                <div class="left-col">
                    <img src="Copy of Quotation.png" class="imiji">
                    <h1>Hear what our customers <br> are saying about us</h1>
                    <p>We prioritize our customers' feedback and strive for their <br>complete satisfaction. Check out some of their experiences!</p>
                </div>
                <div class="right-col">
                    <div class="containerss">
                        <div class="work-showcase-wrapper">
                            <div class="review-slider">
                                <div class="review-card">
                                    <div class="review-header">
                                        <img src="Reviews1.jpg" alt="Annie Andrade" class="reviewer-image">
                                        <h3>Annie Andrade</h3>
                                    </div>
                                    <div class="review-content">
                                        I was blown away by the cake for my daughter's birthday! It was not only beautiful but also the best cake we've ever tasted. Thank you, Triple J & Rose's Bakery, for making her day extra special!
                                    </div>
                                    <div class="review-stars">
                                        <div class="star-rating" style="--percent: 100%"></div>
                                    </div>
                                </div>
                        
                                <div class="review-card">
                                    <div class="review-header">
                                        <img src="Reviews2.jpg" alt="Maria Rodriguez" class="reviewer-image">
                                        <h3>James Quinones</h3>
                                    </div>
                                    <div class="review-content">
                                        Triple J has exceeded our expectations everytime we order. Their creativity and customer service are top-notch. We highly recommend them for any special occasion!
                                    </div>
                                    <div class="review-stars">
                                        <div class="star-rating" style="--percent: 100%"></div>
                                    </div>
                                </div>
                        
                                <div class="review-card">
                                    <div class="review-header">
                                        <img src="Jeo.jpg" alt="David Chen" class="reviewer-image">
                                        <h3>Jeo Duyan</h3>
                                    </div>
                                    <div class="review-content">
                                        The cake looked exactly like the picture I provided. This was the best cake I've ever had. Thank you for making my special day so memorable.
                                    </div>
                                    <div class="review-stars">
                                        <div class="star-rating" style="--percent: 100%"></div>
                                    </div>
                                </div>
                        
                                <div class="review-card">
                                    <div class="review-header">
                                        <img src="Aj.jpg" alt="Emily Thompson" class="reviewer-image">
                                        <h3>Aj Castro</h3>
                                    </div>
                                    <div class="review-content">
                                        I love the texture. Triple J created the most stunning cake for my wedding! The flavors were incredible, and the design was exactly what I envisioned. Their team was so attentive and made the process enjoyable.
                                    </div>
                                    <div class="review-stars">
                                        <div class="star-rating" style="--percent: 100%"></div>
                                    </div>
                                </div>
                            </div>
            </section>
            <section>
                <div class="likod">
                    <h1>Indulge in our custom <br>
                         creations today</h1>
                    <p>Your occasion will be much more special!</p>
                   <a href="MenuSection.php">"<button class="btn"><i class="animation"></i>ORDER NOW!!<i class="animation"></i> 
        </button></a>
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
                        <p>Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="c6a4a7b5a5a7aaaba7bfaaa3a8a386a1aba7afaae8a5a9ab">[email&#160;protected]</a></p>

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
            <script src="cart-persistence.js"></script>
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
        document.addEventListener('DOMContentLoaded', () => {
            const workShowcase = document.getElementById('workShowcase');
            const leftBtn = document.querySelector('.navigation-btn.left');
            const rightBtn = document.querySelector('.navigation-btn.right');

            // Navigation button scroll
            leftBtn.addEventListener('click', () => {
                const scrollAmount = workShowcase.clientWidth * 2;
                workShowcase.scrollLeft -= scrollAmount;
            });

            rightBtn.addEventListener('click', () => {
                const scrollAmount = workShowcase.clientWidth * 2;
                workShowcase.scrollLeft += scrollAmount;
            });
        });

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
	// <![CDATA[  <-- For SVG support
	if ('WebSocket' in window) {
		(function () {
			function refreshCSS() {
				var sheets = [].slice.call(document.getElementsByTagName("link"));
				var head = document.getElementsByTagName("head")[0];
				for (var i = 0; i < sheets.length; ++i) {
					var elem = sheets[i];
					var parent = elem.parentElement || head;
					parent.removeChild(elem);
					var rel = elem.rel;
					if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
						var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
						elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
					}
					parent.appendChild(elem);
				}
			}
			var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
			var address = protocol + window.location.host + window.location.pathname + '/ws';
			var socket = new WebSocket(address);
			socket.onmessage = function (msg) {
				if (msg.data == 'reload') window.location.reload();
				else if (msg.data == 'refreshcss') refreshCSS();
			};
			if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
				console.log('Live reload enabled.');
				sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
			}
		})();
	}
	else {
		console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
	}
</script>
<script>
    const carouselContainer = document.querySelector('.carousel-container');
const carouselItems = document.querySelectorAll('.carousel-item');

let currentIndex = 0;
let carouselWidth = 0;

function setupCarousel() {
  carouselWidth = carouselContainer.offsetWidth;
  carouselItems.forEach((item, index) => {
    item.style.left = `${index * carouselWidth}px`;
  });
}

function startCarousel() {
  setInterval(() => {
    currentIndex++;
    if (currentIndex >= carouselItems.length) {
      currentIndex = 0;
      carouselItems.forEach((item) => {
        item.style.left = '0';
      });
    }
    carouselContainer.scrollLeft = currentIndex * carouselWidth;
  }, 3000); // Change the delay (in milliseconds) to adjust the carousel speed
}

window.addEventListener('load', () => {
  setupCarousel();
  startCarousel();
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.querySelector('.nav-toggle');
    const navLinks = document.querySelector('.nav-links');

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
  <script>
    // Intersection Observer to detect when elements enter the viewport
document.addEventListener('DOMContentLoaded', function() {
  // Elements to animate - EXCLUDING hero section and meltdown
  const animatedElements = [
    { selector: '.history .mama, .history .papa', animation: 'fade-in-side', delay: 0.1 },
    { selector: '.history .ptext', animation: 'fade-up', delay: 0.2 },
    { selector: '.history1 .pinsan', animation: 'fade-in-side', delay: 0.1 },
    { selector: '.history1 .htext2 p', animation: 'fade-up', delay: 0.2 },
    { selector: '.review-card', animation: 'fade-in-scale', delay: 0.15 },
    { selector: '.likod h1, .likod p', animation: 'fade-up', delay: 0.1 },
    { selector: '.btn', animation: 'fade-up', delay: 0.2 }
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
});
  </script>
</body>
</html>