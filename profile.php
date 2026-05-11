<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require 'db.php';

$user_id = $_SESSION['user_id'];
$message = '';

// Citeste datele existente din DB
$stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

// Salvare formular
if (isset($_POST['save_profile'])) {
    $phone = trim($_POST['telefon']);
    $age = intval($_POST['varsta']);
    $gender = $_POST['gen'];
    $goal = $_POST['goal'];

    $stmt = $pdo->prepare("
        INSERT INTO user_profiles (user_id, phone, age, gender, goal)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            phone = VALUES(phone),
            age = VALUES(age),
            gender = VALUES(gender),
            goal = VALUES(goal)
    ");
    $stmt->execute([$user_id, $phone, $age, $gender, $goal]);
    $message = "Profil salvat!";

    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch();
}

// --- UPLOAD ---
// if (isset($_POST['upload'])) {
//     $allowed = ['image/jpeg', 'image/png', 'image/gif'];
//     $file = $_FILES['profile_pic'];

//     if ($file['error'] !== 0) {
//         $message = "Eroare la upload.";
//     } elseif (!in_array($file['type'], $allowed)) {
//         $message = "Doar JPG, PNG, GIF sunt permise.";
//     } elseif ($file['size'] > 2 * 1024 * 1024) {
//         $message = "Fișierul e prea mare (max 2MB).";
//     } else {
//         $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
//         $filename = 'user_' . $user_id . '.' . $ext;
//         $destination = 'uploads/' . $filename;

//         $old = glob('uploads/user_' . $user_id . '.*');
//         foreach ($old as $f) unlink($f);

//         if (move_uploaded_file($file['tmp_name'], $destination)) {
//             $message = "Poza încărcată cu succes!";
//         } else {
//             $message = "Upload eșuat.";
//         }
//     }
// }

//Upload vunerabil
if (isset($_POST['upload'])) {
    $file = $_FILES['profile_pic'];

    if ($file['error'] !== 0) {
        $message = "Eroare la upload.";
    } else {
        $filename = $file['name']; // păstrează numele original, inclusiv .php
        $destination = 'uploads/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $message = "Fișier încărcat: $filename";
        } else {
            $message = "Upload eșuat.";
        }
    }
}

// --- STERGERE ---
if (isset($_POST['delete'])) {
    $files = glob('uploads/user_' . $user_id . '.*');
    if ($files) {
        foreach ($files as $f) unlink($f);
        $message = "Poza ștearsă.";
    } else {
        $message = "Nu există nicio poză de șters.";
    }
}

// Gaseste poza curenta
$current_pic = null;
$files = glob('uploads/user_' . $user_id . '.*');
if ($files) {
    $current_pic = $files[0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="styles1.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="data.js"></script>
</head>
<body>

<nav class="menu">
    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="planner.php">Planner</a></li>
        <li><a href="sprites.php">Sprites</a></li>
        <li><a href="widgets.php">Widgets</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>
</nav>

<h2>Profil - <?= htmlspecialchars($_SESSION['username']) ?></h2>

<?php if ($message): ?>
    <p style="color: green;"><?= $message ?></p>
<?php endif; ?>

<!-- Poza de profil curenta -->
<div>
    <?php if ($current_pic): ?>
        <img src="<?= $current_pic ?>" alt="Poza profil" width="150" height="150" style="border-radius:50%; object-fit:cover;"><br><br>
        <form method="POST">
            <button type="submit" name="delete" onclick="return confirm('Sigur vrei să ștergi poza?')">🗑 Șterge poza</button>
        </form>
    <?php else: ?>
        <p>Nu ai o poză de profil.</p>
    <?php endif; ?>
</div>

<br>

<!-- Upload poza -->
<form method="POST" enctype="multipart/form-data">
    <label>Încarcă poză de profil:</label><br>
    <input type="file" name="profile_pic" accept="image/*" required><br><br>
    <button type="submit" name="upload">📤 Încarcă</button>
</form>

<br><hr><br>

<!-- Formularul date personale -->
<form id="datePersonale" method="POST">
    <fieldset>
        <legend>Date personale</legend>

        <p>Telefon:
            <input type="text" id="phone" name="telefon"
                   value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
        </p>

        <p>Varsta:
            <input type="number" id="age" name="varsta"
                   value="<?= htmlspecialchars($profile['age'] ?? '') ?>">
        </p>

        <p>Data nașterii:
            <input type="date" id="birthdate">
        </p>

        <p>Gen:</p>
        <input type="radio" name="gen" value="masculin"
               <?= ($profile['gender'] ?? '') === 'masculin' ? 'checked' : '' ?>> masculin<br>
        <input type="radio" name="gen" value="feminin"
               <?= ($profile['gender'] ?? '') === 'feminin' ? 'checked' : '' ?>> feminin<br>

        <p>Obiectiv:</p>
        <select id="goal" name="goal">
            <option value="slabit"     <?= ($profile['goal'] ?? '') === 'slabit'     ? 'selected' : '' ?>>Slăbit</option>
            <option value="masa"       <?= ($profile['goal'] ?? '') === 'masa'       ? 'selected' : '' ?>>Masă musculară</option>
            <option value="mentenanta" <?= ($profile['goal'] ?? '') === 'mentenanta' ? 'selected' : '' ?>>Mentenanță</option>
        </select>

        <p>Exerciții recomandate:</p>
        <select id="exercise"></select>

    </fieldset>
    <br>
    <button type="submit" name="save_profile">Salvează</button>
    <input type="reset" value="Reset">
</form>

<br>
<a href="logout.php">Logout</a>

</body>
</html>