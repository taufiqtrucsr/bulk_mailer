<?php
include("connection.php");

// Retrieve current values from the database
$sql = "SELECT MAX(id) AS idno, subject, body, attachment_path, from_name, from_email, campaign_name FROM email_draft";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id = $row['idno']+1;
    $current_subject = $row['subject'];
    $current_body = $row['body'];
    $current_attachment_path = $row['attachment_path'];
    $current_fromname = $row['from_name'];
    $current_fromemail = $row['from_email'];
    $current_campainname = $row['campaign_name'];
} else {
    // Handle the case where no row exists
    die("No draft found.");
}

// Get POST data and use current values if new data is not provided
$subject = !empty($_POST['subject']) ? filter_var($_POST['subject'], FILTER_SANITIZE_STRING) : $current_subject;
$body = !empty($_POST['body']) ? $_POST['body'] : $current_body;
$fromname = !empty($_POST['fromname']) ? filter_var($_POST['fromname'], FILTER_SANITIZE_STRING) : $current_fromname;
$fromemail = !empty($_POST['fromemail']) ? filter_var($_POST['fromemail'], FILTER_SANITIZE_STRING) : $current_fromemail;
$campainname = !empty($_POST['campaingname']) ? filter_var($_POST['campaingname'], FILTER_SANITIZE_STRING) : $current_campainname;

// Handle file upload
$attachment_path = $current_attachment_path;
if (!empty($_FILES['attachment']['name'])) {
    $target_dir = TARGET_DIR;
    $attachment_path = $target_dir . basename($_FILES['attachment']['name']);
    move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path);
}

$sql = "INSERT INTO email_draft (id, subject, body, attachment_path, from_name, from_email, campaign_name) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssss", $id, $subject, $body, $attachment_path, $fromname, $fromemail, $campainname);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Draft saved successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to save draft"]);
}

$stmt->close();
$conn->close();
