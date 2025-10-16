<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['students'])) {
    header("Location: studentlogin.php");
    exit();
}

$lrn = $_SESSION['students'];

$stmt = $conn->prepare("SELECT students_ID, FirstName, MiddleName, LastName FROM students WHERE LRN = ?");
$stmt->bind_param("s", $lrn);
$stmt->execute();
$studentResult = $stmt->get_result();
$student = $studentResult->fetch_assoc();
$students_ID = $student['students_ID'];
$fullName = trim($student['FirstName'] . ' ' . $student['MiddleName'] . ' ' . $student['LastName']);

$successMessage = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['form_type'])) {
    $formType = $_POST['form_type'];
    $requestDate = date("Y-m-d");
    $status = "Pending";

    $check = $conn->prepare("SELECT * FROM docreqs WHERE students_ID = ? AND FormType = ?");
    $check->bind_param("is", $students_ID, $formType);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        $successMessage = "<span class='text-danger fw-bold'>You already requested this form.</span>";
    } else {
        $insert = $conn->prepare("INSERT INTO docreqs (students_ID, LRN, FormType, RequestDate, Status) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("issss", $students_ID, $lrn, $formType, $requestDate, $status);
        $insert->execute();

        $successMessage = "Your request has been submitted!";
    }
}

$query = "SELECT * FROM docreqs WHERE students_ID = ? ORDER BY RequestDate DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $students_ID);
$stmt->execute();
$docResults = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIMS</title>
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
      padding: 15px;
      text-align: center;
      font-size: 20px;
      font-weight: bold;
    }

    .sidebar {
      background-color: #0d4b16;
      height: 100vh;
      color: white;
      padding: 15px;
      overflow-y: auto;
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

    .btn-outline-light {
      font-family: Arial, Helvetica, sans-serif;
    }

    .btn-icon {
      margin-right: 8px;
      width: 20px;
    }

    .form-section {
      background-color: #fffde7;
      padding: 25px;
      border-radius: 10px;
      min-height: 400px;
    }

    .form-control {
      border-radius: 20px;
      margin-bottom: 15px;
    }

    .submit-btn {
      background-color: #124820;
      color: white;
      border-radius: 25px;
      padding: 10px 30px;
      font-weight: bold;
      width: 100%;
      max-width: 400px;
    }

    .submit-btn:hover {
      background-color: #a8aa10ff;
    }

    .doc-card {
      background-color: #fff;
      border: 1px solid #ccc;
      padding: 15px 20px;
      margin-bottom: 15px;
      border-radius: 10px;
    }

    .doc-card strong {
      color: #1b5e20;
    }

    .status-pending {
      color: orange;
    }

    .status-approved {
      color: green;
    }

    .status-rejected {
      color: red;
    }

    .container-fluid {
      min-height: calc(100vh - 70px); /* Adjust for header height */
    }

    .row {
      min-height: 100%;
    }

    @media (max-width: 992px) {
      .sidebar {
        height: auto;
        padding: 10px;
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
        overflow-y: visible;
      }

      .container-fluid {
        min-height: auto;
      }

      .row {
        min-height: auto;
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

      .doc-card {
        padding: 12px 15px;
        margin-bottom: 10px;
      }

      .form-control {
        font-size: 16px;
        padding: 12px 15px; 
      }

      .submit-btn {
        padding: 12px 20px; 
        font-size: 16px;
        width: 100%;
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

      .avatar {
        width: 40px;
        height: 40px;
      }

      .form-section {
        padding: 10px;
      }

      .doc-card {
        padding: 10px 12px;
        margin-bottom: 8px;
      }

      .form-control {
        font-size: 16px;
        margin-bottom: 12px;
        padding: 12px 12px;
      }

      .submit-btn {
        font-size: 16px;
        padding: 12px 15px;
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
        width: 35px;
        height: 35px;
      }

      .form-section {
        padding: 8px;
      }

      .doc-card {
        padding: 8px 10px;
        margin-bottom: 6px;
      }

      .form-control {
        font-size: 16px;
        padding: 10px 10px;
        margin-bottom: 10px;
      }

      .submit-btn {
        font-size: 15px;
        padding: 10px 12px;
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
        <a href="studentdash.php" style="text-decoration: none;">
          <img src="lnhslogo.png" alt="Student" class="avatar me-2">
        </a>
        <div>
          <div style="font-size:20px;">Student</div>
          <?php
$first = $student['FirstName'];
$middle = $student['MiddleName'];
$last = $student['LastName'];

// Hide 'n/a' or empty middle names
if (empty($middle) || strtolower(trim($middle)) === 'n/a') {
    $displayName = "$first $last";
} else {
    $displayName = "$first $middle $last";
}
?>
<small><?= htmlspecialchars($displayName) ?></small>

        </div>
      </div>

      <a href="viewgrades.php" class="btn btn-outline-light">
        <i class="bi bi-clipboard-data btn-icon"></i>View Grades
      </a>

      <a href="#collapseAccount" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseAccount">
        <i class="bi bi-person-circle btn-icon"></i>Account
        <i class="bi bi-chevron-right"></i>
      </a>
      <div class="collapse" id="collapseAccount">
        <a href="persoinfo.php" class="btn btn-outline-light sub-btn">
          <i class="bi bi-person btn-icon"></i>Personal Information
        </a>
        <a href="passmanage.php" class="btn btn-outline-light sub-btn">
          <i class="bi bi-lock btn-icon"></i>Password Management
        </a>
      </div>

      <a href="reqdocs.php" class="btn btn-outline-light active">
        <i class="bi bi-file-earmark-text btn-icon"></i>Request Form
      </a>

      <br><br>
      <a href="#" class="logout text-decoration-none" id="logoutBtn">
        <i class="bi bi-box-arrow-right me-2"></i>Logout
      </a>
    </div>

    <div class="col-md-9 p-3">
      <div class="form-section">
        <?php if ($successMessage): ?>
          <div class="alert alert-success text-center"><?= $successMessage; ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-4 d-flex flex-column align-items-center">
          <div style="width: 100%; max-width: 400px;">
            <select name="form_type" class="form-control" required>
              <option value="" disabled selected>Select a document</option>
              <option value="Good Moral">Good Moral</option>
              <option value="Diploma">Diploma</option>
              <option value="Certification of Grades">Certification of Grades</option>
            </select>
          </div>
          <div class="mt-2">
            <button type="submit" class="btn submit-btn">Submit Request</button>
          </div>
        </form>

        <h5 class="mb-3">Requested Forms</h5>
        <?php if ($docResults->num_rows > 0): ?>
          <?php while ($doc = $docResults->fetch_assoc()): ?>
            <div class="doc-card">
              <p><strong>Type:</strong> <?= htmlspecialchars($doc['FormType']); ?></p>
              <p><strong>Date:</strong> <?= htmlspecialchars(date("F j, Y", strtotime($doc['RequestDate']))); ?></p>
              <p><strong>Status:</strong>
                <span class="status-<?= strtolower($doc['Status']); ?>">
                  <?= htmlspecialchars($doc['Status']); ?>
                </span>
              </p>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-center">No document requests yet.</p>
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
</script>
</body>
</html>
