<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}
require 'connection.php';
$user_id = $_SESSION['User_ID'];

// STEP 1: Get the Cart_ID for the user
$stmt = $conn->prepare("SELECT Cart_ID FROM cart WHERE User_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart = $result->fetch_assoc();
$stmt->close();

if (!$cart) {
    // If no cart exists for this user, create one
    $stmt = $conn->prepare("INSERT INTO cart (User_ID) VALUES (?)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_id = $stmt->insert_id;
    $stmt->close();
} else {
    $cart_id = $cart['Cart_ID'];
}

// STEP 2: Retrieve cart items along with product Price
$stmt = $conn->prepare("SELECT ci.*, p.Price FROM cart_items ci JOIN product p ON ci.Product_ID = p.Product_ID WHERE ci.Cart_ID = ?");
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}
$stmt->close();

if (empty($cart_items)) {
    die("Your cart is empty.");
}

// STEP 3: Insert a new order into the orders table
$status = "Placed"; // default status
$stmt = $conn->prepare("INSERT INTO orders (User_ID, Cart_ID, status) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $user_id, $cart_id, $status);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// STEP 4: Insert each cart item into order_items table
$stmt = $conn->prepare("INSERT INTO order_items (Order_ID, Product_ID, Quantity) VALUES (?, ?, ?)");
foreach ($cart_items as $item) {
    $product_id = $item['Product_ID'];
    $quantity = $item['Quantity'];
    $stmt->bind_param("iii", $order_id, $product_id, $quantity);
    $stmt->execute();
}
$stmt->close();

// STEP 4.5: Calculate total order amount and update user's Aura Points
$order_total = 0;
foreach ($cart_items as $item) {
    $order_total += $item['Price'] * $item['Quantity'];
}

// Convert total to points (1 GBP = 1 point)
$order_points = (int) round($order_total);

// Update user points
$stmt = $conn->prepare("UPDATE users SET Aura_Points = Aura_Points + ? WHERE User_ID = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ii", $order_points, $user_id);
$stmt->execute();
if ($stmt->error) {
    die("Execute failed: " . $stmt->error);
}
$stmt->close();

// STEP 5: Insert payment record
// Get payment method from session
$payment_method = isset($_SESSION['payment_method']) ? $_SESSION['payment_method'] : 'Credit Card';
$payment_status = 'Completed';

$stmt = $conn->prepare("INSERT INTO payment (Order_ID, Amount, method, Status) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("idss", $order_id, $order_total, $payment_method, $payment_status);
$stmt->execute();
if ($stmt->error) {
    die("Execute failed: " . $stmt->error);
}
$payment_id = $stmt->insert_id;
$stmt->close();

// Store the order ID and payment ID in session for receipt page
$_SESSION['last_order_id'] = $order_id;
$_SESSION['last_payment_id'] = $payment_id;
$_SESSION['order_total'] = $order_total;

// STEP 6: Clear the cart by deleting all cart_items for this cart
$stmt = $conn->prepare("DELETE FROM cart_items WHERE Cart_ID = ?");
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$stmt->close();

// Redirect to the receipt page
header("Location: receipt.php");
exit;
?>