<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacherlogin.php");
    exit();
}

$lrnError = "";
$successMessage = "";
$statuses = ["4PS", "IPS", "SNED", "Repeater", "Balik-Aral", "Transferred-In", "Muslim"];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $FirstName = ucwords($_POST['FirstName']);
     $MiddleName = ucwords(isset($_POST['noMiddleName']) ? "N/A" : ($_POST['MiddleName'] ?? ''));
    $LastName = ucwords($_POST['LastName']);
    $Suffix = $_POST['Suffix'];
    $Sex = $_POST['Sex'];
    $Birthdate = $_POST['Birthdate'];
    $LRN = $_POST['LRN'];
    $YearLevelID = $_POST['YearLevelID'];
    $SectionID = $_POST['SectionID'];
    $ContactNumber = $_POST['ContactNumber'];
    $EmailAddress = $_POST['EmailAddress'];
    $Address = ucwords($_POST['Address']);
    $Status = isset($_POST['Status']) ? implode(',', $_POST['Status']) : '';

    $checkQuery = "SELECT * FROM students WHERE LRN = '$LRN'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $lrnError = "This LRN is already registered.";
    } else {
        $query = "INSERT INTO students 
            (FirstName, MiddleName, LastName, Suffix, Sex, Birthdate, LRN, YearLevelID, SectionID, ContactNumber, EmailAddress, Address, Status)
            VALUES 
            ('$FirstName', '$MiddleName', '$LastName', '$Suffix', '$Sex', '$Birthdate', '$LRN', '$YearLevelID', '$SectionID', '$ContactNumber', '$EmailAddress', '$Address', '$Status')";

        if (mysqli_query($conn, $query)) {
            $successMessage = "Student added successfully!";
            $_POST = [];
        } else {
            $lrnError = "Database error: " . mysqli_error($conn);
        }
    }
}

$teacherName = $_SESSION['teacher_name'] ?? '';
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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
            height: 110vh;
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

        .table-responsive {
            overflow-x: auto;
        }

        .form-check-inline {
            margin-right: 15px;
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
                width: 100%;
            }

            .table-responsive {
                font-size: 0.85rem;
            }

            .table th,
            .table td {
                padding: 4px;
                white-space: nowrap;
            }

            .form-check-inline {
                min-width: 100px;
                margin-bottom: 5px;
            }

            .form-check-label {
                font-size: 0.9rem;
            }

            .sidebar .btn {
                font-size: 14px;
            }

            .sidebar .sub-btn {
                width: calc(100% - 10px);
                margin-left: 10px;
                font-size: 13px;
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

            .form-section {
                padding: 10px;
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

            .form-check-inline {
                min-width: 80px;
            }

            .form-check-label {
                font-size: 0.85rem;
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
                 width: 60px;
            height: 60px;
            border-radius: 50%;
            }

            .form-section {
                padding: 8px;
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

            .form-check-inline {
                min-width: 70px;
            }

            .form-check-label {
                font-size: 0.8rem;
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
                     <a href="teacherdash.php" style="text-decoration: none;">
                        <img src="lnhslogo.png" alt="Teacher" class="avatar me-2">
                    </a>
                    <div>
                        <div style="font-size:20px;">Teacher</div>
                        <small><?= htmlspecialchars($teacherName) ?></small>
                    </div>
                </div>
                <a href="#collapseStudents" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseStudents">
                    <i class="bi bi-people btn-icon"></i>Students
                    <i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse show" id="collapseStudents">
                    <a href="addstudteacher.php" class="btn btn-outline-light sub-btn active">
                        <i class="bi bi-person-plus btn-icon"></i>Student Registration
                    </a>
                    <a href="studinfoteach.php" class="btn btn-outline-light sub-btn">
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

                <a href="#collapseAccount" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseAccount">
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
                <div class="form-section">

                    <?php if ($successMessage): ?>
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: "<?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>",
                                showConfirmButton: false,
                                timer: 2000
                            });
                        </script>
                    <?php endif; ?>

                    <?php if ($lrnError && !$successMessage): ?>
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Duplicate LRN',
                                text: "<?= htmlspecialchars($lrnError, ENT_QUOTES, 'UTF-8') ?>",
                            });
                        </script>
                    <?php endif; ?>

                    <form action="addstudteacher.php" method="post" autocomplete="off">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <input type="text" name="FirstName" class="form-control" placeholder="First Name" required value="<?= htmlspecialchars($_POST['FirstName'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <input type="text" id="MiddleName" name="MiddleName" class="form-control" placeholder="Middle Name" value="<?= htmlspecialchars($_POST['MiddleName'] ?? '') ?>">
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" id="noMiddleName" name="noMiddleName"
                                        <?= isset($_POST['noMiddleName']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="noMiddleName">No Middle Name</label>
                                </div>
                            </div>
                            
                            <div class="col-12 col-md-4">
                                <input type="text" name="LastName" class="form-control" placeholder="Last Name" required value="<?= htmlspecialchars($_POST['LastName'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <input type="text" name="Suffix" class="form-control" placeholder="Suffix (e.g., Jr.)" value="<?= htmlspecialchars($_POST['Suffix'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <select name="Sex" class="form-control" required>
                                    <option value="" disabled <?= !isset($_POST['Sex']) ? 'selected' : '' ?>>Select Sex</option>
                                    <option value="Male" <?= (($_POST['Sex'] ?? '') == 'Male') ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= (($_POST['Sex'] ?? '') == 'Female') ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <input type="date" name="Birthdate" class="form-control" required value="<?= htmlspecialchars($_POST['Birthdate'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <input type="text" name="LRN" maxlength="12" pattern="\d{12}" inputmode="numeric" class="form-control <?= $lrnError ? 'is-invalid' : '' ?>" placeholder="LRN (12 digit)" required value="<?= htmlspecialchars($_POST['LRN'] ?? '') ?>">
                                <?php if ($lrnError): ?>
                                    <div class="invalid-feedback"><?= $lrnError ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12 col-md-4">
                                <select name="YearLevelID" id="YearLevelID" class="form-control" required>
                                    <option value="" disabled selected>Select Year Level</option>
                                    <?php
                                    $ylQuery = mysqli_query($conn, "SELECT * FROM yearlevels");
                                    while ($row = mysqli_fetch_assoc($ylQuery)) {
                                        $selected = ($_POST['YearLevelID'] ?? '') == $row['yearlevel_ID'] ? 'selected' : '';
                                        echo "<option value='{$row['yearlevel_ID']}' $selected>{$row['YearName']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <select name="SectionID" id="SectionID" class="form-control" required>
                                    <option value="" disabled selected>Select Section</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <input type="text" name="ContactNumber" maxlength="11" pattern="\d{11}" inputmode="numeric" class="form-control" placeholder="Contact Number" required value="<?= htmlspecialchars($_POST['ContactNumber'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <input type="email" name="EmailAddress" class="form-control" placeholder="name@gmail.com" required value="<?= htmlspecialchars($_POST['EmailAddress'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <input type="text" name="Address" class="form-control" placeholder="Address" required value="<?= htmlspecialchars($_POST['Address'] ?? '') ?>">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Student Status:</label>
                                <div class="status-group d-flex flex-wrap">
                                    <?php foreach ($statuses as $status): ?>
                                        <div class="form-check form-check-inline me-3 mb-2">
                                            <input class="form-check-input" type="checkbox" name="Status[]" value="<?= $status ?>" id="status<?= $status ?>" <?= (isset($_POST['Status']) && in_array($status, $_POST['Status'])) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="status<?= $status ?>"><?= $status ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-center">
                                <button type="submit" name="submit" class="btn register-btn mt-2">REGISTER</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-4">
                        <table id="studentsTable" class="table table-bordered table-striped">

                            <thead>
                                <tr>
                                    <th>LRN</th>
                                    <th>FirstName</th>
                                    <th>MiddleName</th>
                                    <th>LastName</th>
                                    <th>Suffix</th>
                                    <th>Sex</th>
                                    <th>Birthdate</th>
                                    <th>YearLevel</th>
                                    <th>Section</th>
                                    <th>ContactNumber</th>
                                    <th>EmailAddress</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>IsActive</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT s.*, sec.SectionName, yl.YearName 
                    FROM students s
                    LEFT JOIN sections sec ON s.SectionID = sec.section_ID
                    LEFT JOIN yearlevels yl ON s.YearLevelID = yl.yearlevel_ID
                    ORDER BY s.students_ID ASC";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $isActiveText = $row['IsActive'] == 1
                                            ? "<span class='badge bg-success'>Active</span>"
                                            : "<span class='badge bg-danger'>Disabled</span>";

                                        echo "<tr>
                        <td>{$row['LRN']}</td>
                        <td>{$row['FirstName']}</td>
                        <td>{$row['MiddleName']}</td>
                        <td>{$row['LastName']}</td>
                        <td>{$row['Suffix']}</td>
                        <td>{$row['Sex']}</td>
                        <td>{$row['Birthdate']}</td>
                        <td>{$row['YearName']}</td>
                        <td>{$row['SectionName']}</td>
                        <td>{$row['ContactNumber']}</td>
                        <td>{$row['EmailAddress']}</td>
                        <td>{$row['Address']}</td>
                        <td>{$row['Status']}</td>
                        <td>{$isActiveText}</td>
                    </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='15'>No students registered yet.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const middleNameInput = document.getElementById('MiddleName');
        const noMiddleCheckbox = document.getElementById('noMiddleName');

        function toggleMiddleName() {
            if (noMiddleCheckbox.checked) {
                middleNameInput.value = "N/A";
                middleNameInput.disabled = true;
            } else {
                middleNameInput.value = "";
                middleNameInput.disabled = false;
            }
        }

        noMiddleCheckbox.addEventListener('change', toggleMiddleName);

        toggleMiddleName();
    </script>
    <script>
        document.getElementById('YearLevelID').addEventListener('change', function() {
            const yearLevel = this.value;
            fetch('getsectionsteach.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'yearlevel=' + yearLevel
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('SectionID').innerHTML = data;
                });
        });
    </script>
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
 $(document).ready(function() {
            $('#studentsTable').DataTable({
                "pageLength": 5, 
                "lengthChange": false, 
                "ordering": true, 
                "info": true,
                "autoWidth": false
            });
        });
</script>
</body>
</html>