<?php
require_once "connection.php";
session_start();

// Ensure only admins can access this page
//if (!isset($_SESSION['User_ID'])) {
  //  header("Location: admin-login.php");
    //exit();
//}

// Fetch total sales (sum of all payments)
$sql_sales = "SELECT SUM(Amount) AS total_sales FROM payment WHERE Status = 'Completed'";
$result_sales = $conn->query($sql_sales);
$total_sales = ($result_sales->num_rows > 0) ? $result_sales->fetch_assoc()['total_sales'] : 0;

// Fetch total orders
$sql_orders = "SELECT COUNT(*) AS total_orders FROM orders";
$result_orders = $conn->query($sql_orders);
$total_orders = ($result_orders->num_rows > 0) ? $result_orders->fetch_assoc()['total_orders'] : 0;

// Fetch total products
$sql_products = "SELECT COUNT(*) AS total_products FROM product";
$result_products = $conn->query($sql_products);
$total_products = ($result_products->num_rows > 0) ? $result_products->fetch_assoc()['total_products'] : 0;

// Fetch total users
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn->query($sql_users);
$total_users = ($result_users->num_rows > 0) ? $result_users->fetch_assoc()['total_users'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <img src="Aura_logo.png" alt="Admin Logo">
                <h2>Admin Panel</h2>
            </div>
            <ul>
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="manage-products.php">Manage Products</a></li>
                <li><a href="manage-orders.php">Manage Orders</a></li>
                <li><a href="manage-users.php">Manage Users</a></li>
                <li><a href="manage-reviews.php">Manage Reviews</a></li>
                <li><a href="admin-logout.php">Logout</a></li>

            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h1>Welcome, Admin</h1>
            <div class="dashboard">
                <div class="card">Total Sales: £<?= number_format($total_sales, 2) ?></div>
                <div class="card">Orders: <?= $total_orders ?></div>
                <div class="card">Products: <?= $total_products ?></div>
                <div class="card">Users: <?= $total_users ?></div>
            </div>
        </main>
    </div>
</body>
</html>
