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
$fullName = $student['FirstName'] . ' ' . $student['MiddleName'] . ' ' . $student['LastName'];

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

        $successMessage = "Your reqeust  has been submitted!";
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
      height: 100vh;
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

    .submit-btn {
      background-color: #124820;
      color: white;
      border-radius: 25px;
      padding: 10px 30px;
      font-weight: bold;
      width: 100%;
    }

    .submit-btn:hover {
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
    .btn-outline-light{
      font-family: Arial, Helvetica, sans-serif;
    }
  </style>
</head>
<body>
<div class="header">Student Information Management System</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-3 sidebar">
      <div class="mb-4 d-flex align-items-center">
        <a href="studentdash.php"><img src="lnhslogo.png" alt="Student" class="avatar me-2"></a>
        <div>
          <div style="font-size:25px;">Student</div>
          <small><?= htmlspecialchars($fullName); ?></small>
        </div>
      </div>

      <a href="viewgrades.php" class="btn btn-outline-light">View Grades</a>
        <a href="persoinfo.php" class="btn btn-outline-light">Personal Information</a>
        <a href="reqdocs.php" class="btn btn-outline-light">Request Form</a>
        <a href="parentreq.php" class="btn btn-outline-light ">Parent Request</a>
        <a href="passmanage.php" class="btn btn-outline-light">Password Management</a>
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

    <div class="col-md-9 p-4">
      <div class="form-section" style="height:800px; overflow-y: auto;">

        <?php if ($successMessage): ?>
          <div class="alert alert-success text-center"><?= $successMessage; ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-4 d-flex flex-column align-items-center">
          <div style="width: 50%;">
            <select name="form_type" class="form-control" required>
              <option value="" disabled selected>Select a document</option>
              <option value="Good Moral">Good Moral</option>
              <option value="Diploma">Diploma</option>
              <option value="Certification of Grades">Certification of Grades</option>
            </select>
          </div>
          <div>
            <button type="submit" class="btn submit-btn mt-2">Submit Request</button>
          </div>
        </form>

        <h5 class="mb-3">Reqeusted Forms</h5>
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
          <p>No document requests yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
