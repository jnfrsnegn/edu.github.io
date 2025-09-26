<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #ffffff;
      font-family: 'Segoe UI', sans-serif;
      overflow: hidden;
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
    .login-button {
      background-color: #1b5e20;
      color: white;
      border: none;
      width: 200px;
      margin: 10px 0;
      padding: 10px;
      font-size: 16px;
      border-radius: 6px;
      transition: 0.3s;
    }
    .login-button:hover {
      background-color: #145c17;
    }
  </style>
</head>
<body>

  <div class="header">
   Student Information Management System
  </div>

  <div class="container">
    <form action="" method="post">
      <img src="lnhs.png" alt="LNHS Logo" class="school-logo"><br>
      <button type="submit" name="admin-button" value="admin-button" class="login-button fw-bold">Administrator</button><br>
      <button type="submit" name="tea-button" value="tea-button" class="login-button fw-bold">Teacher</button><br>
      <button type="submit" name="stud-button" value="stud-button" class="login-button fw-bold">Student</button><br>
      <button type="submit" name="par-button" value="par-button" class="login-button fw-bold">Parent</button>
    </form>
  </div>

  <?php
    if(isset($_POST["admin-button"])) {
      header("Location: admin/adminlogin.php");
    }
    if(isset($_POST["tea-button"])) {
      header("Location: teacher/teacherlogin.php");
    }
    if(isset($_POST["stud-button"])) {
      header("Location: student/studentlogin.php");
    }
    if(isset($_POST["par-button"])) {
      header("Location: parent/parentlogin.php");
    }
  ?>
  
</body>
</html>