<?php
session_start();

// Get product ID
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Remove item from the cart
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

header("Location: cart.php");
exit;
?>
