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

    .form-section {
      background-color: #fffde7;
      padding: 30px;
      border-radius: 10px;
      min-height: 400px;
    }

    th {
      background-color: #1b5e20;
      color: white;
      text-align: center;
    }

    .btn-outline-light {
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
            <small><?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']) ?></small>
          </div>
        </div>
        <a href="viewgrades.php" class="btn btn-outline-light">View Grades</a>
        <a href="persoinfo.php" class="btn btn-outline-light">Personal Information</a>
        <a href="reqdocs.php" class="btn btn-outline-light">Request Form</a>
        <a href="parentreq.php" class="btn btn-outline-light">Parent Request</a>
        <a href="passmanage.php" class="btn btn-outline-light">Password Management</a>
        <br><br>
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
      </div>
      <div class="col-md-9 p-4">
        <div class="form-section">
          <h3 class="text-center mb-4">My Grades</h3>

          <?php if (!empty($gradesBySubject)): ?>
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
          <?php else: ?>
            <div class="alert alert-warning text-center">No grades recorded yet.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>

</html>