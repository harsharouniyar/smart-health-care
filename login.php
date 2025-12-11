<?php
session_start();
require __DIR__ . "/config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];

        // Redirect based on role
        if ($user["role"] === "admin") {
            header("Location: admin_dashboard.php");
            exit;
        } elseif ($user["role"] === "doctor") {
            header("Location: doctor_dashboard.php");
            exit;
        } else {
            header("Location: index.php");
            exit;
        }

    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Smart Health</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .login-card {
            width: 350px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(14px);
            padding: 30px;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0px 8px 25px rgba(0,0,0,0.25);
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .input {
            width: 100%;
            padding: 13px;
            margin: 10px 0;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 15px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #36d1dc, #5b86e5);
            border: none;
            border-radius: 10px;
            font-size: 17px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn:hover { opacity: 0.9; }

        .error {
            background: #ff4d4d;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 12px;
            font-size: 14px;
        }

        a {
            color: #e1e1e1;
            text-decoration: none;
            font-size: 14px;
        }

        a:hover { text-decoration: underline; }
    </style>
</head>

<body>

<div class="login-card">

    <div class="logo" style="font-size:22px;font-weight:700;">
    Smart Health
    <div style="font-size:12px; opacity:0.75; margin-top:-3px;">
        Smart Care for a Healthier Tomorrow.
    </div>
</div>


    <h2>Welcome Back</h2>
    <p style="opacity:0.8;">Login to continue</p>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input class="input" type="email" name="email" placeholder="Email Address" required>
        <input class="input" type="password" name="password" placeholder="Password" required>
        <button class="btn" type="submit">Login</button>
    </form>

    <p style="margin-top: 12px;">
        New here? <a href="register.php">Create an account</a>
    </p>

</div>

</body>
</html>
