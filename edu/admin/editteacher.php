<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: adminlogin.php");
  exit();
}
$teacherID = $_GET['tid'] ?? null;
$teacher = null;
$error = '';

if (!$teacherID) {
  header("Location: teachinfo.php");
  exit;
}

$query = "SELECT * FROM teachers WHERE teachers_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacherID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
  $teacher = $result->fetch_assoc();
} else {
  $error = "Teacher not found.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $FirstName = $_POST['FirstName'];
  $MiddleName = $_POST['MiddleName'];
  $LastName = $_POST['LastName'];
  $Suffix = $_POST['Suffix'];
  $Sex = $_POST['Sex'];
  $Birthdate = $_POST['Birthdate'];
  $EmployeeID = $_POST['EmployeeID'];
  $Position = $_POST['Position'];
  $ContactNumber = $_POST['ContactNumber'];
  $Address = $_POST['Address'];

  $update = "UPDATE teachers SET 
        FirstName = ?, MiddleName = ?, LastName = ?, Suffix = ?, 
        Sex = ?, Birthdate = ?, EmployeeID = ?, Position = ?, 
        ContactNumber = ?, Address = ? 
        WHERE teachers_ID = ?";
  $stmt = $conn->prepare($update);
  $stmt->bind_param(
    "ssssssssssi",
    $FirstName,
    $MiddleName,
    $LastName,
    $Suffix,
    $Sex,
    $Birthdate,
    $EmployeeID,
    $Position,
    $ContactNumber,
    $Address,
    $teacherID
  );

  if ($stmt->execute()) {
    header("Location: teachinfo.php");
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

    .btn-outline-light {
      font-family: Arial, Helvetica, sans-serif;
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
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Log out?');">Logout</a>
      </div>

      <div class="col-md-9 p-4">
        <?php if ($error): ?>
          <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php elseif ($teacher): ?>
          <div class="form-section">
            <h4 class="text-center mb-4">Edit Teacher Details</h4>

            <form method="POST">
              <div class="row">
                <div class="col-md-4"><input type="text" name="FirstName" class="form-control" placeholder="First Name" required value="<?= htmlspecialchars($teacher['FirstName']) ?>"></div>
                <div class="col-md-4"><input type="text" name="MiddleName" class="form-control" placeholder="Middle Name" value="<?= htmlspecialchars($teacher['MiddleName']) ?>"></div>
                <div class="col-md-4"><input type="text" name="LastName" class="form-control" placeholder="Last Name" required value="<?= htmlspecialchars($teacher['LastName']) ?>"></div>
                <div class="col-md-4"><input type="text" name="Suffix" class="form-control" placeholder="Suffix" value="<?= htmlspecialchars($teacher['Suffix']) ?>"></div>
                <div class="col-md-4">
                  <select name="Sex" class="form-control" required>
                    <option value="Male" <?= $teacher['Sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $teacher['Sex'] == 'Female' ? 'selected' : '' ?>>Female</option>
                  </select>
                </div>
                <div class="col-md-4"><input type="date" name="Birthdate" class="form-control" required value="<?= htmlspecialchars($teacher['Birthdate']) ?>"></div>
                <div class="col-md-4"><input type="text" name="EmployeeID" class="form-control" required value="<?= htmlspecialchars($teacher['EmployeeID']) ?>" placeholder="Employee ID"></div>

     
                <div class="col-md-4">
                  <select name="Position" class="form-control" required>
                    <option value="" disabled>Select Position</option>
                    <option value="SP1" <?= $teacher['Position'] == 'SP1' ? 'selected' : '' ?>>SP1</option>
                    <option value="SP2" <?= $teacher['Position'] == 'SP2' ? 'selected' : '' ?>>SP2</option>
                    <option value="SP3" <?= $teacher['Position'] == 'SP3' ? 'selected' : '' ?>>SP3</option>
                    <option value="MT1" <?= $teacher['Position'] == 'MT1' ? 'selected' : '' ?>>MT1</option>
                    <option value="MT2" <?= $teacher['Position'] == 'MT2' ? 'selected' : '' ?>>MT2</option>
                    <option value="MT3" <?= $teacher['Position'] == 'MT3' ? 'selected' : '' ?>>MT3</option>
                    <option value="MT4" <?= $teacher['Position'] == 'MT4' ? 'selected' : '' ?>>MT4</option>
                  </select>
                </div>

                <div class="col-md-4"><input type="text" name="ContactNumber" class="form-control" maxlength="11" pattern="\d{11}" required value="<?= htmlspecialchars($teacher['ContactNumber']) ?>" placeholder="Contact Number"></div>
                <div class="col-md-4"><input type="text" name="Address" class="form-control" placeholder="Address" required value="<?= htmlspecialchars($teacher['Address']) ?>"></div>
                <div class="col-12 text-center mt-4">
                  <button type="submit" class="btn edit-btn">SAVE CHANGES</button>
                  <a href="teachinfo.php" class="btn btn-secondary ms-2">CANCEL</a>
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
      const confirmSave = confirm('Are you sure you want to save changes?');
      if (!confirmSave) {
        e.preventDefault();
      }
    });
  </script>

</body>

</html>