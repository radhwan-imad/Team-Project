<?php
require_once "connection.php";
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure the user is logged in; otherwise, redirect to login page.
if (!isset($_SESSION['User_ID'])) {
    header("Location: Login.php");
    exit;
}
$user_id = $_SESSION['User_ID'];

// Handle removal of a wishlist item.
if (isset($_POST['remove_from_wishlist'])) {
    $product_id = intval($_POST['product_id']);
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE User_ID = ? AND Product_ID = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
    header("Location: wishlist.php");
    exit;
}

// Handle "Add to Cart" from wishlist.
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $product_name = $_POST['product_name'];
    $product_price = floatval($_POST['product_price']);
    $product_image = $_POST['product_image'];

    // Insert into the session cart (or into your cart table if applicable)
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = [
            'name'     => $product_name,
            'price'    => $product_price,
            'image'    => $product_image,
            'quantity' => 1
        ];
    } else {
        $_SESSION['cart'][$product_id]['quantity']++;
    }

    // Optionally remove the item from wishlist after adding to cart
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE User_ID = ? AND Product_ID = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->close();

    header("Location: wishlist.php");
    exit;
}

// Retrieve wishlist items for the current user
$sql = "SELECT p.Product_ID, p.Name AS product_name, p.Price, p.description, i.Image_URL
        FROM wishlist w
        JOIN product p ON w.Product_ID = p.Product_ID
        LEFT JOIN image i ON p.Image_ID = i.Image_ID
        WHERE w.User_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="shop-all.css">
</head>
<body>
    <!-- Navigation -->
    <header class="navbar">
        <div class="nav-left">
            <a href="Mainpage.html">HOME</a>
            <a href="shop-all.php">SHOP ALL</a>
            <a href="society.html">Au-Ra SOCIETY</a>
            <a href="about.html">ABOUT US</a>
        </div>
        <div class="logo">
            <a href="Mainpage.html">
                <img src="Aura_logo.png" alt="logo">
                <span class="logo-text">AU-RA<br>Fragrance your soul</span>
            </a>
        </div>
        <div class="nav-right">
            <!-- Search Bar -->
            <form method="GET" action="shop-all.php" class="search-form">
                <input type="text" name="query" placeholder="Search for products..." class="search-input">
                <button type="submit">Search</button>
            </form>
            <a href="Signup.php">ACCOUNT</a>
            <a href="contact-us.php">CONTACT-US</a>
            <a href="cart.php">CART ▼ (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
            <a href="wishlist.php">WISHLIST</a>
        </div>
    </header>

    <h2 style="text-align:center; margin-top:20px;">My Wishlist</h2>

    <!-- Wishlist Items -->
    <main class="product-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="images/<?php echo htmlspecialchars($row['Image_URL']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                    <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                    <p>£<?php echo number_format($row['Price'], 2); ?></p>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    
                    <!-- Add to Cart Form -->
                    <form method="POST" action="">
                        <input type="hidden" name="product_id" value="<?php echo $row['Product_ID']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo $row['Price']; ?>">
                        <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($row['Image_URL']); ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                    </form>
                    
                    <!-- Remove from Wishlist Form -->
                    <form method="POST" action="">
                        <input type="hidden" name="product_id" value="<?php echo $row['Product_ID']; ?>">
                        <button type="submit" name="remove_from_wishlist" class="remove-from-wishlist">Remove from Wishlist</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">Your wishlist is empty.</p>
        <?php endif; ?>
    </main>

    <!-- Footer -->
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
