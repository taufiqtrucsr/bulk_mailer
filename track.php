<?php
$email_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($email_id) {
    // Log the email open event
    file_put_contents('email_opens.log', $email_id . " - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
}

// Serve a 1x1 transparent pixel
header('Content-Type: image/png');
readfile('$tracking_url');
exit();