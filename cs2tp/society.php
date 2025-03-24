<?php
session_start();
include 'connection.php'; // This file must define $conn

// Enable error reporting for debugging
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
  <link rel="stylesheet" href="society.css">
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

  <!-- Society Intro Section -->
  <section class="society-intro">
    <div class="overlay">
      <h1>AURA SOCIETY</h1>
      <p>
        Join now and earn points with every purchase, unlock exclusive offers, and gain access to premium scents and
        experiences. Redeem your points for discounts and special rewards, and indulge in the ultimate fragrance luxury.
      </p>
      <div class="buttons">
        <?php if (isset($_SESSION['User_ID'])): ?>
          <!-- If logged in, show an alert on click -->
          <a href="javascript:void(0);" onclick="alert('You are already a member');">
            <button class="join-btn">JOIN NOW</button>
          </a>
          <a href="javascript:void(0);" onclick="alert('You are already a member');">
            <button class="login-btn">LOG IN</button>
          </a>
        <?php else: ?>
          <!-- If not logged in, send to Signup and Login pages -->
          <a href="Signup.php">
            <button class="join-btn">JOIN NOW</button>
          </a>
          <a href="Login.php">
            <button class="login-btn">LOG IN</button>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Rewards Section -->
  <section class="rewards">
    <h2>REWARDS</h2>
    <div class="reward-options">
      <div class="reward">
        <img src="images/voucher.png" alt="£5 Voucher">
        <p>£5 VOUCHER</p>
        <span>500 points</span>
      </div>
      <div class="reward">
        <img src="images/voucher.png" alt="£10 Voucher">
        <p>£10 VOUCHER</p>
        <span>1,000 points</span>
      </div>
      <div class="reward">
        <img src="images/voucher.png" alt="£15 Voucher">
        <p>£15 VOUCHER</p>
        <span>1,500 points</span>
      </div>
    </div>
  </section>

  <!-- Tiers Section -->
  <section class="tiers">
    <h2>TIERS</h2>
    <div class="tier-levels">
      <div class="tier silver">
        <img src="images/silver.webp" alt="Silver Tier">
        <h3>Silver</h3>
        <p>Start here</p>
        <p>5 points per £1</p>
        <p>Early Access to Sales & Promotions</p>
        <p>Free Samples With Every Order</p>
      </div>
      <div class="tier gold">
        <img src="images/gold.webp" alt="Gold Tier">
        <h3>Gold</h3>
        <p>Spend £100</p>
        <p>7 points per £1</p>
        <p>Exclusive Birthday Gift</p>
        <p>Free Samples With Every Order</p>
      </div>
      <div class="tier black">
        <img src="images/diamond.webp" alt="Black Tier">
        <h3>Black</h3>
        <p>Spend £400</p>
        <p>10 points per £1</p>
        <p>Access to VIP Only Earning Events</p>
        <p>Free Samples With Every Order</p>
      </div>
    </div>
  </section>

  <!-- How It Works Section -->
  <section class="how-it-works">
    <h2>HOW IT WORKS</h2>
    <div class="steps">
      <div class="step">
        <img src="images/join.png" alt="Join">
        <h3>STEP 1</h3>
        <p>JOIN</p>
        <p>Sign up and get your first 300 points</p>
      </div>
      <div class="step">
        <img src="images/points.png" alt="Earn Points">
        <h3>STEP 2</h3>
        <p>EARN POINTS</p>
        <p>Earn points with every purchase and by completing special actions</p>
      </div>
      <div class="step">
        <img src="images/rewards.png" alt="Redeem Rewards">
        <h3>STEP 3</h3>
        <p>REDEEM REWARDS</p>
        <p>Use your points for rewards like discounts, exclusive gifts, and more</p>
      </div>
    </div>
  </section>

  <!-- FAQ Section -->
  <section class="faq">
    <h2>FREQUENTLY ASKED QUESTIONS</h2>
    <div class="accordion">
      <div class="accordion-item">
        <button class="accordion-button">What is the Aura Society? <span class="plus-minus">+</span></button>
        <div class="accordion-content">
          <p>The Aura Society is our exclusive rewards program for loyal customers.</p>
        </div>
      </div>
      <div class="accordion-item">
        <button class="accordion-button">Who can join? <span class="plus-minus">+</span></button>
        <div class="accordion-content">
          <p>Anyone can join the AU-RA Society. Simply create an account to start earning points and accessing exclusive benefits.</p>
        </div>
      </div>
      <div class="accordion-item">
        <button class="accordion-button">How do I earn points? <span class="plus-minus">+</span></button>
        <div class="accordion-content">
          <p>Earn points with every purchase you make, and through special promotions or activities available to AU-RA Society members.</p>
        </div>
      </div>
      <div class="accordion-item">
        <button class="accordion-button">How do I view my point balance? <span class="plus-minus">+</span></button>
        <div class="accordion-content">
          <p>You can view your point balance by logging into your account and navigating to the 'Rewards' section.</p>
        </div>
      </div>
      <div class="accordion-item">
        <button class="accordion-button">How do I refer a friend? <span class="plus-minus">+</span></button>
        <div class="accordion-content">
          <p>Share your unique referral link found in your account. You’ll earn points when your friends make a qualifying purchase.</p>
        </div>
      </div>
      <div class="accordion-item">
        <button class="accordion-button">How do I redeem my points? <span class="plus-minus">+</span></button>
        <div class="accordion-content">
          <p>Visit the Rewards section in your account to view available rewards and redeem points for discounts or vouchers.</p>
        </div>
      </div>
      <div class="accordion-item">
        <button class="accordion-button">Is there a limit to the number of points I can earn? <span class="plus-minus">+</span></button>
        <div class="accordion-content">
          <p>There is no limit to how many points you can earn. The more you shop and engage, the more points you'll accumulate.</p>
        </div>
      </div>
      <div class="accordion-item">
        <button class="accordion-button">What do the ‘approved’, ‘pending’ and ‘cancelled’ statuses mean? <span class="plus-minus">+</span></button>
        <div class="accordion-content">
          <p>‘Approved’ points are those available for redemption. ‘Pending’ points are awaiting approval, and ‘Cancelled’ points were voided.</p>
        </div>
      </div>
      <div class="accordion-item">
        <button class="accordion-button">I completed an activity but didn’t earn points! <span class="plus-minus">+</span></button>
        <div class="accordion-content">
          <p>If you completed an activity but didn’t earn points, please allow a few days for processing. Contact support if the issue persists.</p>
        </div>
      </div>
      <div class="accordion-item">
        <button class="accordion-button">Do my points expire? <span class="plus-minus">+</span></button>
        <div class="accordion-content">
          <p>Points expire after 12 months of inactivity. Keep shopping and engaging to retain your points and benefits.</p>
        </div>
      </div>
      <div class="accordion-item">
        <button class="accordion-button">Where can I report a problem or give feedback? <span class="plus-minus">+</span></button>
        <div class="accordion-content">
          <p>We value your feedback! Contact our support team via email or phone, available in the 'Contact Us' section of our website.</p>
        </div>
      </div>
    </div>
  </section>

  <script>
    document.querySelectorAll('.accordion-button').forEach(button => {
      button.addEventListener('click', () => {
        const content = button.nextElementSibling;
        const plusMinus = button.querySelector('.plus-minus');

        if (content.style.display === 'block') {
          content.style.display = 'none';
          plusMinus.textContent = '+';
        } else {
          document.querySelectorAll('.accordion-content').forEach(c => c.style.display = 'none');
          document.querySelectorAll('.plus-minus').forEach(p => p.textContent = '+');

          content.style.display = 'block';
          plusMinus.textContent = '−';
        }
      });
    });
  </script>

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
