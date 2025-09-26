<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['students_ID'])) {
  header("Location: studentlogin.php");
  exit();
}

$student_id = $_SESSION['students_ID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
  $request_id = intval($_POST['request_id']);
  $action = $_POST['action'];

  if (in_array($action, ['Approved', 'Rejected'])) {
    $stmt = $conn->prepare("UPDATE parents_students SET Status=? WHERE id=? AND students_ID=?");
    $stmt->bind_param("sii", $action, $request_id, $student_id);
    $stmt->execute();
  }


  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}


$sql = "SELECT pr.id, p.FirstName AS ParentFirst, p.MiddleName AS ParentMiddle, 
               p.LastName AS ParentLast, p.ContactNumber,
               COALESCE(pr.Status, 'Pending') AS Status
        FROM parents_students pr
        JOIN parents p ON pr.parents_ID = p.parents_ID
        WHERE pr.students_ID = ?
        ORDER BY pr.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>SIMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f5dc;
      overflow: hidden;
    }

    .avatar {
      width: 70px;
      height: 70px;
      border-radius: 50%;
    }

    .header {
      background-color: #1b5e20;
      color: white;
      text-align: center;
      padding: 15px;
      font-size: 24px;
      font-weight: bold;
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
      border-radius: 10px;
      padding: 30px;
    }
    .btn-outline-light {
            font-family: Arial, Helvetica, sans-serif;
        }
  </style>
</head>

<body>
  <div class="header">Student Information Management System</div>

  <div class="container-fluid">
    <div class="row flex-column flex-md-row">

      <div class="col-12 col-md-3 sidebar">
        <div class="mb-4 d-flex align-items-center">
          <a href="studentdash.php" style="text-decoration: none;">
            <img src="lnhslogo.png" alt="Student" class="avatar me-2">
          </a>
          <div>
            <div style="font-size:25px;">Student</div>
            <small><?= $_SESSION['student_name'] ?? '' ?></small>
          </div>
        </div>

        <a href="viewgrades.php" class="btn btn-outline-light">View Grades</a>
        <a href="persoinfo.php" class="btn btn-outline-light">Personal Information</a>
        <a href="reqdocs.php" class="btn btn-outline-light">Request Form</a>
        <a href="parentreq.php" class="btn btn-outline-light active">Parent Request</a>
        <a href="passmanage.php" class="btn btn-outline-light">Password Management</a>
        <br><br>
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
      </div>


      <div class="col-12 col-md-9 p-4">
        <div class="form-section">
          <h3 class="mb-3">Parent Requests</h3>

          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Parent Name</th>
                  <th>Contact</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result->num_rows > 0): ?>
                  <?php $i = 1;
                  while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= $i++ ?></td>
                      <td><?= htmlspecialchars($row['ParentFirst'] . ' ' . $row['ParentMiddle'] . ' ' . $row['ParentLast']) ?></td>
                      <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
                      <td>
                        <?php
                        $status = $row['Status'];
                        if ($status === 'Pending'): ?>
                          <form method="post" class="d-inline">
                            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="action" value="Approved" class="btn btn-warning btn-sm me-1" onclick="return confirm('Approve this request?')">Approve</button>
                            <button type="submit" name="action" value="Rejected" class="btn btn-danger btn-sm" onclick="return confirm('Reject this request?')">Reject</button>
                          </form>
                        <?php else: ?>
                          <?php if ($status === 'Approved'): ?>
                            <span class="badge bg-warning">Approved</span>
                          <?php elseif ($status === 'Rejected'): ?>
                            <span class="badge bg-danger">Rejected</span>
                          <?php endif; ?>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="text-center">No parent requests found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
</body>

</html>