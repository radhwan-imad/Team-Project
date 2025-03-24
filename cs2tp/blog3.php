<?php
session_start();
include 'connection.php'; // Ensure this file sets up $conn

// Enable error reporting for development (remove or comment out on production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="blog.css">
</head>

<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        BLACK FRIDAY IS HERE! UP TO 50% OFF PLUS MANY COMBINATION DISCOUNTS
    </div>

      <!-- Main Navigation -->
    <header class="navbar">
        <div class="nav-left">
            <a href="Mainpage.php">HOME</a>
            <a href="shop-all.php">SHOP ALL</a>
            <a href="society.php">Au-Ra SOCIETY</a>
            <a href="about.php">ABOUT US</a>
        </div>

        <div class="logo">
            <a href="Mainpage.php">
                <img src="Aura_logo.png" alt="logo">
                <span class="logo-text">AU-RA<br>Fragrance your soul</span>
            </a>
        </div>

        <div class="nav-right">
            <form method="GET" action="shop-all.php" class="search-form">
                <input type="text" name="query" placeholder="Search for products..." class="search-input">
                <button type="submit">Search</button>
            </form>

            <!-- User Account / Welcome -->
            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                <a href="logged-in.php">Welcome, <?php echo htmlspecialchars($_SESSION['User_Name']); ?></a>
               
            <?php else: ?>
                <a href="logged-in.php">ACCOUNT</a>
            <?php endif; ?>

          
            
            <!-- Wishlist Link -->
            <a href="wishlist.php">WISHLIST (
            <?php 
            if (isset($_SESSION['User_ID'])) {
                $user_id = $_SESSION['User_ID'];
                if ($stmt = $conn->prepare("SELECT COUNT(*) as wishlist_count FROM wishlist WHERE User_ID = ?")) {
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $stmt->bind_result($wishlist_count);
                    $stmt->fetch();
                    echo $wishlist_count;
                    $stmt->close();
                } else {
                    echo 0;
                }
            } else {
                echo 0;
            }
            ?>
            )</a>
            
         
           <a href="cart.php">CART (
<?php 
if (isset($_SESSION['User_ID'])) {
    $user_id = $_SESSION['User_ID'];
    
    // Fetch the Cart_ID for the logged-in user
    if ($stmt = $conn->prepare("SELECT Cart_ID FROM cart WHERE User_ID = ?")) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result_cart = $stmt->get_result();
        if ($row_cart = $result_cart->fetch_assoc()) {
            $cart_id = $row_cart['Cart_ID'];
            $stmt->close();

            // Get the total quantity in the cart
            if ($stmt = $conn->prepare("SELECT SUM(Quantity) as total_quantity FROM cart_items WHERE Cart_ID = ?")) {
                $stmt->bind_param("i", $cart_id);
                $stmt->execute();
                $stmt->bind_result($total_quantity);
                $stmt->fetch();
                echo $total_quantity ?: 0;
                $stmt->close();
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    } else {
        echo 0;
    }
} else {
    // If not logged in, fall back to session cart count
    echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
}
?>)
</a>
           
             <a href="contact-us.php">CONTACT-US</a>
        </div>
    </header>

   <!-- Blog Content Section -->
   <div class="blog-content">
    <h1 class="heading1">How is Perfume Made? The Art and Science Behind the Fragrance</h1>
    <hr>
    
    
    <div class="section-image story-image">
        <img src="images/story.jpg" alt="Perfume Making Process">
    </div>

    <div class="blog-text">
        <p>
            Perfume is more than just a scent; it’s an experience, a story told through layers of complex notes and carefully chosen ingredients. But have you ever wondered how perfume is made? From the raw materials to the final spritz, creating a fragrance is an intricate process that combines artistry with science. Below are 5 key steps to making a perfume:
        </p>

        <h2>Step 1: Selecting Ingredients</h2>
        <p>
            Both natural and manufactured elements, such as flowers, fruits, and spices, are used to make perfumes. Synthetic components aid in the creation of distinctive fragrances, whereas natural extracts are acquired by distillation, cold pressing, or solvent extraction.
        </p>

        <h2>Step 2: Blending the Notes</h2>
        <p>
            Three layers make up a perfume:
        </p>
        <ul>
            <li><strong>Top Notes:</strong> The first, milder aroma (citrus, herbs, etc.).</li>
            <li><strong>Heart Notes:</strong> The fragrance's central notes, such as flowery and spicy.</li>
            <li><strong>Base Notes:</strong> The lingering base, such as musk or wood.</li>
        </ul>
        <p>
            A harmonizing scent that changes with time is produced by blending these elements.
        </p>

        <h2>Step 3: Extraction of Oils</h2>
        <p>
            The following techniques are used to extract the natural oils:
        </p>
        <ul>
            <li><strong>Distillation:</strong> Using steam to extract plant oils.</li>
            <li><strong>Cold Pressing:</strong> For citrus fruits, cold pressing is used.</li>
            <li><strong>Solvent Extraction:</strong> For fragile flowers like jasmine, use solvent extraction.</li>
        </ul>
        <p>
            The scent is then created by combining these oils with carrier oils or alcohol.
        </p>

        <h2>Step 4: Aging the Perfume</h2>
        <p>
            The perfume's depth and smoothness are enhanced by aging it after the ingredients have had time to settle and mingle. This aging process can take weeks or months to occur.
        </p>

        <h2>Step 5: Bottling and Testing</h2>
        <p>
            After the perfume is perfected, it is put into bottles and put through quality, consistency, and packaging tests. After this, the fragrance is prepared for sale, giving customers a finished item that exemplifies originality and skill.
        </p>

        <p>
            The process of creating a perfume involves both art and science, from selecting the ideal materials to crafting the finished scent. Every stage is essential to producing a fragrance that is both opulent and distinctive. Ready to find your signature scent? Explore our collection and experience the magic of fragrance.
        </p>
    </div>
</div>

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
                        <li><a href="shop-all.php">Shop All</a></li>
                        <li><a href="#">Body</a></li>
                        <li><a href="shop-all.php">Home Scents</a></li>
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
                        <li><a href="https://www.tiktok.com/">TikTok</a></li>
                        <li><a href="https://www.instagram.com/">Instagram</a></li>
                        <li><a href="https://www.facebook.com/">Facebook</a></li>
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
            <p>2024 AU-RA. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>
