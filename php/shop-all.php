<?php

require_once "connection.php"; // it ensures that connection.php correctly setup and connection.php connect a database.

// get the selected sorting option from the URL (default is "Relevance").
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'Relevance';

$sql = "
    SELECT 
        product.Product_ID, 
        product.Name AS product_name, 
        product.description, 
        product.Price, 
        product.Best_Seller, 
        category.Name AS category_name, 
        image.Image_URL 
    FROM product
    LEFT JOIN category ON product.Category_ID = category.Category_ID
    LEFT JOIN image ON product.Image_ID = image.Image_ID
    WHERE image.Is_Main_Image = 1
";
    
// Add sorting logic based on the selected option.
switch ($sort_option) {
    case 'price_low_high':
        $sql .= " ORDER BY product.Price ASC";
        break;
    case 'price_high_low':
        $sql .= " ORDER BY product.Price DESC";
        break;
    case 'best_seller':
        $sql .= " ORDER BY product.Best_Seller DESC, product.Name ASC";
        break;
    default:
        // Default sorting (Relevance) - no additional ORDER BY clause.
        break;
}
    
// Execute the query and handle errors
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
        <!-- Left-side Links -->
        <div class="nav-left">
            <a href="Mainpage.html">HOME</a>
            <a href="shop-all.html">SHOP ALL</a>
            <a href="Candles.html">CANDLES</a>
            <a href="society.html">Au-Ra SOCIETY</a>
            <a href="about.html">ABOUT US</a>
        </div>

        <div class="logo">
            <a href="Mainpage.html">
                <img src="Aura_logo.png" alt="logo"> </a>
            <span class="logo-text">AU-RA</span>
        </div>
        <div class="nav-right">
            <a href="#">SEARCH</a>
            <a href="#">ACCOUNT</a>
            <a href="#">COUNTRY ▼</a>
            <a href="#">WISHLIST</a>
            <a href="#">CART (0)</a>
        </div>
    </header>

    <!-- Shop Filters & Sorting -->
    <section class="shop-controls">
        <button class="filter-button">SHOW FILTERS ▼</button>
        <div class="sort-options">
            <form method="GET" action="">
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

    <!-- Product Grid -->
    <main class="product-grid">


        <!-- Product Grid -->
        <!-- Perfumes -->
        <?php
            while($row = mysqli_fetch_assoc($all_product)){
        ?>
        <div class="product-card">
            <?php if ($row['Best_Seller'] == 1): ?>
                <div class="label">BESTSELLER</div>
            <?php endif; ?>
                <div class="heart-icon">♡</div>
                <img src="images/<?php echo $row['Image_URL']; ?>" alt=<?php echo htmlspecialchars($row['product_name']); ?>>
                <p class="product-type"><?php echo htmlspecialchars($row['category_name']); ?></p>
                <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                <p>£<?php echo number_format($row['Price'], 2); ?></p>
                <a href="ocean-breeze.php?Product_ID=<?php echo $row['Product_ID']; ?>" class="buy-now">Buy Now</a>
        </div>
        <?php
            }
        ?>



    </main>


    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="newsletter">
                <h3>Subscribe to our Newsletter</h3>
                <p>Be the first to discover new arrivals and insider news.</p>
                <form>
                    <label for="email">Email *</label>
                    <input type="email" id="email" placeholder="Enter your email">
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
                    <p>500 Terry Francine Street<br>San Francisco, CA 94158</p>
                    <p><a href="mailto:info@mysite.com">info@mysite.com</a></p>
                    <p>123-456-7890</p>
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
            <img src="images/payment.png" alt="Payment Methods">
            <p>These payment methods are for illustrative purposes only.</p>
        </div>

        <div class="footer-bottom">
            <p>© 2035 by NOUS DEUX FRAGRANCES. Built on Wix Studio™</p>
        </div>
    </footer>
</body>
</html>