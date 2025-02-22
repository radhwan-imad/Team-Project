<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<header class="navbar">
    <div class="nav-left">
        <a href="Mainpage.php">HOME</a>
        <a href="shop-all.php">SHOP ALL</a>
        <a href="society.html">Au-Ra SOCIETY</a>
        <a href="about.html">ABOUT US</a>
    </div>
    <div class="logo">
        <a href="Mainpage.php">
            <img src="Aura_logo.png" alt="logo">
            <span class="logo-text">AU-RA<br>Fragrance your soul</span>
        </a>
    </div>
    <div class="nav-right">
        <form method="GET" action="search.php" class="search-form">
            <input type="text" name="query" placeholder="Search for products..." class="search-input">
            <button type="submit">Search</button>
        </form>
        <a href="Login.php">ACCOUNT</a>
        <a href="contact-us.php">CONTACT-US</a>
        <a href="cart.php">CART (<?php echo count($_SESSION['cart']); ?>)</a>
    </div>
</header>
