<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacherlogin.php");
    exit();
}

$teacherID = $_SESSION['teacher_id'];


$sql = "
    SELECT 
        t.teachers_ID,
        t.FirstName, t.MiddleName, t.LastName, t.Suffix,
        t.Sex, t.Birthdate, t.EmployeeID, t.ContactNumber, t.Address,
        GROUP_CONCAT(DISTINCT y.YearName ORDER BY y.YearName SEPARATOR ', ') AS YearLevels,
        GROUP_CONCAT(DISTINCT s.SectionName ORDER BY s.SectionName SEPARATOR ', ') AS Sections
    FROM teachers t
    LEFT JOIN teacher_subjects ts ON ts.teachers_ID = t.teachers_ID
    LEFT JOIN subjects sub        ON sub.subject_ID   = ts.subject_ID
    LEFT JOIN yearlevels y        ON y.yearlevel_ID   = sub.YearLevelID
    LEFT JOIN sections s          ON s.section_ID     = sub.SectionID
    WHERE t.teachers_ID = ?
    GROUP BY t.teachers_ID
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacherID);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
$stmt->close();

if (!$teacher) {
    echo "<script>alert('Teacher not found.'); window.location.href='teacherlogin.php';</script>";
    exit();
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
    .card-info {
      background: #ffffff;
      border: 1px solid #ccc;
      border-radius: 15px;
      padding: 30px;
      max-width: 700px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .info-label { font-weight: bold; color: #333; }
    .info-value { font-weight: 500; }
    .form-section {
      background-color: #fffde7;
      padding: 30px;
      border-radius: 10px;
    }
    h4.text-center {
      background-color: #0d4b16;
      border-radius: 25px;
      padding: 9px;
      width: 50%;
      color: #ffff;
      margin: 0 auto;
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
        <a href="persoinfoteach.php" class="btn btn-outline-light active">Personal Information</a>
        <a href="passteach.php" class="btn btn-outline-light">Password Management</a>
        <br><br>
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
      </div>

      <div class="col-md-9 p-4">
        <div class="form-section">
          <h4 class="mb-4 text-center">Personal Information</h4>
          <div class="card-info">
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Name:</div>
              <div class="col-sm-8 info-value">
                <?= htmlspecialchars(trim(($teacher['FirstName'] ?? '').' '.($teacher['MiddleName'] ?? '').' '.($teacher['LastName'] ?? '').' '.($teacher['Suffix'] ?? ''))) ?>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Sex:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['Sex'] ?? '') ?></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Birthdate:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['Birthdate'] ?? '') ?></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Employee ID:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['EmployeeID'] ?? '') ?></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Contact Number:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['ContactNumber'] ?? '') ?></div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Address:</div>
              <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['Address'] ?? '') ?></div>
            </div>

            <div class="row mb-3">
              <div class="col-sm-4 info-label">Year Level:</div>
              <div class="col-sm-8 info-value">
                <?= htmlspecialchars($teacher['YearLevels'] ?? 'Not Assigned') ?>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-sm-4 info-label">Section:</div>
              <div class="col-sm-8 info-value">
                <?= htmlspecialchars($teacher['Sections'] ?? 'Not Assigned') ?>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
</body>
</html>
