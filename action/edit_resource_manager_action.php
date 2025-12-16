<?php
// action/edit_resource_manager_action.php

include "../settings/connection.php";

/*
 Expected POST:
 - rm_id (required)
 - user (optional)         -> new user_id (int or empty)
 - department (optional)   -> dep_id
 - resource (optional)     -> res_id
 - user_img (optional file) -> new user image to upload (applies to the user record)
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/view/resource_managers.php?error=invalid_request");
    exit;
}

$rm_id = isset($_POST['rm_id']) ? intval($_POST['rm_id']) : 0;
if ($rm_id <= 0) {
    header("Location: ../admin/view/resource_managers.php?error=missing_rm_id");
    exit;
}

// sanitize optional fields (allow empty)
$user_id_new = isset($_POST['user']) && $_POST['user'] !== '' ? intval($_POST['user']) : null;
$dep_id_new = isset($_POST['department']) && $_POST['department'] !== '' ? intval($_POST['department']) : null;
$res_id_new = isset($_POST['resource']) && $_POST['resource'] !== '' ? intval($_POST['resource']) : null;

// We'll need to know the current assignment to handle image deletion if needed
// Fetch current resmanagers record
$stmt = $conn->prepare("SELECT user_id, dep_id, res_id FROM resmanagers WHERE rm_id = ?");
$stmt->bind_param("i", $rm_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header("Location: ../admin/view/resource_managers.php?error=rm_not_found");
    exit;
}
$current = $res->fetch_assoc();
$old_user_id = intval($current['user_id']);

// Determine which user record we will update the image for:
// If a new user_id was provided, the image update should apply to that user (and we should consider deleting old user's image when replaced)
// But typically the image belongs to the user currently assigned; we'll delete old user's image if a new image is uploaded regardless of whether user changed.
$target_user_id_for_image = $user_id_new ?? $old_user_id;

// Process image upload (optional)
$uploaded_new_image_path = null;
if (!empty($_FILES['user_img']) && $_FILES['user_img']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['user_img']['error'] !== UPLOAD_ERR_OK) {
        header("Location: ../admin/view/resource_managers.php?error=file_upload_error");
        exit;
    }

    $fileTmpPath = $_FILES['user_img']['tmp_name'];
    $fileName = $_FILES['user_img']['name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExt = ['jpg','jpeg','png','gif'];

    if (!in_array($fileExt, $allowedExt)) {
        header("Location: ../admin/view/resource_managers.php?error=invalid_image_type");
        exit;
    }

    // prepare upload folder
    $uploadDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/crms/images/users_uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $newFileName = md5(time() . $fileName . rand(1000,9999)) . '.' . $fileExt;
    $destination = $uploadDir . $newFileName;

    if (!move_uploaded_file($fileTmpPath, $destination)) {
        header("Location: ../admin/view/resource_managers.php?error=could_not_move_file");
        exit;
    }

    // store web-accessible path used across your project (follow same pattern as register_action.php)
    $uploaded_new_image_path = '/crms/images/users_uploads/' . $newFileName;
}

// If a new image was uploaded, delete the previous image file for the old user (if exists and not empty)
if ($uploaded_new_image_path !== null) {
    // fetch old user's user_img
    $uStmt = $conn->prepare("SELECT user_img, email FROM users WHERE user_id = ?");
    $uStmt->bind_param("i", $old_user_id);
    $uStmt->execute();
    $uRes = $uStmt->get_result();
    $old_user_img = null;
    if ($uRes->num_rows > 0) {
        $uRow = $uRes->fetch_assoc();
        $old_user_img = $uRow['user_img'];
    }

    // delete file if old_user_img points to /crms/images/users_uploads/<file>
    if (!empty($old_user_img)) {
        // get filename (strip path)
        $filename = basename($old_user_img);
        $fullPath = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/crms/images/users_uploads/' . $filename;
        if (file_exists($fullPath) && is_file($fullPath)) {
            // attempt delete - ignore failure
            @unlink($fullPath);
        }
    }

    // update the target user's user_img to the new path
    $updateImgStmt = $conn->prepare("UPDATE users SET user_img = ? WHERE user_id = ?");
    $updateImgStmt->bind_param("si", $uploaded_new_image_path, $target_user_id_for_image);
    $updateImgStmt->execute();
    $updateImgStmt->close();
}

// Prepare UPDATE for resmanagers table
$updateParts = [];
$types = "";
$params = [];

// If user_id provided set it, else keep current (no change)
if ($user_id_new !== null) {
    $updateParts[] = "user_id = ?";
    $types .= "i";
    $params[] = $user_id_new;
}

// department
if ($dep_id_new !== null) {
    $updateParts[] = "dep_id = ?";
    $types .= "i";
    $params[] = $dep_id_new;
}

// resource
if ($res_id_new !== null) {
    $updateParts[] = "res_id = ?";
    $types .= "i";
    $params[] = $res_id_new;
}

if (!empty($updateParts)) {
    $sql = "UPDATE resmanagers SET " . implode(", ", $updateParts) . " WHERE rm_id = ?";
    $types .= "i";
    $params[] = $rm_id;

    $stmt2 = $conn->prepare($sql);

    // bind dynamically
    $bindNames[] = $types;
    for ($i = 0; $i < count($params); $i++) {
        $bindNames[] = &$params[$i];
    }
    call_user_func_array([$stmt2, 'bind_param'], $bindNames);
    $execOk = $stmt2->execute();
    $stmt2->close();

} else {
    // nothing to update in resmanagers (but perhaps the image updated). We'll continue.
    $execOk = true;
}

// Determine the email of the user currently assigned to rm after update
$final_user_id = $user_id_new ?? $old_user_id;
$email = '';
$uStmt2 = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
$uStmt2->bind_param("i", $final_user_id);
$uStmt2->execute();
$uRes2 = $uStmt2->get_result();
if ($uRes2->num_rows > 0) {
    $email = $uRes2->fetch_assoc()['email'];
}
$uStmt2->close();

// Redirect back to view with success
$encoded = urlencode($email ?: 'Resource Manager');
header("Location: ../admin/view/resource_managers.php?edited_email={$encoded}");
exit;
