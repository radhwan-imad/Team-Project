<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];

    // Remove the product from the cart
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($item) use ($product_id) {
        return $item['id'] !== $product_id;
    });

    // Redirect back to the cart page
    header('Location: cart.php');
    exit;
}
?>
