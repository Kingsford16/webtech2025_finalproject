<?php
include "../settings/core.php";

if (!isset($_POST['res_id'])) {
    header("Location: ../admin/view/resources.php");
    exit;
}

$res_id     = $_POST['res_id'];
$res_name   = trim($_POST['res_name']);
$location   = $_POST['location'];
$old_img    = $_POST['old_res_img'];

$name_error  = "";
$image_error = "";
$new_image_name = $old_img;

// VALIDATION
if ($res_name === "") {
    $name_error = "Resource name cannot be empty.";
}

if ($name_error !== "") {
    header("Location: ../admin/view/resources.php?name_error=$name_error");
    exit;
}

// HANDLE IMAGE UPLOAD
if (!empty($_FILES['res_image']['name'])) {

    $file = $_FILES['res_image'];
    $allowed = ['image/jpeg', 'image/jpg', 'image/png'];

    if (!in_array($file['type'], $allowed)) {
        $image_error = "Only JPG, JPEG, and PNG are allowed.";
    }

    if ($file['size'] > 3000000) {
        $image_error = "Image must not exceed 3MB.";
    }

    if ($image_error !== "") {
        header("Location: ../admin/view/resources.php?image_error=$image_error");
        exit;
    }

    // GENERATE NEW IMAGE NAME
    $new_image_name = time() . "_" . basename($file["name"]);
    $target_path = "../images/resources_uploads/" . $new_image_name;

    // UPLOAD
    move_uploaded_file($file["tmp_name"], $target_path);

    // DELETE OLD IMAGE
    $old_path = "../images/resources_uploads/" . $old_img;
    if (file_exists($old_path)) {
        unlink($old_path);
    }
}

// UPDATE DATABASE
$sql = "
    UPDATE resources 
    SET res_name = ?, loc_id = ?, res_img = ?
    WHERE res_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sisi", $res_name, $location, $new_image_name, $res_id);
$stmt->execute();

$stmt->close();

// SUCCESS POPUP
header("Location: ../admin/view/resources.php?edit_success=" . urlencode($res_name));
exit;

?>
