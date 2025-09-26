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
    $SectionName = $_POST['SectionName'];
    $YearLevelID = $_POST['YearLevelID'];

    if (empty($SectionName) || empty($YearLevelID)) {
        $errorMessage = "Both Section Name and Year Level are required.";
    } else {

        $check = mysqli_query($conn, "SELECT * FROM sections WHERE SectionName='$SectionName' AND yearlevel_ID='$YearLevelID'");
        if (mysqli_num_rows($check) > 0) {
            $errorMessage = "This section already exists for the selected Year Level.";
        } else {
            $insert = mysqli_query($conn, "INSERT INTO sections (SectionName, yearlevel_ID) VALUES ('$SectionName', '$YearLevelID')");
            if ($insert)
                $successMessage = "Section added successfully!";
            else
                $errorMessage = "Database error: " . mysqli_error($conn);
        }
    }
}


if (isset($_POST['edit_section'])) {
    $section_ID = $_POST['section_ID'];
    $SectionName = $_POST['SectionName'];
    if (empty($SectionName)) {
        $errorMessage = "Section name cannot be empty.";
    } else {
        $update = mysqli_query($conn, "UPDATE sections SET SectionName='$SectionName' WHERE section_ID='$section_ID'");
        if ($update)
            $successMessage = "Section updated successfully!";
        else
            $errorMessage = "Database error: " . mysqli_error($conn);
    }
}


$yearlevels = mysqli_query($conn, "SELECT * FROM yearlevels ORDER BY CAST(SUBSTRING(YearName, 7) AS UNSIGNED)");

$filterYearID = $_GET['yearlevel'] ?? '';

$sql = "
    SELECT sec.section_ID, sec.SectionName, yl.YearName, yl.yearlevel_ID
    FROM sections sec
    LEFT JOIN yearlevels yl ON sec.yearlevel_ID = yl.yearlevel_ID
";
if ($filterYearID) {
    $sql .= " WHERE sec.yearlevel_ID = '$filterYearID'";
}
$sql .= " ORDER BY CAST(SUBSTRING(yl.YearName,7) AS UNSIGNED), sec.SectionName";
$sections = mysqli_query($conn, $sql);
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
                    <a href="admindash.php" style="text-decoration: none;"><img src="lnhslogo.png" alt="Admin"
                            class="avatar me-2"></a>
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
                <a href="addsubject.php" class="btn btn-outline-light">Add Subject</a>
                <a href="managesections.php" class="btn btn-outline-light active">Manage Sections</a>
                <br><br>
                <a href="logout.php" class="logout text-decoration-none"
                    onclick="return confirm('Are you sure you want to log out?');">Logout</a>
            </div>

            <div class="col-12 col-md-9 p-4">
                <div class="form-section">

                    <?php if ($successMessage): ?>
                        <div class="alert alert-success text-center"><?= $successMessage ?></div>
                    <?php endif; ?>
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger text-center"><?= $errorMessage ?></div>
                    <?php endif; ?>


                    <h4>Add Section</h4>
                    <form method="post" class="mb-4">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <input type="text" name="SectionName" class="form-control" placeholder="Section Name"
                                    required>
                            </div>
                            <div class="col-12 col-md-6">
                                <select name="YearLevelID" class="form-control" required>
                                    <option value="" disabled selected>Select Year Level</option>
                                    <?php
                                    mysqli_data_seek($yearlevels, 0);
                                    while ($yl = mysqli_fetch_assoc($yearlevels)) {
                                        echo "<option value='{$yl['yearlevel_ID']}'>{$yl['YearName']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-center mt-2">
                                <button type="submit" name="add_section" class="btn register-btn">Add Section</button>
                            </div>
                        </div>
                    </form>

                    <form method="get" class="mb-3">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <select name="yearlevel" class="form-control" onchange="this.form.submit()">
                                    <option value="">Select Year Level</option>
                                    <?php
                                    mysqli_data_seek($yearlevels, 0);
                                    while ($yl = mysqli_fetch_assoc($yearlevels)) {
                                        $selected = ($yl['yearlevel_ID'] == $filterYearID) ? 'selected' : '';
                                        echo "<option value='{$yl['yearlevel_ID']}' $selected>{$yl['YearName']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </form>

                    <?php if ($filterYearID): ?>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Section Name</th>
                                        <th>Year Level</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($sections) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($sections)): ?>
                                            <tr>
                                                <td>
                                                    <form method="post" class="d-flex justify-content-center"
                                                        onsubmit="return confirm('Are you sure you want to update this section?');">
                                                        <input type="text" name="SectionName"
                                                            value="<?= htmlspecialchars($row['SectionName']) ?>"
                                                            class="form-control" style="width: 150px;" required>
                                                        <input type="hidden" name="section_ID" value="<?= $row['section_ID'] ?>">
                                                        <button type="submit" name="edit_section"
                                                            class="btn register-btn ms-2">Update</button>
                                                    </form>
                                                </td>
                                                <td><?= htmlspecialchars($row['YearName']) ?></td>
                                                <td>
                                                    <a href="deletesection.php?id=<?= $row['section_ID'] ?>"
                                                        class="btn register-btn" style="background-color:red;"
                                                        onclick="return confirm('Delete this section?');">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3">No sections found for this Year Level.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

</body>

</html>