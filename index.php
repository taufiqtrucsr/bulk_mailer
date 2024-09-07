<?php
//index.php
include("pdoconnection.php");
$query = "
    SELECT c.*, IFNULL(dup.duplicate_count, 0) as duplicate_count 
    FROM customer c
    LEFT JOIN (
        SELECT customer_email, COUNT(*) as duplicate_count 
        FROM customer 
        GROUP BY customer_email 
        HAVING COUNT(*) > 1
    ) dup ON c.customer_email = dup.customer_email
";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();

$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT campaign_name FROM email_draft";
$campaignoutput = $connect->query($sql);

$campaigns = $campaignoutput->fetchAll(PDO::FETCH_ASSOC);
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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.2/mdb.min.css" rel="stylesheet" />
</head>

<body>
    <br />
    <div class="container" style="margin-bottom:5rem">
        <!-- <h3 align="center">truCSR Mass Mailer</h3> -->
        <div class="text-center">
            <img src="pkpa_logo.png" alt="" class="col-6 col-lg-4">
        </div>
        <br />
        <div id="table-container" class="table-responsive">
            <table class="table table-bordered table-striped">
                <td colspan="3" class="upload_btn">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="success"><?php echo htmlspecialchars($_GET['success']); ?></div>
                    <?php endif; ?>
                    <form action="upload.php" method="post" enctype="multipart/form-data"
                        class="row justify-content-center align-items-center px-3">
                        <!-- <input type="file" name="csv_file" accept=".csv" class="form-control" required> -->
                        <div class="d-flex align-items-center justify-content-center col-lg-9 col-md-12 px-0 gap-2">
                            <input type="file" name="csv_file" accept=".csv" class="form-control w-75" required>
                            <span class="text-body-tertiary w-50 text-start">eg. MYDATA.csv</span>
                        </div>
                        <input type="submit" name="submit" class="btn btn-outline-success col-lg-3 col-md-12 "
                            id="uploadcsv" value="Upload CSV File">
                    </form>
                </td>
                <td colspan="2" class="text-center send_bulk_btn">
                    <button type="button" name="bulk_email" class="btn btn-primary email_button" id="bulk_email"
                        data-action="bulk">Send Bulk Email</button>
                </td>
            </table>
            <table id="maintable" class="table table-bordered table-striped sortable">
                <thead>
                    <tr class="equal_tr">
                        <th class="id_row">Sr No</th>
                        <th class="name_row">Name</th>
                        <th class="name_row">Email</th>
                        <th class="selectall">Select All <input type="checkbox" id="select-all" /></th>
                        <th class="act_row">Action</th>
                    </tr>
                </thead>
                <tbody class="data_tbody">
                    <?php
                    $count = 0;
                    foreach ($result as $row) {
                        echo '
      <tr class="equal_tr data-item">
      <td class="id_row">' . $row["customer_id"] . '</td>
       <td class="name_row">' . $row["customer_name"] . '</td>
       <td class="name_row">' . $row["customer_email"] . '</td>
       <td class="act_row">
        <input type="checkbox" name="single_select" class="single_select" data-id="' . $row["customer_id"] . '" data-email="' . $row["customer_email"] . '" data-name="' . $row["customer_name"] . '" />
       </td>
       <td class="act_row"><button type="button" name="email_button" class="btn btn-primary btn-sm email_button singlebutton position-relative" id="' . $row["customer_id"] . '" data-id="' . $row["customer_id"] . '" data-email="' . $row["customer_email"] . '" data-name="' . $row["customer_name"] . '" data-action="single">Send Mail' . ($row["duplicate_count"] > 0
                            ? '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger">
                Duplicate
                <span class="visually-hidden">unread messages</span>
              </span>'
                            : '') . '</button></td>
       </tr>
      ';
                    }
                    ?>
                </tbody>
            </table>
            <div id="bottom_anchor"></div>
        </div>

        <div class="bg-secondary-subtle d-flex fixed-footer align-items-center justify-content-between fixed-element">
            <div class="dropdown anlytic">
                <button data-mdb-ripple-init data-mdb-dropdown-init class="btn btn-secondary dropdown-toggle"
                    id="dropdownMenuButton" type="button" aria-expanded="false">
                    Analytics
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li>
                        <button class="dropdown-item" type="button">Campaign &raquo;</button>
                        <ul class="dropdown-menu dropdown-submenu">
                            <?php foreach ($campaigns as $row) {
                                echo '<li><button class="dropdown-item camp" data-name="' . $row['campaign_name'] . '">' . $row['campaign_name'] . '</button></li>';
                            } ?>
                        </ul>
                    </li>
                    <li>
                        <button class="dropdown-item bounce_analytics disabled">Bounce Analytics</button>
                    </li>
                    <li><button class="dropdown-item" id="showLogBtn" type="button">Email Logs</button></li>
                </ul>
            </div>
            <button id="statusBtn" class="btn btn-info d-inline-flex gap-2 align-items-center d-none"><i
                    class="fa-solid fa-circle-info" style="font-size:1rem"></i>Status</button>
            <div class="search-box">
                <i class="fas fa-search btnsearch"></i>
                <input type="text" class="form-control" id="searchInput" placeholder="Type to Search...">
            </div>
            <p id="totalCount1" class="mb-0 d-flex align-items-center text-light-emphasis"></p>
            <p id="totalCount" class="mb-0 d-flex align-items-center text-light-emphasis"></p>

            <button id="composeBtn" class="btn btn-secondary d-inline-flex gap-2 align-items-center"
                data-bs-toggle="modal" data-bs-target="#composeModal"><svg xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12.8995 6.85453L17.1421 11.0972L7.24264 20.9967H3V16.754L12.8995 6.85453ZM14.3137 5.44032L16.435 3.319C16.8256 2.92848 17.4587 2.92848 17.8492 3.319L20.6777 6.14743C21.0682 6.53795 21.0682 7.17112 20.6777 7.56164L18.5563 9.68296L14.3137 5.44032Z">
                    </path>
                </svg>Compose Mail</button>

        </div>

        <!-- Compose Mail Modal -->
        <div id="composeModal" class="modal fade" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="composeModalLabel">Compose Mail</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- <label for="subject" class="form-label">Subject:</label><br>
                        <input type="text" id="subject" name="subject" class="form-control"><br><br> -->
                        <form class="needs-validation" novalidate>
                            <div class="form-floating pb-3">
                                <input type="text" class="form-control" id="campaingname" name="campaingname"
                                    placeholder="Campaign Name" required>
                                <label for="floatingInput">Campaign Name</label>
                                <div class="invalid-feedback">
                                    Campaign Name
                                </div>
                            </div>
                            <div class="d-flex gap-2 pb-3 justify-content-between">
                                <div class="form-floating col">
                                    <input type="text" class="form-control" id="fromname" name="fromname"
                                        placeholder="From Name" required>
                                    <label for="floatingInput">From Name</label>
                                    <div class="invalid-feedback">
                                        From Name
                                    </div>
                                </div>
                                <div class="form-floating col">
                                    <input type="email" class="form-control" id="fromemail" name="fromemail"
                                        placeholder="From Email ID" required>
                                    <label for="floatingInput">From Email ID</label>
                                    <div class="invalid-feedback">
                                        Please provide a valid email.
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating pb-3">
                                <input type="text" class="form-control" id="subject" name="subject"
                                    placeholder="Subject" required>
                                <label for="floatingInput">Subject</label>
                                <div class="invalid-feedback">
                                    Please Enter Subject.
                                </div>
                            </div>

                            <div class="mb-4">
                                <textarea class="form-control simple" placeholder="Body" id="body" name="body"
                                    style="height: 200px !important" required></textarea>
                                <div class="invalid-feedback pt-3">
                                    Please Enter Body.
                                </div>
                            </div>

                            <div>
                                <label for="attachment" class="form-label">Attachment:</label>
                                <input class="form-control" type="file" id="attachment" name="attachment">
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" id="sendBtn">Save
                                    changes</button>
                            </div>
                        </form>
                    </div>



                </div>
            </div>

        </div>
    </div>
</body>

</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script
    src="https://cdn.tiny.cloud/1/qagffr3pkuv17a8on1afax661irst1hbr4e6tbv888sz91jc/tinymce/7/tinymce.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.2/mdb.umd.min.js"></script>
<script src="main.js"></script>