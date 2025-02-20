<?php
session_start();

// Initialize variables
$error_messages = [];
$form_data = $_SESSION['form_data'] ?? [];

if (isset($_POST['submitted'])) {
    require_once('connection.php');

    // Sanitize inputs
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

    // Specific validations
    if (!empty($input['signup-email']) && !filter_var($input['signup-email'], FILTER_VALIDATE_EMAIL)) {
        $error_messages['signup-email'] = "Please enter a valid email (e.g., name@example.com).";
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

    // Check for existing email
    if (empty($error_messages)) {
        $verify_query = $conn->prepare("SELECT Email_ID FROM users WHERE Email_ID = ?");
        $verify_query->bind_param("s", $input['signup-email']);
        $verify_query->execute();
        if ($verify_query->get_result()->num_rows > 0) {
            $error_messages['signup-email'] = "Email is already taken!";
        }
    }

    // Process form if no errors
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

            echo "<script>alert('Congratulations! You are now registered. Please Login now'); window.location.href = 'Login.php';</script>";
            exit;
        } catch (mysqli_sql_exception $ex) {
            $error_messages['general'] = "Database error occurred!";
        }
    }

    // Store errors and data in session
    $_SESSION['error_messages'] = $error_messages;
    $_SESSION['form_data'] = $_POST;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Retrieve errors and clear session
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
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; }
        .input-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        .input-group.invalid input { border: 2px solid red; }
        .input-group small { color: grey; font-size: 12px; }
        .error { color: red; font-size: 14px; margin-top: 5px; }
        .password-toggle { cursor: pointer; margin-left: 10px; }
    </style>
</head>
<body>
    <!-- Same header and announcement bar as before -->
    <div class="announcement-bar">BLACK FRIDAY IS HERE! UP TO 50% OFF PLUS MANY COMBINATION DISCOUNTS</div>
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
            <a href="#">CART (0)</a>
        </div>
    </header>

    <main>
        <section class="signup-form-container">
            <div class="form-card">
                <h2>Create an Account</h2>
                <form action="#" method="post">
                    <?php 
                    $inputs = [
                        'first-name' => 'First Name',
                        'last-name' => 'Last Name',
                        'signup-email' => 'Email Address',
                        'contact-no' => 'Contact Number',
                        'signup-password' => 'Password',
                        'signup-password-confirm' => 'Re-enter Password'
                    ];
                    foreach ($inputs as $name => $label): ?>
                        <div class="input-group <?php echo isset($error_messages[$name]) ? 'invalid' : ''; ?>">
                            <label for="<?php echo $name; ?>"><?php echo $label; ?></label>
                            <input 
                                type="<?php echo strpos($name, 'password') !== false ? 'password' : 'text'; ?>" 
                                id="<?php echo $name; ?>" 
                                name="<?php echo $name; ?>" 
                                placeholder="Enter your <?php echo strtolower($label); ?>" 
                                value="<?php echo htmlspecialchars($form_data[$name] ?? ''); ?>"
                                required
                                <?php echo $name === 'signup-email' ? 'pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"' : ''; ?>
                                <?php echo $name === 'contact-no' ? 'pattern="\+?[0-9]{1,4}?[0-9\s\-]{7,15}$"' : ''; ?>
                            >
                            <?php if (strpos($name, 'password') !== false): ?>
                                <span class="password-toggle" onclick="togglePassword('<?php echo $name; ?>')">Show</span>
                            <?php endif; ?>
                            <?php if ($name === 'signup-password'): ?>
                                <small></small>
                            <?php endif; ?>
                            <?php if (isset($error_messages[$name])): ?>
                                <div class="error"><?php echo $error_messages[$name]; ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="button-container">
                        <button type="submit" name="submit" class="primary-btn">Sign Up</button>
                        <input type="hidden" name="submitted" value="true">
                    </div>
                    <div class="links">
                        <p>Already have an account? <a href="Login.php">Login here</a>.</p>
                    </div>
                </form>
                <?php if (isset($error_messages['general'])): ?>
                    <div class="error" style="text-align: center;"><?php echo $error_messages['general']; ?></div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Same footer as before -->
    <footer>
        <!-- Footer content unchanged -->
    </footer>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const toggle = input.nextElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                toggle.textContent = 'Hide';
            } else {
                input.type = 'password';
                toggle.textContent = 'Show';
            }
        }
    </script>
</body>
</html>
