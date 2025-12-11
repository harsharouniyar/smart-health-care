<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['patient'])) {
    die("Invalid patient.");
}

$doctor_user_id = $_SESSION['user_id'];
$patient_id = $_GET['patient'];

// Get REAL doctor_id
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
$stmt->execute([$doctor_user_id]);
$doctor_id = $stmt->fetchColumn();

// Validate doctor actually has this patient
$check = $pdo->prepare("
    SELECT COUNT(*) 
    FROM appointments 
    WHERE doctor_id = ? AND user_id = ?
");
$check->execute([$doctor_id, $patient_id]);  // FIXED

if ($check->fetchColumn() == 0) {
    die("You cannot upload records for a patient not assigned to you.");
}

// If form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST["title"];
    $notes = $_POST["notes"] ?? "";
    $filePath = "";

    // FILE UPLOAD
    if (!empty($_FILES["file"]["name"])) {
        $targetDir = "uploads/records/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $filename = time() . "_" . basename($_FILES["file"]["name"]);
        $filePath = $targetDir . $filename;

        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
            die("File upload failed. Check folder permissions.");
        }
    }

    // INSERT INTO DATABASE
    $stmt = $pdo->prepare("
        INSERT INTO medical_records (user_id, title, notes, file_path)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$patient_id, $title, $notes, $filePath]);

    header("Location: doctor_view_records.php?uploaded=1");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Medical Record</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body { background:#eef; font-family:Poppins; padding:20px; }
        .box {
            width:450px; margin:auto; background:white; padding:25px;
            border-radius:12px; box-shadow:0 4px 25px rgba(0,0,0,0.1);
        }
        input, textarea {
            width:100%; padding:10px; margin-top:10px;
            border-radius:8px; border:1px solid #ccc;
        }
        button {
            width:100%; padding:12px; margin-top:15px;
            background:#1e3c72; color:white; border:none;
            border-radius:8px; cursor:pointer;
        }
        button:hover { opacity:.9; }
    </style>
</head>

<body>

<div class="box">
    <h2>📄 Upload Medical Record</h2>

    <form method="POST" enctype="multipart/form-data">
        <label>Record Title</label>
        <input type="text" name="title" required>

        <label>Notes</label>
        <textarea name="notes" required></textarea>

        <label>Upload File (PDF/Image)</label>
        <input type="file" name="file" accept="application/pdf,image/*" required>

        <button type="submit">Upload Record</button>
    </form>
</div>

</body>
</html>
