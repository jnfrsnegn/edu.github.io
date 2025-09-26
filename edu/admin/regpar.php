<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['admin'])) {
  header("Location: adminlogin.php");
  exit();
}

$successMessage = '';
$errorMessage = '';

if (isset($_POST['ajax'])) {
  if ($_POST['ajax'] === "validateContact") {
    $contact = $_POST['contact'];
    $check = $conn->prepare("SELECT parents_ID FROM parents WHERE ContactNumber=?");
    $check->bind_param("s", $contact);
    $check->execute();
    $result = $check->get_result();
    echo $result->num_rows > 0 ? "duplicate" : "ok";
    exit;
  }

  if ($_POST['ajax'] === "validateLRN") {
    $lrn = $_POST['lrn'];
    $stmt = $conn->prepare("SELECT students_ID FROM students WHERE LRN=?");
    $stmt->bind_param("s", $lrn);
    $stmt->execute();
    $result = $stmt->get_result();
    echo $result->num_rows > 0 ? "valid" : "invalid";
    exit;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax'])) {
  $FirstName = $_POST['FirstName'];
  $MiddleName = $_POST['MiddleName'];
  $LastName = $_POST['LastName'];
  $Sex = $_POST['Sex'];
  $Birthdate = $_POST['Birthdate'];
  $ContactNumber = $_POST['ContactNumber'];
  $Address = $_POST['Address'];
  $ChildLRNs = $_POST['ChildLRN'];

  $check = $conn->prepare("SELECT parents_ID FROM parents WHERE ContactNumber=?");
  $check->bind_param("s", $ContactNumber);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    $parent = $result->fetch_assoc();
    $parent_id = $parent['parents_ID'];
  } else {
    $sql = "INSERT INTO parents (FirstName, MiddleName, LastName, Sex, Birthdate, ContactNumber, Address) 
            VALUES (?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $FirstName, $MiddleName, $LastName, $Sex, $Birthdate, $ContactNumber, $Address);
    $stmt->execute();
    $parent_id = $stmt->insert_id;
  }

  $successCount = 0;
  foreach ($ChildLRNs as $ChildLRN) {
    if (trim($ChildLRN) == '') continue;

    $stmt = $conn->prepare("SELECT students_ID FROM students WHERE LRN=?");
    $stmt->bind_param("s", $ChildLRN);
    $stmt->execute();
    $studentResult = $stmt->get_result();

    if ($studentResult->num_rows > 0) {
      $student = $studentResult->fetch_assoc();
      $student_id = $student['students_ID'];

      $stmt = $conn->prepare("INSERT INTO parents_students (parents_ID, students_ID, Status) VALUES (?, ?, 'Pending')");
      $stmt->bind_param("ii", $parent_id, $student_id);
      if ($stmt->execute()) $successCount++;
    } else {
      $errorMessage = "Child LRN $ChildLRN is not registered in the system.";
    }
  }

  if ($successCount > 0) {
    $successMessage = "Parent registered! ($successCount request(s) pending student approval)";
  } elseif (!$errorMessage) {
    $errorMessage = "No valid child LRNs found.";
  }
}

$filter = $_GET['filter'] ?? 'all';

$sql = "SELECT p.parents_ID, p.FirstName, p.MiddleName, p.LastName, p.Sex, p.Birthdate, p.ContactNumber, p.Address, ps.Status,
        GROUP_CONCAT(CONCAT(s.FirstName,' ',s.MiddleName,' ',s.LastName) SEPARATOR ', ') AS Children
        FROM parents p
        LEFT JOIN parents_students ps ON p.parents_ID = ps.parents_ID
        LEFT JOIN students s ON ps.students_ID = s.students_ID";

if ($filter === 'approved') {
  $sql .= " WHERE ps.Status='Approved'";
} elseif ($filter === 'rejected') {
  $sql .= " WHERE ps.Status='Rejected'";
}

$sql .= " GROUP BY p.parents_ID ORDER BY p.parents_ID DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>SIMS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
      background-color: #a8aa10ff;
    }

    .invalid {
      border: 2px solid red;
      background: #ffe5e5;
    }

    .table-responsive {
      max-height: 450px;
      overflow-y: auto;
      overflow-x: auto;
    }

    table {
      min-width: 1000px;
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
          <a href="admindash.php" style="text-decoration:none;">
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
                <a href="regpar.php" class="btn btn-outline-light active">Register Parents</a>
                <a href="addsubject.php" class="btn btn-outline-light">Add Subject</a>
                <a href="managesections.php" class="btn btn-outline-light ">Manage Sections</a>
                <br><br>
        <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
      </div>

      <div class="col-12 col-md-9 p-4">
        <div class="form-section">
          <?php if ($successMessage): ?>
            <script>
              alert("<?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>");
            </script>
          <?php endif; ?>
          <?php if ($errorMessage): ?>
            <script>
              alert("<?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>");
            </script>
          <?php endif; ?>

  
          <form method="post" action="regpar.php" autocomplete="off">
            <div class="row">
              <div class="col-md-4"><input type="text" name="FirstName" class="form-control" placeholder="First Name" required></div>
              <div class="col-md-4"><input type="text" name="MiddleName" class="form-control" placeholder="Middle Name"></div>
              <div class="col-md-4"><input type="text" name="LastName" class="form-control" placeholder="Last Name" required></div>
              <div class="col-md-4">
                <select name="Sex" class="form-control" required>
                  <option value="" disabled selected>Select Sex</option>
                  <option>Male</option>
                  <option>Female</option>
                </select>
              </div>
              <div class="col-md-4"><input type="date" name="Birthdate" class="form-control" required></div>
              <div class="col-md-4"><input type="text" name="ContactNumber" maxlength="11" pattern="\d{11}" class="form-control" placeholder="Contact Number" required></div>
              <div class="col-12"><input type="text" name="Address" class="form-control" placeholder="Address" required></div>

              <div class="col-12 mt-3">
                <h5>Children (Enter LRN)</h5>
                <div id="childFields">
                  <div class="row mb-2 child-row">
                    <div class="col-8">
                      <input type="text" name="ChildLRN[]" maxlength="12" pattern="\d{12}" class="form-control child-input" placeholder="Enter Child LRN" required>
                    </div>
                    <div class="col-4">
                      <button type="button" class="btn btn-success" onclick="addChild()">+ Add Another Child</button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 text-center mt-3">
                <button type="submit" class="btn register-btn">REGISTER</button>
              </div>
            </div>
          </form>

          <hr>

          <div class="mb-2">
            <label>Filter:</label>
            <select id="filterParent" class="form-select w-auto">
              <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Show All Parents</option>
              <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>Approved</option>
              <option value="rejected" <?= $filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
          </div>

  
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Sex</th>
                  <th>Birthdate</th>
                  <th>Contact</th>
                  <th>Address</th>
                  <th>Children</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result->num_rows > 0): ?>
                  <?php $i = 1;
                  while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= $i++ ?></td>
                      <td><?= htmlspecialchars($row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['LastName']) ?></td>
                      <td><?= htmlspecialchars($row['Sex']) ?></td>
                      <td><?= htmlspecialchars($row['Birthdate']) ?></td>
                      <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
                      <td><?= htmlspecialchars($row['Address']) ?></td>
                      <td><?= htmlspecialchars($row['Children'] ?? '-') ?></td>
                      <td><?= htmlspecialchars($row['Status'] ?? 'Pending') ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center">No parents found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <script>
            function addChild() {
              let html = `<div class="row mb-2">
              <div class="col-8">
                <input type="text" maxlength="12" pattern="\\d{12}" inputmode="numeric" 
                       name="ChildLRN[]" class="form-control" placeholder="Enter Child LRN" required>
              </div>
              <div class="col-4">
                <button type="button" class="btn btn-danger" onclick="this.parentElement.parentElement.remove()">Remove</button>
              </div>
            </div>`;
              $("#childFields").append(html);
            }

            $("#filterParent").change(function() {
              let filter = $(this).val();
              window.location.href = "regpar.php?filter=" + filter;
            });

            $(document).ready(function() {
              $("form").on("submit", function(e) {
                let contact = $("input[name='ContactNumber']").val().trim();
                let lrns = $("input[name='ChildLRN[]']").map(function() {
                  return $(this).val().trim();
                }).get().filter(lrn => lrn.length > 0);
                if (lrns.length === 0) {
                  alert("Please enter at least one child LRN.");
                  return false;
                }

                e.preventDefault();
                let form = this;

                $.post("regpar.php", {
                  ajax: "validateContact",
                  contact: contact
                }, function(data) {
                  if (data === "duplicate") {
                    alert("This contact number is already registered in the system.");
                    $("input[name='ContactNumber']").addClass("invalid").focus();
                    return false;
                  } else {
                    $("input[name='ContactNumber']").removeClass("invalid");
                    let invalidLRN = null,
                      checkCount = 0;
                    lrns.forEach(function(lrn) {
                      $.post("regpar.php", {
                        ajax: "validateLRN",
                        lrn: lrn
                      }, function(res) {
                        checkCount++;
                        if (res !== "valid" && !invalidLRN) invalidLRN = lrn;
                        if (checkCount === lrns.length) {
                          if (invalidLRN) {
                            alert("The LRN " + invalidLRN + " is not registered in the system.");
                            $("input[name='ChildLRN[]']").each(function() {
                              if ($(this).val().trim() === invalidLRN) $(this).addClass("invalid").focus();
                            });
                          } else {
                            form.submit();
                          }
                        }
                      });
                    });
                  }
                });
              });
            });
          </script>

        </div>
      </div>
    </div>
  </div>
</body>

</html>