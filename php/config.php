<?php

$host = "localhost";
$username = "root";  // Your MySQL username
$password = "";      // Your MySQL password
$database = "aura";  // Your database name

try {
    // Correctly formatted DSN string
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
 
} catch(PDOException $ex) {
    // Handle connection errors
    echo "Failed to connect to the database: " . $ex->getMessage();
    exit;
}
?>











