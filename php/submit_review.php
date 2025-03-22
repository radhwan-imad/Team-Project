<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['User_ID'])) {
    echo json_encode(["success" => false, "message" => "User not logged in!"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['User_ID'];
    $product_id = $_POST['product_id'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $comment = $_POST['comment'] ?? '';

    if (empty($product_id) || empty($rating) || empty($comment)) {
        echo json_encode(["success" => false, "message" => "All fields are required!"]);
        exit();
    }

    if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        echo json_encode(["success" => false, "message" => "Invalid rating value!"]);
        exit();
    }

    $insertStmt = $conn->prepare("INSERT INTO review (Product_ID, User_ID, Rating, Review_Text) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);

    if ($insertStmt->execute()) {
        echo json_encode(["success" => true, "message" => "Review submitted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to submit review. Try again!"]);
    }

    $insertStmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request!"]);
}
?>