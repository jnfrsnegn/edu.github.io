<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['teacher_id'])) {
  header("Location: teacherlogin.php");
  exit();
}

$teacher_id = $_SESSION['teacher_id'];
$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_grades'])) {
  $quarter = (int)$_POST['quarter'];

  foreach ($_POST['grades'] as $student_id => $grade) {
    $grade = round(floatval($grade), 2);

    if ($grade < 60 || $grade > 100) {
      $errorMessage = "Grades must be between 60.00 and 100.00 (Student ID: $student_id).";
      break;
    }

    $checkActive = $conn->query("SELECT IsActive FROM students WHERE students_ID = '$student_id'");
    if ($checkActive->num_rows > 0) {
      $rowActive = $checkActive->fetch_assoc();
      if ($rowActive['IsActive'] == 0) {
        $errorMessage = "You cannot give grades to a disabled student (ID: $student_id).";
        break;
      }
    }

    if ($quarter > 1) {
      $prevQuarter = $quarter - 1;
      $checkPrev = $conn->query("SELECT * FROM grades 
          WHERE students_ID = '$student_id' 
          AND subject_ID = '{$_POST['subject_ID']}' 
          AND quarter = '$prevQuarter'");
      if ($checkPrev->num_rows == 0) {
        $errorMessage = "You must enter grades for Quarter $prevQuarter first.";
        break;
      }
    }

    $check = $conn->query("SELECT * FROM grades 
        WHERE students_ID = '$student_id' 
        AND subject_ID = '{$_POST['subject_ID']}' 
        AND quarter = '$quarter'");
    if ($check->num_rows > 0) {
      $conn->query("UPDATE grades 
          SET grade = '$grade', created_at = NOW() 
          WHERE students_ID = '$student_id' 
          AND subject_ID = '{$_POST['subject_ID']}' 
          AND quarter = '$quarter'");
    } else {
      $conn->query("INSERT INTO grades (students_ID, subject_ID, grade, quarter, created_at) 
          VALUES ('$student_id', '{$_POST['subject_ID']}', '$grade', '$quarter', NOW())");
    }
  }

  if (!$errorMessage) {
    $successMessage = "Grades saved successfully!";
  }
}

$subjects = $conn->query("
    SELECT s.subject_ID, s.SubjectName, yl.YearName, sec.SectionName
    FROM subjects s
    JOIN yearlevels yl ON s.YearLevelID = yl.yearlevel_ID
    JOIN sections sec ON s.SectionID = sec.section_ID
    JOIN teacher_subjects ts ON ts.subject_ID = s.subject_ID
    WHERE ts.teachers_ID = '$teacher_id'
    ORDER BY yl.YearName, sec.SectionName
");

$selected_subject = $_GET['subject'] ?? '';
$selected_quarter = (int)($_GET['quarter'] ?? 1);
$students = [];

if (!empty($selected_subject)) {
  $students = $conn->query("
        SELECT st.students_ID, st.FirstName, st.LastName, st.IsActive, g.grade
        FROM students st
        JOIN subjects s ON s.SectionID = st.SectionID
        LEFT JOIN grades g ON g.students_ID = st.students_ID 
            AND g.subject_ID = '$selected_subject' 
            AND g.quarter = '$selected_quarter'
        WHERE s.subject_ID = '$selected_subject'
        ORDER BY st.LastName
    ");
}

$teacherName = $_SESSION['teacher_name'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIMS - Grades Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      background: #f5f5dc;
      font-family: 'Segoe UI', sans-serif;
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

    .main-section {
      background-color: #fffde7;
      padding: 30px;
      border-radius: 10px;
      min-height: 80vh;
    }

    .btn-primary {
      background: #124820;
      border: none;
    }

    .btn-primary:hover {
      background: #a8aa10;
      color: black;
    }

    th {
      background-color: #1b5e20;
      color: white;
      text-align: center;
    }

    td {
      text-align: center;
      vertical-align: middle;
    }

    h4.text-center {
      background-color: #0d4b16;
      border-radius: 25px;
      padding: 10px;
      width: 60%;
      color: #fff;
      margin: 0 auto 20px auto;
    }

    .sidebar .sub-btn.active {
      background-color: #1b5e20;
      border-color: #1b5e20;
    }

    @media (max-width: 992px) {
      .sidebar {
        height: auto;
        padding: 10px;
      }

      .main-section {
        padding: 20px;
      }

      .avatar {
        width: 60px;
        height: 60px;
      }

      h4.text-center {
        width: 80%;
        font-size: 18px;
        padding: 8px;
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

      .header {
        font-size: 18px;
      }

      .avatar {
        width: 50px;
        height: 50px;
      }

      .main-section {
        padding: 15px;
      }

      h4.text-center {
        width: 90%;
        font-size: 16px;
        padding: 6px;
      }

      .table-responsive {
        font-size: 0.85rem;
      }

      th,
      td {
        padding: 4px;
      }

      .d-flex.flex-column.flex-sm-row {
        flex-direction: column !important;
        gap: 10px;
      }

      .form-select {
        font-size: 16px;
        padding: 12px;
      }

      .btn-primary {
        font-size: 16px;
        padding: 12px;
      }
    }

    @media (max-width: 480px) {
      .avatar {
        width: 40px;
        height: 40px;
      }

      h4.text-center {
        width: 100%;
        font-size: 15px;
        padding: 5px;
      }

      .table th,
      .table td {
        font-size: 12px;
        padding: 3px;
      }

      .form-select {
        font-size: 0.9rem;
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
      .avatar {
        width: 40px;
        height: 40px;
      }

      h4.text-center {
        font-size: 14px;
        padding: 4px;
      }

      .table th,
      .table td {
        font-size: 11px;
        padding: 2px;
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
            <small><?= $teacherName ?></small>
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

        <a href="#collapseGrades" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseGrades">
          <i class="bi bi-clipboard-data btn-icon"></i>Grades
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse show" id="collapseGrades">
          <a href="gradesmanage.php" class="btn btn-outline-light sub-btn active">
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

      <div class="col-md-9 col-12 p-4">
        <div class="main-section">
          <h4 class="text-center">Grades Management</h4>

          <?php if ($successMessage): ?>
            <div class="alert alert-success text-center"><?= $successMessage ?></div>
          <?php endif; ?>
          <?php if ($errorMessage): ?>
            <div class="alert alert-danger text-center"><?= $errorMessage ?></div>
          <?php endif; ?>

          <form method="GET" class="mb-4 d-flex flex-column flex-sm-row gap-2">
            <select name="subject" class="form-select" onchange="this.form.submit()">
              <option value="">Select Subject & Section</option>
              <?php while ($row = $subjects->fetch_assoc()): ?>
                <option value="<?= $row['subject_ID'] ?>" <?= $row['subject_ID'] == $selected_subject ? 'selected' : '' ?>>
                  [<?= $row['YearName'] ?>] <?= $row['SectionName'] ?> - <?= $row['SubjectName'] ?>
                </option>
              <?php endwhile; ?>
            </select>

            <select name="quarter" class="form-select" onchange="this.form.submit()">
              <?php for ($q = 1; $q <= 4; $q++): ?>
                <option value="<?= $q ?>" <?= $q == $selected_quarter ? 'selected' : '' ?>>Quarter <?= $q ?></option>
              <?php endfor; ?>
            </select>
          </form>

          <?php if (!empty($selected_subject) && $students->num_rows > 0): ?>
            <form method="POST">
              <input type="hidden" name="subject_ID" value="<?= $selected_subject ?>">
              <input type="hidden" name="quarter" value="<?= $selected_quarter ?>">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Student Name</th>
                      <th>Status</th>
                      <th>Grade</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = $students->fetch_assoc()): ?>
                      <tr>
                        <td><?= htmlspecialchars($row['LastName'] . ', ' . $row['FirstName']) ?></td>
                        <td><?= $row['IsActive'] ? 'Active' : 'Disabled' ?></td>
                        <td>
                          <?php if ($row['IsActive']): ?>
                            <input type="number"
                              name="grades[<?= $row['students_ID'] ?>]"
                              class="form-control text-center"
                              min="60" max="100" step="0.01"
                              value="<?= htmlspecialchars($row['grade'] ?? '') ?>">
                          <?php else: ?>
                            <input type="text" class="form-control text-center bg-light" value="Disabled" disabled>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
              <button type="submit" name="save_grades" class="btn btn-primary w-100 mt-2">Save Grades</button>
            </form>
          <?php elseif (!empty($selected_subject)): ?>
            <div class="alert alert-warning text-center">No students enrolled on this Section.</div>
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