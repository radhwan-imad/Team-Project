<?php
require_once "connection.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Handle order deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Start a transaction
    $conn->begin_transaction();
    
    try {
        // First delete all order items associated with this order
        $delete_items_sql = "DELETE FROM order_items WHERE Order_ID = ?";
        $stmt_items = $conn->prepare($delete_items_sql);
        $stmt_items->bind_param("i", $delete_id);
        $stmt_items->execute();
        
        // Then delete payment records associated with this order
        $delete_payment_sql = "DELETE FROM payment WHERE Order_ID = ?";
        $stmt_payment = $conn->prepare($delete_payment_sql);
        $stmt_payment->bind_param("i", $delete_id);
        $stmt_payment->execute();
        
        // Finally delete the order itself
        $delete_order_sql = "DELETE FROM orders WHERE Order_ID = ?";
        $stmt_order = $conn->prepare($delete_order_sql);
        $stmt_order->bind_param("i", $delete_id);
        $stmt_order->execute();
        
        // Commit the transaction
        $conn->commit();
        
        header("Location: manage-orders.php?msg=Order deleted successfully");
        exit();
    } catch (Exception $e) {
        // Roll back the transaction if anything went wrong
        $conn->rollback();
        $error_message = "Failed to delete order: " . $e->getMessage();
    }
}
// Fetch orders
$sql = "SELECT orders.Order_ID, users.First_Name, users.Last_Name, orders.status 
        FROM orders 
        JOIN users ON orders.User_ID = users.User_ID";
$result = $conn->query($sql);
if (!$result) {
    die("Invalid query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="admin.css">
</head>
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
/* Product Table Styling */
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

tbody tr:hover {
    background-color: #fdf5e6;
}
/* Action buttons */
a[role="button"], a[href*='edit'], a[href*='#'] {
    display: inline-block;
    padding: 8px 15px;
    margin: 5px 0;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 600;
    transition: background-color 0.3s, transform 0.2s;
}

a[role="button"] {
    background-color: #3e1805;
    color: white;
    margin-bottom: 20px;
}

a[role="button"]:hover {
    background-color: #5a2609;
    transform: translateY(-2px);
}

a[href*='edit'] {
    background-color: #a27b5c;
    color: white;
    margin-right: 10px;
}

a[href*='edit']:hover {
    background-color: #8d6b53;
}

a[href*='#'] {
    background-color: #f44336;
    color: white;
}

a[href*='#']:hover {
    background-color: #d32f2f;
}

/* Alert messages */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    color: white;
}

.alert-success {
    background-color: #4CAF50;
}

.alert-danger {
    background-color: #f44336;
}

/* Delete confirmation modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 20px;
    border-radius: 8px;
    width: 400px;
    text-align: center;
}

.modal-buttons {
    margin-top: 20px;
}

.modal-buttons button {
    padding: 8px 15px;
    margin: 0 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
}

.btn-confirm {
    background-color: #f44336;
    color: white;
}

.btn-cancel {
    background-color: #ccc;
    color: #333;
}
</style>
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
        <h2>List of Orders</h2>
        
        <?php if (isset($_GET['msg'])) { ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php } ?>
        
        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php } ?>
        
        <table>
        	<thead>
            	<tr>
                	<th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['Order_ID'] ?></td>
                            <td><?= $row['First_Name'] . " " . $row['Last_Name'] ?></td>
                            <td><?= $row['status'] ?></td>
                            <td>
                            	<a href='edit-order.php?id=<?= $row['Order_ID'] ?>'>Edit</a>
                            	<a href='#' onclick="confirmDelete(<?= $row['Order_ID'] ?>)">Delete</a>
                        	</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Deletion</h3>
        <p>Are you sure you want to delete this order?</p>
        <div class="modal-buttons">
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <button class="btn-confirm" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>


<script>
    // Modal functionality
    const modal = document.getElementById('deleteModal');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    
    function confirmDelete(orderId) {
        modal.style.display = 'block';
        confirmBtn.onclick = function() {
            // Change this line to ensure the URL is correct
            window.location.href = 'manage-orders.php?delete_id=' + orderId;
        };
    }
    
    function closeModal() {
        modal.style.display = 'none';
    }
    
    // Close modal if clicked outside
    window.onclick = function(event) {
        if (event.target == modal) {
            closeModal();
        }
    };
    
    // Hide alert messages after 3 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.display = 'none';
        });
    }, 3000);
</script>
</body>
</html>