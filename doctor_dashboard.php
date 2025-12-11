<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

$doctor_user_id = $_SESSION['user_id'];

// Get doctor ID
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
$stmt->execute([$doctor_user_id]);
$doctor_id = $stmt->fetchColumn();

// Get doctor appointments
$appointments = $pdo->prepare("
    SELECT a.*, u.name AS patient_name, u.id AS patient_id
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    WHERE a.doctor_id = ?
    ORDER BY a.date ASC, a.time ASC
");
$appointments->execute([$doctor_id]);
$data = $appointments->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin:0;
            background:#eef;
            font-family:Poppins;
            padding:20px;
        }

        table {
            width:100%;
            background:white;
            border-collapse:collapse;
            margin-top:20px;
            border-radius:10px;
            overflow:hidden;
        }

        th {
            background:#1e3c72;
            color:white;
            padding:12px;
        }

        td {
            padding:12px;
            border-bottom:1px solid #ddd;
        }

        .menu-link {
            display:inline-block;
            padding:10px 15px;
            background:#1e3c72;
            color:white;
            border-radius:8px;
            text-decoration:none;
            margin-right:10px;
            font-weight:600;
        }

        .btn-small {
            padding:6px 10px;
            background:#1e3c72;
            color:white;
            border:none;
            border-radius:6px;
            cursor:pointer;
        }

        .btn-upload {
            padding:6px 12px;
            background:#36a3e5;
            color:white;
            border-radius:6px;
            text-decoration:none;
            font-size:14px;
            font-weight:600;
            display:inline-block;
        }

        .btn-upload:hover {
            background:#1c8fd4;
        }

        td.upload-cell {
            text-align:center;
            width:140px;
        }
    </style>
</head>

<body>

<h2>👨‍⚕️ Doctor Dashboard</h2>
<p>Welcome, Dr. <?= htmlspecialchars($_SESSION['name']) ?></p>

<!-- Buttons -->
<a href="doctor_view_records.php" class="menu-link">📁 Patient Medical Records</a>
<a href="doctor_timeslots.php" class="menu-link">🕒 Manage Time Slots</a>
<a href="logout.php" class="menu-link" style="background:#ff6b6b;">Logout</a>

<table>
    <tr>
        <th>Patient</th>
        <th>Date</th>
        <th>Time</th>
        <th>Message</th>
        <th>Status</th>
        <th>Update</th>
        <th>Upload Record</th>
    </tr>

    <?php foreach ($data as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['patient_name']) ?></td>
            <td><?= htmlspecialchars($a['date']) ?></td>
            <td><?= htmlspecialchars($a['time']) ?></td>
            <td><?= htmlspecialchars($a['message']) ?></td>
            <td><?= ucfirst($a['status']) ?></td>

            <!-- STATUS UPDATE -->
            <td>
                <form method="POST" action="update_status.php" style="display:flex; gap:6px;">
                    <input type="hidden" name="id" value="<?= $a['id'] ?>">

                    <select name="status">
                        <option value="pending"   <?= $a['status']=="pending"?"selected":"" ?>>Pending</option>
                        <option value="confirmed" <?= $a['status']=="confirmed"?"selected":"" ?>>Confirmed</option>
                        <option value="completed" <?= $a['status']=="completed"?"selected":"" ?>>Completed</option>
                    </select>

                    <button class="btn-small">Save</button>
                </form>
            </td>

            <!-- UPLOAD RECORD BUTTON -->
            <td class="upload-cell">
                 <a href="doctor_upload_record.php?patient=<?= $a['patient_id'] ?>" class="btn-upload">📤 Upload</a>
            </td>

        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
