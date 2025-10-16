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

$statusOptions = ['4Ps', 'IPs', 'SNED', 'Repeater', 'Balik-Aral', 'Transferred-In', 'Muslim'];

if (!$studentID) {
    header("Location: studinfoteach.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM students WHERE students_ID = ?");
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
    $FirstName = $_POST['FirstName'];
    $MiddleName = $_POST['MiddleName'];
    $LastName = $_POST['LastName'];
    $Suffix = $_POST['Suffix'];
    $Sex = $_POST['Sex'];
    $Birthdate = $_POST['Birthdate'];
    $LRN = $_POST['LRN'];
    $YearLevelID = $_POST['YearLevelID'] ?? $student['YearLevelID'];
    $SectionID = $_POST['SectionID'] ?? $student['SectionID'];
    $ContactNumber = $_POST['ContactNumber'];
    $EmailAddress = $_POST['EmailAddress'];
    $Address = $_POST['Address'];
    $Status = isset($_POST['Status']) ? implode(',', $_POST['Status']) : '';

    $stmt = $conn->prepare("UPDATE students SET 
        FirstName=?, MiddleName=?, LastName=?, Suffix=?, 
        Sex=?, Birthdate=?, LRN=?, YearLevelID=?, SectionID=?, 
        ContactNumber=?, EmailAddress=?, Address=?, Status=? 
        WHERE students_ID=?");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMS - Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5dc;
            overflow-x: hidden;
        }

        .header {
            background-color: #1b5e20;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .sidebar {
            background-color: #0d4b16;
            height: 100vh;
            color: white;
            padding: 15px;
        }

        .sidebar .btn {
            width: 100%;
            text-align: left;
            margin-bottom: 10px;
            position: relative;
            font-size: 15px;
        }

        .sidebar .btn i.bi-chevron-right {
            transition: transform 0.3s ease;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .sidebar .btn[aria-expanded="true"] i.bi-chevron-right {
            transform: translateY(-50%) rotate(90deg);
        }

        .sidebar .sub-btn {
            width: calc(100% - 15px);
            margin-left: 15px;
            margin-bottom: 5px;
        }

        .sidebar .sub-btn.active {
            background-color: #1b5e20;
            border-color: #1b5e20;
        }

        .logout {
            color: red;
            font-weight: bold;
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }

        .btn-outline-light {
            font-family: Arial, Helvetica, sans-serif;
        }

        .btn-icon {
            margin-right: 8px;
            width: 20px;
        }

        .form-section {
            background-color: #fffde7;
            padding: 25px;
            border-radius: 10px;
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
            background-color: #a8aa10;
            color: black;
        }

        h4.text-center {
            background-color: #0d4b16;
            border-radius: 25px;
            padding: 10px;
            width: 50%;
            color: #fff;
            margin: 0 auto 20px;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <div class="header">Student Information Management System</div>

    <div class="container-fluid">
        <div class="row flex-column flex-md-row">
            <div class="col-md-3 sidebar">
                <div class="mb-3 d-flex align-items-center">
                    <a href="teacherdash.php" style="text-decoration: none;">
                        <img src="lnhslogo.png" alt="Teacher" class="avatar me-2">
                    </a>
                    <div>
                        <div style="font-size:20px;">Teacher</div>
                        <small><?= htmlspecialchars($_SESSION['teacher_name'] ?? '') ?></small>
                    </div>
                </div>

                <a href="#collapseStudents" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true"
                    aria-controls="collapseStudents">
                    <i class="bi bi-people btn-icon"></i>Students
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse show" id="collapseStudents">
                    <a href="addstudteacher.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-person-plus btn-icon"></i>Student Registration
                    </a>
                    <a href="studinfoteach.php" class="btn btn-outline-light sub-btn active">
                        <i class="bi bi-person-gear btn-icon"></i>Student Informations
                    </a>
                </div>

                <a href="#collapseGrades" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false"
                    aria-controls="collapseGrades">
                    <i class="bi bi-clipboard-data btn-icon"></i>Grades
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse" id="collapseGrades">
                    <a href="gradesmanage.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-clipboard-data btn-icon"></i>Grades Management
                    </a>
                </div>

                <a href="#collapseAccount" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false"
                    aria-controls="collapseAccount">
                    <i class="bi bi-person-circle btn-icon"></i>Account
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse" id="collapseAccount">
                    <a href="persoinfoteach.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-person btn-icon"></i>Personal Information
                    </a>
                    <a href="passteach.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-lock btn-icon"></i>Password Management
                    </a>
                </div>

                <br><br>
                <a href="#" class="logout text-decoration-none" id="logoutBtn">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </div>

            <div class="col-md-9 p-3">
                <?php if ($error): ?>
                    <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                <?php elseif ($student): ?>
                    <div class="form-section">
                        <h4 class="text-center">Edit Student Details</h4>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-4"><input type="text" name="FirstName" class="form-control"
                                        placeholder="First Name" required
                                        value="<?= htmlspecialchars($student['FirstName']) ?>"></div>
                                <div class="col-md-4"><input type="text" name="MiddleName" class="form-control"
                                        placeholder="Middle Name" value="<?= htmlspecialchars($student['MiddleName']) ?>">
                                </div>
                                <div class="col-md-4"><input type="text" name="LastName" class="form-control"
                                        placeholder="Last Name" required
                                        value="<?= htmlspecialchars($student['LastName']) ?>"></div>
                                <div class="col-md-4"><input type="text" name="Suffix" class="form-control"
                                        placeholder="Suffix" value="<?= htmlspecialchars($student['Suffix']) ?>"></div>
                                <div class="col-md-4">
                                    <select name="Sex" class="form-control" required>
                                        <option value="Male" <?= $student['Sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                        <option value="Female" <?= $student['Sex'] == 'Female' ? 'selected' : '' ?>>Female
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4"><input type="date" name="Birthdate" class="form-control" readonly
                                        value="<?= htmlspecialchars($student['Birthdate']) ?>"></div>
                                <div class="col-md-4"><input type="text" name="LRN" class="form-control" maxlength="12"
                                        pattern="\d{12}" inputmode="numeric"
                                        value="<?= htmlspecialchars($student['LRN']) ?>"></div>
                                <div class="col-md-4"><input type="text" name="ContactNumber" class="form-control"
                                        maxlength="11" pattern="\d{11}" required
                                        value="<?= htmlspecialchars($student['ContactNumber']) ?>"></div>
                                <div class="col-md-4"><input type="email" name="EmailAddress" class="form-control" required
                                        value="<?= htmlspecialchars($student['EmailAddress']) ?>"></div>
                                <div class="col-md-4"><input type="text" name="Address" class="form-control"
                                        placeholder="Address" required value="<?= htmlspecialchars($student['Address']) ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Status</label>
                                    <div class="checkbox-group">
                                        <?php foreach ($statusOptions as $status): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="Status[]"
                                                    value="<?= $status ?>" <?= in_array($status, $studentStatus) ? 'checked' : '' ?>>
                                                <label class="form-check-label"><?= $status ?></label>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.form-section form');
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Save Changes?',
                    text: "Are you sure you want to update this student's information?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#124820',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, save changes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>

</html>