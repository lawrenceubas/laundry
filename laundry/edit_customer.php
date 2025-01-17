<?php
$host = 'localhost';
$user = 'root'; // Database username
$password = ''; // Database password
$dbname = 'booking_system';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('database.php');

// Fetch the customer data if the ID is passed
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    // If customer is not found
    if (!$customer) {
        echo "<p>Customer not found.</p>";
        exit();
    }
}

// Handle form submission to update customer details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $pdo->prepare("UPDATE customers SET name = ?, phone = ?, email = ?, address = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $email, $address, $id]);
    echo "<p>Customer updated successfully!</p>";
    header("Location: customers.php"); // Redirect back to customers list
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f9f9f9;
            color: #2c3e50;
        }
        .navbar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            height: 100%;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }
        .navbar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ecf0f1;
        }
        .navbar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 15px 20px;
            margin: 5px 0;
            border-radius: 5px;
            transition: background 0.3s;
            font-size: 18px;
        }
        .navbar a:hover {
            background-color: #34495e;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .content h1 {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
            color: #2c3e50;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input:focus, textarea:focus {
            border-color: #2c3e50;
            outline: none;
            box-shadow: 0 0 5px rgba(44, 62, 80, 0.2);
        }
        button {
            background-color: #2c3e50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        button:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>

    <!-- Left-Side Navbar -->
    <div class="navbar">
        <h2 style="text-align: center;">Laundry Master</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="customers.php">Customers</a>
        <a href="orders.php">Orders</a>
        <a href="supplylist.php">Supply List</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>Edit Customer</h1>

        <!-- Edit Customer Form -->
        <div class="form-container">
            <form method="POST" action="edit_customer.php?id=<?= $customer['id'] ?>">
                <table>
                    <tr>
                        <th>Field</th>
                        <th>Input</th>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td><input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($customer['name']) ?>" required></td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td><input type="text" name="phone" placeholder="Phone" value="<?= htmlspecialchars($customer['phone']) ?>" required></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($customer['email']) ?>" required></td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td><textarea name="address" placeholder="Address" required><?= htmlspecialchars($customer['address']) ?></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit">Update Customer</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

</body>
</html>
