<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');

// Check if an order ID is provided
if (!isset($_GET['id'])) {
    die("Order ID not specified.");
}

$order_id = $_GET['id'];

// Fetch the order details from the database
$query = "SELECT * FROM orders WHERE id = :order_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['order_id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the order exists
if (!$order) {
    die("Order not found.");
}

// Fetch customer names for the dropdown
$customers = $pdo->query("SELECT id, name FROM customers")->fetchAll(PDO::FETCH_ASSOC);

// Update the order if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $customer_id = $_POST['customer_id'];
    $service = $_POST['service'];
    $weight = $_POST['weight'];
    $total_price = $_POST['total_price'];
    
    // Update the order in the database
    $update_query = "UPDATE orders SET customer_id = :customer_id, service = :service, weight = :weight, total_price = :total_price WHERE id = :order_id";
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->execute([
        'customer_id' => $customer_id,
        'service' => $service,
        'weight' => $weight,
        'total_price' => $total_price,
        'order_id' => $order_id
    ]);
    
    // Redirect to the orders page after updating
    header("Location: orders.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link rel="icon" href="asset/img/lm.png" type="image/x-icon">
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            margin: 40px auto;
            max-width: 600px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #34495e;
        }
        label {
            font-size: 16px;
            color: #34495e;
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
        }
        input, select {
            width: 90%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }
        button {
            width: 94%;
            padding: 10px;
            background-color: #2c3e50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #34495e;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>

<!-- Edit Order Form -->
<div class="container">
    <h2>Edit Order</h2>
    <form action="edit_order.php?id=<?= $order_id ?>" method="POST">
        <label for="customer_id">Customer:</label>
        <select name="customer_id" id="customer_id" required>
            <?php foreach ($customers as $customer): ?>
                <option value="<?= $customer['id'] ?>" <?= $customer['id'] == $order['customer_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($customer['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="service">Service:</label>
        <input type="text" name="service" id="service" value="<?= htmlspecialchars($order['service']) ?>" required>

        <label for="weight">Weight (kg):</label>
        <input type="number" name="weight" id="weight" value="<?= htmlspecialchars($order['weight']) ?>" required min="1">

        <label for="total_price">Total Price (₱):</label>
        <input type="number" name="total_price" id="total_price" value="<?= htmlspecialchars($order['total_price']) ?>" required min="1">

        <button type="submit">Save Changes</button>
    </form>
</div>

</body>
</html>
