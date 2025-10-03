<?php
session_start();
require '../conn.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacherlogin.php");
    exit();
}

$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    $teacherID = $_SESSION['teacher_id'];

    // Fetch current password
    $stmt = $conn->prepare("SELECT Password, EmployeeID FROM teachers WHERE teachers_ID = ?");
    $stmt->bind_param("i", $teacherID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        $errorMessage = "Teacher not found!";
    } else {
        $dbPassword = $row['Password'];
        $defaultPassword = substr($row['EmployeeID'], -4); // in case still using default

        // Validate current password (check hash or default)
        if ((!empty($dbPassword) && password_verify($currentPassword, $dbPassword)) ||
            ($currentPassword === $defaultPassword && empty($dbPassword))) {
            
            if ($newPassword !== $confirmPassword) {
                $errorMessage = "New passwords do not match!";
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $update = $conn->prepare("UPDATE teachers SET Password = ? WHERE teachers_ID = ?");
                $update->bind_param("si", $hashedPassword, $teacherID);

                if ($update->execute()) {
                    $successMessage = "Password updated successfully!";
                } else {
                    $errorMessage = "Update failed: " . $conn->error;
                }
            }
        } else {
            $errorMessage = "Current password is incorrect!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SIMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family:'Segoe UI',sans-serif;
      background-color:#f5f5dc;
      overflow:hidden;
    }
    .sidebar {
      background-color:#0d4b16;
      min-height:100vh;
      color:white;
      padding:20px;
    }
    .sidebar .btn {
      width:100%;
      text-align:left;
      margin-bottom:10px;
    }
    .logout {
      color:red;
      font-weight:bold;
    }
    .header {
      background-color:#1b5e20;
      color:white;
      padding:15px;
      text-align:center;
      font-size:24px;
      font-weight:bold;
    }
    .avatar {
      width:70px;
      height:70px;
      border-radius:50%;
    }
    .form-section {
      background-color:#fffde7;
      padding:30px;
      border-radius:10px;
      min-height:300px;
    }
    .form-control {
      border-radius:20px;
      margin-bottom:15px;
    }
    .register-btn {
      background-color:#124820;
      color:white;
      border-radius:25px;
      padding:10px 30px;
      font-weight:bold;
      width:100%;
      max-width:400px;
    }
    .register-btn:hover {
      background-color:#a8aa10ff;
    }
    h4.text-center {
      background-color:#0d4b16;
      border-radius:25px;
      padding:9px;
      width:50%;
      color:#ffff;
      margin:0 auto;
    }
    .btn-outline-light {
      font-family: Arial, Helvetica, sans-serif;
    }
  </style>
</head>
<body>

<div class="header">Student Information Management System</div>

<div class="container-fluid">
  <div class="row flex-column flex-md-row">
    <div class="col-12 col-md-3 sidebar">
      <div class="mb-4 d-flex align-items-center">
        <a href="teacherdash.php" style="text-decoration: none;">
          <img src="lnhslogo.png" alt="Teacher" class="avatar me-2">
        </a>
        <div>
          <div style="font-size:25px;">Teacher</div>
          <small><?= htmlspecialchars($_SESSION['teacher_name'] ?? '') ?></small>
        </div>
      </div>

      <a href="addstudteacher.php" class="btn btn-outline-light ">Student Registration</a>
        <a href="manageteach.php" class="btn btn-outline-light">Manage Informations</a>
        <a href="gradesmanage.php" class="btn btn-outline-light">Grades Management</a>
        <a href="persoinfoteach.php" class="btn btn-outline-light">Personal Information</a>
        <a href="passteach.php" class="btn btn-outline-light active">Password Management</a>
        <br><br>
      <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
    </div>

    <div class="col-12 col-md-9 p-4">
      <div class="form-section">
        <h4 class="text-center mb-4">Change your Password</h4>

        <?php if ($successMessage): ?>
          <div class="alert alert-success text-center"><?= $successMessage ?></div>
        <?php elseif ($errorMessage): ?>
          <div class="alert alert-danger text-center"><?= $errorMessage ?></div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="row justify-content-center">
            <div class="col-12 col-md-6">
              <input type="password" name="currentPassword" class="form-control" placeholder="Enter Current Password" required>
              <input type="password" name="newPassword" class="form-control" placeholder="Enter New Password" required>
              <input type="password" name="confirmPassword" class="form-control" placeholder="Confirm New Password" required>
              <div class="d-flex justify-content-center">
                <button type="submit" class="btn register-btn mt-2">UPDATE PASSWORD</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
