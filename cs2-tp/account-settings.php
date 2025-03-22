<?php
session_start();
require_once("connection.php");

// Check if the user is logged in; if not, redirect to Login.php
if (!isset($_SESSION['User_ID'])) {
    header("Location: Login.php");
    exit();
}

// Initialize variables for error and success messages
$error_messages = [];
$success_message = '';

// Handle form submission
if (isset($_POST['submitted'])) {
    // Get form data
    $first_name = trim($_POST['first-name'] ?? '');
    $last_name = trim($_POST['last-name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Validate inputs
    if (empty($first_name)) {
        $error_messages['first-name'] = "First Name is required.";
    }
    if (empty($last_name)) {
        $error_messages['last-name'] = "Last Name is required.";
    }
    if (empty($email)) {
        $error_messages['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_messages['email'] = "Invalid email format.";
    }
    if (empty($phone)) {
        $error_messages['phone'] = "Phone number is required.";
    } elseif (!preg_match('/^\+?[0-9]{1,4}?[0-9\s\-]{7,15}$/', $phone)) {
        $error_messages['phone'] = "Invalid phone number format (e.g., +44 1234567890).";
    }

    // Check if the new email is already taken by another user
    if ($email !== $_SESSION['Email_ID']) {
        $check_email = $conn->prepare("SELECT User_ID FROM users WHERE Email_ID = ? AND User_ID != ?");
        $check_email->bind_param("si", $email, $_SESSION['User_ID']);
        $check_email->execute();
        if ($check_email->get_result()->num_rows > 0) {
            $error_messages['email'] = "Email is already taken by another user.";
        }
        $check_email->close();
    }

    // If no errors, update the database
    if (empty($error_messages)) {
        $stmt = $conn->prepare("UPDATE users SET First_Name = ?, Last_Name = ?, Email_ID = ?, Contact_NO = ? WHERE User_ID = ?");
        $stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone, $_SESSION['User_ID']);
        $stmt->execute();
        $stmt->close();

        // Update session variables with the new data
        $_SESSION['First_Name'] = $first_name;
        $_SESSION['Last_Name'] = $last_name;
        $_SESSION['Email_ID'] = $email;
        $_SESSION['Contact_NO'] = $phone;

        $success_message = "Your profile has been updated successfully.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="Mainpage.css">
    <link rel="stylesheet" href="account-settings.css">
    <style>
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
        .success {
            color: #4caf50;
            font-size: 16px;
            margin-bottom: 20px;
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
        <!-- Left-side Links -->
        <div class="nav-left">
            <a href="Mainpage.php">HOME</a>
            <a href="shop-all.php">SHOP ALL</a>
            <a href="society.html">Au-Ra SOCIETY</a>
            <a href="about.html">ABOUT US</a>
        </div>

        <!-- Centered Logo -->
        <div class="logo">
            <a href="Mainpage.php">
                <img src="Aura_logo.png" alt="logo"> 
            </a>
            <span class="logo-text">AU-RA<br>Fragrance your soul</span>
        </div>

        <!-- Right-side Navigation -->
        <div class="nav-right">
            <form method="GET" action="search.php" class="search-form">
                <input type="text" name="query" placeholder="Search for products..." class="search-input">
                <button type="submit">Search</button>
            
            </form>
            Welcome, <?php echo htmlspecialchars($_SESSION['User_Name']); ?>!</a>
            <a href="Login.php">ACCOUNT</a>
            <a href="contact-us.php">CONTACT-US</a>
            <a href="cart.php">CART (0)</a>
        </div>
    </header>

    <!-- Main Content: Profile Page -->
    <main>
        <section class="profile-section">
            <div class="profile-container">
                <div class="profile-header">
                    <h2>Your Profile</h2>
                    <p>View and edit your personal details below.</p>
                </div>

                <!-- Profile Form -->
                <form id="profile-form" class="profile-details" action="" method="POST">
                    <div class="profile-img">
                        <img src="images/meme cat.jpg" alt="Profile Picture" class="profile-pic">
                        <input type="file" id="profile-image" name="profile-image" accept="image/*" />
                    </div>

                    <div class="profile-info">
                        <h3>Personal Information</h3>

                        <!-- Success Message -->
                        <?php if ($success_message): ?>
                            <div class="success"><?php echo $success_message; ?></div>
                        <?php endif; ?>

                        <!-- First Name -->
                        <label for="first-name">First Name:</label>
                        <input type="text" id="first-name" name="first-name" value="<?php echo htmlspecialchars($_SESSION['First_Name'] ?? ''); ?>" required>
                        <?php if (isset($error_messages['first-name'])): ?>
                            <div class="error"><?php echo $error_messages['first-name']; ?></div>
                        <?php endif; ?>

                        <!-- Last Name -->
                        <label for="last-name">Last Name:</label>
                        <input type="text" id="last-name" name="last-name" value="<?php echo htmlspecialchars($_SESSION['Last_Name'] ?? ''); ?>" required>
                        <?php if (isset($error_messages['last-name'])): ?>
                            <div class="error"><?php echo $error_messages['last-name']; ?></div>
                        <?php endif; ?>

                        <!-- Email -->
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['Email_ID'] ?? ''); ?>" required>
                        <?php if (isset($error_messages['email'])): ?>
                            <div class="error"><?php echo $error_messages['email']; ?></div>
                        <?php endif; ?>

                        <!-- Phone -->
                        <label for="phone">Phone:</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_SESSION['Contact_NO'] ?? ''); ?>" required>
                        <?php if (isset($error_messages['phone'])): ?>
                            <div class="error"><?php echo $error_messages['phone']; ?></div>
                        <?php endif; ?>

                        <!-- Address (optional, not updated in database yet) -->
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" placeholder="Enter your address">
                    </div>

                    <div class="profile-actions">
                        <input type="hidden" name="submitted" value="true">
                        <button type="submit" class="primary-btn">Save Changes</button>
                    </div>
                </form>

                <!-- Back to Dashboard Button -->
                <div class="back-btn-container">
                    <a href="logged-in.php" class="back-btn">Back to Dashboard</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>© 2024 AU-RA. All rights reserved.</p>
        <ul class="footer-links">
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="privacy-policy.html">Privacy Policy</a></li>
            <li><a href="terms.html">Terms of Service</a></li>
        </ul>
    </footer>
</body>
</html>