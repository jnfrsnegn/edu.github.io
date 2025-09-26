<?php
require '../conn.php';

if (isset($_POST['yearlevel'])) {
    $yearlevel_ID = intval($_POST['yearlevel']);

    $query = "SELECT section_ID, SectionName 
              FROM sections 
              WHERE yearlevel_ID = $yearlevel_ID 
              ORDER BY SectionName ASC";
    $result = mysqli_query($conn, $query);

    echo '<option value="" disabled selected>Select Section</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='{$row['section_ID']}'>{$row['SectionName']}</option>";
    }
}
?>
