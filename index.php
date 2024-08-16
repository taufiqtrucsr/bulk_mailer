<?php
//index.php
$connect = new PDO("mysql:host=localhost;dbname=testing", "root", "");
$query = "SELECT * FROM customer ORDER BY customer_id";
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
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <br />
    <div class="container" style="margin-bottom:5rem">
        <h3 align="center">truCSR Mass Mailer</h3>
        <br />
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <td colspan="3" class="upload_btn">
                            <?php if (isset($_GET['error'])): ?>
                                <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
                            <?php endif; ?>
                            <?php if (isset($_GET['success'])): ?>
                                <div class="success"><?php echo htmlspecialchars($_GET['success']); ?></div>
                            <?php endif; ?>
                            <form action="upload.php" method="post" enctype="multipart/form-data">
                                <!-- <input type="file" name="csv_file" accept=".csv" class="form-control" required> -->
                                <div class="d-flex gap-3 align-items-center justify-content-center">
                                    <input type="file" name="csv_file" accept=".csv" class="form-control w-75" required>
                                    <span class="text-body-tertiary">eg. MYDATA.csv</span>
                                </div>
                                <input type="submit" name="submit" class="btn btn-outline-success mt-2"
                                    value="Upload CSV File">
                            </form>

                        </td>
                        <td colspan="2" class="text-center send_bulk_btn">
                            <button type="button" name="bulk_email" class="btn btn-primary email_button" id="bulk_email"
                                data-action="bulk">Send Bulk Email</button>
                            <!-- Compose Mail Button -->
                        </td>

                    </tr>
                    <tr class="equal_tr">
                        <th>Sr No</th>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th class="selectall">Select All <input type="checkbox" id="select-all"
                                onclick="toggleSelectAll(this)" /></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    foreach ($result as $row) {
                        $count++;
                        echo '
      <tr class="equal_tr">
      <td>' . $row["customer_id"] . '</td>
       <td>' . $row["customer_name"] . '</td>
       <td>' . $row["customer_email"] . '</td>
       <td>
        <input type="checkbox" name="single_select" class="single_select" data-id="' . $row["customer_id"] . '" data-email="' . $row["customer_email"] . '" data-name="' . $row["customer_name"] . '" />
       </td>
       <td><button type="button" name="email_button" class="btn btn-primary btn-sm email_button singlebutton" id="' . $count . '" data-id="' . $row["customer_id"] . '" data-email="' . $row["customer_email"] . '" data-name="' . $row["customer_name"] . '" data-action="single">Send Mail</button></td>
      </tr>
      ';
                    }
                    ?>
                </tbody>
            </table>
            <button id="composeBtn" class="btn btn-secondary d-inline-flex gap-2 align-items-center"
                data-bs-toggle="modal" data-bs-target="#composeModal"><svg xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12.8995 6.85453L17.1421 11.0972L7.24264 20.9967H3V16.754L12.8995 6.85453ZM14.3137 5.44032L16.435 3.319C16.8256 2.92848 17.4587 2.92848 17.8492 3.319L20.6777 6.14743C21.0682 6.53795 21.0682 7.17112 20.6777 7.56164L18.5563 9.68296L14.3137 5.44032Z">
                    </path>
                </svg>Compose Mail</button>

            <!-- Compose Mail Modal -->
            <div id="composeModal" class="modal fade" tabindex="-1" aria-labelledby="composeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="composeModalLabel">Compose Mail</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- <label for="subject" class="form-label">Subject:</label><br>
                        <input type="text" id="subject" name="subject" class="form-control"><br><br> -->

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="subject" name="subject"
                                    placeholder="Subject">
                                <label for="floatingInput">Subject</label>
                            </div>

                            <div class="form-floating mb-3">
                                <textarea class="form-control" placeholder="Body" id="body" name="body"
                                    style="height: 200px"></textarea>
                                <label for="floatingInput">Body</label>
                            </div>

                            <div class="mb-3">
                                <label for="attachment" class="form-label">Attachment:</label>
                                <input class="form-control" type="file" id="attachment" name="attachment">
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="sendBtn" data-bs-dismiss="modal">Save
                                changes</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="main.js"></script>