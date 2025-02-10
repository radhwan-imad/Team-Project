<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['User_ID'];

$query = "SELECT o.Order_ID, o.date, o.status, oi.Product_ID, oi.Quantity, p.Name, p.Price, i.Image_URL
          FROM orders o
          JOIN order_items oi ON o.Order_ID = oi.Order_ID
          JOIN product p ON oi.Product_ID = p.Product_ID
          JOIN image i ON p.Product_ID = i.Product_ID
          WHERE o.User_ID = ? 
          ORDER BY o.date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    if (!isset($orders[$row['Order_ID']])) {
        $orders[$row['Order_ID']] = [
            'date' => $row['date'],
            'status' => $row['status'],
            'products' => []
        ];
    }

    $orders[$row['Order_ID']]['products'][] = [
        'Product_ID' => $row['Product_ID'],
        'Name' => $row['Name'],
        'Quantity' => $row['Quantity'],
        'Price' => $row['Price'],
        'Image_URL' => $row['Image_URL']
    ];
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Orders - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="past-orders.css">
    <link rel="stylesheet" href="Mainpage.css">
</head>

<body class="past-orders-page">
    <div class="announcement-bar">
        BLACK FRIDAY IS HERE! UP TO 50% OFF PLUS MANY COMBINATION DISCOUNTS
    </div>
    <header class="navbar">
        <div class="nav-left">
            <a href="Mainpage.html">HOME</a>
            <a href="shop-all.html">SHOP ALL</a>
            <a href="Candles.html">CANDLES</a>
            <a href="society.html">Au-Ra SOCIETY</a>
            <a href="about.html">ABOUT US</a>
        </div>

        <div class="logo">
            <a href="Mainpage.html">
                <img src="Aura_logo.png" alt="logo"> </a>
            <span class="logo-text">AU-RA</span>
        </div>
        <div class="nav-right">
            <a href="#">SEARCH</a>
            <a href="account.html">ACCOUNT</a>
            <a href="#">COUNTRY ▼</a>
            <a href="#">WISHLIST</a>
            <a href="#">CART (0)</a>
        </div>
    </header>

	<main class="past-orders-container">
        <section class="past-orders-column">
            <h2>Past Orders</h2>
            <div class="orders-container">
                <?php foreach ($orders as $order_id => $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
                            <p><strong>Date:</strong> <?php echo $order['date']; ?></p>
                            <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
                            <button class="toggle-details">View Details ▼</button>
                        </div>
                        <div class="order-details">
                            <ul>
                                <?php foreach ($order['products'] as $product): ?>
                                    <li>
                                        <img src="images/<?php echo $product['Image_URL']; ?>" alt="<?php echo $product['Name']; ?>" class="product-image">
                                        <div class="product-info">
                                            <span><strong><?php echo $product['Name']; ?> - £<?php echo $product['Price']; ?></strong></span>
                                            <p>Quantity: <?php echo $product['Quantity']; ?></p>
                                            <button class="review-btn" data-product-id="<?php echo $product['Product_ID']; ?>" data-order="<?php echo $order_id; ?>">Leave a Review</button>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <p><strong>Total:</strong> £
                                <?php
                                    $total_price = 0;
                                    foreach ($order['products'] as $product) {
                                        $total_price += $product['Price'] * $product['Quantity'];
                                    }
                                    echo $total_price;
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="back-btn-container">
                <a href="logged-in.php" class="back-btn">Back to Dashboard</a>
            </div>
        </section>

        <section class="aura-society-column">
            <h2>Au-Ra Society</h2>
            <p>Welcome to the Au-Ra Society, our exclusive rewards program! Earn points with every purchase, enjoy free
                delivery, and unlock access to premium fragrances and experiences.</p>
            <div class="current-points">
                <p><strong>Your Current Points: 293</strong></p>
            </div>
            <p>Start earning rewards today:</p>
            <ul class="rewards-list">
                <li><strong>500 Points:</strong> £5 Voucher</li>
                <li><strong>1,000 Points:</strong> £10 Voucher</li>
                <li><strong>1,500 Points:</strong> £15 Voucher</li>
            </ul>
            <p>Level up through our tier system:</p>
            <ul class="tier-list">
                <li><strong>Silver:</strong> Earn 5 points per £1 spent, enjoy free samples, and get early access to
                    sales.</li>
                <li><strong>Gold:</strong> Spend £100 to unlock Gold status. Earn 7 points per £1 spent, receive
                    exclusive birthday gifts, and enjoy free samples.</li>
                <li><strong>Black:</strong> Spend £400 to unlock Black status. Earn 10 points per £1 spent, access
                    VIP-only events, and enjoy free samples.</li>
            </ul>
        </section>
    </main>

    <footer>
        <div class="footer-content">
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
                    <p>500 Terry Francine Street<br>San Francisco, CA 94158</p>
                    <p><a href="mailto:info@mysite.com">info@mysite.com</a></p>
                    <p>123-456-7890</p>
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
        <div class="payment-methods">
            <p>Pay Securely with</p>
            <img src="images/payment.png" alt="Payment Methods">
        </div>
        <div class="footer-bottom">
            <p>© 2024 by AU-RA. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.querySelectorAll('.toggle-details').forEach(button => {
            button.onclick = function () {
                const details = this.parentElement.nextElementSibling;
                if (details.style.display === 'block') {
                    details.style.display = 'none';
                    this.textContent = 'View Details ▼';
                } else {
                    details.style.display = 'block';
                    this.textContent = 'Hide Details ▲';
                }
            };
        });
    </script>

<div id="review-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Leave a Review</h2>
        <form id="review-form">
            <input type="hidden" id="order-id" name="order_id">
            <input type="hidden" id="product-id" name="product_id">

            <label for="rating">Rating:</label>
            <div class="star-rating">
                <input type="hidden" id="rating" name="rating" required>
                <div class="stars">
                    <span data-value="1">☆</span>
                    <span data-value="2">☆</span>
                    <span data-value="3">☆</span>
                    <span data-value="4">☆</span>
                    <span data-value="5">☆</span>
                </div>
                <div class="rating-text">Click to rate</div>
            </div>

            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" required></textarea>

            <button type="submit">Submit Review</button>
        </form>
    </div>
</div>

<script src="review.js"></script>

</body>

</html>