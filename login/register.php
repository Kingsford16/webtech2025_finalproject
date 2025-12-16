<?php
include '../settings/connection.php';

// Initialize errors array
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $staff_or_student_id = trim($_POST['staff_or_student_id'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $repassword = $_POST['repassword'] ?? '';

    /* ------------------------------
       VALIDATIONS
    ------------------------------ */

    // Check required fields
    if (empty($fname)) $errors['fname'] = "First name is required.";
    if (empty($lname)) $errors['lname'] = "Last name is required.";
    if (empty($email)) $errors['email'] = "Email is required.";
    if (empty($staff_or_student_id)) $errors['staff_or_student_id'] = "Student ID is required.";
    if (empty($phone)) $errors['phone'] = "Phone number is required.";
    if (empty($password)) $errors['password'] = "Password is required.";
    if (empty($repassword)) $errors['repassword'] = "Confirm password is required.";

    // Password validation
    if (!empty($password) && strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
    }
    if ($password !== $repassword) {
        $errors['repassword'] = "Passwords do not match.";
    }

    // Email validation (must end with @ashesi.edu.gh)
    if (!empty($email) && !preg_match('/@ashesi\.edu\.gh$/i', $email)) {
        $errors['email'] = "Email must end with @ashesi.edu.gh";
    }

    // Phone validation (E.164 format: +country code + number, e.g. +233xxxxxxxxx)
    if (!empty($phone) && !preg_match('/^\+[1-9]\d{1,14}$/', $phone)) {
        $errors['phone'] = "Phone must be in E.164 format (e.g. +233xxxxxxxxx)";
    }

    // Check for duplicates BEFORE image upload
    if (empty($errors)) {
        // Check email exists
        $check_email = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        if ($check_email->get_result()->num_rows > 0) {
            $errors['email'] = "Email already exists.";
        }

        // Check staff_or_student_id exists
        $check_student = $conn->prepare("SELECT user_id FROM users WHERE staff_or_student_id = ?");
        $check_student->bind_param("s", $staff_or_student_id);
        $check_student->execute();
        if ($check_student->get_result()->num_rows > 0) {
            $errors['staff_or_student_id'] = "Staff | Student ID already exists.";
        }

        // Check phone exists
        $check_phone = $conn->prepare("SELECT user_id FROM users WHERE phone = ?");
        $check_phone->bind_param("s", $phone);
        $check_phone->execute();
        if ($check_phone->get_result()->num_rows > 0) {
            $errors['phone'] = "Phone number already exists.";
        }
    }

    /* ------------------------------
       IMAGE VALIDATION (only if no other errors)
    ------------------------------ */
    $user_img = NULL;
    if (empty($errors) && !empty($_FILES['image']['name'])) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png']; // Removed GIF as per requirements

        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors['image'] = "Only JPG, JPEG, and PNG files are allowed.";
        } else {
            // Upload image
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/crms/images/users_uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            if (move_uploaded_file($fileTmpPath, $uploadDir . $newFileName)) {
                $user_img = '/crms/images/users_uploads/' . $newFileName;
            } else {
                $errors['image'] = "Failed to upload image.";
            }
        }
    }

    /* ------------------------------
       PROCESS VALID DATA
    ------------------------------ */
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // INSERT USER (fixed bind_param - 8 parameters)
        $sql = "INSERT INTO users (user_role, fname, lname, email, staff_or_student_id, phone, password, user_img)
                VALUES (3, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $fname, $lname, $email, $staff_or_student_id, $phone, $hashed_password, $user_img);
        $stmt->execute();

        header("Location: register.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>CRMS - Register</title>
    <link rel="stylesheet" href="../css/register.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .error { color: #e74c3c; font-size: 0.875rem; margin-top: 4px; display: block; }
    </style>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form name="signUpForm" action="register.php" method="post" enctype="multipart/form-data">
                
                <!-- FIRST NAME -->
                <input type="text" name="fname" placeholder="Enter First Name" value="<?php echo htmlspecialchars($_POST['fname'] ?? ''); ?>" required>
                <?php if (isset($errors['fname'])): ?><span class="error"><?php echo $errors['fname']; ?></span><?php endif; ?>

                <!-- LAST NAME -->
                <input type="text" name="lname" placeholder="Enter Last Name" value="<?php echo htmlspecialchars($_POST['lname'] ?? ''); ?>" required>
                <?php if (isset($errors['lname'])): ?><span class="error"><?php echo $errors['lname']; ?></span><?php endif; ?>

                <!-- EMAIL -->
                <input type="email" name="email" placeholder="Enter Email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                <?php if (isset($errors['email'])): ?><span class="error"><?php echo $errors['email']; ?></span><?php endif; ?>

                <!-- STUDENT ID -->
                <input type="text" name="staff_or_student_id" placeholder="Enter Staff | Student ID" value="<?php echo htmlspecialchars($_POST['staff_or_student_id'] ?? ''); ?>" required>
                <?php if (isset($errors['staff_or_student_id'])): ?><span class="error"><?php echo $errors['staff_or_student_id']; ?></span><?php endif; ?>

                <!-- PHONE -->
                <input type="tel" name="phone" placeholder="Enter Phone Number" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                <?php if (isset($errors['phone'])): ?><span class="error"><?php echo $errors['phone']; ?></span><?php endif; ?>

                <!-- PASSWORD -->
                <input type="password" name="password" placeholder="Enter Password" value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>" required>
                <?php if (isset($errors['password'])): ?><span class="error"><?php echo $errors['password']; ?></span><?php endif; ?>

                <!-- CONFIRM PASSWORD -->
                <input type="password" name="repassword" placeholder="Confirm Password" value="<?php echo htmlspecialchars($_POST['repassword'] ?? ''); ?>" required>
                <?php if (isset($errors['repassword'])): ?><span class="error"><?php echo $errors['repassword']; ?></span><?php endif; ?>

                <!-- IMAGE UPLOAD -->
                <label for="image" style="display:block; margin-top:12px; color:#333;">Upload profile picture (optional):</label>
                <input type="file" name="image" id="image" accept="image/jpeg,image/jpg,image/png">
                <?php if (isset($errors['image'])): ?><span class="error"><?php echo $errors['image']; ?></span><?php endif; ?>

                <button type="submit" class="registerbtn" id="register">Sign Up</button>
            </form>
        </div>

        <div class="login-container">
            <p><b style="color:black">Already have an account?</b>
                <a href="login.php" class="login-link"><b>Login</b></a>
            </p>
        </div>

        <p style="text-align:center;">
            <a href="../index.php" class="home-link"><b>Home</b></a>
        </p>
    </div>

    <!-- Registration Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-check-circle" style="color: green; font-size: 48px;"></i>
            </div>
            <div class="modal-body">
                <h2>Registration Successful!</h2>
                <p>Your account has been created successfully.</p>
            </div>
            <div class="modal-footer">
                <button id="okButton" class="ok-btn">OK</button>
            </div>
        </div>
    </div>

    <script>
    // Check URL for success flag
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
        const modal = document.getElementById('successModal');
        const okButton = document.getElementById('okButton');
        modal.style.display = 'block';
        okButton.addEventListener('click', () => {
            window.location.href = 'login.php';
        });
    }
    </script>
</body>
</html>
