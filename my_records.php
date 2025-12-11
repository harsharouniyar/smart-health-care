<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM medical_records WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Medical Records</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body { margin:0; font-family:Poppins; background:#eef; padding:40px; }
        .container { max-width:700px; margin:auto; background:white; padding:25px; border-radius:12px; }
        h2 { text-align:center; margin-bottom:20px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:12px; border-bottom:1px solid #ddd; }
        .btn { background:#1e3c72; padding:10px 14px; color:white; text-decoration:none; border-radius:8px; }
    </style>
</head>

<body>

<div class="container">
    <h2>📄 My Medical Records</h2>

    <?php if (isset($_GET['success'])): ?>
        <p style="background:#4CAF50; color:white; padding:10px; border-radius:6px;">
            Record uploaded successfully!
        </p>
    <?php endif; ?>

    <a class="btn" href="upload_record.php">Upload New Record</a>

    <table>
        <tr>
            <th>Title</th>
            <th>Notes</th>
            <th>File</th>
            <th>Date</th>
        </tr>

        <?php foreach ($records as $r): ?>
        <tr>
            <td><?= $r['title'] ?></td>
            <td><?= $r['notes'] ?></td>
            <td><a href="<?= $r['file_path'] ?>" target="_blank">View</a></td>
            <td><?= $r['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <br>
    <a class="btn" href="index.php">Back to Dashboard</a>
</div>

</body>
</html>
