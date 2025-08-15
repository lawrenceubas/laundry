<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the customer_id is provided
if (isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];

    // Include the database connection
    include('database.php');

    try {
        // Prepare the delete query (make sure we include user_id to prevent unauthorized access)
        $query = "DELETE FROM customers WHERE id = :customer_id AND user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['customer_id' => $customer_id, 'user_id' => $_SESSION['user_id']]);

        // Redirect back to the customers page with a success message
        header("Location: customers.php?message=Customer+deleted+successfully");
        exit();
    } catch (PDOException $e) {
        // If there is an error, show a message
        echo "Error deleting customer: " . $e->getMessage();
    }
} else {
    // If no customer_id is provided, redirect to the customers page
    header("Location: customers.php");
    exit();
}
?>
