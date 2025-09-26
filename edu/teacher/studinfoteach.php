<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['teacher_id'])) {
  header("Location: teacherlogin.php");
  exit();
}

$teacherID = $_SESSION['teacher_id'];
$teacherName = $_SESSION['teacher_name'] ?? '';

// --- Fetch assigned YearLevels and Sections for this teacher ---
$filterQuery = "
    SELECT DISTINCT yl.yearlevel_ID, yl.YearName, sec.section_ID, sec.SectionName
    FROM subjects subj
    JOIN yearlevels yl ON subj.YearLevelID = yl.yearlevel_ID
    JOIN sections sec ON subj.SectionID = sec.section_ID
    JOIN teacher_subjects ts ON ts.subject_ID = subj.subject_ID
    WHERE ts.teachers_ID = ?
    ORDER BY yl.YearName, sec.SectionName
";
$stmtFilter = $conn->prepare($filterQuery);
$stmtFilter->bind_param("i", $teacherID);
$stmtFilter->execute();
$filterResults = $stmtFilter->get_result();

// Build a nested array: yearlevel_ID => ['YearName'=>..., 'sections' => [section_id => SectionName, ...]]
$yearLevels = [];
while ($r = $filterResults->fetch_assoc()) {
    $ylid = (int)$r['yearlevel_ID'];
    $secid = (int)$r['section_ID'];
    if (!isset($yearLevels[$ylid])) {
        $yearLevels[$ylid] = [
            'YearName' => $r['YearName'],
            'sections' => []
        ];
    }
    $yearLevels[$ylid]['sections'][$secid] = $r['SectionName'];
}

// --- Get selected filter values (cast to int) ---
$selectedYearLevel = isset($_GET['YearLevelID']) ? intval($_GET['YearLevelID']) : 0;
$selectedSection   = isset($_GET['SectionID']) ? intval($_GET['SectionID']) : 0;

// --- Query students based on teacher’s subjects & selected filter ---
$query = "
    SELECT DISTINCT s.*, yl.YearName, sec.SectionName, s.IsActive
    FROM students s
    JOIN yearlevels yl ON s.YearLevelID = yl.yearlevel_ID
    JOIN sections sec ON s.SectionID = sec.section_ID
    JOIN subjects subj ON subj.YearLevelID = s.YearLevelID AND subj.SectionID = s.SectionID
    JOIN teacher_subjects ts ON ts.subject_ID = subj.subject_ID
    WHERE ts.teachers_ID = ?
";

$params = [$teacherID];
$types = "i";

if ($selectedYearLevel > 0) {
    $query .= " AND s.YearLevelID = ?";
    $types .= "i";
    $params[] = $selectedYearLevel;
}

if ($selectedSection > 0) {
    $query .= " AND s.SectionID = ?";
    $types .= "i";
    $params[] = $selectedSection;
}

$query .= " ORDER BY s.LastName, s.FirstName";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}

// bind params dynamically
$a_params = array_merge([$types], $params);
$refs = [];
foreach ($a_params as $key => $value) {
    // bind_param requires references
    $refs[$key] = &$a_params[$key];
}
call_user_func_array([$stmt, 'bind_param'], $refs);

$stmt->execute();
$results = $stmt->get_result();
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
    .sidebar { background-color: #0d4b16; min-height: 100vh; color: white; padding: 20px; }
    .sidebar .btn { width: 100%; text-align: left; margin-bottom: 10px; font-family: Arial, Helvetica, sans-serif; }
    .logout { color: red; font-weight: bold; }
    .form-section { background-color: #fffde7; padding: 30px; border-radius: 10px; }
    .header { background-color: #1b5e20; color: white; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; }
    .avatar { width: 70px; height: 70px; border-radius: 50%; }
    th { background-color: #1b5e20; color: white; }
    td, th { text-align: center; vertical-align: middle; }
    .table-responsive { max-height: 450px; overflow-y: auto; }
    .edit-btn { background-color: #124820; color: white; border-radius: 25px; padding: 6px 20px; font-weight: bold; }
    .edit-btn:hover { background-color: #a8aa10ff; color: black; }
    h4.text-center { background-color: #0d4b16; border-radius: 25px; padding: 10px; width: 50%; color: #ffff; margin: 0 auto; }
    .btn-outline-light { font-family: Arial, Helvetica, sans-serif; }
  </style>
</head>
<body>

<div class="header">Student Information Management System</div>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 sidebar">
      <div class="mb-4 d-flex align-items-center">
        <a href="teacherdash.php" style="text-decoration: none;">
          <img src="lnhslogo.png" alt="Teacher" class="avatar me-2">
        </a>
        <div>
          <div style="font-size:25px;">Teacher</div>
          <small><?= htmlspecialchars($teacherName) ?></small>
        </div>
      </div>

      <a href="addstudteacher.php" class="btn btn-outline-light">Student Registration</a>
      <a href="manageteach.php" class="btn btn-outline-light">Manage Informations</a>
      <a href="gradesmanage.php" class="btn btn-outline-light">Grades Management</a>
      <a href="persoinfoteach.php" class="btn btn-outline-light">Personal Information</a>
      <a href="passteach.php" class="btn btn-outline-light">Password Management</a>
      <a href="regparteach.php" class="btn btn-outline-light">Register Parents</a>
      <br><br>
      <a href="logout.php" class="logout text-decoration-none"
         onclick="return confirm('Are you sure you want to log out?');">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 p-4">
      <div class="form-section">
        <h4 class="mb-4 text-center">My Students</h4>

        <form method="get" class="row mb-4">
          <div class="col-md-4">
            <label class="form-label"><strong>Year Level </strong></label>
            <select name="YearLevelID" class="form-select" onchange="this.form.submit()">
              <option value="">All Year Levels</option>
              <?php foreach ($yearLevels as $ylid => $data): ?>
                <option value="<?= $ylid ?>"
                  <?= ($selectedYearLevel == $ylid) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($data['YearName']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label"><strong>Section</strong></label>
            <select name="SectionID" class="form-select" onchange="this.form.submit()" <?= $selectedYearLevel ? '' : 'disabled' ?>>
              <option value="">All Sections</option>
              <?php
                if ($selectedYearLevel && isset($yearLevels[$selectedYearLevel])) {
                    foreach ($yearLevels[$selectedYearLevel]['sections'] as $secid => $secName) {
                        $sel = ($selectedSection == $secid) ? 'selected' : '';
                        echo '<option value="' . intval($secid) . '" ' . $sel . '>' . htmlspecialchars($secName) . '</option>';
                    }
                }
              ?>
            </select>
            <?php if (!$selectedYearLevel): ?>
              <div class="form-text">Select a Year Level first to choose Section.</div>
            <?php endif; ?>
          </div>

          <div class="col-md-4 d-flex align-items-end">
  <a href="<?= htmlspecialchars(basename(__FILE__)) ?>" class="btn btn-secondary">Reset</a>
</div>

        </form>

  
        <?php if ($results && $results->num_rows > 0): ?>
          <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th><th>FirstName</th><th>MiddleName</th><th>LastName</th><th>Suffix</th>
                  <th>Sex</th><th>Birthdate</th><th>LRN</th><th>YearLevel</th><th>Section</th>
                  <th>ContactNumber</th><th>EmailAddress</th><th>Address</th><th>Status</th><th>Active</th><th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $results->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['students_ID']) ?></td>
                    <td><?= htmlspecialchars($row['FirstName']) ?></td>
                    <td><?= htmlspecialchars($row['MiddleName']) ?></td>
                    <td><?= htmlspecialchars($row['LastName']) ?></td>
                    <td><?= htmlspecialchars($row['Suffix']) ?></td>
                    <td><?= htmlspecialchars($row['Sex']) ?></td>
                    <td><?= htmlspecialchars($row['Birthdate']) ?></td>
                    <td><?= htmlspecialchars($row['LRN']) ?></td>
                    <td><?= htmlspecialchars($row['YearName']) ?></td>
                    <td><?= htmlspecialchars($row['SectionName']) ?></td>
                    <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
                    <td><?= htmlspecialchars($row['EmailAddress']) ?></td>
                    <td><?= htmlspecialchars($row['Address']) ?></td>
                    <td><?= htmlspecialchars($row['Status']) ?></td>
                    <td><?= $row['IsActive'] ? 'Active' : 'Disabled' ?></td>
                    <td>
                      <?php if ($row['IsActive']): ?>
                        <a href="editstudteach.php?sid=<?= $row['students_ID'] ?>" class="btn edit-btn btn-sm">Edit</a>
                      <?php else: ?>
                        <span class="text-muted">No Action</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="alert alert-warning text-center mt-4 col-md-8 mx-auto">
            No students found in your assigned Year Level and Section(s).
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

</body>
</html>
