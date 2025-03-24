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
    <link rel="stylesheet" href="Mainpage.css">
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

    <section>
        <div class="slideshow-container">

            <!-- Full-width images with number and caption text -->
            <!-- Full-width images with number and caption text -->
            <div class="mySlides fade">
                <div class="video-container">
                    <video style="width: 100%;" autoplay muted loop preload="auto">
                        <source src="images/Avideo.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                    </video>
                <div class="video-overlay">
                    <h1>Welcome to AU-RA</h1>
         
                </div>
                </div>
                <div class="text">Fragrance your soul.</div>
            </div>

            <div class="mySlides fade">
                <div class="video-container">
                    <video style="width: 100%;" autoplay muted loop preload="auto">
                        <source src="images/Cvideo.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                    </video>
                <div class="video-overlay">
                    <h1>Welcome to AU-RA</h1>
         
                </div>
                </div>
                <div class="text">Fragrance your soul.</div>
            </div>
           
           <div class="mySlides fade">
                <div class="video-container">
                    <video style="width: 100%;" autoplay muted loop preload="auto">
                        <source src="images/video.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                    </video>
                <div class="video-overlay">
                    <h1>Welcome to AU-RA</h1>
         
                </div>
                </div>
                <div class="text">Fragrance your soul.</div>
            </div>
            <div class="mySlides fade">

                <img src="images/background.webp" style="width:100%">
                <div class="text">Best Perfumes.</div>
            </div>

            <!-- Next and previous buttons -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>
        <br>

        <!-- The dots/circles -->
        <div style="text-align:center">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
            <span class="dot" onclick="currentSlide(4)"></span>
        </div>
        <script defer src="Mainpage.js"></script>
    </section>

    <section>
        <div class="heading1">
            <hr>
            <h1>Popular Categories</h1>
            <hr>
        </div>
        <div class="Catagories">
            <div>
                <a href="Fruity-perfumes.php">
                    <img src="images/woody.jpg" alt="Fruity Fragrance">
                    <!-- Added an 'alt' attribute for accessibility -->
                    <h3>Fruity Fragrance</h3>
                </a>
            </div>
            <div>
                
                <a href="Scented-candles.php">
                    <img src="images/candle front page.png" alt="Scented Candles">
                    <h3>Scented Candles</h3>
                </a>
                
            </div>
            <div>
                <a href="Floral-perfumes.php">
                    <img src="images/flora.jpg" alt="Floral Fragrance">
                    <h3>Floral Fragrance</h3>
                </a>
            </div>
        </div>
        
    </section>

   <!-- Bestseller Section -->
    <section class="bestseller-section">
        <div class="heading1">
            <hr class="f">
            <h1>Bestsellers</h1>
            <hr>
        </div>
        <div class="bestseller-grid">
            <div class="product">
                <a href="product.php?Product_ID=8">

                    <img src="images/spicy bottle.png" alt="oriental vanilla Perfume">
                    <p class="product-type">Perfume Oil</p>
                    <h3>Dushkaven</h3>
                    <p>£119.99</p>
                </a>
            </div>
            <div class="product">
                <a href="product.php?Product_ID=5">
                    <img src="images/ocean bottle.png" alt="Ocean breeze">
                    <p class="product-type">Eau De Parfum</p>
                    <h3>Ayura Bloom</h3>
                    <p>£99.99</p>
            </a>
                </div>
            <div class="product">
            <a href="product.php?Product_ID=7">

                <img src="images/green bottle.png" alt="Herbal Green">
                <p class="product-type">Perfume Oil</p>
                <h3>Radwens Heaven</h3>
                <p>From £109.99</p>
            </a>
                </div>
        </div>
    </section>
    <!-- From the Blog Section -->
    <section class="blog-section">
        <div class="heading1">
            <hr class="f">
            <h1>From the Blog</h1>
            <hr>
        </div>
        <div class="blog-grid">
            <div class="blog-post">
            	<a href="blog1.php">
                <img src="images/blog11.jpg" alt="Fragrance Oils">
                <h3>Fragrance Oils vs. Essential Oils: What is the Difference?</h3>
            	</a>
            </div>
            <div class="blog-post">
				<a href="blog2.php">
                <img src="images/blog222.png" alt="Can Perfume Go Off?">
                <h3>Can Perfume Go Off?</h3>
            	</a>
            </div>
            
            <div class="blog-post">
            <a href="blog3.php">
                <img src="images/blog3.png" alt="How is Perfume Made?">
                <h3>How is Perfume Made?</h3>
            </a>
            </div>
        </div>
    </section>

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