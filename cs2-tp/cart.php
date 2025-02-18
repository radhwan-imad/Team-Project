<?php
session_start();
require 'connection.php'; // Include database connection file

// Get current user ID from session
$user_id = $_SESSION["User_ID"] ?? null;
if (!$user_id) {
    header("Location: Login.php");
    exit;
}

// Handle Add, Remove, Update Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $action = $_POST['action'];

    // Get Cart_ID for the User
    $cartQuery = "SELECT Cart_ID FROM cart WHERE User_ID = ?";
    $stmt = $conn->prepare($cartQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cartResult = $stmt->get_result();
    $cart = $cartResult->fetch_assoc();
    $cart_id = $cart['Cart_ID'] ?? null;

    if (!$cart_id) {
        // If no cart exists, create one
        $createCartQuery = "INSERT INTO cart (User_ID) VALUES (?)";
        $stmt = $conn->prepare($createCartQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cart_id = $stmt->insert_id;
    }

    if ($action === 'add') {
        $quantity = 1;

        // Check if item already exists in cart
        $checkQuery = "SELECT * FROM cart_items WHERE Cart_ID = ? AND Product_ID = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ii", $cart_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update quantity if item exists
            $updateQuery = "UPDATE cart_items SET Quantity = Quantity + ? WHERE Cart_ID = ? AND Product_ID = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("iii", $quantity, $cart_id, $product_id);
        } else {
            // Insert new item if not exists
            $insertQuery = "INSERT INTO cart_items (Cart_ID, Product_ID, Quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("iii", $cart_id, $product_id, $quantity);
        }
        $stmt->execute();
    } elseif ($action === 'remove') {
        // Decrease quantity
        $updateQuery = "UPDATE cart_items SET Quantity = Quantity - 1 WHERE Cart_ID = ? AND Product_ID = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ii", $cart_id, $product_id);
        $stmt->execute();

        // Remove if quantity reaches zero
        $deleteQuery = "DELETE FROM cart_items WHERE Cart_ID = ? AND Product_ID = ? AND Quantity <= 0";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("ii", $cart_id, $product_id);
        $stmt->execute();
    }  elseif($action === 'Remove'){
        // Remove if quantity reaches zero
        $deleteQuery = "DELETE FROM cart_items WHERE Cart_ID = ? AND Product_ID = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("ii", $cart_id, $product_id);
        $stmt->execute();
    }
    

    // Redirect to prevent form resubmission
    header("Location: cart.php");
    exit;
}

// Fetch Cart Items for the User
$cartItemsQuery = "SELECT p.Product_ID, p.Name, p.Price, i.Image_URL, ci.Quantity 
FROM cart_items ci
JOIN product p ON ci.Product_ID = p.Product_ID
JOIN cart c ON ci.Cart_ID = c.Cart_ID
JOIN image i ON p.Product_ID = i.Product_ID AND i.Is_Main_Image = 1
WHERE c.User_ID = ?
";
$stmt = $conn->prepare($cartItemsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_price = 0;
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['Price'] * $row['Quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="Mainpage.css">
    <link rel="stylesheet" href="cart.css">
</head>

<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        BLACK FRIDAY IS HERE! UP TO 50% OFF PLUS MANY COMBINATION DISCOUNTS
    </div>

    <!-- Navbar -->
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
            <form method="GET" action="search.php" class="search-form">
                <input type="text" name="query" placeholder="Search for products..." class="search-input">
                <button type="submit">Search</button>
            </form>
            <a href="Login.php">ACCOUNT</a>
            <a href="contact-us.php">CONTACT-US</a>
            <!-- Display the cart quantity -->
            <a href="cart.php">CART ▼ (<?php 
                if (isset($_SESSION['User_ID'])) {
                    $user_id = $_SESSION['User_ID'];

                    // Fetch the cart ID
                    $stmtc = $conn->prepare("SELECT Cart_ID FROM cart WHERE User_ID = ?");
                    $stmtc->bind_param("i", $user_id);
                    $stmtc->execute();
                    $result_cart = $stmtc->get_result();
                    if ($row = $result_cart->fetch_assoc()) {
                        $cart_id = $row['Cart_ID'];

                        // Get the total quantity in the cart
                        $stmtc = $conn->prepare("SELECT SUM(Quantity) as total_quantity FROM cart_items WHERE Cart_ID = ?");
                        $stmtc->bind_param("i", $cart_id);
                        $stmtc->execute();
                        $stmtc->store_result();
                        if ($stmtc->num_rows > 0) {
                            $stmtc->bind_result($total_quantity);
                            $stmtc->fetch();
                            echo $total_quantity ?: 0;
                        } else {
                            echo 0;
                        }
                    } else {
                        echo 0;
                    }
                } else {
                    echo 0;
                }
            ?>)</a>
        </div>
    </header>


    <main>
    <h1 class="heading1">Your Shopping Cart</h1>
    <div class="cart-container">
        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty. <a href="shop-all.php">Continue shopping</a>.</p>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="images/<?php echo $item['Image_URL']; ?>" alt="<?php echo $item['Name']; ?>">
                    <div>
                        <h2><?php echo $item['Name']; ?></h2>
                        <p>Price: £<?php echo number_format($item['Price'], 2); ?></p>
                        <p>Quantity: <?php echo $item['Quantity']; ?></p>
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $item['Product_ID']; ?>">
                            <button type="submit" name="action" value="remove">-</button>
                            <button type="submit" name="action" value="add">+</button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $item['Product_ID']; ?>">
                            <button type="submit" name="action" value="Remove" class="remove-button">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="cart-total">
                <h3>Total: £<?php echo number_format($total_price, 2); ?></h3>
            </div>
            
            <form action="payment.php" method="POST">
                <button type="submit" class="checkout-button">Proceed to Checkout</button>
            </form>
        <?php endif; ?>
    </div>
    </main>
    <!-- Footer -->
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
