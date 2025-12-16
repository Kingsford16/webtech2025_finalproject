<?php
session_start();
include '../settings/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$uid = $_SESSION['user_id'];

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {

    // 1. Get file info
    $fileTmpPath = $_FILES['profile_image']['tmp_name'];
    $fileName = $_FILES['profile_image']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedExtensions = ['jpg','jpeg','png','gif'];
    
    if (!in_array($fileExtension, $allowedExtensions)) {
    echo "
        <div style='
            margin: 50px auto;
            width: 400px;
            padding: 20px;
            background: #ffe6e6;
            border-left: 6px solid #ff0000;
            font-family: Arial;
            text-align:center;
            border-radius:8px;
        '>
            <h3 style='color:#b30000;'>Upload Failed</h3>
            <p style='color:#333;'>Invalid file type. Only JPG, JPEG, PNG, or GIF files are allowed.</p>
            <a href='{$_SERVER['HTTP_REFERER']}'
               style='display:inline-block;margin-top:15px;
               padding:10px 18px;background:#cc0000;color:white;
               text-decoration:none;border-radius:5px;'>
               Go Back
            </a>
        </div>
    ";
    exit();
    }


    // 2. Create new file name
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/crms/images/users_uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $destPath = $uploadDir . $newFileName;

    // 3. Move uploaded file
    if (move_uploaded_file($fileTmpPath, $destPath)) {

        // 4. Get old image
        $oldImage = null;
        $imgQuery = $conn->prepare("SELECT user_img FROM users WHERE user_id=?");
        $imgQuery->bind_param("i", $uid);
        $imgQuery->execute();
        $imgQuery->bind_result($oldImage);
        $imgQuery->fetch();
        $imgQuery->close();

        // 5. Delete old image (ONLY if it's not placeholder)
        if (!empty($oldImage) && $oldImage !== '/crms/images/placeholder.jpg') {
            $oldImagePath = $_SERVER['DOCUMENT_ROOT'] . $oldImage;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // 6. Save new image path
        $relativePath = '/crms/images/users_uploads/' . $newFileName;
        $stmt = $conn->prepare("UPDATE users SET user_img=? WHERE user_id=?");
        $stmt->bind_param("si", $relativePath, $uid);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
