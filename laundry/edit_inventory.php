<?php
session_start();

// Check if form data is received
if (isset($_POST['item']) && isset($_POST['quantity'])) {
    $item = $_POST['item'];
    $quantity = (int)$_POST['quantity'];

    // Update the inventory in the session
    if (isset($_SESSION['inventory'][$item])) {
        $_SESSION['inventory'][$item] = $quantity;
    }
}

// Redirect back to the inventory page
header('Location: inventory.php');
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Management System - Inventory</title>
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

        /* Main Content */
        main {
            margin-left: 250px;
            width: 100%;
        }

        header {
            background-color: #1C325B;
            color: white;
            text-align: center;
        }

        .header-container {
            display: flex;
            align-items: center;
        }

        header h1 {
            flex-grow: 1;
        }

        .back-btn {
            background-color: #ffffff;
            color: #1C325B;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .back-btn:hover {
            background-color: #f0f0f0;
        }

        .inventory {
            max-width: 1000px;
            margin: 0 auto;
        }

        .inventory h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5em;
            color: #1C325B;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #1C325B;
            color: white;
        }

        table td {
            background-color: #f9f9f9;
        }

        table tr:nth-child(even) td {
            background-color: #f2f2f2;
        }

        table tr:hover td {
            background-color: #d9f7d9;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .actions button {
            padding: 5px 10px;
            background-color: #1C325B;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .actions button:hover {
            background-color: #14324b;
        }

        /* Edit Form */
        .edit-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 300px;
            margin: 0 auto;
        }

        .edit-form input, .edit-form select {
            padding: 10px;
            font-size: 1em;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .edit-form button {
            padding: 10px;
            background-color: #1C325B;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-form button:hover {
            background-color: #14324b;
        }

        .save-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #1C325B;
            color: white;
            font-size: 1.2em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .save-btn:hover {
            background-color: #14324b;
        }
    </style>
</head>
<body>
    <!-- Left-Side Navbar -->
    <div class="navbar">
        <img src="asset/img/lm.png" alt="Laundry Master Logo">
        <h2>Cum Laundry</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="customers.php">Customers</a>
        <a href="orders.php">Orders</a>
        <a href="inventory.php">Inventory</a>
        <a href="calendar.php">Calendar</a>
        <a href="logout.php">Logout</a>
    </div>
    <main>
        <header>
            <div class="header-container">
                <h1>Laundry Management Inventory</h1>
            </div>
        </header>

        <section class="inventory">
            <h2>Inventory List</h2>

            <!-- Edit Inventory Form -->
            <div id="edit-form-container" style="display: none;">
                <h3>Edit Item</h3>
                <form class="edit-form" id="edit-form">
                    <input type="text" id="edit-item" placeholder="Item Name" required>
                    <input type="number" id="edit-quantity" placeholder="Quantity" required>
                    <select id="edit-status" required>
                        <option value="In Stock">In Stock</option>
                        <option value="Low Stock">Low Stock</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>
                    <button type="submit">Save Changes</button>
                </form>
            </div>

            <!-- View Inventory Button -->
            <button class="save-btn" onclick="openPopup()">View Inventory</button>

            <div id="inventory-table-container" style="display: block;">
                <!-- Inventory Table -->
                <table id="inventory-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inventory-list">
                        <!-- Example supply list row -->
                        <tr>
                            <td>Detergent</td>
                            <td>50 bottles</td>
                            <td>In Stock</td>
                            <td class="actions">
                                <button onclick="editInventory(this)">Edit</button>
                                <button onclick="deleteInventory(this)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        const inventoryList = document.getElementById('inventory-list');
        const editForm = document.getElementById('edit-form');
        let editingRow = null;

        // Load data from localStorage on page load
        window.onload = function() {
            const savedData = localStorage.getItem('inventoryData');
            if (savedData) {
                const inventory = JSON.parse(savedData);
                inventory.forEach(item => {
                    addInventoryRow(item);
                });
            }
        };

        // Open inventory in modal popup
        function openPopup() {
            document.getElementById('inventory-table-container').style.display = 'block';
        }

        // Add inventory row
        function addInventoryRow(data) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${data.item}</td>
                <td>${data.quantity}</td>
                <td>${data.status}</td>
                <td class="actions">
                    <button onclick="editInventory(this)">Edit</button>
                    <button onclick="deleteInventory(this)">Delete</button>
                </td>
            `;
            inventoryList.appendChild(row);
        }

        // Edit an inventory item
        function editInventory(button) {
            editingRow = button.parentElement.parentElement;

            const item = editingRow.children[0].innerText;
            const quantity = editingRow.children[1].innerText;
            const status = editingRow.children[2].innerText;

            document.getElementById('edit-item').value = item;
            document.getElementById('edit-quantity').value = quantity;
            document.getElementById('edit-status').value = status;

            document.getElementById('edit-form-container').style.display = 'block';
            document.getElementById('inventory-table-container').style.display = 'none';
        }

        // Save edited inventory item
        editForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const item = document.getElementById('edit-item').value;
            const quantity = document.getElementById('edit-quantity').value;
            const status = document.getElementById('edit-status').value;

            editingRow.children[0].innerText = item;
            editingRow.children[1].innerText = quantity;
            editingRow.children[2].innerText = status;

            saveInventoryData();

            document.getElementById('edit-form-container').style.display = 'none';
            document.getElementById('inventory-table-container').style.display = 'block';
        });

        // Delete inventory row
        function deleteInventory(button) {
            button.parentElement.parentElement.remove();
            saveInventoryData();
        }

        // Save inventory data to localStorage
        function saveInventoryData() {
            const rows = inventoryList.getElementsByTagName('tr');
            const data = [];
            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                data.push({
                    item: cells[0].innerText,
                    quantity: cells[1].innerText,
                    status: cells[2].innerText
                });
            }
            localStorage.setItem('inventoryData', JSON.stringify(data));
        }
    </script>
</body>
</html>
