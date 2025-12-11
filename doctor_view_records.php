<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

$doctor_user_id = $_SESSION['user_id'];

// First, get the doctor.id from doctors table
$docStmt = $pdo->prepare("SELECT id FROM doctors WHERE user_id = ?");
$docStmt->execute([$doctor_user_id]);
$doctor = $docStmt->fetch();

$doctor_id = $doctor['id'];

// Get all patients who booked appointments with this doctor
$stmt = $pdo->prepare("
    SELECT DISTINCT u.id, u.name, u.email
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    WHERE a.doctor_id = ?
");
$stmt->execute([$doctor_id]);
$patients = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Patient Medical Records | Doctor Panel</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f1f4fb;
            padding: 30px;
        }

        h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .no-data {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            font-size: 16px;
            margin-top: 20px;
        }

        .patient-card {
            background: white;
            padding: 25px;
            border-radius: 14px;
            margin-bottom: 35px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.08);
        }

        .patient-header {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fafafa;
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background: #2a5298;
            color: white;
            padding: 12px;
            font-size: 14px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover td {
            background: #eef3ff;
        }

        .file-btn {
            padding: 7px 12px;
            background: #36d1dc;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
        }
        .file-btn:hover {
            background: #2a8aad;
        }

    </style>
</head>
<body>

<h2>📁 Patient Medical Records</h2>

<?php if (count($patients) == 0): ?>
    <div class="no-data">
        No patients have booked an appointment with you yet.
    </div>

<?php else: ?>

    <?php foreach ($patients as $p): ?>

    <div class="patient-card">
        <div class="patient-header">👤 <?= htmlspecialchars($p['name']) ?></div>

        <?php
        $stmt2 = $pdo->prepare("SELECT * FROM medical_records WHERE user_id = ?");
        $stmt2->execute([$p['id']]);
        $records = $stmt2->fetchAll();
        ?>

        <?php if (count($records) == 0): ?>
            <p style="color:#666; margin:10px 0;">No medical records uploaded by this patient.</p>

        <?php else: ?>

            <table>
                <tr>
                    <th>Title</th>
                    <th>Notes</th>
                    <th>File</th>
                    <th>Date</th>
                </tr>

                <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($r['notes'])) ?></td>
                    <td>
                        <a class="file-btn" href="<?= $r['file_path'] ?>" target="_blank">View File</a>
                    </td>
                    <td><?= $r['created_at'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php endif; ?>

    </div>

    <?php endforeach; ?>

<?php endif; ?>

</body>
</html>
