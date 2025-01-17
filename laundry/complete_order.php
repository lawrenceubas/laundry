<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];

    // Update the order status to 'Completed'
    $stmt = $pdo->prepare("UPDATE orders SET status = 'Completed' WHERE id = ?");
    $stmt->execute([$orderId]);

    // Redirect back to the dashboard or order page
    header("Location: dashboard.php");
    exit();
}
?>
