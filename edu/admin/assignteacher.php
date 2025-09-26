<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

$successMessage = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_ID = $_POST['teacher_ID'];
    $subject_ID = $_POST['subject_ID'];


    $checkTeacher = $conn->query("SELECT * FROM teacher_subjects WHERE teachers_ID = '$teacher_ID' AND subject_ID = '$subject_ID'");
    $checkSubject = $conn->query("SELECT * FROM teacher_subjects WHERE subject_ID = '$subject_ID'");

    if ($checkTeacher->num_rows > 0) {
        $successMessage = "This teacher is already assigned to this subject.";
    } elseif ($checkSubject->num_rows > 0) {
        $successMessage = "This subject already has a teacher assigned.";
    } else {
        $conn->query("INSERT INTO teacher_subjects (teachers_ID, subject_ID) VALUES ('$teacher_ID', '$subject_ID')");
        $successMessage = "Teacher assigned successfully!";
    }
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
            background-color: #a8aa10;
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
            text-align: center;
        }

        .btn-outline-light {
            font-family: Arial, Helvetica, sans-serif;
        }

        h4.text-center {
            background-color: #0d4b16;
            border-radius: 25px;
            padding: 10px;
            width: 50%;
            color: #ffff;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="header">Student Information Management System</div>
    <div class="container-fluid">
        <div class="row flex-column flex-md-row">
            <div class="col-12 col-md-3 sidebar">
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
                <a href="assignteacher.php" class="btn btn-outline-light active">Assign Teacher</a>
                <a href="regpar.php" class="btn btn-outline-light">Register Parents</a>
                <a href="addsubject.php" class="btn btn-outline-light">Add Subject</a>
                <a href="managesections.php" class="btn btn-outline-light ">Manage Sections</a>
                <br><br>
                <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
            </div>

            <div class="col-12 col-md-9 p-4">
                <div class="form-section">
                    <h4 class="text-center mb-4">Assign Teacher to Subject</h4>

                    <?php if ($successMessage): ?>
                        <div class="alert alert-info text-center"><?= $successMessage ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <select name="teacher_ID" class="form-control" required>
                                    <option value="">Select Teacher</option>
                                    <?php
                                    $teachers = $conn->query("SELECT * FROM teachers");
                                    while ($row = $teachers->fetch_assoc()) {
                                        echo "<option value='{$row['teachers_ID']}'>{$row['FirstName']} {$row['LastName']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <select name="subject_ID" class="form-control" required>
                                    <option value="">Select Subject</option>
                                    <?php
                                    $subjects = $conn->query("
                    SELECT s.subject_ID, s.SubjectName, yl.YearName, sec.SectionName
                    FROM subjects s
                    JOIN yearlevels yl ON s.YearLevelID = yl.yearlevel_ID
                    JOIN sections sec ON s.SectionID = sec.section_ID
                    WHERE s.subject_ID NOT IN (SELECT subject_ID FROM teacher_subjects)
                  ");
                                    while ($row = $subjects->fetch_assoc()) {
                                        echo "<option value='{$row['subject_ID']}'>[{$row['YearName']}] {$row['SectionName']} - {$row['SubjectName']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn register-btn mt-3">ASSIGN</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>