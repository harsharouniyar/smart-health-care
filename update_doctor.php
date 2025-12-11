<?php
require "config/db.php";

$id = $_POST['id'];
$doctor_id = $_POST['doctor_id'] ?: NULL;

$stmt = $pdo->prepare("UPDATE appointments SET doctor_id=? WHERE id=?");
$stmt->execute([$doctor_id, $id]);

header("Location: admin_dashboard.php");
exit();
