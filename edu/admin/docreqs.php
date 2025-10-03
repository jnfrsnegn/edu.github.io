<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: adminlogin.php");
  exit();
}

$selectedFormType = isset($_GET['filter']) ? $_GET['filter'] : '';
$selectedStatus = isset($_GET['status']) ? $_GET['status'] : '';

$query = "
  SELECT 
    d.request_ID, d.FormType, d.RequestDate, d.Status,
    s.FirstName, s.MiddleName, s.LastName, s.LRN, s.EmailAddress
  FROM docreqs d
  JOIN students s ON d.students_ID = s.students_ID
  WHERE 1=1
";

if (!empty($selectedFormType)) {
  $query .= " AND d.FormType = '" . mysqli_real_escape_string($conn, $selectedFormType) . "'";
}

if (!empty($selectedStatus)) {
  $query .= " AND d.Status = '" . mysqli_real_escape_string($conn, $selectedStatus) . "'";
}

$query .= " ORDER BY d.RequestDate DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SIMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f5dc;
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

    .register-btn {
      background-color: #124820;
      color: white;
      border-radius: 25px;
      padding: 10px 30px;
      font-weight: bold;
      width: 400px;
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
  </style>
</head>
<body style="overflow: hidden;">
  <div class="header">Student Information Management System</div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3 sidebar">
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
        <div class="form-section" style="height:800px">
          <div class="row mt-4">
            <form method="GET" class="d-flex flex-wrap gap-2 mb-3 align-items-center">
              <label class="fw-bold me-2">Filter by Document Type:</label>
              <select name="filter" class="form-select me-2" style="width: 200px;" onchange="this.form.submit()">
                <option value="">show all</option>
                <option value="Good Moral" <?= $selectedFormType == 'Good Moral' ? 'selected' : '' ?>>Good Moral</option>
                <option value="Diploma" <?= $selectedFormType == 'Diploma' ? 'selected' : '' ?>>Diploma</option>
                <option value="Certification of Grades" <?= $selectedFormType == 'Certification of Grades' ? 'selected' : '' ?>>Certification of Grades</option>
              </select>

              <label class="fw-bold me-2">Status:</label>
              <select name="status" class="form-select me-2" style="width: 200px;" onchange="this.form.submit()">
                <option value="">All status</option>
                <option value="Pending" <?= $selectedStatus == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Approved" <?= $selectedStatus == 'Approved' ? 'selected' : '' ?>>Approved</option>
                <option value="Denied" <?= $selectedStatus == 'Denied' ? 'selected' : '' ?>>Denied</option>
              </select>

              <?php if (!empty($selectedFormType) || !empty($selectedStatus)): ?>
                <a href="docreqs.php" class="btn btn-secondary btn-sm">Clear Filter</a>
              <?php endif; ?>
            </form>

            <div class="table-responsive mt-4" style="max-height: 400px; overflow-y: auto;">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>LRN</th>
                    <th>Name</th>
                    <th>Document Type</th>
                    <th>Request Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                      <tr>
                        <td><?= htmlspecialchars($row['LRN']) ?></td>
                        <td><?= htmlspecialchars($row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['LastName']) ?></td>
                        <td><?= htmlspecialchars($row['FormType']) ?></td>
                        <td><?= htmlspecialchars(date("F j, Y", strtotime($row['RequestDate']))) ?></td>
                        <td>
                          <?php if ($row['Status'] === 'Pending'): ?>
                            <a href="docstatus.php?id=<?= $row['request_ID'] ?>&action=approve&email=<?= urlencode($row['EmailAddress']) ?>&form=<?= urlencode($row['FormType']) ?>&status=Approved&notify=1" class="btn btn-warning btn-sm" onclick="return confirm('Approve this request?')">Approve</a>
                            <a href="docstatus.php?id=<?= $row['request_ID'] ?>&action=deny&email=<?= urlencode($row['EmailAddress']) ?>&form=<?= urlencode($row['FormType']) ?>&status=Denied&notify=1" class="btn btn-danger btn-sm" onclick="return confirm('Deny this request?')">Deny</a>
                          <?php elseif ($row['Status'] === 'Approved'): ?>
                            <span class="badge bg-warning">Approved</span>
                          <?php elseif ($row['Status'] === 'Denied'): ?>
                            <span class="badge bg-danger">Denied</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="5">No requests found.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.emailjs.com/dist/email.min.js"></script>
  <script>
    (function() {
      emailjs.init("Py-PphJ0-GQ1CxAuN");
    })();

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('notify') === '1') {
      emailjs.send("service_cny52jd", "template_1gh4wrb", {
        to_email: urlParams.get('email'),
        form_type: urlParams.get('form'),
        status: urlParams.get('status')
      }).then(function() {
        console.log("Email sent!");
      }, function(error) {
        console.log("Email failed:", error);
      });
    }
  </script>
</body>
</html>
