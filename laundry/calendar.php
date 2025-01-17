<?php  
include('database.php');

// Get current month and year
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Calculate first day of the month and number of days in the month
$first_day_of_month = strtotime("$year-$month-01");
$days_in_month = date('t', $first_day_of_month);

// Get events for the month
$stmt = $pdo->prepare("SELECT * FROM events WHERE MONTH(event_date) = ? AND YEAR(event_date) = ?");
$stmt->execute([$month, $year]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group events by date
$events_by_date = [];
foreach ($events as $event) {
    $events_by_date[$event['event_date']][] = $event;
}

// Handle form submission for adding events
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Insert event into the database
    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, start_time, end_time) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $event_date, $start_time, $end_time]);

    // Redirect to the same page to prevent resubmission
    header("Location: calendar.php?month=$month&year=$year");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - Laundry Master</title>
    
    <!-- Favicon -->
    <link rel="icon" href="asset/img/lm.png" type="image/x-icon">

    <style>
        /* Basic Styles */
        table {
            width: 100%;
        
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            height: 100px;
        }

        th {
            font-weight: bold;
            background-color: #f4f4f4;
        }

        .event {
            margin-top: 5px;
            background-color: #4CAF50;
            color: white;
            font-size: 12px;
            padding: 3px;
            border-radius: 4px;
            cursor: pointer;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            margin: 20px auto;
            display: none; /* Initially hidden */
        }
        .card h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
            text-align: center;
        }
        .card input, .card textarea, .card select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .card button {
            background-color: #2c3e50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            border-radius: 4px;
        }
        .card button:hover {
            background-color: #34495e;
        }

        /* Calendar Navigation Links */
        .calendar-nav {
            margin-bottom: 20px;
        }
        .calendar-nav a {
            text-decoration: none;
            color: #2c3e50;
            margin-right: 10px;
            font-size: 16px;
        }
        .calendar-nav a:hover {
            color: #34495e;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            height: 100%;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .sidebar .logo{ 
            width: 60%; /* Adjust the width to make the logo smaller */
            max-width: 120px; /* Set a maximum width for the logo */
            margin-bottom: 20px; /* Add spacing below the logo */
            display: block;
            margin-left: auto;
            margin-right: auto;
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

        /* Main Content */
        main {
            margin-left: 260px;
            padding: 20px;
            flex: 1;
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
    <a href="calendar.php" class="active">Calendar</a>
    <a href="supplylist.php">Supply List</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Main Content -->
<main>
<h1>Calendar - Laundry Master</h1>

<!-- Back to Dashboard Button (left side of the calendar) -->
<a href="dashboard.php" class="back-button">Back</a>

<!-- Calendar Navigation -->
<div class="calendar-nav">
    <a href="?month=<?= $month - 1; ?>&year=<?= $year; ?>">Previous</a>
    <a href="?month=<?= $month + 1; ?>&year=<?= $year; ?>">Next</a>
</div>

<h3><?= date('F Y', strtotime("$year-$month-01")) ?></h3>

<!-- Calendar Display inside a table -->
<table>
    <thead>
        <tr>
            <th>Sun</th>
            <th>Mon</th>
            <th>Tue</th>
            <th>Wed</th>
            <th>Thu</th>
            <th>Fri</th>
            <th>Sat</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Start the calendar grid by filling in the days before the 1st of the month
        $first_day_of_week = date('w', $first_day_of_month);
        $day_counter = 1;
        $total_rows = ceil(($first_day_of_week + $days_in_month) / 7); // Number of weeks in the month

        // Loop through each row
        for ($row = 0; $row < $total_rows; $row++) {
            echo "<tr>";

            // Loop through each day of the week (Sun - Sat)
            for ($col = 0; $col < 7; $col++) {
                // Calculate the current day
                if ($row == 0 && $col < $first_day_of_week) {
                    echo "<td></td>"; // Empty cells before the 1st day of the month
                } elseif ($day_counter <= $days_in_month) {
                    $current_date = "$year-$month-" . str_pad($day_counter, 2, '0', STR_PAD_LEFT);
                    echo "<td>";
                    echo $day_counter;

                    // Display events for that date
                    if (isset($events_by_date[$current_date])) {
                        foreach ($events_by_date[$current_date] as $event) {
                            echo "<div class='event'>{$event['title']}</div>";
                        }
                    }

                    echo "</td>";
                    $day_counter++;
                } else {
                    echo "<td></td>"; // Empty cells after the last day of the month
                }
            }

            echo "</tr>";
        }
        ?>
    </tbody>
</table>

<!-- Button to show the Add Event Form -->
<button onclick="toggleForm()">Add New Schedule</button>

<!-- Add Event Form in a Card -->
<div class="card" id="addScheduleForm">
    <h2>Pick Up Schedule</h2>
    <form method="POST" action="calendar.php?month=<?= $month ?>&year=<?= $year ?>">
        <input type="text" name="title" placeholder="Name" required><br>
        <input type="date" name="event_date" required><br> <!-- updated to event_date -->
        <button type="submit" name="add_event">Add Event</button>
    </form>
</div>

<!-- JavaScript to Toggle the Add Event Form -->
<script>
function toggleForm() {
    const form = document.getElementById('addScheduleForm');
    form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
}
</script>

</main>

</body>
</html>
