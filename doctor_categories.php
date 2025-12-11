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
<title>Find Doctors | Smart Health</title>
<link rel="stylesheet" href="assets/style.css">

<style>

/* CATEGORY DROPDOWN AREA */
.category-container {
    width: 450px;
    margin: 30px auto 10px;
    text-align: center;
}

/* GRID → EXACT 3 PER ROW ON LARGE SCREENS */
.doctor-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    padding: 20px 40px;
}

/* TABLET */
@media (max-width: 1100px) {
    .doctor-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* MOBILE */
@media (max-width: 700px) {
    .doctor-grid {
        grid-template-columns: repeat(1, 1fr);
    }
}

/* DOCTOR CARD */
.doctor-card {
    background: rgba(255,255,255,0.18);
    padding: 22px;
    border-radius: 18px;
    backdrop-filter: blur(12px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    color: #fff;
    transition: 0.3s;
    text-align: center;
    min-height: 350px; /* SAME HEIGHT FOR ALL CARDS */
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

.doctor-card:hover {
    transform: translateY(-6px);
    background: rgba(255,255,255,0.25);
}

/* DOCTOR IMAGE */
.doctor-img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,0.4);
    margin: 0 auto 12px auto;
}

/* NAME */
.doc-name {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 6px;
}

/* CATEGORY */
.doc-category {
    font-size: 14px;
    opacity: 0.85;
    margin-bottom: 10px;
}

/* INFO */
.doc-info {
    font-size: 14px;
    margin-bottom: 4px;
    opacity: 0.95;
}

/* BIO — LIMITED LINES FOR CONSISTENT HEIGHT */
.doc-bio {
    font-size: 13px;
    margin-top: 10px;
    opacity: 0.9;
    text-align: center;

    /* REQUIRED FOR LINE CLAMP */
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
    -webkit-line-clamp: 3;

    /* Fix spacing and alignment */
    min-height: 54px; 
}


/* SMALL PROFESSIONAL BUTTON */
.book-btn {
    width: 150px;
    padding: 8px;
    margin: 15px auto 0 auto;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    border: none;
    background: linear-gradient(90deg,#36d1dc,#5b86e5);
    color: white;
    cursor: pointer;
    transition: 0.3s;
}

.book-btn:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}

</style>

<script>
function loadDoctors(catId) {
    if (!catId) return;

    fetch("fetch_doctors_by_category.php?cat=" + catId)
    .then(res => res.json())
    .then(data => {

        let box = document.getElementById("doctorList");
        box.innerHTML = "";

        if (data.length === 0) {
            box.innerHTML = `<p style='text-align:center;margin-top:20px;'>No doctors available in this category.</p>`;
            return;
        }

        data.forEach(doc => {
            let photo = doc.photo ? doc.photo : "assets/default-doctor.png";

            box.innerHTML += `
                <div class="doctor-card">

                    <img src="${photo}" class="doctor-img">

                    <div class="doc-name">Dr. ${doc.name}</div>
                    <div class="doc-category">${doc.category}</div>

                    <div class="doc-info">🩺 ${doc.experience} years experience</div>
                    <div class="doc-info">💲 Fee: $${doc.fees}</div>

                    <div class="doc-bio">${doc.bio}</div>

                    <a href="appointment.php?doctor=${doc.id}">
                        <button class="book-btn">Book</button>
                    </a>
                </div>
            `;
        });
    });
}
</script>

</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div>
        <div class="logo-text">Smart Health</div>
        <div class="motto">Smart Care for a Healthier Tomorrow.</div>
    </div>

    <div>
        <a href="index.php">Home</a>
        <a href="appointments_list.php">Appointments</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- CATEGORY DROPDOWN -->
<div class="category-container">
    <h2 style="margin-bottom:14px;">👨‍⚕️ Find Doctors by Category</h2>

    <select class="input" onchange="loadDoctors(this.value)">
        <option selected disabled>Select Category</option>

        <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
        <?php endforeach; ?>
    </select>
</div>

<!-- DOCTOR CARDS GRID -->
<div id="doctorList" class="doctor-grid"></div>

</body>
</html>
