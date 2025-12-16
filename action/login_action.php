<?php
session_start();
include '../settings/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT user_id, password, user_role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {

        $stmt->bind_result($user_id, $hashed_password, $user_role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {

            $_SESSION['user_id'] = $user_id;
            $_SESSION['role_id'] = $user_role;

            if ($user_role == 1) {
                header("Location: ../admin/view/admin_dashboard.php");
            } elseif ($user_role == 2) {
                header("Location: ../staff/view/staff_dashboard.php");
            } elseif ($user_role == 3) {
                header("Location: ../student/view/student_dashboard.php");
            } else {
                header("Location: ../login/login.php?error=Wrong email or password");
            }
            exit();
        } 
        else {
            header("Location: ../login/login.php?error=Wrong email or password");
            exit();
        }

    } else {
        header("Location: ../login/login.php?error=Wrong email or password");
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: ../login/login.php?error=Invalid request");
    exit();
}
?>
