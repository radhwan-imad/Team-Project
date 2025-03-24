<?php
session_start();
if (empty($_SESSION["User_ID"])) {
    header("Location: Login.php");
    exit;
}

// Check if cart is empty or no items selected for checkout
$cart_items = $_SESSION['cart'] ?? [];

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

             <a href="contact-us.php">CONTACT-US</a>
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
                                    <span id="month">00</span> /
                                    <span id="year">00</span>
                                </span>
                            </div>
                        </div>
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

    <script>
        // JavaScript for payment form functionality
        const cardNumber = document.getElementById("number");
        const numberInp = document.getElementById("card_number");
        const nameInp = document.getElementById("card_name");
        const cardName = document.getElementById("name");
        const cardMonth = document.getElementById("month");
        const cardYear = document.getElementById("year");
        const monthInp = document.getElementById("card_month");
        const yearInp = document.getElementById("card_year");
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
// Add this code to your existing script in payment.php
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

    // Determine card type based on first digit
    const cardNumber = numberInp.value.replace(/\s/g, '');
    let cardType = 'Credit Card';
    
    // Basic card detection logic
    if (cardNumber[0] === '4') {
        cardType = 'Visa';
    } else if (['51', '52', '53', '54', '55'].includes(cardNumber.substring(0, 2))) {
        cardType = 'MasterCard';
    } else if (['34', '37'].includes(cardNumber.substring(0, 2))) {
        cardType = 'American Express';
    } else if (['60', '65'].includes(cardNumber.substring(0, 2))) {
        cardType = 'Discover';
    }

    // Simulate payment processing
    setTimeout(() => {
        form.classList.add("hidden");
        thankSection.classList.remove("hidden");
        
        // Save payment method to session
        fetch('save_payment_method.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'method=' + encodeURIComponent(cardType)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Payment method saved');
            // Place order after successful payment
            return fetch('place_order.php', {
                method: 'POST'
            });
        })
        .then(() => {
            // place_order.php will handle the redirect
        })
        .catch(error => {
            console.error('Error:', error);
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
        form.addEventListener("submit", handleSubmit);
    </script>
</body>
</html>
