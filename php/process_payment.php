<?php
session_start();
header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Unknown error'
];

try {
    // Validate required fields
    $requiredFields = ['card_name', 'card_number', 'card_month', 'card_year', 'card_cvc', 'User_ID', 'total_price'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Additional validation can be added here
    // Examples:
    // - Validate card number format
    // - Check expiry date
    // - Validate CVC

    // Simulate payment processing (replace with actual payment gateway integration)
    // In a real scenario, you would integrate with a payment processor like Stripe, PayPal, etc.
    
    // For demonstration, just simulating a successful payment
    $bookingId = 'BK-' . uniqid();

    // Clear cart after successful payment
    unset($_SESSION['cart']);

    $response = [
        'status' => 'success',
        'message' => 'Payment processed successfully',
        'booking_id' => $bookingId
    ];
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
exit();