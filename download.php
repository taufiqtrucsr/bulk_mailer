<?php
require 'phpoffice/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

try {
    include("pdoconnection.php");
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT campaign_name FROM email_draft ORDER BY id DESC LIMIT 1";
    $result = $connect->query($sql);

    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $current_campainname = $row['campaign_name'];
    } else {
        die("No draft found.");
    }

    $campainname = !empty($_POST['campaingname']) ? filter_var($_POST['campaingname'], FILTER_SANITIZE_STRING) : $current_campainname;
    $campaignname = str_replace(' ', '_', $campainname);

    $stmt = $connect->query("SELECT * FROM bounce_mail");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $stmt2 = $connect->query("SELECT * FROM customer");
    $data2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);


    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('BounceAnalytics');

    $sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'ListOfData');
    $spreadsheet->addSheet($sheet2);

    $textFile = 'email_log.txt';
    $lines = file($textFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $sheet3 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'SendedMailList');
    $spreadsheet->addSheet($sheet3);

    $heading = ['Sr_No', 'Name', 'Email', 'Status'];
    foreach ($heading as $colIndex => $header) {
        $sheet3->setCellValueByColumnAndRow($colIndex + 1, 1, $header);
    }
    foreach ($lines as $rowIndex => $line) {
        $columns = explode("-", $line);

        foreach ($columns as $colIndex => $column) {
            $sheet3->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 2, $column);
        }
    }

    if (!empty($data)) {
        $header = array_keys($data[0]);
        $sheet->fromArray($header, NULL, 'A1');
        $sheet->fromArray($data, NULL, 'A2');
    } else {
        $sheet->setCellValue('A1', 'No Bounce Email Found in Data.');
    }

    if (!empty($data2)) {
        $header2 = array_keys($data2[0]);
        $sheet2->fromArray($header2, NULL, 'A1');
        $sheet2->fromArray($data2, NULL, 'A2');
    } else {
        $sheet2->setCellValue('A1', 'No data found in customer table.');
    }


    $writer = new Xlsx($spreadsheet);

    $fileName = "{$campaignname}_Bounce_mail.xlsx";
    $writer->save($fileName);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header('Cache-Control: max-age=0');
    readfile($fileName);

    unlink($fileName);

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}