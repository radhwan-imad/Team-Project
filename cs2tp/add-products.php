<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('connection.php'); // Ensure you have a correct database connection

$Name = "";
$Category_ID = "";
$description = "";
$Price = "";
$Best_Seller = 0;
$Image_URL = "";
$Image_Description = "";
$Is_Main_Image = 0;
$successMessage = "";
$errorMessage = "";

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Name = $_POST["Name"];
    $Category_ID = $_POST["Category_ID"];
    $description = $_POST["description"];
    $Price = $_POST["Price"];
    $Best_Seller = isset($_POST["Best_Seller"]) ? 1 : 0;
    $Image_Description = $_POST["Image_Description"];
    $Is_Main_Image = isset($_POST["Is_Main_Image"]) ? 1 : 0;
    $Image_ID = NULL; // Default Image_ID to NULL

    // Validate required fields
    if (empty($Name) || empty($Category_ID) || empty($description) || empty($Price)) {
        $errorMessage = "All required fields must be filled out.";
    } else {
        // Step 1: Insert Product First (Without Image)
        $insertProductSQL = "INSERT INTO product (Name, Category_ID, description, Price, Best_Seller) 
                            VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertProductSQL);
        $stmt->bind_param("sisdi", $Name, $Category_ID, $description, $Price, $Best_Seller);
        
        if ($stmt->execute()) {
            $Product_ID = $stmt->insert_id; // Get the newly created Product_ID
            
            // Step 2: Handle Image Upload
            if (!empty($_FILES["product_image"]["name"])) {
                $targetDir = "images/"; // Folder where images will be stored
                
                // Check if directory exists, if not create it
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                
                // Check if directory is writable
                if (!is_writable($targetDir)) {
                    $errorMessage = "Image directory is not writable.";
                } else {
                    $fileName = basename($_FILES["product_image"]["name"]);
                    $targetFilePath = $targetDir . $fileName;
                    $imageFileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

                    // Check if the uploaded file is an actual image
                    $check = getimagesize($_FILES["product_image"]["tmp_name"]);
                    if ($check !== false) {
                        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFilePath)) {
                            $Image_URL = $fileName;

                            // Step 3: Insert Image into `image` table
                            // Corrected typo in Image_Description column name
                            $insertImageSQL = "INSERT INTO image (Product_ID, Image_URL, Image_Descriptioin, Is_Main_Image) 
                                            VALUES (?, ?, ?, ?)";
                            $stmt = $conn->prepare($insertImageSQL);
                            $stmt->bind_param("issi", $Product_ID, $Image_URL, $Image_Description, $Is_Main_Image);
                            
                            if ($stmt->execute()) {
                                $Image_ID = $stmt->insert_id; // Get the newly created Image_ID

                                // Step 4: Update `product` table with the new `Image_ID`
                                $updateProductSQL = "UPDATE product SET Image_ID = ? WHERE Product_ID = ?";
                                $stmt = $conn->prepare($updateProductSQL);
                                $stmt->bind_param("ii", $Image_ID, $Product_ID);
                                
                                if (!$stmt->execute()) {
                                    $errorMessage = "Error updating product with image: " . $stmt->error;
                                } else {
                                    $successMessage = "Product added successfully with image!";
                                }
                            } else {
                                $errorMessage = "Error inserting image: " . $stmt->error;
                            }
                        } else {
                            $errorMessage = "Error uploading the image.";
                        }
                    } else {
                        $errorMessage = "File is not an image.";
                    }
                }
            } else {
                $successMessage = "Product added successfully without image!";
            }

            // Redirect to the admin page after successful insertion if no errors
            if (empty($errorMessage)) {
                header("Location: manage-products.php");
                exit;
            }
        } else {
            $errorMessage = "Error inserting product: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Au-Ra</title>
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

        /* Action buttons */
        a[role="button"] {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px 0;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.2s;
            background-color: #3e1805;
            color: white;
            margin-bottom: 20px;
        }

        a[role="button"]:hover {
            background-color: #5a2609;
            transform: translateY(-2px);
        }

        /* Image preview */
        .image-preview {
            margin-top: 10px;
            margin-bottom: 20px;
            max-width: 300px;
            display: none;
        }

        .image-preview img {
            width: 100%;
            border-radius: 4px;
            border: 2px solid #ddd;
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
            form {
                padding: 15px;
            }
            
            input[type="text"],
            input[type="number"],
            textarea {
                padding: 10px;
            }
            
            button {
                width: 100%;
            }
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
                <li><a href="settings.php">Settings</a></li>
                <li><a href="admin-logout.php">Logout</a></li>
            </ul>
        </aside>
        
        <div class="main-content">
            <h2>Add New Product</h2>

            <?php if (!empty($errorMessage)) echo "<p style='color: red;'>$errorMessage</p>"; ?>
            <?php if (!empty($successMessage)) echo "<p style='color: green;'>$successMessage</p>"; ?>

            <a href="manage-products.php" role="button">Back to Products</a>

            <form method="POST" enctype="multipart/form-data">
                <label>Product Name:</label>
                <input type="text" name="Name" value="<?php echo htmlspecialchars($Name); ?>" required>

                <label>Category ID:</label>
                <input type="number" name="Category_ID" value="<?php echo htmlspecialchars($Category_ID); ?>" required>

                <label>Description:</label>
                <textarea name="description" required><?php echo htmlspecialchars($description); ?></textarea>

                <label>Price:</label>
                <input type="text" name="Price" value="<?php echo htmlspecialchars($Price); ?>" required>

                <div class="checkbox-container">
                    <input type="checkbox" name="Best_Seller" <?php if ($Best_Seller) echo "checked"; ?>>
                    <label style="display: inline; margin-bottom: 0;">Best Seller</label>
                </div>

                <label>Upload Image:</label>
                <input type="file" name="product_image" id="product_image" onchange="previewImage()">
                
                <!-- Image preview container -->
                <div class="image-preview" id="imagePreview">
                    <img src="#" alt="Image Preview" id="preview-img">
                </div>

                <label>Image Description:</label>
                <textarea name="Image_Description"><?php echo htmlspecialchars($Image_Description); ?></textarea>

                <div class="checkbox-container">
                    <input type="checkbox" name="Is_Main_Image" <?php if ($Is_Main_Image) echo "checked"; ?>>
                    <label style="display: inline; margin-bottom: 0;">Is Main Image</label>
                </div>

                <button type="submit">Add Product</button>
            </form>
        </div>
    </div>

    <script>
        // Function to preview the image before upload
        function previewImage() {
            const fileInput = document.getElementById('product_image');
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('preview-img');

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                previewImg.src = '';
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>