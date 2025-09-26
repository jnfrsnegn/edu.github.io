<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: adminlogin.php");
  exit();
}

$selectedYearLevelID = $_POST['YearLevelID'] ?? '';
$selectedSectionID = $_POST['SectionID'] ?? '';

// ✅ Toggle Enable/Disable
if (isset($_GET['toggle'])) {
  $studentID = $_GET['toggle'];

  $check = $conn->prepare("SELECT IsActive FROM students WHERE students_ID = ?");
  $check->bind_param("i", $studentID);
  $check->execute();
  $resultCheck = $check->get_result();
  $row = $resultCheck->fetch_assoc();

  $newStatus = ($row['IsActive'] == 1) ? 0 : 1;

  $stmt = $conn->prepare("UPDATE students SET IsActive = ? WHERE students_ID = ?");
  $stmt->bind_param("ii", $newStatus, $studentID);
  $stmt->execute();

  $statusText = $newStatus == 1 ? "enabled" : "disabled";
  echo "<script>alert('Student has been $statusText.'); window.location='removeenrollee.php';</script>";
  exit();
}

// ✅ Fetch students
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selectedYearLevelID && $selectedSectionID) {
  $stmt = $conn->prepare("SELECT s.students_ID, s.LRN, s.FirstName, s.MiddleName, s.LastName, 
                                   y.YearName, sec.SectionName, s.IsActive
                            FROM students s
                            LEFT JOIN yearlevels y ON s.YearLevelID = y.yearlevel_ID
                            LEFT JOIN sections sec ON s.SectionID = sec.section_ID
                            WHERE s.YearLevelID = ? AND s.SectionID = ?
                            ORDER BY y.YearName ASC, sec.SectionName ASC");
  $stmt->bind_param("ii", $selectedYearLevelID, $selectedSectionID);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $sql = "SELECT s.students_ID, s.LRN, s.FirstName, s.MiddleName, s.LastName, 
                   y.YearName, sec.SectionName, s.IsActive
            FROM students s
            LEFT JOIN yearlevels y ON s.YearLevelID = y.yearlevel_ID
            LEFT JOIN sections sec ON s.SectionID = sec.section_ID
            ORDER BY y.YearName ASC, sec.SectionName ASC";
  $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
      height: auto;
    }

    .form-control {
      border-radius: 20px;
      margin-bottom: 10px;
    }

    .search-btn {
      background-color: #124820;
      color: white;
      border-radius: 25px;
      padding: 10px 30px;
      font-weight: bold;
      width: 300px;
    }

    .search-btn:hover {
      background-color: #a8aa10ff;
    }

    table {
      margin-top: 20px;
      background-color: white;
    }

    th {
      background-color: #1b5e20;
      color: white;
    }

    td,
    th {
      padding: 8px;
      text-align: center;
    }

    .btn-toggle {
      border: none;
      padding: 5px 15px;
      border-radius: 10px;
      text-decoration: none;
    }

    .btn-disable {
      background-color: #dc3545;
      color: white;
    }

    .btn-disable:hover {
      background-color: #ce3241ff;
    }

    .btn-enable {
      background-color: #28a745;
      color: white;
    }

    .btn-enable:hover {
      background-color: #218838;
    }

    h4.text-center {
      background-color: #0d4b16;
      border-radius: 25px;
      padding: 10px;
      width: 50%;
      color: #ffff;
      margin: 0 auto;
    }
  </style>
</head>

<body>

  <div class="header">Student Information Management System</div>

  <div class="container-fluid">
    <div class="row flex-column flex-md-row">
      <div class="col-12 col-md-3 sidebar">
        <div class="mb-4 d-flex align-items-center">
          <a href="admindash.php" style="text-decoration: none;"><img src="lnhslogo.png" alt="Admin" class="avatar me-2"></a>
          <div>
            <div style="font-size:25px;">Administrator</div>
            <small><?= $_SESSION['admin_name'] ?? '' ?></small>
          </div>
        </div>

        <a href="addstud.php" class="btn btn-outline-light">Student Registration</a>
        <a href="manageadmin.php" class="btn btn-outline-light">Manage Informations</a>
        <a href="docreqs.php" class="btn btn-outline-light">Document Requests</a>
        <a href="removeenrollee.php" class="btn btn-outline-light active">Remove Enrollee</a>
        <a href="persoinfo.php" class="btn btn-outline-light">Personal Information</a>
        <a href="viewrep.php" class="btn btn-outline-light">View Reports</a>
        <a href="passmanage.php" class="btn btn-outline-light">Password Management</a>
        <a href="regteach.php" class="btn btn-outline-light">Register Teachers</a>
        <a href="assignteacher.php" class="btn btn-outline-light">Assign Teacher</a>
        <a href="regpar.php" class="btn btn-outline-light">Register Parents</a>
        <a href="addsubject.php" class="btn btn-outline-light">Add Subject</a>
        <a href="managesections.php" class="btn btn-outline-light ">Manage Sections</a>
        <br><br>
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirmLogout();">Logout</a>

        <script>
          function confirmLogout() {
            return confirm("Are you sure you want to log out?");
          }
        </script>
      </div>

      <div class="col-12 col-md-9 p-4">
        <div class="form-section">
          <h4 class="text-center mb-4">Remove Enrollee</h4>

          <form method="POST" class="d-flex flex-column align-items-center">
            <div class="row w-100 justify-content-center">
              <div class="col-md-5">
                <select name="YearLevelID" id="YearLevelID" class="form-control text-center" required>
                  <option value="" disabled <?= !$selectedYearLevelID ? 'selected' : '' ?>>Select Year Level</option>
                  <?php
                  $yearRes = mysqli_query($conn, "SELECT * FROM yearlevels");
                  while ($row = mysqli_fetch_assoc($yearRes)) {
                    $selected = ($selectedYearLevelID == $row['yearlevel_ID']) ? 'selected' : '';
                    echo "<option value='{$row['yearlevel_ID']}' $selected>{$row['YearName']}</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-5">
                <select name="SectionID" id="SectionID" class="form-control text-center" required>
                  <option value="" disabled <?= !$selectedSectionID ? 'selected' : '' ?>>Select Section</option>
                  <?php
                  if ($selectedYearLevelID) {
                    $sectionQuery = mysqli_query($conn, "SELECT * FROM sections WHERE yearlevel_ID = '$selectedYearLevelID'");
                    while ($sRow = mysqli_fetch_assoc($sectionQuery)) {
                      $isSelected = ($selectedSectionID == $sRow['section_ID']) ? 'selected' : '';
                      echo "<option value='{$sRow['section_ID']}' $isSelected>{$sRow['SectionName']}</option>";
                    }
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="mt-3">
              <button type="submit" class="btn search-btn">SEARCH</button>
            </div>
          </form>

          <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
              <table class="table table-bordered table-hover mt-3">
                <thead>
                  <tr>
                    <th>LRN</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Year Level</th>
                    <th>Section</th>
                    <th>IsActive</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($student = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($student['LRN']); ?></td>
                      <td><?= htmlspecialchars($student['FirstName']); ?></td>
                      <td><?= htmlspecialchars($student['MiddleName']); ?></td>
                      <td><?= htmlspecialchars($student['LastName']); ?></td>
                      <td><?= htmlspecialchars($student['YearName'] ?? 'N/A'); ?></td>
                      <td><?= htmlspecialchars($student['SectionName'] ?? 'N/A'); ?></td>
                      <td>
                        <span class="badge <?= $student['IsActive'] == 1 ? 'bg-success' : 'bg-danger' ?>">
                          <?= $student['IsActive'] == 1 ? 'Active' : 'Disabled' ?>
                        </span>
                      </td>
                      <td>
                        <a href="removeenrollee.php?toggle=<?= $student['students_ID']; ?>"
                          class="btn-toggle <?= $student['IsActive'] == 1 ? 'btn-disable' : 'btn-enable' ?>"
                          onclick="return confirm('Are you sure you want to <?= $student['IsActive'] == 1 ? 'disable' : 'enable' ?> this student?');">
                          <?= $student['IsActive'] == 1 ? 'DISABLE' : 'ENABLE' ?>
                        </a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="alert alert-info mt-4">No enrolled students found.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('YearLevelID').addEventListener('change', function() {
      const yearLevel = this.value;
      fetch('getsections.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'yearlevel=' + yearLevel
        })
        .then(response => response.text())
        .then(data => {
          document.getElementById('SectionID').innerHTML = data;
        });
    });
  </script>

</body>

</html>