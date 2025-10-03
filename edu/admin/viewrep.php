<?php
session_start();
require '../conn.php';

if (!isset($_SESSION['admin'])) {
  header("Location: adminlogin.php");
  exit();
}

$mainFilter = $_GET['mainFilter'] ?? '';
$subFilter = $_GET['subFilter'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f5dc;
      
    
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
      min-height: 800px;
    }

    .btn-print {
      background-color: #1b5e20;
      color: white;
      border-radius: 25px;
      padding: 8px 20px;
      font-weight: bold;
      margin-top: 15px;
    }

    .btn-print:hover {
      background-color: #a8aa10ff;
    }

    .print-header {
      display: none;
    }

    @media print {

      .header,
      .sidebar,
      .filter-section,
      .btn-print {
        display: none !important;
      }

      body {
        background: white !important;
      }

      .form-section {
        background: white !important;
        border: none !important;
        padding: 0;
      }

      .print-header {
        display: block !important;
        margin-bottom: 20px;
        width: 100%;
      }
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
          <a href="admindash.php" style="text-decoration:none;">
            <img src="lnhslogo.png" class="avatar me-2" alt="Admin">
          </a>
          <div>
            <div style="font-size:25px;">Administrator</div>
            <small><?= $_SESSION['admin_name'] ?? '' ?></small>
          </div>
        </div>
         <a href="addstud.php" class="btn btn-outline-light active">Add Student</a>
        <a href="manageadmin.php" class="btn btn-outline-light">Manage Informations</a>
        <a href="docreqs.php" class="btn btn-outline-light">Document Requests</a>
        <a href="removeenrollee.php" class="btn btn-outline-light">Student Status</a>
        <a href="persoinfo.php" class="btn btn-outline-light">Personal Information</a>
        <a href="viewrep.php" class="btn btn-outline-light">View Reports</a>
        <a href="passmanage.php" class="btn btn-outline-light">Password Management</a>
        <a href="regteach.php" class="btn btn-outline-light">Register Teachers</a>
        <a href="assignteacher.php" class="btn btn-outline-light">Assign Teacher</a>
        <a href="addsubject.php" class="btn btn-outline-light">Add Subject</a>
        <a href="managesections.php" class="btn btn-outline-light ">Manage Sections</a>
        <br><br>
       
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Logout?')">Logout</a>
      </div>

      <div class="col-md-9 p-4">
        <div class="form-section">

          <div class="print-header" style="background-color:#1b5e20;color:white;padding:15px;border-radius:5px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <img src="../lnhs.png" alt="Left Logo" style="height:80px;">
              <h2 style="flex-grow:1; text-align:center; margin:0;">Department of Education</h2>
              <img src="../deped.png" alt="Right Logo" style="height:80px;">
            </div>
            <div style="text-align:center; margin-top:5px;">
              <strong><i>Region ll</i></strong><br>
              <strong><i>Lal-lo National High School</i></strong>
            </div>
          </div>

          <h4 class="mb-4 text-center">Enrollment Reports</h4>

          <div class="filter-section text-center mb-3">
            <form method="GET" action="">
              <select name="mainFilter" class="form-select mb-2" onchange="this.form.submit()">
                <option value="">-- Select Report --</option>
                <option value="total" <?= $mainFilter == 'total' ? 'selected' : '' ?>>Total Enrollees</option>
              </select>

              <?php if ($mainFilter == 'total'): ?>
                <select name="subFilter" class="form-select" onchange="this.form.submit()">
                  <option value="">-- Select Sub Filter --</option>
                  <option value="all" <?= $subFilter == 'all' ? 'selected' : '' ?>>All Enrollees of LNHS</option>
                  <option value="year" <?= $subFilter == 'year' ? 'selected' : '' ?>>By Year Level</option>
                  <option value="section" <?= $subFilter == 'section' ? 'selected' : '' ?>>By Section</option>
                  <option value="sex" <?= $subFilter == 'sex' ? 'selected' : '' ?>>By Sex</option>
                  <option value="status" <?= $subFilter == 'status' ? 'selected' : '' ?>>By Status</option>
                </select>
              <?php endif; ?>
            </form>
          </div>

          <div class="report-display mt-4">
            <?php
            if ($mainFilter == 'total') {
              if ($subFilter == 'year') {
                $result = $conn->query("SELECT YearLevelID, COUNT(*) AS total FROM students WHERE IsActive=1 GROUP BY YearLevelID ORDER BY YearLevelID ASC");
                echo '<table class="table table-bordered"><thead><tr><th>Year Level</th><th>Total</th></tr></thead><tbody>';
                while ($row = $result->fetch_assoc()) {
                  $yearName = $conn->query("SELECT YearName FROM yearlevels WHERE yearlevel_ID=" . $row['YearLevelID'])->fetch_assoc()['YearName'];
                  echo "<tr><td>$yearName</td><td>{$row['total']}</td></tr>";
                }
                echo '</tbody></table>';
              } elseif ($subFilter == 'section') {
                $result = $conn->query("SELECT SectionID, COUNT(*) AS total FROM students WHERE IsActive=1 GROUP BY SectionID");
                echo '<table class="table table-bordered"><thead><tr><th>Section</th><th>Total</th></tr></thead><tbody>';
                while ($row = $result->fetch_assoc()) {
                  $section = $conn->query("SELECT SectionName, yearlevel_ID FROM sections WHERE section_ID=" . $row['SectionID'])->fetch_assoc();
                  $yearName = $conn->query("SELECT YearName FROM yearlevels WHERE yearlevel_ID=" . $section['yearlevel_ID'])->fetch_assoc()['YearName'];
                  echo "<tr><td>$yearName - {$section['SectionName']}</td><td>{$row['total']}</td></tr>";
                }
                echo '</tbody></table>';
              } elseif ($subFilter == 'sex') {
                $result = $conn->query("SELECT Sex, COUNT(*) AS total FROM students WHERE IsActive=1 GROUP BY Sex");
                echo '<table class="table table-bordered"><thead><tr><th>Sex</th><th>Total</th></tr></thead><tbody>';
                while ($row = $result->fetch_assoc()) {
                  echo "<tr><td>{$row['Sex']}</td><td>{$row['total']}</td></tr>";
                }
                echo '</tbody></table>';
              } elseif ($subFilter == 'status') {
                $statuses = ["4PS", "1PS", "SNED", "Repeater", "Balik-Aral", "Transferred-In", "Muslim"];
                echo '<table class="table table-bordered"><thead><tr><th>Status</th><th>Total</th></tr></thead><tbody>';
                foreach ($statuses as $st) {
                  $count = $conn->query("SELECT COUNT(*) AS total FROM students WHERE IsActive=1 AND Status LIKE '%$st%'")->fetch_assoc()['total'];
                  echo "<tr><td>$st</td><td>$count</td></tr>";
                }
                echo '</tbody></table>';
              } else {
                $total = $conn->query("SELECT COUNT(*) AS total FROM students WHERE IsActive=1")->fetch_assoc()['total'];
                echo "<h2 class='text-center text-success'>Total Enrollees: $total</h2>";
              }
            }
            ?>
          </div>

          <div class="text-center">
            <button class="btn btn-print" onclick="window.print()">Print Report</button>
          </div>

        </div>
      </div>
    </div>
  </div>

</body>

</html>