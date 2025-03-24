<?php
session_start(); // Start session at the beginning

require "connection.php"; // Ensure this file connects to the database properly

// Initialize variables
$email = $key = "";

// Validate and retrieve parameters from the URL
if (isset($_GET['Email_ID']) && filter_var(base64_decode($_GET['Email_ID']), FILTER_VALIDATE_EMAIL)) {
    $email = base64_decode($_GET['Email_ID']);
}

if (isset($_GET['key']) && strlen($_GET['key']) == 32) { // MD5 hash length
    $key = $_GET['key'];
}

// Check if both parameters are present
if (empty($email) || empty($key)) {
    $_SESSION['error'] = "Invalid link or parameters missing.";
    header('Location: error.php'); // Redirect to an error page
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new-password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';

    if (empty($newPassword) || empty($confirmPassword)) {
        $errorMessage = "Both password fields are required!";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = "Passwords do not match!";
    } else {
        // Hash the password securely
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            // Update the password in the database
            $sql = "UPDATE users SET password = ?, activation = NULL WHERE Email_ID = ? AND activation = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $hashedPassword, $email, $key); // "sss" indicates string params
            $stmt->execute();

            if ($stmt->affected_rows > 0) { // Check if any row was affected
                $_SESSION['success'] = "Password reset successfully.";
                echo "<script>alert('Congratulations! Password reset successfully. Please Login now');</script>";
                echo "<script>window.location.href = 'Login.php';</script>";
            } else {
                $errorMessage = "Invalid or expired reset link.";
            }
        } catch (mysqli_sql_exception $e) {
            $errorMessage = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - AU-RA</title>
    <link rel="stylesheet" href="Mainpage.css">
    <link rel="stylesheet" href="account.css"> 
    <style>
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda; /* Light green background */
            color: #155724; /* Dark green text */
            border: 1px solid #c3e6cb; /* Green border */
        }

        .alert-danger {
            background-color: #f8d7da; /* Light red background */
            color: #721c24; /* Dark red text */
            border: 1px solid #f5c6cb; /* Red border */
        }
    </style>
</head>
<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        BLACK FRIDAY IS HERE! UP TO 50% OFF PLUS MANY COMBINATION DISCOUNTS
    </div>

        <!-- Main Navigation -->
    <header class="navbar">
        <div class="nav-left">
            <a href="Mainpage.php">HOME</a>
            <a href="shop-all.php">SHOP ALL</a>
            <a href="society.php">Au-Ra SOCIETY</a>
            <a href="about.php">ABOUT US</a>
        </div>

        <div class="logo">
            <a href="Mainpage.php">
                <img src="Aura_logo.png" alt="logo">
                <span class="logo-text">AU-RA<br>Fragrance your soul</span>
            </a>
        </div>

        <div class="nav-right">
            <form method="GET" action="shop-all.php" class="search-form">
                <input type="text" name="query" placeholder="Search for products..." class="search-input">
                <button type="submit">Search</button>
            </form>

            <!-- User Account / Welcome -->
            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                <a href="logged-in.php">Welcome, <?php echo htmlspecialchars($_SESSION['User_Name']); ?></a>
               
            <?php else: ?>
                <a href="logged-in.php">ACCOUNT</a>
            <?php endif; ?>

          
            
            <!-- Wishlist Link -->
            <a href="wishlist.php">WISHLIST (
            <?php 
            if (isset($_SESSION['User_ID'])) {
                $user_id = $_SESSION['User_ID'];
                if ($stmt = $conn->prepare("SELECT COUNT(*) as wishlist_count FROM wishlist WHERE User_ID = ?")) {
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $stmt->bind_result($wishlist_count);
                    $stmt->fetch();
                    echo $wishlist_count;
                    $stmt->close();
                } else {
                    echo 0;
                }
            } else {
                echo 0;
            }
            ?>
            )</a>
            
         
           <a href="cart.php">CART (
<?php 
if (isset($_SESSION['User_ID'])) {
    $user_id = $_SESSION['User_ID'];
    
    // Fetch the Cart_ID for the logged-in user
    if ($stmt = $conn->prepare("SELECT Cart_ID FROM cart WHERE User_ID = ?")) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result_cart = $stmt->get_result();
        if ($row_cart = $result_cart->fetch_assoc()) {
            $cart_id = $row_cart['Cart_ID'];
            $stmt->close();

            // Get the total quantity in the cart
            if ($stmt = $conn->prepare("SELECT SUM(Quantity) as total_quantity FROM cart_items WHERE Cart_ID = ?")) {
                $stmt->bind_param("i", $cart_id);
                $stmt->execute();
                $stmt->bind_result($total_quantity);
                $stmt->fetch();
                echo $total_quantity ?: 0;
                $stmt->close();
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    } else {
        echo 0;
    }
} else {
    // If not logged in, fall back to session cart count
    echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
}
?>)
</a>
           
             <a href="contact-us.php">CONTACT-US</a>
        </div>
    </header>

    <main>
        <section class="reset-password-form-container">
            <div class="form-card">
                <h2>Update Password</h2>
                
                <?php if (isset($errorMessage)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="input-group">
                        <label for="new-password">New Password</label>
                        <input type="password" id="new-password" name="new-password" placeholder="New Password" required>
                    </div>
                    <div class="input-group">
                        <label for="confirm-password">Confirm Password</label>
                        <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" required>
                    </div>
                    <!-- Hidden fields for email and key -->
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email); ?>">
                    <input type="hidden" name="key" value="<?= htmlspecialchars($key); ?>">

                    <div class="button-container">
                        <button type="submit" name="submit" class="primary-btn">Reset Password</button>
                    </div>
                    <div class="links">
                        <p>Remember your password? <a href="login.html">Login here</a>.</p>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="newsletter">
                <h3>Subscribe to our Newsletter</h3>
                <p>Be the first to discover new arrivals and insider news.</p>
                <form>
                    <label for="email">Email *</label>
                    <input type="email" id="email" placeholder="Enter your email">
                    <label>
                        <input type="checkbox"> Yes, subscribe me to your newsletter.
                    </label>
                    <button type="submit">Subscribe</button>
                </form>
            </div>

            <div class="footer-links">
                <div>
                    <h4>Shop</h4>
                    <ul>
                        <li><a href="shop-all.php">Shop All</a></li>
                        <li><a href="#">Body</a></li>
                        <li><a href="shop-all.php">Home Scents</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Shipping Policy</a></li>
                        <li><a href="#">Refund Policy</a></li>
                        <li><a href="#">Accessibility Statement</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Headquarters</h4>
                    <p>500 Terry Francine Street<br>San Francisco, CA 94158</p>
                    <p><a href="mailto:info@mysite.com">info@mysite.com</a></p>
                    <p>123-456-7890</p>
                </div>
                <div>
                    <h4>Socials</h4>
                    <ul>
                        <li><a href="https://www.tiktok.com/">TikTok</a></li>
                        <li><a href="https://www.instagram.com/">Instagram</a></li>
                        <li><a href="https://www.facebook.com/">Facebook</a></li>
                        <li><a href="https://www.youtube.com/">YouTube</a></li>
                    </ul>
                </div>
            </div>
        </div>


        <div class="payment-methods">
            <p>Pay Securely with</p>
            <img src="images/payment.png" alt="Payment Methods">
            <p>These payment methods are for illustrative purposes only.</p>
        </div>

        <div class="footer-bottom">
            <p>2024 AU-RA. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>