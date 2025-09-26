<?php
require '../conn.php';
session_start();

if (isset($_POST["login"])) {
    $inputLRN = trim($_POST["lrn"]);
    $inputPassword = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT * FROM students WHERE LRN = ?");
    $stmt->bind_param("s", $inputLRN);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $students = $result->fetch_assoc();

        if ($students['IsActive'] == 0) {
            $error = "Your account has been disabled. Please contact the administrator.";
        } else {
            $stored_password = $students["Password"];

            if (empty($stored_password)) {
                $default_password = substr($students["LRN"], -4);

                if ($inputPassword === $default_password) {
                    $hashed = password_hash($inputPassword, PASSWORD_DEFAULT);
                    $update = $conn->prepare("UPDATE students SET Password = ? WHERE students_ID = ?");
                    $update->bind_param("si", $hashed, $students["students_ID"]);
                    $update->execute();

                    $_SESSION["students_ID"] = $students["students_ID"];
                    $_SESSION["students"] = $students["LRN"];
                    $_SESSION["student_name"] = $students["FirstName"] . ' ' . $students["LastName"];

                    header("Location: studentdash.php");
                    exit();
                } else {
                    $error = "Incorrect default password.";
                }
            } else {
                if (password_verify($inputPassword, $stored_password)) {
                    $_SESSION["students_ID"] = $students["students_ID"];
                    $_SESSION["students"] = $students["LRN"];
                    $_SESSION["student_name"] = $students["FirstName"] . ' ' . $students["LastName"];

                    header("Location: studentdash.php");
                    exit();
                } else {
                    $error = "Incorrect password.";
                }
            }
        }
    } else {
        $error = "LRN not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
      background-color: #ffffff;
      font-family: 'Segoe UI', sans-serif;
    }
    .header {
      background-color: #1b5e20;
      color: white;
      padding: 15px 0;
      text-align: center;
      font-weight: bold;
      font-size: 24px;
    }
    .container {
      margin-top: 10px;
      text-align: center;
    }
    .school-logo {
      width: 320px;
      height: 320px;
      margin-bottom: 30px;
    }
    .login-title {
      background-color: #f1c40f;
      color: #000;
      padding: 8px 16px;
      font-weight: bold;
      border-radius: 6px;
      display: inline-block;
      margin-bottom: 20px;
    }
    .login-card {
      max-width: 350px;
      margin: 0 auto;
      border: 1px solid #ddd;
      padding: 20px;
      border-radius: 8px;
    }
    .form-control {
      border-radius: 20px;
      margin-bottom: 15px;
    }
    .btn-login {
      background-color: #d4c804;
      color: black;
      width: 100%;
      border-radius: 20px;
      font-weight: bold;
    }
    .btn-login:hover {
      background-color: #c7ba00;
    }
  </style>
</head>
<body class="bg-light">
  <div class="header">Student Information Management System</div>

  <div class="container">
    <img src="../lnhs.png" alt="LNHS Logo" class="school-logo"><br>
    <div class="login-title">Student Login</div>

    <div class="login-card">
      <form action="" method="post">
        <div class="text-start mb-1 fw-bold">ID</div>
        <input type="text" name="lrn" required placeholder="Enter ID" class="form-control">

        <div class="text-start mb-1 fw-bold">Password</div>
        <input type="password" id="password" name="password" required placeholder="Enter Password" class="form-control">

        <div class="form-check mt-2 mb-3 text-start">
          <input class="form-check-input" type="checkbox" id="showPassword">
          <label class="form-check-label" for="showPassword">Show Password</label>
        </div>

        <input type="submit" name="login" value="LOGIN" class="btn btn-login">
      </form>

      <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?= $error ?></div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    const showPassword = document.querySelector("#showPassword");
    const passwordInput = document.querySelector("#password");

    showPassword.addEventListener("change", function(){
      passwordInput.type = this.checked ? "text" : "password";
    });
  </script>
</body>
</html>
