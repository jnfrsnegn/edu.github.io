<?php
session_start();

if (!isset($_SESSION['student_name'])) {
    header("Location: studentlogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f5dc;
      overflow: hidden;
    }

    .sidebar {
      background-color: #0d4b16;
      height: 100vh;
      color: white;
      padding: 20px;
    }

    .sidebar .btn {
      width: 100%;
      text-align: left;
      margin-bottom: 10px;
    }

    .logout {
      color: red;
      font-weight: bold;
    }

    .header {
      background-color: #1b5e20;
      color: white;
      padding: 15px;
      text-align: center;
      font-size: 24px;
      font-weight: bold;
    }

    .avatar {
      width: 70px;
      height: 70px;
      border-radius: 50%;
    }
    .btn-outline-light{
      font-family: Arial, Helvetica, sans-serif;
    }
  </style>
</head>
<body>

  <div class="header">Student Information Management System</div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3 sidebar">
        <div class="mb-4 d-flex align-items-center">
          <img src="lnhslogo.png" alt="Student" class="avatar me-2">
          <div>
            <div style="font-size:25px;">Student</div>
            <small><?php echo htmlspecialchars($_SESSION['student_name']); ?></small>
          </div>
        </div>

       <a href="viewgrades.php" class="btn btn-outline-light">View Grades</a>
        <a href="persoinfo.php" class="btn btn-outline-light">Personal Information</a>
        <a href="reqdocs.php" class="btn btn-outline-light">Request Form</a>
        <a href="passmanage.php" class="btn btn-outline-light">Password Management</a>
        <br><br>
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout </a>
      </div>

      <div class="col-md-9 p-4">
      
      </div>
    </div>
  </div>

</body>
</html>
