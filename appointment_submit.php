<?php
session_start();
require "config/db.php";
require __DIR__ . "/config/mail.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id   = $_SESSION["user_id"];
$date      = $_POST["date"];
$time      = $_POST["time"];
$message   = $_POST["message"];
$doctor_id = $_POST["doctor_id"];

// INSERT APPOINTMENT
$stmt = $pdo->prepare("
    INSERT INTO appointments (user_id, doctor_id, date, time, message)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([$user_id, $doctor_id, $date, $time, $message]);

// FETCH PATIENT DETAILS
$patientStmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$patientStmt->execute([$user_id]);
$patient = $patientStmt->fetch();

// FETCH DOCTOR DETAILS
$doctorStmt = $pdo->prepare("
    SELECT u.name, u.email 
    FROM users u 
    JOIN doctors d ON d.user_id = u.id
    WHERE d.id = ?
");
$doctorStmt->execute([$doctor_id]);
$doctor = $doctorStmt->fetch();

// PATIENT EMAIL
$patientSubject = "✔ Appointment Confirmed — Smart Health";
$patientBody = "
<div style='font-family:Poppins,Arial;margin:20px;padding:20px;background:#f4f6f9;border-radius:8px;'>
    <h2 style='color:#2a5298;'>Hello {$patient['name']},</h2>
    <p>Your appointment has been successfully booked.</p>

    <div style='padding:15px;background:white;border-radius:8px;border-left:4px solid #2a5298;margin-top:10px;'>
        <b>📅 Date:</b> {$date}<br>
        <b>⏰ Time:</b> {$time}<br>
        <b>👨‍⚕️ Doctor:</b> Dr. {$doctor['name']}<br>
    </div>

    <p style='margin-top:20px;'>Thank you for choosing <b>Smart Health</b>.</p>
</div>
";

sendMail($patient["email"], $patientSubject, $patientBody);

// DOCTOR EMAIL
$doctorSubject = "📅 New Appointment Booked — Smart Health";
$doctorBody = "
<div style='font-family:Poppins,Arial;margin:20px;padding:20px;background:#f4f6f9;border-radius:8px;'>
    <h2 style='color:#1e3c72;'>Dear Dr. {$doctor['name']},</h2>
    <p>A new appointment has been scheduled with you.</p>

    <div style='padding:15px;background:white;border-radius:8px;border-left:4px solid #1e3c72;margin-top:10px;'>
        <b>🧍 Patient:</b> {$patient['name']}<br>
        <b>📅 Date:</b> {$date}<br>
        <b>⏰ Time:</b> {$time}<br>
        <b>📝 Message:</b> {$message}<br>
    </div>

    <p style='margin-top:20px;'>Please check the appointment inside your dashboard.</p>
</div>
";

sendMail($doctor["email"], $doctorSubject, $doctorBody);

// REDIRECT CLEANLY TO APPOINTMENTS PAGE
header("Location: appointments_list.php?success=1");
exit;
