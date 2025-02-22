<?php
session_start();
require_once "connection.php"; // Ensure this file sets up $conn (mysqli object)
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
// Get the Product_ID from the URL and sanitize it
$product_id = isset($_GET['Product_ID']) ? intval($_GET['Product_ID']) : 0;
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if ($product_id <= 0) {
    header("Location: error-page.php"); // Redirect if Product_ID is invalid
    exit;
}

// Fetch product details
$product_sql = "
    SELECT 
        p.Product_ID, 
        p.Name AS product_name, 
        p.description, 
        p.Price, 
        p.Best_Seller, 
        c.Name AS category_name, 
        i.Image_URL
    FROM product p
    LEFT JOIN category c ON p.Category_ID = c.Category_ID
    LEFT JOIN image i ON p.Image_ID = i.Image_ID
    WHERE p.Product_ID = ?
";

$product_stmt = $conn->prepare($product_sql);
if (!$product_stmt) {
    die("Error preparing product query: " . $conn->error);
}

$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

// Check if the product exists
if ($product_result->num_rows > 0) {
    $product = $product_result->fetch_assoc();
} else {
    header("Location: error-page.php"); // Redirect if product not found
    exit;
}

// Fetch product notes
$notes_sql = "
    SELECT 
        nl.Note_Name, 
        nl.Note_Image, 
        nl.Note_Text
    FROM product_notes pn
    LEFT JOIN notes_library nl ON pn.Note_ID = nl.Note_ID
    WHERE pn.Product_ID = ?
";

$notes_stmt = $conn->prepare($notes_sql);
if (!$notes_stmt) {
    die("Error preparing notes query: " . $conn->error);
}

$notes_stmt->bind_param("i", $product_id);
$notes_stmt->execute();
$notes_result = $notes_stmt->get_result();
$notes = $notes_result->fetch_all(MYSQLI_ASSOC);

// Prepare arrays to categorize the notes
$top_notes = [];
$heart_notes = [];
$base_notes = [];

// Categorize notes based on the Note_Type
while ($note = $notes_result->fetch_assoc()) {
    if ($note['Note_Type'] == 'Top') {
        $top_notes[] = $note;
    } elseif ($note['Note_Type'] == 'Heart') {
        $heart_notes[] = $note;
    } elseif ($note['Note_Type'] == 'Base') {
        $base_notes[] = $note;
    }
}

// Fetch product reviews
$reviews_sql = "
    SELECT 
        u.First_Name, 
        u.Last_Name, 
        r.Rating, 
        r.Review_Text 
    FROM 
        review r
    JOIN 
        users u ON r.User_ID = u.User_ID
    WHERE 
        r.Product_ID = ?
";

$reviews_stmt = $conn->prepare($reviews_sql);
if (!$reviews_stmt) {
    die("Error preparing reviews query: " . $conn->error);
}

$reviews_stmt->bind_param("i", $product_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();
$reviews = $reviews_result->fetch_all(MYSQLI_ASSOC);

// Debugging (only during development; remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//<!-- <div class="notes">
  //                  <h3>All Notes</h3>-->
//
  //                  <!-- Display Top Notes 
    //                <h4>Top Notes</h4>-->
      //              <?php foreach ($top_notes as $note): 
        //                <p><strong>Top:</strong> <?php echo htmlspecialchars($note['Note_Name']); </p>
          //          <?php endforeach; 

            //        <!-- Display Heart Notes -->
                   // <h4>Heart Notes</h4>
             //       <?php foreach ($heart_notes as $note): 
               //         <p><strong>Heart:</strong> <?php echo htmlspecialchars($note['Note_Name']); </p>
                 //   <?php endforeach; 

                   // <!-- Display Base Notes -->
                //    <h4>Base Notes</h4>
                //    <?php foreach ($base_notes as $note): 
                  //      <p><strong>Base:</strong> <?php echo htmlspecialchars($note['Note_Name']); </p>
                  //  <?php endforeach; 
               // </div>-->

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
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $stmt = $conn->prepare("UPDATE cart_items SET Quantity = Quantity + 1 WHERE Cart_ID = ? AND Product_ID = ?");
        } else {
            $stmt = $conn->prepare("INSERT INTO cart_items (Cart_ID, Product_ID, Quantity) VALUES (?, ?, 1)");
        }
        $stmt->bind_param("ii", $cart_id, $product_id);
        $stmt->execute();

        
        header("Location: product.php?Product_ID=" . $product_id);
        exit;
    }
}
            
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="product.css">

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
				<form method="GET" action="shop-all.php" class="search-form">
            <input type="text" name="query" placeholder="Search for products..." class="search-input">
            <button type="submit">Search</button>
        </form>
            
                    <a href="Login.php">ACCOUNT</a>
                    <a href="contact-us.php">CONTACT-US</a>
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
    <main class="product-detail">
        <div class="breadcrumb">Home > <?php echo htmlspecialchars($product['product_name']);?></div>

        <div class="product-container">
            <div class="product-image">
                <img src="images/<?php echo $product['Image_URL']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
            </div>

            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                <div class="review-wishlist">
                    <div class="review">
                        <span class="stars">★★★★★</span>
                        <a href="#reviews"> reviews</a>
                    </div>
                    <div class="wishlist">
                        <a href="#wishlist" class="wishlist-link">♡ Wishlist</a>
                    </div>
                </div>

                <p class="price">£<?php echo number_format($product['Price'], 2); ?></p>
                <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>

                <div class="fragrance-family">
                    <h3>Fragrance Family</h3>
                    <p><?php echo htmlspecialchars($product['category_name']); ?></p>
                </div>

               



                <div class="gender">
                    <h3>Gender</h3>
                    <p>Unisex</p>
                </div>

                

                <form method="POST" action="">
                        <input type="hidden" name="product_id" value="<?php echo $product['Product_ID']; ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                </form>
                <div class="payment-options">
                    <img src="images/shoppay.jpg" alt="Shop Pay" class="payment-image">
                    <img src="images/klarna.jpg" alt="Klarna" class="payment-image">
                    <img src="images/clearpay.jpg" alt="Clearpay" class="payment-image">
                </div>

                <p class="installment-info">3 interest-free payments of £<?php echo number_format($product['Price'] / 3, 2); ?> with Klarna. No fees.</p>
                <p class="installment-info">3 interest-free payments of £<?php echo number_format($product['Price'] / 3, 2); ?> with Clearpay. No fees.</p>

                <div class="pairing-section">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<!-- Notes -->
    
    <section class="tasting-notes">
        <h2>Tasting Notes</h2>
        <div class="tasting-notes-content">
            <?php foreach ($notes as $note): ?>
                <div class="note">
                    <img src="images/<?php echo htmlspecialchars($note['Note_Image']); ?>" alt="<?php echo htmlspecialchars($note['Note_Name']); ?>">
                    <div class="note-info">
                        <h3><?php echo htmlspecialchars($note['Note_Name']); ?></h3>
                        <p><?php echo htmlspecialchars($note['Note_Text']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="faq-section">
        <h2>Frequently Asked Questions</h2>
        <div class="accordion">
            <div class="accordion-item">
                <button class="accordion-button">How to apply this fragrance? <span class="plus-minus">+</span></button>
                <div class="accordion-content">
                    <p>Apply to pulse points like the wrists, neck, and behind the ears for a long-lasting scent.</p>
                </div>
            </div>

            <div class="accordion-item">
                <button class="accordion-button">How should I store my fragrances? <span
                        class="plus-minus">+</span></button>
                <div class="accordion-content">
                    <p>Store in a cool, dry place away from direct sunlight to preserve the fragrance quality.</p>
                </div>
            </div>

            <div class="accordion-item">
                <button class="accordion-button">What can I layer this fragrance with? <span
                        class="plus-minus">+</span></button>
                <div class="accordion-content">
                    <p>Layer with complementary scents like amber or musk to enhance depth and warmth.</p>
                </div>
            </div>

            <div class="accordion-item">
                <button class="accordion-button">Shipping <span class="plus-minus">+</span></button>
                <div class="accordion-content">
                    <p>We offer standard and expedited shipping options. See our shipping policy for details.</p>
                </div>
            </div>

            <div class="accordion-item">
                <button class="accordion-button">Returns <span class="plus-minus">+</span></button>
                <div class="accordion-content">
                    <p>Returns are accepted within 30 days of purchase with proof of receipt. Conditions apply.</p>
                </div>
            </div>

            <div class="accordion-item">
                <button class="accordion-button">Ingredients <span class="plus-minus">+</span></button>
                <div class="accordion-content">
                    <p>This fragrance contains natural essential oils and high-quality synthetic compounds.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript for Accordion -->
    <script>
        document.querySelectorAll('.accordion-button').forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                const plusMinus = button.querySelector('.plus-minus');

                if (content.style.maxHeight) {
                    content.style.maxHeight = null;
                    plusMinus.textContent = '+';
                } else {
                    document.querySelectorAll('.accordion-content').forEach(c => c.style.maxHeight = null);
                    document.querySelectorAll('.plus-minus').forEach(p => p.textContent = '+');

                    content.style.maxHeight = content.scrollHeight + 'px';
                    plusMinus.textContent = '−';
                }
            });
        });
    </script>
    <!-- Reviews Section -->
    <section class="reviews-section">
    <h2>Customer Reviews</h2>
    <div class="reviews">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <h3><?php echo htmlspecialchars($review['First_Name'] . ' ' . $review['Last_Name']); ?></h3>
                    <p>
                        <?php
                        // Render star ratings based on the rating value
                        $rating = intval($review['Rating']);
                        echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                        ?>
                    </p>
                    <p>“<?php echo htmlspecialchars($review['Review_Text']); ?>”</p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet. Be the first to review this product!</p>
        <?php endif; ?>
    </div>
</section>

    </section>
    
    <!-- Products You May Also Like Section -->
    <section class="related-products">
        <p class="subtitle">Why not also try</p>
        <h2 class="related-products-heading">Products You May Also Like</h2>
        <div class="related-products-grid">
            <div class="product">
                <img src="images/5.jpg" alt="Floral symphony">
                <p class="product-type">Eau De Parfum Combo</p>
                <h3>Lirien</h3>
                <p>£109.99</p>
               
            </div>
            <div class="product">
                <img src="images/4.jpg" alt="Ocean breeze">
                <p class="product-type">Perfume Oil</p>
                <h3>Listrewood</h3>
                <p>From £119.99.00</p>
                
            </div>
            <div class="product">
                <img src="images/1.jpg" alt="Citrus Zest">
                <p class="product-type">Eau De Parfum</p>
                <h3>Rayun</h3>
                <p>£109.99</p>
              
            </div>
            <div class="product">
                <img src="images/2.jpg" alt="Gourmand Caramel
            ">
                <p class="product-type">Eau De Parfum</p>
                <h3>Mosharra Essence</h3>
                <p>£109.99</p>
                <p>1 size</p>
            </div>
        </div>
    </section>





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
                    <p>500 Terry Francine Street<br>San Francisco, CA 94158</p>
                    <p><a href="mailto:info@mysite.com">info@mysite.com</a></p>
                    <p>123-456-7890</p>
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


        <div class="payment-methods">
            <p>Pay Securely with</p>
            <img src="images/payment.png" alt="Payment Methods">
            <p>These payment methods are for illustrative purposes only.</p>
        </div>

        <div class="footer-bottom">
            <p>©2024 AU-RA. All rights reserved.™</p>
        </div>
    </footer>
</body>
</html>