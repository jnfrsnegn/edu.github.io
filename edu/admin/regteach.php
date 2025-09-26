<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: adminlogin.php");
  exit();
}

$teacherIdError = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $FirstName = $_POST['FirstName'];
  $MiddleName = $_POST['MiddleName'];
  $LastName = $_POST['LastName'];
  $Suffix = $_POST['Suffix'];
  $Sex = $_POST['Sex'];
  $Birthdate = $_POST['Birthdate'];
  $EmployeeID = $_POST['EmployeeID'];
  $ContactNumber = $_POST['ContactNumber'];
  $Address = $_POST['Address'];
  $Position = $_POST['Position'];

  $checkQuery = "SELECT * FROM teachers WHERE EmployeeID = '$EmployeeID'";
  $checkResult = mysqli_query($conn, $checkQuery);

  if (mysqli_num_rows($checkResult) > 0) {
    $teacherIdError = "This Employee ID is already registered.";
  } else {
    $query = "INSERT INTO teachers 
            (FirstName, MiddleName, LastName, Suffix, Sex, Birthdate, EmployeeID, ContactNumber, Address, Position)
            VALUES 
            ('$FirstName', '$MiddleName', '$LastName', '$Suffix', '$Sex', '$Birthdate', '$EmployeeID', '$ContactNumber', '$Address', '$Position')";

    if (mysqli_query($conn, $query)) {
      $_SESSION['successMessage'] = "Teacher added successfully!";
      header("Location: regteach.php");
      exit;
    } else {
      $teacherIdError = "Database error: " . mysqli_error($conn);
    }
  }
}

if (isset($_SESSION['successMessage'])) {
  $successMessage = $_SESSION['successMessage'];
  unset($_SESSION['successMessage']);
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

    .register-btn {
      background-color: #124820;
      color: white;
      border-radius: 25px;
      padding: 10px 30px;
      font-weight: bold;
      width: 100%;
      max-width: 400px;
    }

    .register-btn:hover {
      background-color: #a8aa10;
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
    <div class="row flex-column flex-md-row">
      <div class="col-12 col-md-3 sidebar">
        <div class="mb-4 d-flex align-items-center">
          <a href="admindash.php" style="text-decoration: none;"><img src="lnhslogo.png" alt="Admin" class="avatar me-2"></a>
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
                <a href="regteach.php" class="btn btn-outline-light active">Register Teachers</a>
                <a href="assignteacher.php" class="btn btn-outline-light">Assign Teacher</a>
                <a href="regpar.php" class="btn btn-outline-light">Register Parents</a>
                <a href="addsubject.php" class="btn btn-outline-light">Add Subject</a>
                <a href="managesections.php" class="btn btn-outline-light ">Manage Sections</a>
                <br><br>
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
      </div>

      <div class="col-12 col-md-9 p-4">
        <div class="form-section">
          <?php if (!empty($successMessage)): ?>
            <script>
              alert("<?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>");
            </script>
          <?php endif; ?>

          <form method="post" action="regteach.php" autocomplete="off">
            <div class="row">
              <div class="col-md-4"><input type="text" name="FirstName" class="form-control" placeholder="First Name" required></div>
              <div class="col-md-4"><input type="text" name="MiddleName" class="form-control" placeholder="Middle Name"></div>
              <div class="col-md-4"><input type="text" name="LastName" class="form-control" placeholder="Last Name" required></div>
              <div class="col-md-4"><input type="text" name="Suffix" class="form-control" placeholder="Suffix"></div>
              <div class="col-md-4">
                <select name="Sex" class="form-control" required>
                  <option value="" disabled selected>Select Sex</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
              <div class="col-md-4"><input type="date" name="Birthdate" class="form-control" required></div>
              <div class="col-md-4">
                <input type="text" name="EmployeeID" maxlength="12" pattern="\d{4,12}" inputmode="numeric"
                  class="form-control <?= $teacherIdError ? 'is-invalid' : '' ?>" placeholder="Employee ID" required>
                <?php if ($teacherIdError): ?><div class="invalid-feedback"><?= $teacherIdError ?></div><?php endif; ?>
              </div>
              <div class="col-md-4"><input type="text" name="ContactNumber" maxlength="11" pattern="\d{11}" inputmode="numeric" class="form-control" placeholder="Contact Number" required></div>
              <div class="col-md-4"><input type="text" name="Address" class="form-control" placeholder="Address" required></div>
              <div class="col-md-4">
                <select name="Position" class="form-control" required>
                  <option value="" disabled selected>Select Position</option>
                  <option value="SP1">SP1</option>
                  <option value="SP2">SP2</option>
                  <option value="SP3">SP3</option>
                  <option value="MT1">MT1</option>
                  <option value="MT2">MT2</option>
                  <option value="MT3">MT3</option>
                  <option value="MT4">MT4</option>
                </select>
              </div>
              <div class="col-md-12 text-center">
                <button type="submit" class="btn register-btn mt-2">REGISTER</button>
              </div>
            </div>
          </form>

          <div class="table-responsive mt-4" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-bordered table-striped text-center">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>First Name</th>
                  <th>Middle Name</th>
                  <th>Last Name</th>
                  <th>Employee ID</th>
                  <th>Position</th>
                  <th>Sex</th>
                  <th>Contact</th>
                  <th>Address</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT * FROM teachers";
                $result = mysqli_query($conn, $sql);
                if ($result->num_rows > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                          <td>{$row['teachers_ID']}</td>
                          <td>{$row['FirstName']}</td>
                          <td>{$row['MiddleName']}</td>
                          <td>{$row['LastName']}</td>
                          <td>{$row['EmployeeID']}</td>
                          <td>{$row['Position']}</td>
                          <td>{$row['Sex']}</td>
                          <td>{$row['ContactNumber']}</td>
                          <td>{$row['Address']}</td>
                        </tr>";
                  }
                } else {
                  echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>