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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIMS</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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

    .form-section {
      background-color: #fffde7;
      padding: 25px;
      border-radius: 10px;
    }

    .table-responsive {
      overflow-x: auto;
    }

    th {
      background-color: #1b5e20;
      color: white;
      text-align: center;
    }

    td,
    th {
      padding: 8px;
      text-align: center;
      font-size: 14px;
    }

    .btn-status {
      margin: 2px;
    }

    .btn-icon {
      margin-right: 8px;
    }

    @media (max-width: 992px) {
      .sidebar {
        height: auto;
        padding: 10px;
      }
    }

    @media (max-width: 768px) {
      .sidebar {
        position: relative;
        height: auto;
        width: 100%;
      }

      .filter-form {
        flex-direction: column !important;
        align-items: stretch !important;
      }

      .filter-form .form-select {
        width: 100% !important;
        margin-bottom: 10px;
      }

      .btn-status {
        padding: 4px 8px;
        font-size: 0.8rem;
      }
    }

    @media (max-width: 480px) {

      td,
      th {
        font-size: 12px;
        padding: 6px;
      }

      .btn-status {
        display: block;
        width: 100%;
        margin-bottom: 5px;
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
          <a href="admindash.php" style="text-decoration:none;">
            <img src="lnhslogo.png" class="avatar me-2" alt="Admin">
          </a>
          <div>
            <div style="font-size:20px;">Administrator</div>
            <small><?= $_SESSION['admin_name'] ?? '' ?></small>
          </div>
        </div>

        <a href="#collapseStudent" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true">
          <i class="bi bi-people-fill btn-icon"></i>Student Management
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse show" id="collapseStudent">
          <a href="addstud.php" class="btn btn-outline-light sub-btn"><i class="bi bi-person-plus btn-icon"></i>Add Student</a>
          <a href="docreqs.php" class="btn btn-outline-light sub-btn active"><i class="bi bi-file-earmark-text btn-icon"></i>Document Requests</a>
          <a href="removeenrollee.php" class="btn btn-outline-light sub-btn"><i class="bi bi-person-x btn-icon"></i>Student Status</a>
        </div>

        <a href="#collapseInfo" class="btn btn-outline-light" data-bs-toggle="collapse">
          <i class="bi bi-info-circle-fill btn-icon"></i>Manage Informations
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseInfo">
          <a href="studinfo.php" class="btn btn-outline-light sub-btn"><i class="bi bi-people btn-icon"></i>Student Information</a>
          <a href="teachinfo.php" class="btn btn-outline-light sub-btn"><i class="bi bi-person-badge btn-icon"></i>Teacher Information</a>
          <a href="persoinfo.php" class="btn btn-outline-light sub-btn"><i class="bi bi-person btn-icon"></i>Personal Information</a>
          <a href="passmanage.php" class="btn btn-outline-light sub-btn"><i class="bi bi-lock btn-icon"></i>Password Management</a>
        </div>

        <a href="#collapseTeacher" class="btn btn-outline-light" data-bs-toggle="collapse">
          <i class="bi bi-person-badge-fill btn-icon"></i>Teacher Management
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseTeacher">
          <a href="regteach.php" class="btn btn-outline-light sub-btn"><i class="bi bi-person-plus btn-icon"></i>Register Teachers</a>
          <a href="assignteacher.php" class="btn btn-outline-light sub-btn"><i class="bi bi-arrow-right-circle btn-icon"></i>Assign Teacher</a>
        </div>

        <a href="#collapseAcademic" class="btn btn-outline-light" data-bs-toggle="collapse">
          <i class="bi bi-journal-bookmark-fill btn-icon"></i>Subjects & Sections
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseAcademic">
          <a href="addsubject.php" class="btn btn-outline-light sub-btn"><i class="bi bi-journal-plus btn-icon"></i>Add Subject</a>
          <a href="managesections.php" class="btn btn-outline-light sub-btn"><i class="bi bi-gear btn-icon"></i>Manage Sections</a>
        </div>

        <a href="viewrep.php" class="btn btn-outline-light"><i class="bi bi-bar-chart-fill btn-icon"></i>View Reports</a>

        <br><br>
        <a href="#" class="logout text-decoration-none" id="logoutBtn">
          <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
      </div>

      <div class="col-md-9 p-3">
        <div class="form-section">
          <form method="GET" class="d-flex flex-wrap gap-2 mb-3 align-items-center filter-form">
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

          <div class="table-responsive mt-4">
            <table id="studentsTable" class="table table-bordered table-striped">
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
                          <button class="btn btn-warning btn-sm btn-status" onclick="confirmAction('approve', '<?= $row['request_ID'] ?>', '<?= urlencode($row['EmailAddress']) ?>', '<?= urlencode($row['FormType']) ?>')">Approve</button>
                          <button class="btn btn-danger btn-sm btn-status" onclick="confirmAction('deny', '<?= $row['request_ID'] ?>', '<?= urlencode($row['EmailAddress']) ?>', '<?= urlencode($row['FormType']) ?>')">Deny</button>
                        <?php elseif ($row['Status'] === 'Approved'): ?>
                          <span class="badge bg-warning">Approved</span>
                        <?php elseif ($row['Status'] === 'Denied'): ?>
                          <span class="badge bg-danger">Denied</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5">No requests found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById("logoutBtn").addEventListener("click", function(e) {
      e.preventDefault();
      Swal.fire({
        title: "Are you sure?",
        text: "You will be logged out of the system.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#1b5e20",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, log out"
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "logout.php";
        }
      });
    });

    function confirmAction(action, id, email, form) {
      const actionText = action === 'approve' ? 'Approve this request?' : 'Deny this request?';
      const btnColor = action === 'approve' ? '#1b5e20' : '#d33';
      const status = action === 'approve' ? 'Approved' : 'Denied';

      Swal.fire({
        title: actionText,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = `docstatus.php?id=${id}&action=${action}&email=${email}&form=${form}&status=${status}&notify=1`;
        }
      });
    }
  </script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const params = new URLSearchParams(window.location.search);
    const notif = params.get("notif");

    if (notif === "approved") {
      Swal.fire({
        title: "Request Approved!",
        text: "The student has been notified via email.",
        icon: "success",
        confirmButtonColor: "#1b5e20"
      });
    } 
    else if (notif === "denied") {
      Swal.fire({
        title: "Request Denied!",
        text: "The student has been notified via email.",
        icon: "info",
        confirmButtonColor: "#1b5e20"
      });
    } 
    else if (notif === "failed") {
      Swal.fire({
        title: "Email Failed!",
        text: "The status was updated but the email could not be sent.",
        icon: "error",
        confirmButtonColor: "#d33"
      });
    }
  });
</script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  $(document).ready(function() {
    $('#studentsTable').DataTable({
        pageLength: 10,       
        lengthChange: false,    
        ordering: true,        
        order: [[3, 'desc']],  
        columnDefs: [
            { orderable: false, targets: [4] }
        ],
        language: {
            emptyTable: "No requests found." 
        }
    });
});
</script>
</body>

</html>