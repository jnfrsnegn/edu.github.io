<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacherlogin.php");
    exit();
}

$parentID = $_GET['pid'] ?? null;
$parent = null;
$error = '';
$alertMessage = '';

if (!$parentID) {
    header("Location: parentinfoteach.php");
    exit;
}


$query = "SELECT * FROM parents WHERE parents_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $parentID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $parent = $result->fetch_assoc();
} else {
    $error = "Parent not found.";
}


$childrenQuery = "
    SELECT s.LRN, s.FirstName, s.MiddleName, s.LastName, ps.Status
    FROM parents_students ps
    INNER JOIN students s ON ps.students_ID = s.students_ID
    WHERE ps.parents_ID = ?
";
$childrenStmt = $conn->prepare($childrenQuery);
$childrenStmt->bind_param("i", $parentID);
$childrenStmt->execute();
$childrenResult = $childrenStmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $FirstName = $_POST['FirstName'];
    $MiddleName = $_POST['MiddleName'];
    $LastName = $_POST['LastName'];
    $Sex = $_POST['Sex'];
    $Birthdate = $_POST['Birthdate'];
    $ContactNumber = $_POST['ContactNumber'];
    $Address = $_POST['Address'];
    $ChildLRNs = $_POST['ChildLRN'] ?? [];

    $update = "UPDATE parents 
               SET FirstName=?, MiddleName=?, LastName=?, Sex=?, Birthdate=?, ContactNumber=?, Address=? 
               WHERE parents_ID=?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("sssssssi", $FirstName, $MiddleName, $LastName, $Sex, $Birthdate, $ContactNumber, $Address, $parentID);
    $stmt->execute();


    foreach ($ChildLRNs as $lrn) {
        if (trim($lrn) === '')
            continue;

   
        $stmtCheck = $conn->prepare("SELECT students_ID FROM students WHERE LRN=?");
        $stmtCheck->bind_param("s", $lrn);
        $stmtCheck->execute();
        $res = $stmtCheck->get_result();

        if ($res->num_rows > 0) {
            $student = $res->fetch_assoc();
            $studentID = $student['students_ID'];

         
            $checkLink = $conn->prepare("SELECT * FROM parents_students WHERE parents_ID=? AND students_ID=?");
            $checkLink->bind_param("ii", $parentID, $studentID);
            $checkLink->execute();
            $exist = $checkLink->get_result();

            if ($exist->num_rows > 0) {
            
                $alertMessage .= "This LRN ($lrn) is already linked to this account.\\n";
            } else {
              
                $stmt2 = $conn->prepare("INSERT INTO parents_students (parents_ID, students_ID, Status) VALUES (?,?, 'Pending')");
                $stmt2->bind_param("ii", $parentID, $studentID);
                $stmt2->execute();
            }
        } else {
         
            $alertMessage .= "This LRN ($lrn) is not registered in the system.\\n";
        }
    }

    if (empty($alertMessage)) {
        header("Location: parentinfoteach.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SIMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5dc;
            overflow: hidden;
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
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .edit-btn {
            background-color: #124820;
            color: white;
            border-radius: 25px;
            padding: 10px 30px;
            font-weight: bold;
        }

        .edit-btn:hover {
            background-color: #a8aa10ff;
            color: black;
        }

        .avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
        }

        h4.text-center {
            background-color: #0d4b16;
            border-radius: 25px;
            padding: 10px;
            width: 50%;
            color: #fff;
            margin: 0 auto;
        }

        .btn-outline-light {
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>

<body>
    <div class="header">Student Information Management</div>
    <div class="container-fluid">
        <div class="row">
   
            <div class="col-md-3 sidebar">
                <div class="mb-4 d-flex align-items-center">
                    <a href="teacherdash.php"><img src="lnhslogo.png" alt="Teacher" class="avatar me-2"></a>
                    <div>
                        <div style="font-size:25px;">Teacher</div>
                        <small><?= htmlspecialchars($_SESSION['teacher_name'] ?? '') ?></small>
                    </div>
                </div>
                <a href="addstudteacher.php" class="btn btn-outline-light">Student Registration</a>
                <a href="manageteach.php" class="btn btn-outline-light">Manage Informations</a>
                <a href="gradesmanage.php" class="btn btn-outline-light">Grades Management</a>
                <a href="persoinfoteach.php" class="btn btn-outline-light">Personal Information</a>
                <a href="passteach.php" class="btn btn-outline-light">Password Management</a>
                <a href="regparteach.php" class="btn btn-outline-light">Register Parents</a>
                <br><br>
                <a href="logout.php" class="logout text-decoration-none"
                    onclick="return confirm('Log out?');">Logout</a>
            </div>

            <div class="col-md-9 p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger text-center"><?= $error ?></div>
                <?php elseif ($parent): ?>
                    <div class="form-section">
                        <h4 class="text-center mb-4">Edit Parent Details</h4>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-4"><input type="text" name="FirstName" class="form-control" required
                                        value="<?= htmlspecialchars($parent['FirstName']) ?>" placeholder="First Name">
                                </div>
                                <div class="col-md-4"><input type="text" name="MiddleName" class="form-control"
                                        value="<?= htmlspecialchars($parent['MiddleName']) ?>" placeholder="Middle Name">
                                </div>
                                <div class="col-md-4"><input type="text" name="LastName" class="form-control" required
                                        value="<?= htmlspecialchars($parent['LastName']) ?>" placeholder="Last Name"></div>
                                <div class="col-md-4">
                                    <select name="Sex" class="form-control" required>
                                        <option value="Male" <?= $parent['Sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= $parent['Sex'] == 'Female' ? 'selected' : '' ?>>Female
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4"><input type="date" name="Birthdate" class="form-control" required
                                        value="<?= htmlspecialchars($parent['Birthdate']) ?>"></div>
                                <div class="col-md-4"><input type="text" name="ContactNumber" maxlength="11"
                                        pattern="\d{11}" inputmode="numeric" class="form-control" required
                                        value="<?= htmlspecialchars($parent['ContactNumber']) ?>"
                                        placeholder="Contact Number"></div>
                                <div class="col-md-12"><input type="text" name="Address" class="form-control" required
                                        value="<?= htmlspecialchars($parent['Address']) ?>" placeholder="Address"></div>

               
                                <div class="col-12 mt-3">
                                    <h5>Existing Children</h5>
                                    <ul class="list-group mb-3">
                                        <?php if ($childrenResult->num_rows > 0): ?>
                                            <?php while ($child = $childrenResult->fetch_assoc()): ?>
                                                <li class="list-group-item">
                                                    <?= htmlspecialchars($child['LRN']) ?> -
                                                    <?= htmlspecialchars($child['FirstName'] . " " . $child['MiddleName'] . " " . $child['LastName']) ?>
                                                    (<?= htmlspecialchars($child['Status']) ?>)
                                                </li>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <li class="list-group-item">No children linked yet.</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>

                                <div class="col-12 mt-3">
                                    <h5>Add New Child (Enter LRN)</h5>
                                    <div id="childFields">
                                        <div class="row mb-2 child-row">
                                            <div class="col-8">
                                                <input type="text" name="ChildLRN[]" maxlength="12" pattern="\d{12}"
                                                    class="form-control" placeholder="Enter Child LRN">
                                            </div>
                                            <div class="col-4">
                                                <button type="button" class="btn btn-success" onclick="addChild()">+ Add
                                                    Another Child</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn edit-btn">SAVE CHANGES</button>
                                    <a href="parentinfoteach.php" class="btn btn-secondary ms-2">CANCEL</a>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($alertMessage)): ?>
        <script>
            alert("<?= $alertMessage ?>");
        </script>
    <?php endif; ?>

    <script>
        function addChild() {
            let html = `
        <div class="row mb-2 child-row">
            <div class="col-8">
                <input type="text" name="ChildLRN[]" maxlength="12" pattern="\\d{12}" class="form-control" placeholder="Enter Child LRN">
            </div>
            <div class="col-4">
                <button type="button" class="btn btn-danger" onclick="this.parentElement.parentElement.remove()">Remove</button>
            </div>
        </div>`;
            document.getElementById('childFields').insertAdjacentHTML('beforeend', html);
        }
        document.querySelector('form')?.addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to save changes?')) e.preventDefault();
        });
    </script>
</body>

</html>