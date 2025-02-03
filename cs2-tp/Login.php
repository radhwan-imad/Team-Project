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
                $stat = $conn->prepare('SELECT User_ID, Password, First_Name FROM users WHERE Email_ID = ?');
                $stat->bind_param("s", $_POST['login-email']);
                $stat->execute();

                // Get the result
                $result = $stat->get_result();

                // Check if a row was returned
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();

                    // Verify the password
                    if (password_verify($_POST['login-password'], $row['Password'])) {
                        // Set session variables
                        $_SESSION["User_ID"] = $row['User_ID'];
                        $_SESSION["Email_ID"] = $_POST['login-email'];
                        $_SESSION["User_Name"] = $row['First_Name']; // Save user's first name
                        $_SESSION["user_logged_in"] = true;

                        // Redirect to the main page
                        header("Location: logged-in.html");
                        exit();
                    } else {
                        $error_message = 'Incorrect password. Please try again.';
                    }
                } else {
                    $error_message = 'Email address not found.';
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

    <header class="navbar">
        <div class="nav-left">
            <a href="Mainpage.php">HOME</a>
            <a href="shop-all.php">SHOP ALL</a>
            <a href="society.html">Au-Ra SOCIETY</a>
            <a href="about.html">ABOUT US</a>
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

    <!-- Main Content -->
    <main>
        <section class="login-form-container">
            <div class="form-card">
                <h2>Login</h2>
                <form action="#" method="post">
                    <div class="input-group">
                        <label for="login-email">Email Address</label>
                        <input type="email" id="login-email" name="login-email" placeholder="Enter your email" required value="<?php echo htmlspecialchars($form_data["login-email"] ?? ''); ?>">
                    </div>

                    <div class="input-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="login-password" placeholder="Enter your password" required>
                    </div>

                    <div class="button-container">
                        <button type="submit" class="primary-btn">Login</button>
                        <input type="hidden" name="submitted" value="TRUE" />
                    </div>

                    <div class="links">
                        <p class="helper-text">Forgot your password? <a href="resetpassword.php">Reset it here</a>.</p>
                        <p>"Don't have an account? <a href="Signup.php">Create one here</a>."</p>
                    </div>
                </form>
                
                <?php if ($error_message): ?>
                    <div style="color:red; padding: 8px; margin-left: 50px">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>© 2024 AU-RA. All rights reserved.</p>
    </footer>
</body>
</html>
