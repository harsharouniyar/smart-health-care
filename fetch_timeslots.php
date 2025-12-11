<?php
require "config/db.php";

$doctor = $_GET['doctor'] ?? null;
$date   = $_GET['date'] ?? null;

if (!$doctor || !$date) { 
    echo json_encode([]); 
    exit; 
}

// Fetch doctor time range
$stmt = $pdo->prepare("
    SELECT slot_start, slot_end 
    FROM doctor_timeslots
    WHERE doctor_id = ? AND slot_date = ?
");
$stmt->execute([$doctor, $date]);
$periods = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch ALL booked times (pending, confirmed, completed)
$bookedStmt = $pdo->prepare("
    SELECT time 
    FROM appointments 
    WHERE doctor_id = ? AND date = ?
      AND status IN ('pending', 'confirmed', 'completed')
");
$bookedStmt->execute([$doctor, $date]);
$bookedTimes = $bookedStmt->fetchAll(PDO::FETCH_COLUMN);

// Normalize time format
$bookedTimes = array_map(function($t) {
    return date("H:i", strtotime($t));
}, $bookedTimes);

$slots = [];

foreach ($periods as $p) {
    $start = strtotime($p['slot_start']);
    $end   = strtotime($p['slot_end']);

    while ($start < $end) {
        $slotStart = date("H:i", $start);
        $slotEnd   = date("H:i", $start + 1800);

        $slots[] = [
            "start"  => $slotStart,
            "end"    => $slotEnd,
            "booked" => in_array($slotStart, $bookedTimes)
        ];

        $start += 1800;
    }
}

echo json_encode($slots);
?>
