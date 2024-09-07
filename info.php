<?
// if ($inbox) {
//   echo 'IMAP connection successful!';
//   // Close the connection
//   imap_close($inbox);
// } else {
//   echo 'IMAP connection failed: ' . imap_last_error();
// }


// $email = filter_var($row["email"], FILTER_SANITIZE_EMAIL);
// if ($email && filter_var($row["email"], FILTER_VALIDATE_EMAIL)) {

//   if ($result) {
//     echo 'ok';
//   } else {
//     echo 'Failed to send email';
//   }
// } else {
//   echo 'Invalid Email';
// }

// sleep(1);



// require_once 'PHPMailer-BMH/BounceMailHandler.php';

// $bounceHandler = new BounceMailHandler();
// $bounceHandler->mailhost = 'imap.gmail.com';
// $bounceHandler->mailbox_user = 'taufiq.surti@trucsr.in';
// $bounceHandler->mailbox_password = 'apppassword';
// $bounceHandler->port = 993;
// $bounceHandler->service = 'imap';
// $bounceHandler->actionFunction = 'callbackAction';
// try {
//   $bounceHandler->openMailbox();
// } catch (Exception $e) {
//   echo "Error: " . $e->getMessage();
// }

// function callbackAction($msgnum, $bounce_type, $email, $subject) {
//   if ($bounce_type == 'hard') {
//       echo "Mail bounce back for $email";
//   }
//   echo "Email $email bounced with type $bounce_type. Subject: $subject\n";
// }

// try {
//   $bounceHandler->processMailbox();
//   $bounceHandler->closeMailbox();
// } catch (Exception $e) {
//   echo "Error: " . $e->getMessage();
// }



//   $mail->From = 'taufiq.surti@trucsr.in';   //Sets the From email address for the message
//   $mail->FromName = 'Taufiq';     //Sets the From name of the message 
// $mail->addReplyTo('taufiq.surti@trucsr.in');
// $mail->Subject = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit';
// $mail->Body = '
// <p>Sed at odio sapien. Vivamus efficitur, nibh sit amet consequat suscipit, ante quam eleifend felis, mattis dignissim lectus ipsum eget lectus. Nullam aliquam tellus vitae nisi lobortis, in hendrerit metus facilisis. Donec iaculis viverra purus a efficitur. Maecenas dignissim finibus ultricies. Curabitur ultricies tempor mi ut malesuada. Morbi placerat neque blandit, volutpat felis et, tincidunt nisl.</p>
// <p>In imperdiet congue sollicitudin. Quisque finibus, ipsum eget sagittis pellentesque, eros leo tempor ante, interdum mollis tortor diam ut nisl. Vivamus odio mi, congue eu ipsum vulputate, consequat hendrerit sapien. Aenean mauris nibh, ultrices accumsan ultricies eget, ultrices ut dui. Donec bibendum lectus a nibh interdum, vel condimentum eros auctor.</p>
// <p>Quisque dignissim pharetra tortor, sit amet auctor enim euismod at. Sed vitae enim at augue convallis pellentesque. Donec rhoncus nisi et posuere fringilla. Phasellus elementum iaculis convallis. Curabitur laoreet, dui eget lacinia suscipit, quam erat vehicula nulla, non ultrices elit massa eu dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam vulputate mauris vel ultricies tempor.</p>
// <p>Mauris est leo, tincidunt sit amet lacinia eget, consequat convallis justo. Morbi sollicitudin purus arcu. Suspendisse pellentesque interdum enim non consectetur. Etiam eleifend pharetra ante a feugiat.</p>
// ';
// $mail->addAttachment('C:\Users\neera\Downloads\project test.jpeg', 'project test.jpeg');

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



use BounceMailHandler\BounceMailHandler;
require_once 'PHPMailer-BMH/BounceMailHandler.php';

$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$u_name = 'taufiq.surti@trucsr.in';
$pass = 'feedrqdyoezvxeng';

$options = [
  'actionFunction' => 'processBounce',
  'verbose' => BounceMailHandler::VERBOSE_SIMPLE,
];

$bmh = new BounceMailHandler($hostname, $u_name, $pass, $options);

$bmh->openMailbox();
$bmh->processMailbox();

function processBounce($msgnum, $bounceType, $email, $subject, $xHeader, $body, $rawHeader)
{
  echo "Bounce detected: $bounceType - $email\n";
  echo "Message number: $msgnum\n";
  echo "Bounce Type: $bounceType\n";
  echo "Email Address: $email\n";
  echo "Subject: $subject\n";
  echo $rawHeader;
  echo $xHeader;
  echo $body;

  if ($bounceType == 'Hard Bounce') {
    // Example: Remove from the database
    // removeEmailFromDatabase($email);
    echo "Hard bounce detected. Removing $email from the list.\n";
  }
}

// $inbox = imap_open("{imap.gmail.com:993/imap/ssl}INBOX", "taufiq.surti@trucsr.in", "feedrqdyoezvxeng");
// if (!$inbox) {
//   die("Failed to connect to IMAP server: " . imap_last_error());
// }

// $emails = imap_search($inbox, 'FROM "mailer-daemon@googlemail.com"');
// $email_number = imap_num_msg($inbox) + 1;
// $max_attempts = 30;
// $attempt = 0;


// backup.php
// use BounceMailHandler\BounceMailHandler;
// require "PHPMailer-BMH-master\src\BounceMailHandler\BounceMailHandler.php";
// require "PHPMailer-BMH-master\src\BounceMailHandler\phpmailer-bmh_rules.php";

// class CustomBounceMailHandler extends BounceMailHandler {

//     // Custom processing function with the correct method signature and access level
//     public function processBounce(int $pos, string $type, int $totalFetched) {
//         echo "Processing bounce at position: $pos\n";
//         echo "Bounce type: $type\n";
//         echo "Total messages fetched: $totalFetched\n";

//         // Fetch the email information
//         $bounce = $this->bmhResults[$pos];
//         $email = $bounce['recipient'];
//         $bounceType = $bounce['bounce_type'];
//         $diagnosticCode = $bounce['diag_code'];

//         echo "Bounce detected for email: $email\n";
//         echo "Bounce type: $bounceType\n";
//         echo "Reason: $diagnosticCode\n";
//         echo "--------------------------\n";
//     }
// }


// // IMAP connection details
// $hostname = '{imap.example.com:993/imap/ssl}INBOX';
// $username = 'your_email@example.com';
// $password = 'your_password';

// // Instantiate the custom handler
// $bmh = new CustomBounceMailHandler();

// // Set the required options
// $bmh->actionFunction = [$bmh, 'processBounce']; // Reference the processBounce method
// $bmh->mailhost = $hostname;
// $bmh->mailboxUsername = $username;
// $bmh->mailboxPassword = $password;
// $bmh->port = 993; // Typically for SSL
// $bmh->service = 'imap';
// $bmh->moveSoft = false;
// $bmh->deleteMsg = false;

// // Process the mailbox
// $imap = @imap_open($bmh->mailhost, $bmh->mailboxUsername, $bmh->mailboxPassword);
// if ($imap) {
//     $bmh->openMailbox();
//     $bmh->processMailbox();
//     imap_close($imap);
// } else {
//     echo "IMAP connection failed: " . imap_last_error();
// }
// function processBounce($msgnum, $bounce)
// {
//     echo "Processing bounce #$msgnum\n";

//     $email = $bounce['recipient'];
//     $bounceType = $bounce['bounce_type'];
//     $diagnosticCode = $bounce['diag_code'];

//     echo "Bounce detected for email: $email\n";
//     echo "Bounce type: $bounceType\n";
//     echo "Reason: $diagnosticCode\n";
//     echo "--------------------------\n";
// }
// $bmh = new BounceMailHandler();
// $bmh->actionFunction = 'processBounce';
// $bmh->verbose = true;

// $bmh->mailhost = '{imap.gmail.com:993/imap/ssl}INBOX'; // your mail server
// $bmh->mailboxUserName = 'taufiq.surti@trucsr.in'; // your mailbox username
// $bmh->mailboxPassword = 'feedrqdyoezvxeng'; // your mailbox password

// $imap = @imap_open($bmh->mailhost, $bmh->mailboxUserName, $bmh->mailboxPassword);
// if ($imap === false) {
//     die('IMAP connection failed: ' . imap_last_error());
// } else {
//     $bmh->disableDelete = true;
//     $bmh->processMailbox();

//     $numMessages = imap_num_msg($imap);
//     echo "Number of messages in mailbox: $numMessages\n";

//     if ($numMessages > 0) {
//         $bmh->disableDelete = true;
//         $bmh->processMailbox();
//     } else {
//         echo "No messages to process.\n";
//     }

//     imap_close($imap);
// }


if ($emails) {
  foreach ($emails as $email_number) {
      // echo $email_number;
      $overview = imap_fetch_overview($inbox, $email_number, 0);
      $header = imap_fetchheader($inbox, $email_number);
      $message = imap_fetchbody($inbox, $email_number, 1);
      $srno++;

      // $message = html_entity_decode($message);
      // $message = strip_tags($message);

      // Extract the email ID from the headers
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

      // echo $message;

      $sql = "INSERT INTO bounce_mail (Sr_no, Bounce_reason, email_id) VALUES (?, ?, ?)";
      $stmt = $conn->prepare($sql);
      // Prepare and bind

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
}

// Function to toggle all checkboxes
// function toggleSelectAll(selectAllCheckbox) {
//   // Get all checkboxes with the class 'option-checkbox'
//   const checkboxes = document.querySelectorAll(".single_select");

//   // Set the checked state of each checkbox to match the 'Select All' checkbox
//   checkboxes.forEach((checkbox) => {
//     checkbox.checked = selectAllCheckbox.checked;
//   });
// }
?>
<!-- // $(".camp").click(function () {
//   Swal.fire({
//     title: "<?php echo $row['campaign_name'] ?>",
//     html: '<iframe id="contentFrame" src="" width="100%" height="200px"></iframe><br><br><button id="button1" class="swal2-confirm swal2-styled">Bounce Analytics</button><button id="button2" class="swal2-confirm swal2-styled">Email Logs</button><button id="button3" class="swal2-confirm swal2-styled">List Of Data</button>',
//     width: 800,
//     padding: "1em",
//     allowOutsideClick: false,
//     showConfirmButton: false,
//     showCloseButton:true,
//     didOpen: () => {
//       $("#button1").click (function () {
//         document.getElementById("contentFrame").src =
//           "campaign.php";
//       });
//       $("#button2").click (function () {
//         document.getElementById("contentFrame").src =
//           "campaign.php";
//       });
//       $("#button3").click (function () {
//         document.getElementById("contentFrame").src =
//           "campaign.php";
//       });
//     },
//   });
// }); -->

$logFile = 'email_log.txt';
    ob_start();
    foreach ($_POST['email_data'] as $index => $row) {
      
      ob_flush();
      flush();
      file_put_contents($logFile, $row['email'] . PHP_EOL, FILE_APPEND);
      sleep(1);
      ob_end_flush();
    }

    $result = $mail->Send();
      $id = $row['data_id'];


      if ($result) {

        echo '<script type="text/javascript">
          document.getElementById("' . $id . '").textContent = "Success";
          document.getElementById("' . $id . '").classList.remove("btn-danger");
          document.getElementById("' . $id . '").classList.add("btn-success");
          document.getElementById("' . $id . '").removeAttribute("disabled");</script>';

        $delete_included = false;
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

      $sql1 = "DELETE FROM Sended_Mail";
$conn->exec($sql1);

$sql2 = "INSERT INTO Sended_Mail (Sr_No, Customer_Name , Email) VALUES (?, ?, ?)";
$emailresult1 = $conn->query($sql2);

$emailresult1->bind_param("iss", $row['data_id'], $row["name"], $row['email']);

$("#statusBtn").on("click", function () {
    fetch("email_log.php")
      .then((response) => response.text())
      .then((data) => {
        Swal.fire({
          title: "Loading...",
          text: "Please wait while we load the file.",
          html: `<pre style="text-align:left;overflow-y:scroll;height:200px">${data}</pre>`,
          width: "50%",
          customClass: {
            popup: "swal2-log-popup",
            content: "swal2-log-content",
          },
          allowOutsideClick: false,
          showCloseButton: true,
          didOpen: () => {
            Swal.showLoading();
          },
        });
      })
      .catch((error) => {
        Swal.fire("Error", "Could not load log file.", "error");
      });
  });


  function fetchFileContent() {
    $.ajax({
      url: "email_log.php",
      method: "GET",
      data: { lastModTime: lastModTime },
      success: function (response) {
        const data = JSON.parse(response);
        lastModTime = data.lastModTime;
        Swal.fire({
          title: "Loading...",
          text: "Please wait while we load the file.",
          html: `<pre style="text-align:left;overflow-y:scroll;height:200px">${data.content}</pre>`,
          width: "50%",
          allowOutsideClick: false,
          showCloseButton: true,
          didOpen: () => {
            Swal.hideLoading();
          },
        });
        fetchFileContent();
      },
      error: function () {
        Swal.fire({
          title: "Error!",
          text: "Error loading file.",
          icon: "error",
          allowOutsideClick: false,
          showCloseButton: true,
        });
      },
    });
  }