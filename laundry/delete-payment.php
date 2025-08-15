<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the transaction_id is provided
if (isset($_GET['transaction_id'])) {
    $transaction_id = $_GET['transaction_id'];

    // Include the database connection
    include('database.php');

    try {
        // Prepare the delete query
        $query = "DELETE FROM payments WHERE transaction_id = :transaction_id AND user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['transaction_id' => $transaction_id, 'user_id' => $_SESSION['user_id']]);

        // Redirect back to the dashboard or payment history page after successful deletion
        header("Location: dashboard.php?message=Payment+deleted+successfully");
        exit();
    } catch (PDOException $e) {
        // If there is an error, show a message
        echo "Error deleting payment: " . $e->getMessage();
    }
} else {
    // If no transaction_id is provided, redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}
?>