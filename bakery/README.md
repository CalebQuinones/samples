# Triple J's Bakery - AICakes Feature

## Overview
AICakes is a custom cake design feature powered by Stability AI that allows customers to generate custom cake designs based on their specifications and preferences. The feature uses the Stability AI API to transform customer input into photorealistic cake images.

## Setup Instructions

### 1. API Key Configuration
Before using the AICakes feature, you need to configure your Stability AI API key:

1. Sign up for a Stability AI account at [platform.stability.ai](https://platform.stability.ai/)
2. Generate an API key from the dashboard
3. Open `stability_api.php` and replace `YOUR_STABILITY_API_KEY` with your actual API key:

```php
define('STABILITY_API_KEY', 'YOUR_STABILITY_API_KEY'); // Replace with your actual API key
```

### 2. File Structure
The AICakes feature consists of the following files:

- `stability_api.php` - Handles API communication with Stability AI
- `aicakes.js` - Client-side JavaScript for form handling and UI interactions
- `aicakes.css` - Styling for the AICakes UI components
- `MenuSection.php` - Contains the AICakes modal form (already integrated)

### 3. Testing the Feature
Once configured, the AICakes feature can be tested by:

1. Navigating to the Menu page
2. Clicking on "Customize your own cake!"
3. Selecting "Build with AICakes" 
4. Filling out the cake specifications form
5. Clicking "Let AICakes do the magic"

## How It Works

1. **User Input**: Customers fill out a form specifying their cake preferences (type, size, flavor, tiers, etc.) and provide a description
2. **Form Processing**: The form data is validated and sent to the server
3. **Prompt Generation**: The system converts the form data into a detailed prompt for the AI
4. **Image Generation**: The prompt is sent to Stability AI's API, which returns generated cake images
5. **Selection**: Customers select their preferred design from the generated options
6. **Cart Integration**: The selected design is added to the customer's cart as a custom cake order

## Troubleshooting

### API Errors
If you encounter API errors:
- Verify your API key is correctly set in `stability_api.php`
- Check your API usage limits in the Stability AI dashboard
- Verify your internet connection and server connectivity

### Image Generation Issues
If images aren't generating properly:
- Make sure the prompt generation is working correctly
- Try providing more detailed descriptions
- Check the API response for specific error messages

## Credits
- Stability AI - [stable-diffusion-xl](https://platform.stability.ai/docs/api-reference#tag/Generation)
- Triple J's Bakery Development Team 