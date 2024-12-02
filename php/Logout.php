<?php
    session_start();
    session_destroy();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AU-RA</title>
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
            <a href="shop-all.html">SHOP ALL</a>
            <a href="Candles.html">CANDLES</a>
            <a href="society.html">Au-Ra SOCIETY</a>
            <a href="about.html">ABOUT US</a>
        </div>

        <!-- Centered Logo -->
        <div class="logo">
            <a href="Mainpage.html">
                <img src="Aura_logo.png" alt="logo"> </a>
            <span class="logo-text">AU-RA</span>
        </div>

        <!-- Right-side Links -->
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
        <section class="login-form-container">
            <div class="form-card">
                <form action="#" method="post">
                    <div class="input-group">
					<h2>Logged out now!</h2>
        <p>Would you like to log in again? <a href="Login.php">Log in</a></p>
    </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 AU-RA. All rights reserved.</p>
    </footer>
</body>
</html>