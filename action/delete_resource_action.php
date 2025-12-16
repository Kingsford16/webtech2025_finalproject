<?php
include '../settings/connection.php';
session_start();

// Only allow POST for AJAX
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Check if user is admin
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check res_id
if (!isset($_POST['res_id'])) {
    echo json_encode(['success' => false, 'message' => 'Resource ID not provided']);
    exit();
}

$resId = intval($_POST['res_id']);

// Get resource info first (image)
$stmt = $conn->prepare("SELECT res_name, res_img FROM resources WHERE res_id = ?");
$stmt->bind_param("i", $resId);
$stmt->execute();
$stmt->bind_result($resName, $resImg);
$stmt->fetch();
$stmt->close();

if (!$resName) {
    echo json_encode(['success' => false, 'message' => 'Resource not found']);
    exit();
}

// Delete resource manager assignment if exists
$stmt = $conn->prepare("DELETE FROM resmanagers WHERE res_id = ?");
$stmt->bind_param("i", $resId);
$stmt->execute();
$stmt->close();

// Delete resource record
$stmt = $conn->prepare("DELETE FROM resources WHERE res_id = ?");
$stmt->bind_param("i", $resId);

if ($stmt->execute()) {
    $stmt->close();

    // Delete image file
    $imgPath = "../images/resources_uploads/" . $resImg;
    if (file_exists($imgPath)) {
        unlink($imgPath);
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database delete failed']);
}

$conn->close();
?>
