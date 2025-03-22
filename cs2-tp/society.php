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
<link rel="stylesheet" href="society.css">
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
            <a href="Mainpage.php">HOME</a>
            <a href="shop-all.php">SHOP ALL</a>
            <a href="society.php">Au-Ra SOCIETY</a>
            <a href="about.php">ABOUT US</a>
        </div>

        <!-- Centered Logo -->
        <div class="logo">
            <a href="Mainpage.html">
                <img src="Aura_logo.png" alt="logo"> 
            <span class="logo-text">AU-RA<br>Fragrance your soul</span>
            </a>
    </div>

        <div class="nav-right">
            <!-- Collapsible Search Bar -->
                    <form method="GET" action="search.php" class="search-form">
                        <input
                            type="text"
                            name="query"
                            placeholder="Search for products..."
                            class="search-input"
                        >
                        <button type="submit">Search</button>
        </form>
        <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
            <a href="Logout.php">Logout</a>
        <?php else: ?>
            <a href="logged-in.php">ACCOUNT</a>
        <?php endif; ?>
                    <a href="contact-us.php">CONTACT-US</a>
                    <a href="cart.php">CART (0)</a>
                </div>
        
</header>
    

  

    

        <!-- Header (navbar) here -->

        <section class="society-intro">
            <div class="overlay">
                <h1>AURA SOCIETY</h1>
                <p>Join now and earn points with every purchase, unlock exclusive offers, and gain access to premium
                    scents and experiences. Redeem your points for discounts and special rewards, and indulge in the
                    ultimate fragrance luxury.</p>
              <div class="buttons">
            <a href="Signup.php"><button class="join-btn">JOIN NOW</button></a>
            <a href="Login.php"><button class="login-btn">LOG IN</button></a>
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
                    <button class="accordion-button">What is the Aura Society? <span
                            class="plus-minus">+</span></button>
                    <div class="accordion-content">
                        <p>The Aura Society is our exclusive rewards program for loyal customers.</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-button">Who can join? <span class="plus-minus">+</span></button>
                    <div class="accordion-content">
                        <p>Anyone can join the AU-RA Society. Simply create an account to start earning points and
                            accessing exclusive benefits.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">How do I earn points? <span class="plus-minus">+</span></button>
                    <div class="accordion-content">
                        <p>Earn points with every purchase you make, and through special promotions or activities
                            available to AU-RA Society members.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">How do I view my point balance? <span
                            class="plus-minus">+</span></button>
                    <div class="accordion-content">
                        <p>You can view your point balance by logging into your account and navigating to the 'Rewards'
                            section.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">How do I refer a friend? <span class="plus-minus">+</span></button>
                    <div class="accordion-content">
                        <p>Share your unique referral link found in your account. You’ll earn points when your friends
                            make a qualifying purchase.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">How do I redeem my points? <span
                            class="plus-minus">+</span></button>
                    <div class="accordion-content">
                        <p>Visit the Rewards section in your account to view available rewards and redeem points for
                            discounts or vouchers.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">Is there a limit to the number of points I can earn? <span
                            class="plus-minus">+</span></button>
                    <div class="accordion-content">
                        <p>There is no limit to how many points you can earn. The more you shop and engage, the more
                            points you'll accumulate.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">What do the ‘approved’, ‘pending’ and ‘cancelled’ statuses mean?
                        <span class="plus-minus">+</span></button>
                    <div class="accordion-content">
                        <p>‘Approved’ points are those available for redemption. ‘Pending’ points are awaiting approval,
                            and ‘Cancelled’ points were voided.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">I completed an activity but didn’t earn points! <span
                            class="plus-minus">+</span></button>
                    <div class="accordion-content">
                        <p>If you completed an activity but didn’t earn points, please allow a few days for processing.
                            Contact support if the issue persists.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">Do my points expire? <span class="plus-minus">+</span></button>
                    <div class="accordion-content">
                        <p>Points expire after 12 months of inactivity. Keep shopping and engaging to retain your points
                            and benefits.</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-button">Where can I report a problem or give feedback? <span
                            class="plus-minus">+</span></button>
                    <div class="accordion-content">
                        <p>We value your feedback! Contact our support team via email or phone, available in the
                            'Contact Us' section of our website.</p>
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
    

    
   
   

    <!-- Footer here -->

    <script src="accordion.js"></script>
</body>

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
        <p>© 2035 by NOUS DEUX FRAGRANCES. Built on Wix Studio™</p>
    </div>
</footer>

</html>