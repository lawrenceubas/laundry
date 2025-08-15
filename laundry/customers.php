<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');

// Function to add a new customer
function addCustomer($name, $phone, $email, $address) {
    global $pdo; // Make sure we use the PDO object defined in the included 'database.php'

    // Prepare the SQL query to insert the customer into the database
    $stmt = $pdo->prepare("INSERT INTO customers (name, phone, email, address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $phone, $email, $address]);

    // Set a session message for successful customer addition
    $_SESSION['add_message'] = 'Customer added successfully!';

    // Redirect to the same page to show the updated customer list
    header("Location: customers.php");
    exit();
}

// Function to delete a customer
function deleteCustomer($id) {
    global $pdo;

    // Mark the customer as deleted by setting the 'is_deleted' flag to 1
    $stmt = $pdo->prepare("UPDATE customers SET is_deleted = 1 WHERE id = ?");
    $stmt->execute([$id]);

    // Set a session message for successful deletion
    $_SESSION['delete_message'] = 'Customer marked as deleted successfully!';
}

// Handle form submission to add a new customer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Call the addCustomer function
    addCustomer($name, $phone, $email, $address);
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    deleteCustomer($deleteId);
    header("Location: customers.php");
    exit(); // Redirect after deletion
}

// Fetch all active customers (those that are not marked as deleted)
$customers = $pdo->query("SELECT * FROM customers WHERE is_deleted = 0")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link rel="icon" href="asset/img/icon.png" type="image/x-icon">
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
        .form-container {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
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
    </style>
</head>
<body>

    <div class="navbar">
        <img src="asset/img/icon.png" alt="Laundry Master Logo">
        <h2 style="text-align: center;">Cum Laundry</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="customers.php">Customers</a>
        <a href="orders.php">Orders</a>
        <a href="inventory.php">Inventory</a>
        <a href="calendar.php">Calendar</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Customer Management</h1>

        <!-- Display Success Message if Customer is Added -->
        <?php
        if (isset($_SESSION['add_message'])) {
            echo "<script>alert('{$_SESSION['add_message']}');</script>";
            unset($_SESSION['add_message']);  // Clear the session message after alert
        }

        // Display Success Message if Customer is Deleted
        if (isset($_SESSION['delete_message'])) {
            echo "<script>alert('{$_SESSION['delete_message']}');</script>";
            unset($_SESSION['delete_message']);  // Clear the session message after alert
        }
        ?>

        <!-- Add New Customer Form -->
        <div class="form-container">
            <h2>Add New Customer</h2>
            <form method="POST" action="customers.php">
                <input type="text" name="name" placeholder="Name" required>
                <input type="text" name="phone" placeholder="Phone" required>
                <input type="email" name="email" placeholder="Email" required>
                <textarea name="address" placeholder="Address" required></textarea>
                <button type="submit">Add Customer</button>
            </form>
        </div>

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
                    <td><?= htmlspecialchars($customer['name']) ?></td>
                    <td><?= htmlspecialchars($customer['phone']) ?></td>
                    <td><?= htmlspecialchars($customer['email']) ?></td>
                    <td><?= htmlspecialchars($customer['address']) ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="edit_customer.php?id=<?= $customer['id'] ?>">Edit</a>
                            <a href="?delete_id=<?= $customer['id'] ?>" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
