<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

$selectedYearLevelID = $_POST['YearLevelID'] ?? '';
$selectedSectionID = $_POST['SectionID'] ?? '';

if (isset($_GET['toggle'])) {
    $studentID = (int)$_GET['toggle'];
    $check = $conn->prepare("SELECT IsActive FROM students WHERE students_ID = ?");
    $check->bind_param("i", $studentID);
    $check->execute();
    $resultCheck = $check->get_result();
    if ($resultCheck && $row = $resultCheck->fetch_assoc()) {
        $newStatus = ($row['IsActive'] == 1) ? 0 : 1;
        $stmt = $conn->prepare("UPDATE students SET IsActive = ? WHERE students_ID = ?");
        $stmt->bind_param("ii", $newStatus, $studentID);
        $stmt->execute();
        $statusText = $newStatus == 1 ? "enabled" : "restricted";
        header("Location: removeenrollee.php?swal=1&statusText=" . urlencode($statusText));
        exit();
    } else {
        header("Location: removeenrollee.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selectedYearLevelID && $selectedSectionID) {
    $stmt = $conn->prepare("SELECT s.students_ID, s.LRN, s.FirstName, s.MiddleName, s.LastName,
                                    y.YearName, sec.SectionName, s.IsActive
                            FROM students s
                            LEFT JOIN yearlevels y ON s.YearLevelID = y.yearlevel_ID
                            LEFT JOIN sections sec ON s.SectionID = sec.section_ID
                            WHERE s.YearLevelID = ? AND s.SectionID = ?
                            ORDER BY y.YearName ASC, sec.SectionName ASC");
    $stmt->bind_param("ii", $selectedYearLevelID, $selectedSectionID);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT s.students_ID, s.LRN, s.FirstName, s.MiddleName, s.LastName,
                    y.YearName, sec.SectionName, s.IsActive
            FROM students s
            LEFT JOIN yearlevels y ON s.YearLevelID = y.yearlevel_ID
            LEFT JOIN sections sec ON s.SectionID = sec.section_ID
            ORDER BY y.YearName ASC, sec.SectionName ASC";
    $result = $conn->query($sql);
}
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

        .form-control {
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .table-responsive {
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

        .btn-toggle {
            border: none;
            padding: 5px 15px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-restrict {
            background-color: #dc3545;
            color: white;
        }

        .btn-restrict:hover {
            background-color: #c82333;
            color: white;
        }

        .btn-enable {
            background-color: #28a745;
            color: white;
        }

        .btn-enable:hover {
            background-color: #218838;
        }

        h4.text-center {
            background-color: #0d4b16;
            border-radius: 25px;
            padding: 10px;
            width: 50%;
            color: #ffff;
            margin: 0 auto;
        }

        .btn-outline-light {
            font-family: Arial, Helvetica, sans-serif;
        }

        .btn-icon {
            margin-right: 8px;
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
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                height: auto;
                width: 100%;
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

            .search-form .row {
                flex-direction: column;
                align-items: center;
            }

            .search-form .col-md-5 {
                width: 100%;
                max-width: 300px;
            }

            .search-form .form-control {
                text-align: left;
                margin-bottom: 10px;
            }

            .table-responsive table {
                font-size: 0.85rem;
            }

            .btn-toggle {
                padding: 6px 12px;
                font-size: 0.9rem;
                display: block;
                width: 100%;
                margin-bottom: 5px;
            }

            h4.text-center {
                width: 90%;
                padding: 8px;
                font-size: 1rem;
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

            .form-control {
                font-size: 14px;
            }

            .register-btn {
                font-size: 14px;
                padding: 8px 20px;
            }

            td,
            th {
                font-size: 12px;
                padding: 6px;
            }

            .btn-toggle {
                font-size: 0.85rem;
                padding: 4px 8px;
            }

            h4.text-center {
                width: 100%;
                font-size: 0.9rem;
                padding: 6px;
            }

            .search-form .col-md-5 {
                max-width: 100%;
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

            .form-control {
                font-size: 13px;
                padding: 8px;
            }

            .register-btn {
                font-size: 13px;
                padding: 8px 20px;
            }

            td,
            th {
                font-size: 11px;
                padding: 4px;
            }

            .btn-toggle {
                font-size: 0.8rem;
                padding: 3px 6px;
            }

            h4.text-center {
                font-size: 0.85rem;
                padding: 5px;
            }

            .search-form .form-control {
                font-size: 12px;
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
                        <img src="lnhslogo.png" class="avatar me-2" alt="Admin">
                    </a>
                    <div>
                        <div style="font-size:20px;">Administrator</div>
                        <small><?= htmlspecialchars($_SESSION['admin_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                    </div>
                </div>

                <a href="#collapseStudent" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseStudent">
                    <i class="bi bi-people-fill btn-icon"></i>Student Management
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse show" id="collapseStudent">
                    <a href="addstud.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-person-plus btn-icon"></i>Add Student
                    </a>
                    <a href="docreqs.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-file-earmark-text btn-icon"></i>Document Requests
                    </a>
                    <a href="removeenrollee.php" class="btn btn-outline-light sub-btn active">
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
                        <i class="bi bi-people btn-icon"></i>Teacher Information
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
                    <h4 class="text-center mb-4">Student Status</h4>
                    <form method="POST" class="d-flex flex-column align-items-center search-form">
                        <div class="row w-100 justify-content-center">
                            <div class="col-md-5">
                                <select name="YearLevelID" id="YearLevelID" class="form-control text-center" required>
                                    <option value="" disabled <?= !$selectedYearLevelID ? 'selected' : '' ?>>Select Year Level</option>
                                    <?php
                                    $yearRes = mysqli_query($conn, "SELECT * FROM yearlevels");
                                    while ($row = mysqli_fetch_assoc($yearRes)) {
                                        $selected = ($selectedYearLevelID == $row['yearlevel_ID']) ? 'selected' : '';
                                        echo "<option value='{$row['yearlevel_ID']}' $selected>" . htmlspecialchars($row['YearName']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <select name="SectionID" id="SectionID" class="form-control text-center" required>
                                    <option value="" disabled <?= !$selectedSectionID ? 'selected' : '' ?>>Select Section</option>
                                    <?php
                                    if ($selectedYearLevelID) {
                                        $sectionQuery = mysqli_query($conn, "SELECT * FROM sections WHERE yearlevel_ID = '$selectedYearLevelID'");
                                        while ($sRow = mysqli_fetch_assoc($sectionQuery)) {
                                            $isSelected = ($selectedSectionID == $sRow['section_ID']) ? 'selected' : '';
                                            echo "<option value='{$sRow['section_ID']}' $isSelected>" . htmlspecialchars($sRow['SectionName']) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn register-btn">SEARCH</button>
                        </div>
                    </form>

                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="table-responsive mt-4" style="max-height: 450px; overflow-y: auto;">
                           <table id="studentsTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>LRN</th>
                                        <th>First Name</th>
                                        <th>Middle Name</th>
                                        <th>Last Name</th>
                                        <th>Year Level</th>
                                        <th>Section</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($student = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($student['LRN']); ?></td>
                                            <td><?= htmlspecialchars($student['FirstName']); ?></td>
                                            <td><?= htmlspecialchars($student['MiddleName']); ?></td>
                                            <td><?= htmlspecialchars($student['LastName']); ?></td>
                                            <td><?= htmlspecialchars($student['YearName'] ?? 'N/A'); ?></td>
                                            <td><?= htmlspecialchars($student['SectionName'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge <?= $student['IsActive'] == 1 ? 'bg-success' : 'bg-warning text-dark' ?>">
                                                    <?= $student['IsActive'] == 1 ? 'Active' : 'Restricted' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="removeenrollee.php?toggle=<?= $student['students_ID']; ?>"
                                                    class="btn-toggle <?= $student['IsActive'] == 1 ? 'btn-restrict' : 'btn-enable' ?>"
                                                    data-action="<?= $student['IsActive'] == 1 ? 'restrict' : 'reactivate' ?>">
                                                    <?= $student['IsActive'] == 1 ? 'RESTRICT' : 'REACTIVATE' ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mt-4">No enrolled students found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('YearLevelID').addEventListener('change', function() {
            const yearLevel = this.value;
            fetch('getsections.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'yearlevel=' + encodeURIComponent(yearLevel)
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('SectionID').innerHTML = data;
                });
        });
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-toggle').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const action = this.dataset.action || 'perform this action';
                    const href = this.getAttribute('href');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Do you really want to ${action} this student?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1b5e20',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = href;
                        }
                    });
                });
            });

            <?php if (isset($_GET['swal']) && isset($_GET['statusText'])): 
                $st = htmlspecialchars($_GET['statusText'], ENT_QUOTES, 'UTF-8');
            ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Student has been <?= $st ?>.',
                confirmButtonColor: '#1b5e20'
            }).then(() => {
                window.location.href = 'removeenrollee.php';
            });
            <?php endif; ?>
        });
    </script>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#studentsTable').DataTable({
            "pageLength": 5,
            "lengthChange": false,
            "ordering": true,
            "order": [[4, "asc"], [5, "asc"]], 
            "columnDefs": [
                { "orderable": false, "targets": [6,7] } 
            ]
        });
    });
</script>

</body>

</html>
