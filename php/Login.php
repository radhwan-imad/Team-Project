<?php
session_start();
require_once("connection.php"); // Ensure this is the first thing that runs after session start

// Initialize error message and form values
$error_message = '';
$form_data = [];

if (isset($_POST['submitted'])) {
    // Check if both email and password fields are filled
    if (empty($_POST['login-email']) || empty($_POST['login-password'])) {
        $error_message = 'Please fill both the email and password fields!';
    } else {
        // Attempt to connect to the database
        if ($conn) {
            try {
                // Prepare the statement to avoid SQL injection
               // Modify this query in login.php
$stat = $conn->prepare('SELECT User_ID, First_Name, Last_Name, Email_ID, Password FROM users WHERE Email_ID = ?');
                $stat->bind_param("s", $_POST['login-email']);
                $stat->execute();

                // Get the result
                $result = $stat->get_result();  // Getting result set

                // Check if a row was returned
                if ($result->num_rows > 0) {  // Checking row count for MySQLi
                    $row = $result->fetch_assoc();  // Fetch the associated array

                    if (password_verify($_POST['login-password'], $row['Password'])) {
                        // Store all necessary user data in session
                        $_SESSION['User_ID'] = $row['User_ID'];
                        $_SESSION['User_Name'] = $row['First_Name'];
                        $_SESSION['Last_Name'] = $row['Last_Name'];
                        $_SESSION['Email_ID'] = $row['Email_ID'];
                       
                        $_SESSION["user_logged_in"] = true;
                        // Check if the user already has a cart in the database
                        $checkCartStmt = $conn->prepare("SELECT Cart_ID FROM cart WHERE User_ID = ?");
                        $checkCartStmt->bind_param("i", $row['User_ID']);
                        $checkCartStmt->execute();
                        $cartResult = $checkCartStmt->get_result();
                        if ($cartResult->num_rows > 0) {
                            // Fetch existing Cart_ID
                            $cartRow = $cartResult->fetch_assoc();
                            $cartID = $cartRow['Cart_ID'];
                        } else {
                            // Create a new cart for the user
                            $createCartStmt = $conn->prepare("INSERT INTO cart (User_ID) VALUES (?)");
                            $createCartStmt->bind_param("i", $row['User_ID']);
                            $createCartStmt->execute();
                            $cartID = $createCartStmt->insert_id;
                        }
                        // Store Cart_ID in session
                        $_SESSION["Cart_ID"] = $cartID;
                        // Merge session cart items into the database
                        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $productID => $quantity) {
                                // Check if item already exists in database cart
                                $checkCartItemStmt = $conn->prepare("SELECT Quantity FROM cart_items WHERE Cart_ID = ? AND Product_ID = ?");
                                $checkCartItemStmt->bind_param("ii", $cartID, $productID);
                                $checkCartItemStmt->execute();
                                $cartItemResult = $checkCartItemStmt->get_result();
                                if ($cartItemResult->num_rows > 0) {
                                    // If item exists, update quantity
                                    $cartItemRow = $cartItemResult->fetch_assoc();
                                    $newQuantity = $cartItemRow['Quantity'] + $quantity;
                                    $updateCartItemStmt = $conn->prepare("UPDATE cart_items SET Quantity = ? WHERE Cart_ID = ? AND Product_ID = ?");
                                    $updateCartItemStmt->bind_param("iii", $newQuantity, $cartID, $productID);
                                    $updateCartItemStmt->execute();
                                } else {
                                    // If item doesn't exist, insert new item
                                    $insertCartItemStmt = $conn->prepare("INSERT INTO cart_items (Cart_ID, Product_ID, Quantity) VALUES (?, ?, ?)");
                                    $insertCartItemStmt->bind_param("iii", $cartID, $productID, $quantity);
                                    $insertCartItemStmt->execute();
                                }
                            }
                            // Clear session cart after merging
                            unset($_SESSION['cart']);
                        }
                    
                        header("Location: logged-in.php");
                        exit();
                    } else {
                        $error_message = 'Error logging in, password does not match.';
                    }
                } else {
                    $error_message = 'Error logging in, email not found.';
                }
            } catch (Exception $ex) {
                $error_message = "Failed to execute query: " . htmlspecialchars($ex->getMessage());
            }
        } else {
            $error_message = "Unable to connect to the database.";
        }
    }

    // Save the error message and input data to session
    $_SESSION['error_message'] = $error_message;
    $_SESSION['form_data'] = $_POST;

    // Redirect back to the same page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
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
    <title>Login - AU-RA</title>
    <link rel="icon" type="img/x-icon" href="Aura_logo1.png">
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
                <img src="Aura_logo.png" alt="logo">
            </a>
            <span class="logo-text">AU-RA<br>Fragrance your soul</span>
        </div>

        <div class="nav-right">
        <form method="GET" action="shop-all.php" class="search-form">
                <input type="text" name="query" placeholder="Search for products..." class="search-input">
                <button type="submit">Search</button>
            </form>
            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                <a href="logged-in.php">Welcome, <?php echo htmlspecialchars($_SESSION['User_Name']); ?></a>
                <a href="Logout.php">Logout</a>
            <?php else: ?>
                <a href="Signup.php">ACCOUNT</a>
            <?php endif; ?>
            <a href="contact-us.php">CONTACT-US</a>
            <a href="cart.php">CART (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
        </div>
    </header>
    <main>
        <section class="login-form-container">
            <div class="form-card">
                <h2>Login</h2>
                <form action="#" method="post">
                    <div class="input-group">
                        <label for="login-email">Email Address</label>
                        <input type="email" id="login-email" name="login-email" required value="<?php echo htmlspecialchars($form_data["login-email"] ?? ''); ?>">
                    </div>

                    <div class="input-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="login-password" required>
                    </div>

                    <div class="button-container">
                        <button type="submit" class="primary-btn">Login</button>
                        <input type="hidden" name="submitted" value="TRUE" />
                    </div>
                    <div class="links">
                        <p class="helper-text">Forgot your password? <a href="resetpassword.php">Reset it here</a>.</p>
                        <p>Don't have an account? <a href="Signup.php">Create one here</a>."</p>
                    </div>

                    <?php if ($error_message): ?>
                        <div style="color:red; padding: 8px; margin-left: 50px">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </section>
    </main>

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
   