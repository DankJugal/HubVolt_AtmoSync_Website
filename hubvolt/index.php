<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}
require_once "config.php";
$sql = "SELECT * FROM devices";
$result = $conn->query($sql);

$devices = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $devices[] = $row;
    }
}

// Summary calculations
$total = count($devices);
$device_on_count = count(array_filter($devices, fn($d) => ($d['device_port_status']) === 'ON'));
$device_off_count = $total - $device_on_count;
$utilization = $total > 0 ? round(($device_on_count / $total) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>HubVolt - IoT Device Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  width: 100%;
  height: 100%;
  background-color: #f8fafc;
  overflow-x: hidden;
}

  body {
    background-color: #f8fafc;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .summary-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
  }

  .summary-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
  }

  .summary-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
  }

  .icon-primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .icon-success { background: linear-gradient(135deg, #10b981, #059669); }
  .icon-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
  .icon-info { background: linear-gradient(135deg, #6366f1, #4f46e5); }

  .summary-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
  }

  .summary-label {
    font-weight: 500;
    font-size: 0.875rem;
    color: #6b7280;
  }

  .toggle-switch.active {
    background-color: #10b981;
    color: white;
    border: none;
    padding: 4px 12px;
    border-radius: 8px;
  }

  .toggle-switch {
    background-color: #6b7280;
    color: white;
    border: none;
    padding: 4px 12px;
    border-radius: 8px;
  }

  .btn-delete {
    background-color: #dc2626;
    color: white;
    border: none;
    padding: 5px 12px;
    border-radius: 8px;
  }
  .delete-header {
  background: #dc2626;
  color: #fff;
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
}

.btn-danger-confirm {
  background: linear-gradient(135deg, #dc2626, #b91c1c);
  color: #fff;
  border: none;
  font-weight: 600;
  padding: 0.5rem 1.2rem;
  border-radius: 8px;
  transition: background 0.2s;
}

.btn-danger-confirm:hover {
  background: linear-gradient(135deg, #b91c1c, #7f1d1d);
  color: #fff;
}

  .btn-delete:hover {
    background-color: #b91c1c;
  }

  .status-active {
    background-color: #dcfce7;
    color: #166534;
    padding: 6px 12px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
  }

  .status-inactive {
    background-color: #f3f4f6;
    color: #6b7280;
    padding: 6px 12px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
  }

  .btn-atmosync {
    background: linear-gradient(135deg, #10b981, #059669);
    border: none;
    color: white;
    font-weight: 600;
    padding: 0.5rem 1.2rem;
    border-radius: 8px;
    text-decoration: none;
  }

  .btn-atmosync:hover {
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
    transform: translateY(-1px);
  }

  .devices-table {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-top: 2rem;
  }

  .table thead th {
    background: #f8fafc;
    border: none;
    color: #374151;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 1.25rem 1rem;
  }

  .table tbody td {
    border-color: #f3f4f6;
    padding: 1.25rem 1rem;
    vertical-align: middle;
  }
</style>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><i class="fas fa-bolt me-2"></i>HubVolt</a>
    <div class="navbar-nav ms-auto">
      <a class="btn btn-atmosync me-2" href="../atmosync/index.php"><i class="fas fa-cloud-sun me-2"></i>AtmoSync</a>
      <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
    </div>
  </div>
</nav>

<div class="container-fluid py-4">
  <div class="row g-4 mb-5">
    <div class="col-lg-3 col-md-6">
      <div class="summary-card">
        <div class="card-body d-flex align-items-center">
          <div class="summary-icon icon-primary me-3"><i class="fas fa-microchip"></i></div>
          <div>
            <div class="summary-number"><?= $total ?></div>
            <div class="summary-label">Total Devices</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="summary-card">
        <div class="card-body d-flex align-items-center">
          <div class="summary-icon icon-success me-3"><i class="fas fa-power-off"></i></div>
          <div>
            <div class="summary-number"><?= $device_on_count ?></div>
            <div class="summary-label">Devices ON</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="summary-card">
        <div class="card-body d-flex align-items-center">
          <div class="summary-icon icon-warning me-3"><i class="fas fa-pause-circle"></i></div>
          <div>
            <div class="summary-number"><?= $device_off_count ?></div>
            <div class="summary-label">Devices OFF</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="summary-card">
        <div class="card-body d-flex align-items-center">
          <div class="summary-icon icon-info me-3"><i class="fas fa-chart-line"></i></div>
          <div>
            <div class="summary-number"><?= $utilization ?>%</div>
            <div class="summary-label">Utilization</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Devices Table -->
  <div class="devices-table">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>Device Name</th>
            <th>MAC Address</th>
            <th>IP Address</th>
            <th>Port Status</th>
            <th>Device Status</th>
            <th>Installation Time</th>
            <th>Last Connected</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($devices as $device): ?>
          <tr>
            <td><strong><?= htmlspecialchars($device['device_name']) ?></strong></td>
            <td><code><?= htmlspecialchars($device['device_mac_address']) ?></code></td>
            <td><code><?= htmlspecialchars($device['device_ip_address']) ?></code></td>
            <td>
<form method="POST" action="updateDevice.php" class="toggle-form" style="display:inline;">
  <input type="hidden" name="device_name" value="<?= trim($device['device_name']) ?>">
  <input type="hidden" name="new_status" value="<?= trim(($device['device_port_status'])) === 'ON' ? 'OFF' : 'ON' ?>">
  <button type="submit"
          class="toggle-switch <?= trim($device['device_port_status']) === 'ON' ? 'active' : '' ?>">
    <?= ($device['device_port_status']) ?>
  </button>
</form>

            </td>
            <td>
<span class="<?= strtolower($device['device_status']) === 'online' ? 'status-active' : 'status-inactive' ?>">
  <?= htmlspecialchars($device['device_status']) ?>
</span>
            </td>
            <td><?= htmlspecialchars($device['device_installation_time']) ?></td>
            <td><?= htmlspecialchars($device['device_last_connected']) ?></td>
            <td>
  <button class="btn btn-delete" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $device['device_name'] ?>">
    <i class="fas fa-trash-alt"></i> Delete
  </button>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal<?= $device['device_name'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $device['device_name'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header delete-header">
          <h5 class="modal-title" id="deleteModalLabel<?= $device['device_name'] ?>">Confirm Delete</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this device (Device name: <?= $device['device_name'] ?>)?
        </div>
        <div class="modal-footer">
<form class="delete-form" data-device="<?= htmlspecialchars($device['device_name']) ?>" method="POST" action="deleteDevice.php" style="display:inline;">
  <input type="hidden" name="device_name" value="<?= trim($device['device_name']) ?>">
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
  <button type="submit" class="btn btn-danger-confirm">Yes, Delete</button>
</form>
        </div>
      </div>
    </div>
  </div>
</td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.toggle-form').forEach(form => {
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(form);
    const response = await fetch('updateDevice.php', {
      method: 'POST',
      body: formData
    });
    const result = await response.text();
    if (result.includes('successfully')) {
      alert(`Device turned ${formData.get('new_status')} successfully!`);

      location.reload();
    } else {
      alert(result);
    }
  });
});

document.querySelectorAll('.delete-form').forEach(form => {
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(form);
    const response = await fetch('deleteDevice.php', {
      method: 'POST',
      body: formData
    });
    const result = await response.text();
    if (result.includes('successfully')) {
      alert('Device deleted successfully!');
      location.reload(); // Or remove the row without reload
    } else {
      alert(result);
    }
  });
});
</script>
<div class="text-center mt-5 pb-4">
  <p>
    <a class="btn btn-outline-primary" href="../info/aboutus.php">
      Learn more about the team → About Us
    </a>
  </p>
  <p class="mt-3 small text-muted">
    View full repository on GitHub:
    <a href="https://github.com/DankJugal/SRIP_FINAL" target="_blank">SRIP_FINAL GitHub</a>
  </p>
</div>

</body>
</html>
<!-- 


fetch('updateDevice.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  body: 'device_name=HubVolt_Device_005&new_status=ON'
})
.then(response => response.text())
.then(console.log)
.catch(console.error); -->


<!-- Can use this snippet to directly call the api from the  -->

<!-- There are two options one is to convert this to GET method and the other is to call this javascript block for the following and so on -->