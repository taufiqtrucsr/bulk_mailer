<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("connection.php");

$sql = "SELECT * FROM email_draft ORDER BY id DESC LIMIT 1";
$emailresult = $conn->query($sql);

if ($emailresult->num_rows > 0) {
  $row = $emailresult->fetch_assoc();
  $subject = $row['subject'];
  $body = $row['body'];
  $attachment_path = $row['attachment_path'];
  $filename = basename($attachment_path);
  $fromname = $row['from_name'];
  $fromemail = $row['from_email'];
  $campainname = $row['campaign_name'];
} else {
  echo "No data found.";
}

// $conn->close();

if (isset($_POST['email_data'])) {
  require 'phpmailer/src/Exception.php';
  require 'phpmailer/src/PHPMailer.php';
  require 'phpmailer/src/SMTP.php';

  $mail = new PHPMailer;
  $checkpoint = 500;
  $interval = 500;
  $batchSize = 100;
  $delete_included = false;

  try {
    $mail->IsSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->SMTPAuth = true;
    $mail->Username = $fromemail;
    $mail->Password = PASSWORD;
    $mail->SMTPSecure = 'ssl';
    $mail->Sender = $fromemail;
    $mail->setFrom($fromemail, $fromname);
    $mail->IsHTML(true);
    $mail->Subject = $subject;

    $logFile = 'email_log.txt';
    file_put_contents($logFile, '');

    $totalEmails = count($_POST['email_data']);
    $batchCount = ceil($totalEmails / $batchSize);
    ob_start();
    for ($batch = 0; $batch < $batchCount; $batch++) {
      $start = $batch * $batchSize;
      $end = min(($start + $batchSize), $totalEmails);
      for ($index = $start; $index < $end; $index++) {
        $row = $_POST['email_data'][$index];
        $mail->AddAddress($row["email"], $row["name"]);
        $mail->WordWrap = 50;

        if ($index === 0) {
          $body = str_replace("[Name of NGO Contact]", $row["name"], $body);
        } else {
          $previousName = $_POST['email_data'][$index - 1]["name"];
          $body = str_replace($previousName, $row["name"], $body);
        }

        $mail->Body = $body;
        $mail->addAttachment($attachment_path, $filename);
        $mail->AddCustomHeader("Disposition-Notification-To: $fromemail");

        $result = $mail->Send();

        if ($result) {
          if ($index >= $checkpoint) {
            if (!$delete_included) {
              include('delete.php');
              $delete_included = true;
            } else {
              include('backup.php');
            }
            $checkpoint += $interval;
          }
        }

        $mail->clearAddresses();
        $mail->ClearAttachments();
        $mail->ClearAllRecipients();

        ob_flush();
        flush();

        nl2br(file_put_contents($logFile, $index + 1 . " - " . $row['name'] . " - " . $row['email'] . " - Success" . PHP_EOL, FILE_APPEND));

        sleep(1);
        ob_end_flush();
      }
    }
    // ob_start();
    // foreach ($_POST['email_data'] as $index => $row) {

    //   $mail->AddAddress($row["email"], $row["name"]);
    //   $mail->WordWrap = 50;

    //   if ($index === 0) {
    //     $body = str_replace("[Name of NGO Contact]", $row["name"], $body);
    //   } else {
    //     $previousName = $_POST['email_data'][$index - 1]["name"];
    //     $body = str_replace($previousName, $row["name"], $body);
    //   }

    //   // $recipient_id = urlencode($row["email"]);
    //   // $tracking_url = URL . "track.php?id=" . $recipient_id;

    //   // $scriptCode = '<iframe src="' . $tracking_url . '" width="1" height="1" style="background-image: url(' . $trackingUrl . ');" alt="Download PDF"></iframe>';

    //   $mail->Body = $body;
    //   $mail->addAttachment($attachment_path, $filename);
    //   $mail->AddCustomHeader("Disposition-Notification-To: $fromemail");

    //   $result = $mail->Send();

    //   if ($result) {
    //     if ($index >= $checkpoint) {
    //       if (!$delete_included) {
    //         include('delete.php');
    //         $delete_included = true;
    //       } else {
    //         include('backup.php');
    //       }
    //       $checkpoint += $interval;
    //     }
    //   }

    //   $mail->clearAddresses();
    //   $mail->ClearAttachments();
    //   $mail->ClearAllRecipients();

    //   ob_flush();
    //   flush();

    //   nl2br(file_put_contents($logFile, $index + 1 . " - " . $row['name'] . " - " . $row['email'] . " - Success" . PHP_EOL, FILE_APPEND));

    //   sleep(1);
    //   ob_end_flush();
    // }
    if (!$delete_included) {
      include('delete.php');
    }
  } catch (Exception $e) {
    echo 'Failed to send message to ' . $row['email'] . ': ' . $mail->ErrorInfo . '<br>';
  }
}
