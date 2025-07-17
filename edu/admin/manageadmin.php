
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
        <button class="btn btn-outline-light">Manage Informations</button>
        <a href="docreqs.php" class="btn btn-outline-light">Document Requests</a>
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
    <div class="row mt-4 justify-content-center">
  <div class="col-md-4 mb-3">
    <a href="teachinfo.php" style="text-decoration: none;">
    <div class="card text-center shadow" style="background-color: #4a6c7c; border-radius: 15px; cursor: pointer;" name="teacher">
      <div class="card-body">
        <div style="background-color: #f3f3f3; width: 120px; height: 120px; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
          <img src="avatar.png" alt="Teacher Icon" style="width: 70px;">
        </div>
        <h5 class="card-title text-white">Teacher Information</h5>
      </div>
    </div>
    </a>
  </div>
         <a href="studinfo.php" style="text-decoration: none;">
  <div class="col-md-4 mb-3">
  
    <div class="card text-center shadow" style="background-color: #4a6c7c; border-radius: 15px; cursor: pointer; ">
      <div class="card-body">
        <div style="background-color: #f3f3f3; width: 120px; height: 120px; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
          <img src="avatar.png" alt="Student Icon" style="width: 70px;">
        </div>
        <h5 class="card-title text-white">Student Information</h5>
      </div>
    </div>
   </a>
  </div>

  <div class="col-md-4 mb-3">
    <div class="card text-center shadow" style="background-color: #4a6c7c; border-radius: 15px; cursor: pointer;">
      <div class="card-body">
        <div style="background-color: #f3f3f3; width: 120px; height: 120px; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
          <img src="avatar.png" alt="Parent Icon" style="width: 70px;">
        </div>
        <h5 class="card-title text-white">Parent Information</h5>
      </div>
    </div>
  </div>
</div>


  </div>
</div>

</div>
</body>
</html>
