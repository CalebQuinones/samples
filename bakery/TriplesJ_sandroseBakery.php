<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Triple Js Bakery</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Madimi+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Madimi+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="figma2.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
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

<div class="buong-content">
    <section class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <div class="year">Since 2008</div>
                <h1>Celebrate Homemade</h1>
                <p>At Triple J's Bakery, we craft each treat with the care and love of a family recipe passed down through generations. Our ovens warm not just dough, but hearts - creating memories one sweet bite at a time.</p>
                <a href="MenuSection.php" class="btn btn-primary">SHOP NOW!</a>
                <a href="MenuSection.php" class="btn btn-secondary">VIEW MENU</a>
            </div>
            <div class="hero-image">
                <img src="behindcake.png" class="behind" id="likod">
                <img src="maincake.png" class="maincake">
            </div>
            <div class="event-bar">
              <nav class="event-nav">
                  <a href="#birthday" data-cake="maincake.png" class="active">Birthday</a>
                  <a href="#wedding" data-cake="weddingcaku.png">Wedding</a>
                  <a href="#shower" data-cake="showercaku.png">Shower</a>
                  <a href="#events" data-cake="occasionscaku.png">Events</a>
                  <a href="#corporate" data-cake="corporatecaku.png">Corporate</a>
              </nav>
          </div>
        </div>
    </section>
    <div class>
        <img src="meltdown2.png" class="melt"></div>
    </div>


    <section class="features">
        <div class="feature-grid">
            <div class="feature-card">
                <i class="fas fa-birthday-cake"></i>
                <h3>Custom Orders</h3>
                <p>Just for you.... <br>
                Crafted with care, tailored to your taste, and made to perfection.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-heart"></i>
                <h3>Fresh Daily</h3>
                <p>Baked every morning, bringing warmth and flavor to your day.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-box"></i>
                <h3>Quick Package</h3>
                <p>Freshly prepared and ready within 24 hours, just for you!</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-clock"></i>
                <h3>Made with Love</h3>
                <p>Inspired by cherished family recipes, crafted with care and a touch of tradition.</p>
            </div>
        </div>
    </section>

    <section class="catalog">
        <div class="container">
            <h2>Our Catalog</h2>
            <p>Every morning, we fire up our ovens and get to work baking delicious treats using the highest quality ingredients. Hand made with love and care, just like grandma used to do.</p>
            <div class="catalog-slider">
                
                <div class="catalog-grid">
                    <div class="catalog-item">
                        <div class="heart-decoration"></div>
                        <img src="pink-and-blue-aesthetic-thank-you-card-lanscape-82.png" alt="Cakes">
                        <h3>CAKES</h3>
                    </div>
                    <div class="catalog-item">
                        <div class="heart-decoration"></div>
                        <img src="cupcakes.png" alt="Cupcakes">
                        <h3>CUPCAKES</h3>
                    </div>
                    <div class="catalog-item">
                        <div class="heart-decoration"></div>
                        <img src="breads.png" alt="Breads">
                        <h3>BREADS</h3>
                    </div>
                    <div class="catalog-item">
                        <div class="heart-decoration"></div>
                        <img src="breakfast.png" alt="Breakfast">
                        <h3>BREAKFAST</h3>
                    </div>
                </div>
                
            </div>
        </div>
    </section>
    
    <section class="celebrate">
        <h2>So many reasons to celebrate</h2>
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
        </div>
        <div class="celebration-grid">
          <div class="celebration-card">
            <div class="heart-decoration"></div>
            <img src="pink-and-blue-aesthetic-thank-you-card-lanscape-82.png" alt="Birthday cake">
            <h3>BIRTHDAYS</h3>
            <p>To love that lasts forever and a cake you'll never stop talking about.</p>
          </div>
          <div class="celebration-card">
            <div class="heart-decoration"></div>
            <img src="weddings.png" alt="Wedding cake">
            <h3>WEDDINGS</h3>
            <p>Every day is someone's big day. Make this the one they'll never forget.</p>
          </div>
          <div class="celebration-card">
            <div class="heart-decoration"></div>
            <img src="occasionscaku.png" alt="Event">
            <h3>EVENTS</h3>
            <p>The talk of this year's holiday party will be the treats you got from us.</p>
          </div>
          <div class="celebration-card">
            <div class="heart-decoration"></div>
            <img src="corporatecaku.png" alt="Various cakes">
            <h3>MANY MORE!</h3>
            <p>The main goal of our bakery, is to hold a special place in people's hearts.</p>
          </div>
        </div>

        <div class="ribbon">
          <img src="ribbon1.png">
          <a href="MenuSection.php"><button class="shop-now">SHOP NOW</button></a>
          <section class="pickup"> 
            <h3>In-Store Pickup</h3>
            <p>To love that lasts forever and a cake you'll never stop talking about.</p>
          </section>
        </div>
  
  
         <div class="container1">
        <div>
            <h1 class="head">Since 2008, we want to <br> hold a special place in <br> people's hearts.</h1>
            <p class="pp wan">
                Triple J & Rose's Bakery was established in 2008. This company is more than just a place to purchase bread and pastries. This bakery encompasses the aspects of food production, retail, and often, a sense of nostalgia and comfort.
            </p>
        </div>
        <div>
            <div class="image-container1">
                <img src="papa.jpg">
            </div>
        </div>
        <div>
            <div class="image-container1">
                <img src="mama.jpg">
            </div>
        </div>
        <div>
            <h1 class="head2">It's a Family Business!</h1>
            <p class="pp"> 
                The main goal of our bakery, is to hold a special place in people's hearts. They evoke memories of childhood, family gatherings, and celebrations. The comfort and nostalgia associated with baked goods can make a bakery a cherished destination. This emotional connection is a significant factor in the success of Triple J & Rose's Bakery.
            </p>
            <a href="Abouts.php"><button class="more-about-us">More About us</button></a>
        </div>
    </div>
      </section>

  
<section class="facebook-section">
    <h2 class="section-title">Visit our Facebook Gallery</h2>
    
    <div class="gallery-container">
      <button class="nav-button prev" aria-label="Previous slide">‹</button>
      
      <div class="gallery">
        <div class="gallery-item">
          <a href="https://www.facebook.com/triplejrosesbakery" target="_blank" rel="noopener noreferrer" class="polaroid">
            <img src="8.png" />
            <div class="overlay">
              <svg class="facebook-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </div>
          </a>
        </div>
        
        <div class="gallery-item">
          <a href="https://www.facebook.com/triplejrosesbakery" target="_blank" rel="noopener noreferrer" class="polaroid">
            <img src="10.png"  />
            <div class="overlay">
              <svg class="facebook-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </div>
          </a>
        </div>
        
        <div class="gallery-item">
          <a href="https://www.facebook.com/triplejrosesbakery" target="_blank" rel="noopener noreferrer" class="polaroid">
            <img src="4.png"  />
            <div class="overlay">
              <svg class="facebook-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </div>
          </a>
        </div>
        
        <div class="gallery-item">
          <a href="https://www.facebook.com/triplejrosesbakery" target="_blank" rel="noopener noreferrer" class="polaroid">
            <img src="3.png"  />
            <div class="overlay">
              <svg class="facebook-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </div>
          </a>
        </div>
        
        <div class="gallery-item">
          <a href="https://www.facebook.com/triplejrosesbakerye" target="_blank" rel="noopener noreferrer" class="polaroid">
            <img src="2.png" />
            <div class="overlay">
              <svg class="facebook-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </div>
          </a>
        </div>
        
        <div class="gallery-item">
          <a href="https://www.facebook.com/triplejrosesbakery" target="_blank" rel="noopener noreferrer" class="polaroid">
            <img src="1.png"  />
            <div class="overlay">
              <svg class="facebook-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </div>
          </a>
        </div>
      </div>
      
      <button class="nav-button next" aria-label="Next slide">›</button>
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
            <p>Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="fb999a88989a97969a82979e959ebb9c969a9297d5989496">[email&#160;protected]</a></p>
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
    // Get DOM elements
    const cartIcon = document.getElementById('cartIcon');
    const cartPopup = document.getElementById('cartPopup');
    const closeCart = document.getElementById('closeCart');
    const shopMoreBtn = document.getElementById('shopMoreBtn');
    const checkoutBtn = document.getElementById('checkoutBtn');
    const modalOverlay = document.getElementById('modalOverlay');

    // Add click event listeners
    cartIcon.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        modalOverlay.style.display = 'block';
        cartPopup.style.display = 'block';
        // Force reflow
        void cartPopup.offsetWidth;
        modalOverlay.classList.add('active');
        cartPopup.classList.add('active');
        document.body.style.overflow = 'hidden';
        if (typeof updateCartUI === 'function') {
            updateCartUI();
        }
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

    checkoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (!window.cart || window.cart.length === 0) {
            alert('Your cart is empty. Please add items before checking out.');
            return;
        }
        window.location.href = 'checkout.php';
    });

    // Close cart when clicking outside
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeCartPopup();
        }
    });

    // Prevent clicks inside cart popup from closing it
    cartPopup.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Initialize cart UI
    if (typeof updateCartUI === 'function') {
        updateCartUI();
    }
});
</script>
</body>
</html>