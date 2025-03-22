<?php
session_start();

// Initialize variables
$error_messages = [];
$form_data = $_SESSION['form_data'] ?? [];
$success_message = '';

if (isset($_POST['submitted'])) {
    require_once('connection.php');

    $fields = [
        'first-name' => 'First Name',
        'last-name' => 'Last Name',
        'signup-email' => 'Email',
        'contact-no' => 'Contact Number',
        'signup-password' => 'Password',
        'signup-password-confirm' => 'Confirm Password'
    ];

    $input = [];
    foreach ($fields as $key => $label) {
        $input[$key] = trim($_POST[$key] ?? '');
        if (empty($input[$key])) {
            $error_messages[$key] = "$label is required.";
        }
    }

    if (!empty($input['signup-email']) && !filter_var($input['signup-email'], FILTER_VALIDATE_EMAIL)) {
        $error_messages['signup-email'] = "Please enter a valid email (e.g., john.doe@example.com).";
    }
    if (!empty($input['contact-no']) && !preg_match('/^\+?[0-9]{1,4}?[0-9\s\-]{7,15}$/', $input['contact-no'])) {
        $error_messages['contact-no'] = "Please enter a valid phone number (e.g., +44 1234567890).";
    }
    if (!empty($input['signup-password']) && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,20}$/', $input['signup-password'])) {
        $error_messages['signup-password'] = "Password must be 8-20 characters with an uppercase, lowercase, number, and special character.";
    }
    if ($input['signup-password'] !== $input['signup-password-confirm']) {
        $error_messages['signup-password-confirm'] = "Passwords do not match.";
    }

    if (empty($error_messages)) {
        $verify_query = $conn->prepare("SELECT Email_ID FROM users WHERE Email_ID = ?");
        $verify_query->bind_param("s", $input['signup-email']);
        $verify_query->execute();
        if ($verify_query->get_result()->num_rows > 0) {
            $error_messages['signup-email'] = "Email is already taken!";
        }
    }

    if (empty($error_messages)) {
        try {
            $hashed_password = password_hash($input['signup-password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (First_Name, Last_Name, Email_ID, Password, Contact_NO) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $input['first-name'], $input['last-name'], $input['signup-email'], $hashed_password, $input['contact-no']);
            $stmt->execute();

            $id = $conn->insert_id;
            $cart_stmt = $conn->prepare("INSERT INTO cart (User_ID) VALUES (?)");
            $cart_stmt->bind_param("i", $id);
            $cart_stmt->execute();

            $success_message = "Congratulations! You are now registered. Please login now.";
        } catch (mysqli_sql_exception $ex) {
            $error_messages['general'] = "Database error occurred!";
        }
    }

    if (!empty($error_messages)) {
        $_SESSION['error_messages'] = $error_messages;
        $_SESSION['form_data'] = $_POST;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$error_messages = $_SESSION['error_messages'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['error_messages'], $_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AU-RA</title>
    <link rel="icon" type="image/x-icon" href="Aura_logo1.png">
    <link rel="stylesheet" href="Mainpage.css">
    <link rel="stylesheet" href="account.css">
    <style>
        /* Override default browser password toggle icons */
        input[type="password"]::-webkit-password-button,
        input[type="password"]::-ms-reveal {
            display: none !important;
        }

        input[type="password"] {
            -webkit-text-security: disc; /* Ensure password masking */
        }

        .input-group { margin-bottom: 20px; position: relative; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: normal; color: #666; } /* Changed to gray, non-bold */
        .input-group input { 
            width: 100%; 
            padding: 10px; 
            box-sizing: border-box; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            /* Disable browser default password toggle icon */
            -webkit-appearance: none; /* Chrome, Safari, Edge */
            -moz-appearance: none; /* Firefox */
            appearance: none; /* Standard */
        }
        .input-group.invalid input { border: 2px solid red; }
        .input-group small { color: #666; font-size: 12px; display: block; margin-top: 5px; }
        .error { color: red; font-size: 14px; margin-top: 5px; }
        .success { color: #4caf50; font-size: 16px; text-align: center; margin-bottom: 20px; }
        .password-toggle { 
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 40px;
            color: #a27b5c;
            font-size: 14px;
            background: none; /* Ensure no background for icons */
            border: none; /* Ensure no border for icons */
            padding: 0; /* Remove padding that might imply an icon */
        }
        .form-card { max-width: 400px; margin: 40px auto; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 8px; }
        .form-card h2 { color: #333; font-weight: bold; font-size: 24px; margin-bottom: 20px; } /* Reverted to bold, dark gray */
        .primary-btn { background: #a27b5c; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        .primary-btn:disabled { background: #ccc; cursor: not-allowed; }
        .reset-btn { background: #ccc; color: #333; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; margin-top: 10px; }
        .button-container { margin-top: 20px; }
        .loading::after { content: " Loading..."; }
        .links a { color: #a27b5c; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
        .links p { color: #666; }
        footer { padding: 40px 20px; }
        .footer-content { max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between; }
        .newsletter { flex: 1; min-width: 250px; margin-bottom: 20px; }
        .footer-links { flex: 3; display: flex; justify-content: space-between; flex-wrap: wrap; }
        .footer-links div { min-width: 150px; margin-bottom: 20px; }
        .payment-methods { text-align: center; padding: 20px 0; }
        .footer-bottom { text-align: center; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="announcement-bar">BLACK FRIDAY IS HERE! UP TO 50% OFF PLUS MANY COMBINATION DISCOUNTS</div>
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
            <a href="Signup.php">SIGN UP</a>
            <a href="Login.php">LOG IN</a>
            <a href="contact-us.php">CONTACT-US</a>
            <a href="#">CART (0)</a>
        </div>
    </header>

    <main>
        <section class="signup-form-container">
            <div class="form-card">
                <h2>Create an Account</h2>
                <?php if ($success_message): ?>
                    <div class="success"><?php echo $success_message; ?> <a href="Login.php" style="color: #a27b5c;">Login here</a></div>
                <?php else: ?>
                    <form id="signup-form" action="#" method="post" onsubmit="return validateForm()">
                        <?php 
                        $inputs = [
                            'first-name' => ['label' => 'First Name', 'placeholder' => 'e.g., John'],
                            'last-name' => ['label' => 'Last Name', 'placeholder' => 'e.g., Doe'],
                            'signup-email' => ['label' => 'Email Address', 'placeholder' => 'e.g., john.doe@example.com'],
                            'contact-no' => ['label' => 'Contact Number', 'placeholder' => 'e.g., +44 1234567890'],
                            'signup-password' => ['label' => 'Password', 'placeholder' => 'Create a strong password'],
                            'signup-password-confirm' => ['label' => 'Re-enter Password', 'placeholder' => 'Confirm your password']
                        ];
                        foreach ($inputs as $name => $data): ?>
                            <div class="input-group <?php echo isset($error_messages[$name]) ? 'invalid' : ''; ?>">
                                <label for="<?php echo $name; ?>">
                                    <?php echo $data['label']; ?>
                                </label>
                                <input 
                                    type="<?php echo strpos($name, 'password') !== false ? 'password' : 'text'; ?>" 
                                    id="<?php echo $name; ?>" 
                                    name="<?php echo $name; ?>" 
                                    placeholder="<?php echo $data['placeholder']; ?>" 
                                    value="<?php echo htmlspecialchars($form_data[$name] ?? ''); ?>"
                                    required
                                    <?php echo $name === 'signup-email' ? 'oninput="validateEmail(this)"' : ''; ?>
                                    <?php echo $name === 'signup-password' ? 'oninput="validatePassword(this)"' : ''; ?>
                                    <?php echo $name === 'signup-password-confirm' ? 'oninput="validatePasswordConfirm(this)"' : ''; ?>
                                >
                                <?php if (strpos($name, 'password') !== false): ?>
                                    <span class="password-toggle" onclick="togglePassword('<?php echo $name; ?>')">Show</span>
                                <?php endif; ?>
                                <?php if ($name === 'signup-password'): ?>
                                    <small>8-20 characters, including uppercase, lowercase, number, and special character.</small>
                                <?php endif; ?>
                                <?php if (isset($error_messages[$name])): ?>
                                    <div class="error"><?php echo $error_messages[$name]; ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="button-container">
                            <button type="submit" name="submit" class="primary-btn" id="submit-btn">Sign Up</button>
                            <input type="hidden" name="submitted" value="true">
                            <button type="button" class="reset-btn" onclick="confirmReset()">Reset Form</button>
                        </div>
                        <div class="links">
                            <p>Already have an account? <a href="Login.php">Login here</a></p>
                        </div>
                    </form>
                    <?php if (isset($error_messages['general'])): ?>
                        <div class="error" style="text-align: center;"><?php echo $error_messages['general']; ?></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
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
                    <p>500 Terry Francine Street<br>San Francisco, CA 94158<br>info@mysite.com<br>123-456-7890</p>
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
            <img src="images/payment.png" alt="Payment Methods" style="width: auto; height: 30px;">
            <p>These payment methods are for illustrative purposes only. Update this section to show the payment methods your website accepts based on your payment processor(s).</p>
        </div>
        <div class="footer-bottom">
            <p>2024 AU-RA. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.getElementById('first-name').focus(); // Auto-focus first field

        function togglePassword(id) {
            const input = document.getElementById(id);
            const toggle = input.nextElementSibling;
            input.type = input.type === 'password' ? 'text' : 'password';
            toggle.textContent = input.type === 'password' ? 'Show' : 'Hide';
        }

        function validateEmail(input) {
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            const group = input.parentElement;
            group.classList.toggle('invalid', !emailRegex.test(input.value) && input.value !== '');
        }

        function validatePassword(input) {
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,20}$/;
            const group = input.parentElement;
            group.classList.toggle('invalid', !passwordRegex.test(input.value) && input.value !== '');
            validatePasswordConfirm(document.getElementById('signup-password-confirm'));
        }

        function validatePasswordConfirm(input) {
            const password = document.getElementById('signup-password').value;
            const group = input.parentElement;
            group.classList.toggle('invalid', input.value !== password && input.value !== '');
        }

        function validateForm() {
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            return true; // Let PHP handle final validation
        }

        function confirmReset() {
            if (confirm("Are you sure you want to reset the form? All entered data will be lost.")) {
                document.getElementById('signup-form').reset();
            }
        }
    </script>
</body>
</html>