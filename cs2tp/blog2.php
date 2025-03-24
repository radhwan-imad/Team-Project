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
    <h1 class="heading1">Can Perfume Go Off? Here’s What You Need to Know</h1>
    <br>
    <hr>
    
        
    

    <!-- Blog Image -->
    <div class="section-image story-image">
        <img src="images/blog22.jpg" alt="Perfume Image">
    </div>

    <div class="blog-text">
        <p>
            A perfume is a luxury that gives you a distinctive look and is an investment in your own sense of style. You may question, though, if perfume can go bad like any other product. Does it grow unpleasant to wear or lose its scent with time? Yes, perfume can deteriorate and change over time if improperly stored, but there are a few things to know about how and why this occurs.
        </p>
        <h2>Can Perfume Expire?</h2>
        <p>
            There is a possibility that perfume will eventually "go off." However, unlike food products, perfumes do not have an official expiration date. Perfume quality might deteriorate over time, but it usually happens gradually. Depending on the components and how properly the perfume is stored, certain scents may persist for years while others may alter more quickly.
        </p>

        <h2>Signs Your Perfume Might Have Gone Off</h2>
        <p>
        Here are some signs that your perfume might have gone off or expired:</p>
            <ul>
                <li><strong>Fragrance Change:</strong> Your perfume may have gone off if it doesn't smell the same as it did when you originally bought it. Degradation is indicated by a change in fragrance, particularly if it becomes sour, flat, or metallic.</li>
                <li><strong>Layer Separation:</strong> If your perfume has separated into distinct layers, it may indicate that the oils are beginning to degrade. This might occur if the scent was exposed to sunshine or extremely high or low temperatures.</li>
                <li><strong>Less Sillage or Longevity:</strong> Your perfume may have lost its effectiveness over time if it doesn't project as well or lasts as long as it once did.</li>
            </ul>
        

        <h2>What Causes Perfume to Go Off?</h2>
        <p>
        Here are the main factors:</p>
            <ul>
                <li><strong>Exposure to Air:</strong> Oxidation, which happens when perfume is exposed to air, can alter the fragrance's chemical makeup. When not in use, always make sure your perfume bottle is securely closed.</li>
                <li><strong>Light Exposure:</strong> Your perfume may fade as a result of sunlight breaking down its oils. Because of this, it's best to keep your perfume somewhere cool and dark.</li>
                <li><strong>Heat:</strong> Perfume's essential oils may degrade more quickly in hot weather. Perfume deterioration can be accelerated by keeping it in a warm location, such as a restroom or close to a heat source.</li>
            </ul>
        

        <h2>How to Make Your Perfume Last Longer</h2>
        <p>Here are some suggestions to help you keep your perfume fresh and avoid it expiring too soon:</p>
        <ul>
            <li><strong>Proper Storage:</strong> Keep your perfume out of direct sunlight and off of heat sources. A closet, drawer, or even a box might serve as a perfect storage space.</li>
            <li><strong>Keep the Cap Tight:</strong> To avoid oxidation, which can occur when air enters the container, make sure the cap is always shut snugly.</li>
            <li><strong>Avoid Decanting:</strong> Perfume can become contaminated by air if it is constantly moved from bottle to bottle. Ideally, it should be stored in its original bottle.</li>
        </ul>

        <h2>How Long Does Perfume Last?</h2>
        <p>
            Depending on their contents, perfumes can last anywhere from three to five years, and some can stay even longer. The general rule is that a fragrance's shelf life decreases with increasing naturalness. High essential oil concentration perfumes may have a longer shelf life than synthetic fragrances.
        </p>
        <p>
            You can determine the age of your perfume by smelling it, which is the greatest method to determine its condition. It's time to say goodbye if it smells bad or has altered significantly.
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
