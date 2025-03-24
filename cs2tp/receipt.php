<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['User_ID'];
$order_id = $_SESSION['last_order_id'] ?? 0;
$payment_id = $_SESSION['last_payment_id'] ?? 0;

// If we don't have a valid order ID in session, get the latest order
if (!$order_id) {
    $query = "SELECT Order_ID FROM orders WHERE User_ID = ? ORDER BY date DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $order_id = $row['Order_ID'];
    }
    $stmt->close();
}

// Get order details and items
$query = "SELECT o.Order_ID, o.date, o.status, oi.Product_ID, oi.Quantity, p.Name, p.Price, i.Image_URL
          FROM orders o
          JOIN order_items oi ON o.Order_ID = oi.Order_ID
          JOIN product p ON oi.Product_ID = p.Product_ID
          LEFT JOIN image i ON p.Image_ID = i.Image_ID
          WHERE o.Order_ID = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$order_info = null;
$order_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    if (!$order_info) {
        $order_info = [
            'date' => $row['date'],
            'status' => $row['status']
        ];
    }
    
    $item_price = $row['Price'] * $row['Quantity'];
    $total_price += $item_price;
    
    $order_items[] = [
        'name' => $row['Name'],
        'quantity' => $row['Quantity'],
        'price' => $row['Price'],
        'total' => $item_price,
        'image' => $row['Image_URL']
    ];
}
$stmt->close();

// Retrieve voucher discount from session, if it exists
$voucher_discount = $_SESSION['voucher_discount'] ?? 0;
$final_price = $total_price - $voucher_discount;

// Get payment information
$payment_info = null;
if ($order_id) {
    $query = "SELECT Payment_ID, Amount, method, Status FROM payment WHERE Order_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $payment_info = $row;
    }
    $stmt->close();
}

// Generate a random order number if we don't have one
$order_number = 'AU-' . substr(md5($order_id), 0, 8);

// Format the order date
$order_date = $order_info ? date('Y-m-d H:i:s', strtotime($order_info['date'])) : date('Y-m-d H:i:s');

// (Optional) Do not unset voucher discount until after the receipt is generated
// unset($_SESSION['voucher_discount']);
// Also clear other session variables as needed:
unset($_SESSION['last_order_id']);
unset($_SESSION['last_payment_id']);
unset($_SESSION['order_total']);
unset($_SESSION['payment_method']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="Mainpage.css">
    <style>
        .receipt-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .receipt-header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .order-details {
            margin-bottom: 20px;
        }
        .order-items {
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .order-total {
            text-align: right;
            margin-top: 20px;
            font-size: 1.2em;
            font-weight: bold;
        }
        .payment-details {
            margin: 20px 0;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .receipt-actions {
            text-align: center;
            margin-top: 20px;
        }
        .receipt-button, .continue-shopping-button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .receipt-button {
            background-color: #4CAF50;
            color: white;
        }
        .continue-shopping-button {
            background-color: #333;
            color: white;
        }
    </style>
</head>
<body>
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
                <a href="logged-in.php">Welcome, <?php echo htmlspecialchars($_SESSION['User_Name']); ?></a>
            <?php else: ?>
                <a href="logged-in.php">ACCOUNT</a>
            <?php endif; ?>
            <a href="contact-us.php">CONTACT-US</a>
        </div>
    </header>
    
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>Order Receipt</h1>
            <p>Order Number: <?php echo $order_number; ?></p>
            <p>Date: <?php echo $order_date; ?></p>
        </div>

        <div class="order-details">
            <h2>Order Items</h2>
            <div class="order-items">
                <?php foreach ($order_items as $item): ?>
                <div class="item-row">
                    <div>
                        <?php echo $item['name']; ?> x <?php echo $item['quantity']; ?>
                    </div>
                    <div>
                        £<?php echo number_format($item['total'], 2); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="order-total">
                <?php if ($voucher_discount > 0): ?>
                    <p><strong>Subtotal: £<?php echo number_format($total_price, 2); ?></strong></p>
                    <p><strong>Discount Applied: -£<?php echo number_format($voucher_discount, 2); ?></strong></p>
                    <p><strong>Final Total: £<?php echo number_format($final_price, 2); ?></strong></p>
                <?php else: ?>
                    <strong>Total: £<?php echo number_format($total_price, 2); ?></strong>
                <?php endif; ?>
            </div>
        </div>

        <div class="payment-details">
            <h2>Payment Information</h2>
            <?php if ($payment_info): ?>
            <div class="payment-row">
                <div>Payment Method:</div>
                <div><?php echo htmlspecialchars($payment_info['method']); ?></div>
            </div>
            <div class="payment-row">
                <div>Payment Status:</div>
                <div><?php echo htmlspecialchars($payment_info['Status']); ?></div>
            </div>
            <div class="payment-row">
                <div>Transaction ID:</div>
                <div><?php echo $payment_info['Payment_ID']; ?></div>
            </div>
            <?php else: ?>
            <p>Payment information not available.</p>
            <?php endif; ?>
        </div>

        <div class="receipt-actions">
            <button class="receipt-button" onclick="window.print()">Print Receipt</button>
            <button class="continue-shopping-button" onclick="window.location.href='past-orders.php'">Continue</button>
        </div>
    </div>

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
            <img src="images/payment.png" alt="Payment Methods" style="width: auto; height: 30px;">
            <p>These payment methods are for illustrative purposes only.</p>
        </div>

        <div class="footer-bottom">
            <p>2024 AU-RA. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
