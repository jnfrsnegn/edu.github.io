<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: adminlogin.php");
  exit();
}
$studentID = $_GET['sid'] ?? null;
$student = null;
$error = '';

if (!$studentID) {
  header("Location: studinfo.php");
  exit;
}

// Fetch student
$query = "SELECT * FROM students WHERE students_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
  $student = $result->fetch_assoc();
} else {
  $error = "Student not found.";
}

// Status options
$statusOptions = ['4Ps', '1Ps', 'SNED', 'Repeater', 'Balik-Aral', 'Transferred-In', 'Muslim'];
$studentStatus = $student['Status'] ? explode(',', $student['Status']) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $FirstName = $_POST['FirstName'];
  $MiddleName = $_POST['MiddleName'];
  $LastName = $_POST['LastName'];
  $Suffix = $_POST['Suffix'];
  $Sex = $_POST['Sex'];
  $Birthdate = $_POST['Birthdate'];
  $LRN = $_POST['LRN'];
  $YearLevelID = $_POST['YearLevelID'];
  $SectionID = $_POST['SectionID'];
  $ContactNumber = $_POST['ContactNumber'];
  $EmailAddress = $_POST['EmailAddress'];
  $Address = $_POST['Address'];
  $Status = isset($_POST['Status']) ? implode(',', $_POST['Status']) : '';

  $update = "UPDATE students SET 
        FirstName = ?, MiddleName = ?, LastName = ?, Suffix = ?, 
        Sex = ?, Birthdate = ?, LRN = ?, YearLevelID = ?, 
        SectionID = ?, ContactNumber = ?, EmailAddress = ?, Address = ?, Status = ? 
        WHERE students_ID = ?";

  $stmt = $conn->prepare($update);
  $stmt->bind_param(
    "ssssssssissssi",
    $FirstName,
    $MiddleName,
    $LastName,
    $Suffix,
    $Sex,
    $Birthdate,
    $LRN,
    $YearLevelID,
    $SectionID,
    $ContactNumber,
    $EmailAddress,
    $Address,
    $Status,
    $studentID
  );

  if ($stmt->execute()) {
    header("Location: studinfo.php");
    exit();
  } else {
    $error = "Update failed: " . $stmt->error;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>SIM</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f5dc;
      overflow: hidden;
    }

    .header {
      background-color: #1b5e20;
      color: white;
      text-align: center;
      padding: 15px;
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

    .form-section {
      background-color: #fffde7;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-control {
      border-radius: 20px;
      margin-bottom: 15px;
    }

    .edit-btn {
      background-color: #124820;
      color: white;
      border-radius: 25px;
      padding: 10px 30px;
      font-weight: bold;
    }

    .edit-btn:hover {
      background-color: #a8aa10ff;
      color: black;
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
      width: 50%;
      color: #ffff;
      margin: 0 auto;
    }

    .checkbox-group {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 15px;
    }
  </style>
</head>

<body>

  <div class="header">Student Information Management</div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3 sidebar">
        <div class="mb-4 d-flex align-items-center">
          <a href="admindash.php"><img src="lnhslogo.png" alt="Admin" class="avatar me-2"></a>
          <div>
            <div style="font-size:25px;">Administrator</div><small><?= $_SESSION['admin_name'] ?? '' ?></small>
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
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Log out?');">Logout</a>
      </div>

      <div class="col-md-9 p-4">
        <?php if ($error): ?>
          <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php elseif ($student): ?>
          <div class="form-section">
            <h4 class="text-center mb-4">Edit Student Details</h4>
            <form method="POST">
              <div class="row">
                <div class="col-md-4"><input type="text" name="FirstName" class="form-control" placeholder="First Name" required value="<?= htmlspecialchars($student['FirstName']) ?>"></div>
                <div class="col-md-4"><input type="text" name="MiddleName" class="form-control" placeholder="Middle Name" value="<?= htmlspecialchars($student['MiddleName']) ?>"></div>
                <div class="col-md-4"><input type="text" name="LastName" class="form-control" placeholder="Last Name" required value="<?= htmlspecialchars($student['LastName']) ?>"></div>
                <div class="col-md-4"><input type="text" name="Suffix" class="form-control" placeholder="Suffix" value="<?= htmlspecialchars($student['Suffix']) ?>"></div>
                <div class="col-md-4">
                  <select name="Sex" class="form-control" required>
                    <option value="Male" <?= $student['Sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $student['Sex'] == 'Female' ? 'selected' : '' ?>>Female</option>
                  </select>
                </div>
                <div class="col-md-4"><input type="date" name="Birthdate" class="form-control" readonly value="<?= htmlspecialchars($student['Birthdate']) ?>"></div>
                <div class="col-md-4"><input type="text" name="LRN" class="form-control" maxlength="12" pattern="\d{12}" inputmode="numeric" value="<?= htmlspecialchars($student['LRN']) ?>"></div>
                <div class="col-md-4"><input type="text" name="ContactNumber" class="form-control" maxlength="11" pattern="\d{11}" required value="<?= htmlspecialchars($student['ContactNumber']) ?>"></div>
                <div class="col-md-4"><input type="email" name="EmailAddress" class="form-control" required value="<?= htmlspecialchars($student['EmailAddress']) ?>"></div>
                <div class="col-md-4">
                  <select class="form-control" readonly>
                    <?php
                    $ylQuery = "SELECT YearName FROM yearlevels WHERE yearlevel_ID = ?";
                    $stmtYL = $conn->prepare($ylQuery);
                    $stmtYL->bind_param("i", $student['YearLevelID']);
                    $stmtYL->execute();
                    $ylResult = $stmtYL->get_result();
                    $yearName = $ylResult->fetch_assoc()['YearName'] ?? 'Not assigned';
                    echo "<option selected>$yearName</option>";
                    ?>
                  </select>
                  <input type="hidden" name="YearLevelID" value="<?= htmlspecialchars($student['YearLevelID']) ?>">
                </div>
                <div class="col-md-4">
                  <select class="form-control" readonly>
                    <?php
                    $secQuery = "SELECT SectionName FROM sections WHERE section_ID = ?";
                    $stmtSec = $conn->prepare($secQuery);
                    $stmtSec->bind_param("i", $student['SectionID']);
                    $stmtSec->execute();
                    $secResult = $stmtSec->get_result();
                    $sectionName = $secResult->fetch_assoc()['SectionName'] ?? 'Not assigned';
                    echo "<option selected>$sectionName</option>";
                    ?>
                  </select>
                  <input type="hidden" name="SectionID" value="<?= htmlspecialchars($student['SectionID']) ?>">
                </div>
                <div class="col-md-4"><input type="text" name="Address" class="form-control" placeholder="Address" required value="<?= htmlspecialchars($student['Address']) ?>"></div>

                <div class="col-12">
                  <label class="form-label">Status</label>
                  <div class="checkbox-group">
                    <?php foreach ($statusOptions as $status): ?>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="Status[]" value="<?= $status ?>" <?= in_array($status, $studentStatus) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= $status ?></label>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>

                <div class="col-12 text-center mt-4">
                  <button type="submit" class="btn edit-btn">SAVE CHANGES</button>
                  <a href="studinfo.php" class="btn btn-secondary ms-2">CANCEL</a>
                </div>
              </div>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    document.querySelector('form').addEventListener('submit', function(e) {
      if (!confirm('Are you sure you want to save changes?')) e.preventDefault();
    });
  </script>

</body>

</html>