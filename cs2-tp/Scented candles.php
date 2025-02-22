<?php
session_start();

// Disable caching for this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Past date

// Check if the user is logged in by verifying session data
if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000, '/');
}
require_once "connection.php";
$user_id = $_SESSION["User_ID"];
$sql = "SELECT product.Product_ID, product.Name, product.description, image.Image_URL 
        FROM product 
        INNER JOIN category ON product.Category_ID = category.Category_ID
        INNER JOIN image ON product.Image_ID = image.Image_ID
        WHERE category.Name IN ('Fruity Candles', 'Floral Candles') AND image.Is_Main_Image = 1;";

$all_product = $conn->query($sql);

if (!$all_product) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="scented-candle.css">
<link rel="stylesheet" href="Mainpage.css">
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

    <!-- Video Cover Section -->
    <div class="video-cover-container">
        <video autoplay muted loop class="video-cover">
            <source src="images/scented candles.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="video-overlay">
            <h1>Welcome to AU-RA</h1>
            <p>Discover our Scented Candles Collection</p>
        </div>
    </div>

    <div class="background-image-container">
        <h1 class="page-title">Bestseller's Candles</h1>
        <p class="page-description">Discover our luxurious candle collection, designed to elevate your space with warmth and ambiance. Perfect for every moment.</p>
        
        <div class="product-cards">
            <?php while ($row = mysqli_fetch_assoc($all_product)) { ?>
            <div class="product-card">
                <img src="images/<?php echo $row['Image_URL']; ?>" alt="<?php echo htmlspecialchars($row['Name']); ?>" class="product-image">
                <div class="product-info">
                    <h2><?php echo htmlspecialchars($row['Name']); ?></h2>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <a href="product.php?Product_ID=<?php echo $row['Product_ID']; ?>&user_id=<?php echo 'User ID: ' . htmlspecialchars($user_id); // Debugging line ?>" class="product-btn">Buy Now</a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <footer>
    <div class="footer-content">
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
    <div class="payment-methods">
        <p>Pay Securely with</p>
        <img src="images/payment.png" alt="Payment Methods" style="width: auto; height: 30px;">
        <p>These payment methods are for illustrative purposes only. Update this section to show the payment methods
            your website accepts based on your payment processor(s).</p>
    </div>
    <div class="footer-bottom">
        <p>2024 AU-RA. All rights reserved.</p>
    </div>
    </footer>
</body>
</html>
