<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Health - Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #fff;
        }

        /* NAVBAR */
        .navbar {
            width: 100%;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(6px);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .logo {
            font-size: 22px;
            font-weight: 700;
        }
        .navbar small {
            font-size: 12px;
            opacity: 0.75;
            display: block;
            margin-top: 2px;
        }
        .navbar a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
            font-weight: 500;
        }

        /* HERO */
        .hero {
            text-align: center;
            padding: 70px 20px 10px;
        }
        .hero h1 {
            font-size: 40px;
            margin-bottom: 8px;
        }
        .hero p {
            opacity: 0.9;
            font-size: 18px;
        }

        /* MENU */
        .menu-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 25px;
            gap: 15px;
        }
        .menu-card {
            width: 320px;
            background: rgba(255,255,255,0.12);
            padding: 16px;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: 0.3s;
            color: #fff;
        }
        .menu-card:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-4px);
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 45px;
            opacity: 0.7;
            padding-bottom: 20px;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">
        Smart Health
        <small>Smart Care for a Healthier Tomorrow.</small>
    </div>
    <div>
        <a href="index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <h1>Welcome, <?= htmlspecialchars($name) ?> 👋</h1>
    <p>Your personal health dashboard</p>
</div>

<!-- MENU CARDS -->
<div class="menu-container">

    <a href="doctor_categories.php"><div class="menu-card">👨‍⚕️ Find Doctors by Category</div></a>

    <a href="appointments_list.php"><div class="menu-card">📅 My Appointments</div></a>

    <a href="appointment.php"><div class="menu-card">📝 Book Appointment</div></a>

    <a href="my_records.php"><div class="menu-card">📄 My Medical Records</div></a>

    <a href="upload_record.php"><div class="menu-card">⬆ Upload Medical Records</div></a>

    <a href="contact.php"><div class="menu-card">☎ Contact Us</div></a>

</div>

<!-- FOOTER -->
<div class="footer">© <?= date("Y") ?> Smart Health — All Rights Reserved</div>

</body>
</html>
