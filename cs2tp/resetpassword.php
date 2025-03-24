<?php
require "connection.php";  
require 'PHPMailer-master/PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mailAddress = ""; // Changed variable name for clarity
$error_message = "";
$success_message = "";

if (isset($_POST['submit'])) {
    $mailAddress = $_POST['reset-email']; // Use mailAddress for email input

    if (empty($mailAddress)) {
        $error_message = "Enter your email!";
    } elseif (!filter_var($mailAddress, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format!";
    } else {
        // SQL query to select user
        if ($stmt = $conn->prepare("SELECT Email_ID, First_Name, User_ID FROM users WHERE Email_ID = ?")) {
            $stmt->bind_param("s", $mailAddress);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 0) { // Check if no rows were returned
                $error_message = "E-mail address did not match!";
            } else {
                // Fetch user details
                $stmt->bind_result($email, $name, $id);
                $stmt->fetch(); // fetch will load the values into the bound variables

                // Generate a unique key for password reset
                $key = md5(uniqid(rand(), true));

                // Update the user record with the generated key
                if ($stmt_update = $conn->prepare("UPDATE users SET activation = ? WHERE User_ID = ?")) {
                    $stmt_update->bind_param("si", $key, $id); // "si" indicates string, integer
                    $stmt_update->execute();
                    $stmt_update->close(); // Close the update statement
                }

                // Prepare the reset link
                $resetLink = "https://cs2team40.cs2410-web01pvm.aston.ac.uk/cs2tp/updatepassword.php?Email_ID=" . base64_encode($mailAddress) . "&key=$key";
                $subject = 'Password Reset | AURA';
                $message = "Hello $name,<br><br>
                            Someone requested to reset your password.<br>
                            If this was you, <a href='$resetLink'>click here</a> to reset your password.<br><br>
                            If not, just ignore this email.<br><br>
                            Thank you,<br>AURA";

                // Create a new PHPMailer instance
                $phpMailer = new PHPMailer(true); 

                // SMTP configuration
                $phpMailer->isSMTP();
                $phpMailer->Host = 'smtp.gmail.com'; 
                $phpMailer->SMTPAuth = true;
                $phpMailer->Username = 'auraprojects.2024@gmail.com'; 
                $phpMailer->Password = 'jdpj urbw qkwn wtff'; // Make sure to use an app password if using 2FA
                $phpMailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
                $phpMailer->Port = 587; 
            
                // Recipients
                $phpMailer->setFrom('auraprojects.2024@gmail.com', 'AU-RA');
                $phpMailer->addAddress($mailAddress, $name); // Add a recipient
            
                $phpMailer->isHTML(true); 
                $phpMailer->Subject = $subject;
                $phpMailer->Body    = $message;

                if ($phpMailer->send()) {
                    $success_message = "Reset email sent successfully!";
                } else {
                    $error_message = "Failed to send email: " . htmlspecialchars($phpMailer->ErrorInfo);
                }
            }
            $stmt->close(); // Close the statement
        } else {
            // Log or handle the error if the statement preparation fails
            $error_message = "Database error: " . htmlspecialchars($conn->error);
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
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
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
                <h2>Reset Password</h2>
                <form action="" method="post">
                    <?php 
                    if (!empty($error_message)) {
                        echo '<div class="alert alert-danger">' . htmlspecialchars($error_message) . '</div>';
                    }
                    if (!empty($success_message)) {
                        echo '<div class="alert alert-success">' . htmlspecialchars($success_message) . '</div>';
                    }
                    ?>
                    <div class="input-group">
                        <label for="reset-email">Enter your email address</label>
                        <input type="email" id="reset-email" name="reset-email" placeholder="Enter your email" required>
                    </div>
                    <div class="button-container">
                        <button type="submit" name="submit" class="primary-btn">Send Verification Code</button>
                    </div>
                    <div class="links">
                        <p>Remember your password? <a href="Login.php">Login here</a>.</p>
                    </div>
                </form>
            </div>
        </section>

        <section class="additional-info">
            <div class="info-container" style="margin-top: 100px;">
                <h3>How to Reset Your Password</h3>
                <p>To reset your password, enter the email associated with your account, and we will send you a verification code. Once you enter the code, you can choose a new password.</p>
                <p>If you have any trouble, feel free to <a href="contact-us.php">"contact support"</a>.</p>
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