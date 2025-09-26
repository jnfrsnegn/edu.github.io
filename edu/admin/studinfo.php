<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: adminlogin.php");
  exit();
}

$results = [];
$selectedYearLevelID = '';
$selectedSectionID = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $selectedYearLevelID = $_POST['YearLevelID'] ?? '';
  $selectedSectionID = $_POST['SectionID'] ?? '';

  if ($selectedYearLevelID && $selectedSectionID) {
    $stmt = $conn->prepare("SELECT s.*, sec.SectionName, yl.YearName 
                            FROM students s 
                            LEFT JOIN sections sec ON s.SectionID = sec.section_ID 
                            LEFT JOIN yearlevels yl ON s.YearLevelID = yl.yearlevel_ID
                            WHERE s.YearLevelID = ? AND s.SectionID = ?
                            ORDER BY s.students_ID ASC");
    $stmt->bind_param("ii", $selectedYearLevelID, $selectedSectionID);
    $stmt->execute();
    $results = $stmt->get_result();
  }
} else {
  $defaultQuery = "SELECT s.*, sec.SectionName, yl.YearName 
                   FROM students s 
                   LEFT JOIN sections sec ON s.SectionID = sec.section_ID 
                   LEFT JOIN yearlevels yl ON s.YearLevelID = yl.yearlevel_ID
                   ORDER BY s.students_ID ASC";
  $results = mysqli_query($conn, $defaultQuery);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
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

    .form-section {
      background-color: #fffde7;
      padding: 30px;
      border-radius: 10px;
    }

    .form-control {
      border-radius: 20px;
      margin-bottom: 15px;
    }

    .search-btn {
      background-color: #124820;
      color: white;
      border-radius: 25px;
      padding: 10px 30px;
      font-weight: bold;
      width: 400px;
    }

    .search-btn:hover {
      background-color: #a8aa10ff;
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

    th {
      background-color: #1b5e20;
      color: white;
    }

    td,
    th {
      text-align: center;
      vertical-align: middle;
    }

    .table-responsive {
      max-height: 450px;
      overflow-y: auto;
    }

    .edit-btn {
      background-color: #124820;
      color: white;
      border-radius: 25px;
      padding: 6px 20px;
      font-weight: bold;
    }

    .edit-btn:hover {
      background-color: #a8aa10ff;
      color: black;
    }

    h4.text-center {
      background-color: #0d4b16;
      border-radius: 25px;
      padding: 10px;
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
          <a href="admindash.php" style="text-decoration: none;"><img src="lnhslogo.png" alt="Admin" class="avatar me-2"></a>
          <div>
            <div style="font-size:25px;">Administrator</div>
            <small><?= $_SESSION['admin_name'] ?? '' ?></small>
          </div>
        </div>

        <a href="addstud.php" class="btn btn-outline-light">Student Registration</a>
        <a href="manageadmin.php" class="btn btn-outline-light">Manage Informations</a>
        <a href="docreqs.php" class="btn btn-outline-light">Document Requests</a>
        <a href="removeenrollee.php" class="btn btn-outline-light">Remove Enrollee</a>
        <a href="persoinfo.php" class="btn btn-outline-light">Personal Information</a>
        <a href="viewrep.php" class="btn btn-outline-light">View Reports</a>
        <a href="passmanage.php" class="btn btn-outline-light">Password Management</a>
        <a href="regteach.php" class="btn btn-outline-light">Register Teachers</a>
        <a href="assignteacher.php" class="btn btn-outline-light">Assign Teacher</a>
        <a href="regpar.php" class="btn btn-outline-light">Register Parents</a>
        <a href="addsubject.php" class="btn btn-outline-light">Add Subject</a>
        <a href="managesections.php" class="btn btn-outline-light">Manage Sections</a>
        <br><br>
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
      </div>

      <div class="col-md-9 p-4">
        <div class="form-section">
          <h4 class="mb-4 text-center">Student Information</h4>

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

          <?php if ($results && $results->num_rows > 0): ?>
            <div class="table-responsive mt-5">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>FirstName</th>
                    <th>MiddleName</th>
                    <th>LastName</th>
                    <th>Suffix</th>
                    <th>Sex</th>
                    <th>Birthdate</th>
                    <th>LRN</th>
                    <th>YearLevel</th>
                    <th>Section</th>
                    <th>ContactNumber</th>
                    <th>EmailAddress</th>
                    <th>Address</th>
                    <th>IsActive</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $results->fetch_assoc()): ?>
                    <tr>
                      <td><?= $row['students_ID'] ?></td>
                      <td><?= $row['FirstName'] ?></td>
                      <td><?= $row['MiddleName'] ?></td>
                      <td><?= $row['LastName'] ?></td>
                      <td><?= $row['Suffix'] ?></td>
                      <td><?= $row['Sex'] ?></td>
                      <td><?= $row['Birthdate'] ?></td>
                      <td><?= $row['LRN'] ?></td>
                      <td><?= $row['YearName'] ?></td>
                      <td><?= $row['SectionName'] ?></td>
                      <td><?= $row['ContactNumber'] ?></td>
                      <td><?= $row['EmailAddress'] ?></td>
                      <td><?= $row['Address'] ?></td>
                      <td><?= $row['IsActive'] ? 'Active' : 'Disabled' ?></td>
                      <td>
                        <?php if ($row['IsActive']): ?>
                          <a href="editstud.php?sid=<?= $row['students_ID'] ?>" class="btn edit-btn btn-sm">Edit</a>
                        <?php else: ?>
                          <span class="text-muted">No Action</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="alert alert-danger text-center mt-4 col-md-6 mx-auto">
              No students found for the selected Yearlevel and Section.
            </div>
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