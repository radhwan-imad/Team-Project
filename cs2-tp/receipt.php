<?php
session_start();

// Check if there are no cart items in the session
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$cart_items = $_SESSION['cart'];

// Calculate total price
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Generate a random order number
$order_number = 'AU-' . strtoupper(substr(md5(uniqid()), 0, 8));

// Get current date and time
$order_date = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="Mainpage.css">
    <style>
        .receipt-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .order-details {
            margin-bottom: 20px;
        }
        .order-items {
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .order-total {
            text-align: right;
            margin-top: 20px;
        }
        .receipt-actions {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
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
                    <a href="cart.php">CART (0)</a>
                </div>
        
</header>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>Order Receipt</h1>
            <p>Order Number: <?php echo $order_number; ?></p>
            <p>Date: <?php echo $order_date; ?></p>
        </div>

        <div class="order-details">
            <h2>Order Items</h2>
            <div class="order-items">
                <?php foreach ($cart_items as $item): ?>
                    <div>
                        <?php echo $item['name']; ?> 
                        x <?php echo $item['quantity']; ?> 
                        - £<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="order-total">
                <strong>Total: £<?php echo number_format($total_price, 2); ?></strong>
            </div>
        </div>

      <div class="receipt-actions">
    <button class="receipt-button" onclick="window.print()">Print Receipt</button>
    <button class="continue-shopping-button" onclick="window.location.href='shop-all.php'">Continue Shopping</button>
</div>

    </div>

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
</footer><footer>
        <!-- Footer content from previous pages -->
        <div class="footer-content">
            <div class="footer-bottom">
                <p>2024 AU-RA. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>