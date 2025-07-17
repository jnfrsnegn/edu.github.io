<?php
require '../conn.php'; // adjust if your path is different

$results = [];
$selectedYear = '';
$selectedSection = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $selectedYear = $_POST['YearLevel'] ?? '';
  $selectedSection = $_POST['Section'] ?? '';

  if ($selectedYear && $selectedSection) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE YearLevel = ? AND Section = ?");
    $stmt->bind_param("ss", $selectedYear, $selectedSection);
    $stmt->execute();
    $results = $stmt->get_result();
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
    body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5dc; overflow: hidden; }
    .sidebar { background-color: #0d4b16; height: 100vh; color: white; padding: 20px; }
    .sidebar .btn { width: 100%; text-align: left; margin-bottom: 10px; }
    .logout { color: red; font-weight: bold; }
    .form-section { background-color: #fffde7; padding: 30px; border-radius: 10px; }
    .form-control { border-radius: 20px; margin-bottom: 15px; max-width: 400px; }
    .header { background-color: #1b5e20; color: white; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; }
    .avatar { width: 40px; height: 40px; border-radius: 50%; }
    th { background-color: #1b5e20; color: white; }
    td, th { padding: 8px; text-align: center; }
    table { margin-top: 20px; }
  </style>
</head>
<body>
  <div class="header">Student Information Management System</div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3 sidebar">
        <div class="mb-4 d-flex align-items-center">
          <img src="lnhslogo.png" alt="Admin" class="avatar me-2">
          <div>
            <div style="font-size:25px;">Administrator</div>
            <small>Janferson Eugenio</small>
          </div>
        </div>

        <a href="addstud.php" class="btn btn-outline-light">Student Registration</a>
        <a href="manageadmin.php" class="btn btn-outline-light">Manage Informations</a>
        <button class="btn btn-outline-light">Document Requests</button>
        <button class="btn btn-outline-light">Remove Enrollee</button>
        <button class="btn btn-outline-light">Personal Information</button>
        <button class="btn btn-outline-light">Profile Management</button>
        <button class="btn btn-outline-light">View Reports</button>
        <br><br>
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirmLogout();">
          <i class="bi bi-box-arrow-left"></i> Logout
        </a>
        <script>function confirmLogout() { return confirm("Are you sure you want to log out?"); }</script>
      </div>

      <div class="col-md-9 p-4">
        <div class="form-section">
          <h2>Student Information</h2>
          <form method="POST">
            <div class="row">
              <div class="col-md-6">
                <select name="YearLevel" id="YearLevel" class="form-control" required>
                  <option value="" disabled hidden>Select Year Level</option>
                  <?php
                  for ($i = 7; $i <= 12; $i++) {
                    $sel = ($selectedYear == $i) ? "selected" : "";
                    echo "<option value='$i' $sel>Grade $i</option>";
                  }
                  ?>
                </select>
              </div>

              <div class="col-md-6">
                <select name="Section" id="Section" class="form-control" required <?= $selectedYear ? '' : 'disabled' ?>>
                  <option value="" disabled hidden>Select Section</option>
                </select>
              </div>

              <div class="col-12 mt-3">
                <button type="submit" class="btn btn-success px-5">Search</button>
              </div>
            </div>
          </form>

          <?php if ($results && $results->num_rows > 0): ?>
          <div class="table-responsive mt-4">
            <table class="table table-bordered">
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
                  <th>Contact</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $results->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['ID'] ?></td>
                  <td><?= $row['FirstName'] ?></td>
                  <td><?= $row['MiddleName'] ?></td>
                  <td><?= $row['LastName'] ?></td>
                  <td><?= $row['Sex'] ?></td>
                  <td><?= $row['Birthdate'] ?></td>
                  <td><?= $row['LRN'] ?></td>
                  <td><?= $row['YearLevel'] ?></td>
                  <td><?= $row['Section'] ?></td>
                  <td><?= $row['ContactNumber'] ?></td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
          <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p class="mt-4 text-danger">No students found for Grade <?= $selectedYear ?>, Section <?= htmlspecialchars($selectedSection) ?>.</p>
          <?php endif; ?>
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
    const selectedSection = "<?= $selectedSection ?>";

    function updateSections() {
      const selected = parseInt(yearLevel.value);
      const options = selected >= 11 ? sectionOptions.shs : sectionOptions.jhs;

      section.innerHTML = '<option disabled hidden>Select Section</option>';
      section.disabled = false;

      options.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt;
        option.textContent = opt;
        if (opt === selectedSection) option.selected = true;
        section.appendChild(option);
      });
    }

    if (yearLevel.value) {
      updateSections();
    }

    yearLevel.addEventListener('change', updateSections);
  </script>
</body>
</html>
