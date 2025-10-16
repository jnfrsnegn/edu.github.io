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
      min-height: 800px;
    }

    .btn-print {
      background-color: #1b5e20;
      color: white;
      border-radius: 25px;
      padding: 10px 30px;
      font-weight: bold;
      margin-top: 15px;
    }

    .btn-print:hover {
      background-color: #a8aa10ff;
    }

    .print-header {
      display: none;
    }

    h4.text-center {
      background-color: #0d4b16;
      border-radius: 25px;
      padding: 10px;
      width: 50%;
      color: #ffff;
      margin: 0 auto 20px;
    }

    .filter-section {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .report-table {
      font-size: 0.95rem;
    }

    .report-table th {
      background-color: #1b5e20;
      color: white;
      text-align: center;
    }

    .total-display {
      font-size: 2.5rem;
      font-weight: bold;
      text-align: center;
      margin: 30px 0;
      color: #28a745;
    }

    .btn-outline-light {
      font-family: Arial, Helvetica, sans-serif;
    }

    .btn-icon {
      margin-right: 8px;
      width: 20px;
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

      .report-table {
        font-size: 1rem;
      }

      .total-display {
        font-size: 2rem;
      }
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
        min-height: auto;
      }

      .filter-section {
        padding: 10px;
      }

      .report-table {
        font-size: 0.9rem;
      }

      .total-display {
        font-size: 2rem;
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

      .filter-section {
        padding: 12px;
      }

      .filter-section .form-select {
        font-size: 16px;
        padding: 12px 15px;
        margin-bottom: 10px;
      }

      .btn-print {
        width: 100%;
        padding: 12px 20px;
        font-size: 16px;
      }

      .report-table {
        font-size: 0.85rem;
      }

      .report-table th,
      .report-table td {
        padding: 8px 4px;
        white-space: nowrap;
      }

      .total-display {
        font-size: 2rem;
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

      .filter-section {
        padding: 10px;
      }

      .filter-section .form-select {
        font-size: 16px;
        padding: 12px 12px;
        margin-bottom: 8px;
      }

      .btn-print {
        font-size: 16px;
        padding: 12px 15px;
      }

      .report-table {
        font-size: 0.8rem;
      }

      .report-table th,
      .report-table td {
        padding: 6px 2px;
        font-size: 12px;
      }

      .total-display {
        font-size: 1.5rem;
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

      .filter-section {
        padding: 8px;
      }

      .filter-section .form-select {
        font-size: 16px;
        padding: 10px 10px;
        margin-bottom: 6px;
      }

      .btn-print {
        font-size: 15px;
        padding: 10px 12px;
      }

      .report-table {
        font-size: 0.75rem;
      }

      .report-table th,
      .report-table td {
        padding: 4px 1px;
        font-size: 11px;
      }

      .total-display {
        font-size: 1.25rem;
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
          <a href="admindash.php" style="text-decoration:none;">
            <img src="lnhslogo.png" class="avatar me-2" alt="Admin">
          </a>
          <div>
            <div style="font-size:20px;">Administrator</div>
            <small><?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?></small>
          </div>
        </div>

        <a href="#collapseStudent" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseStudent">
          <i class="bi bi-people-fill btn-icon"></i>Student Management
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseStudent">
          <a href="addstud.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person-plus btn-icon"></i>Add Student
          </a>
          <a href="docreqs.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-file-earmark-text btn-icon"></i>Document Requests
          </a>
          <a href="removeenrollee.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person-x btn-icon"></i>Student Status
          </a>
        </div>

        <a href="#collapseInfo" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseInfo">
          <i class="bi bi-info-circle-fill btn-icon"></i>Manage Informations
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseInfo">
          <a href="studinfo.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-people btn-icon"></i>Student Information
          </a>
          <a href="teachinfo.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person-badge btn-icon"></i>Teacher Information
          </a>
          <a href="persoinfo.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person btn-icon"></i>Personal Information
          </a>
          <a href="passmanage.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-lock btn-icon"></i>Password Management
          </a>
        </div>

        <a href="#collapseTeacher" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseTeacher">
          <i class="bi bi-person-badge-fill btn-icon"></i>Teacher Management
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseTeacher">
          <a href="regteach.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person-plus btn-icon"></i>Register Teachers
          </a>
          <a href="assignteacher.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-arrow-right-circle btn-icon"></i>Assign Teacher
          </a>
        </div>

        <a href="#collapseAcademic" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseAcademic">
          <i class="bi bi-journal-bookmark-fill btn-icon"></i>Subjects & Sections
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseAcademic">
          <a href="addsubject.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-journal-plus btn-icon"></i>Add Subject
          </a>
          <a href="managesections.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-gear btn-icon"></i>Manage Sections
          </a>
        </div>

        <a href="viewrep.php" class="btn btn-outline-light ">
          <i class="bi bi-bar-chart-fill btn-icon"></i>View Reports
        </a>

        <br><br>
        <a href="#" class="logout text-decoration-none" id="logoutBtn">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
      </div>

      <div class="col-md-9 p-3">
        <div class="form-section">
          <h4 class="text-center mb-4">Enrollment Reports</h4>

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

          <div class="filter-section text-center mb-3">
            <form method="GET" action="">
              <div class="row justify-content-center">
                <div class="col-12 col-md-4 mb-2">
                  <select name="mainFilter" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Select Report --</option>
                    <option value="total" <?= $mainFilter == 'total' ? 'selected' : '' ?>>Total Enrollees</option>
                  </select>
                </div>
                <?php if ($mainFilter == 'total'): ?>
                  <div class="col-12 col-md-4 mb-2">
                    <select name="subFilter" class="form-select" onchange="this.form.submit()">
                      <option value="">-- Select Sub Filter --</option>
                      <option value="all" <?= $subFilter == 'all' ? 'selected' : '' ?>>All Enrollees of LNHS</option>
                      <option value="year" <?= $subFilter == 'year' ? 'selected' : '' ?>>By Year Level</option>
                      <option value="section" <?= $subFilter == 'section' ? 'selected' : '' ?>>By Section</option>
                      <option value="sex" <?= $subFilter == 'sex' ? 'selected' : '' ?>>By Sex</option>
                      <option value="status" <?= $subFilter == 'status' ? 'selected' : '' ?>>By Status</option>
                    </select>
                  </div>
                <?php endif; ?>
              </div>
            </form>
          </div>

          <div class="report-display mt-4">
            <?php
            if ($mainFilter == 'total') {
              if ($subFilter == 'all') {
                $total = $conn->query("SELECT COUNT(*) AS total FROM students WHERE IsActive=1")->fetch_assoc()['total'];
                echo "<div class='total-display'>Total Enrollees: " . htmlspecialchars($total) . "</div>";
              } elseif ($subFilter == 'year') {
                $result = $conn->query("SELECT YearLevelID, COUNT(*) AS total FROM students WHERE IsActive=1 GROUP BY YearLevelID ORDER BY YearLevelID ASC");
                echo '<div class="table-responsive"><table class="table table-bordered report-table"><thead><tr><th>Year Level</th><th>Total</th></tr></thead><tbody>';
                while ($row = $result->fetch_assoc()) {
                  $yearName = $conn->query("SELECT YearName FROM yearlevels WHERE yearlevel_ID=" . $row['YearLevelID'])->fetch_assoc()['YearName'];
                  echo "<tr><td>" . htmlspecialchars($yearName) . "</td><td>" . htmlspecialchars($row['total']) . "</td></tr>";
                }
                echo '</tbody></table></div>';
              } elseif ($subFilter == 'section') {
                $result = $conn->query("SELECT SectionID, COUNT(*) AS total FROM students WHERE IsActive=1 GROUP BY SectionID");
                echo '<div class="table-responsive"><table class="table table-bordered report-table"><thead><tr><th>Section</th><th>Total</th></tr></thead><tbody>';
                while ($row = $result->fetch_assoc()) {
                  $section = $conn->query("SELECT SectionName, yearlevel_ID FROM sections WHERE section_ID=" . $row['SectionID'])->fetch_assoc();
                  $yearName = $conn->query("SELECT YearName FROM yearlevels WHERE yearlevel_ID=" . $section['yearlevel_ID'])->fetch_assoc()['YearName'];
                  echo "<tr><td>" . htmlspecialchars($yearName . ' - ' . $section['SectionName']) . "</td><td>" . htmlspecialchars($row['total']) . "</td></tr>";
                }
                echo '</tbody></table></div>';
              } elseif ($subFilter == 'sex') {
                $result = $conn->query("SELECT Sex, COUNT(*) AS total FROM students WHERE IsActive=1 GROUP BY Sex");
                echo '<div class="table-responsive"><table class="table table-bordered report-table"><thead><tr><th>Sex</th><th>Total</th></tr></thead><tbody>';
                while ($row = $result->fetch_assoc()) {
                  echo "<tr><td>" . htmlspecialchars($row['Sex']) . "</td><td>" . htmlspecialchars($row['total']) . "</td></tr>";
                }
                echo '</tbody></table></div>';
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
        document.getElementById("logoutBtn").addEventListener("click", function(e) {
            e.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "You will be logged out of the system.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1b5e20",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, log out"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "logout.php";
                }
            });
        });
    </script>
</body>

</html>