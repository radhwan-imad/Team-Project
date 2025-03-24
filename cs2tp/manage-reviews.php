<?php
require_once "connection.php";
session_start();



// Handle review deletion if ID is provided
if(isset($_POST['delete_review']) && isset($_POST['review_id']) && !empty($_POST['review_id'])) {
    $reviewId = intval($_POST['review_id']);
    
    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM review WHERE Review_ID = ?");
    $stmt->bind_param("i", $reviewId);
    
    if($stmt->execute()) {
        // Set success message
        $message = "Review deleted successfully";
        $message_type = "success";
    } else {
        // Set error message
        $message = "Failed to delete review: " . $conn->error;
        $message_type = "error";
    }
    
    $stmt->close();
}

// Fetch reviews
$sql = "SELECT review.Review_ID, users.First_Name, users.Last_Name, product.Name AS Product_Name, review.Rating, review.Review_Text 
        FROM review 
        JOIN users ON review.User_ID = users.User_ID
        JOIN product ON review.Product_ID = product.Product_ID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Admin</title>
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
        button.delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.2s;
        }

        button.delete-btn:hover {
            background-color: #d32f2f;
            transform: translateY(-2px);
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fdf5e6;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #a27b5c;
            border-radius: 8px;
            width: 50%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .modal-content h3 {
            color: #3e1805;
            margin-bottom: 20px;
            font-family: 'Playfair Display', serif;
        }
        
        .modal-buttons {
            margin-top: 20px;
        }
        
        .modal-buttons button {
            padding: 10px 20px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Lora', serif;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .cancel-btn {
            background-color: #ccc;
            color: #333;
        }
        
        .confirm-btn {
            background-color: #f44336;
            color: white;
        }
        
        .confirm-btn:hover {
            background-color: #d32f2f;
        }
        
        .cancel-btn:hover {
            background-color: #bbb;
        }
        
        /* Message styles */
        .success-message, .error-message {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            <h2>Manage Reviews</h2>
            
            <?php if(isset($message)) { ?>
                <div class="<?= $message_type == 'success' ? 'success-message' : 'error-message' ?>">
                    <?= $message ?>
                </div>
            <?php } ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Reviewer</th>
                        <th>Product</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['First_Name'] . " " . $row['Last_Name']) ?></td>
                            <td><?= htmlspecialchars($row['Product_Name']) ?></td>
                            <td><?= $row['Rating'] ?>/5</td>
                            <td><?= htmlspecialchars($row['Review_Text']) ?></td>
                            <td><button class="delete-btn" onclick="confirmDelete(<?= $row['Review_ID'] ?>)">Delete</button></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Delete Review</h3>
            <p>Are you sure you want to delete this review? This action cannot be undone.</p>
            <div class="modal-buttons">
                <button class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button class="confirm-btn" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
    
    <!-- Hidden form for submitting delete request -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="review_id" id="review_id_input">
        <input type="hidden" name="delete_review" value="1">
    </form>

    <script>
        // Get the modal
        var modal = document.getElementById("deleteModal");
        var confirmBtn = document.getElementById("confirmDeleteBtn");
        var deleteForm = document.getElementById("deleteForm");
        var reviewIdInput = document.getElementById("review_id_input");
        
        // Function to open modal and set review ID
        function confirmDelete(reviewId) {
            reviewIdInput.value = reviewId;
            modal.style.display = "block";
            
            // Set the onclick event for confirm button
            confirmBtn.onclick = function() {
                deleteForm.submit();
            }
        }
        
        // Function to close modal
        function closeModal() {
            modal.style.display = "none";
        }
        
        // Close the modal if user clicks outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }
        
        // Auto-hide messages after 5 seconds
        setTimeout(function() {
            var messages = document.querySelectorAll('.success-message, .error-message');
            messages.forEach(function(message) {
                message.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html>