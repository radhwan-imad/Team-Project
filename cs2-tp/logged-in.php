
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
            
            <a href="society.html">Au-Ra SOCIETY</a>
            <a href="about.html">ABOUT US</a>
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
        
                    <a href="logged-in.php">ACCOUNT</a>
                    <a href="contact-us.php">CONTACT-US</a>
                    <a href="cart.php">CART </a>
                </div>
        
</header>

    <!-- Main Content -->
    <main>
        <section class="user-dashboard">
            <!-- Profile Section -->
            <div class="profile-overview">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['User_Name'], ENT_QUOTES, 'UTF-8'); ?>!</span>
                <img src="images/meme cat.jpg" alt="User Picture" class="user-picture">
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
                    <a href="society.html" class="dashboard-item">
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

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 AU-RA. All rights reserved.</p>
        <ul class="footer-links">
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="privacy-policy.html">Privacy Policy</a></li>
            <li><a href="terms.html">Terms of Service</a></li>
        </ul>
    </footer>
</body>

</html>
