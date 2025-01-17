<?php
include('database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $orderId = $_POST['orderId'];
  $newStatus = $_POST['newStatus'];

  try {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $orderId]);

    echo json_encode(['success' => true]);
  } catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
  }

  exit();
}
?>