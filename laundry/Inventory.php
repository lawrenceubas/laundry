<?php
$conn = new mysqli("localhost", "root", "", "laundry_management");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM inventory WHERE id = $id");
    $message = "🗑️ Item deleted.";
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemName = trim($_POST['item_name']);
    $quantity = (int)$_POST['stock']; // using 'stock' from input form, maps to quantity column

    if (isset($_POST['update_item'])) {
        $id = (int)$_POST['item_id'];
        $stmt = $conn->prepare("UPDATE inventory SET item_name = ?, quantity = ? WHERE id = ?");
        $stmt->bind_param("sii", $itemName, $quantity, $id);
        $stmt->execute();
        $message = "✏️ Item updated.";
    } elseif (isset($_POST['add_item'])) {
        $check = $conn->prepare("SELECT id, quantity FROM inventory WHERE item_name = ?");
        $check->bind_param("s", $itemName);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $newQty = $row['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE inventory SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $newQty, $row['id']);
            $stmt->execute();
            $message = "✅ Existing stock updated.";
        } else {
            $stmt = $conn->prepare("INSERT INTO inventory (item_name, quantity) VALUES (?, ?)");
            $stmt->bind_param("si", $itemName, $quantity);
            $stmt->execute();
            $message = "✅ New item added.";
        }
    }
}

$inventoryResult = $conn->query("SELECT * FROM inventory");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="icon" href="asset/img/icon.png" type="image/x-icon">
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
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .btn {
            padding: 15px 25px;
            font-size: 16px;
            border: none;
            background-color: #007BFF;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            margin: 10px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0; top: 0;
            width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 20px 30px;
            border-radius: 10px;
            width: 90%; max-width: 700px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            position: relative;
        }

        .close {
            position: absolute;
            top: 10px; right: 20px;
            font-size: 24px;
            color: #aaa;
            cursor: pointer;
        }

        .close:hover { color: black; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .edit-btn, .delete-btn {
            padding: 6px 10px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .edit-btn { background-color: #ffc107; color: #212529; }
        .delete-btn { background-color: #dc3545; color: white; }
        .edit-btn:hover { background-color: #e0a800; }
        .delete-btn:hover { background-color: #c82333; }

        form label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 4px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        form button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
        }

        form button:hover {
            background: #0056b3;
        }

        .message {
            margin-top: 10px;
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            text-align: center;
        }

        .header-btns {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-btns h2 {
            margin: 0;
        }

        .small-btn {
            padding: 6px 12px;
            font-size: 14px;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<!-- Sidebar Navbar -->
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

<!-- Main Content -->
<div class="main-content">
    <button class="btn" onclick="openInventory()">📦 Open Inventory</button>

    <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>
</div>

<!-- Single Modal for Add/Edit/Inventory -->
<div class="modal" id="modal">
    <div class="modal-content">
     <span class="close" onclick="closeModal()">❎</span>
        <div id="modalHeader">
            <h2>Inventory</h2>
            <button class="btn small-btn" onclick="openAddModal()">➕ Add Item</button>
        </div>
        
        <!-- Inventory Table -->
        <div id="inventoryContent" style="display: block;">
            <table>
                <tr><th>ID</th><th>Item</th><th>Stock</th><th>Actions</th></tr>
                <?php while ($row = $inventoryResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td>
                            <td>
            <button class="btn btn-edit" onclick="openEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['item_name'], ENT_QUOTES) ?>', <?= $row['quantity'] ?>)">✏️ Edit</button>
                            <a class="delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this item?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <!-- Add/Edit Item Form -->
        <div id="formContent" style="display: none;">
            <h3 id="modalTitle">Add Item</h3>
            <form method="POST">
                <input type="hidden" name="item_id" id="item_id">
                <label>Item Name</label>
                <input type="text" name="item_name" id="item_name" required>
                <label>Stock</label>
                <input type="number" name="stock" id="stock" required min="1">
                <button type="submit" name="add_item" id="submitBtn">Add Item</button>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById("modal");
    const modalHeader = document.getElementById("modalHeader");
    const inventoryContent = document.getElementById("inventoryContent");
    const formContent = document.getElementById("formContent");
    const itemId = document.getElementById("item_id");
    const itemName = document.getElementById("item_name");
    const stock = document.getElementById("stock");
    const modalTitle = document.getElementById("modalTitle");
    const submitBtn = document.getElementById("submitBtn");

    function openInventory() {
        modal.style.display = "block";
        modalHeader.style.display = "block";
        inventoryContent.style.display = "block";
        formContent.style.display = "none";
    }

    function closeModal() {
        modal.style.display = "none";
    }

    function openAddModal() {
        modalTitle.innerText = "Add Item";
        submitBtn.name = "add_item";
        submitBtn.innerText = "Add Item";
        itemId.value = "";
        itemName.value = "";
        stock.value = "";
        modalHeader.style.display = "none";
        inventoryContent.style.display = "none";
        formContent.style.display = "block";
    }

    function openEditModal(id, name, qty) {
        modalTitle.innerText = "Edit Item";
        submitBtn.name = "update_item";
        submitBtn.innerText = "Update Item";
        itemId.value = id;
        itemName.value = name;
        stock.value = qty;
        modalHeader.style.display = "none";
        inventoryContent.style.display = "none";
        formContent.style.display = "block";
    }
</script>

</body>
</html>