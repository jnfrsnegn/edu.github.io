<?php
require '../conn.php';

if (isset($_GET['id']) && isset($_GET['action']) && isset($_GET['email']) && isset($_GET['form'])) {
    $requestID = intval($_GET['id']);
    $action = $_GET['action'];
    $email = $_GET['email'];    
    $formType = $_GET['form'];     

    if ($action === 'approve') {
        $status = 'Approved';
    } elseif ($action === 'deny') {
        $status = 'Denied';
    } else {
        header("Location: docreqs.php");
        exit();
    }


    $query = $conn->prepare("
        SELECT s.FirstName, s.MiddleName, s.LastName 
        FROM docreqs d
        JOIN students s ON d.students_ID = s.students_ID
        WHERE d.request_ID = ?
    ");
    $query->bind_param("i", $requestID);
    $query->execute();
    $result = $query->get_result();
    $student = $result->fetch_assoc();

    $fullName = trim($student['FirstName'] . ' ' . $student['MiddleName'] . ' ' . $student['LastName']);


    $stmt = $conn->prepare("UPDATE docreqs SET Status = ? WHERE request_ID = ?");
    $stmt->bind_param("si", $status, $requestID);
    $stmt->execute();


    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Sending Email...</title>
        <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
<script>
    (function(){
        emailjs.init({
            publicKey: "Py-PphJ0-GQ1CxAuN" 
        });
    })();

    emailjs.send("service_cny52jd", "template_1gh4wrb", {
        to_email: "<?php echo $email; ?>",
        name: "<?php echo $fullName; ?>",
        time: "<?php echo date('F j, Y h:i A'); ?>",
        form_type: "<?php echo $formType; ?>",
        message: "Your request for <?php echo $formType; ?> has been <?php echo $status; ?>. And ready for Pickup."
    }).then(function() {
        alert("Email sent to <?php echo $email; ?>");
        window.location.href = "docreqs.php";
    }, function(error) {
        alert("Failed to send email: " + JSON.stringify(error));
        window.location.href = "docreqs.php";
    });
</script>

    </head>
    <body></body>
    </html>
    <?php
    exit();
} else {
    header("Location: docreqs.php");
    exit();
}
?>
