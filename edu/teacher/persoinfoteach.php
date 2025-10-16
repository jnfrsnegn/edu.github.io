<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacherlogin.php");
    exit();
}

$teacherID = $_SESSION['teacher_id'];

$sql = "
    SELECT 
        t.teachers_ID,
        t.FirstName, t.MiddleName, t.LastName, t.Suffix,
        t.Sex, t.Birthdate, t.EmployeeID, t.ContactNumber,t.EmailAddress, t.Address,
        GROUP_CONCAT(DISTINCT y.YearName ORDER BY y.YearName SEPARATOR ', ') AS YearLevels,
        GROUP_CONCAT(DISTINCT s.SectionName ORDER BY s.SectionName SEPARATOR ', ') AS Sections
    FROM teachers t
    LEFT JOIN teacher_subjects ts ON ts.teachers_ID = t.teachers_ID
    LEFT JOIN subjects sub        ON sub.subject_ID   = ts.subject_ID
    LEFT JOIN yearlevels y        ON y.yearlevel_ID   = sub.YearLevelID
    LEFT JOIN sections s          ON s.section_ID     = sub.SectionID
    WHERE t.teachers_ID = ?
    GROUP BY t.teachers_ID
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacherID);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
$stmt->close();

if (!$teacher) {
    echo "<script>alert('Teacher not found.'); window.location.href='teacherlogin.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMS - Personal Information</title>
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

        /* Info Card */
        .card-info {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 15px;
            padding: 30px;
            max-width: 700px;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .info-label {
            font-weight: bold;
            color: #333;
        }

        .info-value {
            font-weight: 500;
        }

        h4.text-center {
            background-color: #0d4b16;
            border-radius: 25px;
            padding: 9px;
            width: 50%;
            color: #fff;
            margin: 0 auto 20px;
        }

        .form-section {
            background-color: #fffde7;
            padding: 30px;
            border-radius: 10px;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 992px) {
            .sidebar {
                height: auto;
                padding: 10px;
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

            .header {
                font-size: 18px;
            }

            .avatar {
                width: 50px;
                height: 50px;
            }

            .card-info {
                padding: 20px;
                margin: 10px;
            }

            .card-info .row {
                flex-direction: column;
            }

            .info-label {
                font-size: 14px;
                margin-bottom: 3px;
            }

            .info-value {
                font-size: 15px;
            }

            h4.text-center {
                width: 90%;
                font-size: 17px;
            }

            .logout {
                font-size: 14px;
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
                font-size: 13px;
                padding: 8px;
            }

            .sidebar .sub-btn {
                font-size: 12px;
                padding: 6px;
            }

            .avatar {
                width: 40px;
                height: 40px;
            }

            h4.text-center {
                width: 100%;
                font-size: 15px;
            }

            .logout {
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
    <div class="header">Student Information Management System</div>

    <div class="container-fluid">
        <div class="row flex-column flex-md-row">
            <!-- Sidebar -->
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

                <a href="#collapseStudents" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseStudents">
                    <i class="bi bi-people btn-icon"></i>Students
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse" id="collapseStudents">
                    <a href="addstudteacher.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-person-plus btn-icon"></i>Student Registration
                    </a>
                    <a href="manageteach.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-person-gear btn-icon"></i>Student Informations
                    </a>
                </div>

                <a href="#collapseGrades" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseGrades">
                    <i class="bi bi-clipboard-data btn-icon"></i>Grades
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse" id="collapseGrades">
                    <a href="gradesmanage.php" class="btn btn-outline-light sub-btn">
                        <i class="bi bi-clipboard-data btn-icon"></i>Grades Management
                    </a>
                </div>

                <a href="#collapseAccount" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseAccount">
                    <i class="bi bi-person-circle btn-icon"></i>Account
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse show" id="collapseAccount">
                    <a href="persoinfoteach.php" class="btn btn-outline-light sub-btn active">
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
            <div class="col-md-9 col-12 p-4">
                <div class="form-section">
                    <h4 class="text-center">Personal Information</h4>
                    <div class="card-info">
                        <?php
                        $fullName = trim(($teacher['FirstName'] ?? '') . ' ' . ($teacher['MiddleName'] ?? '') . ' ' . ($teacher['LastName'] ?? '') . ' ' . ($teacher['Suffix'] ?? ''));
                        ?>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Name:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($fullName) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Sex:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['Sex'] ?? '') ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Birthdate:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['Birthdate'] ?? '') ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Employee ID:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['EmployeeID'] ?? '') ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Contact Number:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['ContactNumber'] ?? '') ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Email Address</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['EmailAddress'] ?? '') ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Address:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['Address'] ?? '') ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Year Level:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['YearLevels'] ?? 'Not Assigned') ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Section:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($teacher['Sections'] ?? 'Not Assigned') ?></div>
                        </div>
                    </div>
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