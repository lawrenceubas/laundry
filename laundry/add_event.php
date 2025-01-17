<?php
include('database.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Insert event into the database
    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, start_time, end_time) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $event_date, $start_time, $end_time]);

    echo "Event added successfully!";
    header("Location: calendar.php"); // Redirect to the calendar page
    exit();
}
?>

<form method="POST" action="add_event.php">
    <label for="title">Event Title:</label>
    <input type="text" name="title" required><br><br>

    <label for="description">Description:</label>
    <textarea name="description"></textarea><br><br>

    <label for="event_date">Event Date:</label>
    <input type="date" name="event_date" required><br><br>

    <button type="submit">Add Event</button>
</form>
