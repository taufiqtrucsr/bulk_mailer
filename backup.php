<?php
// include("connection.php");

$sql = "SELECT MAX(Sr_no) AS last_srno FROM bounce_mail";
$stmt = $conn->query($sql);

if ($stmt->num_rows > 0) {
    $row = $stmt->fetch_assoc();
    $srno = $row['last_srno'] + 1;
}
$sql = "SELECT * FROM email_draft ORDER BY id DESC LIMIT 1";
$emailresult = $conn->query($sql);

if ($emailresult->num_rows > 0) {
    $row = $emailresult->fetch_assoc();
    $fromemail = $row['from_email'];
}

$inbox = imap_open("{imap.gmail.com:993/imap/ssl}INBOX", $fromemail, PASSWORD);
if (!$inbox) {
    die("Failed to connect to IMAP server: " . imap_last_error());
}

$today = date('d-M-Y');
$emails = imap_search($inbox, 'FROM "mailer-daemon@googlemail.com" UNSEEN SINCE "' . $today . '"');

if ($emails) {
    $batches = 100;
    $totalmails = count($emails);
    for ($i = 0; $i < $totalEmails; $i += $batches) {
        $batc = array_slice($emails, $i, $batches);
        foreach ($batc as $email_number) {
            $overview = imap_fetch_overview($inbox, $email_number, 0);
            $header = imap_fetchheader($inbox, $email_number);
            $message = imap_fetchbody($inbox, $email_number, 1);
            $srno++;

            preg_match('/X-Failed-Recipients: (.+)/', $header, $id_matches);
            if (empty($id_matches)) {
                preg_match('/X-Failed-Recipients:\s*(.+)/i', $header, $id_matches);
            }
            $email_id = isset($id_matches[1]) ? trim($id_matches[1]) : 'Unknown';

            preg_match('/\*\* (.+) \*\*/', $message, $matches);
            if (empty($matches)) {
                preg_match('/\*\* \s*(.+)/i', $message, $matches);
            }
            $reason = isset($matches[1]) ? trim($matches[1]) : 'Unknown';

            $sql = "INSERT INTO bounce_mail (Sr_no, Bounce_reason, email_id) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }
            $stmt->bind_param("iss", $srno, $reason, $email_id);

            if ($stmt->execute()) {
                // echo "New record created successfully.\n";
            } else {
                echo "Error: " . $stmt->error . "\n";
            }

            $stmt->close();
            imap_delete($inbox, $email_number);
        }
        imap_expunge($inbox);
        usleep(500000);
    }
}

imap_close($inbox);

