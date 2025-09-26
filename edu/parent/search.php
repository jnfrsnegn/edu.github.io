<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['parents'])) {
    header("Location: parentlogin.php");
    exit();
}

$parentContact = $_SESSION['parents'];


$stmtParent = $conn->prepare("SELECT * FROM parents WHERE ContactNumber = ?");
$stmtParent->bind_param("s", $parentContact);
$stmtParent->execute();
$resultParent = $stmtParent->get_result();
$parentData = $resultParent->fetch_assoc();

$children = [];
if ($parentData) {

    $stmt = $conn->prepare("
    SELECT s.*, yl.YearName, sec.SectionName
    FROM students s
    JOIN parents_students ps ON s.students_ID = ps.students_ID
    JOIN yearlevels yl ON s.YearLevelID = yl.yearlevel_ID
    JOIN sections sec ON s.SectionID = sec.section_ID
    WHERE ps.parents_ID = ? AND s.IsActive = 1 AND ps.Status = 'Approved'
");

    $stmt->bind_param("i", $parentData['parents_ID']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $child = $row;

        $stmt2 = $conn->prepare("
            SELECT sub.SubjectName, g.grade, g.quarter
            FROM grades g
            JOIN subjects sub ON g.subject_ID = sub.subject_ID
            WHERE g.students_ID = ?
        ");
        $stmt2->bind_param("i", $row['students_ID']);
        $stmt2->execute();
        $gradesResult = $stmt2->get_result();

        $gradesBySubject = [];
        while ($g = $gradesResult->fetch_assoc()) {
            $gradesBySubject[$g['SubjectName']][$g['quarter']] = $g['grade'];
        }


        $finalAverages = [];
        foreach ($gradesBySubject as $subject => $quarterGrades) {
            $total = 0;
            $count = 0;
            for ($q = 1; $q <= 4; $q++) {
                if (isset($quarterGrades[$q])) {
                    $total += $quarterGrades[$q];
                    $count++;
                }
            }
            $finalAverages[$subject] = $count > 0 ? round($total / $count, 2) : null;
        }

        $child['grades'] = $gradesBySubject;
        $child['finalAverages'] = $finalAverages;
        $stmt2->close();

        $children[] = $child;
    }
    $stmt->close();
}


$selectedChild = null;
if (!empty($_GET['child_id'])) {
    foreach ($children as $c) {
        if ($c['students_ID'] == $_GET['child_id']) {
            $selectedChild = $c;
            break;
        }
    }
} elseif (!empty($children)) {
    $selectedChild = $children[0];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
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
            height: 100vh;
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

        .form-section {
            background-color: #fffde7;
            padding: 30px;
            border-radius: 10px;
        }

        .card-info {
            background: #ffffff;
            border: 1px solid #ccc;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .scrollable {
            max-height: 450px;
            overflow-y: auto;
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
            width: 70%;
            color: #ffff;
            margin: 0 auto 20px auto;
        }

        table th {
            background-color: #1b5e20;
            color: white;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">Student Information Management System</div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 sidebar">
                <div class="mb-4 d-flex align-items-center">
                    <img src="lnhslogo.png" alt="Parent" class="avatar me-2">
                    <div>
                        <div style="font-size:25px;">Parent</div>
                        <small><?= htmlspecialchars($parentData['FirstName'] . ' ' . $parentData['LastName']) ?></small>
                    </div>
                </div>
                <a href="search.php" class="btn btn-outline-light">Child's Information</a>
                <a href="persoinfoparent.php" class="btn btn-outline-light">Personal Information</a>
                <a href="passman.php" class="btn btn-outline-light">Password Management</a>
                <br><br>
                <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
            </div>

            <div class="col-md-9 p-4">
                <div class="form-section">
                    <?php if (!empty($children)): ?>
                        <div class="mb-4">
                            <label for="childSelect" class="form-label fw-bold">Select Child:</label>
                            <select id="childSelect" class="form-select" onchange="location = '?child_id=' + this.value;">
                                <?php foreach ($children as $c): ?>
                                    <option value="<?= $c['students_ID'] ?>" <?= ($selectedChild && $selectedChild['students_ID'] == $c['students_ID']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['FirstName'] . ' ' . $c['LastName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($selectedChild): ?>
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <h4 class="text-center"><?= htmlspecialchars($selectedChild['FirstName'] . ' ' . $selectedChild['LastName']) ?>'s Information</h4>
                                    <div class="card-info scrollable">
                                        <?php foreach (['FirstName', 'MiddleName', 'LastName', 'Sex', 'Birthdate', 'LRN', 'YearName', 'SectionName', 'ContactNumber', 'EmailAddress', 'Address'] as $field): ?>
                                            <div class="row mb-2">
                                                <div class="col-sm-5 info-label"><?= $field ?>:</div>
                                                <div class="col-sm-7 info-value"><?= htmlspecialchars($selectedChild[$field]) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h4 class="text-center">Grades</h4>
                                    <div class="card-info scrollable">
                                        <?php if (!empty($selectedChild['grades'])): ?>
                                            <table class="table table-striped table-bordered text-center">
                                                <thead>
                                                    <tr>
                                                        <th>Subject</th>
                                                        <th>Quarter 1</th>
                                                        <th>Quarter 2</th>
                                                        <th>Quarter 3</th>
                                                        <th>Quarter 4</th>
                                                        <th>Final Average</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($selectedChild['grades'] as $subject => $quarterGrades): ?>
                                                        <tr>
                                                            <td class="text-start"><?= htmlspecialchars($subject) ?></td>
                                                            <?php for ($q = 1; $q <= 4; $q++): ?>
                                                                <td><?= isset($quarterGrades[$q]) ? htmlspecialchars($quarterGrades[$q]) : '<span class="text-muted">Not graded</span>' ?></td>
                                                            <?php endfor; ?>
                                                            <td><?= $selectedChild['finalAverages'][$subject] !== null ? $selectedChild['finalAverages'][$subject] : '<span class="text-muted">N/A</span>' ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                            <p class="text-center">No grades found for this student.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-danger text-center">No children found for your account.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>