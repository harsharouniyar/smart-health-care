<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM doctor_categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Appointment | Smart Health</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
body {
    margin: 0;
    font-family: "Inter", sans-serif;
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    color: white;
}

/* NAVBAR */
.navbar {
    width: 100%;
    padding: 18px 45px;
    background: rgba(255,255,255,0.10);
    backdrop-filter: blur(10px);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.navbar a {
    color: white;
    text-decoration: none;
    margin-left: 20px;
}

/* FORM BOX */
.container {
    width: 540px;
    margin: 60px auto;
    background: rgba(255,255,255,0.18);
    backdrop-filter: blur(20px);
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 8px 35px rgba(0,0,0,0.25);
}

.title { font-size: 28px; font-weight: 700; text-align: center; }
.subtitle { text-align: center; opacity: 0.85; margin-bottom: 20px; }

/* INPUTS */
.input {
    width: 100%;
    height: 50px;
    padding: 0 14px;
    margin-top: 12px;
    border-radius: 10px;
    border: none;
    background: rgba(255,255,255,0.25);
    color: white;
}
textarea.input {
    height: 90px;
    padding-top: 12px;
    resize: none;
}
.input::placeholder { color: rgba(255,255,255,0.6); }

/* SECTION LABEL */
.section-title {
    margin-top: 22px;
    margin-bottom: 4px;
    font-size: 14px;
    font-weight: 600;
}

/* GRID */
.slot-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 12px;
}

/* AVAILABLE SLOT */
.slot-btn {
    padding: 12px 0;
    background: rgba(255,255,255,0.22);
    border-radius: 10px;
    text-align: center;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
    border: 2px solid transparent;
}
.slot-btn:hover {
    background: rgba(255,255,255,0.35);
    transform: translateY(-3px);
}

/* SELECTED SLOT */
.slot-selected {
    background: #36d1dc !important;
    border-color: white;
}

/* BOOKED SLOT */
.slot-booked {
    background: #ff4d4d !important;
    color: white !important;
    cursor: not-allowed !important;
    border-color: #ffb3b3 !important;
    opacity: 0.8;
}

/* BUTTON */
.btn {
    margin-top: 20px;
    width: 100%;
    padding: 14px;
    background: linear-gradient(90deg,#36d1dc,#5b86e5);
    border: none;
    border-radius: 12px;
    font-size: 17px;
    font-weight: 600;
    color: white;
    cursor: pointer;
}
.btn:hover { opacity: 0.92; transform: translateY(-3px); }
</style>

<script>
function loadDoctors(catId) {
    fetch("fetch_doctors.php?cat=" + catId)
    .then(res => res.json())
    .then(data => {
        let doctorSelect = document.getElementById("doctorSelect");
        doctorSelect.innerHTML = "<option disabled selected>Select Doctor</option>";

        data.forEach(doc => {
            doctorSelect.innerHTML += `
                <option value="${doc.id}">
                    Dr. ${doc.name} — ${doc.experience} yrs
                </option>
            `;
        });
    });
}

function loadSlots() {
    let doctor = document.getElementById("doctorSelect").value;
    let date = document.getElementById("dateInput").value;

    if (!doctor || !date) return;

    fetch(`fetch_timeslots.php?doctor=${doctor}&date=${date}`)
    .then(res => res.json())
    .then(slots => {
        let slotDiv = document.getElementById("slotGrid");
        slotDiv.innerHTML = "";

        if (slots.length === 0) {
            slotDiv.innerHTML = "<p>No available slots.</p>";
            return;
        }

        slots.forEach(s => {
            let css = s.booked ? "slot-booked" : "slot-btn";
            let click = s.booked ? "" : `onclick="selectSlot('${s.start}', this)"`;

            slotDiv.innerHTML += `
                <div class="${css}" ${click}>
                    ${s.start} - ${s.end}
                </div>
            `;
        });
    });
}

function selectSlot(time, el) {
    document.getElementById("timeInput").value = time;
    document.querySelectorAll(".slot-btn").forEach(btn => btn.classList.remove("slot-selected"));
    el.classList.add("slot-selected");
}
</script>

</head>

<body>

<div class="navbar">
    <div class="logo">Smart Health</div>
    <div>
        <a href="index.php">Dashboard</a>
        <a href="appointments_list.php">Appointments</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">

    <div class="title">📝 Book Appointment</div>
    <div class="subtitle">Select Category → Doctor → Date → Slot</div>

    <form action="appointment_submit.php" method="POST">

        <div class="section-title">Category</div>
        <select class="input" onchange="loadDoctors(this.value)" required>
            <option selected disabled>Select Category</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <div class="section-title">Doctor</div>
        <select class="input" id="doctorSelect" onchange="loadSlots()" name="doctor_id" required>
            <option disabled selected>Select Doctor</option>
        </select>

        <div class="section-title">Date</div>
        <input class="input" id="dateInput" type="text" name="date" placeholder="Select Date" required>

        <div class="section-title">Time Slots</div>
        <div id="slotGrid" class="slot-grid"></div>

        <input type="hidden" name="time" id="timeInput">

        <div class="section-title">Reason for Visit</div>
        <textarea class="input" name="message" placeholder="Describe your symptoms..." required></textarea>

        <button class="btn">Confirm Appointment</button>

    </form>
</div>

<script>
flatpickr("#dateInput", {
    minDate: "today",
    dateFormat: "Y-m-d",
    disableMobile: true,
    onChange: loadSlots
});
</script>

</body>
</html>
