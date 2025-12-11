<?php
require "config/db.php";

$cat = $_GET['cat'] ?? null;

if (!$cat) { echo json_encode([]); exit; }

$stmt = $pdo->prepare("
    SELECT d.id, u.name, d.experience
    FROM doctor_category_map m
    JOIN doctors d ON m.doctor_id = d.id
    JOIN users u ON d.user_id = u.id
    WHERE m.category_id = ?
");
$stmt->execute([$cat]);
echo json_encode($stmt->fetchAll());
