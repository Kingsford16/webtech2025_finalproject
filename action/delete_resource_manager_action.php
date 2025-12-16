<?php
header('Content-Type: application/json');
include '../settings/connection.php';

if (!isset($_POST['rm_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing rm_id']);
    exit;
}

$rmId = intval($_POST['rm_id']);

// STEP 1 — get user_id
$stmt = $conn->prepare("SELECT user_id FROM resmanagers WHERE rm_id = ?");
$stmt->bind_param("i", $rmId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Resource manager not found']);
    exit;
}

$userId = $row['user_id'];

// STEP 2 — delete resmanagers record
$stmt = $conn->prepare("DELETE FROM resmanagers WHERE rm_id = ?");
$stmt->bind_param("i", $rmId);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Delete failed']);
    exit;
}

$stmt->close();

// STEP 3 — do NOT delete the user's image (keep it for users table)

echo json_encode(['success' => true]);
exit;
