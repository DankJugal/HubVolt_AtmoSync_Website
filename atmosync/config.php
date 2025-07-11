<?php
$conn = new mysqli("localhost", "root", "", "atmosync");
require_once "adminLogger.php";
// Fetch all devices
$logger = new AdminLogger();

if ($conn->connect_error) {
    http_response_code(500);
    die("DB connection failed: " . $conn->connect_error);
    $logger->error("DB connection failed");

}
?>
