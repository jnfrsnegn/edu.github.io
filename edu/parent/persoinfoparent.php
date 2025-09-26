<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['parents_id'])) {
    header("Location: parentlogin.php");
    exit();
}

$parentID = $_SESSION['parents_id'];


$stmt = $conn->prepare("SELECT * FROM parents WHERE parents_ID = ?");
$stmt->bind_param("i", $parentID);
$stmt->execute();
$result = $stmt->get_result();
$parent = $result->fetch_assoc();

if (!$parent) {
    echo "<script>alert('Parent not found.'); window.location.href='parentlogin.php';</script>";
    exit();
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

        .card-info {
            background: #ffffff;
            border: 1px solid #ccc;
            border-radius: 15px;
            padding: 30px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .info-label {
            font-weight: bold;
            color: #333;
        }

        .info-value {
            font-weight: 500;
        }

        .form-section {
            background-color: #fffde7;
            padding: 30px;
            border-radius: 10px;
        }

        h4.text-center {
            background-color: #0d4b16;
            border-radius: 25px;
            padding: 9px;
            width: 50%;
            color: #fff;
            margin: 0 auto;
        }
        .btn-outline-light{
      font-family: Arial, Helvetica, sans-serif;
    }
    </style>
</head>

<body>
    <div class="header">Student Information Management System</div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 sidebar">
                <div class="mb-4 d-flex align-items-center">
                    <a href="parentdash.php" style="text-decoration: none;"><img src="../lnhslogo.png" alt="Parent" class="avatar me-2"></a>
                    <div>
                        <div style="font-size:25px;">Parent</div>
                        <small><?= htmlspecialchars($_SESSION['parents_name']) ?></small>
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
                    <h4 class="mb-4 text-center">Personal Information</h4>
                    <div class="card-info">
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">First Name:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($parent['FirstName']) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Middle Name:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($parent['MiddleName']) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Last Name:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($parent['LastName']) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Sex:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($parent['Sex']) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Birthdate:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($parent['Birthdate']) ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 info-label">Contact Number:</div>
                            <div class="col-sm-8 info-value"><?= htmlspecialchars($parent['ContactNumber']) ?></div>
                        </div>
                        <?php if (isset($parent['Address'])): ?>
                            <div class="row mb-3">
                                <div class="col-sm-4 info-label">Address:</div>
                                <div class="col-sm-8 info-value"><?= htmlspecialchars($parent['Address']) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>

</html>