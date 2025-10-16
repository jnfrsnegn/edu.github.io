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
  $FirstName = ucwords(trim($_POST['FirstName']));
  $MiddleName = ucwords(trim($_POST['MiddleName']));
  $LastName = ucwords(trim($_POST['LastName']));
  $Suffix = trim($_POST['Suffix']);
  $Sex = $_POST['Sex'];
  $Birthdate = $_POST['Birthdate'];
  $EmployeeID = trim($_POST['EmployeeID']);
  $ContactNumber = trim($_POST['ContactNumber']);
  $EmailAddress=trim($_POST['EmailAddress']);
  $Address = trim($_POST['Address']);
  $Position = $_POST['Position'];

  $checkStmt = $conn->prepare("SELECT teachers_ID FROM teachers WHERE EmployeeID = ?");
  $checkStmt->bind_param("s", $EmployeeID);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();

  if ($checkResult->num_rows > 0) {
    $teacherIdError = "This Employee ID is already registered.";
  } else {
    $insertStmt = $conn->prepare("INSERT INTO teachers 
            (FirstName, MiddleName, LastName, Suffix, Sex, Birthdate, EmployeeID, ContactNumber,EmailAddress, Address, Position)
            VALUES (?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?)");
    $insertStmt->bind_param(
      "sssssssssss",
      $FirstName,
      $MiddleName,
      $LastName,
      $Suffix,
      $Sex,
      $Birthdate,
      $EmployeeID,
      $ContactNumber,
      $EmailAddress,
      $Address,
      $Position
    );

    if ($insertStmt->execute()) {
      $successMessage = "Teacher added successfully!";
    } else {
      $teacherIdError = "Database error: " . $insertStmt->error;
    }
    $insertStmt->close();
  }
  $checkStmt->close();
}
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

    h4.text-center {
      background-color: #0d4b16;
      border-radius: 25px;
      padding: 10px;
      width: 50%;
      color: #ffff;
      margin: 0 auto 20px;
    }

    .table-responsive {
      max-height: 400px;
      overflow-y: auto;
      overflow-x: auto;
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
    }

    .btn-outline-light {
      font-family: Arial, Helvetica, sans-serif;
    }

    .btn-icon {
      margin-right: 8px;
      width: 20px; 
    }

    @media (max-width: 992px) {
      .sidebar {
        height: auto;
        padding: 10px;
      }

      h4.text-center {
        width: 80%;
        font-size: 1.1rem;
      }

      .form-section {
        padding: 20px;
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
     
      .form-row .col-md-4 {
        width: 100%;
        margin-bottom: 10px;
      }

      .form-control {
        font-size: 16px; 
        padding: 12px 15px; 
      }

      .register-btn {
        padding: 12px 20px; 
        font-size: 16px;
      }

      .table-responsive {
        font-size: 0.85rem;
      }

      .table th,
      .table td {
        padding: 4px;
        white-space: nowrap;
      }
    }

    @media (max-width: 480px) {
      .header {
        font-size: 16px;
        padding: 10px;
      }

      .sidebar .btn {
        font-size: 14px;
      }

      .form-section {
        padding: 10px;
      }

      h4.text-center {
        width: 100%;
        font-size: 0.9rem;
        padding: 6px;
      }

      .form-control {
        font-size: 16px;
        margin-bottom: 12px;
        padding: 12px 12px;
      }

      .register-btn {
        font-size: 16px;
        padding: 12px 15px;
      }

      .table th,
      .table td {
        font-size: 12px;
        padding: 6px;
      }
    }

    @media (max-width: 320px) {
      .header {
        font-size: 15px;
        padding: 8px;
      }

      .sidebar {
        padding: 8px;
      }

      .sidebar .btn {
        font-size: 13px;
      }

      .form-section {
        padding: 8px;
      }

      h4.text-center {
        font-size: 0.85rem;
        padding: 5px;
      }

      .form-control {
        font-size: 16px;
        padding: 10px 10px;
        margin-bottom: 10px;
      }

      .register-btn {
        font-size: 15px;
        padding: 10px 12px;
      }

      .table th,
      .table td {
        font-size: 11px;
        padding: 4px;
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
          <a href="admindash.php" style="text-decoration: none;"><img src="lnhslogo.png" alt="Admin" class="avatar me-2"></a>
          <div>
            <div style="font-size:20px;">Administrator</div><small><?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?></small>
          </div>
        </div>

        <a href="#collapseStudent" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseStudent">
          <i class="bi bi-people-fill btn-icon"></i>Student Management
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseStudent">
          <a href="addstud.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person-plus btn-icon"></i>Add Student
          </a>
          <a href="docreqs.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-file-earmark-text btn-icon"></i>Document Requests
          </a>
          <a href="removeenrollee.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person-x btn-icon"></i>Student Status
          </a>
        </div>

        <a href="#collapseInfo" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseInfo">
          <i class="bi bi-info-circle-fill btn-icon"></i>Manage Informations
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseInfo">
          <a href="studinfo.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-people btn-icon"></i>Student Information
          </a>
          <a href="teachinfo.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person-badge btn-icon"></i>Teacher Information
          </a>
          <a href="persoinfo.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-person btn-icon"></i>Personal Information
          </a>
          <a href="passmanage.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-lock btn-icon"></i>Password Management
          </a>
        </div>

        <a href="#collapseTeacher" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseTeacher">
          <i class="bi bi-person-badge-fill btn-icon"></i>Teacher Management
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse show" id="collapseTeacher">
          <a href="regteach.php" class="btn btn-outline-light sub-btn active">
            <i class="bi bi-person-plus btn-icon"></i>Register Teachers
          </a>
          <a href="assignteacher.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-arrow-right-circle btn-icon"></i>Assign Teacher
          </a>
        </div>

        <a href="#collapseAcademic" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseAcademic">
          <i class="bi bi-journal-bookmark-fill btn-icon"></i>Subjects & Sections
          <i class="bi bi-chevron-right"></i>
        </a>
        <div class="collapse" id="collapseAcademic">
          <a href="addsubject.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-journal-plus btn-icon"></i>Add Subject
          </a>
          <a href="managesections.php" class="btn btn-outline-light sub-btn">
            <i class="bi bi-gear btn-icon"></i>Manage Sections
          </a>
        </div>

        <a href="viewrep.php" class="btn btn-outline-light">
          <i class="bi bi-bar-chart-fill btn-icon"></i>View Reports
        </a>

        <br><br>
         <a href="#" class="logout text-decoration-none" id="logoutBtn">
    <i class="bi bi-box-arrow-right me-2"></i>Logout
</a>
      </div>

      <div class="col-md-9 p-3">
        <div class="form-section">
          <h4 class="text-center mb-4">Register Teacher</h4>

          <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success text-center"><?= htmlspecialchars($successMessage) ?></div>
          <?php endif; ?>

          <form method="post" action="regteach.php" autocomplete="off" class="form-row">
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
                <?php if ($teacherIdError): ?><div class="invalid-feedback"><?= htmlspecialchars($teacherIdError) ?></div><?php endif; ?>
              </div>
              <div class="col-md-4"><input type="text" name="ContactNumber" maxlength="11" pattern="\d{11}" inputmode="numeric" class="form-control" placeholder="Contact Number" required></div>
              <div class="col-12 col-md-4"><input type="email" name="EmailAddress" class="form-control"
    placeholder="name@gmail.com" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
    required>
</div>
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
              <div class="col-12 text-center">
                <button type="submit" class="btn register-btn mt-2">REGISTER</button>
              </div>
            </div>
          </form>

          <div class="table-responsive mt-4">
            <table id="teachersTable" class="table table-bordered table-striped text-center">
              <thead>
                <tr>
                  <th>Employee ID</th>
                  <th>First Name</th>
                  <th>Middle Name</th>
                  <th>Last Name</th>
                  <th>Position</th>
                  <th>Sex</th>
                  <th>Contact</th>
                  <th>EmailAddress</th>
                  <th>Address</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT * FROM teachers ORDER BY teachers_ID DESC";
                $result = mysqli_query($conn, $sql);
                if ($result && mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                          <td>" . htmlspecialchars($row['EmployeeID']) . "</td>
                          <td>" . htmlspecialchars($row['FirstName']) . "</td>
                          <td>" . htmlspecialchars($row['MiddleName']) . "</td>
                          <td>" . htmlspecialchars($row['LastName']) . "</td>
                          <td>" . htmlspecialchars($row['Position']) . "</td>
                          <td>" . htmlspecialchars($row['Sex']) . "</td>
                          <td>" . htmlspecialchars($row['ContactNumber']) . "</td>
                          <td>" . htmlspecialchars($row['EmailAddress']) . "</td>
                          <td>" . htmlspecialchars($row['Address']) . "</td>
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
        $('#teachersTable').DataTable({
    "pageLength": 10,
    "lengthChange": false,
    "ordering": true,
    "order": [[0, "asc"]], 
    "columnDefs": [
        { "orderable": false, "targets": [8] } 
    ]
});

    });
</script>
</body>

</html>