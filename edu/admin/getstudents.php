<?php
require '../conn.php';

if (isset($_POST['section'])) {
    $section_ID = intval($_POST['section']);

    $query = "SELECT * FROM students WHERE SectionID = $section_ID";
    $result = mysqli_query($conn, $query);

    echo '<option value="" disabled selected>Select Student</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='{$row['students_ID']}'>{$row['FirstName']} {$row['LastName']} (LRN: {$row['LRN']})</option>";
    }
}
?>
