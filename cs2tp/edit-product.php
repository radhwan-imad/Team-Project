<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('connection.php');

// Initialize variables
$Product_ID = $Name = $Category_ID = $description = "";
$Image_ID = $Price = $Best_Seller = "";
$errorMessage = $successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["Product_ID"])) {
        header("Location: manage_products.php");
        exit;
    }

    $Product_ID = $_GET["Product_ID"];

    // Fetch product details
    $sql = "SELECT p.*, i.Image_ID, i.Image_descriptioin, i.Image_URL 
            FROM product p 
            LEFT JOIN image i ON p.Product_ID = i.Product_ID 
            WHERE p.Product_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $Product_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        header("Location: admin_products.php");
        exit;
    }

    $Name = $row["Name"];
    $Category_ID = $row["Category_ID"];
    $description = $row["description"];  // Keeping typo as requested
    $Image_ID = $row["Image_ID"];
    $Price = $row["Price"];
    $Best_Seller = $row["Best_Seller"];
    $Image_URL = $row["Image_URL"];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Product_ID = $_POST["Product_ID"];
    $Name = $_POST["Name"];
    $Category_ID = $_POST["Category_ID"];
    $description = $_POST["description"];
    $Price = $_POST["Price"];
    $Best_Seller = isset($_POST["Best_Seller"]) ? 1 : 0;

    if (empty($Name) || empty($Category_ID) || empty($Price)) {
        $errorMessage = "Name, Category, and Price fields are required.";
    } else {
        $conn->begin_transaction();
        try {
            // Update product details
            $sql = "UPDATE product SET Name = ?, Category_ID = ?, description = ?, Price = ?, Best_Seller = ? WHERE Product_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sisidi", $Name, $Category_ID, $description, $Price, $Best_Seller, $Product_ID);
            $stmt->execute();

            // Handle image upload
            if (!empty($_FILES['product_image']['name'])) {
                $target_dir = "images/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0775, true);
                }

                $file_tmp = $_FILES["product_image"]["tmp_name"];
                $file_name = $_FILES["product_image"]["name"];
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

                if (!in_array($file_extension, $allowed_extensions)) {
                    throw new Exception("Only JPG, JPEG, PNG, and GIF files are allowed.");
                }

                $new_filename = "product_" . $Product_ID . "_" . time() . "." . $file_extension;
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    $image_url = $new_filename;
                    $image_desc = "Image for " . htmlspecialchars($Name);

                    $check_img = "SELECT Image_ID FROM image WHERE Product_ID = ?";
                    $stmt_img = $conn->prepare($check_img);
                    $stmt_img->bind_param("i", $Product_ID);
                    $stmt_img->execute();
                    $result_img = $stmt_img->get_result();

                    if ($result_img->num_rows > 0) {
                        $row_img = $result_img->fetch_assoc();
                        $image_id = $row_img['Image_ID'];
                        $update_img = "UPDATE image SET Image_URL = ?, Image_descriptioin = ? WHERE Image_ID = ?"; // Keeping typo
                        $stmt_update = $conn->prepare($update_img);
                        $stmt_update->bind_param("ssi", $image_url, $image_desc, $image_id);
                        $stmt_update->execute();
                    } else {
                        $insert_img = "INSERT INTO image (Product_ID, Image_URL, Image_descriptioin, Is_Main_Image) VALUES (?, ?, ?, 1)"; // Keeping typo
                        $stmt_insert = $conn->prepare($insert_img);
                        $stmt_insert->bind_param("iss", $Product_ID, $image_url, $image_desc);
                        $stmt_insert->execute();
                    }
                } else {
                    throw new Exception("Error moving uploaded file. Check folder permissions.");
                }
            }
            $conn->commit();
            $successMessage = "Product updated successfully.";
            header("Location: manage-products.php");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $errorMessage = "Error updating product: " . $e->getMessage();
        }
    }
}

$categories_sql = "SELECT Category_ID, Name FROM category ORDER BY Name";
$categories_result = $conn->query($categories_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
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

        /* Form styling */
        form {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #3e1805;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            font-family: 'Lora', serif;
            transition: border-color 0.3s;
        }

        input[type="file"] {
            margin-top: 10px;
            background-color: white;
            padding: 10px;
            border-radius: 4px;
            width: 100%;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            border-color: #3e1805;
            outline: none;
            box-shadow: 0 0 5px rgba(62, 24, 5, 0.5);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-check {
            display: flex;
            align-items: center;
        }

        .form-check-input {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .form-check-label {
            font-weight: 600;
            color: #3e1805;
        }

        /* Image styling */
        .img-thumbnail {
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Small text styling */
        .form-text {
            color: #6c757d;
            font-size: 0.875em;
            margin-top: 5px;
        }

        /* Button styling */
        .btn {
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            font-weight: 600;
            font-family: 'Lora', serif;
            border: none;
            display: inline-block;
            margin-right: 10px;
        }

        .btn-primary {
            background-color: #3e1805;
            color: white;
        }

        .btn-primary:hover {
            background-color: #5a2609;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #a27b5c;
            color: white;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background-color: #8d6b53;
            transform: translateY(-2px);
        }

        /* Alert styling */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-danger {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
            color: #b71c1c;
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
        }

        @media (max-width: 768px) {
            .btn {
                padding: 8px 15px;
                font-size: 14px;
                display: block;
                margin: 10px 0;
                width: 100%;
                text-align: center;
            }
        }
    </style>
    <script>
        function confirmUpdate(event) {
            if (!confirm("Are you sure you want to update this product?")) {
                event.preventDefault();
            }
        }
    </script>
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
            <h2>Edit Product</h2>
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger" role="alert"><strong><?= $errorMessage ?></strong></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" onsubmit="confirmUpdate(event)">
                <input type="hidden" name="Product_ID" value="<?= $Product_ID; ?>">

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" class="form-control" name="Name" value="<?= htmlspecialchars($Name); ?>" required>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" name="Category_ID" required>
                        <option value="">Select Category</option>
                        <?php while ($category = $categories_result->fetch_assoc()): ?>
                            <option value="<?= $category['Category_ID']; ?>" <?= ($category['Category_ID'] == $Category_ID) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($category['Name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($description); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Price</label>
                    <input type="number" class="form-control" name="Price" value="<?= $Price; ?>" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Product Image</label>
                    <?php if (!empty($Image_URL)): ?>
                        <div class="mb-2">
                            <img src="images/<?= htmlspecialchars($Image_URL); ?>" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control-file" name="product_image" accept="image/*">
                    <small class="form-text">Upload a new image only if you want to change the current one.</small>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" name="Best_Seller" id="Best_Seller" <?= ($Best_Seller == 1) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="Best_Seller">Mark as Best Seller</label>
                </div>

                <button type="submit" class="btn btn-primary">Update Product</button>
                <a class="btn btn-secondary" href="manage-products.php" role="button">Cancel</a>
            </form>
        </div>
    </div>
</body>

</html>