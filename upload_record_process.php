<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id'];

// Create directory if not exists
$targetDir = "uploads/records/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$title = $_POST["title"];
$notes = $_POST["notes"] ?? "";
$filePath = "";

// File upload check
if (!empty($_FILES["file"]["name"])) {

    $filename = time() . "_" . basename($_FILES["file"]["name"]);
    $filePath = $targetDir . $filename;

    // Check upload errors
    if ($_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
        die("Upload failed with error code: " . $_FILES["file"]["error"]);
    }

    // Try uploading file
    if (!move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
        die("File upload failed. Folder may not be writable.");
    }
}

// Insert into database
$stmt = $pdo->prepare("
    INSERT INTO medical_records (user_id, title, notes, file_path)
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$user_id, $title, $notes, $filePath]);

header("Location: my_records.php?success=1");
exit;
?>
