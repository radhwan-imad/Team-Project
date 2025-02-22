<?php
require_once "connection.php";

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug information
$debug_info = [];
$debug_info[] = "Session status: " . (isset($_SESSION['User_ID']) ? "Logged in (User_ID: {$_SESSION['User_ID']})" : "Not logged in");

// Initialize the wishlist if it doesn't exist
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Process form submissions first
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add product to cart
    if (isset($_POST['add_to_cart'])) {
        if (!isset($_SESSION['User_ID'])) {
            header("Location: Login.php");
            exit;
        }
        
        $user_id = $_SESSION['User_ID'];
        
        // Fetch Cart_ID for the User
        $stmt = $conn->prepare("SELECT Cart_ID FROM cart WHERE User_ID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $cart_id = $row['Cart_ID'];
        } else {
            // If no cart exists, create one
            $stmt = $conn->prepare("INSERT INTO cart (User_ID) VALUES (?)");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $cart_id = $stmt->insert_id;
        }
        
        $product_id = $_POST['product_id'];
        
        // Check if the product is already in the cart
        $stmt = $conn->prepare("SELECT Quantity FROM cart_items WHERE Cart_ID = ? AND Product_ID = ?");
        $stmt->bind_param("ii", $cart_id, $product_id);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // Update quantity if product exists
            $stmt->close();
            $stmt = $conn->prepare("UPDATE cart_items SET Quantity = Quantity + 1 WHERE Cart_ID = ? AND Product_ID = ?");
            $stmt->bind_param("ii", $cart_id, $product_id);
            $stmt->execute();
        } else {
            // Insert new product into cart_items
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO cart_items (Cart_ID, Product_ID, Quantity) VALUES (?, ?, 1)");
            $stmt->bind_param("ii", $cart_id, $product_id);
            $stmt->execute();
        }
        
        header("Location: shop-all.php");
        exit;
    }
    
    // Add product to wishlist
    if (isset($_POST['add_to_wishlist'])) {
        if (!isset($_SESSION['User_ID'])) {
            header("Location: Login.php");
            exit;
        }
        
        $user_id = $_SESSION['User_ID'];
        $product_id = $_POST['product_id'];
        
        // Check if the product is already in the wishlist
        $stmt = $conn->prepare("SELECT Wishlist_ID FROM wishlist WHERE User_ID = ? AND Product_ID = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows == 0) {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO wishlist (User_ID, Product_ID) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
        }
        
        header("Location: shop-all.php");
        exit;
    }
}

// Get sorting and search query parameters
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'Relevance';
$search_query = isset($_GET['query']) ? $_GET['query'] : '';

// Build and execute the product query
try {
    $sql = "
        SELECT 
            product.Product_ID AS Product_ID, 
            product.Name AS product_name, 
            product.description AS description, 
            product.Price AS Price, 
            product.Best_Seller AS Best_Seller, 
            category.Name AS category_name, 
            image.Image_URL AS Image_URL
        FROM product
        LEFT JOIN product_notes ON product.Product_ID = product_notes.Product_ID
        LEFT JOIN notes_library ON product_notes.Note_ID = notes_library.Note_ID
        LEFT JOIN category ON product.Category_ID = category.Category_ID
        LEFT JOIN image ON product.Image_ID = image.Image_ID
        WHERE image.Is_Main_Image = 1
          AND (
              notes_library.Note_Name LIKE ?
              OR product.Name LIKE ?
              OR product.description LIKE ?
          )
        GROUP BY product.Product_ID
    ";

    // Add sorting logic
    switch ($sort_option) {
        case 'price_low_high':
            $sql .= " ORDER BY Price ASC";
            break;
        case 'price_high_low':
            $sql .= " ORDER BY Price DESC";
            break;
        case 'best_seller':
            $sql .= " ORDER BY Best_Seller DESC, product_name ASC";
            break;
        default:
            $sql .= " ORDER BY product_name ASC";
            break;
    }

    // Prepare the query
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("SQL Error: " . $conn->error);
    }

    // Execute the query
    $searchTerm = "%" . $search_query . "%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Error executing query: " . $conn->error);
    }
    
    $debug_info[] = "Products found: " . $result->num_rows;
    
} catch (Exception $e) {
    $debug_info[] = "Error: " . $e->getMessage();
    // Create an empty result set to avoid errors in the template
    $result = new mysqli_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - AU-RA</title>
    <link rel="stylesheet" href="shop-all.css">
    <style>
        .debug-info {
            background-color: #ffeeee;
            border: 1px solid #ffaaaa;
            padding: 10px;
            margin: 10px 0;
            font-family: monospace;
        }
    </style>
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
            <form method="GET" action="shop-all.php" class="search-form">
                <input type="text" name="query" placeholder="Search for products..." class="search-input" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit">Search</button>
            </form>
            <a href="<?php echo isset($_SESSION['User_ID']) ? 'Account.html' : 'Signup.php'; ?>"><?php echo isset($_SESSION['User_ID']) ? 'MY ACCOUNT' : 'ACCOUNT'; ?></a>
            <a href="contact-us.php">CONTACT-US</a>
            <!-- Wishlist Link using persistent (database) wishlist -->
            <a href="wishlist.php">WISHLIST (<?php 
                if(isset($_SESSION['User_ID'])){
                    $user_id = $_SESSION['User_ID'];
                    $stmt = $conn->prepare("SELECT COUNT(*) as wishlist_count FROM wishlist WHERE User_ID = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result_count = $stmt->get_result();
                    $row = $result_count->fetch_assoc();
                    echo $row['wishlist_count'];
                } else {
                    echo 0;
                }
            ?>)</a>
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

    <!-- Shop Filters & Sorting -->
    <section class="shop-controls">
        <div class="sort-options">
            <form method="GET" action="">
                <input type="hidden" name="query" value="<?php echo htmlspecialchars($search_query); ?>">
                <span>SORT BY</span>
                <select name="sort" onchange="this.form.submit()">
                    <option value="Relevance" <?php echo $sort_option == 'Relevance' ? 'selected' : ''; ?>>Relevance</option>
                    <option value="price_low_high" <?php echo $sort_option == 'price_low_high' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high_low" <?php echo $sort_option == 'price_high_low' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="best_seller" <?php echo $sort_option == 'best_seller' ? 'selected' : ''; ?>>Best Sellers</option>
                </select>
            </form>
        </div>
    </section>
    
    <!-- Main Content -->
    <main class="product-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <?php if ($row['Best_Seller'] == 1): ?>
                        <div class="label">BESTSELLER</div>
                    <?php endif; ?>
                    <img src="images/<?php echo htmlspecialchars($row['Image_URL']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                    <p class="product-type"><?php echo htmlspecialchars($row['category_name']); ?></p>
                    <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                    <p>£<?php echo number_format($row['Price'], 2); ?></p>
                    <form method="POST" action="">
                        <input type="hidden" name="product_id" value="<?php echo $row['Product_ID']; ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                    </form>
                    <form method="POST" action="">
                        <input type="hidden" name="product_id" value="<?php echo $row['Product_ID']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo $row['Price']; ?>">
                        <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($row['Image_URL']); ?>">
                        <button type="submit" name="add_to_wishlist" class="add-to-wishlist">Add to Wishlist</button>
                    </form>
                    <a href="product.php?Product_ID=<?php echo $row['Product_ID']; ?>" class="buy-now">Buy Now</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products found. Please try a different search or check back later.</p>
        <?php endif; ?>
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