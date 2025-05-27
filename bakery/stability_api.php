<?php
/**
 * Stability AI API Integration for Triple J's Bakery
 * This file handles interactions with Stability AI's text-to-image API
 */

// API Configuration
define('STABILITY_API_KEY', 'sk-iJdWrfxWpzX4fLRQy0XVVYeWNCQ82it2gv7zTWoUuQGlrpdY'); // Replace with your actual API key
define('STABILITY_API_URL', 'https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/text-to-image');

/**
 * Generate cake images based on user specifications
 * 
 * @param array $cakeData Cake specifications from form submission
 * @return array Response containing generated images or error
 */
function generateCakeImages($cakeData) {
    // Validate API key
    if (STABILITY_API_KEY === 'YOUR_STABILITY_API_KEY') {
        return [
            'success' => false,
            'message' => 'API key not configured. Please set your Stability AI API key in stability_api.php.'
        ];
    }

    // Build the prompt from form data
    $prompt = buildCakePrompt($cakeData);
    
    // Prepare API request
    $requestData = [
        'text_prompts' => [
            [
                'text' => $prompt,
                'weight' => 1
            ],
            [
                'text' => 'blurry, distorted, low quality, unrealistic, out of frame, bad anatomy, cartoon style',
                'weight' => -1
            ]
        ],
        'cfg_scale' => 8,
        'height' => 1024,
        'width' => 1024,
        'samples' => 2,
        'steps' => 30,
        'style_preset' => 'photographic'
    ];

    // Set up curl request
    $ch = curl_init(STABILITY_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . STABILITY_API_KEY
    ]);

    // Execute request and handle response
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errorMsg = curl_error($ch);
    
    curl_close($ch);

    // Process response
    if ($httpCode !== 200) {
        // API request failed
        $error = json_decode($response, true);
        return [
            'success' => false,
            'message' => 'API request failed: ' . ($error['message'] ?? $errorMsg ?? 'Unknown error'),
            'http_code' => $httpCode
        ];
    }

    // Decode the successful response
    $result = json_decode($response, true);
    
    if (!isset($result['artifacts']) || empty($result['artifacts'])) {
        return [
            'success' => false,
            'message' => 'No images were generated'
        ];
    }

    // Process the images
    $images = [];
    foreach ($result['artifacts'] as $artifact) {
        $base64Image = $artifact['base64'];
        $images[] = 'data:image/png;base64,' . $base64Image;
    }

    // Return success response with images
    return [
        'success' => true,
        'images' => $images,
        'prompt' => $prompt
    ];
}

/**
 * Build a detailed prompt for cake generation based on user form data
 * 
 * @param array $cakeData User form inputs
 * @return string Detailed prompt for the AI
 */
function buildCakePrompt($cakeData) {
    // Extract data from form
    $type = $cakeData['cakeType'] ?? '';
    $tiers = $cakeData['cakeTiers'] ?? '';
    $size = $cakeData['cakeSize'] ?? '';
    $flavor = $cakeData['cakeFlavor'] ?? '';
    $filling = $cakeData['fillingType'] ?? '';
    $frosting = $cakeData['frostingType'] ?? '';
    $description = $cakeData['cakeDescription'] ?? '';
    
    // Build base prompt
    $prompt = "A professional high-resolution photograph of a beautiful $flavor cake";
    
    // Add cake type
    if ($type) {
        $prompt .= " for a $type occasion";
    }
    
    // Add tiers description
    if ($tiers) {
        if ($tiers == 1) {
            $prompt .= " with a single tier";
        } else {
            $prompt .= " with $tiers tiers";
        }
    }
    
    // Add frosting details
    if ($frosting) {
        switch($frosting) {
            case 'buttercream':
                $prompt .= ", decorated with smooth buttercream frosting";
                break;
            case 'fondant':
                $prompt .= ", covered in elegant fondant";
                break;
            case 'ganache':
                $prompt .= ", glazed with rich chocolate ganache";
                break;
            case 'nakedcake':
                $prompt .= " in naked cake style with visible layers";
                break;
        }
    }
    
    // Add filling information if relevant
    if ($filling && $filling != 'buttercream') {
        switch($filling) {
            case 'chocolate':
                $prompt .= " with chocolate ganache filling between layers";
                break;
            case 'fruit':
                $prompt .= " with fresh fruit filling between layers";
                break;
            case 'custard':
                $prompt .= " with custard filling between layers";
                break;
        }
    }
    
    // Add user's specific description
    if ($description) {
        $prompt .= ". " . $description;
    }
    
    // Add final details for high quality generation
    $prompt .= ". The cake is presented on a elegant cake stand, with professional bakery lighting, high detail, photorealistic quality.";
    
    return $prompt;
}

/**
 * Process the AI Cake form submission
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process only AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
        // Get JSON data from request
        $jsonData = file_get_contents('php://input');
        $formData = json_decode($jsonData, true);
        
        // Validate required fields
        $requiredFields = ['cakeType', 'cakeTiers', 'cakeSize', 'cakeFlavor', 'fillingType', 'frostingType', 'cakeDescription'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (empty($formData[$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ]);
            exit;
        }
        
        // Generate images using Stability AI
        $result = generateCakeImages($formData);
        
        // Return response as JSON
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
} 