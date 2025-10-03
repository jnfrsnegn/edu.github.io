
<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
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
      height: auto;

      display: flex;
      justify-content: center;
      align-items: center;
    }

    .form-control {
      border-radius: 20px;
      margin-bottom: 15px;
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
      width: 65px;
      height: 65px;
      border-radius: 50%;
    }

    .card {
      background-color: #8f913aff;
      border-radius: 15px;
      cursor: pointer;
      transition: transform 0.2s ease-in-out;
      min-height: 500px; 
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 110px;
    }

    .card:hover {
      transform: scale(1.03);
    }

    .card a {
      color: inherit;
      text-decoration: none;
    }

    .card a:hover {
      text-decoration: none;
    }

    .card-icon-wrapper {
      background-color: #f3f3f3;
      width: 120px;
      height: 120px;
      border-radius: 50%;
      margin: 0 auto 20px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card-body {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .card-title {
      margin-top: 10px;
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
        <div class="form-section">
          <div class="row justify-content-center text-center w-100">
            <div class="col-md-4 mb-4 d-flex justify-content-center">
              <a href="teachinfo.php" class="w-100 text-decoration-none">
                <div class="card shadow">
                  <div class="card-body">
                    <div class="card-icon-wrapper">
                      <img src="avatar.png" alt="Teacher Icon" style="width: 70px;">
                    </div>
                    <h5 class="card-title text-white">Teacher Information</h5>
                  </div>
                </div>
              </a>
            </div>

            <div class="col-md-4 mb-4 d-flex justify-content-center">
              <a href="studinfo.php" class="w-100 text-decoration-none">
                <div class="card shadow">
                  <div class="card-body">
                    <div class="card-icon-wrapper">
                      <img src="avatar.png" alt="Student Icon" style="width: 70px;">
                    </div>
                    <h5 class="card-title text-white">Student Information</h5>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</body>
</html>
