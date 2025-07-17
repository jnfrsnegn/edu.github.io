
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }

    th {
      background-color: #1b5e20;
      color: white;
    }

    td, th {
      padding: 8px;
      text-align: center;
    }

    table {
      margin-top: 20px;
    }
  </style>
</head>
<body style="overflow: hidden;">

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

        <script>
          function confirmLogout() {
            return confirm("Are you sure you want to log out?");
          }
        </script>
      </div>
          <div class="col-md-9 p-4">
  <div class="form-section">
    <div class="row mt-4">
      <input type="text" name="search">Search Parent ID
    </div>

  </div>
</div>

</div>
</body>
</html>
