<?php
require "config/db.php";
session_start();

if ($_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

$id = $_POST['id'];

$stmt = $pdo->prepare("DELETE FROM doctor_timeslots WHERE id = ?");
$stmt->execute([$id]);

header("Location: doctor_timeslots.php");
exit;
