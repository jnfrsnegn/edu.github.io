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

$statusOptions = ['4Ps', 'IPs', 'SNED', 'Repeater', 'Balik-Aral', 'Transferred-In', 'Muslim'];
$studentStatus = $student['Status'] ? explode(',', $student['Status']) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $FirstName = ucwords($_POST['FirstName']);
  $MiddleName = ucwords($_POST['MiddleName']);
  $LastName = ucwords($_POST['LastName']);
  $Suffix = $_POST['Suffix'];
  $Sex = $_POST['Sex'];
  $Birthdate = $_POST['Birthdate'];
  $LRN = $_POST['LRN'];
  $YearLevelID = $_POST['YearLevelID'];
  $SectionID = $_POST['SectionID'];
  $ContactNumber = $_POST['ContactNumber'];
  $EmailAddress = $_POST['EmailAddress'];
  $Address = ucwords($_POST['Address']);
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
  <title>SIMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
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
      text-align: center;
      padding: 15px;
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
      border-radius: 10px;
      padding: 25px;
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

    .btn-outline-light {
      font-family: Arial, Helvetica, sans-serif;
    }

    .btn-icon {
      margin-right: 8px;
      width: 20px}

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

   
      .form-row .col-md-4 {
        width: 100%;
        margin-bottom: 10px;
      }

      .form-control {
        font-size: 16px;
        padding: 12px 15px; 
      }

      .checkbox-group {
        flex-direction: column;
        gap: 8px;
      }

      .form-check {
        margin-bottom: 5px;
      }

      .edit-btn {
        width: 100%;
        padding: 12px 20px;
        font-size: 16px;
        margin-bottom: 10px;
      }

      .btn-secondary {
        width: 100%;
        padding: 12px 20px;
        font-size: 16px;
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

      .form-control {
        font-size: 16px;
        margin-bottom: 12px;
        padding: 12px 12px;
      }

      .edit-btn,
      .btn-secondary {
        font-size: 16px;
        padding: 12px 15px;
      }

      .checkbox-group {
        gap: 6px;
      }

      .form-check-label {
        font-size: 14px;
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

      .form-control {
        font-size: 16px;
        padding: 10px 10px;
        margin-bottom: 10px;
      }

      .edit-btn,
      .btn-secondary {
        font-size: 15px;
        padding: 10px 12px;
      }

      .checkbox-group {
        gap: 5px;
      }

      .form-check-label {
        font-size: 13px;
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
            <div style="font-size:20px;">Administrator</div><small><?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?></small>
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

        <a href="#collapseInfo" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseInfo">
          <i class="bi bi-info-circle-fill btn-icon"></i>Manage Informations
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse show" id="collapseInfo">
          <a href="studinfo.php" class="btn btn-outline-light sub-btn active">
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

        <a href="viewrep.php" class="btn btn-outline-light">
          <i class="bi bi-bar-chart-fill btn-icon"></i>View Reports
        </a>

        <br><br>
        <a href="#" class="logout text-decoration-none" id="logoutBtn">
    <i class="bi bi-box-arrow-right me-2"></i>Logout
</a>
      </div>

      <div class="col-md-9 p-3">
        <?php if ($error): ?>
          <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($student): ?>
          <div class="form-section">
            <h4 class="text-center mb-4">Edit Student Details</h4>
            <form method="POST">
              <div class="row form-row">
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-section form');
    form.addEventListener('submit', function(e) {
      e.preventDefault(); 

      Swal.fire({
        title: 'Save Changes?',
        text: "Are you sure you want to update this student's information?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#124820',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save changes.',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit(); 
        }
      });
    });
  });
</script>

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