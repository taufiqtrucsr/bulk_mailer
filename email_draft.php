<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testing";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve current values from the database
$sql = "SELECT subject, body, attachment_path FROM email_draft WHERE id = 1 LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_subject = $row['subject'];
    $current_body = $row['body'];
    $current_attachment_path = $row['attachment_path'];
} else {
    // Handle the case where no row exists
    die("No draft found.");
}

// Get POST data and use current values if new data is not provided
$subject = !empty($_POST['subject']) ? filter_var($_POST['subject'], FILTER_SANITIZE_STRING) : $current_subject;
$body = !empty($_POST['body']) ? filter_var($_POST['body'], FILTER_SANITIZE_STRING) : $current_body;

// Handle file upload
$attachment_path = $current_attachment_path;
if (!empty($_FILES['attachment']['name'])) {
    $target_dir = "C:\\Users\\neera\\Downloads\\";
    $attachment_path = $target_dir . basename($_FILES['attachment']['name']);
    move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path);
}

// Update the row with new or existing data
$sql = "UPDATE email_draft SET subject = ?, body = ?, attachment_path = ? WHERE id = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $subject, $body, $attachment_path);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Draft saved successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to save draft"]);
}

$stmt->close();
$conn->close();
?>
