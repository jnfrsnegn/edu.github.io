<?php
session_start();
$teacherName = $_SESSION['teacher_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SIMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
      min-height: 800px;
    }

    .card {
      background-color: #8f913aff;
      border-radius: 15px;
      cursor: pointer;
      transition: transform 0.2s ease-in-out;
      min-height: 500px; 
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 120px;
    }

    .card:hover {
      transform: scale(1.03);
    }

    .card a {
      color: inherit;
      text-decoration: none;
    }

    .card a:hover {
      text-decoration: none;
    }

    .card-icon-wrapper {
      background-color: #f3f3f3;
      width: 120px;
      height: 120px;
      border-radius: 50%;
      margin: 0 auto 20px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card-body {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .card-title {
      margin-top: 10px;
    }

    .btn-outline-light{
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

      .form-section {
        padding: 20px;
        min-height: auto;
      }

      .card {
        min-height: 400px;
        margin-bottom: 80px;
      }

      .card-icon-wrapper {
        width: 100px;
        height: 100px;
      }

      .card-title {
        font-size: 1.1rem;
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
        order: -1; /* Sidebar first on mobile */
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

      .card {
        min-height: 300px;
        margin-bottom: 60px;
        width: 100%;
      }

      .card-icon-wrapper {
        width: 80px;
        height: 80px;
      }

      .card-icon-wrapper img {
        width: 50px;
      }

      .card-title {
        font-size: 1rem;
        padding: 0 10px;
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

      .form-section {
        padding: 10px;
      }

      .card {
        min-height: 250px;
        margin-bottom: 40px;
        border-radius: 10px;
      }

      .card-icon-wrapper {
        width: 70px;
        height: 70px;
      }

      .card-icon-wrapper img {
        width: 40px;
      }

      .card-title {
        font-size: 0.9rem;
        padding: 0 5px;
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
        width: 40px;
        height: 40px;
      }

      .form-section {
        padding: 8px;
      }

      .card {
        min-height: 200px;
        margin-bottom: 30px;
        border-radius: 8px;
      }

      .card-icon-wrapper {
        width: 60px;
        height: 60px;
      }

      .card-icon-wrapper img {
        width: 35px;
      }

      .card-title {
        font-size: 0.85rem;
        padding: 0 3px;
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
          <a href="teacherdash.php" style="text-decoration: none;">
                        <img src="lnhslogo.png" alt="Teacher" class="avatar me-2">
                    </a>
          <div>
            <div style="font-size:20px;">Teacher</div>
            <small><?= htmlspecialchars($teacherName) ?></small>
          </div>
        </div>

        <!-- Students Sub-Menu (Expanded by default on this page) -->
        <a href="#collapseStudents" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseStudents">
          <i class="bi bi-people btn-icon"></i>Students
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse show" id="collapseStudents">
          <a href="addstudteacher.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person-plus btn-icon"></i>Student Registration
          </a>
          <a href="manageteach.php" class="btn btn-outline-light sub-btn active">
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
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">
          <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
      </div>

      <div class="col-md-9 p-3">
        <div class="form-section">
          <div class="row justify-content-center text-center w-100">
            <div class="col-12 col-md-4 mb-4 d-flex justify-content-center">
              <a href="studinfoteach.php" class="w-100 text-decoration-none">
                <div class="card shadow">
                  <div class="card-body">
                    <div class="card-icon-wrapper">
                      <img src="avatar.png" alt="Student Icon" style="width: 70px;">
                    </div>
                    <h5 class="card-title text-white">Student Information</h5>
                  </div>
                </div>
              </a>
            </div>

            
          </div>
        </div>
      </div> 
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>