<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacherlogin.php");
    exit();
}

$studentID = $_GET['sid'] ?? null;
$student = null;
$error = '';

// Status options
$statusOptions = ['4Ps', '1Ps', 'SNED', 'Repeater', 'Balik-Aral', 'Transferred-In', 'Muslim'];

if (!$studentID) {
    header("Location: studinfoteach.php");
    exit;
}

// Fetch student record
$query = "SELECT * FROM students WHERE students_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $student = $result->fetch_assoc();
} else {
    $error = "Student not found.";
}

$studentStatus = $student['Status'] ? explode(',', $student['Status']) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $FirstName     = $_POST['FirstName'];
    $MiddleName    = $_POST['MiddleName'];
    $LastName      = $_POST['LastName'];
    $Suffix        = $_POST['Suffix'];
    $Sex           = $_POST['Sex'];
    $Birthdate     = $_POST['Birthdate'];
    $LRN           = $_POST['LRN'];
    $YearLevelID   = $_POST['YearLevelID'] ?? $student['YearLevelID'];
    $SectionID     = $_POST['SectionID'] ?? $student['SectionID'];
    $ContactNumber = $_POST['ContactNumber'];
    $EmailAddress  = $_POST['EmailAddress'];
    $Address       = $_POST['Address'];
    $Status        = isset($_POST['Status']) ? implode(',', $_POST['Status']) : '';

    $update = "UPDATE students SET 
        FirstName = ?, MiddleName = ?, LastName = ?, Suffix = ?, 
        Sex = ?, Birthdate = ?, LRN = ?, YearLevelID = ?, SectionID = ?, 
        ContactNumber = ?, EmailAddress = ?, Address = ?, Status = ? 
        WHERE students_ID = ?";

    $stmt = $conn->prepare($update);
    $stmt->bind_param(
        "ssssssssissssi",
        $FirstName,
        $MiddleName,
        $LastName,
        $Suffix,
        $Sex,
        $Birthdate,
        $LRN,
        $YearLevelID,
        $SectionID,
        $ContactNumber,
        $EmailAddress,
        $Address,
        $Status,
        $studentID
    );

    if ($stmt->execute()) {
        header("Location: studinfoteach.php");
        exit();
    } else {
        $error = "Update failed: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student - Teacher</title>
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
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
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
            <a href="studinfoteach.php" class="btn btn-outline-light">Student Information</a>
            <a href="persoinfoteach.php" class="btn btn-outline-light">Personal Information</a>
            <a href="passteach.php" class="btn btn-outline-light">Password Management</a>
            <a href="regparteach.php" class="btn btn-outline-light">Register Parents</a>
            <br><br>
            <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Log out?');">Logout</a>
        </div>

        <div class="col-md-9 p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?= $error ?></div>
            <?php elseif ($student): ?>
                <div class="form-section">
                    <h4 class="text-center mb-4">Edit Student Details</h4>
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-4"><input type="text" name="FirstName" class="form-control" required value="<?= htmlspecialchars($student['FirstName']) ?>" placeholder="First Name"></div>
                            <div class="col-md-4"><input type="text" name="MiddleName" class="form-control" value="<?= htmlspecialchars($student['MiddleName']) ?>" placeholder="Middle Name"></div>
                            <div class="col-md-4"><input type="text" name="LastName" class="form-control" required value="<?= htmlspecialchars($student['LastName']) ?>" placeholder="Last Name"></div>
                            <div class="col-md-4"><input type="text" name="Suffix" class="form-control" value="<?= htmlspecialchars($student['Suffix']) ?>" placeholder="Suffix"></div>
                            <div class="col-md-4">
                                <select name="Sex" class="form-control" required>
                                    <option value="Male" <?= $student['Sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= $student['Sex'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4"><input type="date" name="Birthdate" class="form-control" required value="<?= htmlspecialchars($student['Birthdate']) ?>"></div>
                            <div class="col-md-4"><input type="text" name="LRN" maxlength="12" pattern="\d{12}" inputmode="numeric" class="form-control" required value="<?= htmlspecialchars($student['LRN']) ?>" placeholder="LRN"></div>
                            <div class="col-md-4"><input type="text" name="ContactNumber" maxlength="11" pattern="\d{11}" inputmode="numeric" class="form-control" required value="<?= htmlspecialchars($student['ContactNumber']) ?>" placeholder="Contact Number"></div>
                            <div class="col-md-4"><input type="email" name="EmailAddress" class="form-control" required value="<?= htmlspecialchars($student['EmailAddress']) ?>" placeholder="name@gmail.com"></div>
                            <div class="col-md-4"><input type="text" name="Address" class="form-control" required value="<?= htmlspecialchars($student['Address']) ?>" placeholder="Address"></div>


                            <input type="hidden" name="YearLevelID" value="<?= htmlspecialchars($student['YearLevelID']) ?>">
                            <input type="hidden" name="SectionID" value="<?= htmlspecialchars($student['SectionID']) ?>">


                            <div class="col-12 mb-3">
                                <label class="form-label">Status:</label><br>
                                <div class="checkbox-group">
                                    <?php foreach ($statusOptions as $st): ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="Status[]" value="<?= $st ?>" <?= in_array($st, $studentStatus) ? 'checked' : '' ?>>
                                            <label class="form-check-label"><?= $st ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn edit-btn">SAVE CHANGES</button>
                                <a href="studinfoteach.php" class="btn btn-secondary ms-2">CANCEL</a>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to save changes?')) e.preventDefault();
    });
</script>

</body>
</html>
