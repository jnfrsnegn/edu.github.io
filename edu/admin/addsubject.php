<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $SubjectName = trim($_POST['SubjectName']);
    $YearLevelID = (int)($_POST['YearLevelID'] ?? 0);
    $SectionID = (int)($_POST['SectionID'] ?? 0);

    if (empty($SubjectName) || empty($YearLevelID) || empty($SectionID)) {
        $errorMessage = "All fields are required.";
    } else {
        $normalizedSubject = strtolower(str_replace(' ', '', $SubjectName));

        $checkStmt = $conn->prepare("
            SELECT subject_ID
            FROM subjects
            WHERE YearLevelID = ? AND SectionID = ? 
            AND REPLACE(LOWER(SubjectName), ' ', '') = ?
        ");
        $checkStmt->bind_param("iis", $YearLevelID, $SectionID, $normalizedSubject);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $errorMessage = "This subject already exists for the selected Year Level and Section.";
        } else {
            $formattedSubject = ucwords(strtolower($SubjectName));

            $insertStmt = $conn->prepare("
                INSERT INTO subjects (SubjectName, YearLevelID, SectionID)
                VALUES (?, ?, ?)
            ");
            $insertStmt->bind_param("sii", $formattedSubject, $YearLevelID, $SectionID);

            if ($insertStmt->execute()) {
                $successMessage = "Subject added successfully!";
                $_POST = []; 
            } else {
                $errorMessage = "Database error: " . $insertStmt->error;
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    }
}

$yearlevelsStmt = $conn->prepare("SELECT * FROM yearlevels ORDER BY CAST(SUBSTRING(YearName, 7) AS UNSIGNED)");
$yearlevelsStmt->execute();
$yearlevelsResult = $yearlevelsStmt->get_result();

$subjectsStmt = $conn->prepare("
    SELECT s.subject_ID, s.SubjectName, yl.YearName, sec.SectionName
    FROM subjects s
    LEFT JOIN yearlevels yl ON s.YearLevelID = yl.yearlevel_ID
    LEFT JOIN sections sec ON s.SectionID = sec.section_ID
    ORDER BY CAST(SUBSTRING(yl.YearName, 7) AS UNSIGNED), sec.SectionName, s.SubjectName
");
$subjectsStmt->execute();
$subjectsResult = $subjectsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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
            background-color: #a8aa10ff;
        }

        h4.text-center {
            background-color: #0d4b16;
            border-radius: 25px;
            padding: 10px;
            width: 50%;
            color: #ffff;
            margin: 0 auto 20px;
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
            overflow-x: auto;
        }

        th {
            background-color: #1b5e20;
            color: white;
            text-align: center;
        }

        td,
        th {
            padding: 8px;
            text-align: center;
            font-size: 14px;
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

            .table-responsive {
                font-size: 0.9rem;
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


            .form-row .col-md-4 {
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

            .table-responsive {
                font-size: 0.85rem;
            }

            .table th,
            .table td {
                padding: 4px;
                white-space: nowrap;
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

            .table th,
            .table td {
                font-size: 12px;
                padding: 6px;
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

            .table th,
            .table td {
                font-size: 11px;
                padding: 4px;
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
                    <a href="admindash.php" style="text-decoration: none;"><img src="lnhslogo.png" alt="Admin" class="avatar me-2"></a>
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

                <a href="#collapseTeacher" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseTeacher">
                    <i class="bi bi-person-badge-fill btn-icon"></i>Teacher Management
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse" id="collapseTeacher">
                    <a href="regteach.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-person-plus btn-icon"></i>Register Teachers
                    </a>
                    <a href="assignteacher.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-arrow-right-circle btn-icon"></i>Assign Teacher
                    </a>
                </div>

                <a href="#collapseAcademic" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseAcademic">
                    <i class="bi bi-journal-bookmark-fill btn-icon"></i>Subjects & Sections
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse show" id="collapseAcademic">
                    <a href="addsubject.php" class="btn btn-outline-light sub-btn active">
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
                    <h4 class="text-center mb-4">Add Subject</h4>
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success text-center"><?= htmlspecialchars($successMessage) ?></div>
                    <?php endif; ?>
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($errorMessage) ?></div>
                    <?php endif; ?>

                    <form method="post" class="form-row">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="SubjectName" class="form-control" placeholder="Subject Name" required value="<?= htmlspecialchars($_POST['SubjectName'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <select name="YearLevelID" id="YearLevelID" class="form-control" required>
                                    <option value="" disabled <?= empty($_POST['YearLevelID']) ? 'selected' : '' ?>>Select Year Level</option>
                                    <?php
                                    $yearlevelsResult->data_seek(0); 
                                    while ($row = $yearlevelsResult->fetch_assoc()) {
                                        $selected = ($_POST['YearLevelID'] ?? '') == $row['yearlevel_ID'] ? 'selected' : '';
                                        echo "<option value='{$row['yearlevel_ID']}' $selected>" . htmlspecialchars($row['YearName']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="SectionID" id="SectionID" class="form-control" required>
                                    <option value="" disabled <?= empty($_POST['SectionID']) ? 'selected' : '' ?>>Select Section</option>
                                    <?php
                                    if (!empty($_POST['YearLevelID'])) {
                                        $sectionsStmt = $conn->prepare("SELECT * FROM sections WHERE yearlevel_ID = ? ORDER BY SectionName");
                                        $sectionsStmt->bind_param("i", $_POST['YearLevelID']);
                                        $sectionsStmt->execute();
                                        $sectionsResult = $sectionsStmt->get_result();
                                        while ($sRow = $sectionsResult->fetch_assoc()) {
                                            $sSelected = ($_POST['SectionID'] ?? '') == $sRow['section_ID'] ? 'selected' : '';
                                            echo "<option value='{$sRow['section_ID']}' $sSelected>" . htmlspecialchars($sRow['SectionName']) . "</option>";
                                        }
                                        $sectionsStmt->close();
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-center">
                                <button type="submit" class="btn register-btn mt-2">ADD SUBJECT</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-4">
                        <table id="subjectTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Subject Name</th>
                                    <th>Year Level</th>
                                    <th>Section</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; ?>
                                <?php if ($subjectsResult && $subjectsResult->num_rows > 0): ?>
                                    <?php $subjectsResult->data_seek(0);?>
                                    <?php while ($row = $subjectsResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars(ucwords(strtolower($row['SubjectName']))) ?></td>
                                            <td><?= htmlspecialchars($row['YearName']) ?></td>
                                            <td><?= htmlspecialchars($row['SectionName']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No subjects added yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("logoutBtn").addEventListener("click", function(e) {
            e.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "You will be logged out of the system.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1b5e20",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, log out"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "logout.php";
                }
            });
        });
    </script>
    <script>
        document.getElementById('YearLevelID').addEventListener('change', function () {
            const yearLevelID = this.value;
            const sectionSelect = document.getElementById('SectionID');
            sectionSelect.innerHTML = '<option value="">Loading...</option>';

            fetch('getsections.php?yearlevel_ID=' + yearLevelID)
                .then(response => response.json())
                .then(data => {
                    sectionSelect.innerHTML = '<option value="">Select Section</option>';
                    data.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.section_ID;
                        option.textContent = section.SectionName;
                        sectionSelect.appendChild(option);
                    });
                })
                .catch(err => {
                    console.error('Error loading sections:', err);
                    sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
                });
        });

         $(document).ready(function() {
        $('#subjectTable').DataTable({
    "pageLength": 10,
    "lengthChange": false,
    "ordering": false,
    "order": [[0, "asc"]], 
    "columnDefs": [
        { "orderable": false, "targets": [2] } 
    ]
});

    });
    </script>
</body>
</html>
