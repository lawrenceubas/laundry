<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    
    <!-- Favicon -->
    <link rel="icon" href="asset/img/lm.png" type="image/x-icon">

    <style>
       /* General Styling */
       body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f9f9f9;
        }
        .navbar {
            width: 200px;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            height: 100%;
            padding-top: 20px;
            padding-left: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .navbar img {
            width: 60%; /* Adjust the width to make the logo smaller */
            max-width: 120px; /* Set a maximum width for the logo */
            margin-bottom: 20px; /* Add spacing below the logo */
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .navbar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .navbar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin: 5px 0;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .navbar a:hover {
            background-color: #34495e;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
            flex: 1;
        }

        .form-container {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .form-container select, .form-container input, .form-container button {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
        }

        .form-container button {
            background-color: #2c3e50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #34495e;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <!-- Add your image here -->
        <img src="asset/img/lm.png">
        <h2>Laundry Master</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="customers.php">Customers</a>
        <a href="orders.php">Orders</a>
        <a href="supplylist.php">Supply List</a>
        <a href="calendar.php">Calendar</a> 
        
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Order Management</h1>

        <div class="form-container">
            <h2>Add New Order</h2>
            <form method="POST" action="orders.php">
                <select name="customer_id">
                    <?php
                        $customers = $pdo->query("SELECT id, name FROM customers")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($customers as $customer) {
                            echo "<option value='" . $customer['id'] . "'>" . $customer['name'] . "</option>";
                        }
                    ?>
                </select>
                <input type="text" name="service" placeholder="Service (e.g., Wash & Fold)" required>
                <input type="number" step="0.01" name="weight" placeholder="Weight (kg)" required>
                <button type="submit">Add Order</button>
            </form>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerId = $_POST['customer_id'];
            $service = $_POST['service'];
            $weight = $_POST['weight'];

            $stmt = $pdo->prepare("INSERT INTO orders (customer_id, service, weight, status) VALUES (?, ?, ?, 'Pending')");
            $stmt->execute([$customerId, $service, $weight]);

            echo "<p>Order created successfully!</p>";
            header("Refresh:0");
            exit();
        }

        $orders = $pdo->query("
            SELECT orders.id, customers.name AS customer_name, orders.service, orders.weight, orders.status, orders.created_at
            FROM orders
            JOIN customers ON orders.customer_id = customers.id
            ORDER BY orders.created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <h2>All Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Service</th>
                    <th>Weight (kg)</th>
                    <th>Status</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= htmlspecialchars($order['service']) ?></td>
                    <td><?= htmlspecialchars($order['weight']) ?></td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
