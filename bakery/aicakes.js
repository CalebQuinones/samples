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
            
            resultsImagesContainer.appendChild(card);
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
    }
    
    /**
     * Handle selection of a cake design
     * @param {string} imageUrl - Selected image URL (base64)
     * @param {Object} formData - Original form data
     */
    function selectCakeDesign(imageUrl, formData) {
        // Create custom cake cart item
        const customCake = {
            type: 'custom',
            name: 'AI-Generated Custom Cake',
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
                description: formData.cakeDescription
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
        
        // Hide loading overlay
        hideLoadingOverlay();
        
        // Show success message
        alert('Your AI-generated cake design has been added to your cart!');
    }
}); 