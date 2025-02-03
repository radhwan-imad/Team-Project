<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if it hasn't been started yet
}

// Connect to database
require "connection.php"; // Ensure this file connects to the database properly

// Initialize variables
$new = $confirm = $email = $key = "";

if (isset($_POST['submit'])) {
    // Fetch POST values
    $new = isset($_POST['new-password']) ? $_POST['new-password'] : "";
    $confirm = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : "";
    $email = isset($_POST['email']) ? $_POST['email'] : "";  
    $key = isset($_POST['key']) ? $_POST['key'] : "";  
    
    // Validate input
    if (empty($new)) {
        echo '<div class="alert alert-danger">Enter new password!</div>';
    } elseif (empty($confirm)) {
        echo '<div class="alert alert-danger">Confirm new password!</div>';
    } elseif ($new !== $confirm) {
        echo '<div class="alert alert-danger">Password did not match!</div>';
    } else {
        // Hash the password
        $pass = password_hash($new, PASSWORD_DEFAULT);
        
        // Execute the SQL query
        try {
            $sql = "UPDATE users SET password = ?, activation = NULL WHERE Email_ID = ?";
            $stmt = $conn->prepare($sql);
            // Bind parameters for MySQLi
            $stmt->bind_param("ss", $pass, $email); // "ss" means two string parameters
            $stmt->execute();

            // Check if the password update was successful
            if ($stmt->affected_rows > 0) {
                $_SESSION['success'] = "true";
                header('Location: login.php');
                exit();
            } else {
                echo '<div class="alert alert-danger">Oops! Your account could not be activated. Please recheck the link or contact the system administrator.</div>';
            }
        } catch (mysqli_sql_exception $e) {
            echo '<div class="alert alert-danger">Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}
?>