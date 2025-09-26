<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

if (isset($_GET['id'])) {
    $section_ID = $_GET['id'];


    mysqli_begin_transaction($conn);

    try {
        
        mysqli_query($conn, "
            DELETE ts
            FROM teacher_subjects ts
            JOIN subjects s ON ts.subject_ID = s.subject_ID
            WHERE s.SectionID = '$section_ID'
        ");


        mysqli_query($conn, "DELETE FROM subjects WHERE SectionID = '$section_ID'");

        mysqli_query($conn, "UPDATE students SET SectionID = NULL WHERE SectionID = '$section_ID'");

        mysqli_query($conn, "DELETE FROM sections WHERE section_ID='$section_ID'");

        mysqli_commit($conn);

        header("Location: managesections.php?success=Section+deleted+successfully");
        exit();
    } catch (Exception $e) {
 
        mysqli_rollback($conn);
        echo "Error deleting section: " . $e->getMessage();
    }
} else {
    header("Location: managesections.php");
    exit();
}
?>
