<?php
include('connection.php');

$sql = "
    SELECT 
        product.Product_ID AS Product_ID, 
        product.Name AS product_name, 
        product.description AS description, 
        product.Price AS Price, 
        product.Best_Seller AS Best_Seller, 
        category.Name AS category_name, 
        image.Image_URL AS Image_URL
    FROM product
    LEFT JOIN category ON product.Category_ID = category.Category_ID
    LEFT JOIN image ON product.Image_ID = image.Image_ID
    WHERE image.Is_Main_Image = 1
    GROUP BY product.Product_ID
";

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
    <title>Au-Ra</title>
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

/* Image styling in table */
td img {
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.product-image-placeholder {
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    color: #adb5bd;
    border-radius: 5px;
}

/* Action buttons */
a[role="button"], a[href*='edit-product.php'], a[href*='delete-product.php'] {
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

a[href*='edit-product.php'] {
    background-color: #a27b5c;
    color: white;
    margin-right: 10px;
}

a[href*='edit-product.php']:hover {
    background-color: #8d6b53;
}

a[href*='delete-product.php'] {
    background-color: #f44336;
    color: white;
}

a[href*='delete-product.php']:hover {
    background-color: #d32f2f;
}

/* Form styling */
form {
    background-color: #a27b5c;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    color: white;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

input[type="text"],
input[type="number"],
textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    font-family: 'Lora', serif;
    transition: border-color 0.3s;
}

input[type="file"] {
    margin-bottom: 20px;
    background-color: white;
    padding: 10px;
    border-radius: 4px;
    width: 100%;
}

input[type="text"]:focus,
input[type="number"]:focus,
textarea:focus {
    border-color: #3e1805;
    outline: none;
    box-shadow: 0 0 5px rgba(62, 24, 5, 0.5);
}

textarea {
    min-height: 120px;
    resize: vertical;
}

.checkbox-container {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

input[type="checkbox"] {
    margin-right: 10px;
    transform: scale(1.2);
}

button {
    background-color: #3e1805;
    color: white;
    border: none;
    padding: 12px 20px;
    font-size: 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
    font-weight: 600;
    font-family: 'Lora', serif;
}

button:hover {
    background-color: #5a2609;
    transform: translateY(-2px);
}

/* Success and error messages */
p[style*="color: red"] {
    background-color: #ffebee;
    border-left: 4px solid #f44336;
    padding: 10px 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    color: #b71c1c;
}

p[style*="color: green"] {
    background-color: #e8f5e9;
    border-left: 4px solid #4caf50;
    padding: 10px 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    color: #1b5e20;
}

/* Best Seller Badge styling */
span[style*="background-color: #d4edda"] {
    font-weight: 600;
}

/* Dashboard Cards */
.dashboard {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.card {
    background: white;
    padding: 20px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    min-width: 200px;
    flex: 1;
    text-align: center;
    font-weight: bold;
    font-family: 'Playfair Display', serif;
}

.card h3 {
    color: #3e1805;
    margin-bottom: 10px;
}

.card p {
    font-size: 2rem;
    color: #a27b5c;
}

/* Responsive styling */
@media (max-width: 992px) {
    .admin-container {
        flex-direction: column;
    }
    
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }

    .sidebar ul {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .sidebar ul li {
        margin: 5px;
    }
    
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
    
    table {
        font-size: 14px;
    }
    
    td, th {
        padding: 10px;
    }
}

@media (max-width: 768px) {
    .dashboard {
        flex-direction: column;
    }
    
    td:nth-child(2), /* Image column */
    th:nth-child(2) {
        display: none;
    }
    
    a[role="button"], a[href*='edit-product.php'], a[href*='delete-product.php'] {
        padding: 6px 10px;
        font-size: 14px;
        display: block;
        margin: 5px 0;
        text-align: center;
    }
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
        <h2 >List of Products</h2>
        <a  href="add-products.php" role="button">Add New Products</a>
        <br />
        <table >
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Best Seller</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Product_ID']); ?></td>
                        <td>
                            <?php if (!empty($row['Image_URL'])): ?>
                                <img src="images/<?php echo htmlspecialchars($row['Image_URL']); ?>" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <div class="product-image-placeholder"><i class="fas fa-image text-muted"></i></div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                        <td>£<?php echo number_format($row['Price'], 2); ?></td>
                        <td>
                            <?php echo $row['Best_Seller'] ? '<span style="background-color: #d4edda; color: #155724; padding: 5px; border-radius: 5px;">Yes</span>' : '<span style="background-color: #f8f9fa; color: #6c757d; padding: 5px; border-radius: 5px;">No</span>'; ?>
                        </td>
                        <td>
                            <a  href='edit-product.php?Product_ID=<?php echo $row['Product_ID']; ?>'>Edit</a>
                            <a  href='delete-product.php?Product_ID=<?php echo $row['Product_ID']; ?>' onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>

</html>
