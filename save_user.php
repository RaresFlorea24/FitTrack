<?php
// save_user.php — salvează modificările unui utilizator (POST, JSON)
header('Content-Type: application/json');
require_once 'db.php';

// Citim body-ul JSON trimis de JS
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id'])) {
    echo json_encode(['success' => false, 'error' => 'Date invalide']);
    exit;
}

$id     = (int)$input['id'];
$phone  = trim($input['phone']  ?? '');
$age    = (int)($input['age']   ?? 0);
$gender = trim($input['gender'] ?? '');
$goal   = trim($input['goal']   ?? '');

if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID invalid']);
    exit;
}

try {
    // Verificăm dacă există deja profil pentru user
    $check = $pdo->prepare("SELECT id FROM user_profiles WHERE user_id = :id");
    $check->bindValue(':id', $id, PDO::PARAM_INT);
    $check->execute();
    $exists = $check->fetch();

    if ($exists) {
        // UPDATE
        $stmt = $pdo->prepare("
            UPDATE user_profiles
            SET phone = :phone, age = :age, gender = :gender, goal = :goal
            WHERE user_id = :id
        ");
    } else {
        // INSERT — utilizatorul nu avea profil
        $stmt = $pdo->prepare("
            INSERT INTO user_profiles (user_id, phone, age, gender, goal)
            VALUES (:id, :phone, :age, :gender, :goal)
        ");
    }

    $stmt->bindValue(':id',     $id,     PDO::PARAM_INT);
    $stmt->bindValue(':phone',  $phone,  PDO::PARAM_STR);
    $stmt->bindValue(':age',    $age,    PDO::PARAM_INT);
    $stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
    $stmt->bindValue(':goal',   $goal,   PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Salvat cu succes']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>