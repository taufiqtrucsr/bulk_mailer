<?php
include("pdoconnection.php");
$query = "SELECT * FROM bounce_mail";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Bulk Mail Sender</title>
    <link rel="icon" href="favicon.png" type="image/png" sizes="16x16">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <table class="table table-bordered table-striped sortable">
        <tr class="equal_tr">
            <th class="id_row">Sr_no</th>
            <th class="name_row">Email_id</th>
            <th class="name_row">Bounce_reason</th>
        </tr>
        <?php
        foreach ($result as $row) {
            echo '
        <tr class="equal_tr">
            <td class="id_row">' . $row["Sr_no"] . '</td>
            <td class="name_row">' . $row["email_id"] . '</td>
            <td class="name_row">' . $row["Bounce_reason"] . '</td>
        </tr>';
        }
        ?>
    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>