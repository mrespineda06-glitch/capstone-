<?php
$host = "sql104.infinityfree.com";
$user = "if0_39792027";
$pass = "04kA77Qobzi8zzG";
$dbname = "if0_39792027_vendo_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
