<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['remember_token']) && isset($_COOKIE['remember_user'])) {
        require 'db.php';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_COOKIE['remember_user']]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
        } else {
            header('Location: login.php');
            exit;
        }
    } else {
        header('Location: login.php');
        exit;
    }
}

// MySQLi — citeste profilul userului
$mysqli = new mysqli('127.0.0.1', 'root', '', 'fittrack', 3307);

if ($mysqli->connect_error) {
    die("MySQLi connect failed: " . $mysqli->connect_error);
}

$user_id = $_SESSION['user_id'];
$stmt_my = $mysqli->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
$stmt_my->bind_param("i", $user_id);
$stmt_my->execute();
$profile = $stmt_my->get_result()->fetch_assoc();
$stmt_my->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>FitTrack</title>
    <link rel="stylesheet" href="styles1.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
<a href="logout.php"><button>Logout</button></a>


<h1>FitTrack</h1>

<div class="theme-switch">
    <button id="lightBtn">Light</button>
    <button id="darkBtn">Dark</button>
</div>


<div id="carusel">
    <button class="prev" onclick="prevSlide()">&lt;</button>

    <a id="caruselLink" href="#">
        <span id="caruselText"></span>
    </a>

    <button class="next" onclick="nextSlide()">&gt;</button>
</div>

<h2>Profil utilizator</h2>

<div>
    <p><strong>Nume:</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>
    <?php if ($profile): ?>
        <p><strong>Telefon:</strong> <?= htmlspecialchars($profile['phone'] ?? 'N/A') ?></p>
        <p><strong>Vârstă:</strong> <?= htmlspecialchars($profile['age'] ?? 'N/A') ?></p>
        <p><strong>Gen:</strong> <?= htmlspecialchars($profile['gender'] ?? 'N/A') ?></p>
        <p><strong>Obiectiv:</strong> <?= htmlspecialchars($profile['goal'] ?? 'N/A') ?></p>
    <?php else: ?>
        <p>Nu ai completat profilul. <a href="profile.php">Completează aici</a></p>
    <?php endif; ?>
</div>

<h3>Obiective fitness:</h3>

<ul>
    <li>Slăbit</li>
    <li>Masă musculară
        <ul>
            <li>Exerciții compuse</li>
            <li>Exerciții izolate</li>
        </ul>
    </li>
    <li>Rezistență</li>
</ul>

<ol type="I" start="1">
    <li>Antrenament</li>
    <li>Recuperare</li>
</ol>

<h3>Program săptămânal</h3>

<table>
    <tr>
        <th>Zi</th>
        <th>Antrenament</th>
        <th>Detalii</th>
    </tr>
    <tr>
        <td>Luni</td>
        <td>Piept</td>
        <td>
            <table>
                <tr>
                    <th>Exercițiu</th>
                    <th>Seturi</th>
                    <th>Repetări</th>
                </tr>
                <tr>
                    <td>Bench Press</td>
                    <td>3</td>
                    <td>10</td>
                </tr>
                <tr>
                    <td>Fluturări</td>
                    <td colspan="2">3 x 12</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>Marți</td>
        <td>Spate</td>
        <td>
            <table>
                <tr>
                    <td rowspan="2">Tracțiuni</td>
                    <td>4</td>
                    <td>8</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>10</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<h3>Grupe musculare - exercitii</h3>

<ul class="muscle-list">
    <li class="expandable" onclick="toggleList(this)">
        Piept
        <ul class="hidden">
            <li>Bench Press</li>
            <li>Incline Press</li>
            <li>Chest Fly</li>
        </ul>
    </li>

    <li class="expandable" onclick="toggleList(this)">
        Spate
        <ul class="hidden">
            <li>Pull Ups</li>
            <li>Barbell Row</li>
            <li>Lat Pulldown</li>
        </ul>
    </li>

    <li class="expandable" onclick="toggleList(this)">
        Picioare
        <ul class="hidden">
            <li>Squat</li>
            <li>Leg Press</li>
            <li>Lunges</li>
        </ul>
    </li>
</ul>

<br>

<img src="poza.jpg" width="200" height="150" alt="fitness image" title="Fitness">

<br><br>
<script src="script.js"></script>



<?php
require_once 'sqlite_log.php';
$db = new PDO('sqlite:' . __DIR__ . '/activity.db');
$logs = $db->query("SELECT * FROM activity_log WHERE user_id = " . $_SESSION['user_id'] . " ORDER BY timestamp DESC LIMIT 5")->fetchAll();
?>

<h3>Ultimele autentificări</h3>
<ul>
    <?php foreach ($logs as $log): ?>
        <li><?= $log['action'] ?> — <?= $log['timestamp'] ?></li>
    <?php endforeach; ?>
</ul>
</body>
</html>