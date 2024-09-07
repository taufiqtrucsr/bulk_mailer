<?php
require 'phpoffice/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
include("pdoconnection.php");
$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT campaign_name FROM email_draft WHERE campaign_name = '" . $_POST['campaignName'] . "'";
$result = $connect->query($sql);


$row = $result->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $current_campainname = $row['campaign_name'];
} else {
    die("No draft found.");
}

$campainname = !empty($_POST['campaingname']) ? filter_var($_POST['campaingname'], FILTER_SANITIZE_STRING) : $current_campainname;
$campaignname = str_replace(' ', '_', $campainname);

// $filePath = "{$campaignname}_Bounce_mail.xlsx";
$filePath = TARGET_DIR."{$campaignname}_Bounce_mail.xlsx";

$spreadsheet = IOFactory::load($filePath);

$sheetNames = $spreadsheet->getSheetNames();
$selectedSheet = isset($_GET['sheet']) ? $_GET['sheet'] : 0;
$spreadsheet->setActiveSheetIndex($selectedSheet);

$sheet = $spreadsheet->getActiveSheet();

$sheetData = $sheet->toArray();

echo '<table border="1" class="table table-bordered table-striped sortable">';

if (!empty($sheetData)) {
    echo '<thead><tr>';
    foreach ($sheetData[0] as $headerCell) {
        echo '<th>' . htmlspecialchars($headerCell) . '</th>';
    }
    echo '</tr></thead>';

    echo '<tbody>';
    for ($i = 1; $i < count($sheetData); $i++) {
        echo '<tr class="equal_tr">';
        foreach ($sheetData[$i] as $cell) {
            echo '<td>' . htmlspecialchars($cell) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody>';
}

echo '</table>';

?>