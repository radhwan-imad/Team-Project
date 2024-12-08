<?php
session_start();
require_once("connection.php"); // Ensure this is the first thing that runs after session start


// Get search query
if (isset($_GET['query'])) {
    $search = $_GET['query'];

    // Prepare SQL query
    $query = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($query);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display results
    echo "<h1>Search Results for '$search'</h1>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<h2>" . $row['name'] . "</h2>";
            echo "<p>" . $row['description'] . "</p>";
            echo "<p>Price: $" . $row['price'] . "</p>";
            echo "<img src='" . $row['image_path'] . "' alt='" . $row['name'] . "' style='max-width: 200px;'>";
            echo "</div>";
        }
    } else {
        echo "<p>No products found.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Products</title>
</head>
<body>
    <h1>Product Search</h1>
    <form method="get" action="search.php">
        <input type="text" name="query" placeholder="Search for products..." required>
        <button type="submit">Search</button>
    </form>
</body>
</html>
