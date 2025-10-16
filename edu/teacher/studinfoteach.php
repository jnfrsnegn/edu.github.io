<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['teacher_id'])) {
  header("Location: teacherlogin.php");
  exit();
}

$teacherID = $_SESSION['teacher_id'];
$teacherName = $_SESSION['teacher_name'] ?? '';

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

$selectedYearLevel = isset($_GET['YearLevelID']) ? intval($_GET['YearLevelID']) : 0;
$selectedSection   = isset($_GET['SectionID']) ? intval($_GET['SectionID']) : 0;

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

$a_params = array_merge([$types], $params);
$refs = [];
foreach ($a_params as $key => $value) {
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <style></style>

  <style>
    body { 
      font-family: 'Segoe UI', sans-serif; 
      background-color: #f5f5dc; 
      overflow-x: hidden;
    }

    .header { 
      background-color: #1b5e20; 
      color: white; 
      padding: 15px; 
      text-align: center; 
      font-size: 20px; 
      font-weight: bold;
    }

    .sidebar { 
      background-color: #0d4b16; 
      height: 95vh; 
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

    .form-section { 
      background-color: #fffde7; 
      padding: 25px; 
      border-radius: 10px; 
    }

    th { 
      background-color: #1b5e20; 
      color: white; 
      text-align: center;
    }

    td, th { 
      padding: 8px; 
      text-align: center; 
      font-size: 14px;
      vertical-align: middle;
    }

    .table-responsive { 
      overflow-x: auto;
    }

    .edit-btn { 
      background-color: #124820; 
      color: white; 
      border-radius: 25px; 
      padding: 6px 20px; 
      font-weight: bold; 
      text-decoration: none;
      display: inline-block;
    }

    .edit-btn:hover { 
      background-color: #a8aa10ff; 
      color: black; 
      text-decoration: none;
    }

    h4.text-center { 
      background-color: #0d4b16; 
      border-radius: 25px; 
      padding: 10px; 
      width: 50%; 
      color: #ffff; 
      margin: 0 auto 20px;
    }

    .btn-outline-light { 
      font-family: Arial, Helvetica, sans-serif; 
    }

    .btn-icon { 
      margin-right: 8px; 
      width: 20px;
    }

    .avatar { 
      width: 60px; 
      height: 60px; 
      border-radius: 50%;
    }

    .form-select, .form-label {
      font-size: 14px;
    }

    @media (max-width: 992px) {
      .sidebar {
        height: auto;
        padding: 10px;
      }

      .form-section {
        padding: 20px;
        min-height: auto;
      }

      h4.text-center {
        width: 80%;
        font-size: 1.1rem;
      }

      .table-responsive {
        font-size: 0.9rem;
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

      .form-row {
        flex-direction: column;
        align-items: stretch;
      }

      .form-row .col-md-4 {
        width: 100%;
        margin-bottom: 10px;
      }

      .form-select {
        font-size: 16px; 
        padding: 12px 15px;
      }

      .btn-secondary {
        width: 100%;
        margin-top: 10px;
        font-size: 16px;
        padding: 12px;
      }

      .table-responsive {
        font-size: 0.85rem;
      }

      .table th,
      .table td {
        padding: 4px;
        white-space: nowrap;
      }

      .edit-btn {
        padding: 8px 12px;
        font-size: 14px;
        width: 100%;
        margin: 2px 0;
      }

      .sidebar .btn {
        font-size: 14px;
      }

      .sidebar .sub-btn {
        width: calc(100% - 10px);
        margin-left: 10px;
        font-size: 13px;
      }

      .logout {
        font-size: 14px;
      }
    }

    @media (max-width: 480px) {
      .header {
        font-size: 16px;
        padding: 10px;
      }

      .sidebar {
        padding: 8px;
      }

      .sidebar .btn {
        font-size: 13px;
        padding: 8px;
      }

      .sidebar .sub-btn {
        font-size: 12px;
        padding: 6px;
      }

      .form-section {
        padding: 10px;
      }

      h4.text-center {
        width: 100%;
        font-size: 0.9rem;
        padding: 6px;
      }

      .form-select {
        font-size: 16px;
        padding: 12px 12px;
        margin-bottom: 8px;
      }

      .btn-secondary {
        font-size: 16px;
        padding: 12px 15px;
      }

      .table th,
      .table td {
        font-size: 12px;
        padding: 6px 2px;
      }

      .edit-btn {
        font-size: 14px;
        padding: 10px 8px;
      }

      .logout {
        font-size: 13px;
      }
    }

    @media (max-width: 320px) {
      .header {
        font-size: 15px;
        padding: 8px;
      }

      .sidebar {
        padding: 6px;
      }

      .sidebar .btn {
        font-size: 12px;
        padding: 6px;
      }

      .sidebar .sub-btn {
        font-size: 11px;
        padding: 4px;
        margin-left: 8px;
      }

      .avatar {
        width: 40px;
        height: 40px;
      }

      .form-section {
        padding: 8px;
      }

      h4.text-center {
        font-size: 0.85rem;
        padding: 5px;
      }

      .form-select {
        font-size: 16px;
        padding: 10px 10px;
        margin-bottom: 6px;
      }

      .btn-secondary {
        font-size: 15px;
        padding: 10px 12px;
      }

      .table th,
      .table td {
        font-size: 11px;
        padding: 4px 1px;
      }

      .edit-btn {
        font-size: 13px;
        padding: 8px 6px;
      }

      .logout {
        font-size: 12px;
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
        <a href="teacherdash.php" style="text-decoration: none;">
          <img src="lnhslogo.png" alt="Teacher" class="avatar me-2">
        </a>
        <div>
          <div style="font-size:20px;">Teacher</div>
          <small><?= htmlspecialchars($teacherName) ?></small>
        </div>
      </div>

      <a href="#collapseStudents" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseStudents">
        <i class="bi bi-people btn-icon"></i>Students
        <i class="bi bi-chevron-right"></i>
      </a>
      <div class="collapse show" id="collapseStudents">
        <a href="addstudteacher.php" class="btn btn-outline-light sub-btn">
          <i class="bi bi-person-plus btn-icon"></i>Student Registration
        </a>
        <a href="studinfoteach.php" class="btn btn-outline-light sub-btn active">
          <i class="bi bi-person-gear btn-icon"></i>Student Informations
        </a>
      </div>


      <a href="#collapseGrades" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseGrades">
        <i class="bi bi-clipboard-data btn-icon"></i>Grades
        <i class="bi bi-chevron-right"></i>
      </a>
      <div class="collapse" id="collapseGrades">
        <a href="gradesmanage.php" class="btn btn-outline-light sub-btn">
          <i class="bi bi-clipboard-data btn-icon"></i>Grades Management
        </a>
      </div>

      <a href="#collapseAccount" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseAccount">
        <i class="bi bi-person-circle btn-icon"></i>Account
        <i class="bi bi-chevron-right"></i>
      </a>
      <div class="collapse" id="collapseAccount">
        <a href="persoinfoteach.php" class="btn btn-outline-light sub-btn">
          <i class="bi bi-person btn-icon"></i>Personal Information
        </a>
        <a href="passteach.php" class="btn btn-outline-light sub-btn">
          <i class="bi bi-lock btn-icon"></i>Password Management
        </a>
      </div>
      
      <br><br>
      <a href="#" class="logout text-decoration-none" id="logoutBtn">
    <i class="bi bi-box-arrow-right me-2"></i>Logout
</a>
    </div>
    <div class="col-md-9 p-3">
      <div class="form-section">
        <h4 class="mb-4 text-center">My Students</h4>

        <form method="get" class="row mb-4 form-row">
          <div class="col-12 col-md-4">
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

          <div class="col-12 col-md-4">
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

          <div class="col-12 col-md-4 d-flex align-items-end">
            <a href="<?= htmlspecialchars(basename(__FILE__)) ?>" class="btn btn-secondary">Reset</a>
          </div>
        </form>

  
        <?php if ($results && $results->num_rows > 0): ?>
          <div class="table-responsive mt-4">
            <table id="studentsTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>LRN</th><th>FirstName</th><th>MiddleName</th><th>LastName</th><th>Suffix</th>
                  <th>Sex</th><th>Birthdate</th><th>YearLevel</th><th>Section</th>
                  <th>ContactNumber</th><th>EmailAddress</th><th>Address</th><th>Status</th><th>Active</th><th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $results->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['LRN']) ?></td>
                    <td><?= htmlspecialchars($row['FirstName']) ?></td>
                    <td><?= htmlspecialchars($row['MiddleName']) ?></td>
                    <td><?= htmlspecialchars($row['LastName']) ?></td>
                    <td><?= htmlspecialchars($row['Suffix']) ?></td>
                    <td><?= htmlspecialchars($row['Sex']) ?></td>
                    <td><?= htmlspecialchars($row['Birthdate']) ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
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
 $(document).ready(function() {
            $('#studentsTable').DataTable({
                "pageLength": 5, 
                "lengthChange": false, 
                "ordering": true, 
                "info": true,
                "autoWidth": false
            });
        });
</script>
</body>
</html>