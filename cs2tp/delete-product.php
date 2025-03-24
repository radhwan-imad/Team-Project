<?php
if (isset($_GET["Product_ID"])) {
    $Product_ID = $_GET["Product_ID"];

    include('connection.php');

    // Start transaction to ensure atomicity
    $conn->begin_transaction();

    try {
        // Delete related images
        $stmt = $conn->prepare("DELETE FROM image WHERE Product_ID = ?");
        $stmt->bind_param("i", $Product_ID);
        $stmt->execute();
        $stmt->close();

        // Delete related reviews
        $stmt = $conn->prepare("DELETE FROM review WHERE Product_ID = ?");
        $stmt->bind_param("i", $Product_ID);
        $stmt->execute();
        $stmt->close();

        // Delete from cart items
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE Product_ID = ?");
        $stmt->bind_param("i", $Product_ID);
        $stmt->execute();
        $stmt->close();

        // Delete from order items
        $stmt = $conn->prepare("DELETE FROM order_items WHERE Product_ID = ?");
        $stmt->bind_param("i", $Product_ID);
        $stmt->execute();
        $stmt->close();

        // Delete from product notes
        $stmt = $conn->prepare("DELETE FROM product_notes WHERE Product_ID = ?");
        $stmt->bind_param("i", $Product_ID);
        $stmt->execute();
        $stmt->close();

        // Finally, delete the product
        $stmt = $conn->prepare("DELETE FROM product WHERE Product_ID = ?");
        $stmt->bind_param("i", $Product_ID);
        $stmt->execute();

        // Check if the product deletion was successful
        if ($stmt->affected_rows > 0) {
            $conn->commit(); // Commit the transaction if everything is successful
            $stmt->close();
            $conn->close();
            header('Location: manage-products.php');
            exit;
        } else {
            throw new Exception("Error deleting record: " . $conn->error);
        }
    } catch (Exception $e) {
        $conn->rollback(); // Rollback the transaction on error
        echo "Transaction failed: " . $e->getMessage();
    }

    $conn->close();
} else {
    // Redirect to Adminoption.php if Product_ID is not set
    header('Location: Adminoption.php');
    exit;
}
?>
