<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $pdo->prepare("UPDATE users SET name=?, phone=?, address=? WHERE id=?");
    $stmt->execute([$name, $phone, $address, $id]);

    $_SESSION["name"] = $name;
    $msg = "Profile updated successfully!";
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | Smart Health</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            display: flex;
            justify-content: center;
            padding-top: 70px;
        }

        /* NAVBAR */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
        }

        /* PROFILE CARD */
        .card {
            width: 420px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(12px);
            padding: 30px;
            border-radius: 14px;
            text-align: center;
            color: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
        }

        .avatar {
            width: 95px;
            height: 95px;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 3px solid white;
        }

        input {
            width: 100%;
            padding: 13px;
            margin: 10px 0;
            border-radius: 8px;
            border: none;
            font-size: 15px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #4fa3f7;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            color: white;
            cursor: pointer;
            margin-top: 12px;
            font-weight: 600;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .success {
            background: #4CAF50;
            padding: 10px;
            border-radius: 6px;
            color: white;
            margin-bottom: 10px;
            font-size: 14px;
        }

        a.back {
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            font-size: 15px;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo" style="font-size:22px;font-weight:700;">Smart Health</div>
    <div>
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- PROFILE CARD -->
<div class="card">
    
    <img class="avatar" src="https://cdn-icons-png.flaticon.com/512/1946/1946429.png" alt="Profile">

    <h2>My Profile</h2>
    
    <?php if (!empty($msg)) echo "<div class='success'>$msg</div>"; ?>

    <form method="POST">

        <input name="name" value="<?= $user['name'] ?>" placeholder="Full Name" required>

        <input name="phone" value="<?= $user['phone'] ?>" placeholder="Phone Number">

        <input name="address" value="<?= $user['address'] ?>" placeholder="Address">

        <button class="btn">Update Profile</button>

    </form>

    <a class="back" href="index.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
