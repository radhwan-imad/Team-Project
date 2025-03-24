<?php session_start();
require_once("connection.php");
if(!isset($_SESSION['User_ID'])) {
    header("Location: Login.php");
    exit();
}

$userDetails = $conn->prepare('SELECT registration_date FROM users WHERE User_ID = ?');
$userDetails->bind_param('i', $_SESSION['User_ID']);
$userDetails->execute();
$result = $userDetails->get_result();
$userData = $result->fetch_assoc();

$memberSince = date('F Y', strtotime($userData['registration_date']));


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="Mainpage.css">
    <link rel="stylesheet" href="logged-in.css">
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
            <a href="Mainpage.php">
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
         Welcome, <?php echo htmlspecialchars($_SESSION['User_Name']); ?>!</a>
        
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

    <!-- Main Content -->
    <main>
        <section class="user-dashboard">
            <!-- Profile Section -->
            <div class="profile-overview">
                <h2>Welcome Back,  <?php echo htmlspecialchars($_SESSION['User_Name']); ?>!</h2>
                <img src="images/logo10.jpg" alt="User Picture" class="user-picture">
                <p>Explore our exclusive offers and updates tailored just for you. Thank you for choosing AU-RA!</p>
                <div class="profile-info">
                    <div class="profile-details">
                        <p><strong>Name:</strong>  <?php echo htmlspecialchars($_SESSION['User_Name']) ?> <?php echo htmlspecialchars($_SESSION['Last_Name']); ?></p>
                        <p><strong>Email:</strong>  <?php echo htmlspecialchars($_SESSION['Email_ID']); ?></p>
                        <p><strong>Member Since:</strong> <?php echo htmlspecialchars($memberSince); ?></p>
                    
                    </div>
                </div>
            </div>
            

            <!-- Dashboard Options -->
            <div class="dashboard-options">
                <h3>Your Dashboard</h3>
                <div class="dashboard-links">
                    <a href="past-orders.php" class="dashboard-item">
                        <img src="images/past1.png" alt="Orders Icon">
                        <h3>Past Orders</h3>
                    </a>
                    <a href="account-settings.php" class="dashboard-item">
                        <img src="images/gents.png" alt="Settings Icon">
                        <h3>Account Settings</h3>
                    </a>
                    <a href="society.php" class="dashboard-item">
                        <img src="images/rewards.png" alt="Society Icon">
                        <h3>Au-Ra Society</h3>
                    </a>
                </div>
            </div>

            <!-- Log-Out Button -->
            <div class="logout-btn-container">
                <a href="Logout.php" class="logout-btn">Log Out</a>
            </div>
        </section>
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
