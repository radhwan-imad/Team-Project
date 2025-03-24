<?php
session_start();
require 'connection.php'; // Include database connection file

// Get current user ID from session
$user_id = $_SESSION["User_ID"] ?? null;
if (!$user_id) {
    header("Location: Login.php");
    exit;
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

// Fetch existing addresses for the user (simplified query)
$addressQuery = "SELECT ua.User_Address_ID, a.Address_ID, a.Address_line_1, a.Address_line_2, a.Postcode, a.country
                FROM user_address ua
                JOIN address a ON ua.Address_ID = a.Address_ID
                WHERE ua.User_ID = ?";
$stmt = $conn->prepare($addressQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$addresses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get all cities for the dropdown
$citiesQuery = "SELECT City_ID, Name FROM city ORDER BY Name";
$citiesResult = $conn->query($citiesQuery);
$cities = $citiesResult->fetch_all(MYSQLI_ASSOC);

// Calculate final price using voucher discount if set (otherwise final price equals subtotal)
$discount_amount = $_SESSION['voucher_discount'] ?? 0;
$final_price = max(0, $total_price - $discount_amount);

// Handle checkout process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_checkout'])) {
    $error_message = null;
    
    // Check if address is selected
    if (!empty($_POST['saved_address'])) {
        // User selected an existing address
        $_SESSION['selected_address_id'] = $_POST['saved_address'];
        header("Location: payment.php");
        exit;
    } else if (isset($_POST['address_line_1']) && !empty($_POST['address_line_1']) && 
              isset($_POST['postcode']) && !empty($_POST['postcode']) && 
              isset($_POST['country']) && !empty($_POST['country']) && 
              isset($_POST['city_id']) && !empty($_POST['city_id'])) {
        // User entered a new address - save it and proceed
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Get values and set defaults for optional fields
            $address_line_1 = $_POST['address_line_1'];
            $address_line_2 = $_POST['address_line_2'] ?? '';
            $postcode = $_POST['postcode'];
            $country = $_POST['country'];
            $city_id = $_POST['city_id'];
            
            // Check if postcode exists with the same city_id, if not, add it
            $checkPostcodeStmt = $conn->prepare("SELECT Postcode FROM postcode WHERE Postcode = ? AND City_ID = ?");
            $checkPostcodeStmt->bind_param("si", $postcode, $city_id);
            $checkPostcodeStmt->execute();
            if ($checkPostcodeStmt->get_result()->num_rows == 0) {
                $insertPostcodeStmt = $conn->prepare("INSERT INTO postcode (Postcode, City_ID) VALUES (?, ?)");
                $insertPostcodeStmt->bind_param("si", $postcode, $city_id);
                $insertPostcodeStmt->execute();
            }
            
            // Check if the same address already exists
            $checkAddressStmt = $conn->prepare("SELECT Address_ID FROM address WHERE Address_line_1 = ? AND Address_line_2 = ? AND Postcode = ? AND country = ?");
            $checkAddressStmt->bind_param("ssss", $address_line_1, $address_line_2, $postcode, $country);
            $checkAddressStmt->execute();
            $addressResult = $checkAddressStmt->get_result();
            
            if ($addressResult->num_rows > 0) {
                // Address exists, get its ID
                $existingAddress = $addressResult->fetch_assoc();
                $address_id = $existingAddress['Address_ID'];
                
                // Check if user already has this address
                $checkUserAddressStmt = $conn->prepare("SELECT User_Address_ID FROM user_address WHERE User_ID = ? AND Address_ID = ?");
                $checkUserAddressStmt->bind_param("ii", $user_id, $address_id);
                $checkUserAddressStmt->execute();
                $userAddressResult = $checkUserAddressStmt->get_result();
                
                if ($userAddressResult->num_rows == 0) {
                    // Add to user_address
                    $userAddressStmt = $conn->prepare("INSERT INTO user_address (User_ID, Address_ID) VALUES (?, ?)");
                    $userAddressStmt->bind_param("ii", $user_id, $address_id);
                    $userAddressStmt->execute();
                    $user_address_id = $conn->insert_id;
                } else {
                    $userAddress = $userAddressResult->fetch_assoc();
                    $user_address_id = $userAddress['User_Address_ID'];
                }
            } else {
                // Insert new address
                $addressStmt = $conn->prepare("INSERT INTO address (Address_line_1, Address_line_2, Postcode, country) VALUES (?, ?, ?, ?)");
                $addressStmt->bind_param("ssss", $address_line_1, $address_line_2, $postcode, $country);
                $addressStmt->execute();
                $address_id = $conn->insert_id;
                
                // Insert into user_address
                $userAddressStmt = $conn->prepare("INSERT INTO user_address (User_ID, Address_ID) VALUES (?, ?)");
                $userAddressStmt->bind_param("ii", $user_id, $address_id);
                $userAddressStmt->execute();
                $user_address_id = $conn->insert_id;
            }
            
            // Commit transaction
            $conn->commit();
            
            $_SESSION['selected_address_id'] = $user_address_id;
            header("Location: payment.php");
            exit;
            
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Error adding address: " . $e->getMessage();
        }
    } else {
        $error_message = "Please select an existing address or add a new one before proceeding to checkout.";
    }
}

// Handle separate address form submission if needed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_address'])) {
    if (isset($_POST['address_line_1']) && isset($_POST['postcode']) && isset($_POST['country']) && isset($_POST['city_id'])) {
        $conn->begin_transaction();
        try {
            $address_line_1 = $_POST['address_line_1'];
            $address_line_2 = $_POST['address_line_2'] ?? '';
            $postcode = $_POST['postcode'];
            $country = $_POST['country'];
            $city_id = $_POST['city_id'];
            
            $checkPostcodeStmt = $conn->prepare("SELECT Postcode FROM postcode WHERE Postcode = ? AND City_ID = ?");
            $checkPostcodeStmt->bind_param("si", $postcode, $city_id);
            $checkPostcodeStmt->execute();
            if ($checkPostcodeStmt->get_result()->num_rows == 0) {
                $insertPostcodeStmt = $conn->prepare("INSERT INTO postcode (Postcode, City_ID) VALUES (?, ?)");
                $insertPostcodeStmt->bind_param("si", $postcode, $city_id);
                $insertPostcodeStmt->execute();
            }
            
            $checkAddressStmt = $conn->prepare("SELECT Address_ID FROM address WHERE Address_line_1 = ? AND Address_line_2 = ? AND Postcode = ? AND country = ?");
            $checkAddressStmt->bind_param("ssss", $address_line_1, $address_line_2, $postcode, $country);
            $checkAddressStmt->execute();
            $addressResult = $checkAddressStmt->get_result();

            if ($addressResult->num_rows > 0) {
                $existingAddress = $addressResult->fetch_assoc();
                $address_id = $existingAddress['Address_ID'];
                
                $checkUserAddressStmt = $conn->prepare("SELECT User_Address_ID FROM user_address WHERE User_ID = ? AND Address_ID = ?");
                $checkUserAddressStmt->bind_param("ii", $user_id, $address_id);
                $checkUserAddressStmt->execute();
                
                if ($checkUserAddressStmt->get_result()->num_rows == 0) {
                    $userAddressStmt = $conn->prepare("INSERT INTO user_address (User_ID, Address_ID) VALUES (?, ?)");
                    $userAddressStmt->bind_param("ii", $user_id, $address_id);
                    $userAddressStmt->execute();
                }
            } else {
                $addressStmt = $conn->prepare("INSERT INTO address (Address_line_1, Address_line_2, Postcode, country) VALUES (?, ?, ?, ?)");
                $addressStmt->bind_param("ssss", $address_line_1, $address_line_2, $postcode, $country);
                $addressStmt->execute();
                $address_id = $conn->insert_id;
                
                $userAddressStmt = $conn->prepare("INSERT INTO user_address (User_ID, Address_ID) VALUES (?, ?)");
                $userAddressStmt->bind_param("ii", $user_id, $address_id);
                $userAddressStmt->execute();
            }
            
            $conn->commit();
            header("Location: checkout.php");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Error adding address: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill out all required fields";
    }
}

if (isset($_GET['address_added']) && $_GET['address_added'] == 1) {
    $stmt = $conn->prepare($addressQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $addresses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="Mainpage.css">
    <link rel="stylesheet" href="cart.css">
    <style>
        .cart-checkout-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin: 20px 0;
            padding: 0 20px;
            max-width: calc(100% - 40px);
            margin-left: auto;
            margin-right: auto;
        }
        .cart-listing {
            flex: 1;
            min-width: 300px;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 10px;
            background-color: #fdf5e6;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
        }
        .address-form-container {
            flex: 1;
            min-width: 300px;
            max-height: 600px;
            overflow-y: auto;
            background-color: #fdf5e6;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            align-self: flex-start;
            position: sticky;
            top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .save-address-btn {
            background-color: #333;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .save-address-btn:hover {
            background-color: #555;
        }
        .saved-addresses {
            margin-top: 20px;
        }
        .address-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .address-item.selected {
            border-color: #333;
            background-color: #f0f0f0;
        }
        .success-message {
            background-color: #dff0d8; 
            padding: 10px; 
            margin-bottom: 15px; 
            border-radius: 4px;
            color: #3c763d;
        }
        .error-message {
            background-color: #f2dede; 
            padding: 10px; 
            margin-bottom: 15px; 
            border-radius: 4px;
            color: #a94442;
        }
        .checkout-button {
            display: block;
            width: 100%;
            text-align: center;
            background-color: black;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 1em;
            font-weight: bold;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .checkout-button:hover {
            background-color: #45a049;
        }
        .address-form-toggle {
            margin: 15px 0;
            cursor: pointer;
            color: #0066cc;
        }
    </style>
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
            <a href="wishlist.php">WISHLIST (<?php 
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
            ?>)</a>
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
        <h1 class="heading1">Check Your Order</h1>
        
        <?php if (isset($_GET['address_added']) && $_GET['address_added'] == 1): ?>
            <div class="success-message">
                Address added successfully!
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="cart-checkout-container">
            <!-- Left Side: Cart Items -->
            <div class="cart-listing">
                <?php if (empty($cart_items)): ?>
                    <p>Your cart is empty. <a href="shop-all.php">Continue shopping</a>.</p>
                <?php else: ?>
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

                    <!-- Totals Display: If a voucher discount is applied, show Final Total -->
                    <div class="cart-total">
                        <?php if ($discount_amount > 0): ?>
                            <h3>Final Total: £<?php echo number_format($final_price, 2); ?></h3>
                        <?php else: ?>
                            <h3>Total: £<?php echo number_format($total_price, 2); ?></h3>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Right Side: Shipping Form -->
            <div class="address-form-container">
                <h2>Shipping Address</h2>
                
                <?php if (!empty($cart_items)): ?>
                    <form action="" method="POST" id="checkoutForm">
                        <?php if (!empty($addresses)): ?>
                            <div class="saved-addresses">
                                <h3>Your Saved Addresses</h3>
                                <?php foreach ($addresses as $address): ?>
                                    <div class="address-item">
                                        <input type="radio" name="saved_address" id="address_<?php echo $address['User_Address_ID']; ?>" value="<?php echo $address['User_Address_ID']; ?>" class="address-radio">
                                        <label for="address_<?php echo $address['User_Address_ID']; ?>">
                                            <?php echo htmlspecialchars($address['Address_line_1']); ?><br>
                                            <?php if (!empty($address['Address_line_2'])): ?>
                                                <?php echo htmlspecialchars($address['Address_line_2']); ?><br>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($address['Postcode']); ?><br>
                                            <?php echo htmlspecialchars($address['country']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="address-form-toggle" id="toggleAddressForm">+ Add a new address</div>
                        <?php endif; ?>
                        
                        <div id="newAddressForm" <?php echo !empty($addresses) ? 'style="display:none;"' : ''; ?>>
                            <h3><?php echo empty($addresses) ? 'Add New Address' : 'New Address'; ?></h3>
                            
                            <div class="form-group">
                                <label for="address_line_1">Address Line 1: <span style="color: red;">*</span></label>
                                <input type="text" id="address_line_1" name="address_line_1">
                            </div>
                            
                            <div class="form-group">
                                <label for="address_line_2">Address Line 2:</label>
                                <input type="text" id="address_line_2" name="address_line_2">
                            </div>
                            
                            <div class="form-group">
                                <label for="city_id">City: <span style="color: red;">*</span></label>
                                <select id="city_id" name="city_id">
                                    <option value="">Select a city</option>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?php echo $city['City_ID']; ?>">
                                            <?php echo htmlspecialchars($city['Name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="postcode">Postcode: <span style="color: red;">*</span></label>
                                <input type="text" id="postcode" name="postcode">
                            </div>
                            
                            <div class="form-group">
                                <label for="country">Country: <span style="color: red;">*</span></label>
                                <input type="text" id="country" name="country">
                            </div>
                            
                            <button type="submit" name="save_address" class="save-address-btn">Save Address Only</button>
                        </div>
                        
                        <button type="submit" name="proceed_checkout" class="checkout-button">Proceed to Checkout</button>
                    </form>
                <?php endif; ?>
            </div>
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
            // Toggle new address form
            const toggleAddressForm = document.getElementById('toggleAddressForm');
            const newAddressForm = document.getElementById('newAddressForm');
            
            if (toggleAddressForm) {
                toggleAddressForm.addEventListener('click', function() {
                    newAddressForm.style.display = newAddressForm.style.display === 'none' ? 'block' : 'none';
                    toggleAddressForm.textContent = newAddressForm.style.display === 'none' ? '+ Add a new address' : '- Hide address form';
                    
                    // Clear any selected address radio
                    const addressRadios = document.querySelectorAll('.address-radio');
                    addressRadios.forEach(radio => {
                        radio.checked = false;
                    });
                    document.querySelectorAll('.address-item').forEach(item => {
                        item.classList.remove('selected');
                    });
                });
            }
            
            // Handle address selection
            const addressRadios = document.querySelectorAll('.address-radio');
            addressRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.address-item').forEach(item => {
                        item.classList.remove('selected');
                    });
                    this.closest('.address-item').classList.add('selected');
                    newAddressForm.style.display = 'none';
                    if (toggleAddressForm) {
                        toggleAddressForm.textContent = '+ Add a new address';
                    }
                    const addressForm = document.getElementById('newAddressForm');
                    if (addressForm) {
                        const inputs = addressForm.querySelectorAll('input[type="text"], select');
                        inputs.forEach(input => {
                            input.value = '';
                        });
                    }
                });
            });
            
            // Form validation on checkout
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
