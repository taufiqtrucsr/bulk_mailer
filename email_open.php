<?php
$logFile = 'email_opens.log';

if (file_exists($logFile)) {
    echo nl2br(file_get_contents($logFile));
} else {
    echo 'Log file not found.';
}