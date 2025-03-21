<?php
require_once "connection.php"; // Ensure this file correctly connects to your database.

session_start(); // Start session for cart functionality

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Initialize the wishlist if it doesn't exist
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Add product to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];

    // Check if the product is already in the cart
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = [
            'name' => $product_name,
            'price' => $product_price,
            'image' => $product_image,
            'quantity' => 1
        ];
    } else {
        $_SESSION['cart'][$product_id]['quantity']++;
    }
    header("Location: shop-all.php");
    exit;
}

// Add product to wishlist (persistent version)
if (isset($_POST['add_to_wishlist'])) {
    // Check if user is logged in
    if (!isset($_SESSION['User_ID'])) {
        // Redirect to login page if not logged in
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

// Determine whether to display the wishlist or the products
$viewWishlist = isset($_GET['view']) && $_GET['view'] == 'wishlist';

// Only run the product query if not viewing the wishlist
if (!$viewWishlist) {
    // Get sorting and search query parameters
    $sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'Relevance';
    $search_query = isset($_GET['query']) ? $_GET['query'] : '';

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

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $searchTerm = "%" . $search_query . "%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Handle query errors
    if (!$result) {
        die("Error executing query: " . $conn->error);
    }
} else {
    // If wishlist is being viewed, preserve the search query and sort option for consistency
    $sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'Relevance';
    $search_query = isset($_GET['query']) ? $_GET['query'] : '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="shop-all.css">
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
            <a href="Signup.php">ACCOUNT</a>
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

            <a href="cart.php">CART ▼ (<?php echo count($_SESSION['cart']); ?>)</a>
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
        <?php if ($viewWishlist): ?>
            <?php 
            // Query the database for the user's wishlist items
            if(isset($_SESSION['User_ID'])){
                $user_id = $_SESSION['User_ID'];
                $stmt = $conn->prepare("SELECT p.Product_ID, p.Name AS product_name, p.Price, p.description, i.Image_URL
                                        FROM wishlist w
                                        JOIN product p ON w.Product_ID = p.Product_ID
                                        LEFT JOIN image i ON p.Image_ID = i.Image_ID
                                        WHERE w.User_ID = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $wishlistResult = $stmt->get_result();
            }
            ?>
            <?php if (isset($wishlistResult) && $wishlistResult->num_rows > 0): ?>
                <?php while ($row = $wishlistResult->fetch_assoc()): ?>
                    <div class="product-card">
                        <img src="images/<?php echo htmlspecialchars($row['Image_URL']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                        <p>£<?php echo number_format($row['Price'], 2); ?></p>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Your wishlist is empty.</p>
            <?php endif; ?>
        <?php else: ?>
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
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo $row['Price']; ?>">
                        <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($row['Image_URL']); ?>">
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
