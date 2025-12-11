<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$messages = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Messages | Admin Panel</title>
    <style>
        body {
            font-family: Poppins, sans-serif;
            background: #eef2f7;
            padding: 30px;
        }
        h2 { margin-bottom: 20px; }

        .msg-card {
            background: white;
            padding: 18px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .msg-card b { color: #1e3c72; }
        .divider {
            height: 1px;
            background: #ddd;
            margin: 10px 0;
        }
    </style>
</head>

<body>

<h2>📩 Patient Messages</h2>

<?php if (count($messages) == 0): ?>
    <p>No messages found.</p>

<?php else: ?>
    <?php foreach ($messages as $m): ?>
        <div class="msg-card">
            <b>Name:</b> <?= $m['name'] ?><br>
            <b>Email:</b> <?= $m['email'] ?><br>
            <b>Phone:</b> <?= $m['phone'] ?><br>
            <div class="divider"></div>
            <b>Message:</b><br>
            <?= nl2br($m['message']) ?><br><br>
            <small>📅 <?= $m['created_at'] ?></small>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
