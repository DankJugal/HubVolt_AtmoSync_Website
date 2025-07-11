<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}
// Connect to MySQL database (adjust credentials and DB name as needed)
$conn = new mysqli("localhost", "root", "", "atmosync");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch device data
$devices = [];
$sql = "SELECT * FROM devices"; // Replace with your actual table
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $devices[] = $row;
    }
}

// Fetch locations
$locations = [];
$locationResult = $conn->query("SELECT * FROM lab_incharge");
if ($locationResult && $locationResult->num_rows > 0) {
    while ($loc = $locationResult->fetch_assoc()) {
        $locations[] = $loc;
    }
}

$total = count($devices);
$active = count(array_filter($devices, fn($d) => strtolower($d['device_status']) === 'online'));
$inactive = $total - $active;
$utilization = $total > 0 ? round(($active / $total) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AtmoSync - Environmental Monitoring Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .navbar-atmosync {
            background: linear-gradient(135deg, #10b981, #059669) !important;
        }
        
        .summary-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }
        
        .summary-card .card-body {
            padding: 2rem;
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
        
        .icon-primary { background: linear-gradient(135deg, #10b981, #059669); }
        .icon-success { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .icon-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .icon-info { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        
        .summary-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1;
        }
        
        .summary-label {
            color: #6b7280;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .devices-table {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .table thead th {
            background: #f0fdf4;
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
        
        .temp-badge {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 0.375rem 0.875rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .humidity-badge {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 0.375rem 0.875rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .status-active {
            background: #dcfce7;
            color: #166534;
            padding: 0.375rem 0.875rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-inactive {
            background: #f3f4f6;
            color: #6b7280;
            padding: 0.375rem 0.875rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-edit {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #6b7280;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
        }
        
        .btn-edit:hover {
            background: #10b981;
            border-color: #10b981;
            color: white;
            transform: translateY(-1px);
        }
        
        .btn-delete {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        
        .btn-delete:hover {
            background: #dc2626;
            border-color: #dc2626;
            color: white;
            transform: translateY(-1px);
        }
        
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-radius: 16px 16px 0 0;
            border: none;
        }
        
        .modal-header.delete-header {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
        }
        
        .btn-hubvolt {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-hubvolt:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-1px);
            color: white;
        }
        
        .btn-danger-confirm {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-danger-confirm:hover {
            background: linear-gradient(135deg, #b91c1c, #991b1b);
            transform: translateY(-1px);
            color: white;
        }
        
        .env-reading {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .avg-temp {
            color: #ef4444;
            font-weight: 700;
            font-size: 1.25rem;
        }
        
        .avg-humidity {
            color: #3b82f6;
            font-weight: 700;
            font-size: 1.25rem;
        }
    </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark navbar-atmosync shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="#"><i class="fas fa-cloud-sun me-2"></i>AtmoSync</a>
      <div class="navbar-nav ms-auto">
        <a class="btn btn-hubvolt me-2" href="../hubvolt/index.php"><i class="fas fa-bolt me-2"></i>HubVolt</a>
        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
      </div>
    </div>
  </nav>

  <div class="container-fluid py-4">
    <div class="row g-4 mb-5">
      <div class="col-lg-3 col-md-6">
        <div class="summary-card">
          <div class="card-body d-flex align-items-center">
            <div class="summary-icon icon-primary me-3"><i class="fas fa-thermometer-half"></i></div>
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
            <div class="summary-icon icon-success me-3"><i class="fas fa-check-circle"></i></div>
            <div>
              <div class="summary-number"><?= $active ?></div>
              <div class="summary-label">Active Devices</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="summary-card">
          <div class="card-body d-flex align-items-center">
            <div class="summary-icon icon-warning me-3"><i class="fas fa-exclamation-triangle"></i></div>
            <div>
              <div class="summary-number"><?= $inactive ?></div>
              <div class="summary-label">Inactive Devices</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="summary-card">
          <div class="card-body d-flex align-items-center">
            <div class="summary-icon icon-info me-3"><i class="fas fa-percent"></i></div>
            <div>
              <div class="summary-number"><?= $utilization ?>%</div>
              <div class="summary-label">Utilization</div>
              <small class="text-muted"><?= $active ?> / <?= $total ?> Active</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="devices-table">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Device Name</th>
              <th>MAC Address</th>
              <th>IP Address</th>
              <th>Call Frequency</th>
              <th>Location</th>
              <th>Status</th>
              <th>Installation Time</th>
              <th>Last Connected</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($devices as $d): ?>
            <tr>
              <td><strong><?= htmlspecialchars($d['device_name']) ?></strong></td>
              <td><code><?= htmlspecialchars($d['device_mac_address']) ?></code></td>
              <td><code><?= htmlspecialchars($d['device_ip_address']) ?></code></td>
              <td><code><?= htmlspecialchars($d['device_call_frequency']) ?></code></td>
              <td>
                <?php
                  $locName = '';
                  foreach ($locations as $loc) {
                    if ($loc['locationid'] == $d['device_location_id']) {
                      $locName = $loc['location'];
                      break;
                    }
                  }
                  echo htmlspecialchars($locName ?: $d['device_location_id']);
                ?>
              </td>
              <td><span class="<?= strtolower($d['device_status']) === 'online' ? 'status-active' : 'status-inactive' ?>"><?= htmlspecialchars($d['device_status']) ?></span></td>
              <td><?= htmlspecialchars($d['device_installation_time']) ?></td>
              <td><?= htmlspecialchars($d['device_last_connected']) ?></td>
              <td>
                <?php $modalId = 'editModal' . preg_replace('/[^a-zA-Z0-9_]/', '_', $d['device_name']); ?>
                <button class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">Edit</button>

                <!-- Edit Device Modal -->
                <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $modalId ?>" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <form class="edit-form" method="POST" action="editDeviceConfig.php">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editModalLabel<?= $modalId ?>">Edit Device: <?= htmlspecialchars($d['device_name']) ?></h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="device_name" value="<?= htmlspecialchars($d['device_name']) ?>">
                          <div class="mb-3">
                            <label for="callFreq<?= $modalId ?>" class="form-label">Call Frequency (seconds)</label>
                            <div class="input-group">
                              <button type="button" class="btn btn-outline-secondary btn-minus" data-target="callFreq<?= $modalId ?>">-5</button>
                              <input type="number" class="form-control call-freq-input" id="callFreq<?= $modalId ?>" name="device_call_frequency" value="<?= htmlspecialchars($d['device_call_frequency']) ?>" min="1" required>
                              <button type="button" class="btn btn-outline-secondary btn-plus" data-target="callFreq<?= $modalId ?>">+5</button>
                            </div>
                            <div class="form-text">Enter the call frequency in seconds. Must be greater than 0.</div>
                          </div>
                          <div class="mb-3">
                            <label for="locID<?= $modalId ?>" class="form-label">Location</label>
                            <select class="form-select" id="locID<?= $modalId ?>" name="device_location_id" required>
                              <?php foreach ($locations as $loc): ?>
                                <option value="<?= htmlspecialchars($loc['locationid']) ?>" <?= $d['device_location_id'] == $loc['locationid'] ? 'selected' : '' ?>>
                                  <?= htmlspecialchars($loc['location']) ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-hubvolt">Save Changes</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Delete Button triggers modal -->
                <?php $deleteModalId = 'deleteModal' . preg_replace('/[^a-zA-Z0-9_]/', '_', $d['device_name']); ?>
                <button class="btn btn-delete" data-bs-toggle="modal" data-bs-target="#<?= $deleteModalId ?>">
                  <i class="fas fa-trash-alt"></i> Delete
                </button>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="<?= $deleteModalId ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $deleteModalId ?>" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header delete-header">
                        <h5 class="modal-title" id="deleteModalLabel<?= $deleteModalId ?>">Confirm Delete</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Are you sure you want to delete this device (Device name: <?= htmlspecialchars($d['device_name']) ?>)?
                      </div>
                      <div class="modal-footer">
                        <form class="delete-form" data-device="<?= htmlspecialchars($d['device_name']) ?>" method="POST" action="deleteDeviceConfig.php" style="display:inline;">
                          <input type="hidden" name="device_name" value="<?= htmlspecialchars($d['device_name']) ?>">
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

  <script>
document.addEventListener('DOMContentLoaded', function() {
  // +5/-5 buttons
  document.querySelectorAll('.btn-minus').forEach(btn => {
    btn.addEventListener('click', function() {
      const input = document.getElementById(this.dataset.target);
      let val = parseInt(input.value) || 1;
      val = Math.max(1, val - 5);
      input.value = val;
    });
  });
  document.querySelectorAll('.btn-plus').forEach(btn => {
    btn.addEventListener('click', function() {
      const input = document.getElementById(this.dataset.target);
      let val = parseInt(input.value) || 1;
      val = val + 5;
      input.value = val;
    });
  });

  // Prevent negative or zero values on manual input
  document.querySelectorAll('.call-freq-input').forEach(input => {
    input.addEventListener('input', function() {
      if (this.value < 1) this.value = 1;
    });
  });

  // AJAX form submit
  document.querySelectorAll('.edit-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      const freqInput = form.querySelector('.call-freq-input');
      if (parseInt(freqInput.value) < 1) {
        alert('Call frequency must be greater than 0.');
        freqInput.value = 1;
        return;
      }
      const formData = new FormData(form);
      const response = await fetch('editDeviceConfig.php', {
        method: 'POST',
        body: formData
      });
      const result = await response.text();
      if (result.includes('success')) {
        alert('Device updated successfully!');
        // Close the modal
        const modal = form.closest('.modal');
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) modalInstance.hide();
        // Reload after a short delay to allow modal to close smoothly
        setTimeout(() => location.reload(), 500);
      } else {
        alert(result);
      }
    });
  });

  document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      const formData = new FormData(form);
      const response = await fetch('deleteDeviceConfig.php', {
        method: 'POST',
        body: formData
      });
      const result = await response.text();
      if (result.includes('deleted successfully')) {
        alert(result);
        // Close the modal
        const modal = form.closest('.modal');
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) modalInstance.hide();
        // Reload after a short delay to allow modal to close smoothly
        setTimeout(() => location.reload(), 500);
      } else {
        alert(result);
      }
    });
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