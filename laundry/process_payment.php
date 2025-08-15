<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $paymentMethod = $_POST['payment_method'];
    $amount = $_POST['amount'];
    $status = 'Completed'; // Assuming payment is successful

    // Prepare and execute the query to insert the payment
    $query = "INSERT INTO payments (user_id, amount, status) VALUES (:user_id, :amount, :status)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'user_id' => $userId,
        'amount' => $amount,
        'status' => $status
    ]);

    // Redirect back to the dashboard or any page
    header("Location: dashboard.php");
    exit();
}
?>