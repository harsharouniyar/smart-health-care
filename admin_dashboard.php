<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// FETCH all appointments with patient + doctor names
$stmt = $pdo->query("
    SELECT a.*, u.name AS patient_name, d.name AS doctor_name
    FROM appointments a
    LEFT JOIN users u ON a.user_id = u.id
    LEFT JOIN users d ON a.doctor_id = d.id
    ORDER BY a.date DESC
");
$appointments = $stmt->fetchAll();

// FETCH ALL DOCTORS
$doctorList = $pdo->query("
    SELECT u.id, u.name 
    FROM users u 
    WHERE u.role = 'doctor'
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body { margin:0; font-family:Poppins; background:#eef; padding-left:240px; }
        .sidebar {
            width:220px; background:#1e3c72; height:100vh;
            position:fixed; top:0; left:0; color:white; padding-top:20px;
        }
        .sidebar h2 { text-align:center; }
        .sidebar a {
            display:block; padding:12px 20px; color:white; text-decoration:none;
        }
        .sidebar a:hover { background:rgba(255,255,255,0.2); }

        .container { padding:30px; }
        table { width:100%; border-collapse:collapse; background:white; }
        th { background:#1e3c72; color:white; padding:12px; }
        td { padding:12px; border-bottom:1px solid #ddd; }

        select, button {
            padding:6px;
            border-radius:6px;
            margin-right: 5px;
        }

        .btn-small {
            padding:6px 10px;
            background:#1e3c72;
            color:white;
            border:none;
            border-radius:6px;
            cursor:pointer;
        }
        .btn-small:hover {
            opacity:0.8;
        }
    </style>
</head>

<body>

<div class="sidebar">
    <h2>Smart Health</h2>

    <a href="admin_dashboard.php">📋 Appointments</a>
    <a href="admin_messages.php">📩 Messages</a>
    <a href="admin_messages.php">📩 View Messages</a>

    <a href="doctor_categories.php">📋 Doctors by Category</a>
    <a href="register.php">➕ Add New Doctor</a>
    <a href="logout.php" style="background:#ff6b6b;">Logout</a>
</div>

<div class="container">
    <h2>🛠 Manage Appointments</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Date</th>
            <th>Time</th>
            <th>Doctor</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach($appointments as $a): ?>
        <tr>
            <td><?= $a['id'] ?></td>
            <td><?= $a['patient_name'] ?></td>
            <td><?= $a['date'] ?></td>
            <td><?= $a['time'] ?></td>

            <td>
                <form method="POST" action="update_status.php" style="display:flex;">
                    <input type="hidden" name="id" value="<?= $a['id'] ?>">

                    <select name="doctor_id">
                        <option value="">Not Assigned</option>
                        <?php foreach ($doctorList as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= ($a['doctor_id']==$d['id'])?"selected":"" ?>>
                                <?= $d['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
            </td>

            <td>
                <select name="status">
                    <option value="pending"   <?= $a['status']=="pending"?"selected":"" ?>>Pending</option>
                    <option value="confirmed" <?= $a['status']=="confirmed"?"selected":"" ?>>Confirmed</option>
                    <option value="completed" <?= $a['status']=="completed"?"selected":"" ?>>Completed</option>
                    <option value="cancelled" <?= $a['status']=="cancelled"?"selected":"" ?>>Cancelled</option>
                </select>
            </td>

            <td>
                <button class="btn-small">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

</body>
</html>
