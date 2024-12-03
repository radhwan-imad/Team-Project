<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start the session if it hasn't been started yet
}
// Connect to database
require "config.php";

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
        echo '<div class="alert alert-danger absolute center text-center" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
              </button>
              <span class="text-danger">Enter new password!</span>
              </div>';
    } elseif (empty($confirm)) {
        echo '<div class="alert alert-danger absolute center text-center" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
              </button>
              <span class="text-danger">Confirm new password!</span>
              </div>';
    } elseif ($new !== $confirm) {
        echo '<div class="alert alert-danger absolute center text-center" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">×</span>
              </button>
              <span class="text-danger">Password did not match!</span>
              </div>';
    } else {
        // Hash the password
        $pass = password_hash($new, PASSWORD_DEFAULT);
        
        // Execute the SQL query
        try {
            $sql = "UPDATE users SET password = ?, activation = NULL WHERE Email_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$pass, $email]);  // Bind parameters and execute

            // Check if the password update was successful
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "true";
                header('Location: login.php');
                exit();
            } else {
                echo '<div class="alert alert-danger absolute center text-center" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">×</span>
                      </button>
                      <span class="text-danger">Oops! Your account could not be activated. Please recheck the link or contact the system administrator.</span>
                      </div>';
            }
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger absolute center text-center" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">×</span>
                  </button>
                  <span class="text-danger">Database error: ' . $e->getMessage() . '</span>
                  </div>';
        }
    }
}
?>