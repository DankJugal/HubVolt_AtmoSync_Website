<?php
require_once "config.php";
require_once "adminLogger.php";
$logger = new AdminLogger();

$deviceName = $_POST['device_name'] ?? '';
$callFreq = intval($_POST['device_call_frequency'] ?? 0);
$locationId = $_POST['device_location_id'] ?? '';

$missingFields = [];
if (!$deviceName) $missingFields[] = "device_name";
if (!$callFreq) $missingFields[] = "device_call_frequency";
if (!$locationId) $missingFields[] = "device_location_id";

if (!empty($missingFields)) {
    http_response_code(400);
    $msg = "Missing required fields: " . implode(', ', $missingFields);
    $logger->warning("Validation failed - $msg");
    echo $msg;
    exit;
}

if ($callFreq < 1) {
    http_response_code(400);
    $msg = "Invalid call frequency ($callFreq) for device: " . htmlspecialchars($deviceName);
    $logger->warning($msg);
    echo "Call frequency must be greater than 0 for device: " . htmlspecialchars($deviceName);
    exit;
}

$stmt = $conn->prepare("UPDATE devices SET device_call_frequency = ?, device_location_id = ? WHERE device_name = ?");
if (!$stmt) {
    http_response_code(500);
    $logger->error("Prepare failed: " . $conn->error);
    echo "Internal Server Error";
    exit;
}

$stmt->bind_param("sis", $callFreq, $locationId, $deviceName);
if (!$stmt->execute()) {
    http_response_code(500);
    $logger->error("Execution failed for device '$deviceName': " . $stmt->error);
    echo "Internal Server Error";
    exit;
}

if ($stmt->affected_rows > 0) {
    $msg = "Device '$deviceName' updated. Call Frequency: $callFreq, Location ID: $locationId";
    $logger->info($msg);
    echo "success: $msg";
} else {
    $msg = "No changes made or device not found for '$deviceName'";
    $logger->info($msg);
    echo $msg;
}
?>
