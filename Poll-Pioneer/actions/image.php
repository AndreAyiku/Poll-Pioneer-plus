<?php
include '../db/config.php';

if (isset($_GET['id'])) {
    $poll_id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("SELECT PollImage FROM PP_Polls WHERE PollID = ?");
    $stmt->bind_param("i", $poll_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if ($row['PollImage']) {
            header("Content-Type: image/jpeg");
            echo $row['PollImage'];
            exit;
        }
    }
}

// If no image found or error, serve a default placeholder
header("Content-Type: image/png");
readfile("../assets/images/poll-image.png");
?>