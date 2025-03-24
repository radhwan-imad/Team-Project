<?php
session_start();
require_once("connection.php");

// Check if the user is logged in; if not, redirect to Login.php
if (!isset($_SESSION['User_ID'])) {
    header("Location: Login.php");
    exit();
}

// Initialize variables for error and success messages
$error_messages = [];
$success_message = '';

// Fetch all cities for the dropdown
$citiesQuery = "SELECT City_ID, Name FROM city ORDER BY Name";
$citiesResult = $conn->query($citiesQuery);

if ($citiesResult === false) {
    $error_messages['database'] = "Error fetching cities: " . $conn->error;
    $cities = [];
} else {
    $cities = $citiesResult->fetch_all(MYSQLI_ASSOC);
    error_log("Number of cities fetched: " . count($cities));
    if (empty($cities)) {
        $error_messages['cities'] = "No cities found in the database. Please contact support.";
    }
}

// Fetch the user's current address (if any) when the page loads
$addressQuery = "SELECT a.Address_line_1, a.Address_line_2, a.Postcode, a.country, p.City_ID
                FROM user_address ua
                JOIN address a ON ua.Address_ID = a.Address_ID
                JOIN postcode p ON a.Postcode = p.Postcode
                WHERE ua.User_ID = ?";
$stmt = $conn->prepare($addressQuery);
$stmt->bind_param("i", $_SESSION['User_ID']);
$stmt->execute();
$addressResult = $stmt->get_result();
$address = $addressResult->fetch_assoc();
$stmt->close();

// Store address in session for display
if ($address) {
    $_SESSION['Address_line_1'] = $address['Address_line_1'];
    $_SESSION['Address_line_2'] = $address['Address_line_2'];
    $_SESSION['Postcode'] = $address['Postcode'];
    $_SESSION['Country'] = $address['country'];
    $_SESSION['City_ID'] = $address['City_ID'];
} else {
    // If no address exists, set session variables to empty
    $_SESSION['Address_line_1'] = '';
    $_SESSION['Address_line_2'] = '';
    $_SESSION['Postcode'] = '';
    $_SESSION['Country'] = '';
    $_SESSION['City_ID'] = '';
}

// Handle form submission
if (isset($_POST['submitted'])) {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address_line_1 = trim($_POST['address_line_1'] ?? '');
    $address_line_2 = trim($_POST['address_line_2'] ?? '');
    $postcode = trim($_POST['postcode'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $city_id = trim($_POST['city_id'] ?? '');

    // Debug: Log the form data
    error_log("Form submitted with data: name=$name, email=$email, phone=$phone, address_line_1=$address_line_1, postcode=$postcode, country=$country, city_id=$city_id");

    // Validate inputs
    if (empty($name)) {
        $error_messages['name'] = "Name is required.";
    }
    if (empty($email)) {
        $error_messages['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_messages['email'] = "Invalid email format.";
    }
    if (empty($phone)) {
        $error_messages['phone'] = "Phone number is required.";
    } elseif (!preg_match('/^\+?[0-9]{1,4}?[0-9\s\-]{7,15}$/', $phone)) {
        $error_messages['phone'] = "Invalid phone number format (e.g., +44 1234567890).";
    }
    // Validate address fields (if any are provided, all required fields must be filled)
    if (!empty($address_line_1) || !empty($postcode) || !empty($country) || !empty($city_id)) {
        if (empty($address_line_1) || empty($postcode) || empty($country) || empty($city_id)) {
            $error_messages['address'] = "If providing an address, please include address line 1, postcode, country, and city.";
        }
    }

    // Check if the new email is already taken by another user
    if ($email !== $_SESSION['Email_ID']) {
        $check_email = $conn->prepare("SELECT User_ID FROM users WHERE Email_ID = ? AND User_ID != ?");
        $check_email->bind_param("si", $email, $_SESSION['User_ID']);
        $check_email->execute();
        if ($check_email->get_result()->num_rows > 0) {
            $error_messages['email'] = "Email is already taken by another user.";
        }
        $check_email->close();
    }

    // Debug: Log validation errors
    if (!empty($error_messages)) {
        error_log("Validation errors: " . print_r($error_messages, true));
    }

    // If no errors, update the database
    if (empty($error_messages)) {
        // Begin transaction for database updates
        $conn->begin_transaction();

        try {
            // Debug: Log that we're updating the user details
            error_log("Updating user details for User_ID: " . $_SESSION['User_ID']);

            // Update user details (First_Name, Last_Name, Email_ID, Contact_NO)
            $stmt = $conn->prepare("UPDATE users SET First_Name = ?, Last_Name = ?, Email_ID = ?, Contact_NO = ? WHERE User_ID = ?");
            $stmt->bind_param("ssssi", $name, $last_name, $email, $phone, $_SESSION['User_ID']);
            $stmt->execute();
            $stmt->close();

            // Update address if provided
            if (!empty($address_line_1) && !empty($postcode) && !empty($country) && !empty($city_id)) {
                // Debug: Log that we're updating the address
                error_log("Updating address for User_ID: " . $_SESSION['User_ID']);

                // Check if the postcode exists in the postcode table with the given city_id
                $checkPostcodeStmt = $conn->prepare("SELECT Postcode FROM postcode WHERE Postcode = ? AND City_ID = ?");
                $checkPostcodeStmt->bind_param("si", $postcode, $city_id);
                $checkPostcodeStmt->execute();
                $postcodeResult = $checkPostcodeStmt->get_result();
                if ($postcodeResult->num_rows == 0) {
                    // Postcode doesn't exist, insert it
                    $insertPostcodeStmt = $conn->prepare("INSERT INTO postcode (Postcode, City_ID) VALUES (?, ?)");
                    $insertPostcodeStmt->bind_param("si", $postcode, $city_id);
                    $insertPostcodeStmt->execute();
                    $insertPostcodeStmt->close();
                    error_log("Inserted new postcode: $postcode with City_ID: $city_id");
                } else {
                    error_log("Postcode $postcode already exists with City_ID: $city_id");
                }
                $checkPostcodeStmt->close();

                // Check if the same address already exists in the address table
                $checkAddressStmt = $conn->prepare("SELECT Address_ID FROM address WHERE Address_line_1 = ? AND Address_line_2 = ? AND Postcode = ? AND country = ?");
                $checkAddressStmt->bind_param("ssss", $address_line_1, $address_line_2, $postcode, $country);
                $checkAddressStmt->execute();
                $addressResult = $checkAddressStmt->get_result();

                if ($addressResult->num_rows > 0) {
                    // Address already exists, get its ID
                    $existingAddress = $addressResult->fetch_assoc();
                    $address_id = $existingAddress['Address_ID'];
                    error_log("Address already exists with Address_ID: $address_id");

                    // Check if user already has this address
                    $checkUserAddressStmt = $conn->prepare("SELECT User_Address_ID FROM user_address WHERE User_ID = ? AND Address_ID = ?");
                    $checkUserAddressStmt->bind_param("ii", $_SESSION['User_ID'], $address_id);
                    $checkUserAddressStmt->execute();
                    $userAddressResult = $checkUserAddressStmt->get_result();

                    if ($userAddressResult->num_rows == 0) {
                        // User doesn't have this address yet, add it to user_address
                        $userAddressStmt = $conn->prepare("INSERT INTO user_address (User_ID, Address_ID) VALUES (?, ?)");
                        $userAddressStmt->bind_param("ii", $_SESSION['User_ID'], $address_id);
                        $userAddressStmt->execute();
                        $userAddressStmt->close();
                        error_log("Linked user to existing address, Address_ID: $address_id");
                    }
                    $checkUserAddressStmt->close();
                } else {
                    // Address doesn't exist, insert it
                    $addressStmt = $conn->prepare("INSERT INTO address (Address_line_1, Address_line_2, Postcode, country) VALUES (?, ?, ?, ?)");
                    $addressStmt->bind_param("ssss", $address_line_1, $address_line_2, $postcode, $country);
                    $addressStmt->execute();
                    $address_id = $conn->insert_id;
                    $addressStmt->close();
                    error_log("Inserted new address with Address_ID: $address_id");

                    // Insert into user_address table
                    $userAddressStmt = $conn->prepare("INSERT INTO user_address (User_ID, Address_ID) VALUES (?, ?)");
                    $userAddressStmt->bind_param("ii", $_SESSION['User_ID'], $address_id);
                    $userAddressStmt->execute();
                    $userAddressStmt->close();
                    error_log("Linked user to new address, Address_ID: $address_id");
                }
                $checkAddressStmt->close();
            }

            // Commit transaction
            $conn->commit();

            // Debug: Log successful update
            error_log("Profile updated successfully for User_ID: " . $_SESSION['User_ID']);

            // Update session variables with the new data
            $_SESSION['User_Name'] = $name;
            $_SESSION['Last_Name'] = $last_name;
            $_SESSION['Email_ID'] = $email;
            $_SESSION['Contact_NO'] = $phone;
            $_SESSION['Address_line_1'] = $address_line_1;
            $_SESSION['Address_line_2'] = $address_line_2;
            $_SESSION['Postcode'] = $postcode;
            $_SESSION['Country'] = $country;
            $_SESSION['City_ID'] = $city_id;

            // Set success message without email confirmation
            $success_message = "Your profile has been updated successfully.";
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            $success_message = "Error updating profile: " . $e->getMessage();
            error_log("Error updating profile for User_ID: " . $_SESSION['User_ID'] . " - " . $e->getMessage());
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="Mainpage.css">
    <link rel="stylesheet" href="account-settings.css">
    <style>
        /* Profile Section Styling */
.profile-section {
    padding: 40px 20px;
    background-color: #f9f5f0;
}

.profile-container {
    max-width: 800px;
    margin: 40px auto;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding:20px;
}

.profile-header {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 1px solid #e5e5e5;
    padding-bottom: 20px;
}

.profile-header h2 {
    color: #a27b5c;
    font-size: 28px;
    margin-bottom: 10px;
}

.profile-header p {
    color: #666;
}

/* Form Styling */
.profile-details {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.profile-info {
    width: 100%;
}

.profile-info h3 {
    font-size: 22px;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

/* Form Fields */
.profile-info label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 16px;
}

.profile-info input,
.profile-info select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fff;
    font-size: 16px;
    font-family: 'Lora', serif;
    color: #333;
    box-sizing: border-box;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.profile-info input:focus,
.profile-info select:focus {
    border-color: #a27b5c;
    box-shadow: 0 0 0 2px rgba(162, 123, 92, 0.2);
    outline: none;
}

/* Custom select styling */
.profile-info select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url('data:image/svg+xml;utf8,<svg fill="%23333" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 16px;
    padding-right: 40px;
    cursor: pointer;
}

/* Error and Success Messages */
.error {
    color: #e74c3c;
    font-size: 14px;
    margin-top: -15px;
    margin-bottom: 15px;
    display: block;
}

.success {
    color: #2ecc71;
    font-size: 16px;
    background: #e8f8f1;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 25px;
    border-left: 4px solid #2ecc71;
}

/* Form Actions */
.profile-actions {
    display: flex;
    justify-content: center;
    margin-top: 10px;
}

.primary-btn {
    background-color: #a27b5c;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.primary-btn:hover {
    background-color: #8a6547;
}

/* Back Button */
.back-btn-container {
    text-align: center;
    margin-top: 25px;
}

.back-btn {
    display: inline-block;
    color: #666;
    text-decoration: none;
    font-size: 16px;
    transition: color 0.3s ease;
}

.back-btn:hover {
    color: #a27b5c;
    text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .profile-container {
        padding: 20px;
    }
    
    .profile-info input,
    .profile-info select {
        padding: 10px;
        font-size: 15px;
    }
    
    .primary-btn {
        width: 100%;
    }
}
    </style>
    <script>
        // JavaScript function to confirm form submission
        function confirmSubmission() {
            return confirm("Are you sure you want to save these changes to your profile?");
        }
    </script>
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
            </a>
            <span class="logo-text">AU-RA<br>Fragrance your soul</span>
        </div>

        <!-- Right-side Navigation -->
        <div class="nav-right">
            <form method="GET" action="search.php" class="search-form">
                <input type="text" name="query" placeholder="Search for products..." class="search-input">
                <button type="submit">Search</button>
            </form>
            <?php if (isset($_SESSION['Name']) && !empty($_SESSION['Name'])): ?>
            
                <a href="account-settings.php">ACCOUNT</a>
                <a href="Logout.php">LOGOUT</a>
            <?php else: ?>
                <a href="logged-in.php">ACCOUNT</a>
            <?php endif; ?>
            <a href="contact-us.php">CONTACT-US</a>
            
        </div>
    </header>

    <!-- Main Content: Profile Page -->
    <main>
        <section class="profile-container">
            
                <div class="profile-header">
                    <h2>Your Profile</h2>
                    <p>View and edit your personal details below.</p>
                </div>

                <!-- Profile Form -->
                <form id="profile-form" class="profile-details" action="" method="POST" onsubmit="return confirmSubmission()">
                    
                    <div class="profile-info">
                        <h3>Personal Information</h3>

                        <!-- Success Message -->
                        <?php if ($success_message): ?>
                            <div class="success"><?php echo $success_message; ?></div>
                        <?php endif; ?>

                        <!-- Display database errors if any -->
                        <?php if (isset($error_messages['database'])): ?>
                            <div class="error"><?php echo $error_messages['database']; ?></div>
                        <?php endif; ?>
                        <?php if (isset($error_messages['cities'])): ?>
                            <div class="error"><?php echo $error_messages['cities']; ?></div>
                        <?php endif; ?>

                        <!-- First Name -->
                        <label for="name">First Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['Name'] ?? ''); ?>" required>
                        <?php if (isset($error_messages['name'])): ?>
                            <div class="error"><?php echo $error_messages['name']; ?></div>
                        <?php endif; ?>

                        <!-- Last Name -->
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_SESSION['Last_Name'] ?? ''); ?>">
                        <?php if (isset($error_messages['last_name'])): ?>
                            <div class="error"><?php echo $error_messages['last_name']; ?></div>
                        <?php endif; ?>

                        <!-- Email -->
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['Email_ID'] ?? ''); ?>" required>
                        <?php if (isset($error_messages['email'])): ?>
                            <div class="error"><?php echo $error_messages['email']; ?></div>
                        <?php endif; ?>

                        <!-- Phone -->
                        <label for="phone">Phone:</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_SESSION['Contact_NO'] ?? ''); ?>" required>
                        <?php if (isset($error_messages['phone'])): ?>
                            <div class="error"><?php echo $error_messages['phone']; ?></div>
                        <?php endif; ?>

                        <!-- Address Fields -->
                        <label for="address_line_1">Address Line 1:</label>
                        <input type="text" id="address_line_1" name="address_line_1" value="<?php echo htmlspecialchars($_SESSION['Address_line_1'] ?? ''); ?>">
                        <?php if (isset($error_messages['address'])): ?>
                            <div class="error"><?php echo $error_messages['address']; ?></div>
                        <?php endif; ?>

                        <label for="address_line_2">Address Line 2:</label>
                        <input type="text" id="address_line_2" name="address_line_2" value="<?php echo htmlspecialchars($_SESSION['Address_line_2'] ?? ''); ?>">

                        <label for="city_id">City:</label>
                        <select id="city_id" name="city_id">
                            <option value="">Select a city</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo $city['City_ID']; ?>" <?php echo ($_SESSION['City_ID'] == $city['City_ID']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($city['Name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="postcode">Postcode:</label>
                        <input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($_SESSION['Postcode'] ?? ''); ?>">

                        <label for="country">Country:</label>
                        <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($_SESSION['Country'] ?? ''); ?>">
                    </div>

                    <div class="profile-actions">
                        <input type="hidden" name="submitted" value="true">
                        <button type="submit" class="primary-btn">Save Changes</button>
                    </div>
                </form>

                <!-- Back to Dashboard Button -->
                <div class="back-btn-container">
                    <a href="logged-in.php" class="back-btn">Back to Dashboard</a>
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