<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

$successMessage = "";
$errorMessage = "";

if (isset($_POST['add_section'])) {
    $SectionName = trim($_POST['SectionName']);
    $YearLevelID = (int)($_POST['YearLevelID'] ?? 0);

    if (empty($SectionName) || empty($YearLevelID)) {
        $errorMessage = "Both Section Name and Year Level are required.";
    } else {
        $checkStmt = $conn->prepare("SELECT section_ID FROM sections WHERE SectionName = ? AND yearlevel_ID = ?");
        $checkStmt->bind_param("si", $SectionName, $YearLevelID);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $errorMessage = "This section already exists for the selected Year Level.";
        } else {
            $insertStmt = $conn->prepare("INSERT INTO sections (SectionName, yearlevel_ID) VALUES (?, ?)");
            $insertStmt->bind_param("si", $SectionName, $YearLevelID);

            if ($insertStmt->execute()) {
                $successMessage = "Section added successfully!";
            } else {
                $errorMessage = "Database error: " . $insertStmt->error;
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    }
}
if (isset($_POST['edit_section'])) {
    $section_ID = (int)($_POST['section_ID'] ?? 0);
    $SectionName = trim($_POST['SectionName']);

    if (empty($SectionName) || empty($section_ID)) {
        $errorMessage = "Section name cannot be empty.";
    } else {
        $updateStmt = $conn->prepare("UPDATE sections SET SectionName = ? WHERE section_ID = ?");
        $updateStmt->bind_param("si", $SectionName, $section_ID);

        if ($updateStmt->execute()) {
            $successMessage = "updated";
        } else {
            $errorMessage = "Database error: " . $updateStmt->error;
        }
        $updateStmt->close();
    }
}
if (isset($_POST['delete_section'])) {
    $section_ID = (int)($_POST['section_ID'] ?? 0);
    if ($section_ID > 0) {
        $deleteStmt = $conn->prepare("DELETE FROM sections WHERE section_ID = ?");
        $deleteStmt->bind_param("i", $section_ID);
        if ($deleteStmt->execute()) {
            $successMessage = "Section deleted successfully!";
        } else {
            $errorMessage = "Failed to delete section: " . $deleteStmt->error;
        }
        $deleteStmt->close();
    }
}

$yearlevelsResult = $conn->query("SELECT * FROM yearlevels ORDER BY CAST(SUBSTRING(YearName, 7) AS UNSIGNED)");
$filterYearID = (int)($_GET['yearlevel'] ?? 0);

$sql = "SELECT sec.section_ID, sec.SectionName, yl.YearName 
        FROM sections sec 
        LEFT JOIN yearlevels yl ON sec.yearlevel_ID = yl.yearlevel_ID";
if ($filterYearID) {
    $sql .= " WHERE sec.yearlevel_ID = " . $filterYearID;
}
$sql .= " ORDER BY CAST(SUBSTRING(yl.YearName, 7) AS UNSIGNED), sec.SectionName";
$sectionsResult = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
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
            height: 117vh;
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
            transition: transform .3s ease;
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
        }

        .register-btn:hover {
            background-color: #a8aa10ff;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border-radius: 25px;
            padding: 6px 12px;
            font-weight: bold;
            font-size: .875rem;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        h4.text-center {
            background-color: #0d4b16;
            border-radius: 25px;
            padding: 10px;
            width: 50%;
            color: #fff;
            margin: 0 auto 20px;
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
            vertical-align: middle;
        }

        .btn-icon {
            margin-right: 8px;
            width: 20px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .container-fluid {
            min-height: calc(100vh - 70px);
        }

        .row {
            min-height: 100%;
        }

        @media (max-width: 768px) {
            .sidebar {
                height: auto;
                position: relative;
                order: -1;
                width: 100%;
                padding: 10px;
            }

            .header {
                font-size: 18px;
                padding: 12px;
            }

            .avatar {
                width: 50px;
                height: 50px;
            }

            .form-section {
                padding: 15px;
                margin-top: 10px;
            }

            h4.text-center {
                width: 90%;
                font-size: 1.1rem;
                padding: 8px;
            }

            .form-control {
                font-size: 16px;
                padding: 12px 15px;
                margin-bottom: 12px;
            }

            .register-btn {
                width: 100%;
                padding: 12px 20px;
                font-size: 16px;
            }

            .table-responsive {
                font-size: 0.9rem;
            }

            .table th,
            .table td {
                padding: 6px 4px;
                font-size: 13px;
                white-space: nowrap;
            }

            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: 5px;
                align-items: center;
            }

            .action-buttons .form-control {
                width: 100% !important;
                max-width: 200px;
                margin-bottom: 5px;
            }

            .action-buttons button {
                width: 100%;
                max-width: 100px;
            }

            .sidebar .btn {
                font-size: 14px;
                padding: 10px;
            }

            .sidebar .sub-btn {
                width: calc(100% - 10px);
                margin-left: 10px;
                font-size: 13px;
                padding: 8px;
            }

            .logout {
                font-size: 14px;
            }

            .container-fluid {
                min-height: auto;
            }

            .row {
                min-height: auto;
            }
        }

        @media (max-width: 480px) {
            .header {
                font-size: 16px;
                padding: 10px;
            }

            .sidebar {
                padding: 8px;
            }

            .sidebar .btn {
                font-size: 14px;
                padding: 8px;
            }

            .sidebar .sub-btn {
                font-size: 12px;
                padding: 6px;
            }

            .avatar {
                width: 50px;
                height: 50px;
            }

            .form-section {
                padding: 12px;
            }

            h4.text-center {
                width: 100%;
                font-size: 1rem;
                padding: 6px;
            }

            .table th,
            .table td {
                font-size: 12px;
                padding: 4px 2px;
            }

            .form-control {
                padding: 12px 12px;
                margin-bottom: 10px;
            }

            .register-btn {
                padding: 12px 15px;
                font-size: 15px;
            }

            .action-buttons button {
                font-size: 14px;
                padding: 8px 10px;
            }
        }

        @media (max-width: 320px) {
            .header {
                font-size: 15px;
                padding: 8px;
            }

            .sidebar {
                padding: 6px;
            }

            .sidebar .btn {
                font-size: 12px;
                padding: 6px;
            }

            .sidebar .sub-btn {
                font-size: 11px;
                padding: 4px;
                margin-left: 8px;
            }

            .avatar {
                width: 35px;
                height: 35px;
            }

            .form-section {
                padding: 10px;
            }

            h4.text-center {
                font-size: 0.9rem;
                padding: 5px;
            }

            .table th,
            .table td {
                font-size: 11px;
                padding: 3px 1px;
            }

            .form-control {
                padding: 10px 10px;
                margin-bottom: 8px;
            }

            .register-btn {
                padding: 10px 12px;
                font-size: 14px;
            }

            .action-buttons {
                gap: 3px;
            }

            .action-buttons .form-control {
                max-width: 150px;
            }

            .action-buttons button {
                font-size: 13px;
                padding: 6px 8px;
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
                    <a href="admindash.php" style="text-decoration:none;">
                        <img src="lnhslogo.png" alt="Admin" class="avatar me-2">
                    </a>
                    <div>
                        <div style="font-size:20px;">Administrator</div>
                        <small><?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?></small>
                    </div>
                </div>

                <a href="#collapseStudent" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="bi bi-people-fill btn-icon"></i>Student Management<i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse" id="collapseStudent">
                    <a href="addstud.php" class="btn btn-outline-light sub-btn"><i class="bi bi-person-plus btn-icon"></i>Add Student</a>
                    <a href="docreqs.php" class="btn btn-outline-light sub-btn"><i class="bi bi-file-earmark-text btn-icon"></i>Document Requests</a>
                    <a href="removeenrollee.php" class="btn btn-outline-light sub-btn"><i class="bi bi-person-x btn-icon"></i>Student Status</a>
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
                <a href="#collapseAcademic" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true">
                    <i class="bi bi-journal-bookmark-fill btn-icon"></i>Subjects & Sections<i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse show" id="collapseAcademic">
                    <a href="addsubject.php" class="btn btn-outline-light sub-btn"><i class="bi bi-journal-plus btn-icon"></i>Add Subject</a>
                    <a href="managesections.php" class="btn btn-outline-light sub-btn active"><i class="bi bi-gear btn-icon"></i>Manage Sections</a>
                </div>
                <a href="viewrep.php" class="btn btn-outline-light">
                    <i class="bi bi-bar-chart-fill btn-icon"></i>View Reports
                </a>
                <br><br>
                <a href="#" class="logout text-decoration-none" id="logoutBtn"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>

            <div class="col-md-9 p-3">
                <div class="form-section">
                    <h4 class="text-center mb-4">Manage Sections</h4>

                    <form method="post" class="mb-4">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <input type="text" name="SectionName" class="form-control" placeholder="Section Name" required value="<?= htmlspecialchars($_POST['SectionName'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <select name="YearLevelID" class="form-control" required>
                                    <option value="" disabled <?= empty($_POST['YearLevelID']) ? 'selected' : '' ?>>Select Year Level</option>
                                    <?php
                                    $yearlevelsResult->data_seek(0);
                                    while ($yl = $yearlevelsResult->fetch_assoc()): ?>
                                        <option value="<?= $yl['yearlevel_ID'] ?>" <?= ($_POST['YearLevelID'] ?? '') == $yl['yearlevel_ID'] ? 'selected' : '' ?>><?= htmlspecialchars($yl['YearName']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-center mt-2">
                                <button type="submit" name="add_section" class="btn register-btn">Add Section</button>
                            </div>
                        </div>
                    </form>

                    <h5 class="mb-3">Filter Sections by Year Level</h5>
                    <form method="get" class="mb-3">
                        <div class="row">
                            <div class="col-12 col-md-10">
                                <select name="yearlevel" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Year Levels</option>
                                    <?php
                                    $yearlevelsResult->data_seek(0);
                                    while ($yl = $yearlevelsResult->fetch_assoc()): ?>
                                        <option value="<?= $yl['yearlevel_ID'] ?>" <?= $filterYearID == $yl['yearlevel_ID'] ? 'selected' : '' ?>><?= htmlspecialchars($yl['YearName']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-2 mt-2 mt-md-0">
                                <button type="submit" class="btn register-btn w-100">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-2">
                        <table id="sectionTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Section Name</th>
                                    <th>Year Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($sectionsResult->num_rows > 0):
                                    $sectionsResult->data_seek(0);
                                    while ($row = $sectionsResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['SectionName']) ?></td>
                                            <td><?= htmlspecialchars($row['YearName']) ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <form method="post" class="d-inline me-1 me-sm-0 mb-1 mb-sm-0">
                                                        <input type="hidden" name="section_ID" value="<?= $row['section_ID'] ?>">
                                                        <input type="text" name="SectionName" value="<?= htmlspecialchars($row['SectionName']) ?>" class="form-control d-inline-block" style="width:120px;" required>
                                                        <button type="submit" name="edit_section" class="btn btn-success btn-sm">Edit</button>
                                                    </form>
                                                    <form method="post" class="d-inline">
                                                        <input type="hidden" name="section_ID" value="<?= $row['section_ID'] ?>">
                                                        <button type="submit" name="delete_section" class="btn delete-btn btn-sm" onclick="return confirmDelete(event)">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile;
                                    else: ?>
                                        <tr>
                                            <td colspan="4">No sections found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#sectionTable').DataTable({
                pageLength: 5,
                lengthChange: false,
                "ordering": false,
            });

            <?php if ($successMessage === "updated"): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Section Updated!',
                    text: 'Section name has been successfully updated.',
                    confirmButtonColor: '#124820'
                });
            <?php elseif ($successMessage && $successMessage !== "updated"): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= addslashes($successMessage) ?>',
                    confirmButtonColor: '#124820'
                });
            <?php elseif ($errorMessage): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?= addslashes($errorMessage) ?>',
                    confirmButtonColor: '#dc3545'
                });
            <?php endif; ?>
        });

        function confirmDelete(e) {
            e.preventDefault();
            const form = e.target.closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "This section will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        }

        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Logout Confirmation',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = 'logout.php';
            });
        });


        document.querySelectorAll('form button[name="edit_section"]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                Swal.fire({
                    title: 'Save changes?',
                    text: "Do you want to update this section name?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#124820',
                    cancelButtonColor: '#6c757d',
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