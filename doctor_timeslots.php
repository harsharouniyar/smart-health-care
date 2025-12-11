<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

// find doctor internal id
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$doctor_id = $stmt->fetch()['id'];

// load timeslots
$slots = $pdo->prepare("
    SELECT * FROM doctor_timeslots 
    WHERE doctor_id = ?
    ORDER BY slot_date ASC, slot_start ASC
");
$slots->execute([$doctor_id]);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Time Slots</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="navbar">
    <div class="logo">Smart Health <small>Doctor Portal</small></div>
    <div>
        <a href="doctor_dashboard.php">Dashboard</a>
        <a href="doctor_timeslots.php">Time Slots</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="card" style="max-width:600px;">

<h2>Add Available Time Slot</h2>

<form method="POST" action="doctor_timeslot_add.php">
    <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">

    <label>Date</label>
    <input class="input" type="date" name="slot_date" required>

    <label>Start Time</label>
    <input class="input" type="time" name="slot_start" required>

    <label>End Time</label>
    <input class="input" type="time" name="slot_end" required>

    <button class="btn">Add Slot</button>
</form>

<hr>

<h3>Your Slots</h3>
<table>
<tr>
    <th>Date</th>
    <th>Start</th>
    <th>End</th>
    <th>Action</th>
</tr>

<?php foreach($slots as $s): ?>
<tr>
    <td><?= $s['slot_date'] ?></td>
    <td><?= $s['slot_start'] ?></td>
    <td><?= $s['slot_end'] ?></td>
    <td>
        <form method="POST" action="doctor_timeslot_delete.php">
            <input type="hidden" name="id" value="<?= $s['id'] ?>">
            <button class="btn" style="background:#ff4d4d">Delete</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>

</table>

</div>

</body>
</html>
