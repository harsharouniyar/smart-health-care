<?php
session_start();
require "config/db.php";

// Restrict access if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all appointments for logged-in patient
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY date DESC");
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments | Smart Health</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
        }

        /* NAVBAR */
        .navbar {
            width: 100%;
            padding: 15px 40px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(6px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            margin-left: 20px;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 32px;
            font-weight: 700;
        }

        .success {
            background: #4CAF50;
            padding: 10px;
            text-align: center;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .appointment-card {
            background: rgba(255,255,255,0.18);
            padding: 18px;
            border-radius: 14px;
            margin-bottom: 15px;
        }

        .appointment-card strong {
            font-size: 18px;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 6px;
            margin-top: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .pending { background: orange; }
        .confirmed { background: green; }
        .cancelled { background: #ff4d4d; }
        .completed { background: #357edd; }

        .btn-back {
            width: 100%;
            padding: 12px;
            border: none;
            background: linear-gradient(90deg,#36d1dc,#5b86e5);
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            cursor: pointer;
        }

        .btn-back:hover { opacity: 0.9; }
    </style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div style="font-weight:700;font-size:20px;">Smart Health</div>
    <div>
        <a href="index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- MAIN CONTAINER -->
<div class="container">

    <h1>📅 My Appointments</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="success">✔ Appointment booked successfully!</div>
    <?php endif; ?>

    <?php if (count($appointments) == 0): ?>
        <p style="text-align:center;font-size:18px;">No appointments yet.</p>

    <?php else: ?>
        <?php foreach ($appointments as $a): ?>
            <div class="appointment-card">
                <strong><?= htmlspecialchars($a['date']) ?> at <?= htmlspecialchars($a['time']) ?></strong>

                <p><?= nl2br(htmlspecialchars($a['message'])) ?></p>

                <!-- Status -->
                <div class="status <?= $a['status'] ?>">
                    <?= ucfirst($a['status']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="index.php">
        <button class="btn-back">⬅ Back to Dashboard</button>
    </a>

</div>

</body>
</html>
