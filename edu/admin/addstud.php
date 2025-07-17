<?php
require '../conn.php';

$lrnError = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $FirstName = $_POST['FirstName'];
    $MiddleName = $_POST['MiddleName'];
    $LastName = $_POST['LastName'];
    $Sex = $_POST['Sex'];
    $Birthdate = $_POST['Birthdate'];
    $LRN = $_POST['LRN'];
    $YearLevel = $_POST['YearLevel'];
    $Section = $_POST['Section'];
    $ContactNumber = $_POST['ContactNumber'];


    $checkQuery = "SELECT * FROM students WHERE LRN = '$LRN'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $lrnError = "This LRN is already registered.";
    } else {
        $query = "INSERT INTO students (FirstName, MiddleName, LastName, Sex, Birthdate, LRN, YearLevel, Section, ContactNumber)
                  VALUES ('$FirstName', '$MiddleName', '$LastName', '$Sex', '$Birthdate', '$LRN', '$YearLevel', '$Section', '$ContactNumber')";

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
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }
    th {
      background-color: #1b5e20;
      color: white;
    }
    td, th {
      padding: 8px;
      text-align: center;
    }
    table {
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <div class="header">Student Information Management System</div>

  <div class="container-fluid">
    <div class="row flex-column flex-md-row">
      <div class="col-12 col-md-3 sidebar">
        <div class="mb-4 d-flex align-items-center">
          <img src="lnhslogo.png" alt="Admin" class="avatar me-2">
          <div>
            <div style="font-size:25px;">Administrator</div>
            <small>Janferson Eugenio</small>
          </div>
        </div>

        <a href="addstud.php" class="btn btn-outline-light">Student Registration</a>
        <a href="manageadmin.php" class="btn btn-outline-light">Manage Informations</a>
        <a href="docreqs.php" class="btn btn-outline-light">Document Requests</a>
        <button class="btn btn-outline-light">Remove Enrollee</button>
        <button class="btn btn-outline-light">Personal Information</button>
        <button class="btn btn-outline-light">Profile Management</button>
        <button class="btn btn-outline-light">View Reports</button>
        <br><br>
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirmLogout();">
          <i class="bi bi-box-arrow-left"></i> Logout
        </a>

        <script>
          function confirmLogout() {
            return confirm("Are you sure you want to log out?");
          }
        </script>
      </div>

      <div class="col-12 col-md-9 p-4">
        <div class="form-section">
          <?php if ($successMessage): ?>
              <script>
                alert("<?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>");
              </script>
            <?php endif; ?>

          <form action="addstud.php" method="post" autocomplete="off">
            <div class="row">
              <div class="col-12 col-md-4">
                <input type="text" name="FirstName" class="form-control" placeholder="First Name" required
                       value="<?= htmlspecialchars($_POST['FirstName'] ?? '') ?>">
              </div>
              <div class="col-12 col-md-4">
                <input type="text" name="MiddleName" class="form-control" placeholder="Middle Name"
                       value="<?= htmlspecialchars($_POST['MiddleName'] ?? '') ?>">
              </div>
              <div class="col-12 col-md-4">
                <input type="text" name="LastName" class="form-control" placeholder="Last Name" required
                       value="<?= htmlspecialchars($_POST['LastName'] ?? '') ?>">
              </div>
              <div class="col-12 col-md-4">
                <select name="Sex" class="form-control" required>
                  <option value="" disabled <?= !isset($_POST['Sex']) ? 'selected' : '' ?>>Select Sex</option>
                  <option value="Male" <?= (($_POST['Sex'] ?? '') == 'Male') ? 'selected' : '' ?>>Male</option>
                  <option value="Female" <?= (($_POST['Sex'] ?? '') == 'Female') ? 'selected' : '' ?>>Female</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <input type="date" name="Birthdate" class="form-control" required
                       value="<?= htmlspecialchars($_POST['Birthdate'] ?? '') ?>">
              </div>
              <div class="col-12 col-md-4">
                <input type="text" name="LRN" maxlength="12" pattern="\d{12}" inputmode="numeric"
                       class="form-control <?= $lrnError ? 'is-invalid' : '' ?>" placeholder="LRN (12 digit)" required
                       value="<?= htmlspecialchars($_POST['LRN'] ?? '') ?>">
                <?php if ($lrnError): ?>
                  <div class="invalid-feedback"><?= $lrnError ?></div>
                <?php endif; ?>
              </div>
              <div class="col-12 col-md-4">
                <select name="YearLevel" id="YearLevel" class="form-control" required>
                  <option value="" disabled <?= !isset($_POST['YearLevel']) ? 'selected' : '' ?>>Select Year Level</option>
                  <?php
                  for ($i = 7; $i <= 12; $i++) {
                      $selected = (($_POST['YearLevel'] ?? '') == $i) ? 'selected' : '';
                      echo "<option value='$i' $selected>Grade $i</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-12 col-md-4">
                 <select name="Section" id="Section" class="form-control" required>
                  <option value="" disabled selected hidden>Select Section</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <input type="text" name="ContactNumber" class="form-control" placeholder="Contact Number" required
                       value="<?= htmlspecialchars($_POST['ContactNumber'] ?? '') ?>">
              </div>
              <div class="col-12 d-flex justify-content-center">
                <button type="submit" name="submit" class="btn register-btn mt-2">REGISTER</button>
              </div>
            </div>
          </form>
        </div>

        <div class="table-responsive mt-4" style="max-height: 400px; overflow-y: auto;">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Sex</th>
                <th>Birthdate</th>
                <th>LRN</th>
                <th>Year Level</th>
                <th>Section</th>
                <th>Contact Number</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT ID, FirstName, MiddleName, LastName, Sex, Birthdate, LRN, YearLevel, Section, ContactNumber FROM students";
              $result = $conn->query($sql);
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr>
                              <td>{$row['ID']}</td>
                              <td>{$row['FirstName']}</td>
                              <td>{$row['MiddleName']}</td>
                              <td>{$row['LastName']}</td>
                              <td>{$row['Sex']}</td>
                              <td>{$row['Birthdate']}</td>
                              <td>{$row['LRN']}</td>
                              <td>{$row['YearLevel']}</td>
                              <td>{$row['Section']}</td>
                              <td>{$row['ContactNumber']}</td>
                            </tr>";
                  }
              } else {
                  echo "<tr><td colspan='10'>0 results</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    const sectionOptions = {
      jhs: ["Section A", "Section B", "Section C", "SPA"],
      shs: ["STEM Track", "ABM Track", "HUMSS Track", "Arts & Design Track"]
    };

    const yearLevel = document.getElementById('YearLevel');
    const section = document.getElementById('Section');

    yearLevel.addEventListener('change', updateSections);

    function updateSections() {
      const selected = parseInt(yearLevel.value);
      const options = selected >= 11 ? sectionOptions.shs : sectionOptions.jhs;

      section.innerHTML = '<option disabled selected hidden>Select Section</option>';
      options.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt;
        option.text = opt;
        section.appendChild(option);
      });


      const previous = <?= json_encode($_POST['Section'] ?? '') ?>;
      if (previous) section.value = previous;
    }

    if (yearLevel.value) {
      updateSections();
    }
  </script>
</body>
</html>
