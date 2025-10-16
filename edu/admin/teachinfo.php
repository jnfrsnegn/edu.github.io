<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

$searchError = '';
$teacherFound = [];
$allTeachers = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search = htmlspecialchars($_POST['search']);
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE EmployeeID = ?");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $teacherFound[] = $row;
        }
    } else {
        $searchError = "Teacher ID not found.";
    }
    $stmt->close();
}

$allQuery = "SELECT * FROM teachers";
$allTeachers = mysqli_query($conn, $allQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SIMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

.search-form {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0;
  margin: 25px 0;
}

.search-form .input-group {
  display: flex;
  align-items: stretch;
  justify-content: center;
  max-width: 500px;
  width: 100%;
}

.search-form .form-control {
  border-radius: 25px 0 0 25px;
  border: 1px solid #ccc;
  padding: 10px 18px;
  font-size: 15px;
  height: 46px; 
  outline: none;
}

.search-form .search-btn {
  border-radius: 0 25px 25px 0;
  background-color: #124820;
  color: white;
  font-weight: bold;
  padding: 0 25px;
  height: 46px; 
  border: none;
  transition: background-color 0.3s ease;
}

.search-form .search-btn:hover {
  background-color: #a8aa10ff;
  color: black;
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

.table-responsive {
  max-height: 450px;
  overflow-y: auto;
  overflow-x: auto;
}

.edit-btn {
  background-color: #124820;
  color: white;
  border-radius: 25px;
  padding: 6px 20px;
  font-weight: bold;
  text-decoration: none;
}

.edit-btn:hover {
  background-color: #a8aa10ff;
  color: black;
}

h4.text-center {
  background-color: #0d4b16;
  border-radius: 25px;
  padding: 10px;
  width: 50%;
  color: #ffff;
  margin: 0 auto;
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

  .input-group {
    max-width: 100%;
  }
}

@media (max-width: 768px) {
  .sidebar {
    position: relative;
    height: auto;
    width: 100%;
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

  .search-form {
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }

  .search-form .input-group {
    flex-direction: column;
    width: 100%;
    max-width: 100%;
  }

  .search-form .form-control,
  .search-form .search-btn {
    border-radius: 25px;
    width: 100%;
    height: auto;
    padding: 12px 20px;
  }

  .table-responsive table {
    font-size: 0.85rem;
  }

  .table th,
  .table td {
    padding: 4px;
    white-space: nowrap;
  }

  .edit-btn {
    padding: 4px 12px;
    font-size: 0.9rem;
    display: block;
    width: 100%;
    margin-bottom: 5px;
  }

  h4.text-center {
    width: 90%;
    padding: 8px;
    font-size: 1rem;
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

  .form-control {
    font-size: 14px;
  }

  .search-btn {
    font-size: 14px;
  }

  td,
  th {
    font-size: 12px;
    padding: 6px;
  }

  .edit-btn {
    font-size: 0.85rem;
    padding: 6px 10px;
  }

  h4.text-center {
    width: 100%;
    font-size: 0.9rem;
    padding: 6px;
  }

  .search-form .form-control,
  .search-form .search-btn {
    font-size: 14px;
    padding: 10px 20px;
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

  .form-control {
    font-size: 13px;
    padding: 8px;
  }

  .search-btn {
    font-size: 13px;
    padding: 8px 15px;
  }

  td,
  th {
    font-size: 11px;
    padding: 4px;
  }

  .edit-btn {
    font-size: 0.8rem;
    padding: 4px 8px;
  }

  h4.text-center {
    font-size: 0.85rem;
    padding: 5px;
  }

  .search-form .form-control,
  .search-form .search-btn {
    font-size: 12px;
    padding: 10px 15px;
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
          <div style="font-size:20px;">Administrator</div>
          <small><?= $_SESSION['admin_name'] ?? '' ?></small>
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
      <a href="#collapseInfo" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseInfo">
        <i class="bi bi-info-circle-fill btn-icon"></i>Manage Informations
        <i class="bi bi-chevron-right"></i>
      </a>
      <div class="collapse show" id="collapseInfo">
        <a href="studinfo.php" class="btn btn-outline-light sub-btn">
          <i class="bi bi-people btn-icon"></i>Student Information
        </a>
        <a href="teachinfo.php" class="btn btn-outline-light sub-btn active">
          <i class="bi bi-person-badge btn-icon"></i>Teacher Information
        </a>
        <a href="persoinfo.php" class="btn btn-outline-light sub-btn">   
          <i class="bi bi-person btn-icon"></i>Personal Information
        </a>
        <a href="passmanage.php" class="btn btn-outline-light sub-btn">
          <i class="bi bi-lock btn-icon"></i>Password Management
        </a>
      </div>
      <a href="#collapseTeacher" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseTeacher">
        <i class="bi bi-person-badge-fill btn-icon"></i>Teacher Management
        <i class="bi bi-chevron-right"></i>
      </a>
      <div class="collapse" id="collapseTeacher">
        <a href="regteach.php" class="btn btn-outline-light sub-btn">
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
        <h4 class="mb-4 text-center">Teacher Information</h4>
        <form method="POST" class="search-form">
  <div class="input-group">
    <input type="text" name="search" class="form-control" placeholder="Enter Teacher ID">
    <button type="submit" class="search-btn">
      <i class="fas fa-search"></i>
    </button>
  </div>
</form>

        <?php if (!empty($searchError)): ?>
          <div class="alert alert-danger mt-4 text-center col-md-6 mx-auto" role="alert">
            <?= $searchError ?>
          </div>
        <?php elseif (!empty($teacherFound)): ?>
          <div class="table-responsive mt-5">
            <table id="searchTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>EmployeeID</th>
                  <th>FirstName</th>
                  <th>MiddleName</th>
                  <th>LastName</th>
                  <th>Suffix</th>
                  <th>Sex</th>
                  <th>Birthdate</th>
                  <th>Position</th>
                  <th>ContactNumber</th>
                  <th>EmailAddress</th>
                  <th>Address</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($teacherFound as $row): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['EmployeeID']) ?></td>
                    <td><?= htmlspecialchars($row['FirstName']) ?></td>
                    <td><?= htmlspecialchars($row['MiddleName']) ?></td>
                    <td><?= htmlspecialchars($row['LastName']) ?></td>
                    <td><?= htmlspecialchars($row['Suffix']) ?></td>
                    <td><?= htmlspecialchars($row['Sex']) ?></td>
                    <td><?= htmlspecialchars($row['Birthdate']) ?></td>
                    <td><?= htmlspecialchars($row['Position']) ?></td>
                    <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
                    <td><?= htmlspecialchars($row['EmailAddress']) ?></td>
                    <td><?= htmlspecialchars($row['Address']) ?></td>
                    <td>
                      <a href="editteacher.php?tid=<?= urlencode($row['teachers_ID']) ?>" class="edit-btn">EDIT</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php elseif ($allTeachers && mysqli_num_rows($allTeachers) > 0): ?>
          <div class="table-responsive mt-5">
            <table id="allTeachersTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>EmployeeID</th>
                  <th>FirstName</th>
                  <th>MiddleName</th>
                  <th>LastName</th>
                  <th>Suffix</th>
                  <th>Sex</th>
                  <th>Birthdate</th>
                  <th>Position</th>
                  <th>ContactNumber</th>
                  <th>EmailAddress</th>
                  <th>Address</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = mysqli_fetch_assoc($allTeachers)): ?>
                  <tr>
                   <td><?= htmlspecialchars($row['EmployeeID']) ?></td>
                    <td><?= htmlspecialchars($row['FirstName']) ?></td>
                    <td><?= htmlspecialchars($row['MiddleName']) ?></td>
                    <td><?= htmlspecialchars($row['LastName']) ?></td>
                    <td><?= htmlspecialchars($row['Suffix']) ?></td>
                    <td><?= htmlspecialchars($row['Sex']) ?></td>
                    <td><?= htmlspecialchars($row['Birthdate']) ?></td>
                    <td><?= htmlspecialchars($row['Position']) ?></td>
                    <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
                    <td><?= htmlspecialchars($row['EmailAddress']) ?></td>
                    <td><?= htmlspecialchars($row['Address']) ?></td>
                    <td>
                      <a href="editteacher.php?tid=<?= urlencode($row['teachers_ID']) ?>" class="edit-btn btn-sm">EDIT</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="alert alert-info mt-4 text-center">No teachers registered yet.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
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
    </script>
    <script>
    $(document).ready(function() {
        $('#searchTable,#allTeachersTable').DataTable({
    "pageLength": 10,
    "lengthChange": false,
    "ordering": true,
    "order": [[0, "asc"]], 
    "columnDefs": [
        { "orderable": false, "targets": [11] } 
    ]
});

    });
</script>
</body>
</html>