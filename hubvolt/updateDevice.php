<?php
require_once "config.php";
require_once "adminLogger.php";

$logger = new AdminLogger();

// Get values from POST or GET
$deviceName = $_POST['device_name'] ?? $_GET['device_name'] ?? '';
$newStatus = $_POST['new_status'] ?? $_GET['new_status'] ?? '';

// Log invalid requests
if (!$deviceName && !$newStatus) {
    http_response_code(400);
    echo "Both device name and new status missing";
    $logger->error("Device status update failed: Both device name and new status missing.");
    exit;
}

if (!$deviceName) {
    http_response_code(400);
    echo "Missing device name";
    $logger->error("Device status update failed: Missing device name.");
    exit;
}

if (!$newStatus) {
    http_response_code(400);
    echo "Missing new status";
    $logger->error("Device status update failed: Missing new status for device '$deviceName'.");
    exit;
}

// 1. Get device IP address
$stmt = $conn->prepare("SELECT device_ip_address FROM devices WHERE device_name = ?");
$stmt->bind_param("s", $deviceName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo "Device '$deviceName' not found";
    $logger->warning("Port status update failed: Device '$deviceName' not found in database.");
    exit;
}

$row = $result->fetch_assoc();
$deviceIp = $row['device_ip_address'];

// 2. Send control command to device
$controlPayload = "CONTROL $newStatus";
$ch = curl_init("http://$deviceIp/");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $controlPayload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: text/plain',
]);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // 2 seconds timeout

$response = curl_exec($ch);
if ($response === false) {
    http_response_code(502);
    echo "Failed to reach '$deviceName' device";
    $logger->error("Device status update failed: Unable to contact device '$deviceName' at IP $deviceIp.");
    exit;
}
curl_close($ch);

// 3. Check device response
$response = trim($response);
$expected = $deviceName . ' ' . $newStatus;
if ($response !== $expected) {
    echo "Device did not confirm status change";
    $logger->warning("Device status update mismatch: Expected '$expected', but got '$response' from device '$deviceName'.");
    exit;
}
// In case of simultaneous requests to the same device with the same status from Dashboard and Slot Booking system.

// 4. Update status in DB
$update = $conn->prepare("UPDATE devices SET device_port_status = ? WHERE device_name = ?");
$update->bind_param("ss", $newStatus, $deviceName);
$update->execute();

echo "Device status for '$deviceName' updated to '$newStatus' successfully.";
$logger->info("Device status for device '$deviceName' updated to '$newStatus' successfully.");
?>
