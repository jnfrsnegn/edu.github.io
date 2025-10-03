<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: adminlogin.php");
  exit();
}

$lrnError = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
  $Status = isset($_POST['Status']) ? implode(",", $_POST['Status']) : null;

  $checkQuery = "SELECT * FROM students WHERE LRN = '$LRN'";
  $checkResult = mysqli_query($conn, $checkQuery);

  if (mysqli_num_rows($checkResult) > 0) {
    $lrnError = "This LRN is already registered.";
  } else {
    $query = "INSERT INTO students 
            (FirstName, MiddleName, LastName, Suffix, Sex, Birthdate, LRN, YearLevelID, SectionID, ContactNumber, EmailAddress, Address, Status)
            VALUES 
            ('$FirstName', '$MiddleName', '$LastName', '$Suffix', '$Sex', '$Birthdate', '$LRN', '$YearLevelID', '$SectionID', '$ContactNumber','$EmailAddress', '$Address', '$Status')";

    if (mysqli_query($conn, $query)) {
      $successMessage = "Student added successfully!";
      $_POST = [];
    } else {
      $lrnError = "Database error: " . mysqli_error($conn);
    }
  }
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
      height: auto;
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
      padding: 8px;
      text-align: center;
    }

    table {
      margin-top: 20px;
    }

    .btn-outline-light {
      font-family: Arial, Helvetica, sans-serif;
    }

    .form-check-label {
      margin-left: 5px;
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
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
      </div>

      <div class="col-12 col-md-9 p-4">
        <div class="form-section">
          <?php if ($successMessage): ?>
            <script>
              alert("<?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>");
            </script>
          <?php endif; ?>

          <form method="post" action="addstud.php" autocomplete="off">
            <div class="row">
              <div class="col-12 col-md-4">
                <input type="text" name="FirstName" class="form-control" placeholder="First Name" required value="<?= htmlspecialchars($_POST['FirstName'] ?? '') ?>">
              </div>
              <div class="col-12 col-md-4">
                <input type="text" name="MiddleName" class="form-control" placeholder="Middle Name" value="<?= htmlspecialchars($_POST['MiddleName'] ?? '') ?>">
              </div>
              <div class="col-12 col-md-4">
                <input type="text" name="LastName" class="form-control" placeholder="Last Name" required value="<?= htmlspecialchars($_POST['LastName'] ?? '') ?>">
              </div>
              <div class="col-12 col-md-4">
                <input type="text" name="Suffix" class="form-control" placeholder="Suffix (e.g., Jr.)" value="<?= htmlspecialchars($_POST['Suffix'] ?? '') ?>">
              </div>
              <div class="col-12 col-md-4">
                <select name="Sex" class="form-control" required>
                  <option value="" disabled <?= !isset($_POST['Sex']) ? 'selected' : '' ?>>Select Sex</option>
                  <option value="Male" <?= (($_POST['Sex'] ?? '') == 'Male') ? 'selected' : '' ?>>Male</option>
                  <option value="Female" <?= (($_POST['Sex'] ?? '') == 'Female') ? 'selected' : '' ?>>Female</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <input type="date" name="Birthdate" class="form-control" required value="<?= htmlspecialchars($_POST['Birthdate'] ?? '') ?>">
              </div>
              <div class="col-12 col-md-4">
                <input type="text" name="LRN" maxlength="12" pattern="\d{12}" inputmode="numeric" class="form-control <?= $lrnError ? 'is-invalid' : '' ?>" placeholder="LRN (12 digit)" required value="<?= htmlspecialchars($_POST['LRN'] ?? '') ?>">
                <?php if ($lrnError): ?>
                  <div class="invalid-feedback"><?= $lrnError ?></div>
                <?php endif; ?>
              </div>
              <div class="col-12 col-md-4">
                <select name="YearLevelID" id="YearLevelID" class="form-control" required>
                  <option value="" disabled selected>Select Year Level</option>
                  <?php
                  $ylQuery = mysqli_query($conn, "SELECT * FROM yearlevels");
                  while ($row = mysqli_fetch_assoc($ylQuery)) {
                    $selected = ($_POST['YearLevelID'] ?? '') == $row['yearlevel_ID'] ? 'selected' : '';
                    echo "<option value='{$row['yearlevel_ID']}' $selected>{$row['YearName']}</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <select name="SectionID" id="SectionID" class="form-control" required>
                  <option value="" disabled selected>Select Section</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <input type="text" name="ContactNumber" maxlength="11" pattern="\d{11}" inputmode="numeric" class="form-control <?= $lrnError ? 'is-invalid' : '' ?>" placeholder="Contact Number" required value="<?= htmlspecialchars($_POST['ContactNumber'] ?? '') ?>">
              </div>
              <div class="col-12 col-md-4">
                <input type="email" name="EmailAddress" class="form-control <?= $lrnError ? 'is-invalid' : '' ?>" placeholder="name@gmail.com"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required value="<?= htmlspecialchars($_POST['EmailAddress'] ?? '') ?>">
              </div>
              <div class="col-12 col-md-4">
                <input type="text" name="Address" class="form-control" placeholder="Address" required value="<?= htmlspecialchars($_POST['Address'] ?? '') ?>">
              </div>


              <div class="col-12 mb-3">
                <label class="form-label fw-bold">Student Status:</label>
                <?php
                $statuses = ["4PS", "1PS", "SNED", "Repeater", "Balik-Aral", "Transferred-In", "Muslim"];
                foreach ($statuses as $status): ?>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="Status[]" value="<?= $status ?>" id="status<?= $status ?>" <?= (in_array($status, $_POST['Status'] ?? [])) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status<?= $status ?>"><?= $status ?></label>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="col-12 d-flex justify-content-center">
                <button type="submit" name="submit" class="btn register-btn mt-2">REGISTER</button>
              </div>
            </div>
          </form>

          <div class="table-responsive mt-4" style="max-height: 400px; overflow-y: auto;">
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
                  <th>Status</th>
                  <th>IsActive</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT s.*, sec.SectionName, yl.YearName
              FROM students s
              LEFT JOIN sections sec ON s.SectionID = sec.section_ID
              LEFT JOIN yearlevels yl ON s.YearLevelID = yl.yearlevel_ID
              ORDER BY s.students_ID ASC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                    $isActiveText = $row['IsActive'] == 1
                      ? "<span class='badge bg-success'>Active</span>"
                      : "<span class='badge bg-danger'>Disabled</span>";

                    echo "<tr>
                  <td>{$row['students_ID']}</td>
                  <td>{$row['FirstName']}</td>
                  <td>{$row['MiddleName']}</td>
                  <td>{$row['LastName']}</td>
                  <td>{$row['Suffix']}</td>
                  <td>{$row['Sex']}</td>
                  <td>{$row['Birthdate']}</td>
                  <td>{$row['LRN']}</td>
                  <td>{$row['YearName']}</td>
                  <td>{$row['SectionName']}</td>
                  <td>{$row['ContactNumber']}</td>
                  <td>{$row['EmailAddress']}</td>
                  <td>{$row['Address']}</td>
                  <td>{$row['Status']}</td>
                  <td>{$isActiveText}</td>
                </tr>";
                  }
                } else {
                  echo "<tr><td colspan='15'>No students registered yet.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>

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