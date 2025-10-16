<?php
session_start();
require '../conn.php';

if (!isset($_SESSION['students_ID'])) {
    header("Location: studentlogin.php");
    exit();
}

$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    $student_id = $_SESSION['students_ID'];

    $stmt = $conn->prepare("SELECT Password FROM students WHERE students_ID = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        $errorMessage = "Student not found!";
    } elseif (!password_verify($currentPassword, $row['Password'])) {
        $errorMessage = "Current password is incorrect!";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = "New passwords do not match!";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE students SET Password = ? WHERE students_ID = ?");
        $update->bind_param("si", $hashedPassword, $student_id);
        if ($update->execute()) {
            $successMessage = "Password updated successfully!";
        } else {
            $errorMessage = "Update failed: " . $conn->error;
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f5dc;
      overflow-x: hidden;
    }

    .header {
      background-color: #1b5e20;
      color: white;
      padding: 15px;
      text-align: center;
      font-size: 20px;
      font-weight: bold;
    }

    .sidebar {
      background-color: #0d4b16;
      height: 100vh;
      color: white;
      padding: 15px;
    }

    .sidebar .btn {
      width: 100%;
      text-align: left;
      margin-bottom: 10px;
      position: relative;
      font-size: 15px;
    }

    .sidebar .btn i.bi-chevron-right {
      transition: transform 0.3s ease;
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
    }

    .sidebar .btn[aria-expanded="true"] i.bi-chevron-right {
      transform: translateY(-50%) rotate(90deg);
    }

    .sidebar .sub-btn {
      width: calc(100% - 15px);
      margin-left: 15px;
      margin-bottom: 5px;
    }

    .sidebar .sub-btn.active {
      background-color: #1b5e20;
      border-color: #1b5e20;
    }

    .logout {
      color: red;
      font-weight: bold;
    }

    .avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
    }

    .form-section {
      background-color: #fffde7;
      padding: 25px;
      border-radius: 10px;
      min-height: 300px;
    }

    .form-title {
      font-size: 22px;
      font-weight: bold;
      margin-bottom: 25px;
      color: #1b5e20;
      text-align: center;
    }

    .form-control {
      border-radius: 20px;
      margin-bottom: 15px;
    }

    .register-btn {
      background-color: #124820;
      color: white;
      border-radius: 25px;
      padding: 10px 30px;
      font-weight: bold;
      width: 100%;
      max-width: 400px;
    }

    .register-btn:hover {
      background-color: #a8aa10ff;
    }

    h4.text-center {
      background-color: #0d4b16;
      border-radius: 25px;
      padding: 10px;
      width: 50%;
      color: #ffff;
      margin: 0 auto 20px;
    }

    .btn-outline-light {
      font-family: Arial, Helvetica, sans-serif;
    }

    .btn-icon {
      margin-right: 8px;
      width: 20px;
    }

    @media (max-width: 992px) {
      .sidebar {
        height: auto;
        padding: 10px;
      }

      h4.text-center {
        width: 80%;
        font-size: 1.1rem;
      }

      .form-section {
        padding: 20px;
      }

      .form-title {
        font-size: 20px;
      }
    }

    @media (max-width: 768px) {
      .row {
        flex-direction: column;
      }

      .sidebar {
        position: relative;
        height: auto;
        width: 100%;
        order: -1;
      }

      .avatar {
        width: 50px;
        height: 50px;
      }

      .header {
        font-size: 18px;
      }

      .form-section {
        padding: 15px;
      }

      h4.text-center {
        width: 90%;
        padding: 8px;
        font-size: 1rem;
      }

      .form-title {
        font-size: 18px;
        margin-bottom: 20px;
      }

      .form-control {
        font-size: 16px; 
        padding: 12px 15px; 
      }

      .register-btn {
        padding: 12px 30px; 
        font-size: 16px;
      }

      .col-12.col-md-6 {
        padding: 0 5px; 
      }

      .alert {
        margin-bottom: 15px;
        padding: 12px; 
      }

      .sidebar .btn {
        font-size: 14px;
      }

      .sidebar .sub-btn {
        width: calc(100% - 10px);
        margin-left: 10px;
        font-size: 13px;
      }

      .logout {
        font-size: 14px;
      }
    }

    @media (max-width: 480px) {
      .header {
        font-size: 16px;
        padding: 10px;
      }

      .sidebar .btn {
        font-size: 14px;
      }

      .form-section {
        padding: 10px;
      }

      h4.text-center {
        width: 100%;
        font-size: 0.9rem;
        padding: 6px;
      }

      .form-title {
        font-size: 16px;
        margin-bottom: 15px;
      }

      .form-control {
        font-size: 16px;
        margin-bottom: 12px;
        padding: 12px 12px;
      }

      .register-btn {
        font-size: 16px;
        padding: 12px 20px;
      }

      .alert {
        font-size: 14px;
        padding: 10px;
      }

      .sidebar {
        padding: 8px;
      }

      .sidebar .btn {
        font-size: 13px;
        padding: 8px;
      }

      .sidebar .sub-btn {
        font-size: 12px;
        padding: 6px;
      }

      .logout {
        font-size: 13px;
      }
    }

    @media (max-width: 320px) {
      .header {
        font-size: 15px;
        padding: 8px;
      }

      .sidebar {
        padding: 8px;
      }

      .sidebar .btn {
        font-size: 13px;
      }

      .form-section {
        padding: 8px;
      }

      h4.text-center {
        font-size: 0.85rem;
        padding: 5px;
      }

      .form-title {
        font-size: 15px;
        margin-bottom: 12px;
      }

      .form-control {
        font-size: 16px;
        padding: 10px 10px;
        margin-bottom: 10px;
      }

      .register-btn {
        font-size: 15px;
        padding: 10px 15px;
      }

      .alert {
        font-size: 13px;
        padding: 8px;
      }

      .sidebar .btn {
        font-size: 12px;
        padding: 6px;
      }

      .sidebar .sub-btn {
        font-size: 11px;
        padding: 4px;
        margin-left: 8px;
      }

      .logout {
        font-size: 12px;
      }
    }
  </style>
</head>
<body>

<div class="header">Student Information Management System</div>

<div class="container-fluid">
  <div class="row flex-column flex-md-row">
    <div class="col-md-3 sidebar">
      <div class="mb-3 d-flex align-items-center">
        <a href="studentdash.php" style="text-decoration: none;">
          <img src="lnhslogo.png" alt="Student" class="avatar me-2">
        </a>
        <div>
          <div style="font-size:20px;">Student</div>
          <small><?= htmlspecialchars($_SESSION['student_name'] ?? '') ?></small>
        </div>
      </div>

      <a href="viewgrades.php" class="btn btn-outline-light">
        <i class="bi bi-clipboard-data btn-icon"></i>View Grades
      </a>

      <a href="#collapseAccount" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseAccount">
        <i class="bi bi-person-circle btn-icon"></i>Account
        <i class="bi bi-chevron-right"></i>
      </a>
      <div class="collapse show" id="collapseAccount">
        <a href="persoinfo.php" class="btn btn-outline-light sub-btn">
          <i class="bi bi-person btn-icon"></i>Personal Information
        </a>
        <a href="passmanage.php" class="btn btn-outline-light sub-btn active">
          <i class="bi bi-lock btn-icon"></i>Password Management
        </a>
      </div>

      <a href="reqdocs.php" class="btn btn-outline-light">
        <i class="bi bi-file-earmark-text btn-icon"></i>Request Form
      </a>

      <br><br>
      <a href="#" class="logout text-decoration-none" id="logoutBtn">
        <i class="bi bi-box-arrow-right me-2"></i>Logout
      </a>
    </div>

    <div class="col-md-9 p-3">
      <div class="form-section">
        <h4 class="text-center mb-4">Change your Password</h4>

        <form method="POST" action="" id="passwordForm">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('passwordForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Update Password?',
            text: "Are you sure you want to change your password?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1b5e20',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, save changes.',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); 
            }
        });
    });

    <?php if ($successMessage): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: <?= json_encode($successMessage) ?>,
            confirmButtonColor: '#124820'
        });
    <?php elseif ($errorMessage): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: <?= json_encode($errorMessage) ?>,
            confirmButtonColor: '#d33'
        });
    <?php endif; ?>
});

document.getElementById('logoutBtn').addEventListener('click', function(e) {
    e.preventDefault(); 
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of the system.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1b5e20',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'logout.php';
        }
    });
});
</script>
</body>
</html>
