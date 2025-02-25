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
                $resetLink = "http://localhost/Team-Project-2024-25-CS2TP/Team-Project/php/updatepassword.php?Email_ID=" . base64_encode($mailAddress) . "&key=$key";
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
            <a href="Mainpage.html">HOME</a>
            <a href="shop-all.php">SHOP ALL</a>
            <a href="Candles.html">CANDLES</a>
            <a href="society.html">Au-Ra SOCIETY</a>
            <a href="about.html">ABOUT US</a>
        </div>

        <div class="logo">
            <a href="Mainpage.html"><img src="Aura_logo.png" alt="logo"></a>
            <span class="logo-text">AU-RA</span>
        </div>

        <div class="nav-right">
            <a href="#">SEARCH</a>
            <a href="signup.php">ACCOUNT</a>
            <a href="#">COUNTRY ▼</a>
            <a href="#">WISHLIST</a>
            <a href="#">CART (0)</a>
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
                        <p>Remember your password? <a href="login.html">Login here</a>.</p>
                    </div>
                </form>
            </div>
        </section>

        <section class="additional-info">
            <div class="info-container" style="margin-top: 100px;">
                <h3>How to Reset Your Password</h3>
                <p>To reset your password, enter the email associated with your account, and we will send you a verification code. Once you enter the code, you can choose a new password.</p>
                <p>If you have any trouble, feel free to <a href="contact.html">contact support</a>.</p>
            </div>
        </section>
    </main>

    <footer>
        <p>© 2024 AU-RA. All rights reserved.</p>
    </footer>
</body>
</html>