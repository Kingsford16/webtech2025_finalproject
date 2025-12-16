<?php
include '../settings/connection.php';
session_start();

// Only allow POST for AJAX
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Check if user is admin
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check booking_id
if (!isset($_POST['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Booking ID not found']);
    exit();
}

$bookingId = intval($_POST['booking_id']);

// Update pro_id to 3 (cancelled)
$stmt = $conn->prepare("UPDATE bookings SET pro_id = 3 WHERE booking_id = ?");
$stmt->bind_param("i", $bookingId);

if ($stmt->execute()) {
    $stmt->close();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed']);
}

$conn->close();
?>
