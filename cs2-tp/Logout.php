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


        <!-- Right-side Links -->
        <div class="nav-right">
            <form method="GET" action="search.php" class="search-form">
                        <input
                            type="text"
                            name="query"
                            placeholder="Search for products..."
                            class="search-input"
                        >
                        <button type="submit">Search</button>
        			</form>
            <a href="Login.php">LOG IN</a>
            <a href="Signup.php">SIGN UP</a>
            <a href="contact-us.php">CONTACT-US</a>
            <a href="cart.php">CART (0)</a>
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
                        <li><a href="shop-all.php">Shop All</a></li>
                        <li><a href="Scented candles.php">Candle</a></li>
                        <li><a href="Mainpage.php">Home</a></li>
                    </ul>
            </div>
            <div>
                <h4>Legal</h4>
                <ul>
                        <li><a href="about.html">Terms & Conditions</a></li>
                        <li><a href="about.html">Privacy Policy</a></li>
                        <li><a href="society.html">Shipping Policy</a></li>
                        <li><a href="contact-us.php">Refund Policy</a></li>
                        <li><a href="society.html">Accessibility Statement</a></li>
                    </ul>
            </div>
            <div>
                <h4>Headquarters</h4>
                <p>500 Terry Francine Street<br>San Francisco, CA 94158<br>info@mysite.com<br>123-456-7890</p>
            </div>
            <div>
                <h4>Socials</h4>
                <ul>
                        <li><a href="https://www.tiktok.com/login">TikTok</a></li>
                        <li><a href="https://www.instagram.com/">Instagram</a></li>
                        <li><a href="https://en-gb.facebook.com/">Facebook</a></li>
                        <li><a href="https://www.youtube.com/">YouTube</a></li>
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