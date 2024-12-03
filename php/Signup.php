<?php
session_start();

// Initialize error message and form values
$error_message = '';
$first_name = '';
$last_name = '';
$email_id = '';
$contact_no = '';

if (isset($_POST['submitted'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    // Connect to the database
    require_once('config.php');

    // Prepare and validate form inputs
    $first_name = isset($_POST["first-name"]) ? trim($_POST['first-name']) : false; 
    $last_name = isset($_POST["last-name"]) ? trim($_POST['last-name']) : false;
    $email_id = isset($_POST["signup-email"]) ? trim($_POST['signup-email']) : false;
    $contact_no = isset($_POST["contact-no"]) ? trim($_POST['contact-no']) : false;
    $signup_password = isset($_POST['signup-password']) ? trim($_POST['signup-password']) : false;
    $signup_password_confirm = isset($_POST['signup-password-confirm']) ? trim($_POST['signup-password-confirm']) : false;

    // Check if all required fields are filled
    if (!$first_name || !$last_name || !$email_id || !$contact_no || !$signup_password || !$signup_password_confirm) {
        $error_message = 'Please fill all required fields!';
    } elseif ($signup_password !== $signup_password_confirm) {
        $error_message = 'Passwords do not match!';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,20}$/', $signup_password)) {
        $error_message = 'Password is in the wrong format';
    
    
    
    } else {
        // Unique email check: using prepared statement
        $verify_query = $conn->prepare("SELECT Email_ID FROM users WHERE Email_ID = ?");
        $verify_query->execute([$email_id]);

        if ($verify_query->rowCount() != 0) {
            $error_message = 'Email has already been taken!';
        } else {
            try {
                // Hash the password for storage
                $hashed_password = password_hash($signup_password, PASSWORD_DEFAULT);

                // Insert user information into the 'users' table
                $stat = $conn->prepare("INSERT INTO users (First_Name, Last_Name, Email_ID, Password, Contact_NO) VALUES (?, ?, ?, ?, ?)");
                $stat->execute([$first_name, $last_name, $email_id, $hashed_password, $contact_no]);

                // Get the ID of the newly inserted user
                $id = $conn->lastInsertId();
                echo "<script>alert('Congratulations! You are now registered. Please Login now');</script>";
                echo "<script>window.location.href = 'Login.php';</script>"; // Redirect to login page

            } catch (PDOException $ex) {
                $error_message = "Sorry, a database error occurred! <br>Error details: <em>" . $ex->getMessage() . "</em>";
            }
        }
    }

    // If there's an error, save input data to session
    if ($error_message) {
        $_SESSION['error_message'] = $error_message;
        $_SESSION['form_data'] = $_POST; // Store form data to repopulate
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Retrieve error message and form data if exists
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];

// Clear session variables after use
if (!empty($_SESSION['error_message'])) {
    unset($_SESSION['error_message']);
}
if (!empty($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="Mainpage.css">
    <link rel="stylesheet" href="account.css"> 
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
            <a href="shop-all.html">SHOP ALL</a>
            <a href="Candles.html">CANDLES</a>
            <a href="society.html">Au-Ra SOCIETY</a>
            <a href="about.html">ABOUT US</a>
        </div>
        <div class="logo">
            <a href="Mainpage.html">
                <img src="Aura_logo.png" alt="logo"> </a>
            <span class="logo-text">AU-RA</span>
        </div>
        <div class="nav-right">
            <a href="#">SEARCH</a>
            <a href="Login.php">LOG IN</a>
            <a href="Signup.php">SIGN UP</a>
            <a href="#">COUNTRY ▼</a>
            <a href="#">WISHLIST</a>
            <a href="#">CART (0)</a>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <section class="signup-form-container">
            <div class="form-card">
                <h2>Create an Account</h2>
                <form action="#" method="post">
                    <div class="input-group">
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" name="first-name" placeholder="Enter your first name" required value="<?php echo isset($form_data["first-name"]) ? htmlspecialchars($form_data["first-name"]) : ''; ?>">
                    </div>
                    <div class="input-group">
                        <label for="last-name">Last Name</label>
                        <input type="text" id="last-name" name="last-name" placeholder="Enter your last name" required value="<?php echo isset($form_data["last-name"]) ? htmlspecialchars($form_data["last-name"]) : ''; ?>">
                    </div>
                    <div class="input-group">
                        <label for="signup-email">Email Address</label>
                        <input type="email" id="signup-email" name="signup-email" placeholder="Enter your email" required value="<?php echo isset($form_data["signup-email"]) ? htmlspecialchars($form_data["signup-email"]) : ''; ?>">
                    </div>
                    <div class="input-group">
                        <label for="contact-no">Contact Number</label>
                        <input type="text" id="contact-no" name="contact-no" placeholder="Enter your number" 
                        pattern="\+?[0-9]{1,4}?[0-9\s\-]{7,15}$"
                        title="Enter a valid phone number, including country code if applicable (e.g., +44 1234567890)"
                        required value="<?php echo isset($form_data["contact-no"]) ? htmlspecialchars($form_data["contact-no"]) : ''; ?>">    
                    </div>
                    <div class="input-group">
                        <label for="signup-password">Password</label>
                        <input type="password" id="signup-password" name="signup-password" placeholder="Create a password" required>
                        <small>Password must be 8-20 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.</small>
                    </div>
                    <div class="input-group">
                        <label for="signup-password-confirm">Re-enter Password</label>
                        <input type="password" id="signup-password-confirm" name="signup-password-confirm" placeholder="Re-enter your password" required>
                    </div>
                    <div class="button-container">
                        <button type="submit" name="submit" class="primary-btn">Sign Up</button>
                        <input type="hidden" name="submitted" value="true"/> 
                    </div>
                    <div class="links">
                        <p>Already have an account? <a href="Login.php">Login here</a>.</p>
                    </div>
                </form>
                <?php if ($error_message): ?>
                    <div style="color:red; padding: 10px; margin-left: 90px;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 AU-RA. All rights reserved.</p>
    </footer>
</body>

</html>