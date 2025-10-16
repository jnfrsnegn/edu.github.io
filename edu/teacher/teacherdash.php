<?php
session_start();
if (!isset($_SESSION['teacher'])) {
  header("Location: ../teacher/teacherlogin.php");
  exit();
}
$teacherName = $_SESSION['teacher_name'];
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

    @media (max-width: 992px) {
      .sidebar {
        height: auto;
        padding: 10px;
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
          <img src="lnhslogo.png" alt="Teacher" class="avatar me-2">
          <div>
            <div style="font-size:20px;">Teacher</div>
            <small><?= htmlspecialchars($teacherName) ?></small>
          </div>
        </div>
        <a href="#collapseStudents" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseStudents">
          <i class="bi bi-people btn-icon"></i>Students
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseStudents">
          <a href="addstudteacher.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person-plus btn-icon"></i>Student Registration
          </a>
          <a href="studinfoteach.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person-gear btn-icon"></i>Student Informations
          </a>
        </div>
        <a href="#collapseGrades" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseGrades">
          <i class="bi bi-clipboard-data btn-icon"></i>Grades
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseGrades">
          <a href="gradesmanage.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-clipboard-data btn-icon"></i>Grades Management
          </a>
        </div>
        <a href="#collapseAccount" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseAccount">
          <i class="bi bi-person-circle btn-icon"></i>Account
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseAccount">
          <a href="persoinfoteach.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person btn-icon"></i>Personal Information
          </a>
          <a href="passteach.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-lock btn-icon"></i>Password Management
          </a>
        </div>
        <br><br>
        <a href="#" class="logout text-decoration-none" id="logoutBtn">
    <i class="bi bi-box-arrow-right me-2"></i>Logout
</a>
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