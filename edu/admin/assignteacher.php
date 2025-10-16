<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

$message = "";
$messageType = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_ID = (int)$_POST['teacher_ID'];
    $subject_ID = (int)$_POST['subject_ID'];

    $checkTeacherStmt = $conn->prepare("SELECT * FROM teacher_subjects WHERE teachers_ID = ? AND subject_ID = ?");
    $checkTeacherStmt->bind_param("ii", $teacher_ID, $subject_ID);
    $checkTeacherStmt->execute();
    $checkTeacher = $checkTeacherStmt->get_result();

    if ($checkTeacher->num_rows > 0) {
        $message = "This teacher is already assigned to this subject.";
        $messageType = "error";
    } else {
        $checkSubjectStmt = $conn->prepare("SELECT * FROM teacher_subjects WHERE subject_ID = ?");
        $checkSubjectStmt->bind_param("i", $subject_ID);
        $checkSubjectStmt->execute();
        $checkSubject = $checkSubjectStmt->get_result();

        if ($checkSubject->num_rows > 0) {
            $message = "This subject already has a teacher assigned.";
            $messageType = "error";
        } else {
            $insertStmt = $conn->prepare("INSERT INTO teacher_subjects (teachers_ID, subject_ID) VALUES (?, ?)");
            $insertStmt->bind_param("ii", $teacher_ID, $subject_ID);
            if ($insertStmt->execute()) {
                $message = "Teacher assigned successfully!";
                $messageType = "success";
            } else {
                $message = "Assignment failed: " . $insertStmt->error;
                $messageType = "error";
            }
            $insertStmt->close();
        }
        $checkSubjectStmt->close();
    }
    $checkTeacherStmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SIMS</title>
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

        .form-section {
            background-color: #fffde7;
            padding: 25px;
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

        h4.text-center {
            background-color: #0d4b16;
            border-radius: 25px;
            padding: 10px;
            width: 50%;
            color: #ffff;
            margin: 0 auto 20px;
        }

        .btn-outline-light {
            font-family: Arial, Helvetica, sans-serif;
        }

        .btn-icon {
            margin-right: 8px;
            width: 20px;
        }

        @media (max-width: 992px) {
            .sidebar {
                height: auto;
                padding: 10px;
            }

            h4.text-center {
                width: 80%;
                font-size: 1.1rem;
            }

            .form-section {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            .row {
                flex-direction: column;
            }

            .sidebar {
                position: relative;
                height: auto;
                width: 100%;
                order: -1;
            }

            .avatar {
                width: 50px;
                height: 50px;
            }

            .header {
                font-size: 18px;
            }

            .form-section {
                padding: 15px;
            }

            h4.text-center {
                width: 90%;
                padding: 8px;
                font-size: 1rem;
            }

            .form-row .col-md-6 {
                width: 100%;
                margin-bottom: 10px;
            }

            .form-control {
                font-size: 16px; 
                padding: 12px 15px; 
            }

            .register-btn {
                padding: 12px 20px;
                font-size: 16px;
            }

            .alert {
                margin-bottom: 15px;
                padding: 12px; 
            }
        }

        @media (max-width: 480px) {
            .header {
                font-size: 16px;
                padding: 10px;
            }

            .sidebar .btn {
                font-size: 14px;
            }

            .form-section {
                padding: 10px;
            }

            h4.text-center {
                width: 100%;
                font-size: 0.9rem;
                padding: 6px;
            }

            .form-control {
                font-size: 16px;
                margin-bottom: 12px;
                padding: 12px 12px;
            }

            .register-btn {
                font-size: 16px;
                padding: 12px 15px;
            }

            .alert {
                font-size: 14px;
                padding: 10px;
            }
        }

        @media (max-width: 320px) {
            .header {
                font-size: 15px;
                padding: 8px;
            }

            .sidebar {
                padding: 8px;
            }

            .sidebar .btn {
                font-size: 13px;
            }

            .form-section {
                padding: 8px;
            }

            h4.text-center {
                font-size: 0.85rem;
                padding: 5px;
            }

            .form-control {
                font-size: 16px;
                padding: 10px 10px;
                margin-bottom: 10px;
            }

            .register-btn {
                font-size: 15px;
                padding: 10px 12px;
            }

            .alert {
                font-size: 13px;
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="header">Student Information Management System</div>
    <div class="container-fluid">
        <div class="row flex-column flex-md-row">
            <div class="col-md-3 sidebar">
                <div class="mb-3 d-flex align-items-center">
                    <a href="admindash.php" style="text-decoration: none;">
                        <img src="lnhslogo.png" alt="Admin" class="avatar me-2">
                    </a>
                    <div>
                        <div style="font-size:20px;">Administrator</div>
                        <small><?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?></small>
                    </div>
                </div>

                <a href="#collapseStudent" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseStudent">
                    <i class="bi bi-people-fill btn-icon"></i>Student Management
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse" id="collapseStudent">
                    <a href="addstud.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-person-plus btn-icon"></i>Add Student
                    </a>
                    <a href="docreqs.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-file-earmark-text btn-icon"></i>Document Requests
                    </a>
                    <a href="removeenrollee.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-person-x btn-icon"></i>Student Status
                    </a>
                </div>

                <a href="#collapseInfo" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseInfo">
                    <i class="bi bi-info-circle-fill btn-icon"></i>Manage Informations
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse" id="collapseInfo">
                    <a href="studinfo.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-people btn-icon"></i>Student Information
                    </a>
                    <a href="teachinfo.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-person-badge btn-icon"></i>Teacher Information
                    </a>
                    <a href="persoinfo.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-person btn-icon"></i>Personal Information
                    </a>
                    <a href="passmanage.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-lock btn-icon"></i>Password Management
                    </a>
                </div>

                <a href="#collapseTeacher" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseTeacher">
                    <i class="bi bi-person-badge-fill btn-icon"></i>Teacher Management
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse show" id="collapseTeacher">
                    <a href="regteach.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-person-plus btn-icon"></i>Register Teachers
                    </a>
                    <a href="assignteacher.php" class="btn btn-outline-light sub-btn active">
                        <i class="bi bi-arrow-right-circle btn-icon"></i>Assign Teacher
                    </a>
                </div>

                <a href="#collapseAcademic" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseAcademic">
                    <i class="bi bi-journal-bookmark-fill btn-icon"></i>Subjects & Sections
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse" id="collapseAcademic">
                    <a href="addsubject.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-journal-plus btn-icon"></i>Add Subject
                    </a>
                    <a href="managesections.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-gear btn-icon"></i>Manage Sections
                    </a>
                </div>

                <a href="viewrep.php" class="btn btn-outline-light">
                    <i class="bi bi-bar-chart-fill btn-icon"></i>View Reports
                </a>

                <br><br>
                <a href="#" class="logout text-decoration-none" id="logoutBtn">
    <i class="bi bi-box-arrow-right me-2"></i>Logout
</a>
            </div>

            <div class="col-md-9 p-3">
                <div class="form-section">
                    <h4 class="text-center mb-4">Assign Teacher to Subject</h4>

                    <?php if ($message): ?>
                        <div class="alert alert-<?= $messageType == 'success' ? 'success' : 'warning' ?> text-center"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <form method="POST" class="form-row">
                        <div class="row">
                            <div class="col-md-6">
                                <select name="teacher_ID" class="form-control" required>
                                    <option value="">Select Teacher</option>
                                    <?php
                                    $teachersStmt = $conn->prepare("SELECT teachers_ID, FirstName, LastName FROM teachers ORDER BY LastName, FirstName");
                                    $teachersStmt->execute();
                                    $teachers = $teachersStmt->get_result();
                                    while ($row = $teachers->fetch_assoc()) {
                                        echo "<option value='{$row['teachers_ID']}'>" . htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']) . "</option>";
                                    }
                                    $teachersStmt->close();
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <select name="subject_ID" class="form-control" required>
                                    <option value="">Select Subject</option>
                                    <?php
                                    $subjectsStmt = $conn->prepare("
                                        SELECT s.subject_ID, s.SubjectName, yl.YearName, sec.SectionName
                                        FROM subjects s
                                        JOIN yearlevels yl ON s.YearLevelID = yl.yearlevel_ID
                                        JOIN sections sec ON s.SectionID = sec.section_ID
                                        WHERE s.subject_ID NOT IN (SELECT subject_ID FROM teacher_subjects)
                                        ORDER BY yl.YearName, sec.SectionName, s.SubjectName
                                    ");
                                    $subjectsStmt->execute();
                                    $subjects = $subjectsStmt->get_result();
                                    while ($row = $subjects->fetch_assoc()) {
                                        echo "<option value='{$row['subject_ID']}'>[" . htmlspecialchars($row['YearName']) . "] " . htmlspecialchars($row['SectionName'] . ' - ' . $row['SubjectName']) . "</option>";
                                    }
                                    $subjectsStmt->close();
                                    ?>
                                </select>
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" class="btn register-btn mt-3">ASSIGN</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.getElementById('logoutBtn').addEventListener('click', function(e) {
    e.preventDefault(); 
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of the system.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1b5e20',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout'
    }).then((result) => {
        if (result.isConfirmed) {
            
            window.location.href = 'logout.php';
        }
    });
});


</script>
</body>

</html>