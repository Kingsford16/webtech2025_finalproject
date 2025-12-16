<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CRMS - Login</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/login.css" />
  </head>

  <body>
    <div class="container">
      <form name="loginForm" action="../action/login_action.php" method="post" onsubmit="return validateForm()">

        <h1>Login</h1>

        <!-- ERROR MESSAGE (from login_action.php) -->
        <?php if (isset($_GET['error'])): ?>
        <div id="errorMessage" style="
        width: 100%;
        padding: 12px;
        margin-top: 15px;
        margin-bottom: 15px;
        background-color: rgba(255, 255, 0, 0.20);
        border: 1px solid #ffeb3b;
        color: #ffeb3b;
        font-weight: 700;
        text-align: center;
        border-radius: 6px;
        font-size: 15px;
        ">
        <?php echo htmlspecialchars($_GET['error']); ?>
        </div>

        <script>
        setTimeout(function() {
            let msg = document.getElementById("errorMessage");
            if (msg) {
                msg.style.transition = "opacity 0.8s ease";
                msg.style.opacity = "0";
                setTimeout(() => msg.remove(), 800); 
            }
        }, 3000); // 3 seconds
        </script>
        <?php endif; ?>


        <div class="input-box">
          <input type="email" placeholder="email" name="email" id="email" required />
          <i class="bx bxs-user"></i>
        </div>

        <div class="input-box">
          <input type="password" placeholder="password" name="password" id="password" required />
          <i class="bx bxs-lock-alt"></i>
        </div>

        <button type="submit" class="btn">Login</button>

        <div class="register-link">
          <p><b>Don't have an account?</b> <a href="register.php" style="color:burlywood;"><b>Register here</b></a></p>
        </div>

        <div class="register-link">
          <p><a href="../index.php" style="color:burlywood;"><b>Home</b></a></p>
        </div>

      </form>
    </div>

    <script>
      function validateForm() {

        let email = document.getElementById("email").value.trim();
        let password = document.getElementById("password").value.trim();

        // REGEX: simple email format
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
          alert("Please enter a valid email format.");
          return false;
        }

        if (password.length < 3) {
          alert("Password seems too short.");
          return false;
        }

        return true;
      }
    </script>

  </body>
</html>
