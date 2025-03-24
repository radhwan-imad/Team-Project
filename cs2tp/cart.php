<?php
session_start();
require 'connection.php'; // Include database connection file

// Get current user ID from session
$user_id = $_SESSION["User_ID"] ?? null;
if (!$user_id) {
    header("Location: Login.php");
    exit;
}

// Handle Add, Remove, Update Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if this is a voucher apply action separately
    if (isset($_POST['apply_voucher'])) {
        $voucher_code = trim($_POST['voucher_code'] ?? '');
        $voucher_error = '';
        if ($voucher_code !== '') {
            // Look up voucher in the vouchers table
            $voucherQuery = "SELECT Discount_Amount FROM vouchers WHERE Voucher_Code = ? AND Is_Active = 1 LIMIT 1";
            $stmt = $conn->prepare($voucherQuery);
            $stmt->bind_param("s", $voucher_code);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                // Valid voucher, store discount in session
                $_SESSION['voucher_discount'] = $row['Discount_Amount'];
            } else {
                $voucher_error = "Invalid or expired voucher code.";
                unset($_SESSION['voucher_discount']);
            }
            $stmt->close();
        } else {
            $voucher_error = "Please enter a voucher code.";
            unset($_SESSION['voucher_discount']);
        }
        // Do not redirect if voucher apply action; let the page reload to show the message
    } else {
        // Cart update actions
        $product_id = $_POST['product_id'];
        $action = $_POST['action'];

        // Get Cart_ID for the User
        $cartQuery = "SELECT Cart_ID FROM cart WHERE User_ID = ?";
        $stmt = $conn->prepare($cartQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cartResult = $stmt->get_result();
        $cart = $cartResult->fetch_assoc();
        $cart_id = $cart['Cart_ID'] ?? null;

        if (!$cart_id) {
            // If no cart exists, create one
            $createCartQuery = "INSERT INTO cart (User_ID) VALUES (?)";
            $stmt = $conn->prepare($createCartQuery);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $cart_id = $stmt->insert_id;
        }

        if ($action === 'add') {
            $quantity = 1;
            // Check if item already exists in cart
            $checkQuery = "SELECT * FROM cart_items WHERE Cart_ID = ? AND Product_ID = ?";
            $stmt = $conn->prepare($checkQuery);
            $stmt->bind_param("ii", $cart_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Update quantity if item exists
                $updateQuery = "UPDATE cart_items SET Quantity = Quantity + ? WHERE Cart_ID = ? AND Product_ID = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("iii", $quantity, $cart_id, $product_id);
            } else {
                // Insert new item if not exists
                $insertQuery = "INSERT INTO cart_items (Cart_ID, Product_ID, Quantity) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param("iii", $cart_id, $product_id, $quantity);
            }
            $stmt->execute();
        } elseif ($action === 'remove') {
            // Decrease quantity
            $updateQuery = "UPDATE cart_items SET Quantity = Quantity - 1 WHERE Cart_ID = ? AND Product_ID = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("ii", $cart_id, $product_id);
            $stmt->execute();

            // Remove if quantity reaches zero
            $deleteQuery = "DELETE FROM cart_items WHERE Cart_ID = ? AND Product_ID = ? AND Quantity <= 0";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("ii", $cart_id, $product_id);
            $stmt->execute();
        } elseif ($action === 'Remove') {
            // Remove item entirely
            $deleteQuery = "DELETE FROM cart_items WHERE Cart_ID = ? AND Product_ID = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("ii", $cart_id, $product_id);
            $stmt->execute();
        }
        
        // Clear any voucher discount when the cart is updated
        unset($_SESSION['voucher_discount']);

        // Redirect to prevent form resubmission
        header("Location: cart.php");
        exit;
    }
}

// Fetch Cart Items for the User
$cartItemsQuery = "SELECT p.Product_ID, p.Name, p.Price, i.Image_URL, ci.Quantity 
FROM cart_items ci
JOIN product p ON ci.Product_ID = p.Product_ID
JOIN cart c ON ci.Cart_ID = c.Cart_ID
JOIN image i ON p.Product_ID = i.Product_ID AND i.Is_Main_Image = 1
WHERE c.User_ID = ?";
$stmt = $conn->prepare($cartItemsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_price = 0;
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['Price'] * $row['Quantity'];
}
$stmt->close();

// If the cart is empty, clear any voucher discount
if (empty($cart_items) || $total_price == 0) {
    unset($_SESSION['voucher_discount']);
}

// Apply discount if voucher is in session
$discount_amount = $_SESSION['voucher_discount'] ?? 0;
$final_price = max(0, $total_price - $discount_amount);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="Mainpage.css">
    <link rel="stylesheet" href="cart.css">
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
            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                <a href="logged-in.php">Welcome, <?php echo htmlspecialchars($_SESSION['User_Name']); ?></a>
            <?php else: ?>
                <a href="logged-in.php">ACCOUNT</a>
            <?php endif; ?>
            <a href="wishlist.php">WISHLIST (...) </a>
            <a href="cart.php">CART (<?php 
                if (isset($_SESSION['User_ID'])) {
                    $user_id = $_SESSION['User_ID'];
                    if ($stmt = $conn->prepare("SELECT Cart_ID FROM cart WHERE User_ID = ?")) {
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result_cart = $stmt->get_result();
                        if ($row_cart = $result_cart->fetch_assoc()) {
                            $cart_id = $row_cart['Cart_ID'];
                            $stmt->close();
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
                    echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                }
            ?>)</a>
            <a href="contact-us.php">CONTACT-US</a>
        </div>
    </header>

    <main>
        <h1 class="heading1">Your Shopping Cart</h1>
        <div class="cart-container">
            <?php if (empty($cart_items)): ?>
                <p>Your cart is empty. <a href="shop-all.php">Continue shopping</a>.</p>
            <?php else: ?>
                <!-- Display cart items -->
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <img src="images/<?php echo htmlspecialchars($item['Image_URL']); ?>" alt="<?php echo htmlspecialchars($item['Name']); ?>">
                        <div>
                            <h2><?php echo htmlspecialchars($item['Name']); ?></h2>
                            <p>Price: £<?php echo number_format($item['Price'], 2); ?></p>
                            <p>Quantity: <?php echo $item['Quantity']; ?></p>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $item['Product_ID']; ?>">
                                <button type="submit" name="action" value="remove">-</button>
                                <button type="submit" name="action" value="add">+</button>
                                <button type="submit" name="action" value="Remove" class="remove-button">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Display totals -->
                <div class="cart-total">
                    <h3>Subtotal: £<?php echo number_format($total_price, 2); ?></h3>
                    <?php if ($discount_amount > 0): ?>
                        <h4>Discount: -£<?php echo number_format($discount_amount, 2); ?></h4>
                        <h3>Final Total: £<?php echo number_format($final_price, 2); ?></h3>
                    <?php else: ?>
                        <h3>Total: £<?php echo number_format($total_price, 2); ?></h3>
                    <?php endif; ?>
                </div>

                <!-- Voucher Form: only show if cart is not empty -->
                <div class="voucher-section">
                    <form method="POST" style="display:flex; gap:10px; align-items:center;">
                        <label for="voucher_code">Have a voucher code?</label>
                        <input type="text" name="voucher_code" id="voucher_code" placeholder="Enter voucher code">
                        <button type="submit" class="apply-voucher-btn" name="apply_voucher">Apply Voucher</button>
                    </form>
                    <?php if (!empty($voucher_error)): ?>
                        <p style="color:red; margin-top:5px;"><?php echo $voucher_error; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Proceed to Checkout -->
                <form action="checkout.php" method="POST" style="margin-top:20px;">
                    <button type="submit" class="checkout-button">Proceed to Checkout</button>
                </form>
            <?php endif; ?>
        </div>
    </main>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleAddressForm = document.getElementById('toggleAddressForm');
            const newAddressForm = document.getElementById('newAddressForm');
            if (toggleAddressForm) {
                toggleAddressForm.addEventListener('click', function() {
                    newAddressForm.style.display = newAddressForm.style.display === 'none' ? 'block' : 'none';
                    toggleAddressForm.textContent = newAddressForm.style.display === 'none' ? '+ Add a new address' : '- Hide address form';
                    const addressRadios = document.querySelectorAll('.address-radio');
                    addressRadios.forEach(radio => radio.checked = false);
                    document.querySelectorAll('.address-item').forEach(item => item.classList.remove('selected'));
                });
            }
            const addressRadios = document.querySelectorAll('.address-radio');
            addressRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.address-item').forEach(item => item.classList.remove('selected'));
                    this.closest('.address-item').classList.add('selected');
                    newAddressForm.style.display = 'none';
                    if (toggleAddressForm) {
                        toggleAddressForm.textContent = '+ Add a new address';
                    }
                    const addressForm = document.getElementById('newAddressForm');
                    if (addressForm) {
                        const inputs = addressForm.querySelectorAll('input[type="text"], select');
                        inputs.forEach(input => input.value = '');
                    }
                });
            });
            const checkoutForm = document.getElementById('checkoutForm');
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function(event) {
                    if (event.submitter && event.submitter.name === 'save_address') {
                        return true;
                    }
                    const addressSelected = Array.from(addressRadios).some(radio => radio.checked);
                    if (!addressSelected) {
                        const addressLine1 = document.getElementById('address_line_1').value.trim();
                        const postcode = document.getElementById('postcode').value.trim();
                        const country = document.getElementById('country').value.trim();
                        const cityId = document.getElementById('city_id').value;
                        if (!addressLine1 || !postcode || !country || !cityId) {
                            event.preventDefault();
                            alert('Please select an existing address or complete all required fields to add a new address.');
                            return false;
                        }
                    }
                    return true;
                });
            }
        });
    </script>
</body>
</html>
