<?php
session_start();
require '../conn.php';

if (!isset($_SESSION['parents_id'])) {
    header("Location: parentlogin.php");
    exit();
}

$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    $parentID = $_SESSION['parents_id'];

    $stmt = $conn->prepare("SELECT Password FROM parents WHERE parents_ID = ?");
    $stmt->bind_param("i", $parentID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        $errorMessage = "Parent not found!";
    } elseif (!empty($row['Password']) && !password_verify($currentPassword, $row['Password'])) {
        $errorMessage = "Current password is incorrect!";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = "New passwords do not match!";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE parents SET Password = ? WHERE parents_ID = ?");
        $update->bind_param("si", $hashedPassword, $parentID);

        if ($update->execute()) {
            $successMessage = "Password updated successfully!";
        } else {
            $errorMessage = "Update failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
            min-height: 300px;
        }

        .form-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 25px;
            color: #1b5e20;
            text-align: center;
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

        .btn-outline-light {
            font-family: Arial, Helvetica, sans-serif;
        }

        h4.text-center {
            background-color: #0d4b16;
            border-radius: 25px;
            padding: 9px;
            width: 50%;
            color: #fff;
            margin: 0 auto;
        }
        
    </style>
</head>

<body>

    <div class="header">Student Information Management System</div>

    <div class="container-fluid">
        <div class="row flex-column flex-md-row">
            <div class="col-12 col-md-3 sidebar">
                <div class="mb-4 d-flex align-items-center">
                    <a href="parentdash.php" style="text-decoration: none;">
                        <img src="../lnhslogo.png" alt="Parent" class="avatar me-2">
                    </a>
                    <div>
                        <div style="font-size:25px;">Parent</div>
                        <small><?= $_SESSION['parents_name'] ?></small>
                    </div>
                </div>

                <a href="search.php" class="btn btn-outline-light">Child's Information</a>
        <a href="persoinfoparent.php" class="btn btn-outline-light">Personal Information</a>
        <a href="passman.php" class="btn btn-outline-light active">Password Management</a>
        <br><br>
                <a href="logout.php" class="logout text-decoration-none" onclick="return confirm('Are you sure you want to log out?');">Logout</a>
            </div>

            <div class="col-12 col-md-9 p-4">
                <div class="form-section">
                    <h4 class="text-center mb-4">Change Your Password</h4>

                    <?php if ($successMessage): ?>
                        <div class="alert alert-success text-center"><?= $successMessage ?></div>
                    <?php elseif ($errorMessage): ?>
                        <div class="alert alert-danger text-center"><?= $errorMessage ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-6">
                                <input type="password" name="currentPassword" class="form-control" placeholder="Enter Current Password" required>
                                <input type="password" name="newPassword" class="form-control" placeholder="Enter New Password" required>
                                <input type="password" name="confirmPassword" class="form-control" placeholder="Confirm New Password" required>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn register-btn mt-2">UPDATE PASSWORD</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>