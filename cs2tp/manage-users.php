<?php
require_once "connection.php";
session_start();

// Ensure only admins can access this page


// Fetch users
$sql = "SELECT User_ID, First_Name, Last_Name, Email_ID, Contact_NO, registration_date, Aura_Points FROM users";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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


</style>
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
        <h2 >Manage Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact</th>
						<th>Registration_date</th>
						<th>Aura-Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['User_ID'] ?></td>
                            <td><?= $row['First_Name'] . " " . $row['Last_Name'] ?></td>
                            <td><?= $row['Email_ID'] ?></td>
                            <td><?= $row['Contact_NO'] ?></td>
                    		<td><?= $row['registration_date'] ?></td>
                    		<td><?= $row['Aura_Points'] ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
