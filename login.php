<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica CAPTCHA primul
    if (!isset($_POST['captcha']) || $_POST['captcha'] != $_SESSION['captcha_result']) {
        $error = "CAPTCHA incorect.";
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            require_once 'sqlite_log.php';
            logActivity($user['id'], $user['username'], 'login');

            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
                setcookie('remember_user', $user['id'], time() + (30 * 24 * 60 * 60), '/');
            }

            header('Location: index.php');
            exit;
        } else {
            $error = "Username sau parola gresite.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <h2>Login</h2>
    <?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Parola" required><br><br>

        <img src="captcha.php" id="captcha-img" alt="CAPTCHA"><br>
        <a href="#" onclick="document.getElementById('captcha-img').src='captcha.php?'+Math.random()">
            🔄 Regenerează
        </a><br><br>
        <input type="text" name="captcha" placeholder="Răspuns CAPTCHA" required><br><br>

        <input type="checkbox" name="remember" value="1"> Remember me<br><br>

        <button type="submit">Login</button>
    </form>
    <a href="register.php">Nu am cont</a>
</body>
</html>