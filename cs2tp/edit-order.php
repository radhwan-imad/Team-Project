<?php
require_once "connection.php";

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage-orders.php?msg=No order selected");
    exit();
}

$order_id = $_GET['id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_status = $_POST['status'];
    
    // Update order status
    $update_sql = "UPDATE orders SET status = ? WHERE Order_ID = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        header("Location: manage-orders.php?msg=Order updated successfully");
        exit();
    } else {
        $error_message = "Failed to update order: " . $conn->error;
    }
}

// Get order details
$sql = "SELECT o.Order_ID, u.First_Name, u.Last_Name, o.User_ID, o.status
        FROM orders o
        JOIN users u ON o.User_ID = u.User_ID
        WHERE o.Order_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: manage-orders.php?msg=Order not found");
    exit();
}

$order = $result->fetch_assoc();

// Get address details - using only user_address and address tables
$address_sql = "SELECT a.Address_line_1, a.Address_line_2, a.Postcode, a.country
                FROM user_address ua 
                JOIN address a ON ua.Address_ID = a.Address_ID
                WHERE ua.User_ID = ?
                LIMIT 1";
$address_stmt = $conn->prepare($address_sql);
$address_stmt->bind_param("i", $order['User_ID']);
$address_stmt->execute();
$address_result = $address_stmt->get_result();
$address = ($address_result->num_rows > 0) ? $address_result->fetch_assoc() : [];

// Get order items
$items_sql = "SELECT p.Name, oi.Quantity, p.Price, (oi.Quantity * p.Price) as Subtotal
              FROM order_items oi
              JOIN product p ON oi.Product_ID = p.Product_ID
              WHERE oi.Order_ID = ?";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lora:wght@400;700&family=Playfair+Display:wght@400;700&display=swap');

        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            font-family: 'Lora', serif;
            background-color: #fdf5e6; /* Cream-white */
            color: #333;
        }

        /* Admin Container */
        .admin-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #a27b5c; /* Light Brown */
            color: white;
            height: 100vh;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: sticky;
            top: 0;
        }

        /* Logo */
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 80px;
            border-radius: 50%;
        }

        .logo h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5em;
            margin-top: 10px;
            color: white;
            border-bottom: none;
        }

        /* Sidebar Links */
        .sidebar ul {
            list-style: none;
            width: 100%;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            font-size: 1.1em;
            font-family: 'Playfair Display', serif;
            text-align: center;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .sidebar ul li a:hover {
            background-color: #8d6b53; /* Darker Brown */
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            padding: 30px;
            margin-left: 20px;
        }

        h2 {
            color: #3e1805;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #a27b5c;
            font-family: 'Playfair Display', serif;
        }

        /* Form Styling */
        .edit-form {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #3e1805;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Lora', serif;
            font-size: 16px;
        }

        select.form-control {
            cursor: pointer;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            background-color: #a27b5c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Lora', serif;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn:hover {
            background-color: #8d6b53;
            transform: translateY(-2px);
        }

        .btn-back {
            background-color: #6c757d;
            margin-right: 10px;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        /* Order Details Card */
        .order-details {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .order-details h3 {
            color: #3e1805;
            margin-bottom: 15px;
            font-family: 'Playfair Display', serif;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            width: 150px;
        }

        /* Items Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        thead {
            background-color: #a27b5c;
            color: white;
        }

        th {
            text-align: left;
            padding: 15px;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0e9e1;
            vertical-align: middle;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: white;
        }

        .alert-danger {
            background-color: #f44336;
        }
        
        .address-block {
            line-height: 1.5;
        }
    </style>
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
    <div class="main-content">
        <h2>Edit Order #<?= $order_id ?></h2>
        
        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php } ?>
        
        <div class="order-details">
            <h3>Customer Information</h3>
            <div class="info-row">
                <span class="info-label">Customer:</span>
                <span><?= htmlspecialchars($order['First_Name'] . ' ' . $order['Last_Name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Customer ID:</span>
                <span><?= htmlspecialchars($order['User_ID']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="address-block">
                    <?php if (!empty($address)) { ?>
                        <?= !empty($address['Address_line_1']) ? htmlspecialchars($address['Address_line_1']) . '<br>' : '' ?>
                        <?= !empty($address['Address_line_2']) ? htmlspecialchars($address['Address_line_2']) . '<br>' : '' ?>
                        <?= !empty($address['Postcode']) ? htmlspecialchars($address['Postcode']) . '<br>' : '' ?>
                        <?= !empty($address['country']) ? htmlspecialchars($address['country']) : '' ?>
                    <?php } else { ?>
                        <em>No address information available</em>
                    <?php } ?>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Current Status:</span>
                <span><?= htmlspecialchars($order['status']) ?></span>
            </div>
        </div>
        
        <div class="edit-form">
            <h3>Update Order Status</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="Pending" <?= ($order['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="Processing" <?= ($order['status'] == 'Processing') ? 'selected' : '' ?>>Processing</option>
                        <option value="Shipped" <?= ($order['status'] == 'Shipped') ? 'selected' : '' ?>>Shipped</option>
                        <option value="Delivered" <?= ($order['status'] == 'Delivered') ? 'selected' : '' ?>>Delivered</option>
                        <option value="Cancelled" <?= ($order['status'] == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div>
                    <a href="manage-orders.php" class="btn btn-back">Back to Orders</a>
                    <button type="submit" class="btn">Update Order</button>
                </div>
            </form>
        </div>
        
        <div class="order-items">
            <h3>Order Items</h3>
            <?php if ($items_result->num_rows > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        while ($item = $items_result->fetch_assoc()) { 
                            $total += $item['Subtotal'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['Name']) ?></td>
                                <td><?= $item['Quantity'] ?></td>
                                <td>$<?= number_format($item['Price'], 2) ?></td>
                                <td>$<?= number_format($item['Subtotal'], 2) ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: bold;">Total:</td>
                            <td style="font-weight: bold;">$<?= number_format($total, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No items found for this order.</p>
            <?php } ?>
        </div>
    </div>
</div>
</body>
</html>