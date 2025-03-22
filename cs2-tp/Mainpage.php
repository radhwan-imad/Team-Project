<?php
session_start();
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

      

        <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
            <a href="Logout.php">Logout</a>
        <?php else: ?>
            <a href="logged-in.php">ACCOUNT</a>
        <?php endif; ?>

        <a href="contact-us.php">CONTACT-US</a>
        <a href="cart.php">CART (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
    </div>
</header>

    <section>
        <div class="slideshow-container">

            <!-- Full-width images with number and caption text -->
            <!-- Full-width images with number and caption text -->
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

                <img src="images/7.png" style="width:100%">
                <div class="text">Burn an AU-RA candels and Fragrance your surrounding.</div>
            </div>

           
            <div class="mySlides fade">

                <img src="images/background.webp" style="width:100%">
                <div class="text">Best Perfumes.</div>
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
                <a href="oriental-vanilla.html">

                    <img src="images/spicy bottle.png" alt="oriental vanilla Perfume">
                    <p class="product-type">Perfume Oil</p>
                    <h3>Dushkaven</h3>
                    <p>£119.99</p>
                </a>
            </div>
            <div class="product">
                <a href="ocean-breeze.html">
                    <img src="images/ocean bottle.png" alt="Ocean breeze">
                    <p class="product-type">Eau De Parfum</p>
                    <h3>Ayura Bloom</h3>
                    <p>£99.99</p>
            </a>
                </div>
            <div class="product">
            <a href="radwens-haven.html">

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
            	<a href="blog1.html">
                <img src="images/blog11.jpg" alt="Fragrance Oils">
                <h3>Fragrance Oils vs. Essential Oils: What is the Difference?</h3>
            	</a>
            </div>
            <div class="blog-post">
				<a href="blog2.html">
                <img src="images/blog222.png" alt="Can Perfume Go Off?">
                <h3>Can Perfume Go Off?</h3>
            	</a>
            </div>
            
            <div class="blog-post">
            <a href="blog3.html">
                <img src="images/blog3.png" alt="How is Perfume Made?">
                <h3>How is Perfume Made?</h3>
            </a>
            </div>
        </div>
    </section>


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