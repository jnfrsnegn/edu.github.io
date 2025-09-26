<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: adminlogin.php");
  exit();
}
$jhsQuery = "SELECT COUNT(*) AS total_jhs FROM students 
             LEFT JOIN yearlevels ON students.YearLevelID = yearlevels.yearlevel_ID 
             WHERE YearName IN ('Grade 7', 'Grade 8', 'Grade 9', 'Grade 10')";
$jhsResult = mysqli_query($conn, $jhsQuery);
$jhsCount = ($jhsResult && mysqli_num_rows($jhsResult) > 0) ? mysqli_fetch_assoc($jhsResult)['total_jhs'] : 0;

$shsQuery = "SELECT COUNT(*) AS total_shs FROM students 
             LEFT JOIN yearlevels ON students.YearLevelID = yearlevels.yearlevel_ID 
             WHERE YearName IN ('Grade 11', 'Grade 12')";
$shsResult = mysqli_query($conn, $shsQuery);
$shsCount = ($shsResult && mysqli_num_rows($shsResult) > 0) ? mysqli_fetch_assoc($shsResult)['total_shs'] : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>SIMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    .form-section {
      background-color: #fffde7;
      padding: 30px;
      border-radius: 10px;
      height: auto;
    }

    .avatar {
      width: 70px;
      height: 70px;
      border-radius: 50%;
    }

    .text-center {
      padding-top: 30px
    }

    .btn-outline-light {
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
          <img src="lnhslogo.png" alt="Admin" class="avatar me-2">
          <div>
            <div style="font-size:25px;">Administrator</div>
            <small><?= $_SESSION['admin_name'] ?? '' ?></small>
          </div>
        </div>

        <a href="addstud.php" class="btn btn-outline-light">Student Registration</a>
        <a href="manageadmin.php" class="btn btn-outline-light">Manage Informations</a>
        <a href="docreqs.php" class="btn btn-outline-light">Document Requests</a>
        <a href="removeenrollee.php" class="btn btn-outline-light">Remove Enrollee</a>
        <a href="persoinfo.php" class="btn btn-outline-light">Personal Information</a>
        <a href="viewrep.php" class="btn btn-outline-light">View Reports</a>
        <a href="passmanage.php" class="btn btn-outline-light">Password Management</a>
        <a href="regteach.php" class="btn btn-outline-light">Register Teachers</a>
        <a href="assignteacher.php" class="btn btn-outline-light">Assign Teacher</a>
        <a href="regpar.php" class="btn btn-outline-light">Register Parents</a>
        <a href="addsubject.php" class="btn btn-outline-light">Add Subject</a>
        <a href="managesections.php" class="btn btn-outline-light ">Manage Sections</a>
        <br><br>
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
      </div>

      <div class="col-md-9 p-4">
        <div class="form-section" style="overflow: hidden;">
          <h4 class="text-center mb-4 fw-bold">Enrollee graph</h4>
          <br><br>
          <canvas id="enrollmentChart" height="100" ></canvas>

          <script>
            const ctx = document.getElementById('enrollmentChart').getContext('2d');
            const enrollmentChart = new Chart(ctx, {
              type: 'bar',
              data: {
                labels: ['Junir High School', 'Senior High School'],
                datasets: [{
                  label: 'Total Enrollee/s',
                  data: [<?= $jhsCount ?>, <?= $shsCount ?>],
                  backgroundColor: ['#4caf50', '#fbc02d'],
                  borderColor: ['#388e3c', '#f9a825'],
                  borderWidth: 1
                }]
              },
              options: {
                responsive: true,
                scales: {
                  y: {
                    beginAtZero: true,
                    ticks: {
                      stepSize: 1
                    }
                  }
                }
              }
            });
          </script>
        </div>
      </div>
    </div>
  </div>

</body>

</html>