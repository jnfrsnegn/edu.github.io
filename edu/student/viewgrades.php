<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['students'])) {
  header("Location: studentlogin.php");
  exit();
}

$student_lrn = $_SESSION['students'];

$student = $conn->query("SELECT students_ID, FirstName, LastName 
                         FROM students WHERE LRN = '$student_lrn'")->fetch_assoc();
$student_id = $student['students_ID'];
$studentName = $student['FirstName'] . ' ' . $student['LastName'];

$grades = $conn->query("
    SELECT s.SubjectName, yl.YearName, sec.SectionName, g.grade, g.quarter
    FROM grades g
    JOIN subjects s ON g.subject_ID = s.subject_ID
    JOIN yearlevels yl ON s.YearLevelID = yl.yearlevel_ID
    JOIN sections sec ON s.SectionID = sec.section_ID
    WHERE g.students_ID = '$student_id'
    ORDER BY s.SubjectName, g.quarter
");

$gradesBySubject = [];
while ($row = $grades->fetch_assoc()) {
  $subject = $row['SubjectName'];
  $gradesBySubject[$subject][$row['quarter']] = $row['grade'];
}

$finalAverages = [];
foreach ($gradesBySubject as $subject => $quarterGrades) {
  $total = 0;
  $count = 0;
  for ($q = 1; $q <= 4; $q++) {
    if (isset($quarterGrades[$q])) {
      $total += $quarterGrades[$q];
      $count++;
    }
  }
  $finalAverages[$subject] = $count > 0 ? round($total / $count, 2) : null;
}
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

    th {
      background-color: #1b5e20;
      color: white;
      text-align: center;
    }

    td, th {
      padding: 8px;
      text-align: center;
      font-size: 14px;
      vertical-align: middle;
    }

    .table-responsive {
      overflow-x: auto;
    }

    .container-fluid {
      min-height: calc(100vh - 70px);
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

      .table-responsive {
        font-size: 0.85rem;
      }

      .table th,
      .table td {
        padding: 4px;
        white-space: nowrap;
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

      .table th,
      .table td {
        font-size: 12px;
        padding: 6px 2px;
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

      .table th,
      .table td {
        font-size: 11px;
        padding: 4px 1px;
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

        <a href="viewgrades.php" class="btn btn-outline-light active">
          <i class="bi bi-clipboard-data btn-icon"></i>View Grades
        </a>

        <a href="#collapseAccount" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseAccount">
          <i class="bi bi-person-circle btn-icon"></i>Account
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseAccount">
          <a href="persoinfo.php" class="btn btn-outline-light sub-btn">
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
          <h3 class="text-center mb-4">My Grades</h3>

          <?php if (!empty($gradesBySubject)): ?>
            <div class="table-responsive">
              <table class="table table-bordered text-center">
                <thead>
                  <tr>
                    <th>Subject</th>
                    <th>Quarter 1</th>
                    <th>Quarter 2</th>
                    <th>Quarter 3</th>
                    <th>Quarter 4</th>
                    <th>Final Average</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($gradesBySubject as $subject => $quarterGrades): ?>
                    <tr>
                      <td class="text-start"><?= htmlspecialchars($subject) ?></td>
                      <?php for ($q = 1; $q <= 4; $q++): ?>
                        <td><?= isset($quarterGrades[$q]) ? htmlspecialchars($quarterGrades[$q]) : '<span class="text-muted">Not graded</span>' ?></td>
                      <?php endfor; ?>
                      <td><?= $finalAverages[$subject] !== null ? $finalAverages[$subject] : '<span class="text-muted">N/A</span>' ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="alert alert-warning text-center">No grades recorded yet.</div>
          <?php endif; ?>
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
