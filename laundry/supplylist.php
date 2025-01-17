<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Management System - Supply List</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f9f9f9;
        }
        /* Sidebar Styles */
        .sidebar {
            width: 200px;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            height: 100%;
            padding-top: 20px;
            padding-left: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar .logo-container {
            width: 60%;
            max-width: 120px;
            margin: 20px auto;   
            display: block;
        }
        .sidebar .logo {
            width: 60%;
            max-width: 120px;
            margin: 20px auto;
            display: block;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin: 5px 0;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #34495e;
        }
        main {
            margin-left: 220px;
            padding: 20px;
            flex: 1;
        }
        .form-container, .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .stock-summary {
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: bold;
            color: #333;
        }
        form input, form button {
            margin: 5px 0;
            padding: 10px;
            width: 100%;
            display: block;
        }
        form button {
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #333;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="asset/img/lm.png" alt="Logo" class="logo">
        </div>
        <h2>Laundry Master</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="customers.php">Customers</a>
        <a href="orders.php">Orders</a>
        <a href="supplylist.php" class="active">Supply List</a>
        <a href="calendar.php">Calendar</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <main>
        <?php
        // Initialize or fetch the supplies array
        session_start();
        if (!isset($_SESSION['supplies'])) {
            $_SESSION['supplies'] = [];
        }

        $supplies = $_SESSION['supplies'];

        // Handle form submission to add supplies
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_supply'])) {
            $item = htmlspecialchars($_POST['item']);
            $quantity = intval($_POST['quantity']);
            $supplies[] = ['item_name' => $item, 'quantity' => $quantity];
            $_SESSION['supplies'] = $supplies; // Update session
        }

        // Function to reduce stock when an order is washed
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_order'])) {
            // Example: Deducting stock for detergents
            $detergent_used = $_POST['detergent_quantity']; // Assuming it's passed as a form input
            $supply_found = false;

            // Update stock by reducing detergent usage
            foreach ($supplies as &$supply) {
                if ($supply['item_name'] == 'Detergent') {
                    if ($supply['quantity'] >= $detergent_used) {
                        $supply['quantity'] -= $detergent_used;
                        $supply_found = true;
                    } else {
                        echo "Not enough detergent stock.";
                    }
                    break;
                }
            }

            // Update session with new supplies array
            if ($supply_found) {
                $_SESSION['supplies'] = $supplies;
            }
        }
        ?>

        <div class="form-container">
            <form method="POST" action="">
                <input type="text" name="item" placeholder="Item Name" required>
                <input type="number" name="quantity" placeholder="Quantity" required>
                <button type="submit" name="add_supply">Add Item</button>
            </form>
        </div>

        <!-- Stock Summary -->
        <div class="stock-summary">
            <?php 
            $totalQuantity = array_sum(array_column($supplies, 'quantity')); 
            echo "Total Stock Count: " . $totalQuantity;
            ?>
        </div>

        <!-- Supply List Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($supplies)): ?>
                        <?php foreach ($supplies as $supply): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($supply['item_name']); ?></td>
                                <td><?php echo htmlspecialchars($supply['quantity']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No supplies available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Process Order Form (Example: Deduct detergent usage) -->
        <div class="form-container">
            <form method="POST" action="">
                <h3>Process Order (Wash)</h3>
                <label for="detergent_quantity">Detergent Quantity Used:</label>
                <input type="number" id="detergent_quantity" name="detergent_quantity" required>
                <button type="submit" name="process_order">Process Order</button>
            </form>
        </div>
    </main>
</body>
</html>
