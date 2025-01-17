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
    <title>Dashboard</title>
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
            width: 60%;
            max-width: 120px;
            margin-bottom: 20px;
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
        .header {
            font-size: 24px;
            margin-bottom: 20px;
            color: #34495e;
        }
        .dashboard-content {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .summary-box {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            min-width: 250px;
            text-align: center;
            color: #34495e;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .summary-box:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .summary-box h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .summary-box p {
            font-size: 32px;
            margin: 0;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
            font-size: 16px;
        }
        th {
            background-color: #f4f4f4;
            color: #34495e;
        }
        td {
            background-color: white;
        }
        .footer {
            font-size: 14px;
            color: #7f8c8d;
            text-align: center;
            padding: 20px;
            margin-top: 30px;
        }
        @media (max-width: 768px) {
            .navbar {
                width: 100%;
                height: auto;
                position: static;
                padding: 10px;
            }
            .content {
                margin-left: 0;
            }
            .summary-box {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>

<!-- Left-Side Navbar -->
<div class="navbar">
    <img src="asset/img/lm.png" alt="Laundry Master Logo">
    <h2>Laundry Master</h2>
    <a href="dashboard.php" style="background-color: #34495e;">Dashboard</a>
    <a href="customers.php">Customers</a>
    <a href="orders.php">Orders</a>
    <a href="supplylist.php">Supply List</a>
    <a href="calendar.php">Calendar</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <div class="header">
        Welcome, <?= htmlspecialchars($_SESSION['username']) ?>! | Dashboard
    </div>

    <?php
    // Total summary data
    $totalCustomers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
    $totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $totalServices = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
    $totalWeight = $pdo->query("SELECT SUM(weight) FROM orders")->fetchColumn();
    $totalCompletedOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Completed'")->fetchColumn();
    $totalExpenses = $pdo->query("SELECT SUM(amount) FROM expenses")->fetchColumn();

    // Sales Today
    $salesToday = $pdo->query("SELECT SUM(total_price) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    ?>

    <!-- Summary Cards -->
    <div class="dashboard-content">
        <div class="summary-box">
            <h3>Total Customers</h3>
            <p><?= $totalCustomers ?></p>
        </div>
        <div class="summary-box">
            <h3>Total Orders</h3>
            <p><?= $totalOrders ?></p>
        </div>
        <div class="summary-box">
            <h3>Total Services</h3>
            <p><?= $totalServices ?></p>
        </div>
        <div class="summary-box">
            <h3>Total Weight (kg)</h3>
            <p><?= number_format($totalWeight, 2) ?> kg</p>
        </div>
        <div class="summary-box">
            <h3>Total Completed Orders</h3>
            <p><?= $totalCompletedOrders ?></p>
        </div>
        <div class="summary-box">
            <h3>Total Expenses</h3>
            <p>₱<?= number_format($totalExpenses, 2) ?></p>
        </div>
        <div class="summary-box">
            <h3>Sales Today</h3>
            <p>₱<?= number_format($salesToday, 2) ?></p>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <h2>Recent Orders</h2>
    <?php
    $orders = $pdo->query("SELECT orders.id, customers.name AS customer_name, orders.service, orders.weight, orders.created_at, orders.total_price FROM orders JOIN customers ON orders.customer_id = customers.id ORDER BY orders.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Service</th>
                <th>Weight (kg)</th>
                <th>Total Price</th>
                <th>Order Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= htmlspecialchars($order['service']) ?></td>
                    <td><?= htmlspecialchars($order['weight']) ?></td>
                    <td>₱<?= number_format($order['total_price'], 2) ?></td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                    <td>
                        <a href="edit_order.php?id=<?= htmlspecialchars($order['id']) ?>">Edit</a> |
                        <a href="complete_order.php?id=<?= htmlspecialchars($order['id']) ?>">Complete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>