<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$message = $_POST['message'];

$stmt = $pdo->prepare("
    INSERT INTO contacts (user_id, name, email, phone, message)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([$user_id, $name, $email, $phone, $message]);

header("Location: contact.php?success=1");
exit;
?>
