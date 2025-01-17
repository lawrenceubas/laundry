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
    <title>Customer Management</title>
    
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
            display: none; /* Initially hidden */
        }

        .form-container input, .form-container textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
        }

        .form-container button {
            padding: 10px 20px;
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
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            
        }

        .action-buttons {
            display: flex;
            gap: 15px;
        }

        .action-buttons a {
            padding: 5px 10px;
            background-color: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .action-buttons a:hover {
            background-color: #34495e;
        }

        .toggle-btn {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            cursor: pointer;
            margin-bottom:10px ;
        }

        .toggle-btn:hover {
            background-color: #34495e;
        }

    </style>
</head>
<body>

    <div class="navbar">
        <!-- Logo Image in Navbar -->
        <img src="asset/img/lm.png" alt="Laundry Master Logo"> <!-- Logo Image -->
        <h2 style="text-align: center;">Laundry Master</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="customers.php">Customers</a>
        <a href="orders.php">Orders</a>
        <a href="supplylist.php">Supply List</a>
        <a href="calendar.php">Calendar</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Customer Management</h1>

        <div class="form-container" id="addCustomerForm">
            <h2>Add New Customer</h2>
            <form method="POST" action="customers.php">
                <input type="text" name="name" placeholder="Name" required>
                <input type="text" name="phone" placeholder="Phone" required>
                <input type="email" name="email" placeholder="Email" required>
                <textarea name="address" placeholder="Address" required></textarea>
                <button type="submit">Add Customer</button>
            </form>
        </div>

        <button class="toggle-btn" onclick="toggleForm()">Add New Customer</button>

        <?php
        // Handle form submission to add a customer
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $address = $_POST['address'];

            $stmt = $pdo->prepare("INSERT INTO customers (name, phone, email, address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $email, $address]);

            echo "<p>Customer added successfully!</p>";
            // Reload the page to show the updated customer list
        }

        // Handle Delete action
        if (isset($_GET['delete_id'])) {
            $deleteId = $_GET['delete_id'];

            $stmt = $pdo->prepare("UPDATE customers SET is_deleted = 1 WHERE id = ?");
            $stmt->execute([$deleteId]);

            echo "<p>Customer marked as deleted.</p>";
            // Immediately redirect after deleting
            header("Location: customers.php");
            exit();
        }

        // Fetch all active customers
        $customers = $pdo->query("SELECT * FROM customers WHERE is_deleted = 0")->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <h2>All Customers</h2>
        <table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($customers as $customer): ?>
        <tr>
            <td><?= htmlspecialchars($customer['id']) ?></td>
            <td>
                <?= htmlspecialchars($customer['name']) ?>
                <!-- Additional content inside the table cell -->
                <div style="font-size: 12px; color: gray;">Customer since: <?= htmlspecialchars($customer['created_at']) ?></div>
            </td>
            <td><?= htmlspecialchars($customer['phone']) ?></td>
            <td><?= htmlspecialchars($customer['email']) ?></td>
            <td><?= htmlspecialchars($customer['address']) ?></td>
            <td>
                <div class="action-buttons">
                    <!-- Add buttons inside the table cell -->
                    <a href="edit_customer.php?id=<?= $customer['id'] ?>">Edit</a>
                    <a href="?delete_id=<?= $customer['id'] ?>" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
 </div>

    <script>
    function toggleForm() {
        const form = document.getElementById('addCustomerForm');
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }
    </script>

</body>
</html>
