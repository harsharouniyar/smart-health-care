<?php
require __DIR__ . "/config/db.php";

$error = "";

// Fetch doctor categories from DB
$categories = $pdo->query("SELECT * FROM doctor_categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email exists
    $check = $pdo->prepare("SELECT email FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $error = "This email is already registered.";

    } else {

        // DOCTOR VALIDATION
        if ($role === "doctor") {

            $experience = isset($_POST['experience']) ? (int) $_POST['experience'] : 0;
            $fees       = isset($_POST['fees']) ? (int) $_POST['fees'] : 0;
            $bio        = trim($_POST['bio']);
            $category_id = $_POST['category'] ?? null;

            if ($experience < 0) {
                $error = "Experience cannot be negative.";
            }
            if ($fees < 0) {
                $error = "Consultation fee cannot be negative.";
            }

            if (!empty($error)) {
                goto END_FORM;
            }
        }

        // Insert user
        $insertUser = $pdo->prepare("
            INSERT INTO users (name, email, password, role)
            VALUES (?, ?, ?, ?)
        ");
        $insertUser->execute([$name, $email, $password, $role]);
        $user_id = $pdo->lastInsertId();

        // Doctor-specific inserts
        if ($role === "doctor") {

            $insertDoctor = $pdo->prepare("
                INSERT INTO doctors (user_id, bio, experience, fees)
                VALUES (?, ?, ?, ?)
            ");
            $insertDoctor->execute([$user_id, $bio, $experience, $fees]);

            $doctor_id = $pdo->lastInsertId();

            $map = $pdo->prepare("
                INSERT INTO doctor_category_map (doctor_id, category_id)
                VALUES (?, ?)
            ");
            $map->execute([$doctor_id, $category_id]);
        }

        // Redirect to login
        header("Location: login.php?registered=1");
        exit;
    }
}

END_FORM:
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Smart Health</title>
    <link rel="stylesheet" href="assets/style.css">

<style>
/* Moto + Logo Styling */
.logo {
    text-align:center;
    margin-bottom:12px;
}
.logo .title {
    font-size:26px;
    font-weight:700;
}
.logo .motto {
    font-size:13px;
    opacity:0.85;
    margin-top:4px;
    letter-spacing:0.2px;
}
</style>

</head>

<body>

<div class="card register-card">

    <div class="logo">
        <div class="title">Smart Health</div>
        <div class="motto">Smart Care for a Healthier Tomorrow.</div>
    </div>

    <h2>Create Your Account</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <!-- ROLE -->
        <select class="input" name="role" required id="roleSelect">
            <option value="patient">Register as Patient</option>
            <option value="doctor">Register as Doctor</option>
        </select>

        <!-- BASIC INFO -->
        <input class="input" type="text" name="name" placeholder="Full Name" required>
        <input class="input" type="email" name="email" placeholder="Email Address" required>
        <input class="input" type="password" name="password" placeholder="Create Password" required>

        <!-- DOCTOR FIELDS -->
        <div id="doctorFields" style="display:none;">

            <select class="input" name="category" disabled>
                <option selected disabled>Select Category</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                <?php endforeach; ?>
            </select>

            <input class="input" type="number" name="experience" placeholder="Experience (years)" min="0" disabled>
            <input class="input" type="number" name="fees" placeholder="Consultation Fee ($)" min="0" disabled>
            <textarea class="input" name="bio" placeholder="Doctor Bio" disabled></textarea>

        </div>

        <!-- SUBMIT -->
        <button class="btn" type="submit">Register</button>

    </form>

    <p style="margin-top: 12px;">
        Already have an account?
        <a href="login.php" style="color:#fff; text-decoration:underline;">Login</a>
    </p>

</div>

<script>
// Show/hide + enable/disable doctor fields
document.getElementById("roleSelect").addEventListener("change", function() {
    const isDoctor = this.value === "doctor";
    const area = document.getElementById("doctorFields");

    const fields = area.querySelectorAll("input, textarea, select");

    fields.forEach(f => {
        if (isDoctor) {
            f.removeAttribute("disabled");
            f.setAttribute("required", "required");
        } else {
            f.setAttribute("disabled", "disabled");
            f.removeAttribute("required");
        }
    });

    area.style.display = isDoctor ? "block" : "none";
});
</script>

</body>
</html>
