# Hubvolt & Atmosync Admin Panel — SRIP @ IIT Gandhinagar

![PHP](https://img.shields.io/badge/PHP-8.x-blue?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-Database-blue?logo=mysql)
![Node.js](https://img.shields.io/badge/Node.js-Backend-green?logo=node.js)
![Express.js](https://img.shields.io/badge/Express.js-Framework-black?logo=express)
![JavaScript](https://img.shields.io/badge/JavaScript-Frontend-yellow?logo=javascript)
![HTML](https://img.shields.io/badge/HTML-5-orange?logo=html5)
![CSS](https://img.shields.io/badge/CSS-3-blue?logo=css3)
![Winston](https://img.shields.io/badge/Winston-Logging-purple)
![Logs](https://img.shields.io/badge/Logs-Structured-critical)

---

## Introduction

This repository contains the frontend admin panels and log viewers for two IoT projects — **Hubvolt** and **Atmosync** — developed during the Summer Research Internship Program (SRIP) at **IIT Gandhinagar**.

The admin panels allow:

* Managing and monitoring devices (Add, Update, Delete)
* Realtime communication with respective servers
* Secure database integration with MySQL
* Viewing logs: Admin logs, Device activity logs, and Sensor reading logs
* A clean PHP + HTML/CSS + JavaScript interface

Each panel communicates with its respective backend logging server built using Node.js and Express, which handles structured logging via Winston.

All hardware resources including the ESP32 firmware, circuit diagrams, and deployment instructions are available here: [SRIP\_FINAL\_RESOURCES](https://github.com/DankJugal/SRIP_FINAL_RESOURCES)

---

## Setup Instructions

### 1. Clone the Frontend Project

Open terminal and move to your XAMPP `htdocs` directory:

```bash
cd /path/to/xampp/htdocs
git clone https://github.com/DankJugal/SRIP_FINAL.git
```

This will create the main folder containing both **Atmosync** and **Hubvolt** admin panels.

---

### 2. Clone and Setup Logging Servers

You must clone both logging server repositories and install their dependencies.

#### Atmosync Logging Server

```bash
git clone https://github.com/DankJugal/atmosync atmosync_server
cd atmosync_server
npm install
```

#### Hubvolt Logging Server

```bash
git clone https://github.com/DankJugal/hubvolt hubvolt_server
cd hubvolt_server
npm install
```

Both servers log data using Winston and write logs to the appropriate directories under `/logs`.

---

### 3. Set Up MySQL Databases

1. Start Apache and MySQL via XAMPP
2. Open `http://localhost/phpmyadmin`
3. Create two databases:

   * `atmosync`
   * `hubvolt`
4. Import the respective `.sql` files from the `resources/` folder:

   * `resources/atmosync.sql`
   * `resources/hubvolt.sql`

---

### 4. Configure Database Connections

#### For the PHP Frontends

Edit the config files:

* `atmosync/config.php`
* `hubvolt/config.php`

Update with your MySQL credentials:

```php
$host = 'localhost';
$dbname = 'your_database_name';
$username = 'root';
$password = '';
```

#### For the Node.js Servers

Edit `config.js` in both logging server folders:

```js
module.exports = {
  db: {
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'your_database_name'
  },
  port: 3000 // Use 3001 for the second server to avoid conflicts
};
```

---

### 5. Start Logging Servers

Navigate into each server directory and run:

```bash
node index.js
```

Make sure both servers are running and not using conflicting ports.

---

### 6. Launch the Web Interface

Go to your browser and visit:

```
http://localhost/SRIP_FINAL/
```

From there, you can:

* Access Atmosync and Hubvolt admin panels
* Login/logout as admin
* Add, edit, and delete devices
* View logs by category:

  * Admin panel logs
  * Device activity logs
  * Device readings logs (only for Atmosync)

---

## Directory Structure Overview

```
SRIP_FINAL/
├── atmosync/
│   ├── adminLogger.php
│   ├── config.php
│   ├── deleteDeviceConfig.php
│   ├── editDeviceConfig.php
│   ├── index.php
│   └── ...
├── hubvolt/
│   ├── adminLogger.php
│   ├── config.php
│   ├── deleteDevice.php
│   ├── updateDevice.php
│   ├── index.php
│   └── ...
├── logs/
│   ├── atmosync_logs/
│   │   ├── admin_panel_logs/
│   │   ├── device_activity_logs/
│   │   └── device_readings_logs/
│   └── hubvolt_logs/
│       ├── admin_panel_logs/
│       └── device_activity_logs/
├── info/
│   ├── about.html
│   ├── atmosync.html
│   ├── hubvolt.html
│   └── index.html
├── login.php
├── logout.php
├── index.html
├── readme.md
└── resources/
    ├── atmosync.sql
    └── hubvolt.sql
```

---

## Database Schema Overview

### `atmosync` Database — ER Diagram

```
+------------------+          +------------------+
|    devices       |          |    readings      |
+------------------+          +------------------+
| device_name (PK) |◄────────┐| id (PK)          |
| mac_address      |         └─ device_name (FK) |
| ip_address       |           | temperature      |
| status           |           | humidity         |
| last_connected   |           | timestamp        |
| installation_time|           +------------------+
| location_id (FK) |
| call_frequency   |
+------------------+
        │
        ▼
  [references internal locations table]
```

---

### `hubvolt` Database — ER Diagram

```
+---------------------------+
|         devices           |
+---------------------------+
| device_name (PK)          |
| mac_address               |
| ip_address                |
| status (online/offline)   |
| last_connected (datetime) |
| port_status (ON/OFF)      |
| installation_time         |
+---------------------------+
```

---

## Notes

* Winston logging is configured on both servers to write `.log` files in categorized folders
* Log folders must be writable by the web server
* PHP frontend uses simple file system read access to display logs
* MySQL handles device config and activity records
* Both frontends are completely independent but follow the same structure for easier maintenance

---

### Repo Links (Backend Logging Servers)

* Atmosync Server: [https://github.com/DankJugal/atmosync](https://github.com/DankJugal/atmosync)
* Hubvolt Server: [https://github.com/DankJugal/hubvolt](https://github.com/DankJugal/hubvolt)
