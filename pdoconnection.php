<?php
include("config.php");
$connect = new PDO("mysql:host=" . HOST . ";dbname=" . DB_NAME, USERNAME, DB_PASS);