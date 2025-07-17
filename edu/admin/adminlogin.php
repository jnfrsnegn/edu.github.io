<?php
require '../conn.php';
session_start();

if (isset($_POST["login"])) {
    $employee_id = $_POST["employee_id"];
    $input_password = $_POST["password"];

    $query = "SELECT * FROM admin WHERE EmployeeID = '$employee_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        $expected_password = substr($admin["EmployeeID"], -4);

        if ($input_password === $expected_password) {
            $_SESSION["admin_id"] = $admin["ID"];
            $_SESSION["admin_name"] = $admin["FirstName"] . ' ' . $admin["LastName"];
            header("Location: ../admin/admindash.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Employee ID not found.";
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
    <img src="lnhslogo.png" alt="LNHS Logo" class="school-logo"><br>
    <div class="login-title">Administrator Login</div>

    <div class="login-card">
      <form action="" method="post">
        <div class="text-start mb-1 fw-bold">Employee ID</div>
        <input type="text" name="employee_id" required placeholder="Enter ID" class="form-control">

        <div class="text-start mb-1 fw-bold">Password</div>
        <input type="password" name="password" required placeholder="Enter Password" class="form-control">

        <input type="submit" name="login" value="LOGIN" class="btn btn-login">
      </form>

      <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
