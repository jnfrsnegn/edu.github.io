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
  </style>
</head>
<body>

  <div class="header">Student Information Management System</div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3 sidebar">
        <div class="mb-4 d-flex align-items-center">
          <img src="lnhslogo.png" alt="Admin" class="avatar me-2">
          <div>
            <div style="font-size:14px;">Teacher</div>
            <small>Janferson Eugenio</small>
          </div>
        </div>

           <a href="addstud.php" class="btn btn-outline-light">Student Registration</a>
        <button class="btn btn-outline-light">Manage Informations</button>
        <button class="btn btn-outline-light">Grades Management</button>
        <button class="btn btn-outline-light">Personal Information</button>
        <button class="btn btn-outline-light">Password Management</button>
        <button class="btn btn-outline-light">Register Parents</button>
        <br><br>
        <a href="#" class="logout text-decoration-none"><i class="bi bi-box-arrow-left"></i> Logout</a>
      </div>

      <div class="col-md-9 p-4">
        
      </div>
    </div>
  </div>

</body>
</html>
