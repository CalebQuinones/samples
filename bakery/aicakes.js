/**
 * AICakes - Custom Cake Design with AI
 * Triple J's Bakery
 */

document.addEventListener('DOMContentLoaded', function() {
    // Main elements
    const aiCakeForm = document.getElementById('aiCakeForm');
    const aiCakesModal = document.getElementById('aiCakesModal');

    // Create loading overlay elements and append to body (initially hidden)
    createLoadingOverlay();
    
    // Add event listener to AI Cake form submission
    if (aiCakeForm) {
        aiCakeForm.addEventListener('submit', handleAICakeSubmit);
    }

    /**
     * Handle AI Cake form submission
     * @param {Event} e - Form submission event
     */
    function handleAICakeSubmit(e) {
        e.preventDefault();
        
        // Check if user is logged in
        if (!window.isLoggedIn) {
            alert('Please log in to use AICakes.');
            return;
        }
        
        // Get form data
        const formData = {
            cakeType: document.getElementById('aiCakeType').value,
            cakeTiers: document.getElementById('aiCakeTiers').value,
            cakeSize: document.getElementById('aiCakeSize').value,
            cakeFlavor: document.getElementById('aiCakeFlavor').value,
            fillingType: document.getElementById('aiFillingType').value,
            frostingType: document.getElementById('aiFrostingType').value,
            cakeDescription: document.getElementById('aiDescription').value
        };
        
        // Validate form data
        if (!validateFormData(formData)) {
            return;
        }
        
        // Store calculated price for later use
        const calculatedPrice = parseFloat(
            document.getElementById('aiEstimatedPrice').textContent.replace(/[^\d.]/g, '')
        );
        formData.estimatedPrice = calculatedPrice;
        
        // Hide the AI Cakes modal
        aiCakesModal.classList.remove('active');
        setTimeout(() => {
            aiCakesModal.style.display = 'none';
        }, 300);
        
        // Show loading overlay
        showLoadingOverlay();
        
        // Start API call to generate images
        generateCakeImages(formData);
    }
    
    /**
     * Validate the AI Cake form data
     * @param {Object} formData - Form data
     * @returns {boolean} - True if valid, false otherwise
     */
    function validateFormData(formData) {
        const requiredFields = [
            'cakeType', 'cakeTiers', 'cakeSize', 'cakeFlavor', 
            'fillingType', 'frostingType', 'cakeDescription'
        ];
        
        const missingFields = requiredFields.filter(field => !formData[field]);
        
        if (missingFields.length > 0) {
            alert('Please fill in all required fields.');
            return false;
        }
        
        if (formData.cakeDescription.length < 10) {
            alert('Please provide a more detailed description of your cake.');
            return false;
        }
        
        return true;
    }
    
    /**
     * Create the loading overlay HTML and append to body
     */
    function createLoadingOverlay() {
        // Create main overlay container
        const overlay = document.createElement('div');
        overlay.className = 'aicakes-loading-overlay';
        overlay.id = 'aiCakesLoadingOverlay';
        
        // Create loading content
        overlay.innerHTML = `
            <div class="aicakes-loading-title">Baking your dream cake...</div>
            <div class="aicakes-loading-subtitle">Our AI is crafting cake designs just for you. This may take a moment.</div>
            
            <div class="aicakes-loading-container">
                <div class="aicakes-loading-card"></div>
                <div class="aicakes-loading-card"></div>
            </div>
            
            <div class="aicakes-results">
                <div class="aicakes-results-heading">Choose your favorite design</div>
                <div class="aicakes-results-container" id="aiCakesResultsContainer"></div>
            </div>
            
            <div class="aicakes-error">
                <div class="aicakes-error-icon">❌</div>
                <div class="aicakes-error-heading">Oops! Something went wrong</div>
                <div class="aicakes-error-message" id="aiCakesErrorMessage">
                    We couldn't generate your cake designs. Please try again.
                </div>
                <button class="aicakes-try-again-btn" id="aiCakesTryAgainBtn">Try Again</button>
            </div>
        `;
        
        // Append overlay to body
        document.body.appendChild(overlay);
        
        // Add event listener to try again button
        document.getElementById('aiCakesTryAgainBtn').addEventListener('click', function() {
            hideLoadingOverlay();
            
            // Show AI Cakes modal again
            aiCakesModal.style.display = 'block';
            setTimeout(() => {
                aiCakesModal.classList.add('active');
            }, 10);
        });
    }
    
    /**
     * Show loading overlay with animation
     */
    function showLoadingOverlay() {
        const overlay = document.getElementById('aiCakesLoadingOverlay');
        const loadingContainer = overlay.querySelector('.aicakes-loading-container');
        const resultsContainer = overlay.querySelector('.aicakes-results');
        const errorContainer = overlay.querySelector('.aicakes-error');
        
        // Reset state
        loadingContainer.style.display = 'flex';
        resultsContainer.classList.remove('active');
        errorContainer.classList.remove('active');
        
        // Show overlay
        overlay.style.display = 'flex';
        setTimeout(() => {
            overlay.classList.add('active');
        }, 10);
    }
    
    /**
     * Hide loading overlay with animation
     */
    function hideLoadingOverlay() {
        const overlay = document.getElementById('aiCakesLoadingOverlay');
        
        overlay.classList.remove('active');
        setTimeout(() => {
            overlay.style.display = 'none';
        }, 300);
    }
    
    /**
     * Show error message in the loading overlay
     * @param {string} message - Error message to display
     */
    function showErrorMessage(message) {
        const overlay = document.getElementById('aiCakesLoadingOverlay');
        const loadingContainer = overlay.querySelector('.aicakes-loading-container');
        const errorContainer = overlay.querySelector('.aicakes-error');
        const errorMessage = document.getElementById('aiCakesErrorMessage');
        
        // Hide loading animation
        loadingContainer.style.display = 'none';
        
        // Update and show error message
        errorMessage.textContent = message || 'We couldn\'t generate your cake designs. Please try again.';
        errorContainer.classList.add('active');
    }
    
    /**
     * Generate cake images via API call
     * @param {Object} formData - Form data for cake design
     */
    function generateCakeImages(formData) {
        // Make API call to backend
        fetch('stability_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server responded with status ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Failed to generate images');
            }
            
            // Display the generated images
            displayGeneratedImages(data.images, formData);
        })
        .catch(error => {
            console.error('Error generating cake images:', error);
            showErrorMessage(error.message);
        });
        
        // Add timeout for API calls
        setTimeout(() => {
            const overlay = document.getElementById('aiCakesLoadingOverlay');
            const loadingContainer = overlay.querySelector('.aicakes-loading-container');
            
            // If loading container is still visible after 30 seconds, show timeout error
            if (loadingContainer.style.display !== 'none') {
                showErrorMessage('The request is taking longer than expected. Please try again.');
            }
        }, 30000); // 30 second timeout
    }
    
    /**
     * Create sparkles around an element
     * @param {HTMLElement} element - The element to add sparkles to
     */
    function createSparkleEffect(element) {
        // Create a container for the sparkles
        const sparkleContainer = document.createElement('div');
        sparkleContainer.className = 'sparkle-container';
        element.appendChild(sparkleContainer);
        
        // Create a magic wand animation first
        const wandContainer = document.createElement('div');
        wandContainer.className = 'magic-wand-container';
        wandContainer.innerHTML = '<div class="magic-wand"></div>';
        sparkleContainer.appendChild(wandContainer);
        
        // Create 30 sparkles
        const colors = ['pink', 'white', 'blue', ''];
        
        // Generate sparkles with a delay
        setTimeout(() => {
            for (let i = 0; i < 30; i++) {
                setTimeout(() => {
                    const sparkle = document.createElement('div');
                    sparkle.className = 'sparkle ' + colors[Math.floor(Math.random() * colors.length)];
                    
                    // Random size between 4px and 10px
                    const size = Math.random() * 6 + 4;
                    sparkle.style.width = size + 'px';
                    sparkle.style.height = size + 'px';
                    
                    // Random position
                    sparkle.style.left = Math.random() * 100 + '%';
                    sparkle.style.top = Math.random() * 100 + '%';
                    
                    // Add to container
                    sparkleContainer.appendChild(sparkle);
                    
                    // Remove after animation
                    setTimeout(() => {
                        sparkle.remove();
                    }, 1500);
                }, i * 50); // stagger the creation of sparkles
            }
            
            // Remove the container after all animations are done
            setTimeout(() => {
                sparkleContainer.remove();
            }, 3000);
        }, 500); // Start after a small delay
    }

    /**
     * Display generated images in the results container
     * @param {Array} images - Array of image URLs (base64)
     * @param {Object} formData - Original form data
     */
    function displayGeneratedImages(images, formData) {
        const overlay = document.getElementById('aiCakesLoadingOverlay');
        const loadingContainer = overlay.querySelector('.aicakes-loading-container');
        const resultsContainer = overlay.querySelector('.aicakes-results');
        const resultsImagesContainer = document.getElementById('aiCakesResultsContainer');
        
        // Hide loading animation
        loadingContainer.style.display = 'none';
        
        // Clear previous results
        resultsImagesContainer.innerHTML = '';
        
        // Add "Try Again" button at the top
        const tryAgainContainer = document.createElement('div');
        tryAgainContainer.className = 'aicakes-try-again-container';
        tryAgainContainer.innerHTML = `
            <button class="aicakes-exit-btn">None of these look right - Try again</button>
        `;
        resultsImagesContainer.appendChild(tryAgainContainer);
        
        // Add image cards
        images.forEach((imageUrl, index) => {
            const card = document.createElement('div');
            card.className = 'aicakes-image-card';
            
            card.innerHTML = `
                <img src="${imageUrl}" alt="AI Generated Cake Design ${index+1}">
                <div class="aicakes-image-card-overlay">
                    <button class="aicakes-select-btn" data-image-index="${index}">Select This Design</button>
                </div>
            `;
            
            // Add the card to the container
            resultsImagesContainer.appendChild(card);
            
            // Add sparkle effect when image loads
            const img = card.querySelector('img');
            img.onload = function() {
                createSparkleEffect(card);
            };
        });
        
        // Show results
        resultsContainer.classList.add('active');
        
        // Add click event listeners to select buttons
        const selectButtons = overlay.querySelectorAll('.aicakes-select-btn');
        selectButtons.forEach(button => {
            button.addEventListener('click', function() {
                const imageIndex = parseInt(this.getAttribute('data-image-index'));
                selectCakeDesign(images[imageIndex], formData);
            });
        });
        
        // Add event listener to "Try Again" button
        const exitButton = overlay.querySelector('.aicakes-exit-btn');
        if (exitButton) {
            exitButton.addEventListener('click', function() {
                // Hide the overlay
                hideLoadingOverlay();
                
                // Show the AICakes modal again so they can try again
                aiCakesModal.style.display = 'block';
                setTimeout(() => {
                    aiCakesModal.classList.add('active');
                }, 10);
            });
        }
    }
    
    /**
     * Handle selection of a cake design
     * @param {string} imageUrl - Selected image URL (base64)
     * @param {Object} formData - Original form data
     */
    function selectCakeDesign(imageUrl, formData) {
        // Find the selected card to apply the enhanced sparkle effect
        const selectedCardIndex = Array.from(document.querySelectorAll('.aicakes-image-card img')).findIndex(img => img.src === imageUrl);
        const selectedCard = document.querySelectorAll('.aicakes-image-card')[selectedCardIndex];
        
        if (selectedCard) {
            // Create an enhanced sparkle effect on the selected card
            createEnhancedSparkleEffect(selectedCard);
            
            // Small delay before proceeding to add to cart (to allow animation to play)
            setTimeout(() => {
                // Create custom cake cart item directly from selected image
                const customCake = {
                    type: 'custom',
                    name: 'Custom Cake',
                    price: formData.estimatedPrice || 0,
                    quantity: 1,
                    image: imageUrl,
                    details: {
                        size: formData.cakeSize,
                        flavor: formData.cakeFlavor,
                        filling: formData.fillingType,
                        frosting: formData.frostingType,
                        tiers: formData.cakeTiers,
                        cakeType: formData.cakeType,
                        isAIGenerated: true,
                        description: formData.cakeDescription,
                        referenceImage: imageUrl
                    }
                };
                
                // Add to cart
                if (!window.cart) window.cart = [];
                window.cart.push(customCake);
                
                // Save cart to localStorage
                localStorage.setItem('cart', JSON.stringify(window.cart));
                
                // Update cart UI if function exists
                if (typeof updateCartCount === 'function') {
                    updateCartCount();
                }
                
                // Hide loading overlay after animation completes
                setTimeout(() => {
                    hideLoadingOverlay();
                    
                    // Show success message
                    alert('Your custom cake has been added to your cart!');
                }, 800);
            }, 1500);
        } else {
            // Fallback if card not found
            hideLoadingOverlay();
            
            // Create custom cake cart item directly from selected image
            const customCake = {
                type: 'custom',
                name: 'Custom Cake',
                price: formData.estimatedPrice || 0,
                quantity: 1,
                image: imageUrl,
                details: {
                    size: formData.cakeSize,
                    flavor: formData.cakeFlavor,
                    filling: formData.fillingType,
                    frosting: formData.frostingType,
                    tiers: formData.cakeTiers,
                    cakeType: formData.cakeType,
                    isAIGenerated: true,
                    description: formData.cakeDescription,
                    referenceImage: imageUrl
                }
            };
            
            // Add to cart
            if (!window.cart) window.cart = [];
            window.cart.push(customCake);
            
            // Save cart to localStorage
            localStorage.setItem('cart', JSON.stringify(window.cart));
            
            // Update cart UI if function exists
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
            
            // Show success message
            alert('Your custom cake has been added to your cart!');
        }
    }
    
    /**
     * Create an enhanced sparkle effect for the selected cake
     * @param {HTMLElement} element - The element to add sparkles to
     */
    function createEnhancedSparkleEffect(element) {
        // Create a container for the sparkles
        const sparkleContainer = document.createElement('div');
        sparkleContainer.className = 'sparkle-container';
        element.appendChild(sparkleContainer);
        
        // Add a subtle scale animation to the card
        element.style.transition = 'transform 1s ease';
        element.style.transform = 'scale(1.05)';
        
        // Create more sparkles (60 vs 30 in regular effect)
        const colors = ['pink', 'white', 'blue', ''];
        
        // Generate sparkles with a delay
        for (let i = 0; i < 60; i++) {
            setTimeout(() => {
                const sparkle = document.createElement('div');
                sparkle.className = 'sparkle ' + colors[Math.floor(Math.random() * colors.length)];
                
                // Random size between 4px and 12px (larger than regular)
                const size = Math.random() * 8 + 4;
                sparkle.style.width = size + 'px';
                sparkle.style.height = size + 'px';
                
                // Random position
                sparkle.style.left = Math.random() * 100 + '%';
                sparkle.style.top = Math.random() * 100 + '%';
                
                // Add to container
                sparkleContainer.appendChild(sparkle);
                
                // Remove after animation
                setTimeout(() => {
                    sparkle.remove();
                }, 1500);
            }, i * 25); // faster staggering than regular effect
        }
    }
    
    /**
     * Reset the AICakes form for future use
     */
    function resetAICakesForm() {
        // Reset the form fields if needed
        const form = document.getElementById('aiCakeForm');
        if (form) {
            form.reset();
        }
    }
}); 