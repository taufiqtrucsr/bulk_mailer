<? 
// if ($inbox) {
//   echo 'IMAP connection successful!';
//   // Close the connection
//   imap_close($inbox);
// } else {
//   echo 'IMAP connection failed: ' . imap_last_error();
// }

$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'taufiq.surti@trucsr.in';
$password = 'feedrqdyoezvxeng';

$inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
$email_address = "mailer-daemon@googlemail.com;";
$search_criteria = 'To "' . $email_address . '"';
$emails = imap_search($inbox, $search_criteria);

if ($emails) {
    $email_number = reset($emails);
    $overview = imap_fetch_overview($inbox, $email_number, 0);
    $message = imap_fetchbody($inbox, $email_number, 2);

    if (stripos($overview[0]->subject, 'Delivery Status Notification (Failure)') !== false) {
        echo 'Bounce detected for email with subject: ' . $overview[0]->subject . '<br>';
        echo 'Message: ' . nl2br(htmlentities($message)) . '<br>';
    }
    imap_setflag_full($inbox, $email_number, "\\Seen");
    // imap_delete($inbox, $email_number);
} else {
    echo 'No emails found from: ' . $email_address;
}

imap_close($inbox);

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

?>