<?php
session_start();
if(isset($_POST['method'])) {
    $_SESSION['payment_method'] = $_POST['method'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No payment method provided']);
}
?>