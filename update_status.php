<?php
session_start();
require "config/db.php";

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Only admin or doctor can update status
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

// Validate POST inputs
if (!isset($_POST['id']) || !isset($_POST['status'])) {
    header("Location: admin_dashboard.php?error=invalid");
    exit;
}

$id = $_POST['id'];
$status = $_POST['status'];

// Update appointment status
$stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

// Redirect based on role
if ($_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php?updated=1");
} else {
    header("Location: doctor_dashboard.php?updated=1");
}
exit;
?>
