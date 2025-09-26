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
    $YearLevelID = $_POST['YearLevelID'];
    $SectionID = $_POST['SectionID'];

    if (empty($SubjectName) || empty($YearLevelID) || empty($SectionID)) {
        $errorMessage = "All fields are required.";
    } else {

        $normalizedSubject = strtolower(str_replace(' ', '', $SubjectName));


        $checkQuery = "
            SELECT *
            FROM subjects
            WHERE YearLevelID='$YearLevelID'
              AND SectionID='$SectionID'
              AND REPLACE(LOWER(SubjectName), ' ', '') = '$normalizedSubject'
        ";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $errorMessage = "This subject already exists for the selected Year Level and Section.";
        } else {

            $formattedSubject = ucwords(strtolower($SubjectName));
            $insertQuery = "
                INSERT INTO subjects (SubjectName, YearLevelID, SectionID)
                VALUES ('$formattedSubject', '$YearLevelID', '$SectionID')
            ";
            if (mysqli_query($conn, $insertQuery)) {
                $successMessage = "Subject added successfully!";
                $_POST = [];
            } else {
                $errorMessage = "Database error: " . mysqli_error($conn);
            }
        }
    }
}


$yearlevelsQuery = mysqli_query($conn, "SELECT * FROM yearlevels ORDER BY CAST(SUBSTRING(YearName,7) AS UNSIGNED)");


$subjectsQuery = "
    SELECT s.subject_ID, s.SubjectName, yl.YearName, sec.SectionName
    FROM subjects s
    LEFT JOIN yearlevels yl ON s.YearLevelID = yl.yearlevel_ID
    LEFT JOIN sections sec ON s.SectionID = sec.section_ID
    ORDER BY CAST(SUBSTRING(yl.YearName,7) AS UNSIGNED), sec.SectionName, s.SubjectName
";
$subjectsResult = mysqli_query($conn, $subjectsQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5dc;

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
            background-color: #a8aa10ff;
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

        td,
        th {
            padding: 8px;
            text-align: center;
        }

        table {
            margin-top: 20px;
        }
        .btn-outline-light {
      font-family: Arial, Helvetica, sans-serif;
    }
    </style>
</head>

<body>

    <div class="header">Student Information Management System</div>
    <div class="container-fluid">
        <div class="row flex-column flex-md-row">
            <div class="col-12 col-md-3 sidebar">
                <div class="mb-4 d-flex align-items-center">
                    <a href="admindash.php" style="text-decoration: none;"><img src="lnhslogo.png" alt="Admin" class="avatar me-2"></a>
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
                <a href="assignteacher.php" class="btn btn-outline-light">Assign Teacher</a>
                <a href="regpar.php" class="btn btn-outline-light">Register Parents</a>
                <a href="addsubject.php" class="btn btn-outline-light active">Add Subject</a>
                <a href="managesections.php" class="btn btn-outline-light">Manage Sections</a>
                <br><br>
                <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
            </div>

            <div class="col-12 col-md-9 p-4">
                <div class="form-section">
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success text-center"><?= $successMessage ?></div>
                    <?php endif; ?>
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger text-center"><?= $errorMessage ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <input type="text" name="SubjectName" class="form-control" placeholder="Subject Name" required value="<?= htmlspecialchars($_POST['SubjectName'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <select name="YearLevelID" id="YearLevelID" class="form-control" required>
                                    <option value="" disabled selected>Select Year Level</option>
                                    <?php
                                    mysqli_data_seek($yearlevelsQuery, 0);
                                    while ($row = mysqli_fetch_assoc($yearlevelsQuery)) {
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
                            <div class="col-12 d-flex justify-content-center">
                                <button type="submit" class="btn register-btn mt-2">ADD SUBJECT</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-4" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Subject Name</th>
                                    <th>Year Level</th>
                                    <th>Section</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1; ?>
                                <?php if (mysqli_num_rows($subjectsResult) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($subjectsResult)): ?>
                                        <tr>
                                            <td><?= $counter++ ?></td>
                                            <td><?= htmlspecialchars(ucwords(strtolower($row['SubjectName']))) ?></td>
                                            <td><?= htmlspecialchars($row['YearName']) ?></td>
                                            <td><?= htmlspecialchars($row['SectionName']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">No subjects added yet.</td>
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
        document.getElementById('YearLevelID').addEventListener('change', function() {
            const yearLevel = this.value;
            fetch('getsections.php', {
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

</body>

</html>