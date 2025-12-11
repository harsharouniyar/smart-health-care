<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Medical Record | Smart Health</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: Poppins;
            background: linear-gradient(135deg,#1e3c72,#2a5298);
            color: white;
            display: flex;
            justify-content: center;
            padding-top: 80px;
        }

        .container {
            width: 420px;
            background: rgba(255,255,255,0.15);
            padding: 25px;
            border-radius: 15px;
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
        }

        h2 {
            margin-bottom: 10px;
            text-align: center;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            margin: 8px 0;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg,#36d1dc,#5b86e5);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover { opacity: 0.9; }
        a { color:white; text-decoration:none; }
    </style>
</head>

<body>

<div class="container">
    <h2>📄 Upload Medical Record</h2>

    <form action="upload_record_process.php" method="POST" enctype="multipart/form-data">

        <input type="text" name="title" placeholder="Record Title (e.g. Blood Test)" required>

        <textarea name="notes" placeholder="Add notes (optional)" rows="4"></textarea>

        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required>

        <button class="btn">Upload Record</button>
    </form>

    <a href="my_records.php"><p style="text-align:center; margin-top:12px;">Back</p></a>
</div>

</body>
</html>
