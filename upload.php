<?php
include("connection.php");

$sql = "DELETE FROM customer";
$conn->query($sql);

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csv_file'])) {
        $fileTmpPath = $_FILES['csv_file']['tmp_name'];
        $fileName = $_FILES['csv_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $error_code = $_FILES['csv_file']['error'];

        if ($error_code == UPLOAD_ERR_OK) {
            if ($fileExtension === 'csv') {
                if (($handle = fopen($fileTmpPath, 'r')) !== false) {
                    $stmt = $conn->prepare("INSERT INTO customer (customer_id, customer_name, customer_email) VALUES (?, ?, ?)");

                    fgetcsv($handle); // Skip header row

                    while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                        $customer_id = $row[0];
                        $customer_name = $row[1];
                        $customer_email = $row[2];

                        $stmt->bind_param("iss", $customer_id, $customer_name, $customer_email);
                        
                        try {
                            $stmt->execute();
                        } catch (mysqli_sql_exception $e) {
                            $error_message = "Error: " . $e->getMessage();
                            break;
                        }
                    }

                    fclose($handle);
                    $stmt->close();
                    $conn->close();
                    
                    if (!$error_message) {
                        $success_message = 'Data Import from CSV Completed!';
                    }
                } else {
                    $error_message = "Error opening the file.";
                }
            } else {
                $error_message = "Invalid file format. Please upload a CSV file.";
            }
        } else {
            $error_message = match ($error_code) {
                UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
                UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
                UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
                UPLOAD_ERR_NO_FILE => "No file was uploaded.",
                UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
                UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload.",
                default => "There was an unknown error uploading the file.",
            };
        }
    } else {
        $error_message = "No file was uploaded or form submission error.";
    }

    // Redirect back to index.php with appropriate message
    $redirectUrl = 'index.php';
    if ($error_message) {
        header('Location: ' . $redirectUrl . '?error=' . urlencode($error_message));
    } else if ($success_message) {
        header('Location: ' . $redirectUrl . '?success=' . urlencode($success_message));
    }
    exit();
}

