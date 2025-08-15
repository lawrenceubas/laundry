<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'database.php';

// === ORDER PLACEMENT ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
    $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_STRING);
    $weight = filter_input(INPUT_POST, 'weight', FILTER_VALIDATE_FLOAT);
    $totalPrice = filter_input(INPUT_POST, 'total_price', FILTER_VALIDATE_FLOAT);
    $inventoryItemIds = $_POST['inventory_item'] ?? [];

    if ($customerId && $service && $weight > 0 && $totalPrice > 0 && is_array($inventoryItemIds) && count($inventoryItemIds) > 0) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO orders (customer_id, service, weight, total_price, status) VALUES (?, ?, ?, ?, 'Pending')");
            $stmt->execute([$customerId, $service, $weight, $totalPrice]);
            $orderId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO payments (order_id, amount, date, status, user_id) VALUES (?, ?, NOW(), 'Paid', ?)");
            $stmt->execute([$orderId, $totalPrice, $_SESSION['user_id']]);

            $stmt = $pdo->prepare("UPDATE inventory SET stock = stock - 1 WHERE id = ?");
            foreach ($inventoryItemIds as $itemId) {
                $stmt->execute([$itemId]);
            }

            $pdo->commit();
            echo "<script>alert('Order placed and inventory updated!'); location.href='orders.php';</script>";
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    } else {
        echo "<script>alert('Invalid input.');</script>";
    }
}

// === FETCH INVENTORY & CUSTOMERS ===
$inventory = $pdo->query("SELECT * FROM inventory")->fetchAll(PDO::FETCH_ASSOC);
$customers = $pdo->query("SELECT * FROM customers")->fetchAll(PDO::FETCH_ASSOC);
$orders = $pdo->query("
    SELECT o.id, c.name as customer, o.service, o.weight, o.total_price, o.status
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
    ORDER BY o.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders - Laundry Master</title>
    <link rel="icon" href="asset/img/icon.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* === (CSS omitted for brevity, same as provided above) === */
    </style>

    <script>
        function openOrdersModal() {
            document.getElementById('ordersModal').style.display = 'block';
        }

        function closeOrdersModal() {
            document.getElementById('ordersModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('ordersModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function calculatePrice() {
            const service = document.getElementById("service").value;
            const weight = parseFloat(document.getElementById("weight").value);
            const prices = { "Wash & Fold": 50, "Dry Clean": 75 };
            if (service && weight > 0) {
                const total = prices[service] * weight;
                document.getElementById("totalPriceDisplay").innerText = "Total Price: ₱" + total.toFixed(2);
                document.getElementById("totalPrice").value = total.toFixed(2);
            } else {
                document.getElementById("totalPriceDisplay").innerText = "Total Price: ₱0.00";
            }
        }
    </script>
</head>

<body>
<style>
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
            background: #ffffff;
        }

        h1, h2 {
            color: #white;
        }

        a {
            text-decoration: none;
            color: #333;
        }

        .form-container,
        .table-container {
            background: white;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        select,
        input,
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            background: #2c3e50;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #34495e;
        }

        input[type="number"] {
            width: calc(100% - 20px);
        }

        /* === TABLE STYLES === */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        /* === MODAL STYLES === */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            padding-top: 80px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 1000px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: fadeIn 0.3s;
        }
        .close-btn {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            border: none;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.3);
            font-size: 16px;
            float: right;
            margin-bottom: 10px;
        }

        .close-btn:hover {
            background-color: #34495e;
        }


        /* === RESPONSIVE FIXES === */
        @media (max-width: 768px) {
            .navbar {
                width: 100px;
                padding: 10px;
            }

            .navbar img {
                width: 80%;
            }

            .navbar h2 {
                font-size: 16px;
            }

            .content {
                margin-left: 120px;
            }

            .modal-content {
                width: 95%;
            }

            select,
            input,
            button {
                font-size: 12px;
            }
        }

        /* === MODAL ANIMATION === */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

</style>
<div class="navbar">
    <img src="asset/img/icon.png" alt="Logo">
    <h2>Cum Laundry</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="customers.php">Customers</a>
    <a href="orders.php">Orders</a>
    <a href="inventory.php">Inventory</a>
    <a href="calendar.php">Calendar</a>
    <a href="logout.php">Logout</a>
</div>

<div class="content">
    <h1>Order Management</h1>

    <div class="form-container">
        <h2>Add New Order</h2>
        <form method="POST">
            <select name="customer_id" required>
                <option value="">Select Customer</option>
                <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="service" id="service" required>
                <option value="">Select Service</option>
                <option value="Wash & Fold">Wash & Fold</option>
                <option value="Dry Clean">Dry Clean</option>
            </select>

            <input type="number" step="0.01" name="weight" id="weight" placeholder="Weight (kg)" required>

            <label for="inventory_item">Select Inventory Items (Hold Ctrl or Cmd to select multiple):</label>
            <select name="inventory_item[]" id="inventory_item" multiple required>
                <?php foreach ($inventory as $item): ?>
                    <option value="<?= $item['id'] ?>">
                        <?= htmlspecialchars($item['item_name']) ?> (Stock: <?= $item['stock'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="button" onclick="calculatePrice()">Calculate Price</button>
            <p id="totalPriceDisplay">Total Price: ₱0.00</p>
            <input type="hidden" name="total_price" id="totalPrice">
            <button type="submit" name="place_order">Place Order</button>
        </form>
    </div>

    <div class="table-container">
        <h2>All Orders</h2>
        <button onclick="openOrdersModal()" style="margin-bottom: 15px;">View All Orders</button>
    </div>

    <div id="ordersModal" class="modal">
        <div class="modal-content">
        <button class="close-btn" onclick="closeOrdersModal()">Close</button>
            <h2>All Orders</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Weight (kg)</th>
                            <th>Total Price (₱)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['customer']) ?></td>
                                <td><?= htmlspecialchars($order['service']) ?></td>
                                <td><?= $order['weight'] ?></td>
                                <td><?= number_format($order['total_price'], 2) ?></td>
                                <td><?= $order['status'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>
