<?php
session_start();
include '../settings/core.php';

// Only allow POST for AJAX
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Check if user is logged in (resource manager)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$manager_user_id = $_SESSION['user_id'];
$booking_id = intval($_POST['booking_id'] ?? 0);
$student_email = trim($_POST['student_email'] ?? '');

// Validate booking_id
if ($booking_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit();
}

// Get the rm_id for this manager and resource (from resmanagers table)
$sql = "SELECT rm_id FROM resmanagers WHERE user_id = ? AND res_id = (SELECT res_id FROM bookings WHERE booking_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $manager_user_id, $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$rm_data = $result->fetch_assoc();
$stmt->close();

if (!$rm_data) {
    echo json_encode(['success' => false, 'message' => 'Manager not assigned to this resource']);
    exit();
}

$rm_id = $rm_data['rm_id'];

// Update booking: set rm_id and change pro_id from 2 to 3 (cancelled)
$sql = "UPDATE bookings SET rm_id = ?, pro_id = 3 WHERE booking_id = ? AND app_id = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $rm_id, $booking_id);

if ($stmt->execute()) {
    $stmt->close();
    echo json_encode([
        'success' => true, 
        'message' => "Event by $student_email is cancelled",
        'student_email' => $student_email
    ]);
} else {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Failed to deny booking']);
}

$conn->close();
?>
