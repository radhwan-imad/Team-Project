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
    require_once('connection.php');

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
        $verify_query = $conn->prepare("SELECT Email_ID FROM users WHERE Email_ID = ?");
        $verify_query->bind_param("s", $email_id);
        $verify_query->execute();
        $result = $verify_query->get_result();
    
        if ($result->num_rows != 0) {
            $error_message = 'Email has already been taken!';
        } else {
            try {
                // Hash the password for storage
                $hashed_password = password_hash($signup_password, PASSWORD_DEFAULT);
            
                // Insert user information into the 'users' table
                $stat = $conn->prepare("INSERT INTO users (First_Name, Last_Name, Email_ID, Password, Contact_NO) VALUES (?, ?, ?, ?, ?)");
                $stat->bind_param("sssss", $first_name, $last_name, $email_id, $hashed_password, $contact_no);
                $stat->execute();
            
                // Get the ID of the newly inserted user
                $id = $conn->insert_id;
            
                // Insert a new cart for the user
                $cart_stmt = $conn->prepare("INSERT INTO cart (User_ID) VALUES (?)");
                $cart_stmt->bind_param("i", $id);
                $cart_stmt->execute();
                $cart_stmt->close();
            
                echo "<script>alert('Congratulations! You are now registered. Please Login now');</script>";
                echo "<script>window.location.href = 'Login.php';</script>";
                exit; // End script execution after redirect
            } catch (mysqli_sql_exception $ex) {
                $error_message = "Sorry, a database error occurred!";
            }
            
        }
    }

    // If there's an error, save input data to session
    if ($error_message) {
        $_SESSION['error_message'] = $error_message;
        $_SESSION['form_data'] = $_POST;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Retrieve error message and form data if exists
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];

// Clear session variables after use
unset($_SESSION['error_message'], $_SESSION['form_data']);
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
        <!-- Left-side Links -->
        <div class="nav-left">
            <a href="Mainpage.html">HOME</a>
            <a href="shop-all.php">SHOP ALL</a>
            <a href="society.html">Au-Ra SOCIETY</a>
            <a href="about.html">ABOUT US</a>
        </div>

        <!-- Centered Logo -->
        <div class="logo">
            <a href="Mainpage.html">
                <img src="Aura_logo.png" alt="logo"> 
            <span class="logo-text">AU-RA<br>Fragrance your soul</span>
            </a>
    </div>

        <div class="nav-right">
            <!-- Collapsible Search Bar -->
                    <form method="GET" action="search.php" class="search-form">
                        <input
                            type="text"
                            name="query"
                            placeholder="Search for products..."
                            class="search-input"
                        >
                        <button type="submit">Search</button>
        </form>
                    <a href="Login.php">ACCOUNT</a>
                    <a href="contact-us.php">CONTACT-US</a>
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
    <div class="footer-content">
        <!-- Newsletter Subscription -->
        <div class="newsletter">
            <h3>Subscribe to Our Newsletter</h3>
            <p>Be the first to discover new arrivals and insider news.</p>
            <form>
                <input type="email" placeholder="Email *" required>
                <label>
                    <input type="checkbox"> Yes, subscribe me to your newsletter.
                </label>
                <button type="submit">Subscribe</button>
            </form>
        </div>

        <!-- Footer Links -->
        <div class="footer-links">
            <div>
                <h4>Shop</h4>
                <ul>
                    <li><a href="#">Shop All</a></li>
                    <li><a href="#">Body</a></li>
                    <li><a href="#">Home Scents</a></li>
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
                <p>500 Terry Francine Street<br>San Francisco, CA 94158<br>info@mysite.com<br>123-456-7890</p>
            </div>
            <div>
                <h4>Socials</h4>
                <ul>
                    <li><a href="#">TikTok</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">YouTube</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Payment Methods Section -->
    <div class="payment-methods">
        <p>Pay Securely with</p>
        <img src="images/payment.png" alt="Payment Methods" style="width: auto; height: 30px;">
        <p>These payment methods are for illustrative purposes only. Update this section to show the payment methods
            your website accepts based on your payment processor(s).</p>
    </div>

    <!-- Footer Copyright -->
    <div class="footer-bottom">
        <p>2024 AU-RA. All rights reserved.</p>
    </div>
</footer>
</body>

</html>