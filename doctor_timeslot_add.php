<?php
require "config/db.php";
session_start();

$doctor_id = $_POST['doctor_id'];
$slot_date = $_POST['slot_date'];
$slot_start = $_POST['slot_start'];
$slot_end = $_POST['slot_end'];

$stmt = $pdo->prepare("
    INSERT INTO doctor_timeslots (doctor_id, slot_date, slot_start, slot_end)
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$doctor_id, $slot_date, $slot_start, $slot_end]);

header("Location: doctor_timeslots.php");
exit;
