<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}
$searchError = '';
$parentFound = [];
$allParents = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $search = htmlspecialchars($_POST['search']);

  $query = "SELECT * FROM parents WHERE ContactNumber = '$search'";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)){
      $parentFound[] = $row;
    }
  } else {
    $searchError = "Parent contact not found.";
  }
} else {
  $allQuery = "SELECT * FROM parents";
  $allParents = mysqli_query($conn, $allQuery);
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
      overflow: hidden;
    }
    .sidebar {
      background-color: #0d4b16;
      min-height: 100vh;
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
    .search-btn {
      background-color: #124820;
      color: white;
      border-radius: 25px;
      padding: 10px 30px;
      font-weight: bold;
      width: 400px;
    }
    .search-btn:hover {
      background-color: #a8aa10ff;
    }
    .edit-btn {
      background-color: #124820;
      color: white;
      border-radius: 25px;
      padding: 5px 15px;
      font-weight: bold;
      text-decoration: none;
      display: inline-block;
      text-align: center;
      font-size: 14px;
    }
    .edit-btn:hover {
      background-color: #a8aa10ff;
      color: black;
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
    td, th {
      text-align: center;
      vertical-align: middle;
    }
    .table-responsive {
      max-height: 450px;
      overflow-y: auto;
    }
    h4.text-center{
      background-color: #0d4b16;
      border-radius: 25px;
      padding:10px;
      width:50%;
      color:#ffff;
      margin: 0 auto;
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
        <a href="admindash.php" style="text-decoration: none;">
          <img src="lnhslogo.png" alt="Admin" class="avatar me-2">
        </a>
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
      <div class="form-section" style="height:auto;">
        <h4 class="mb-4 text-center">Parent Information</h4>

        <form method="POST" action="#" class="d-flex flex-column align-items-center">
          <div class="col-12 col-md-6">
            <input type="text" name="search" maxlength="11" pattern="\d{11}" class="form-control text-center" placeholder="Enter Contact Number" required>
          </div>
          <div class="mt-3">
            <button type="submit" class="btn search-btn mt-2">SEARCH</button>
          </div>
        </form>

        <?php if (!empty($searchError)): ?>
          <div class="alert alert-danger mt-4 text-center col-md-6 mx-auto" role="alert">
            <?= $searchError ?>
          </div>

        <?php elseif (!empty($parentFound)): ?>
          <div class="table-responsive mt-5">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>First Name</th>
                  <th>Middle Name</th>
                  <th>Last Name</th>
                  <th>Sex</th>
                  <th>Birthdate</th>
                  <th>Contact</th>
                  <th>Address</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($parentFound as $row): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['parents_ID']) ?></td>
                    <td><?= htmlspecialchars($row['FirstName']) ?></td>
                    <td><?= htmlspecialchars($row['MiddleName']) ?></td>
                    <td><?= htmlspecialchars($row['LastName']) ?></td>
                    <td><?= htmlspecialchars($row['Sex']) ?></td>
                    <td><?= htmlspecialchars($row['Birthdate']) ?></td>
                    <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
                    <td><?= htmlspecialchars($row['Address']) ?></td>
                    <td>
                      <a href="editpar.php?pid=<?= urlencode($row['parents_ID']) ?>" class="edit-btn">EDIT</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

        <?php elseif ($allParents && mysqli_num_rows($allParents) > 0): ?>
          <div class="table-responsive mt-5">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>First Name</th>
                  <th>Middle Name</th>
                  <th>Last Name</th>
                  <th>Sex</th>
                  <th>Birthdate</th>
                  <th>Contact</th>
                  <th>Address</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = mysqli_fetch_assoc($allParents)): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['parents_ID']) ?></td>
                    <td><?= htmlspecialchars($row['FirstName']) ?></td>
                    <td><?= htmlspecialchars($row['MiddleName']) ?></td>
                    <td><?= htmlspecialchars($row['LastName']) ?></td>
                    <td><?= htmlspecialchars($row['Sex']) ?></td>
                    <td><?= htmlspecialchars($row['Birthdate']) ?></td>
                    <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
                    <td><?= htmlspecialchars($row['Address']) ?></td>
                    <td>
                      <a href="editpar.php?pid=<?= urlencode($row['parents_ID']) ?>" class="edit-btn">EDIT</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

</body>
</html>
