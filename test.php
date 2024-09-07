<?php
// Path to your image
$imagePath = 'https://www.trucsr.in/skin/images/favicon.png';

// Get the image data and encode it in Base64
$imageData = base64_encode(file_get_contents($imagePath));

// Output the image with Base64 encoding
echo '<img src="data:image/png;base64,' . $imageData . '" width="10" height="10" alt="Download PDF">';

include("config.php");

$servername = HOST;
$username = USERNAME;
$password = DB_PASS;
$dbname = DB_NAME;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM email_draft LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $subject = $row['subject'];
    $htmlContent = $row['body'];
    $edit = str_replace('white-space: pre-wrap;', 'white-space: normal;', $htmlContent);
    $body = $edit;
    $attachment_path = $row['attachment_path'];
    $filename = basename($attachment_path);
    $fromname = $row['from_name'];
    $fromemail = $row['from_email'];
    $campainname = $row['campaign_name'];
} else {
    echo "No data found.";
}
$recipient_id = urlencode("taufiqsurti014@gmail.com");
$tracking_url = URL . "track.php?id=" . $recipient_id;
echo $tracking_url;

$conn->close();
?>