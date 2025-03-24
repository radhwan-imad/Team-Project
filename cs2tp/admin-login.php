<?php
session_start();
require_once("connection.php"); // Ensure the database connection

$error_message = '';

if (isset($_POST['submitted'])) {
    if (empty($_POST['login-email']) || empty($_POST['login-password'])) {
        $error_message = 'Please enter both email and password!';
    } else {
        $email = $_POST['login-email'];
        $password = $_POST['login-password'];

        // Fetch admin user from the `users` table
        $stmt = $conn->prepare("SELECT User_ID, Password, First_Name FROM users WHERE Email_ID = ? AND is_admin = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();

            // **Plain text password comparison (TEMPORARY - UNSAFE)**
            if ($password === $admin['Password']) {
                // Set admin session variables
                $_SESSION["Admin_ID"] = $admin['User_ID'];
                $_SESSION["Admin_Email"] = $email;
                $_SESSION["Admin_Name"] = $admin['First_Name'];
                $_SESSION["admin_logged_in"] = true;

                // Redirect to admin dashboard
                header("Location: admin.php");
                exit();
            } else {
                $error_message = 'Incorrect password!';
            }
        } else {
            $error_message = 'Admin not found!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AU-RA</title>
    <link rel="stylesheet" href="Mainpage.css">
    <link rel="stylesheet" href="account.css">
</head>
<body>

    <!-- Announcement Bar -->
    <div class="announcement-bar">
        ADMIN LOGIN
    </div>

    <!-- Navbar -->
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

            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <a href="admin.php">Welcome, <?php echo htmlspecialchars($_SESSION['Admin_Name']); ?></a>
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
                <h2>Admin Login</h2>
                <form method="post">
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="login-email" required>
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="login-password" required>
                    </div>
                    <button type="submit" class="primary-btn">Login</button>
                    <input type="hidden" name="submitted" value="TRUE">
                </form>
                <?php if ($error_message): ?>
                    <div style="color:red; text-align:center; margin-top:10px;">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>© 2025 AU-RA. All rights reserved.</p>
    </footer>

</body>
</html>
