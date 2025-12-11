<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info to autofill
$stmt = $pdo->prepare("SELECT name, email, phone FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$success = "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Support | Smart Health</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: Poppins;
            background: linear-gradient(135deg,#1e3c72,#2a5298);
            color: white;
        }

        .navbar {
            width: 100%;
            display:flex;
            justify-content: space-between;
            align-items:center;
            padding: 15px 40px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(8px);
            position: sticky;
            top: 0;
        }
        .navbar a { color:white; text-decoration:none; margin-left:20px; }

        .container {
            max-width: 450px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(14px);
            padding: 25px;
            margin: 70px auto;
            text-align:center;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
        }

        .input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            margin-top: 10px;
        }

        textarea {
            height: 120px;
            resize: none;
        }

        .btn {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: none;
            background: linear-gradient(90deg,#36d1dc,#5b86e5);
            color:white;
            border-radius:10px;
            font-size:16px;
            cursor:pointer;
            font-weight:600;
        }

        .success {
            background: #4CAF50;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

    </style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div style="font-weight:700;font-size:22px;">
        Smart Health
        <div style="font-size:12px;opacity:0.75;margin-top:-3px;">
            Smart Care for a Healthier Tomorrow.
        </div>
    </div>
    <div>
        <a href="index.php">Home</a>
        <a href="appointments_list.php">Appointments</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- CONTACT FORM -->
<div class="container">

    <h2>📩 Contact Support</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="success">Message sent successfully!</div>
    <?php endif; ?>

    <form method="POST" action="contact_submit.php">

        <input class="input" type="text" name="name" value="<?= $user['name'] ?>" required>
        <input class="input" type="email" name="email" value="<?= $user['email'] ?>" required>
        <input class="input" type="text" name="phone" value="<?= $user['phone'] ?>" placeholder="Phone">

        <textarea class="input" name="message" placeholder="Write your message..." required></textarea>

        <button class="btn">Send Message</button>
    </form>
</div>

</body>
</html>
