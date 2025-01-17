<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Master</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="customers.php">Customers</a>
        <a href="orders.php">Orders</a>
        <a href="index.php">Dashboard</a>
        <a href="customers.php">Customers</a>
        <a href="orders.php">Orders</a>
        <a href="laundry_list.php">Laundry List</a>
        <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
    </nav>
    <footer>
        <p>&copy; 2024 Laundry Management System</p>
    </footer>
</body>
</html>

