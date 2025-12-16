<?php
include "../settings/connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve form values
    $user       = trim($_POST['user']);
    $department = trim($_POST['department']);
    $resource   = trim($_POST['resource']);

    /* ------------------------------------------------
       INSERT INTO resmanagers TABLE (CORRECT TABLE)
       ------------------------------------------------ */
    $stmt = $conn->prepare("
        INSERT INTO resmanagers (user_id, dep_id, res_id)
        VALUES (?, ?, ?)
    ");

    // Bind correct number of parameters (3 parameters)
    $stmt->bind_param("iii", $user, $department, $resource);

    if ($stmt->execute()) {

        /* ------------------------------------------------
           FETCH EMAIL OF MANAGER (user_id → users table)
        -------------------------------------------------- */
        $userQuery = "SELECT email FROM users WHERE user_id = ?";
        $uStmt = $conn->prepare($userQuery);
        $uStmt->bind_param("i", $user);
        $uStmt->execute();
        $uResult = $uStmt->get_result();
        $uData = $uResult->fetch_assoc();
        $email = $uData['email'];

        /* ------------------------------------------------
           FETCH RESOURCE NAME (res_id → resources table)
        -------------------------------------------------- */
        $resQuery = "SELECT res_name FROM resources WHERE res_id = ?";
        $rStmt = $conn->prepare($resQuery);
        $rStmt->bind_param("i", $resource);
        $rStmt->execute();
        $rResult = $rStmt->get_result();
        $rData = $rResult->fetch_assoc();
        $resourceName = $rData['res_name'];

        /* ------------------------------------------------
           SUCCESS MESSAGE FOR POPUP
        -------------------------------------------------- */
        $successMsg = urlencode("$email is successfully assigned $resourceName");

        header("Location: ../admin/view/resource_managers.php?success_msg=$successMsg");
        exit;

    } else {
        // INSERT FAILED
        header("Location: ../admin/view/resource_managers.php?error=Database insert failed");
        exit;
    }
}
?>
