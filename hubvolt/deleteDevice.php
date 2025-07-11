<?php
require_once "config.php";
require_once "adminLogger.php";

$logger = new AdminLogger();

$deviceName = $_POST['device_name'] ?? $_GET['device_name'] ?? '';

if (!$deviceName) {
    http_response_code(400);
    echo "Missing required field: device_name";
    $logger->error("Delete failed: Missing 'device_name' in request.");
    exit;
}

$stmt = $conn->prepare("DELETE FROM devices WHERE device_name = ?");
$stmt->bind_param("s", $deviceName);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Device configurations for '" . htmlspecialchars($deviceName) . "' deleted successfully!";
    $logger->info("Device configurations for '$deviceName' deleted successfully.");
} else {
    http_response_code(404);
    echo "No device deleted. Device '" . htmlspecialchars($deviceName) . "' may not exist.";
    $logger->warning("Delete failed: Device '$deviceName' not found or already deleted.");
}
?>
