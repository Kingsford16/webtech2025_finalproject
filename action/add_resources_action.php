<?php
include "../settings/connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $res_name  = trim($_POST['res_name']);
    $location  = intval($_POST['location']);
    $status    = intval($_POST['status']);

    // FILE INFO
    $fileName  = $_FILES['res_image']['name'];
    $tempName  = $_FILES['res_image']['tmp_name'];
    $fileError = $_FILES['res_image']['error'];

    // Allowed image types
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    /* -----------------------------
       CHECK UNIQUE RESOURCE NAME
    ------------------------------*/
    $checkName = $conn->prepare("SELECT res_id FROM resources WHERE res_name = ?");
    $checkName->bind_param("s", $res_name);
    $checkName->execute();
    $checkResult = $checkName->get_result();

    if ($checkResult->num_rows > 0) {
        header("Location: ../admin/view/resources.php?name_error=This name already exists");
        exit;
    }

    /* -----------------------------
       VALIDATE IMAGE TYPE
    ------------------------------*/
    if (!in_array($fileExt, $allowedTypes)) {
        header("Location: ../admin/view/resources.php?image_error=Only jpg, jpeg and png files are allowed");
        exit;
    }

    /* -----------------------------
       MOVE UPLOADED FILE
    ------------------------------*/
    $uploadDir = "../images/resources_uploads/";
    $newFileName = time() . "_" . uniqid() . "." . $fileExt;
    $finalPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($tempName, $finalPath)) {
        header("Location: ../admin/view/resources.php?image_error=Image upload failed");
        exit;
    }

    /* -----------------------------
       INSERT INTO DATABASE
    ------------------------------*/
    $stmt = $conn->prepare("
        INSERT INTO resources (res_status, loc_id, res_name, res_img) 
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("iiss", $status, $location, $res_name, $newFileName);

    if ($stmt->execute()) {

        // SUCCESS MESSAGE
        $successMsg = urlencode("$res_name is added successfully");

        header("Location: ../admin/view/resources.php?success=$successMsg");
        exit;

    } else {
        header("Location: ../admin/view/resources.php?image_error=Database insert failed");
        exit;
    }
}
?>
