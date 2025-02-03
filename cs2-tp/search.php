<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the query parameter is set
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $search = urlencode($_GET['query']); // Encode the query for URL safety
    header("Location: shop-all.php?query=$search"); // Redirect to shop-all.php
    exit;
} else {
    // If no query, redirect to shop-all.php without a search query
    header("Location: shop-all.php");
    exit;
}
?>




    // SQL query to fetch results
    $sql = "SELECT product.Product_ID, product.Name, product.description, product.Price, image.Image_URL 
            FROM product 
            LEFT JOIN image ON product.Product_ID = image.Product_ID
            WHERE product.Name LIKE ? OR product.description LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h1>Search Results for '" . htmlspecialchars($search) . "'</h1>";
    echo "<div class='search-results'>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='search-result-item'>";
            echo "<div class='product-image'>";
            if (!empty($row['Image_URL'])) {
                echo "<img src='" . htmlspecialchars($row['Image_URL']) . "' alt='" . htmlspecialchars($row['Name']) . "'>";
            } else {
                echo "<img src='placeholder.png' alt='No image available'>"; // Fallback image
            }
            echo "</div>";
            echo "<div class='product-details'>";
            echo "<h2>" . htmlspecialchars($row['Name']) . "</h2>";
            echo "<p>" . htmlspecialchars($row['description']) . "</p>";
            echo "<p><strong>Price: $" . htmlspecialchars($row['Price']) . "</strong></p>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No products found.</p>";
    }
    echo "</div>";


$conn->close();
?>