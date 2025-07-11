<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Hubvolt & Atmosync</title>
    <style>
        :root {
            --primary: #1a73e8;
            --accent: #e8f0fe;
            --bg: #f9fafb;
            --text: #333;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg);
            color: var(--text);
        }
        .container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: var(--primary);
        }
        .description {
            font-size: 17px;
            line-height: 1.6;
            margin-bottom: 40px;
            text-align: justify;
        }
        .profile-section {
            margin-top: 40px;
        }
        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        .profile-card {
            background-color: var(--accent);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .profile-card:hover {
            transform: translateY(-5px);
        }
        .profile-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .profile-card strong {
            display: block;
            font-size: 18px;
            margin-bottom: 10px;
            color: #222;
        }
        .profile-card p {
            font-size: 15px;
            margin-bottom: 10px;
        }
        .profile-card a {
            color: var(--primary);
            font-weight: bold;
            text-decoration: none;
        }
        .profile-card a:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>About Us</h1>

        <div class="description">
            <p>This project was developed during the Summer Research Internship Program (SRIP) at IIT Gandhinagar, combining software and hardware design to build two smart systems: <strong>Atmosync</strong> and <strong>Hubvolt</strong>. The systems feature real-time logging, PHP-based admin panels, and Node.js-based backend logging servers.</p>

            <p>The <strong>Hubvolt & AtmoSync — Hardware & Firmware Resources</strong> repository hosts all embedded system assets, including:</p>

            <ul>
                <li><strong>KiCAD Schematics</strong> – Detailed hardware circuit diagrams in KiCAD and PDF formats</li>
                <li><strong>Gerber Files</strong> – Manufacturing-ready PCB layout files for both systems</li>
                <li><strong>Firmware</strong> – Arduino-compatible C++ source code for ESP32 boards</li>
                <li><strong>3D Enclosure Designs</strong> – STL/STEP files for custom device enclosures</li>
            </ul>

            <p>Visit the resource repo here: <a href="https://github.com/DankJugal/SRIP_FINAL_RESOURCES" target="_blank">SRIP_FINAL_RESOURCES</a></p>

            <p>These contributions represent a complete end-to-end IoT solution — from PCB layout and firmware to frontend dashboards and logging infrastructure — built with educational and practical impact in mind.</p>
        </div>

        <div class="profile-section">
            <h2>Student Contributors</h2>
            <div class="profile-grid">
                <div class="profile-card">
                    <img src="https://media.licdn.com/dms/image/v2/D4D03AQHATsJ-MUscoQ/profile-displayphoto-shrink_400_400/profile-displayphoto-shrink_400_400/0/1728203810051?e=1757548800&v=beta&t=KPLa2b6_XAYECdJFv78YaPPHJ3V4dBTbHcjSes33ACI" alt="Jugal Patel">
                    <strong>Jugal Patel</strong>
                    <p>A passionate developer with strong interests in full-stack web development and IoT systems. Contributed extensively to backend and frontend integration.</p>
                    <a href="https://www.linkedin.com/in/jugal-patel-92391531b/?originalSubdomain=in" target="_blank">LinkedIn Profile</a>
                </div>

                <div class="profile-card">
                    <img src="https://media.licdn.com/dms/image/v2/D4D35AQHLXQCzd-ou-g/profile-framedphoto-shrink_400_400/B4DZWBRiLmGcAc-/0/1741630612464?e=1752418800&v=beta&t=YmzLsrgzRsM7krwSqsgP-lRcjKw3vJd1FpR0rIDP8Zc" alt="Bhumil Rangholiya">
                    <strong>Bhumil Rangholiya</strong>
                    <p>Software enthusiast with a focus on real-time data and communication layers. Handled server architecture and MySQL integration.</p>
                    <a href="https://www.linkedin.com/in/bhumil-rangholiya/?originalSubdomain=in" target="_blank">LinkedIn Profile</a>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h2>Mentors</h2>
            <div class="profile-grid">
                <div class="profile-card">
                    <img src="https://media.licdn.com/dms/image/v2/C4E03AQGSCD4acero4w/profile-displayphoto-shrink_400_400/profile-displayphoto-shrink_400_400/0/1556637983958?e=1757548800&v=beta&t=CxaPHGrctcQI5LfofjJmAoARDPrW2efdDTXKuiNXBK8" alt="Rajni Jain Moona">
                    <strong>Prof. Rajni Jain Moona</strong>
                    <p>Faculty at IIT Gandhinagar with expertise in embedded systems and interdisciplinary design. Guided the entire development life cycle.</p>
                    <a href="https://www.linkedin.com/in/rajni-jain-moona-7a3b9713/" target="_blank">LinkedIn Profile</a>
                </div>

                <div class="profile-card">
                    <img src="https://media.licdn.com/dms/image/v2/D4D03AQGxumZ-SYXlzg/profile-displayphoto-shrink_400_400/profile-displayphoto-shrink_400_400/0/1726013771612?e=1757548800&v=beta&t=umv7FWIy7GI3OHZXA_acPqDlflZNAj29kDqryuZtezo" alt="Rajat Moona">
                    <strong>Prof. Rajat Moona</strong>
                    <p>Director of IIT Gandhinagar and expert in cybersecurity and embedded technology. Provided visionary technical and strategic mentorship.</p>
                    <a href="https://www.linkedin.com/in/rajatmoona/?originalSubdomain=in" target="_blank">LinkedIn Profile</a>
                </div>

                <div class="profile-card">
                    <img src="https://www.ee.iitb.ac.in/wiki/_media/faculty/dinesh.jpg?w=130&tok=8c0c2e" alt="Dinesh Sharma">
                    <strong>Prof. Dinesh Sharma</strong>
                    <p>Faculty at IIT Bombay in Electrical Engineering, with contributions in embedded design and digital systems. Provided essential system-level insight.</p>
                    <a href="https://www.ee.iitb.ac.in/wiki/faculty/dinesh" target="_blank">IIT Bombay Profile</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
