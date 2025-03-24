<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - AU-RA</title>
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

<section class="about-section"> 
    <div class="container">
        <!-- Our Story Section -->
       <h1>Our Story</h1>
            <img src="images/story1.webp" alt="Our Story Image" class="section-image story-image">
            <p>Welcome to Au-Ra, a dynamic community united by our desire for fragrances and oils. Our passionate team
                is committed to exploring the captivating world of oils and scents. We believe that scent is a powerful
                form of self-expression and present a hand-picked collection that reflects innovation and uniqueness.
            </p>
            <p>Our fragrances are more than just a product. We are dedicated to quality and sustainability, sourcing
                ingredients responsibly and creating products that are not only appealing to our senses but also
                friendly to the environment. Join our journey as we delve into the enticing world of oils and scents,
                assisting you in finding the perfect aroma that fits your unique style.</p>


            <!-- Our Commitment Section -->
            <h2>Our Commitment</h2>
            <img src="images/forest.jpg" alt="Our Commitment Image" class="section-image">
            <p>At Au-Ra, we recognize the importance of preserving the planet for future generations. That’s why we are
                proud to share our commitment to sustainability. For every perfume purchased, we plant a tree through
                our global reforestation initiative. This isn’t just a gesture; it’s a pledge to offset our
                environmental impact and contribute to a greener tomorrow.</p>
            <p>We also continuously work to minimize our carbon footprint by using eco-friendly packaging materials and
                supporting local communities in our sourcing regions. Every step we take is rooted in a deep respect for
                nature and a vision of a more sustainable future.</p>

            <!-- Our Promise Section -->
            <h2>Our Promise</h2>
            <img src="images/promise.png" alt="Our Promise Image" class="section-image">
            <p>We promise that every product bearing the Au-Ra name is crafted with care, precision, and an unwavering
                commitment to quality. Our perfumers meticulously select the finest ingredients, ensuring that each
                fragrance offers an unparalleled sensory experience. Whether it’s the rich, smoky allure of oud or the
                refreshing zest of citrus oils, every note tells a story of elegance and craftsmanship.</p>
            <p>Ethical sourcing is at the heart of what we do. From sustainable harvesting of oud to partnering with
                small-scale farmers for our essential oils, we ensure that every ingredient is sourced responsibly. This
                means no harm to the environment and fair compensation for the communities we work with. When you choose
                Au-Ra, you’re choosing luxury with a conscience.</p>

            <!-- Team Section -->
           <h2>Our Team</h2>
<ul class="team-list">
 <li>
        <img src="images/gents.png" alt="Ayan Khan">
        <span>Ayan Khan</span>
        <p>Inspiring Rayun</p>
    </li>
   <li>
        <img src="images/women.png" alt="Trisha Sifa">
        <span>Trisha Sifa</span>
        <p>Inspiring Tresor Lush</p>
    </li>
    <li>
        <img src="images/gents.png" alt="Radhwan Imad">
        <span>Radhwan Imad</span>
        <p>Inspiring Radwen's Haven</p>
    </li>
    <li>
        <img src="images/gents.png" alt="Daniel Long So">
        <span>Daniel Long So</span>
        <p>Inspiring Lirien</p>
    </li>
<li>
        <img src="images/women.png" alt="Priscilla Addai Asamoah">
        <span>Priscilla Addai Asamoah</span>
        <p>Inspiring Pristelle</p>
    </li>
 <li>
        <img src="images/gents.png" alt="Dhruhil Gajera">
        <span>Dhruhil Gajera</span>
        <p>Inspiring Duskhaven</p>
    </li>
 <li>
        <img src="images/gents.png" alt="Seleim Ali">
        <span>Seleim Ali</span>
        <p>Inspiring Lustrewood</p>
    </li>

    <li>
        <img src="images/gents.png" alt="Yuvraj Sandhu">
        <span>Yuvraj Sandhu</span>
        <p>Inspiring Ayura Bloom</p>
    </li>
   
    
    <li>
        <img src="images/gents.png" alt="Mohammed Khan">
        <span>Mohammed Khan</span>
        <p>Inspiring Mosharra Essence</p>
    </li>
    
</ul>
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