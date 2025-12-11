<?php
require "config/db.php";

$cat = $_GET['cat'] ?? 0;

$stmt = $pdo->prepare("
    SELECT 
        d.id,
        u.name,
        d.experience,
        d.fees,
        d.bio,
        c.name AS category
    FROM doctor_category_map m
    JOIN doctors d ON m.doctor_id = d.id
    JOIN users u ON d.user_id = u.id
    JOIN doctor_categories c ON c.id = m.category_id
    WHERE m.category_id = ?
");
$stmt->execute([$cat]);

$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add placeholder image for all doctors
foreach ($doctors as &$doc) {
    $doc['photo'] = "assets/default-doctor.jpg";
}

echo json_encode($doctors);
