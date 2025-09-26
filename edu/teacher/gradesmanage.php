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

// Handle saving grades
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_grades'])) {
  $quarter = (int)$_POST['quarter'];

  foreach ($_POST['grades'] as $student_id => $grade) {
    $grade = round(floatval($grade), 2); // keep only 2 decimals

    // ❌ Validate grade
    if ($grade < 60 || $grade > 100) {
      $errorMessage = "Grades must be between 60.00 and 100.00 (Student ID: $student_id).";
      break;
    }

    // ✅ Check if student is active
    $checkActive = $conn->query("SELECT IsActive FROM students WHERE students_ID = '$student_id'");
    if ($checkActive->num_rows > 0) {
      $rowActive = $checkActive->fetch_assoc();
      if ($rowActive['IsActive'] == 0) {
        $errorMessage = "You cannot give grades to a disabled student (ID: $student_id).";
        break;
      }
    }

    // ✅ Check if previous quarter exists (except for Q1)
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

    // ✅ Insert or update grade if active
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

// Fetch subjects assigned to teacher
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>SIMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f5f5dc;
      font-family: 'Segoe UI', sans-serif;
      overflow: hidden;
    }

    .header {
      background-color: #1b5e20;
      color: white;
      padding: 15px;
      text-align: center;
      font-size: 24px;
      font-weight: bold;
    }

    .sidebar {
      background-color: #0d4b16;
      min-height: 100vh;
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

    .avatar {
      width: 70px;
      height: 70px;
      border-radius: 50%;
    }

    h4.text-center {
      background-color: #0d4b16;
      border-radius: 25px;
      padding: 10px;
      width: 60%;
      color: #fff;
      margin: 0 auto 20px auto;
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
          <img src="lnhslogo.png" alt="Teacher" class="avatar me-2">
          <div>
            <div style="font-size:25px;">Teacher</div>
            <small><?= $_SESSION['teacher_name'] ?? '' ?></small>
          </div>
        </div>
        <a href="addstudteacher.php" class="btn btn-outline-light">Student Registration</a>
        <a href="manageteach.php" class="btn btn-outline-light">Manage Informations</a>
        <a href="gradesmanage.php" class="btn btn-outline-light active">Grades Management</a>
        <a href="persoinfoteach.php" class="btn btn-outline-light">Personal Information</a>
        <a href="passteach.php" class="btn btn-outline-light">Password Management</a>
        <a href="regparteach.php" class="btn btn-outline-light">Register Parents</a>
        <br><br>
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
      </div>

      <div class="col-md-9 p-4">
        <div class="main-section">
          <h4 class="text-center">Grades Management</h4>

          <?php if ($successMessage): ?>
            <div class="alert alert-success text-center"><?= $successMessage ?></div>
          <?php endif; ?>
          <?php if ($errorMessage): ?>
            <div class="alert alert-danger text-center"><?= $errorMessage ?></div>
          <?php endif; ?>

          <form method="GET" class="mb-4 d-flex gap-2">
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

</body>

</html>