<?php
require '../conn.php';
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

$lrnError = "";
$successMessage = "";

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


    $checkStmt = $conn->prepare("SELECT * FROM students WHERE LRN = ?");
    $checkStmt->bind_param("s", $LRN);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $lrnError = "This LRN is already registered.";
    } else {
        $AttachmentPath = "";
        if (isset($_FILES['Attachment']) && $_FILES['Attachment']['error'] == 0) {
            $targetDir = "../Downloads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $fileName = time() . "_" . basename($_FILES["Attachment"]["name"]);
            $targetFile = $targetDir . $fileName;
            if (move_uploaded_file($_FILES["Attachment"]["tmp_name"], $targetFile)) {
                $AttachmentPath = "Downloads/" . $fileName;
            }
        }

        $insertStmt = $conn->prepare("INSERT INTO students 
(FirstName, MiddleName, LastName, Suffix, Sex, Birthdate, LRN, YearLevelID, SectionID, ContactNumber, EmailAddress, Address, Status, Attachment)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("ssssssssisssss", $FirstName, $MiddleName, $LastName, $Suffix, $Sex, $Birthdate, $LRN, $YearLevelID, $SectionID, $ContactNumber, $EmailAddress, $Address, $Status, $AttachmentPath);



        if ($insertStmt->execute()) {
            $successMessage = "Student added successfully!";
            $_POST = [];
        } else {
            $lrnError = "Database error: " . $insertStmt->error;
        }
    }
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
            color: white;
            padding: 15px;
            height: 110h;
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



        .container-fluid>.row {
            display: flex;
            flex-wrap: nowrap;
            align-items: stretch;
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

        .status-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
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

            .table-responsive table {
                font-size: 0.85rem;
            }

            .table th,
            .table td {
                padding: 4px;
                white-space: nowrap;
            }

            .status-group {
                justify-content: flex-start;
            }

            .form-check-inline {
                min-width: 100px;
                margin-bottom: 5px;
            }

            .form-check-label {
                font-size: 0.9rem;
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
            }

            td,
            th {
                font-size: 12px;
            }

            .status-group {
                gap: 8px;
            }

            .form-check-inline {
                min-width: 80px;
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
            }

            .form-check-label {
                font-size: 0.8rem;
            }

            .form-check-inline {
                min-width: 70px;
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
                        <small><?= $_SESSION['admin_name'] ?? '' ?></small>
                    </div>
                </div>
                <a href="#collapseStudent" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="true"
                    aria-controls="collapseStudent">
                    <i class="bi bi-people-fill btn-icon"></i>Student Management<i class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse show" id="collapseStudent">
                    <a href="addstud.php" class="btn btn-outline-light sub-btn active"><i
                            class="bi bi-person-plus btn-icon"></i>Add Student</a>
                    <a href="docreqs.php" class="btn btn-outline-light sub-btn"><i
                            class="bi bi-file-earmark-text btn-icon"></i>Document Requests</a>
                    <a href="removeenrollee.php" class="btn btn-outline-light sub-btn"><i
                            class="bi bi-person-x btn-icon"></i>Student Status</a>
                </div>
                <a href="#collapseInfo" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false"
                    aria-controls="collapseInfo">
                    <i class="bi bi-info-circle-fill btn-icon"></i>Manage Informations<i
                        class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse" id="collapseInfo">
                    <a href="studinfo.php" class="btn btn-outline-light sub-btn"><i
                            class="bi bi-people btn-icon"></i>Student Information</a>
                    <a href="teachinfo.php" class="btn btn-outline-light sub-btn"><i
                            class="bi bi-person-badge btn-icon"></i>Teacher Information</a>
                    <a href="persoinfo.php" class="btn btn-outline-light sub-btn"><i
                            class="bi bi-person btn-icon"></i>Personal Information</a>
                    <a href="passmanage.php" class="btn btn-outline-light sub-btn"><i
                            class="bi bi-lock btn-icon"></i>Password Management</a>
                </div>
                <a href="#collapseTeacher" class="btn btn-outline-light" data-bs-toggle="collapse" aria-expanded="false"
                    aria-controls="collapseTeacher">
                    <i class="bi bi-person-badge-fill btn-icon"></i>Teacher Management<i
                        class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse" id="collapseTeacher">
                    <a href="regteach.php" class="btn btn-outline-light sub-btn"><i
                            class="bi bi-person-plus btn-icon"></i>Register Teachers</a>
                    <a href="assignteacher.php" class="btn btn-outline-light sub-btn"><i
                            class="bi bi-arrow-right-circle btn-icon"></i>Assign Teacher</a>
                </div>
                <a href="#collapseAcademic" class="btn btn-outline-light" data-bs-toggle="collapse"
                    aria-expanded="false" aria-controls="collapseAcademic">
                    <i class="bi bi-journal-bookmark-fill btn-icon"></i>Subjects & Sections<i
                        class="bi bi-chevron-right"></i>
                </a>
                <div class="collapse" id="collapseAcademic">
                    <a href="addsubject.php" class="btn btn-outline-light sub-btn"><i
                            class="bi bi-journal-plus btn-icon"></i>Add Subject</a>
                    <a href="managesections.php" class="btn btn-outline-light sub-btn"><i
                            class="bi bi-gear btn-icon"></i>Manage Sections</a>
                </div>
                <a href="viewrep.php" class="btn btn-outline-light"><i class="bi bi-bar-chart-fill btn-icon"></i>View
                    Reports</a>
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

                    <?php $statuses = ["4PS", "IPS", "SNED", "Repeater", "Balik-Aral", "Transferred-In", "Muslim"]; ?>
                    <form method="post" action="addstud.php" autocomplete="off" enctype="multipart/form-data">
                        <div class="row">

                            <div class="col-12 col-md-4"><input type="text" name="FirstName" class="form-control"
                                    placeholder="First Name" required
                                    value="<?= htmlspecialchars($_POST['FirstName'] ?? '') ?>"></div>
                            <div class="col-12 col-md-4">
                                <input type="text" name="MiddleName" id="MiddleName" class="form-control"
                                    placeholder="Middle Name" required value="<?= htmlspecialchars($_POST['MiddleName'] ?? '') ?>">
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" id="noMiddleName" name="noMiddleName"
                                        <?= isset($_POST['noMiddleName']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="noMiddleName">No Middle Name</label>
                                </div>
                            </div>

                            <div class="col-12 col-md-4"><input type="text" name="LastName" class="form-control"
                                    placeholder="Last Name" required
                                    value="<?= htmlspecialchars($_POST['LastName'] ?? '') ?>"></div>
                            <div class="col-12 col-md-4"><input type="text" name="Suffix" class="form-control"
                                    placeholder="Suffix" value="<?= htmlspecialchars($_POST['Suffix'] ?? '') ?>"></div>
                            <div class="col-12 col-md-4"><select name="Sex" class="form-control" required>
                                    <option value="" disabled <?= !isset($_POST['Sex']) ? 'selected' : '' ?>>Select Sex
                                    </option>
                                    <option value="Male" <?= (($_POST['Sex'] ?? '') == 'Male') ? 'selected' : '' ?>>Male
                                    </option>
                                    <option value="Female" <?= (($_POST['Sex'] ?? '') == 'Female') ? 'selected' : '' ?>>
                                        Female</option>
                                </select></div>
                            <div class="col-12 col-md-4"><input type="date" name="Birthdate" class="form-control"
                                    required value="<?= htmlspecialchars($_POST['Birthdate'] ?? '') ?>"></div>
                            <div class="col-12 col-md-4"><input type="text" name="LRN" maxlength="12" pattern="\d{12}"
                                    inputmode="numeric" class="form-control <?= $lrnError ? 'is-invalid' : '' ?>"
                                    placeholder="LRN (12 digit)" required
                                    value="<?= htmlspecialchars($_POST['LRN'] ?? '') ?>"><?php if ($lrnError): ?>
                                    <div class="invalid-feedback"><?= $lrnError ?></div><?php endif; ?>
                            </div>
                            <div class="col-12 col-md-4"><select name="YearLevelID" id="YearLevelID"
                                    class="form-control" required>
                                    <option value="" disabled selected>Select Year Level</option><?php $ylQuery = mysqli_query($conn, "SELECT * FROM yearlevels");
                                                                                                    while ($row = mysqli_fetch_assoc($ylQuery)) {
                                                                                                        $selected = (($_POST['YearLevelID'] ?? '') == $row['yearlevel_ID']) ? 'selected' : '';
                                                                                                        echo "<option value='{$row['yearlevel_ID']}' $selected>{$row['YearName']}</option>";
                                                                                                    } ?>
                                </select></div>
                            <div class="col-12 col-md-4"><select name="SectionID" id="SectionID" class="form-control"
                                    required>
                                    <option value="" disabled selected>Select Section</option>
                                </select></div>
                            <div class="col-12 col-md-4"><input type="text" name="ContactNumber" maxlength="11"
                                    pattern="\d{11}" inputmode="numeric" class="form-control"
                                    placeholder="Contact Number" required
                                    value="<?= htmlspecialchars($_POST['ContactNumber'] ?? '') ?>"></div>
                            <div class="col-12 col-md-4"><input type="email" name="EmailAddress" class="form-control"
                                    placeholder="name@gmail.com" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                    required value="<?= htmlspecialchars($_POST['EmailAddress'] ?? '') ?>"></div>
                            <div class="col-12 col-md-4"><input type="text" name="Address" class="form-control"
                                    placeholder="Address" required
                                    value="<?= htmlspecialchars($_POST['Address'] ?? '') ?>"></div>


                            <div class="col-12 col-md-4">
                                <label class="form-label fw-bold">Attach File:</label>
                                <input type="file" name="Attachment" class="form-control" accept=".pdf,.jpg,.png,.jpeg,.doc,.docx">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Student Status:</label>
                                <div class="status-group">
                                    <?php foreach ($statuses as $status): ?>
                                        <div class="form-check form-check-inline"><input class="form-check-input"
                                                type="checkbox" name="Status[]" value="<?= $status ?>" id="status<?= $status ?>"
                                                <?= (isset($_POST['Status']) && in_array($status, $_POST['Status'])) ? 'checked' : '' ?>><label class="form-check-label"
                                                for="status<?= $status ?>"><?= $status ?></label></div><?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-center"><button type="submit" name="submit"
                                    class="btn register-btn mt-2">REGISTER</button></div>
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
                                    <th>Attachment</th>
                                    <th>Status</th>
                                    <th>IsActive</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT s.*, sec.SectionName, yl.YearName FROM students s LEFT JOIN sections sec ON s.SectionID=sec.section_ID LEFT JOIN yearlevels yl ON s.YearLevelID=yl.yearlevel_ID ORDER BY s.students_ID ASC";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $isActiveText = $row['IsActive'] == 1
                                            ? "<span class='badge bg-success'>Active</span>"
                                            : "<span class='badge bg-danger'>Disabled</span>";

                                        $attachment = $row['Attachment'];
                                        if (!empty($attachment)) {
                                            $filePath = "../Downloads/" . htmlspecialchars(basename($attachment));
                                            $attachmentLink = "<a href='$filePath' target='_blank'>View File</a>";
                                        } else {
                                            $attachmentLink = "No file";
                                        }
                                        echo " <tr>
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
                                                    <td>$attachmentLink</td>
                                                    <td>{$row['Status']}</td>
                                                    <td>$isActiveText</td>
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
            fetch('getsections.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'yearlevel=' + yearLevel
                })
                .then(res => res.text()).then(data => {
                    document.getElementById('SectionID').innerHTML = data;
                });
        });
    </script>
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