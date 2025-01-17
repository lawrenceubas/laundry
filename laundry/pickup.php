<?php
// Include database connection
include('database.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data and sanitize input
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $service = htmlspecialchars($_POST['service']);
    $date = htmlspecialchars($_POST['date']);
    $message = htmlspecialchars($_POST['message']);

    // Prepare SQL query to insert data into bookings table
    $stmt = $pdo->prepare("INSERT INTO bookings (name, email, phone, service, date, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $service, $date, $message]);

    // Check if the insertion was successful
    if ($stmt) {
        // Get the user ID from the session (ensure session is started at the top of the file)
        session_start();
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];

            // Check if the service is "Pickup & Delivery" and create a notification
            if ($service == 'Pickup & Delivery') {
                $notification_message = "New laundry pickup request received for " . htmlspecialchars($name) . " on " . $date;
                
                // Insert a notification into the notifications table
                $notifyStmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                $notifyStmt->execute([$user_id, $notification_message]);
            }

            // Redirect to notification page with a success message
            echo "<script>alert('Booking successfully submitted and notification created!'); window.location.href = 'notification.php';</script>";
        } else {
            // If user_id is not set in the session, handle it (e.g., redirect to login)
            header("Location: login.php");
            exit();
        }
    } else {
        echo "<script>alert('There was an error processing your booking. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking Form</title>
  
  <!-- Flatpickr CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background: url('asset/img/bg.jpg') no-repeat center center fixed; /* Change this URL to your background image */
      background-size: cover;
      margin: 0;
      font-size: 18px;  
      padding: 0;
      box-sizing: border-box;
      color: #333;
    }
    .form-container {
      width: 40%;
      margin: 40px auto;
      background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent background */
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      color: #007BFF;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
    }
    input, select, textarea {
      width: 94%;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    textarea {
      resize: none;
    }
    button {
      background-color: #28a745;
      color: white;
      border: none;
      cursor: pointer;
      padding: 12px;
      font-size: 18px;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background-color: #218838;
    }
    .back-button {
      display: inline-block;
      background-color: #007BFF;
      color: white;
      text-align: center;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      margin: 20px 0;
      font-size: 18px;
    }
    .back-button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <a href="landing.php" class="back-button">Back</a>
  
  <div class="form-container">
    <h2>Book a Laundry Service</h2>
    <form action="bookings.php" method="POST">
      <table>
        <tr>
          <th><label for="name">Full Name</label></th>
          <td><input type="text" id="name" name="name" placeholder="Your full name" required></td>
        </tr>
        <tr>
          <th><label for="email">Email Address</label></th>
          <td><input type="email" id="email" name="email" placeholder="Your email address" required></td>
        </tr>
        <tr>
          <th><label for="phone">Phone Number</label></th>
          <td><input type="tel" id="phone" name="phone" placeholder="Your phone number" required></td>
        </tr>
        <tr>
          <th><label for="service">Select a Service</label></th>
          <td>
            <select id="service" name="service" required>
              <option value="Laundry Service">Laundry Service</option>
              <option value="Dry Cleaning">Dry Cleaning</option>
              <option value="Ironing">Ironing</option>
              <option value="Pickup & Delivery">Pickup & Delivery</option>
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="date">Preferred Date</label></th>
          <td><input type="text" id="date" name="date" placeholder="Select a date" required></td>
        </tr>
        <tr>
          <th><label for="message">Additional Instructions (Optional)</label></th>
          <td><textarea id="message" name="message" rows="5" placeholder="Enter any specific instructions..."></textarea></td>
        </tr>
        <tr>
          <td colspan="2">
            <button type="submit">Submit Booking</button>
          </td>
        </tr>
      </table>
    </form>
  </div>

  <!-- Flatpickr JS -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    flatpickr("#date", {
      minDate: "today", // Prevent selecting past dates
      dateFormat: "Y-m-d", // Format the date
    });
  </script>
</body>
</html>
