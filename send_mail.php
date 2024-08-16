<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testing";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM email_draft LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $subject = $row['subject'];
  $body = $row['body'];
  $attachment_path = $row['attachment_path'];
  $filename = basename($attachment_path);

} else {
  echo "No data found.";
}

$conn->close();

if (isset($_POST['email_data'])) {
  require 'phpmailer/src/Exception.php';
  require 'phpmailer/src/PHPMailer.php';
  require 'phpmailer/src/SMTP.php';

  $mail = new PHPMailer;
  $output = '';

  try {
    $mail->IsSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->SMTPAuth = true;
    $mail->SMTPDebug = false;
    $mail->Username = 'taufiq.surti@trucsr.in';
    $mail->Password = 'feedrqdyoezvxeng';
    $mail->SMTPSecure = 'ssl';
    $mail->Sender = 'taufiq.surti@trucsr.in';

    foreach ($_POST['email_data'] as $row) {
      $mail->ClearAddresses();
      $mail->ClearAttachments();
      $mail->ClearAllRecipients();

      $mail->setFrom('taufiq.surti@trucsr.in', 'Taufiq');
      $mail->AddAddress($row["email"], $row["name"]);
      $mail->WordWrap = 50;
      $mail->IsHTML(true);

      $mail->Subject = $subject;
      $body = str_replace('[Name of NGO Contact]', $row["name"], $body);
      $mail->Body = nl2br($body);
      $mail->addAttachment($attachment_path, $filename);

      $mail->AltBody = strip_tags($body);

      $result = $mail->Send();
      $id = $row['data_id'];

      $email1 = filter_var($row["email"], FILTER_SANITIZE_EMAIL);
      if ($email1 && filter_var($row["email"], FILTER_VALIDATE_EMAIL)) {

        if ($result) {
          $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
          $username = 'taufiq.surti@trucsr.in';
          $password = 'feedrqdyoezvxeng';

          $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
          $email_address = trim($row["email"]);
          $email_address_encoded = imap_utf7_encode($email_address);
          $search_criteria = 'TO "' . $email_address_encoded . '"';
          $emails = imap_search($inbox, $search_criteria);

          if ($emails) {
            $email_number = reset($emails);
            $overview = imap_fetch_overview($inbox, $email_number, 0);
            $message = imap_fetchbody($inbox, $email_number, 2);

            if (
              stripos($overview[0]->subject, 'Delivery Status Notification (Failure)') !== false ||
              stripos($overview[0]->subject, 'Undelivered Mail Returned to Sender') !== false
            ) {

              // Print bounce information
              echo 'Bounce detected for email with subject: ' . $overview[0]->subject . '<br>';
              echo 'Message: ' . nl2br(htmlentities($message)) . '<br>';
            }
            imap_setflag_full($inbox, $email_number, "\\Seen");
            // imap_delete($inbox, $email_number);
          }

          imap_close($inbox);
          echo '<script>
            document.getElementById("' . $id . '").textContent = "Success";
            document.getElementById("' . $id . '").classList.remove("btn-danger");
            document.getElementById("' . $id . '").classList.add("btn-success");
          </script>';
        } else {
          echo '$("body").append(\'<script>
            document.getElementById("' . $id . '").textContent = "Failed";
            document.getElementById("' . $id . '").classList.remove("btn-outline-danger");
            document.getElementById("' . $id . '").classList.add("btn-danger");
          <\/script>\');';
        }
      } else {
        echo '$("body").append(\'<script>
          document.getElementById("' . $id . '").textContent = "Invalid Email";
          document.getElementById("' . $id . '").classList.remove("btn-outline-danger");
          document.getElementById("' . $id . '").classList.add("btn-danger");
        <\/script>\');';
      }
    }
    echo "ok";
  } catch (Exception $e) {
    echo 'Failed to send message to ' . $row['email'] . ': ' . $mail->ErrorInfo . '<br>';
  }
}


