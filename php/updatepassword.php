<?php
session_start(); // Start session at the beginning

require "config.php"; // Ensure this file connects to the database properly

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
            $stmt->execute([$hashedPassword, $email, $key]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Password reset successfully.";
                echo "<script>alert('Congratulations! Password reset successfully. Please Login now');</script>";
                echo "<script>window.location.href = 'Login.php';</script>";
            } else {
                $errorMessage = "Invalid or expired reset link.";
            }
        } catch (PDOException $e) {
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
            <a href="Mainpage.html">HOME</a>
            <a href="perfumes.html">SHOP ALL</a>
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
            <a href="Signup.php">ACCOUNT</a>
            <a href="#">COUNTRY ▼</a>
            <a href="#">WISHLIST</a>
            <a href="#">CART (0)</a>
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

    <footer>
        <p>© 2024 AU-RA. All rights reserved.</p>
    </footer>
</body>
</html>
