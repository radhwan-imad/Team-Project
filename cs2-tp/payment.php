<?php
session_start();
if (empty($_SESSION["User_ID"])) {
	header("Location: Login.php");
	exit;
}
// Check if cart is empty or no items selected for checkout
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$cart_items = $_SESSION['cart'];

// Calculate total price
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="Mainpage.css">
    <link rel="stylesheet" href="payment.css">
</head>
<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        BLACK FRIDAY IS HERE! UP TO 50% OFF PLUS MANY COMBINATION DISCOUNTS
    </div>

    <!-- Navbar -->
    <header class="navbar">
        <div class="nav-left">
            <a href="Mainpage.html">HOME</a>
            <a href="shop-all.php">SHOP ALL</a>
            <a href="society.html">Au-Ra SOCIETY</a>
            <a href="about.html">ABOUT US</a>
        </div>
        <div class="logo">
            <a href="Mainpage.html">
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
            <a href="cart.php">CART (<?php echo count($cart_items); ?>)</a>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="left_section">
                <div class="cards">
                    <div class="front_card">
                        <img src="images/card-logo.svg" alt="" class="card_logo">
                        <div class="card_container">
                            <img src="images/bg-card-front.png" alt="">
                            <h1 id="number">0000 0000 0000 0000</h1>
                            <div class="card_info">
                                <span id="name">Jane Appleseed</span>
                                <span id="date">
                                    <span id="month">00</span>
                                    /
                                    <span id="year">00</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="back_card">
                        <img src="images/bg-card-back.png" alt="">
                        <span id="cvc">000</span>
                    </div>
                </div>
            </div>
            <div class="right_section">
                <form id="payment-form">
                    <div class="grid_1">
                        <label for="card_name">Cardholder Name</label>
                        <input type="text" id="card_name" name="card_name" placeholder="e.g. Jane Appleseed" required>
                    </div>
                    <div class="grid_2">
                        <label for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number" placeholder="e.g. 1234 5678 9123 0000" required maxlength="19">
                    </div>
                    <div class="card_information">
                        <div id="card_date">
                            <label>Exp. Date (MM/YY)</label>
                            <div class="two_inp">
                                <div>
                                    <input type="text" id="card_month" name="card_month" placeholder="MM" required maxlength="2">
                                </div>
                                <div>
                                    <input type="text" id="card_year" name="card_year" placeholder="YY" required maxlength="2">
                                </div>
                            </div>
                        </div>
                        <div class="grid_4">
                            <label for="card_cvc">CVC</label>
                            <input type="text" id="card_cvc" name="card_cvc" placeholder="e.g. 123" required maxlength="3">
                        </div>
                    </div>
                    <button type="submit" id="submit_btn">Pay Now</button>
                </form>
                <div class="thank hidden">
                    <img src="images/icon-complete.svg" alt="">
                    <h1>Thank you!</h1>
                    <p>Your payment has been processed</p>
                    <button onclick="window.location.href='receipt.php'">View Receipt</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer (same as previous version) -->
    <footer>
        <!-- Footer content remains the same as in the previous version -->
    </footer>
    
    <script>
        // JavaScript remains the same as in the previous version, 
        // with only the continue button's onclick changed to point to receipt.php
        const cardNumber = document.getElementById("number");
        const numberInp = document.getElementById("card_number");
        const nameInp = document.getElementById("card_name");
        const cardName = document.getElementById("name");
        const cardMonth = document.getElementById("month");
        const cardYear = document.getElementById("year");
        const monthInp = document.getElementById("card_month");
        const yearInp = document.getElementById("card_year");
        const cardCvc = document.getElementById("cvc");
        const cvcInp = document.getElementById("card_cvc");
        const form = document.getElementById("payment-form");
        const thankSection = document.querySelector(".thank");

        function setCardNumber(e) {
            cardNumber.innerText = format(e.target.value);
        }
        function setCardName(e) {
            cardName.innerText = e.target.value || "Jane Appleseed";
        }
        function setCardMonth(e) {
            cardMonth.innerText = e.target.value || "00";
        }
        function setCardYear(e) {
            cardYear.innerText = e.target.value || "00";
        }
        function setCardCvc(e) {
            cardCvc.innerText = e.target.value || "000";
        }

        function handleSubmit(e) {
            e.preventDefault();
            
            // Simple client-side validation
            const cardNameValid = nameInp.value.trim() !== "";
            const cardNumberValid = /^\d{4}\s\d{4}\s\d{4}\s\d{4}$/.test(numberInp.value);
            const monthValid = /^(0[1-9]|1[0-2])$/.test(monthInp.value);
            const yearValid = /^\d{2}$/.test(yearInp.value);
            const cvcValid = /^\d{3}$/.test(cvcInp.value);

            if (!cardNameValid || !cardNumberValid || !monthValid || !yearValid || !cvcValid) {
                alert("Please fill in all fields correctly.");
                return;
            }

            // Simulate payment processing
            setTimeout(() => {
                form.classList.add("hidden");
                thankSection.classList.remove("hidden");
                
                // Clear cart after successful payment
                fetch('clear_cart.php', {
                    method: 'POST'
                });
            }, 1500);
        }

        function format(s) {
            return s.toString().replace(/\d{4}(?=.)/g, "$& ");
        }

        numberInp.addEventListener("keyup", setCardNumber);
        nameInp.addEventListener("keyup", setCardName);
        monthInp.addEventListener("keyup", setCardMonth);
        yearInp.addEventListener("keyup", setCardYear);
        cvcInp.addEventListener("keyup", setCardCvc);
        form.addEventListener("submit", handleSubmit);
    </script>
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
                        <li><a href="shop-all.php">Shop All</a></li>
                        <li><a href="Scented candles.php">Candle</a></li>
                        <li><a href="Mainpage.php">Home</a></li>
                    </ul>
            </div>
            <div>
                <h4>Legal</h4>
                <ul>
                        <li><a href="about.html">Terms & Conditions</a></li>
                        <li><a href="about.html">Privacy Policy</a></li>
                        <li><a href="society.html">Shipping Policy</a></li>
                        <li><a href="contact-us.php">Refund Policy</a></li>
                        <li><a href="society.html">Accessibility Statement</a></li>
                    </ul>
            </div>
            <div>
                <h4>Headquarters</h4>
                <p>500 Terry Francine Street<br>San Francisco, CA 94158<br>info@mysite.com<br>123-456-7890</p>
            </div>
            <div>
                <h4>Socials</h4>
                <ul>
                        <li><a href="https://www.tiktok.com/login">TikTok</a></li>
                        <li><a href="https://www.instagram.com/">Instagram</a></li>
                        <li><a href="https://en-gb.facebook.com/">Facebook</a></li>
                        <li><a href="https://www.youtube.com/">YouTube</a></li>
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