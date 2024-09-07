<?php
// live-text-long-poll.php

header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Path to the text file
$txtFile = 'email_log.txt';

// Store the last modification time
$lastModTime = isset($_GET['lastModTime']) ? intval($_GET['lastModTime']) : 0;

clearstatcache();
$currentModTime = filemtime($txtFile);

// If the file has been modified since the last request
if ($currentModTime > $lastModTime) {
    $data = file($txtFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Determine the new lines
    $newLines = array_slice($data, -1); // Get the last line if you're adding one row at a time

    // Process the new lines to generate HTML
    $rowsHTML = '';
    foreach ($newLines as $line) {
        $columns = explode("-", $line); // Adjust the delimiter based on your file format
        $columnsCount = count($columns); // Get the total number of columns

        // Generate the <td> elements
        $columnsHTML = array_map(function($column, $index) use ($columns) {
            if ($index === count($columns) - 1) {
                return '<td><button class="btn btn-success rounded btn-sm">' . htmlspecialchars($column) . '</button></td>';
            }
            return '<td>' . htmlspecialchars($column) . '</td>';
        }, $columns, array_keys($columns));

        $rowsHTML .= '<tr>' . implode('', $columnsHTML) . '</tr>';
    }

    echo json_encode([
        'lastModTime' => $currentModTime,
        'content' => $rowsHTML
    ]);
} else {
    echo json_encode([
        'lastModTime' => $lastModTime,
        'content' => ''
    ]);
}
?>
