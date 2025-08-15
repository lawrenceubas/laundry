<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');

// Fetch all summary data in one query
$query = "
    SELECT 
        (SELECT COUNT(*) FROM customers) AS total_customers,
        (SELECT COUNT(*) FROM orders) AS total_orders,
        (SELECT COUNT(*) FROM services) AS total_services,
        (SELECT SUM(weight) FROM orders) AS total_weight,
        (SELECT COUNT(*) FROM orders WHERE status = 'Completed') AS total_completed_orders,
        (SELECT SUM(amount) FROM expenses) AS total_expenses,
        (SELECT SUM(total_price) FROM orders WHERE DATE(created_at) = CURDATE()) AS sales_today";
$stmt = $pdo->query($query);
$summaryData = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch payment history
$queryPayments = "SELECT transaction_id, date, amount, status FROM payments WHERE user_id = :user_id ORDER BY date DESC";
$stmt = $pdo->prepare($queryPayments);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$paymentHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent orders
$queryOrders = "
    SELECT 
        orders.id, 
        customers.name AS customer_name, 
        orders.service, 
        orders.weight, 
        orders.created_at, 
        orders.total_price, 
        orders.status 
    FROM orders 
    JOIN customers ON orders.customer_id = customers.id 
    ORDER BY orders.created_at DESC 
    LIMIT 5";
$orders = $pdo->query($queryOrders)->fetchAll(PDO::FETCH_ASSOC);

// Fetch low stock items$lowStockQuery = "SELECT item_name, quantity FROM inventory WHERE quantity <= 5 ORDER BY quantity ASC";
$lowStockQuery = "SELECT item_name, quantity FROM inventory WHERE quantity <= 5 ORDER BY quantity ASC";
$lowStockItems = $pdo->query($lowStockQuery)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="icon" href="asset/img/icon.png" sizes="18x18" type="image/x-icon">
    <style>
        /* (CSS unchanged from your original post) */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; display: flex; background-color: #f9f9f9; }
        .navbar { width: 200px; background-color: #2c3e50; color: white; position: fixed; height: 100%; padding-top: 20px; padding-left: 20px; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); }
        .navbar img { width: 60%; max-width: 120px; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto; }
        .navbar h2 { text-align: center; margin-bottom: 20px; }
        .navbar a { display: block; color: white; text-decoration: none; padding: 10px 15px; margin: 5px 0; border-radius: 5px; transition: background-color 0.3s ease; }
        .navbar a:hover { background-color: #34495e; }
        .content { margin-left: 220px; padding: 20px; flex: 1; }
        .header { font-size: 24px; margin-bottom: 20px; color: #34495e; }
        .dashboard-content { display: flex; flex-wrap: wrap; gap: 20px; }
        .summary-box { background-color: white; padding: 25px; border-radius: 8px; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1); min-width: 250px; text-align: center; color: #34495e; transition: 0.3s; }
        .summary-box:hover { transform: scale(1.05); }
        .summary-box h3 { font-size: 18px; margin-bottom: 10px; }
        .summary-box p { font-size: 32px; font-weight: bold; margin: 0; }
        .recent-orders-table { width: 100%; border-collapse: collapse; margin-top: 40px; background-color: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1); }
        .recent-orders-table thead { background-color: #f7f9fc; }
        .recent-orders-table th, .recent-orders-table td { padding: 14px 16px; text-align: left; font-size: 15px; border-bottom: 1px solid #eee; }
        .recent-orders-table tbody tr:hover { background-color: #f0f8ff; }
        .action-btn { padding: 6px 12px; margin: 0 4px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .edit-btn { background-color: #3498db; color: white; }
        .complete-btn { background-color: #2ecc71; color: white; }
        .delete-btn { background-color: #e74c3c; color: white; padding: 6px 10px; border-radius: 5px; text-decoration: none; }
        button { padding: 10px 20px; background-color: #2c3e50; color: white; font-size: 16px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #34495e; }
        .popup { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); padding-top: 60px; }
        .popup-content { background-color: #fff; margin: auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 12px; max-height: 80vh; overflow-y: auto; }
        .popup-content table { width: 100%; border-collapse: collapse; }
        .popup-content th, .popup-content td { padding: 10px; border: 1px solid #ddd; }
        .popup-content th { background-color: #f4f4f4; }
        .close-btn { float: right; font-size: 20px; background: none; border: none; color: red; cursor: pointer; }
    </style>
</head>
<body>
<div class="navbar">
    <img src="asset/img/icon.png" alt="Laundry Master Logo">
    <h2>Cum Laundry</h2>
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="customers.php">Customers</a>
    <a href="orders.php">Orders</a>
    <a href="Inventory.php">Inventory</a>
    <a href="calendar.php">Calendar</a>
    <a href="logout.php">Logout</a>
</div>

<div class="content">
    <div class="header">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>! | Dashboard</div>
    <div class="dashboard-content">
        <div class="summary-box">
            <h3>Total Customers</h3>
            <p><?= $summaryData['total_customers'] ?></p>
        </div>
        <div class="summary-box">
            <h3>Total Orders</h3>
            <p><?= $summaryData['total_orders'] ?></p>
        </div>
        <div class="summary-box">
            <h3>Total Weight (kg)</h3>
            <p><?= number_format($summaryData['total_weight'], 2) ?></p>
        </div>
        <div class="summary-box">
            <h3>Total Completed Orders</h3>
            <p><?= $summaryData['total_completed_orders'] ?></p>
        </div>
        <div class="summary-box">
            <h3>Total Sales Today</h3>
            <p>₱<?= number_format($summaryData['sales_today'] ?? 0, 2) ?></p>
        </div>
        <div class="summary-box" style="background-color: #ffe6e6;">
            <h3>Low Stock Items</h3>
            <p><?= count($lowStockItems) ?> item(s)</p>
            <button onclick="document.getElementById('lowStockPopup').style.display='block'" style="margin-top:10px; background-color:#e74c3c;">View</button>
        </div>
    </div><br><br>

    <button onclick="document.getElementById('paymentHistoryPopup').style.display='block'">View Payment History</button>
    <div id="paymentHistoryPopup" class="popup">
        <div class="popup-content">
            <button class="close-btn" onclick="document.getElementById('paymentHistoryPopup').style.display='none'">×</button>
            <h3>Payment History</h3>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($paymentHistory as $payment): ?>
                        <tr>
                            <td><?= htmlspecialchars($payment['transaction_id']) ?></td>
                            <td><?= htmlspecialchars($payment['date']) ?></td>
                            <td>₱<?= number_format($payment['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($payment['status']) ?></td>
                            <td>
                                <a href="delete-payment.php?transaction_id=<?= htmlspecialchars($payment['transaction_id']) ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="lowStockPopup" class="popup">
        <div class="popup-content">
            <button class="close-btn" onclick="document.getElementById('lowStockPopup').style.display='none'">×</button>
            <h3>Low Stock Alerts</h3>
            <?php if (count($lowStockItems) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Available Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStockItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item_name']) ?></td>
                                <td style="color: red; font-weight: bold;"><?= $item['stocks'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>All items are sufficiently stocked.</p>
            <?php endif; ?>
        </div>
    </div>

    <h2>Recent Orders</h2>
    <table class="recent-orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Service</th>
                <th>Weight (kg)</th>
                <th>Total Price</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= htmlspecialchars($order['service']) ?></td>
                    <td><?= htmlspecialchars($order['weight']) ?></td>
                    <td>₱<?= number_format($order['total_price'], 2) ?></td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td>
                        <a href="edit_order.php?id=<?= $order['id'] ?>" class="action-btn edit-btn">Edit</a>
                        <?php if ($order['status'] !== 'Completed'): ?>
                            <a href="javascript:void(0);" onclick="completeOrder(<?= $order['id'] ?>)" class="action-btn complete-btn">Complete</a>
                        <?php else: ?>
                            <span style="color: #27ae60;">Completed</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function completeOrder(orderId) {
    if (confirm("Mark this order as completed?")) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "complete_order.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status === 200) {
                alert("Order marked as completed.");
                location.reload();
            } else {
                alert("Error completing order.");
            }
        };
        xhr.send("order_id=" + orderId);
    }
}
</script>
</body>
</html>
