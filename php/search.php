<?php
//Server name is localhost
$servername = "localhost";
//In my case, User name is root
$username = "cs2team40";
//password is empty
$password = "q6jxdg3oFxNuvVi";
//database name is aura
$database = "cs2team40_aura";
// Creating a connection
$conn = new mysqli($servername,$username,$password,$database);
// check connection
if ($conn->connect_error){
    die("Connection failure: ". $conn->connect_error);
}
// Get the search query
if (isset($_GET['query'])) {
    $search = $_GET['query'];

    // Prepare and execute the SQL query
    $sql = "SELECT product.Product_ID, product.Name, product.description, product.Price, image.Image_URL 
            FROM product 
            LEFT JOIN image ON product.Product_ID = image.Product_ID
            WHERE product.Name LIKE ? OR product.description LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display results
    echo "<h1>Search Results for '" . htmlspecialchars($search) . "'</h1>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<h2>" . htmlspecialchars($row['Name']) . "</h2>";
            echo "<p>" . htmlspecialchars($row['description']) . "</p>";
            echo "<p>Price: $" . htmlspecialchars($row['Price']) . "</p>";
            if (!empty($row['Image_URL'])) {
                echo "<img src='" . htmlspecialchars($row['Image_URL']) . "' alt='" . htmlspecialchars($row['Name']) . "' style='max-width:200px;'>";
            }
            echo "</div><hr>";
        }
    } else {
        echo "<p>No products found.</p>";
    }
}

$conn->close();
?>
