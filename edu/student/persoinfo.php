<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['students'])) {
    header("Location: studentlogin.php");
    exit();
}

$lrn = $_SESSION['students'];
$stmt = $conn->prepare("SELECT * FROM students WHERE LRN = ?");
$stmt->bind_param("s", $lrn);
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_assoc();

if (!$students) {
    echo "<script>alert('Student not found.'); window.location.href='studentlogin.php';</script>";
    exit();
}

$studentName = $students['FirstName'] . ' ' . $students['LastName'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      overflow-y: auto;
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

    .btn-outline-light {
      font-family: Arial, Helvetica, sans-serif;
    }

    .btn-icon {
      margin-right: 8px;
      width: 20px;
    }

    .form-section {
      background-color: #fffde7;
      padding: 25px;
      border-radius: 10px;
      min-height: 400px;
    }

    h4.text-center {
      background-color: #0d4b16;
      border-radius: 25px;
      padding: 9px;
      width: 50%;
      color: #fff;
      margin: 0 auto 20px;
    }

    .card-info {
      background: #ffffff;
      border: 1px solid #ccc;
      border-radius: 15px;
      padding: 30px;
      max-width: 700px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .info-label {
      font-weight: bold;
      color: #333;
    }

    .info-value {
      font-weight: 500;
    }

    .container-fluid {
      min-height: calc(100vh - 70px); /* Adjust for header height */
    }

    .row {
      min-height: 100%;
    }

    @media (max-width: 992px) {
      .sidebar {
        height: auto;
        padding: 10px;
      }

      .form-section {
        padding: 20px;
      }

      h4.text-center {
        width: 80%;
        font-size: 1.1rem;
      }

      .card-info {
        padding: 20px;
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
        overflow-y: visible;
      }

      .container-fluid {
        min-height: auto;
      }

      .row {
        min-height: auto;
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

      .card-info {
        padding: 15px;
        margin: 10px;
      }

      .row > div {
        width: 100%;
        margin-bottom: 10px;
      }

      .info-label, .info-value {
        font-size: 16px;
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

      .avatar {
        width: 40px;
        height: 40px;
      }

      .form-section {
        padding: 10px;
      }

      h4.text-center {
        width: 100%;
        font-size: 0.9rem;
        padding: 6px;
      }

      .card-info {
        padding: 10px;
      }

      .info-label, .info-value {
        font-size: 15px;
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
        padding: 6px;
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

      .avatar {
        width: 35px;
        height: 35px;
      }

      .form-section {
        padding: 8px;
      }

      h4.text-center {
        font-size: 0.85rem;
        padding: 5px;
      }

      .card-info {
        padding: 8px;
      }

      .info-label, .info-value {
        font-size: 14px;
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
            <small><?= htmlspecialchars($studentName) ?></small>
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
          <a href="persoinfo.php" class="btn btn-outline-light sub-btn active">
            <i class="bi bi-person btn-icon"></i>Personal Information
          </a>
          <a href="passmanage.php" class="btn btn-outline-light sub-btn">
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
          <h4 class="text-center mb-4">Personal Information</h4>
          <div class="card-info">
            <div class="row mb-3">
              <div class="col-sm-4 info-label">First Name:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($students['FirstName']) ?></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Middle Name:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($students['MiddleName']) ?></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Last Name:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($students['LastName']) ?></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Sex:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($students['Sex']) ?></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Birthdate:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($students['Birthdate']) ?></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">LRN:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($students['LRN']) ?></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Contact Number:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($students['ContactNumber']) ?></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Email Address:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($students['EmailAddress']) ?></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Address:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($students['Address']) ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script>
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
